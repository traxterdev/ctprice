<?php
/**
 * admin/aplicar-atualizacao.php
 *
 * TEMPORÁRIO — remover após aplicar migration/seed em produção.
 *
 * Ferramenta de implantação de UMA VEZ: aplica a migration pendente do módulo "Banners da Home"
 * (database/migrations/2026_09_22_000001_create_hero_slides_table.sql) e o seed inicial dos 4
 * slides atuais, pelo próprio painel administrativo — usada quando o TI que roda `php
 * database/migrate.php`/`php database/seed_hero_slides.php` via CLI não está disponível no
 * momento da implantação em produção.
 *
 * NÃO duplica lógica: reaproveita database/MigrationRunner.php (mesma classe usada por
 * database/migrate.php) e as funções ctprice_hero_seed_slides()/ctprice_seed_hero_slides() já
 * existentes em database/seed_hero_slides.php (mesmas usadas pelo script CLI) — `require`'das
 * aqui só declaram funções/classe, nenhum SQL roda sozinho ao incluir esses arquivos.
 *
 * Segurança:
 *   - admin_require_login() no topo — mesma autenticação/sessão de todo o resto do `/admin/`;
 *     visitante não autenticado é redirecionado para o login, nunca vê nada desta página.
 *   - GET só EXIBE o status atual (o que está pendente) — nenhuma operação mutável roda em GET.
 *   - POST exige CSRF válido (admin_verify_csrf()) antes de tocar no banco.
 *   - Nenhum parâmetro do navegador decide QUAL SQL/arquivo roda — o diretório de migrations e
 *     a lista de slides do seed são fixos no código (MigrationRunner/seed_hero_slides.php),
 *     nunca vindos de $_POST/$_GET.
 *   - Falhas são logadas via error_log() com o detalhe técnico; a tela mostra só uma mensagem
 *     administrativa genérica — nunca stack trace, DSN, senha ou caminho físico sensível.
 *   - Não expõe nenhuma rota nova sob `database/` — esta página só faz `require` (server-side,
 *     nunca passa por HTTP) dos arquivos que já estavam lá; a regra do .htaccess raiz que já
 *     bloqueia `database/` por HTTP continua intacta e sem alterações.
 *
 * Idempotente de ponta a ponta: reexecutar (inclusive clicando "Aplicar atualização" de novo com
 * tudo já aplicado) não duplica migration nem slide — MigrationRunner::run() só aplica o que não
 * está em `schema_migrations`; ctprice_seed_hero_slides() usa `ON DUPLICATE KEY UPDATE` por
 * `imagem_path`.
 *
 * Ordem: migrations primeiro; o seed só roda se as migrations terminarem sem lançar exceção (ver
 * fluxo do POST abaixo) — se a migration falhar, o seed nunca é tentado.
 *
 * Renderiza o resultado diretamente na mesma resposta do POST (não redireciona-e-mostra-flash,
 * padrão usual dos outros módulos) porque o relatório é estruturado (migrations aplicadas/já
 * existentes, contagem de banners, status final) — mais rico que uma única mensagem de flash. Um
 * F5 depois do POST reenvia o formulário, mas como toda a operação é idempotente isso é
 * inofensivo (não duplica nada), então o redirect-after-POST não é necessário aqui.
 */

declare(strict_types=1);

require __DIR__ . '/../config/bootstrap.php';
require __DIR__ . '/../includes/AdminAuth.php';
require __DIR__ . '/../database/MigrationRunner.php';
require __DIR__ . '/../database/seed_hero_slides.php';

$adminCurrentUser = admin_require_login();
$csrfToken = admin_csrf_token();

$migrationsDir = __DIR__ . '/../database/migrations';

/** true se a tabela existe no schema atual — evita depender de exceção para algo rotineiro. */
function ctprice_temp_table_exists(PDO $pdo, string $table): bool
{
    $stmt = $pdo->prepare(
        'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = :table'
    );
    $stmt->execute(['table' => $table]);
    return ((int) $stmt->fetchColumn()) > 0;
}

/**
 * @return array{seedApplied: bool, totalHeroSlides: ?int}
 */
function ctprice_temp_hero_seed_status(PDO $pdo): array
{
    if (!ctprice_temp_table_exists($pdo, 'hero_slides')) {
        return ['seedApplied' => false, 'totalHeroSlides' => null];
    }

    $expectedPaths = array_column(ctprice_hero_seed_slides(), 'imagem_path');
    $placeholders = implode(',', array_fill(0, count($expectedPaths), '?'));
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM hero_slides WHERE imagem_path IN ($placeholders)");
    $stmt->execute($expectedPaths);
    $matched = (int) $stmt->fetchColumn();

    $total = (int) $pdo->query('SELECT COUNT(*) FROM hero_slides')->fetchColumn();

    return ['seedApplied' => $matched === count($expectedPaths), 'totalHeroSlides' => $total];
}

$dbError = false;
$pdo = null;

try {
    $pdo = Database::connection();
} catch (Throwable $e) {
    error_log('CT Price CMS [admin/aplicar-atualizacao]: falha ao conectar ao banco — ' . $e->getMessage());
    $dbError = true;
}

$postResult = null; // preenchido só quando um POST roda nesta requisição

if (!$dbError && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    if (!admin_verify_csrf($_POST['csrf_token'] ?? null)) {
        $postResult = ['ok' => false, 'message' => 'Sua sessão expirou. Recarregue a página e tente novamente.'];
    } else {
        $migrationOutcome = null;
        $migrationError = null;
        $seedCount = null;
        $seedError = null;

        try {
            $migrationOutcome = MigrationRunner::run($pdo, $migrationsDir);
        } catch (Throwable $e) {
            error_log('CT Price CMS [admin/aplicar-atualizacao]: falha ao aplicar migrations — ' . $e->getMessage());
            $migrationError = 'Não foi possível aplicar as migrations agora. Nenhuma alteração parcial foi mantida além do que já tinha sido aplicado com sucesso antes desta tentativa.';
        }

        if ($migrationError === null) {
            try {
                $seedCount = ctprice_seed_hero_slides($pdo);
            } catch (Throwable $e) {
                error_log('CT Price CMS [admin/aplicar-atualizacao]: falha ao executar o seed — ' . $e->getMessage());
                $seedError = 'As migrations foram aplicadas, mas não foi possível executar o seed dos banners agora.';
            }
        }

        $postResult = [
            'ok' => $migrationError === null && $seedError === null,
            'migrationOutcome' => $migrationOutcome,
            'migrationError' => $migrationError,
            'seedCount' => $seedCount,
            'seedError' => $seedError,
        ];
    }
}

// Status atual (sempre recalculado a partir do banco real — nunca de uma flag própria), tanto
// para a exibição em GET quanto para o resumo final depois de um POST.
$pendingMigrations = [];
$seedStatus = ['seedApplied' => false, 'totalHeroSlides' => null];

if (!$dbError) {
    try {
        $pendingMigrations = MigrationRunner::pending($pdo, $migrationsDir);
        $seedStatus = ctprice_temp_hero_seed_status($pdo);
    } catch (Throwable $e) {
        error_log('CT Price CMS [admin/aplicar-atualizacao]: falha ao ler status — ' . $e->getMessage());
        $dbError = true;
    }
}

$allUpToDate = !$dbError && empty($pendingMigrations) && $seedStatus['seedApplied'];

$adminPageTitle = 'Atualização do banco de dados';
require __DIR__ . '/includes/layout-header.php';
?>
<div class="admin-header">
    <div>
        <h1>Atualização do banco de dados</h1>
        <p>Aplica a migration e o seed pendentes do módulo Banners da Home.</p>
    </div>
    <a class="admin-btn admin-btn--outline" href="<?= BASE_URL ?>/admin/">&larr; Voltar ao dashboard</a>
</div>

<div style="background:#FFF8E6; color:#7A5B00; border:1px solid #F0DFA0; border-radius:8px; padding:12px 16px; margin-bottom:18px; font-size:14px;">
    Ferramenta temporária de implantação. Remover após a atualização de produção.
</div>

<?php if ($dbError): ?>
<div class="admin-alert admin-alert--error">Não foi possível conectar ao banco agora. Tente novamente em instantes.</div>
<?php else: ?>

    <?php if ($postResult !== null): ?>
    <div class="admin-panel" style="margin-bottom:24px;">
        <?php if (!$postResult['ok'] && isset($postResult['message'])): ?>
        <div class="admin-alert admin-alert--error"><?= htmlspecialchars($postResult['message'], ENT_QUOTES, 'UTF-8') ?></div>
        <?php else: ?>

            <?php if ($postResult['migrationError'] !== null): ?>
            <div class="admin-alert admin-alert--error"><?= htmlspecialchars($postResult['migrationError'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php else: ?>
            <div class="admin-alert admin-alert--success">
                Migrations: <?= count($postResult['migrationOutcome']['applied']) ?> aplicada(s) agora,
                <?= count($postResult['migrationOutcome']['already']) ?> já existente(s)
                (<?= $postResult['migrationOutcome']['total'] ?> no total).
            </div>
                <?php if ($postResult['migrationOutcome']['applied']): ?>
                <p class="admin-form__hint">Aplicadas agora: <?= htmlspecialchars(implode(', ', $postResult['migrationOutcome']['applied']), ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($postResult['seedError'] !== null): ?>
            <div class="admin-alert admin-alert--error"><?= htmlspecialchars($postResult['seedError'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php elseif ($postResult['seedCount'] !== null): ?>
            <div class="admin-alert admin-alert--success">
                Seed dos banners executado: <?= (int) $postResult['seedCount'] ?> slide(s) processado(s).
            </div>
            <?php endif; ?>

            <p><strong>Status final:</strong> <?= $postResult['ok'] ? 'Ambiente atualizado.' : 'Falha na atualização — veja as mensagens acima.' ?></p>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="admin-panel">
        <h2 style="margin-top:0;">Status atual</h2>
        <ul style="margin:0 0 18px; padding-left:20px; font-size:14px; color:#33443F;">
            <li>Migrations pendentes: <strong><?= count($pendingMigrations) ?></strong><?= $pendingMigrations ? ' (' . htmlspecialchars(implode(', ', $pendingMigrations), ENT_QUOTES, 'UTF-8') . ')' : '' ?></li>
            <li>Seed dos banners: <strong><?= $seedStatus['seedApplied'] ? 'já aplicado' : 'pendente' ?></strong></li>
            <li>Total de banners cadastrados: <strong><?= $seedStatus['totalHeroSlides'] === null ? '— (tabela ainda não existe)' : (int) $seedStatus['totalHeroSlides'] ?></strong></li>
        </ul>

        <?php if ($allUpToDate): ?>
        <div class="admin-alert admin-alert--success" style="margin-bottom:0;">Ambiente atualizado. Nada pendente.</div>
        <?php else: ?>
        <form method="post" action="<?= BASE_URL ?>/admin/aplicar-atualizacao.php" data-confirm="Aplicar migrations e seed pendentes agora? A operação é segura e idempotente.">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
            <button type="submit" class="admin-btn admin-btn--primary">Aplicar atualização</button>
        </form>
        <?php endif; ?>
    </div>

<?php endif; ?>

<?php require __DIR__ . '/includes/layout-footer.php'; ?>
