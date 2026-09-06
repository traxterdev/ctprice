<?php
/** admin/jobs/toggle.php — mesmo padrão de admin/clients/toggle.php. */

declare(strict_types=1);

require __DIR__ . '/../../config/bootstrap.php';
require __DIR__ . '/../../includes/AdminAuth.php';
require __DIR__ . '/../../repositories/JobRepository.php';

admin_require_login();

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !admin_verify_csrf($_POST['csrf_token'] ?? null)) {
    header('Location: ' . BASE_URL . '/admin/jobs/', true, 302);
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

try {
    $repository = new JobRepository();
    $job = $repository->find($id);
    if ($job) {
        $repository->setActive($id, (int) $job['ativo'] !== 1);
        admin_flash_set('success', 'Status atualizado.');
    } else {
        admin_flash_set('error', 'Vaga não encontrada.');
    }
} catch (Throwable $e) {
    error_log('CT Price CMS [admin/jobs/toggle]: ' . $e->getMessage());
    admin_flash_set('error', 'Não foi possível atualizar agora.');
}

header('Location: ' . BASE_URL . '/admin/jobs/', true, 302);
exit;
