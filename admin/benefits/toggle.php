<?php
/** admin/benefits/toggle.php — mesmo padrão de admin/clients/toggle.php. */

declare(strict_types=1);

require __DIR__ . '/../../config/bootstrap.php';
require __DIR__ . '/../../includes/AdminAuth.php';
require __DIR__ . '/../../repositories/BenefitRepository.php';

admin_require_login();

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !admin_verify_csrf($_POST['csrf_token'] ?? null)) {
    header('Location: ' . BASE_URL . '/admin/benefits/', true, 302);
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

try {
    $repository = new BenefitRepository();
    $benefit = $repository->find($id);
    if ($benefit) {
        $repository->setActive($id, (int) $benefit['ativo'] !== 1);
        admin_flash_set('success', 'Status atualizado.');
    } else {
        admin_flash_set('error', 'Benefício não encontrado.');
    }
} catch (Throwable $e) {
    error_log('CT Price CMS [admin/benefits/toggle]: ' . $e->getMessage());
    admin_flash_set('error', 'Não foi possível atualizar agora.');
}

header('Location: ' . BASE_URL . '/admin/benefits/', true, 302);
exit;
