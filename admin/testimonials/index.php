<?php
/** admin/testimonials/index.php — mesmo padrão de admin/clients/index.php. */

declare(strict_types=1);

require __DIR__ . '/../../config/bootstrap.php';
require __DIR__ . '/../../includes/AdminAuth.php';
require __DIR__ . '/../../repositories/TestimonialRepository.php';

$adminCurrentUser = admin_require_login();
$csrfToken = admin_csrf_token();

$testimonials = [];
$dbError = false;
try {
    $testimonials = (new TestimonialRepository())->allForAdmin();
} catch (Throwable $e) {
    error_log('CT Price CMS [admin/testimonials]: falha ao listar — ' . $e->getMessage());
    $dbError = true;
}

$adminPageTitle = 'Depoimentos';
$adminActiveMenu = 'testimonials';
require __DIR__ . '/../includes/layout-header.php';
?>
<div class="admin-header">
    <div>
        <h1>Depoimentos</h1>
        <p>Exibidos em /depoimentos/ (vídeos do YouTube via youtube-nocookie).</p>
    </div>
    <a class="admin-btn admin-btn--primary" href="<?= BASE_URL ?>/admin/testimonials/form.php">+ Novo depoimento</a>
</div>

<?php if ($dbError): ?>
<div class="admin-alert admin-alert--error">Não foi possível carregar a lista agora.</div>
<?php elseif (!$testimonials): ?>
<div class="admin-panel"><p class="admin-empty-state">Nenhum depoimento cadastrado ainda.</p></div>
<?php else: ?>
<div class="admin-panel">
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th>Foto</th><th>Nome</th><th>Empresa</th><th>Ordem</th><th>Status</th><th>Ações</th></tr></thead>
            <tbody>
                <?php foreach ($testimonials as $index => $t): ?>
                <tr>
                    <td><img class="admin-table__logo" src="<?= BASE_URL ?>/<?= htmlspecialchars($t['foto_path'], ENT_QUOTES, 'UTF-8') ?>" alt=""></td>
                    <td class="admin-table__name"><?= htmlspecialchars($t['nome'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="admin-table__name"><?= htmlspecialchars($t['empresa'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <div class="admin-table__actions">
                            <form method="post" action="<?= BASE_URL ?>/admin/testimonials/move.php">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="id" value="<?= (int) $t['id'] ?>">
                                <input type="hidden" name="direction" value="up">
                                <button type="submit" class="admin-btn admin-btn--outline admin-btn--sm" <?= $index === 0 ? 'disabled' : '' ?> aria-label="Subir">&uarr;</button>
                            </form>
                            <form method="post" action="<?= BASE_URL ?>/admin/testimonials/move.php">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="id" value="<?= (int) $t['id'] ?>">
                                <input type="hidden" name="direction" value="down">
                                <button type="submit" class="admin-btn admin-btn--outline admin-btn--sm" <?= $index === count($testimonials) - 1 ? 'disabled' : '' ?> aria-label="Descer">&darr;</button>
                            </form>
                        </div>
                    </td>
                    <td>
                        <span class="admin-badge <?= ((int) $t['ativo'] === 1) ? 'admin-badge--active' : 'admin-badge--inactive' ?>">
                            <?= ((int) $t['ativo'] === 1) ? 'Ativo' : 'Inativo' ?>
                        </span>
                    </td>
                    <td>
                        <div class="admin-table__actions">
                            <a class="admin-btn admin-btn--outline admin-btn--sm" href="<?= BASE_URL ?>/admin/testimonials/form.php?id=<?= (int) $t['id'] ?>">Editar</a>
                            <form method="post" action="<?= BASE_URL ?>/admin/testimonials/toggle.php">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="id" value="<?= (int) $t['id'] ?>">
                                <button type="submit" class="admin-btn admin-btn--outline admin-btn--sm"><?= ((int) $t['ativo'] === 1) ? 'Desativar' : 'Ativar' ?></button>
                            </form>
                            <form method="post" action="<?= BASE_URL ?>/admin/testimonials/delete.php" data-confirm="Excluir definitivamente este depoimento? Esta ação não pode ser desfeita.">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="id" value="<?= (int) $t['id'] ?>">
                                <button type="submit" class="admin-btn admin-btn--danger admin-btn--sm">Excluir</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php require __DIR__ . '/../includes/layout-footer.php'; ?>
