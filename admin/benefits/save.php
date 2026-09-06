<?php
/** admin/benefits/save.php — mesmo padrão de admin/clients/save.php. */

declare(strict_types=1);

require __DIR__ . '/../../config/bootstrap.php';
require __DIR__ . '/../../includes/AdminAuth.php';
require __DIR__ . '/../../includes/Uploads.php';
require __DIR__ . '/../../repositories/BenefitRepository.php';

admin_require_login();

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Location: ' . BASE_URL . '/admin/benefits/', true, 302);
    exit;
}

if (!admin_verify_csrf($_POST['csrf_token'] ?? null)) {
    admin_flash_set('error', 'Sua sessão expirou. Tente novamente.');
    header('Location: ' . BASE_URL . '/admin/benefits/', true, 302);
    exit;
}

$id = isset($_POST['id']) && $_POST['id'] !== '' ? (int) $_POST['id'] : null;
$nome = trim((string) ($_POST['nome'] ?? ''));
$backTo = $id ? (BASE_URL . '/admin/benefits/form.php?id=' . $id) : (BASE_URL . '/admin/benefits/form.php');

if ($nome === '' || mb_strlen($nome) > 190) {
    admin_flash_set('error', 'Informe um nome válido (até 190 caracteres).');
    header('Location: ' . $backTo, true, 302);
    exit;
}

$repository = new BenefitRepository();
$hasUpload = isset($_FILES['imagem']) && ($_FILES['imagem']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
$newImagePath = null;

if ($hasUpload) {
    try {
        $newImagePath = ctprice_admin_handle_logo_upload($_FILES['imagem'], 'benefits');
    } catch (RuntimeException $e) {
        admin_flash_set('error', $e->getMessage());
        header('Location: ' . $backTo, true, 302);
        exit;
    }
} elseif ($id === null) {
    admin_flash_set('error', 'Envie uma imagem para o novo benefício.');
    header('Location: ' . $backTo, true, 302);
    exit;
}

try {
    if ($id === null) {
        $repository->create($nome, (string) $newImagePath, $repository->nextOrder());
        admin_flash_set('success', 'Benefício criado com sucesso.');
    } else {
        $existing = $repository->find($id);
        if (!$existing) {
            admin_flash_set('error', 'Benefício não encontrado.');
            header('Location: ' . BASE_URL . '/admin/benefits/', true, 302);
            exit;
        }
        $repository->update($id, $nome, $newImagePath);
        if ($newImagePath !== null) {
            ctprice_admin_delete_old_logo_if_owned($existing['imagem_path']);
        }
        admin_flash_set('success', 'Benefício atualizado com sucesso.');
    }
} catch (Throwable $e) {
    error_log('CT Price CMS [admin/benefits/save]: ' . $e->getMessage());
    admin_flash_set('error', 'Não foi possível salvar agora. Tente novamente em instantes.');
    header('Location: ' . $backTo, true, 302);
    exit;
}

header('Location: ' . BASE_URL . '/admin/benefits/', true, 302);
exit;
