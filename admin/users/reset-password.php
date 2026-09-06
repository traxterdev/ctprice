<?php
/**
 * admin/users/reset-password.php
 *
 * Fluxo próprio e separado da edição de perfil (nome/e-mail) — troca SÓ a senha, nunca a exibe
 * (nem antes nem depois), nunca a envia por e-mail, nunca a registra em log (ver §2 da tarefa).
 * Qualquer administrador logado pode redefinir a senha de qualquer conta, incluindo a própria —
 * a única restrição de auto-proteção é sobre desativar/excluir a própria conta (toggle.php/
 * delete.php), não sobre redefinir a própria senha.
 */

declare(strict_types=1);

require __DIR__ . '/../../config/bootstrap.php';
require __DIR__ . '/../../includes/AdminAuth.php';
require __DIR__ . '/../../repositories/AdminUserRepository.php';

$adminCurrentUser = admin_require_login();

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$repository = new AdminUserRepository();
$user = null;
$dbError = false;

try {
    $user = $repository->find($id);
} catch (Throwable $e) {
    error_log('CT Price CMS [admin/users/reset-password]: ' . $e->getMessage());
    $dbError = true;
}

if (!$dbError && !$user) {
    admin_flash_set('error', 'Administrador não encontrado.');
    header('Location: ' . BASE_URL . '/admin/users/', true, 302);
    exit;
}

$error = null;

if (!$dbError && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    if (!admin_verify_csrf($_POST['csrf_token'] ?? null)) {
        $error = 'Sua sessão expirou. Atualize a página e tente novamente.';
    } else {
        $password = (string) ($_POST['password'] ?? '');
        $confirmation = (string) ($_POST['password_confirmation'] ?? '');

        if (!admin_is_strong_password($password)) {
            $error = ADMIN_PASSWORD_HINT;
        } elseif (!hash_equals($password, $confirmation)) {
            $error = 'A confirmação de senha não corresponde à senha informada.';
        } else {
            try {
                $repository->resetPassword($id, password_hash($password, PASSWORD_DEFAULT));
                admin_flash_set('success', 'Senha redefinida com sucesso.');
                header('Location: ' . BASE_URL . '/admin/users/', true, 302);
                exit;
            } catch (Throwable $e) {
                error_log('CT Price CMS [admin/users/reset-password]: ' . $e->getMessage());
                $error = 'Não foi possível redefinir a senha agora. Tente novamente em instantes.';
            }
        }
    }
}

$csrfToken = admin_csrf_token();
$adminPageTitle = 'Redefinir senha';
$adminActiveMenu = 'users';
require __DIR__ . '/../includes/layout-header.php';
?>
<div class="admin-header">
    <div><h1>Redefinir senha</h1></div>
    <a class="admin-btn admin-btn--outline" href="<?= BASE_URL ?>/admin/users/">&larr; Voltar</a>
</div>

<?php if ($dbError): ?>
<div class="admin-alert admin-alert--error">Não foi possível carregar esta tela agora.</div>
<?php else: ?>
<?php if ($error): ?>
<div class="admin-alert admin-alert--error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>
<div class="admin-panel">
    <form class="admin-form" method="post" action="<?= BASE_URL ?>/admin/users/reset-password.php?id=<?= (int) $user['id'] ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">

        <p class="admin-form__hint" style="margin-bottom:16px;">Nova senha para <strong><?= htmlspecialchars($user['nome'], ENT_QUOTES, 'UTF-8') ?></strong> (<?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?>).</p>

        <div class="admin-form__group">
            <label for="password">Nova senha</label>
            <input type="password" id="password" name="password" required autocomplete="new-password">
            <p class="admin-form__hint"><?= ADMIN_PASSWORD_HINT ?></p>
        </div>

        <div class="admin-form__group">
            <label for="password_confirmation">Confirmar nova senha</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
        </div>

        <div class="admin-form__actions">
            <button type="submit" class="admin-btn admin-btn--primary">Redefinir senha</button>
            <a class="admin-btn admin-btn--outline" href="<?= BASE_URL ?>/admin/users/">Cancelar</a>
        </div>
    </form>
</div>
<?php endif; ?>

<?php require __DIR__ . '/../includes/layout-footer.php'; ?>
