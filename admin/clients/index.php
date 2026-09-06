<?php
/**
 * admin/clients/index.php
 *
 * Listagem administrativa de Clientes — ordenar (setas), ativar/desativar, editar, excluir.
 */

declare(strict_types=1);

require __DIR__ . '/../../config/bootstrap.php';
require __DIR__ . '/../../includes/AdminAuth.php';
require __DIR__ . '/../../repositories/ClientRepository.php';

$adminCurrentUser = admin_require_login();
$csrfToken = admin_csrf_token();

$clients = [];
$dbError = false;
try {
    $clients = (new ClientRepository())->allForAdmin();
} catch (Throwable $e) {
    error_log('CT Price CMS [admin/clients]: falha ao listar — ' . $e->getMessage());
    $dbError = true;
}

$adminPageTitle = 'Clientes';
$adminActiveMenu = 'clients';
require __DIR__ . '/../includes/layout-header.php';
?>
<div class="admin-header">
    <div>
        <h1>Clientes</h1>
        <p>Logos exibidos no carrossel da Home/Sobre Nós/Informações e na grade de /clientes/.</p>
    </div>
    <a class="admin-btn admin-btn--primary" href="<?= BASE_URL ?>/admin/clients/form.php">+ Novo cliente</a>
</div>

<?php if ($dbError): ?>
<div class="admin-alert admin-alert--error">Não foi possível carregar a lista agora. Tente novamente em instantes.</div>
<?php elseif (!$clients): ?>
<div class="admin-panel">
    <p class="admin-empty-state">Nenhum cliente cadastrado ainda.</p>
</div>
<?php else: ?>
<div class="admin-panel">
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Logo</th>
                    <th>Nome</th>
                    <th>Ordem</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clients as $index => $client): ?>
                <tr>
                    <td>
                        <img class="admin-table__logo" src="<?= BASE_URL ?>/<?= htmlspecialchars($client['logo_path'], ENT_QUOTES, 'UTF-8') ?>" alt="">
                    </td>
                    <td class="admin-table__name"><?= htmlspecialchars($client['nome'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <div class="admin-table__actions">
                            <form method="post" action="<?= BASE_URL ?>/admin/clients/move.php">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="id" value="<?= (int) $client['id'] ?>">
                                <input type="hidden" name="direction" value="up">
                                <button type="submit" class="admin-btn admin-btn--outline admin-btn--sm" <?= $index === 0 ? 'disabled' : '' ?> aria-label="Subir">&uarr;</button>
                            </form>
                            <form method="post" action="<?= BASE_URL ?>/admin/clients/move.php">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="id" value="<?= (int) $client['id'] ?>">
                                <input type="hidden" name="direction" value="down">
                                <button type="submit" class="admin-btn admin-btn--outline admin-btn--sm" <?= $index === count($clients) - 1 ? 'disabled' : '' ?> aria-label="Descer">&darr;</button>
                            </form>
                        </div>
                    </td>
                    <td>
                        <span class="admin-badge <?= ((int) $client['ativo'] === 1) ? 'admin-badge--active' : 'admin-badge--inactive' ?>">
                            <?= ((int) $client['ativo'] === 1) ? 'Ativo' : 'Inativo' ?>
                        </span>
                    </td>
                    <td>
                        <div class="admin-table__actions">
                            <a class="admin-btn admin-btn--outline admin-btn--sm" href="<?= BASE_URL ?>/admin/clients/form.php?id=<?= (int) $client['id'] ?>">Editar</a>
                            <form method="post" action="<?= BASE_URL ?>/admin/clients/toggle.php">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="id" value="<?= (int) $client['id'] ?>">
                                <button type="submit" class="admin-btn admin-btn--outline admin-btn--sm">
                                    <?= ((int) $client['ativo'] === 1) ? 'Desativar' : 'Ativar' ?>
                                </button>
                            </form>
                            <form method="post" action="<?= BASE_URL ?>/admin/clients/delete.php" data-confirm="Excluir definitivamente este cliente? Esta ação não pode ser desfeita.">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="id" value="<?= (int) $client['id'] ?>">
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
