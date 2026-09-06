<?php
/** admin/benefits/form.php — mesmo padrão de admin/clients/form.php. */

declare(strict_types=1);

require __DIR__ . '/../../config/bootstrap.php';
require __DIR__ . '/../../includes/AdminAuth.php';
require __DIR__ . '/../../repositories/BenefitRepository.php';

$adminCurrentUser = admin_require_login();
$csrfToken = admin_csrf_token();

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$benefit = null;
$dbError = false;

if ($id !== null) {
    try {
        $benefit = (new BenefitRepository())->find($id);
    } catch (Throwable $e) {
        error_log('CT Price CMS [admin/benefits/form]: ' . $e->getMessage());
        $dbError = true;
    }
    if (!$dbError && !$benefit) {
        admin_flash_set('error', 'Benefício não encontrado.');
        header('Location: ' . BASE_URL . '/admin/benefits/', true, 302);
        exit;
    }
}

$isEdit = $benefit !== null;
$adminPageTitle = $isEdit ? 'Editar benefício' : 'Novo benefício';
$adminActiveMenu = 'benefits';
require __DIR__ . '/../includes/layout-header.php';
?>
<div class="admin-header">
    <div><h1><?= $isEdit ? 'Editar benefício' : 'Novo benefício' ?></h1></div>
    <a class="admin-btn admin-btn--outline" href="<?= BASE_URL ?>/admin/benefits/">&larr; Voltar</a>
</div>

<?php if ($dbError): ?>
<div class="admin-alert admin-alert--error">Não foi possível carregar o formulário agora.</div>
<?php else: ?>
<div class="admin-panel">
    <form class="admin-form" method="post" action="<?= BASE_URL ?>/admin/benefits/save.php" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <?php if ($isEdit): ?><input type="hidden" name="id" value="<?= (int) $benefit['id'] ?>"><?php endif; ?>

        <div class="admin-form__group">
            <label for="nome">Nome do benefício</label>
            <input type="text" id="nome" name="nome" required maxlength="190" value="<?= htmlspecialchars($benefit['nome'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <p class="admin-form__hint">Usado também como texto alternativo da imagem.</p>
        </div>

        <div class="admin-form__group">
            <label for="imagem">Imagem <?= $isEdit ? '(opcional — envie só para substituir)' : '' ?></label>
            <?php if ($isEdit && !empty($benefit['imagem_path'])): ?>
            <div class="admin-form__current-logo">
                <img src="<?= BASE_URL ?>/<?= htmlspecialchars($benefit['imagem_path'], ENT_QUOTES, 'UTF-8') ?>" alt="">
                <span class="admin-form__hint">Imagem atual</span>
            </div>
            <?php endif; ?>
            <input type="file" id="imagem" name="imagem" accept="image/png,image/jpeg,image/webp" <?= $isEdit ? '' : 'required' ?>>
            <p class="admin-form__hint">PNG, JPEG ou WEBP — máximo 5MB.</p>
        </div>

        <div class="admin-form__actions">
            <button type="submit" class="admin-btn admin-btn--primary">Salvar</button>
            <a class="admin-btn admin-btn--outline" href="<?= BASE_URL ?>/admin/benefits/">Cancelar</a>
        </div>
    </form>
</div>
<?php endif; ?>

<?php require __DIR__ . '/../includes/layout-footer.php'; ?>
