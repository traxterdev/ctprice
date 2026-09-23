<?php
/**
 * admin/hero/toggle.php — mesmo padrão de admin/partners/toggle.php.
 */

declare(strict_types=1);

require __DIR__ . '/../../config/bootstrap.php';
require __DIR__ . '/../../includes/AdminAuth.php';
require __DIR__ . '/../../repositories/HeroSlideRepository.php';

admin_require_login();

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !admin_verify_csrf($_POST['csrf_token'] ?? null)) {
    header('Location: ' . BASE_URL . '/admin/hero/', true, 302);
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

try {
    $repository = new HeroSlideRepository();
    $slide = $repository->find($id);
    if ($slide) {
        $repository->setActive($id, (int) $slide['ativo'] !== 1);
        admin_flash_set('success', 'Status atualizado.');
    } else {
        admin_flash_set('error', 'Banner não encontrado.');
    }
} catch (Throwable $e) {
    error_log('CT Price CMS [admin/hero/toggle]: ' . $e->getMessage());
    admin_flash_set('error', 'Não foi possível atualizar agora.');
}

header('Location: ' . BASE_URL . '/admin/hero/', true, 302);
exit;
