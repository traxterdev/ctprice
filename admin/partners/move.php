<?php
/**
 * admin/partners/move.php — mesmo padrão de admin/clients/move.php (reordenação sempre dentro da
 * mesma categoria — ver PartnerRepository::swapWithNeighbor()).
 */

declare(strict_types=1);

require __DIR__ . '/../../config/bootstrap.php';
require __DIR__ . '/../../includes/AdminAuth.php';
require __DIR__ . '/../../repositories/PartnerRepository.php';

admin_require_login();

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !admin_verify_csrf($_POST['csrf_token'] ?? null)) {
    header('Location: ' . BASE_URL . '/admin/partners/', true, 302);
    exit;
}

$id = (int) ($_POST['id'] ?? 0);
$direction = (string) ($_POST['direction'] ?? '');

try {
    $repository = new PartnerRepository();
    if ($direction === 'up') {
        $repository->moveUp($id);
    } elseif ($direction === 'down') {
        $repository->moveDown($id);
    }
} catch (Throwable $e) {
    error_log('CT Price CMS [admin/partners/move]: ' . $e->getMessage());
    admin_flash_set('error', 'Não foi possível reordenar agora.');
}

header('Location: ' . BASE_URL . '/admin/partners/', true, 302);
exit;
