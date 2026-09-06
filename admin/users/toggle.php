<?php
/**
 * admin/users/toggle.php
 *
 * Regras obrigatórias (§1 da tarefa), reforçadas aqui NO SERVIDOR (a UI já desabilita o botão em
 * admin/users/index.php, mas nunca é a única defesa):
 *   - o administrador logado não pode desativar a própria conta;
 *   - o sistema nunca pode ficar sem nenhum administrador ativo (vale para QUALQUER conta, não só
 *     a própria — desativar o único administrador ativo restante é bloqueado mesmo que seja outra
 *     conta, não a de quem está logado).
 * "Ativar" uma conta nunca é bloqueado por nenhuma dessas regras (só reduzir o número de ativos é
 * que precisa de cuidado).
 */

declare(strict_types=1);

require __DIR__ . '/../../config/bootstrap.php';
require __DIR__ . '/../../includes/AdminAuth.php';
require __DIR__ . '/../../repositories/AdminUserRepository.php';

$adminCurrentUser = admin_require_login();

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !admin_verify_csrf($_POST['csrf_token'] ?? null)) {
    header('Location: ' . BASE_URL . '/admin/users/', true, 302);
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

try {
    $repository = new AdminUserRepository();
    $user = $repository->find($id);

    if (!$user) {
        admin_flash_set('error', 'Administrador não encontrado.');
        header('Location: ' . BASE_URL . '/admin/users/', true, 302);
        exit;
    }

    $isCurrentlyActive = (int) $user['ativo'] === 1;

    if ($isCurrentlyActive) {
        if ((int) $user['id'] === (int) $adminCurrentUser['id']) {
            admin_flash_set('error', 'Você não pode desativar sua própria conta.');
            header('Location: ' . BASE_URL . '/admin/users/', true, 302);
            exit;
        }
        if ($repository->countActive() <= 1) {
            admin_flash_set('error', 'Não é possível desativar: precisa haver ao menos um administrador ativo.');
            header('Location: ' . BASE_URL . '/admin/users/', true, 302);
            exit;
        }
    }

    $repository->setActive($id, !$isCurrentlyActive);
    admin_flash_set('success', 'Status atualizado.');
} catch (Throwable $e) {
    error_log('CT Price CMS [admin/users/toggle]: ' . $e->getMessage());
    admin_flash_set('error', 'Não foi possível atualizar agora.');
}

header('Location: ' . BASE_URL . '/admin/users/', true, 302);
exit;
