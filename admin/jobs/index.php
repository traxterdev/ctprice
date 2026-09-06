<?php
/**
 * admin/jobs/index.php — listagem administrativa de Vagas (mesmo padrão de admin/clients/index.php).
 */

declare(strict_types=1);

require __DIR__ . '/../../config/bootstrap.php';
require __DIR__ . '/../../includes/AdminAuth.php';
require __DIR__ . '/../../repositories/JobRepository.php';

$adminCurrentUser = admin_require_login();
$csrfToken = admin_csrf_token();

$jobs = [];
$dbError = false;
try {
    $jobs = (new JobRepository())->allForAdmin();
} catch (Throwable $e) {
    error_log('CT Price CMS [admin/jobs]: falha ao listar — ' . $e->getMessage());
    $dbError = true;
}

$adminPageTitle = 'Vagas';
$adminActiveMenu = 'jobs';
require __DIR__ . '/../includes/layout-header.php';
?>
<div class="admin-header">
    <div>
        <h1>Vagas</h1>
        <p>Exibidas em /trabalhe-conosco/. O link de candidatura continua vindo de config/company.php.</p>
    </div>
    <a class="admin-btn admin-btn--primary" href="<?= BASE_URL ?>/admin/jobs/form.php">+ Nova vaga</a>
</div>

<?php if ($dbError): ?>
<div class="admin-alert admin-alert--error">Não foi possível carregar a lista agora. Tente novamente em instantes.</div>
<?php elseif (!$jobs): ?>
<div class="admin-panel"><p class="admin-empty-state">Nenhuma vaga cadastrada ainda.</p></div>
<?php else: ?>
<div class="admin-panel">
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr><th>Título</th><th>Ordem</th><th>Status</th><th>Ações</th></tr>
            </thead>
            <tbody>
                <?php foreach ($jobs as $index => $job): ?>
                <tr>
                    <td class="admin-table__name"><?= htmlspecialchars($job['titulo'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <div class="admin-table__actions">
                            <form method="post" action="<?= BASE_URL ?>/admin/jobs/move.php">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="id" value="<?= (int) $job['id'] ?>">
                                <input type="hidden" name="direction" value="up">
                                <button type="submit" class="admin-btn admin-btn--outline admin-btn--sm" <?= $index === 0 ? 'disabled' : '' ?> aria-label="Subir">&uarr;</button>
                            </form>
                            <form method="post" action="<?= BASE_URL ?>/admin/jobs/move.php">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="id" value="<?= (int) $job['id'] ?>">
                                <input type="hidden" name="direction" value="down">
                                <button type="submit" class="admin-btn admin-btn--outline admin-btn--sm" <?= $index === count($jobs) - 1 ? 'disabled' : '' ?> aria-label="Descer">&darr;</button>
                            </form>
                        </div>
                    </td>
                    <td>
                        <span class="admin-badge <?= ((int) $job['ativo'] === 1) ? 'admin-badge--active' : 'admin-badge--inactive' ?>">
                            <?= ((int) $job['ativo'] === 1) ? 'Ativo' : 'Inativo' ?>
                        </span>
                    </td>
                    <td>
                        <div class="admin-table__actions">
                            <a class="admin-btn admin-btn--outline admin-btn--sm" href="<?= BASE_URL ?>/admin/jobs/form.php?id=<?= (int) $job['id'] ?>">Editar</a>
                            <form method="post" action="<?= BASE_URL ?>/admin/jobs/toggle.php">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="id" value="<?= (int) $job['id'] ?>">
                                <button type="submit" class="admin-btn admin-btn--outline admin-btn--sm"><?= ((int) $job['ativo'] === 1) ? 'Desativar' : 'Ativar' ?></button>
                            </form>
                            <form method="post" action="<?= BASE_URL ?>/admin/jobs/delete.php" data-confirm="Excluir definitivamente esta vaga? Esta ação não pode ser desfeita.">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="id" value="<?= (int) $job['id'] ?>">
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
