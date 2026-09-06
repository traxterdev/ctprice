<?php
/** admin/testimonials/form.php — mesmo padrão de admin/clients/form.php. */

declare(strict_types=1);

require __DIR__ . '/../../config/bootstrap.php';
require __DIR__ . '/../../includes/AdminAuth.php';
require __DIR__ . '/../../repositories/TestimonialRepository.php';

$adminCurrentUser = admin_require_login();
$csrfToken = admin_csrf_token();

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$t = null;
$dbError = false;

if ($id !== null) {
    try {
        $t = (new TestimonialRepository())->find($id);
    } catch (Throwable $e) {
        error_log('CT Price CMS [admin/testimonials/form]: ' . $e->getMessage());
        $dbError = true;
    }
    if (!$dbError && !$t) {
        admin_flash_set('error', 'Depoimento não encontrado.');
        header('Location: ' . BASE_URL . '/admin/testimonials/', true, 302);
        exit;
    }
}

$isEdit = $t !== null;
$adminPageTitle = $isEdit ? 'Editar depoimento' : 'Novo depoimento';
$adminActiveMenu = 'testimonials';
require __DIR__ . '/../includes/layout-header.php';
?>
<div class="admin-header">
    <div><h1><?= $isEdit ? 'Editar depoimento' : 'Novo depoimento' ?></h1></div>
    <a class="admin-btn admin-btn--outline" href="<?= BASE_URL ?>/admin/testimonials/">&larr; Voltar</a>
</div>

<?php if ($dbError): ?>
<div class="admin-alert admin-alert--error">Não foi possível carregar o formulário agora.</div>
<?php else: ?>
<div class="admin-panel">
    <form class="admin-form" method="post" action="<?= BASE_URL ?>/admin/testimonials/save.php" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <?php if ($isEdit): ?><input type="hidden" name="id" value="<?= (int) $t['id'] ?>"><?php endif; ?>

        <div class="admin-form__group">
            <label for="nome">Nome</label>
            <input type="text" id="nome" name="nome" required maxlength="190" value="<?= htmlspecialchars($t['nome'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="admin-form__group">
            <label for="empresa">Empresa</label>
            <input type="text" id="empresa" name="empresa" required maxlength="190" value="<?= htmlspecialchars($t['empresa'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="admin-form__group">
            <label for="depoimento">Depoimento (citação)</label>
            <textarea id="depoimento" name="depoimento" rows="3" required style="width:100%; padding:10px 12px; border:1px solid #D5DCDA; border-radius:6px; font-family:inherit; font-size:14px;"><?= htmlspecialchars($t['depoimento'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div class="admin-form__group">
            <label for="video_id">ID do vídeo no YouTube</label>
            <input type="text" id="video_id" name="video_id" required maxlength="32" placeholder="ex.: yNKcg8QHjws" value="<?= htmlspecialchars($t['video_id'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <p class="admin-form__hint">O trecho depois de "v=" ou "youtu.be/" na URL do vídeo.</p>
        </div>

        <div class="admin-form__group">
            <label for="video_list">ID da playlist do YouTube (opcional)</label>
            <input type="text" id="video_list" name="video_list" maxlength="64" value="<?= htmlspecialchars($t['video_list'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="admin-form__group">
            <label for="site_url">Site (opcional)</label>
            <input type="url" id="site_url" name="site_url" maxlength="500" placeholder="https://" value="<?= htmlspecialchars($t['site_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="admin-form__group">
            <label for="instagram_url">Instagram (opcional)</label>
            <input type="url" id="instagram_url" name="instagram_url" maxlength="500" placeholder="https://instagram.com/..." value="<?= htmlspecialchars($t['instagram_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="admin-form__group">
            <label for="foto">Foto / miniatura do vídeo <?= $isEdit ? '(opcional — envie só para substituir)' : '' ?></label>
            <?php if ($isEdit && !empty($t['foto_path'])): ?>
            <div class="admin-form__current-logo">
                <img src="<?= BASE_URL ?>/<?= htmlspecialchars($t['foto_path'], ENT_QUOTES, 'UTF-8') ?>" alt="">
                <span class="admin-form__hint">Foto atual (usada também como miniatura do vídeo)</span>
            </div>
            <?php endif; ?>
            <input type="file" id="foto" name="foto" accept="image/png,image/jpeg,image/webp" <?= $isEdit ? '' : 'required' ?>>
            <p class="admin-form__hint">PNG, JPEG ou WEBP — máximo 5MB. A mesma imagem é usada como foto da pessoa e como miniatura do vídeo.</p>
        </div>

        <div class="admin-form__actions">
            <button type="submit" class="admin-btn admin-btn--primary">Salvar</button>
            <a class="admin-btn admin-btn--outline" href="<?= BASE_URL ?>/admin/testimonials/">Cancelar</a>
        </div>
    </form>
</div>
<?php endif; ?>

<?php require __DIR__ . '/../includes/layout-footer.php'; ?>
