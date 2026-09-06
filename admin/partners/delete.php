<?php
/**
 * admin/partners/delete.php — mesmo padrão de admin/clients/delete.php.
 */

declare(strict_types=1);

require __DIR__ . '/../../config/bootstrap.php';
require __DIR__ . '/../../includes/AdminAuth.php';
require __DIR__ . '/../../includes/Uploads.php';
require __DIR__ . '/../../repositories/PartnerRepository.php';

admin_require_login();

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !admin_verify_csrf($_POST['csrf_token'] ?? null)) {
    header('Location: ' . BASE_URL . '/admin/partners/', true, 302);
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

try {
    $logoPath = (new PartnerRepository())->delete($id);
    if ($logoPath !== '') {
        ctprice_admin_delete_old_logo_if_owned($logoPath);
    }
    admin_flash_set('success', 'Parceiro excluído.');
} catch (Throwable $e) {
    error_log('CT Price CMS [admin/partners/delete]: ' . $e->getMessage());
    admin_flash_set('error', 'Não foi possível excluir agora.');
}

header('Location: ' . BASE_URL . '/admin/partners/', true, 302);
exit;
