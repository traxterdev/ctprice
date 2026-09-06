<?php
/**
 * admin/login.php
 *
 * Tela de login do CMS. Fluxo:
 *  - GET: exibe o formulário (token CSRF novo por sessão).
 *  - POST: valida CSRF, aplica rate limit (backoff em admin_attempt_login()), autentica.
 *
 * Se já houver sessão administrativa válida, redireciona direto para o dashboard — evita mostrar
 * a tela de login a quem já está autenticado.
 */

declare(strict_types=1);

require __DIR__ . '/../config/bootstrap.php';
require __DIR__ . '/../includes/AdminAuth.php';

admin_start_session();

if (admin_current_user() !== null) {
    header('Location: ' . BASE_URL . '/admin/', true, 302);
    exit;
}

$error = null;
$emailValue = '';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $emailValue = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if (!admin_verify_csrf($_POST['csrf_token'] ?? null)) {
        $error = 'Sua sessão expirou. Atualize a página e tente novamente.';
    } else {
        $result = admin_attempt_login($emailValue, $password);
        if ($result['success']) {
            header('Location: ' . BASE_URL . '/admin/', true, 302);
            exit;
        }
        $error = $result['message'];
    }
}

$csrfToken = admin_csrf_token();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Entrar — CT Price Admin</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/reset.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/fonts.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/variables.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css">
</head>
<body class="admin-body">
<div class="admin-login">
    <div class="admin-login__card">
        <p class="admin-login__title">CT Price</p>
        <p class="admin-login__subtitle">Painel administrativo</p>

        <?php if ($error): ?>
        <div class="admin-alert admin-alert--error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <form class="admin-form" method="post" action="<?= BASE_URL ?>/admin/login.php" style="padding:0; max-width:none;">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

            <div class="admin-form__group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" required autofocus value="<?= htmlspecialchars($emailValue, ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="admin-form__group">
                <label for="password">Senha</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>

            <button type="submit" class="admin-btn admin-btn--primary admin-login__submit">Entrar</button>
        </form>
    </div>
</div>
</body>
</html>
