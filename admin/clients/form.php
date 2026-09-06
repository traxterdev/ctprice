<?php
/**
 * admin/clients/form.php
 *
 * Formulário de criação/edição de um Cliente — GET só exibe; o POST de fato é processado por
 * admin/clients/save.php (padrão POST/Redirect/GET: este arquivo nunca escreve no banco).
 */

declare(strict_types=1);

require __DIR__ . '/../../config/bootstrap.php';
require __DIR__ . '/../../includes/AdminAuth.php';
require __DIR__ . '/../../repositories/ClientRepository.php';

$adminCurrentUser = admin_require_login();
$csrfToken = admin_csrf_token();

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$client = null;
$dbError = false;

if ($id !== null) {
    try {
        $client = (new ClientRepository())->find($id);
    } catch (Throwable $e) {
        error_log('CT Price CMS [admin/clients/form]: falha ao buscar cliente — ' . $e->getMessage());
        $dbError = true;
    }
    if (!$dbError && !$client) {
        admin_flash_set('error', 'Cliente não encontrado.');
        header('Location: ' . BASE_URL . '/admin/clients/', true, 302);
        exit;
    }
}

$isEdit = $client !== null;
$adminPageTitle = $isEdit ? 'Editar cliente' : 'Novo cliente';
$adminActiveMenu = 'clients';
require __DIR__ . '/../includes/layout-header.php';
?>
<div class="admin-header">
    <div>
        <h1><?= $isEdit ? 'Editar cliente' : 'Novo cliente' ?></h1>
    </div>
    <a class="admin-btn admin-btn--outline" href="<?= BASE_URL ?>/admin/clients/">&larr; Voltar</a>
</div>

<?php if ($dbError): ?>
<div class="admin-alert admin-alert--error">Não foi possível carregar o formulário agora. Tente novamente em instantes.</div>
<?php else: ?>
<div class="admin-panel">
    <form class="admin-form" method="post" action="<?= BASE_URL ?>/admin/clients/save.php" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= (int) $client['id'] ?>">
        <?php endif; ?>

        <div class="admin-form__group">
            <label for="nome">Nome</label>
            <input type="text" id="nome" name="nome" required maxlength="190" value="<?= htmlspecialchars($client['nome'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="admin-form__group">
            <label for="site_url">Site do cliente (opcional)</label>
            <input type="url" id="site_url" name="site_url" maxlength="500" placeholder="https://" value="<?= htmlspecialchars($client['site_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="admin-form__group">
            <label for="logo">Logo <?= $isEdit ? '(opcional — envie só para substituir)' : '' ?></label>
            <?php if ($isEdit && !empty($client['logo_path'])): ?>
            <div class="admin-form__current-logo">
                <img src="<?= BASE_URL ?>/<?= htmlspecialchars($client['logo_path'], ENT_QUOTES, 'UTF-8') ?>" alt="">
                <span class="admin-form__hint">Logo atual</span>
            </div>
            <?php endif; ?>
            <input type="file" id="logo" name="logo" accept="image/png,image/jpeg,image/webp" <?= $isEdit ? '' : 'required' ?>>
            <p class="admin-form__hint">PNG, JPEG ou WEBP — máximo 5MB.</p>
        </div>

        <div class="admin-form__actions">
            <button type="submit" class="admin-btn admin-btn--primary">Salvar</button>
            <a class="admin-btn admin-btn--outline" href="<?= BASE_URL ?>/admin/clients/">Cancelar</a>
        </div>
    </form>
</div>
<?php endif; ?>

<?php require __DIR__ . '/../includes/layout-footer.php'; ?>
