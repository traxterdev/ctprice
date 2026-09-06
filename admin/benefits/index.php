<?php
/** admin/benefits/index.php — mesmo padrão de admin/clients/index.php. */

declare(strict_types=1);

require __DIR__ . '/../../config/bootstrap.php';
require __DIR__ . '/../../includes/AdminAuth.php';
require __DIR__ . '/../../repositories/BenefitRepository.php';

$adminCurrentUser = admin_require_login();
$csrfToken = admin_csrf_token();

$benefits = [];
$dbError = false;
try {
    $benefits = (new BenefitRepository())->allForAdmin();
} catch (Throwable $e) {
    error_log('CT Price CMS [admin/benefits]: falha ao listar — ' . $e->getMessage());
    $dbError = true;
}

$adminPageTitle = 'Benefícios';
$adminActiveMenu = 'benefits';
require __DIR__ . '/../includes/layout-header.php';
?>
<div class="admin-header">
    <div>
        <h1>Benefícios</h1>
        <p>Exibidos em /trabalhe-conosco/#beneficios.</p>
    </div>
    <a class="admin-btn admin-btn--primary" href="<?= BASE_URL ?>/admin/benefits/form.php">+ Novo benefício</a>
</div>

<?php if ($dbError): ?>
<div class="admin-alert admin-alert--error">Não foi possível carregar a lista agora.</div>
<?php elseif (!$benefits): ?>
<div class="admin-panel"><p class="admin-empty-state">Nenhum benefício cadastrado ainda.</p></div>
<?php else: ?>
<div class="admin-panel">
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th>Imagem</th><th>Nome</th><th>Ordem</th><th>Status</th><th>Ações</th></tr></thead>
            <tbody>
                <?php foreach ($benefits as $index => $benefit): ?>
                <tr>
                    <td><img class="admin-table__logo" src="<?= BASE_URL ?>/<?= htmlspecialchars($benefit['imagem_path'], ENT_QUOTES, 'UTF-8') ?>" alt=""></td>
                    <td class="admin-table__name"><?= htmlspecialchars($benefit['nome'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <div class="admin-table__actions">
                            <form method="post" action="<?= BASE_URL ?>/admin/benefits/move.php">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="id" value="<?= (int) $benefit['id'] ?>">
                                <input type="hidden" name="direction" value="up">
                                <button type="submit" class="admin-btn admin-btn--outline admin-btn--sm" <?= $index === 0 ? 'disabled' : '' ?> aria-label="Subir">&uarr;</button>
                            </form>
                            <form method="post" action="<?= BASE_URL ?>/admin/benefits/move.php">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="id" value="<?= (int) $benefit['id'] ?>">
                                <input type="hidden" name="direction" value="down">
                                <button type="submit" class="admin-btn admin-btn--outline admin-btn--sm" <?= $index === count($benefits) - 1 ? 'disabled' : '' ?> aria-label="Descer">&darr;</button>
                            </form>
                        </div>
                    </td>
                    <td>
                        <span class="admin-badge <?= ((int) $benefit['ativo'] === 1) ? 'admin-badge--active' : 'admin-badge--inactive' ?>">
                            <?= ((int) $benefit['ativo'] === 1) ? 'Ativo' : 'Inativo' ?>
                        </span>
                    </td>
                    <td>
                        <div class="admin-table__actions">
                            <a class="admin-btn admin-btn--outline admin-btn--sm" href="<?= BASE_URL ?>/admin/benefits/form.php?id=<?= (int) $benefit['id'] ?>">Editar</a>
                            <form method="post" action="<?= BASE_URL ?>/admin/benefits/toggle.php">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="id" value="<?= (int) $benefit['id'] ?>">
                                <button type="submit" class="admin-btn admin-btn--outline admin-btn--sm"><?= ((int) $benefit['ativo'] === 1) ? 'Desativar' : 'Ativar' ?></button>
                            </form>
                            <form method="post" action="<?= BASE_URL ?>/admin/benefits/delete.php" data-confirm="Excluir definitivamente este benefício? Esta ação não pode ser desfeita.">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="id" value="<?= (int) $benefit['id'] ?>">
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
