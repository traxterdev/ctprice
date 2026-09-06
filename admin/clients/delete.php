<?php
/**
 * admin/clients/delete.php
 *
 * Exclusão FÍSICA de um cliente — o formulário que envia aqui (admin/clients/index.php) já exige
 * confirmação via `confirm()` nativo (data-confirm, ver assets/js/admin.js). Remove também o
 * arquivo de logo, mas SÓ se ele pertencer a assets/uploads/ (ver
 * includes/Uploads.php::ctprice_admin_delete_old_logo_if_owned() — nunca apaga um asset original
 * do site migrado de config/clients.php).
 */

declare(strict_types=1);

require __DIR__ . '/../../config/bootstrap.php';
require __DIR__ . '/../../includes/AdminAuth.php';
require __DIR__ . '/../../includes/Uploads.php';
require __DIR__ . '/../../repositories/ClientRepository.php';

admin_require_login();

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !admin_verify_csrf($_POST['csrf_token'] ?? null)) {
    header('Location: ' . BASE_URL . '/admin/clients/', true, 302);
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

try {
    $logoPath = (new ClientRepository())->delete($id);
    if ($logoPath !== '') {
        ctprice_admin_delete_old_logo_if_owned($logoPath);
    }
    admin_flash_set('success', 'Cliente excluído.');
} catch (Throwable $e) {
    error_log('CT Price CMS [admin/clients/delete]: ' . $e->getMessage());
    admin_flash_set('error', 'Não foi possível excluir agora.');
}

header('Location: ' . BASE_URL . '/admin/clients/', true, 302);
exit;
