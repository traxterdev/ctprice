<?php
/**
 * admin/hero/delete.php — mesmo padrão de admin/partners/delete.php.
 */

declare(strict_types=1);

require __DIR__ . '/../../config/bootstrap.php';
require __DIR__ . '/../../includes/AdminAuth.php';
require __DIR__ . '/../../includes/Uploads.php';
require __DIR__ . '/../../repositories/HeroSlideRepository.php';

admin_require_login();

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !admin_verify_csrf($_POST['csrf_token'] ?? null)) {
    header('Location: ' . BASE_URL . '/admin/hero/', true, 302);
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

try {
    $imagemPath = (new HeroSlideRepository())->delete($id);
    if ($imagemPath !== '') {
        ctprice_admin_delete_old_logo_if_owned($imagemPath);
    }
    admin_flash_set('success', 'Banner excluído.');
} catch (Throwable $e) {
    error_log('CT Price CMS [admin/hero/delete]: ' . $e->getMessage());
    admin_flash_set('error', 'Não foi possível excluir agora.');
}

header('Location: ' . BASE_URL . '/admin/hero/', true, 302);
exit;
