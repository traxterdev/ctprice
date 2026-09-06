<?php
/**
 * admin/jobs/form.php — criação/edição de uma vaga (mesmo padrão de admin/clients/form.php).
 */

declare(strict_types=1);

require __DIR__ . '/../../config/bootstrap.php';
require __DIR__ . '/../../includes/AdminAuth.php';
require __DIR__ . '/../../repositories/JobRepository.php';

$adminCurrentUser = admin_require_login();
$csrfToken = admin_csrf_token();

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$job = null;
$dbError = false;

if ($id !== null) {
    try {
        $job = (new JobRepository())->find($id);
    } catch (Throwable $e) {
        error_log('CT Price CMS [admin/jobs/form]: ' . $e->getMessage());
        $dbError = true;
    }
    if (!$dbError && !$job) {
        admin_flash_set('error', 'Vaga não encontrada.');
        header('Location: ' . BASE_URL . '/admin/jobs/', true, 302);
        exit;
    }
}

$isEdit = $job !== null;
$adminPageTitle = $isEdit ? 'Editar vaga' : 'Nova vaga';
$adminActiveMenu = 'jobs';
require __DIR__ . '/../includes/layout-header.php';
?>
<div class="admin-header">
    <div><h1><?= $isEdit ? 'Editar vaga' : 'Nova vaga' ?></h1></div>
    <a class="admin-btn admin-btn--outline" href="<?= BASE_URL ?>/admin/jobs/">&larr; Voltar</a>
</div>

<?php if ($dbError): ?>
<div class="admin-alert admin-alert--error">Não foi possível carregar o formulário agora.</div>
<?php else: ?>
<div class="admin-panel">
    <form class="admin-form" method="post" action="<?= BASE_URL ?>/admin/jobs/save.php">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <?php if ($isEdit): ?><input type="hidden" name="id" value="<?= (int) $job['id'] ?>"><?php endif; ?>

        <div class="admin-form__group">
            <label for="titulo">Título da vaga</label>
            <input type="text" id="titulo" name="titulo" required maxlength="190" value="<?= htmlspecialchars($job['titulo'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="admin-form__group">
            <label for="requisitos">Pré-requisitos</label>
            <textarea id="requisitos" name="requisitos" rows="5" required style="width:100%; padding:10px 12px; border:1px solid #D5DCDA; border-radius:6px; font-family:inherit; font-size:14px;"><?= htmlspecialchars($job['requisitos'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            <p class="admin-form__hint">Um item por linha — cada linha vira um item da lista no site.</p>
        </div>

        <div class="admin-form__group">
            <label for="diferenciais">Diferenciais (opcional)</label>
            <textarea id="diferenciais" name="diferenciais" rows="4" style="width:100%; padding:10px 12px; border:1px solid #D5DCDA; border-radius:6px; font-family:inherit; font-size:14px;"><?= htmlspecialchars($job['diferenciais'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            <p class="admin-form__hint">Um item por linha.</p>
        </div>

        <div class="admin-form__actions">
            <button type="submit" class="admin-btn admin-btn--primary">Salvar</button>
            <a class="admin-btn admin-btn--outline" href="<?= BASE_URL ?>/admin/jobs/">Cancelar</a>
        </div>
    </form>
</div>
<?php endif; ?>

<?php require __DIR__ . '/../includes/layout-footer.php'; ?>
