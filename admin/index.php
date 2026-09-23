<?php
/**
 * admin/index.php — Dashboard
 *
 * Totais dos módulos administráveis por este CMS. Nenhum analytics inventado.
 *
 * Vagas/Benefícios NÃO aparecem aqui — decisão de arquitetura: passarão a vir de outro sistema já
 * desenvolvido, não são mais administrados por este CMS (ver comentário de admin/includes/
 * layout-header.php e das tabelas `jobs`/`benefits`, que continuam existindo — a página pública
 * /trabalhe-conosco/ ainda depende delas — só o CRUD administrativo foi retirado).
 *
 * AJUSTE (2026-09-23): cada contador agora roda sua PRÓPRIA consulta isolada (via
 * ctprice_dashboard_count()) em vez de todas as 10 consultas dividirem um único try/catch — causa
 * real de um bug observado em homologação: a migration de `hero_slides` ainda não tinha sido
 * aplicada lá, então a consulta de Banners lançava e derrubava TODOS os cards (Clientes,
 * Parceiros, Depoimentos, Notícias, Administradores) de uma vez, mesmo esses não tendo nenhum
 * problema. Agora só o card cuja consulta falhar mostra "—"; os demais continuam normalmente.
 * `$dbError` (erro de página inteira, com o alerta genérico) fica reservado só para quando a
 * CONEXÃO em si falha — nesse caso realmente não há nada a mostrar.
 */

declare(strict_types=1);

require __DIR__ . '/../config/bootstrap.php';
require __DIR__ . '/../includes/AdminAuth.php';

$adminCurrentUser = admin_require_login();

/** Conta uma consulta isolada — falha aqui não afeta os outros contadores do Dashboard. */
function ctprice_dashboard_count(?PDO $pdo, string $sql, string $label): ?int
{
    if ($pdo === null) {
        return null;
    }
    try {
        return (int) $pdo->query($sql)->fetchColumn();
    } catch (Throwable $e) {
        error_log("CT Price CMS [admin/index]: falha ao consultar $label — " . $e->getMessage());
        return null;
    }
}

$pdo = null;
$dbError = false;

try {
    $pdo = Database::connection();
} catch (Throwable $e) {
    error_log('CT Price CMS [admin/index]: falha ao conectar ao banco — ' . $e->getMessage());
    $dbError = true;
}

$activeClients = ctprice_dashboard_count($pdo, 'SELECT COUNT(*) FROM clients WHERE ativo = 1', 'clients ativos');
$activePartners = ctprice_dashboard_count($pdo, 'SELECT COUNT(*) FROM partners WHERE ativo = 1', 'partners ativos');
$activeTestimonials = ctprice_dashboard_count($pdo, 'SELECT COUNT(*) FROM video_testimonials WHERE ativo = 1', 'video_testimonials ativos');
$publishedPosts = ctprice_dashboard_count($pdo, 'SELECT COUNT(*) FROM blog_posts WHERE ativo = 1 AND published_at <= NOW()', 'blog_posts publicados');
$activeAdmins = ctprice_dashboard_count($pdo, 'SELECT COUNT(*) FROM admin_users WHERE ativo = 1', 'admin_users ativos');
$activeHeroSlides = ctprice_dashboard_count($pdo, 'SELECT COUNT(*) FROM hero_slides WHERE ativo = 1', 'hero_slides ativos');
$totalClients = ctprice_dashboard_count($pdo, 'SELECT COUNT(*) FROM clients', 'clients total');
$totalPartners = ctprice_dashboard_count($pdo, 'SELECT COUNT(*) FROM partners', 'partners total');
$totalTestimonials = ctprice_dashboard_count($pdo, 'SELECT COUNT(*) FROM video_testimonials', 'video_testimonials total');
$totalPosts = ctprice_dashboard_count($pdo, 'SELECT COUNT(*) FROM blog_posts', 'blog_posts total');
$totalHeroSlides = ctprice_dashboard_count($pdo, 'SELECT COUNT(*) FROM hero_slides', 'hero_slides total');

$adminPageTitle = 'Dashboard';
$adminActiveMenu = 'dashboard';
require __DIR__ . '/includes/layout-header.php';
?>
<div class="admin-header">
    <div>
        <h1>Dashboard</h1>
        <p>Visão geral do conteúdo administrável do site.</p>
    </div>
</div>

<?php if ($dbError): ?>
<div class="admin-alert admin-alert--error">Não foi possível conectar ao banco agora. Tente novamente em instantes.</div>
<?php else: ?>
<div class="admin-stats">
    <div class="admin-stat-card">
        <p class="admin-stat-card__label">Banners ativos</p>
        <p class="admin-stat-card__value"><?= $activeHeroSlides ?? '—' ?></p>
    </div>
    <div class="admin-stat-card">
        <p class="admin-stat-card__label">Clientes ativos</p>
        <p class="admin-stat-card__value"><?= $activeClients ?? '—' ?></p>
    </div>
    <div class="admin-stat-card">
        <p class="admin-stat-card__label">Parceiros ativos</p>
        <p class="admin-stat-card__value"><?= $activePartners ?? '—' ?></p>
    </div>
    <div class="admin-stat-card">
        <p class="admin-stat-card__label">Depoimentos ativos</p>
        <p class="admin-stat-card__value"><?= $activeTestimonials ?? '—' ?></p>
    </div>
    <div class="admin-stat-card">
        <p class="admin-stat-card__label">Posts publicados</p>
        <p class="admin-stat-card__value"><?= $publishedPosts ?? '—' ?></p>
    </div>
    <div class="admin-stat-card">
        <p class="admin-stat-card__label">Administradores ativos</p>
        <p class="admin-stat-card__value"><?= $activeAdmins ?? '—' ?></p>
    </div>
</div>

<h2 class="admin-section-title">Conteúdo do site</h2>
<div class="admin-module-list">
    <div class="admin-module-row">
        <span class="admin-module-row__name">Banners da Home</span>
        <span class="admin-module-row__count"><strong><?= $totalHeroSlides ?? '—' ?></strong> cadastrados</span>
        <a href="<?= BASE_URL ?>/admin/hero/" class="admin-btn admin-btn--outline admin-btn--sm">Gerenciar</a>
    </div>
    <div class="admin-module-row">
        <span class="admin-module-row__name">Clientes</span>
        <span class="admin-module-row__count"><strong><?= $totalClients ?? '—' ?></strong> cadastrados</span>
        <a href="<?= BASE_URL ?>/admin/clients/" class="admin-btn admin-btn--outline admin-btn--sm">Gerenciar</a>
    </div>
    <div class="admin-module-row">
        <span class="admin-module-row__name">Parceiros</span>
        <span class="admin-module-row__count"><strong><?= $totalPartners ?? '—' ?></strong> cadastrados</span>
        <a href="<?= BASE_URL ?>/admin/partners/" class="admin-btn admin-btn--outline admin-btn--sm">Gerenciar</a>
    </div>
    <div class="admin-module-row">
        <span class="admin-module-row__name">Notícias</span>
        <span class="admin-module-row__count"><strong><?= $totalPosts ?? '—' ?></strong> cadastradas</span>
        <a href="<?= BASE_URL ?>/admin/posts/" class="admin-btn admin-btn--outline admin-btn--sm">Gerenciar</a>
    </div>
    <div class="admin-module-row">
        <span class="admin-module-row__name">Depoimentos</span>
        <span class="admin-module-row__count"><strong><?= $totalTestimonials ?? '—' ?></strong> cadastrados</span>
        <a href="<?= BASE_URL ?>/admin/testimonials/" class="admin-btn admin-btn--outline admin-btn--sm">Gerenciar</a>
    </div>
</div>
<?php endif; ?>

<?php require __DIR__ . '/includes/layout-footer.php'; ?>
