<?php
/**
 * admin/jobs/save.php — mesmo padrão de admin/clients/save.php (sem upload).
 */

declare(strict_types=1);

require __DIR__ . '/../../config/bootstrap.php';
require __DIR__ . '/../../includes/AdminAuth.php';
require __DIR__ . '/../../repositories/JobRepository.php';

admin_require_login();

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Location: ' . BASE_URL . '/admin/jobs/', true, 302);
    exit;
}

if (!admin_verify_csrf($_POST['csrf_token'] ?? null)) {
    admin_flash_set('error', 'Sua sessão expirou. Tente novamente.');
    header('Location: ' . BASE_URL . '/admin/jobs/', true, 302);
    exit;
}

$id = isset($_POST['id']) && $_POST['id'] !== '' ? (int) $_POST['id'] : null;
$titulo = trim((string) ($_POST['titulo'] ?? ''));
$requisitos = JobRepository::fromListArray(explode("\n", str_replace("\r\n", "\n", (string) ($_POST['requisitos'] ?? ''))));
$diferenciais = JobRepository::fromListArray(explode("\n", str_replace("\r\n", "\n", (string) ($_POST['diferenciais'] ?? ''))));

$backTo = $id ? (BASE_URL . '/admin/jobs/form.php?id=' . $id) : (BASE_URL . '/admin/jobs/form.php');

if ($titulo === '' || mb_strlen($titulo) > 190) {
    admin_flash_set('error', 'Informe um título válido (até 190 caracteres).');
    header('Location: ' . $backTo, true, 302);
    exit;
}

if ($requisitos === '') {
    admin_flash_set('error', 'Informe ao menos um pré-requisito.');
    header('Location: ' . $backTo, true, 302);
    exit;
}

$repository = new JobRepository();

try {
    if ($id === null) {
        $repository->create($titulo, $requisitos, $diferenciais !== '' ? $diferenciais : null, $repository->nextOrder());
        admin_flash_set('success', 'Vaga criada com sucesso.');
    } else {
        if (!$repository->find($id)) {
            admin_flash_set('error', 'Vaga não encontrada.');
            header('Location: ' . BASE_URL . '/admin/jobs/', true, 302);
            exit;
        }
        $repository->update($id, $titulo, $requisitos, $diferenciais !== '' ? $diferenciais : null);
        admin_flash_set('success', 'Vaga atualizada com sucesso.');
    }
} catch (Throwable $e) {
    error_log('CT Price CMS [admin/jobs/save]: ' . $e->getMessage());
    admin_flash_set('error', 'Não foi possível salvar agora. Tente novamente em instantes.');
    header('Location: ' . $backTo, true, 302);
    exit;
}

header('Location: ' . BASE_URL . '/admin/jobs/', true, 302);
exit;
