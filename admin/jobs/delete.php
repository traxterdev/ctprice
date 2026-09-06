<?php
/** admin/jobs/delete.php — mesmo padrão de admin/clients/delete.php (sem arquivo a limpar). */

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
    (new JobRepository())->delete($id);
    admin_flash_set('success', 'Vaga excluída.');
} catch (Throwable $e) {
    error_log('CT Price CMS [admin/jobs/delete]: ' . $e->getMessage());
    admin_flash_set('error', 'Não foi possível excluir agora.');
}

header('Location: ' . BASE_URL . '/admin/jobs/', true, 302);
exit;
