<?php
/**
 * admin/users/form.php — criação de administrador (nome/e-mail/senha) ou edição (só nome/e-mail
 * — a senha tem fluxo próprio, ver admin/users/reset-password.php).
 */

declare(strict_types=1);

require __DIR__ . '/../../config/bootstrap.php';
require __DIR__ . '/../../includes/AdminAuth.php';
require __DIR__ . '/../../repositories/AdminUserRepository.php';

$adminCurrentUser = admin_require_login();
$csrfToken = admin_csrf_token();

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$user = null;
$dbError = false;

if ($id !== null) {
    try {
        $user = (new AdminUserRepository())->find($id);
    } catch (Throwable $e) {
        error_log('CT Price CMS [admin/users/form]: ' . $e->getMessage());
        $dbError = true;
    }
    if (!$dbError && !$user) {
        admin_flash_set('error', 'Administrador não encontrado.');
        header('Location: ' . BASE_URL . '/admin/users/', true, 302);
        exit;
    }
}

$isEdit = $user !== null;
$adminPageTitle = $isEdit ? 'Editar administrador' : 'Novo administrador';
$adminActiveMenu = 'users';
require __DIR__ . '/../includes/layout-header.php';
?>
<div class="admin-header">
    <div><h1><?= $isEdit ? 'Editar administrador' : 'Novo administrador' ?></h1></div>
    <a class="admin-btn admin-btn--outline" href="<?= BASE_URL ?>/admin/users/">&larr; Voltar</a>
</div>

<?php if ($dbError): ?>
<div class="admin-alert admin-alert--error">Não foi possível carregar o formulário agora.</div>
<?php else: ?>
<div class="admin-panel">
    <form class="admin-form" method="post" action="<?= BASE_URL ?>/admin/users/save.php">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <?php if ($isEdit): ?><input type="hidden" name="id" value="<?= (int) $user['id'] ?>"><?php endif; ?>

        <div class="admin-form__group">
            <label for="nome">Nome</label>
            <input type="text" id="nome" name="nome" required maxlength="150" value="<?= htmlspecialchars($user['nome'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="admin-form__group">
            <label for="email">E-mail (usado para entrar)</label>
            <input type="email" id="email" name="email" required maxlength="190" value="<?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <?php if (!$isEdit): ?>
        <div class="admin-form__group">
            <label for="password">Senha inicial</label>
            <input type="password" id="password" name="password" required autocomplete="new-password">
            <p class="admin-form__hint"><?= ADMIN_PASSWORD_HINT ?></p>
        </div>

        <div class="admin-form__group">
            <label for="password_confirmation">Confirmar senha</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
        </div>
        <?php else: ?>
        <p class="admin-form__hint">Para trocar a senha, use "Redefinir senha" na listagem — não é feito aqui.</p>
        <?php endif; ?>

        <div class="admin-form__actions">
            <button type="submit" class="admin-btn admin-btn--primary">Salvar</button>
            <a class="admin-btn admin-btn--outline" href="<?= BASE_URL ?>/admin/users/">Cancelar</a>
        </div>
    </form>
</div>
<?php endif; ?>

<?php require __DIR__ . '/../includes/layout-footer.php'; ?>
