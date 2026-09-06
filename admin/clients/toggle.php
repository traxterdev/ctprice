<?php
/**
 * admin/clients/toggle.php
 *
 * Ativa/desativa um cliente (retirada do site preferida à exclusão física — ver §12 da tarefa).
 */

declare(strict_types=1);

require __DIR__ . '/../../config/bootstrap.php';
require __DIR__ . '/../../includes/AdminAuth.php';
require __DIR__ . '/../../repositories/ClientRepository.php';

admin_require_login();

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !admin_verify_csrf($_POST['csrf_token'] ?? null)) {
    header('Location: ' . BASE_URL . '/admin/clients/', true, 302);
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

try {
    $repository = new ClientRepository();
    $client = $repository->find($id);
    if ($client) {
        $repository->setActive($id, (int) $client['ativo'] !== 1);
        admin_flash_set('success', 'Status atualizado.');
    } else {
        admin_flash_set('error', 'Cliente não encontrado.');
    }
} catch (Throwable $e) {
    error_log('CT Price CMS [admin/clients/toggle]: ' . $e->getMessage());
    admin_flash_set('error', 'Não foi possível atualizar agora.');
}

header('Location: ' . BASE_URL . '/admin/clients/', true, 302);
exit;
