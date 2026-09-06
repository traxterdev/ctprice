<?php
/**
 * admin/users/save.php — cria (com senha) ou atualiza (só nome/e-mail) um administrador.
 */

declare(strict_types=1);

require __DIR__ . '/../../config/bootstrap.php';
require __DIR__ . '/../../includes/AdminAuth.php';
require __DIR__ . '/../../repositories/AdminUserRepository.php';

admin_require_login();

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Location: ' . BASE_URL . '/admin/users/', true, 302);
    exit;
}

if (!admin_verify_csrf($_POST['csrf_token'] ?? null)) {
    admin_flash_set('error', 'Sua sessão expirou. Tente novamente.');
    header('Location: ' . BASE_URL . '/admin/users/', true, 302);
    exit;
}

$id = isset($_POST['id']) && $_POST['id'] !== '' ? (int) $_POST['id'] : null;
$backTo = $id ? (BASE_URL . '/admin/users/form.php?id=' . $id) : (BASE_URL . '/admin/users/form.php');

$nome = trim((string) ($_POST['nome'] ?? ''));
$email = admin_normalize_email((string) ($_POST['email'] ?? ''));

$repository = new AdminUserRepository();
$errors = [];

if ($nome === '' || mb_strlen($nome) > 150) {
    $errors[] = 'Informe um nome válido (até 150 caracteres).';
}
if ($email === '' || mb_strlen($email) > 190 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Informe um e-mail válido.';
} elseif (!$repository->isEmailAvailable($email, $id)) {
    $errors[] = 'Já existe um administrador com este e-mail.';
}

if ($id === null) {
    $password = (string) ($_POST['password'] ?? '');
    $confirmation = (string) ($_POST['password_confirmation'] ?? '');

    if (!admin_is_strong_password($password)) {
        $errors[] = ADMIN_PASSWORD_HINT;
    } elseif (!hash_equals($password, $confirmation)) {
        $errors[] = 'A confirmação de senha não corresponde à senha informada.';
    }
}

if ($errors) {
    admin_flash_set('error', implode(' ', $errors));
    header('Location: ' . $backTo, true, 302);
    exit;
}

try {
    if ($id === null) {
        $repository->create($nome, $email, password_hash($password, PASSWORD_DEFAULT));
        admin_flash_set('success', 'Administrador criado com sucesso.');
    } else {
        if (!$repository->find($id)) {
            admin_flash_set('error', 'Administrador não encontrado.');
            header('Location: ' . BASE_URL . '/admin/users/', true, 302);
            exit;
        }
        $repository->updateProfile($id, $nome, $email);
        admin_flash_set('success', 'Administrador atualizado com sucesso.');
    }
} catch (Throwable $e) {
    error_log('CT Price CMS [admin/users/save]: ' . $e->getMessage());
    admin_flash_set('error', 'Não foi possível salvar agora. Tente novamente em instantes.');
    header('Location: ' . $backTo, true, 302);
    exit;
}

header('Location: ' . BASE_URL . '/admin/users/', true, 302);
exit;
