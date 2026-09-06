<?php
/** admin/benefits/delete.php — mesmo padrão de admin/clients/delete.php. */

declare(strict_types=1);

require __DIR__ . '/../../config/bootstrap.php';
require __DIR__ . '/../../includes/AdminAuth.php';
require __DIR__ . '/../../includes/Uploads.php';
require __DIR__ . '/../../repositories/BenefitRepository.php';

admin_require_login();

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !admin_verify_csrf($_POST['csrf_token'] ?? null)) {
    header('Location: ' . BASE_URL . '/admin/benefits/', true, 302);
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

try {
    $imagePath = (new BenefitRepository())->delete($id);
    if ($imagePath !== '') {
        ctprice_admin_delete_old_logo_if_owned($imagePath);
    }
    admin_flash_set('success', 'Benefício excluído.');
} catch (Throwable $e) {
    error_log('CT Price CMS [admin/benefits/delete]: ' . $e->getMessage());
    admin_flash_set('error', 'Não foi possível excluir agora.');
}

header('Location: ' . BASE_URL . '/admin/benefits/', true, 302);
exit;
