<?php
/**
 * admin/users/delete.php
 *
 * Mesmas duas proteções de admin/users/toggle.php (não a própria conta; nunca zerar os
 * administradores ativos), reforçadas no servidor. Exclusão física exige confirmação no cliente
 * (data-confirm, ver admin/users/index.php) — mesma convenção dos demais módulos.
 */

declare(strict_types=1);

require __DIR__ . '/../../config/bootstrap.php';
require __DIR__ . '/../../includes/AdminAuth.php';
require __DIR__ . '/../../repositories/AdminUserRepository.php';

$adminCurrentUser = admin_require_login();

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !admin_verify_csrf($_POST['csrf_token'] ?? null)) {
    header('Location: ' . BASE_URL . '/admin/users/', true, 302);
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

try {
    $repository = new AdminUserRepository();
    $user = $repository->find($id);

    if (!$user) {
        admin_flash_set('error', 'Administrador não encontrado.');
        header('Location: ' . BASE_URL . '/admin/users/', true, 302);
        exit;
    }

    if ((int) $user['id'] === (int) $adminCurrentUser['id']) {
        admin_flash_set('error', 'Você não pode excluir sua própria conta.');
        header('Location: ' . BASE_URL . '/admin/users/', true, 302);
        exit;
    }

    if ((int) $user['ativo'] === 1 && $repository->countActive() <= 1) {
        admin_flash_set('error', 'Não é possível excluir: precisa haver ao menos um administrador ativo.');
        header('Location: ' . BASE_URL . '/admin/users/', true, 302);
        exit;
    }

    $repository->delete($id);
    admin_flash_set('success', 'Administrador excluído.');
} catch (Throwable $e) {
    error_log('CT Price CMS [admin/users/delete]: ' . $e->getMessage());
    admin_flash_set('error', 'Não foi possível excluir agora.');
}

header('Location: ' . BASE_URL . '/admin/users/', true, 302);
exit;
