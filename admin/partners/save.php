<?php
/**
 * admin/partners/save.php
 *
 * Processa o POST de admin/partners/form.php — mesmo padrão de admin/clients/save.php, com a
 * categoria como campo adicional. Se a edição TROCAR a categoria, o item vai para o fim da nova
 * categoria (a `ordem` antiga não faz sentido fora da categoria original — ver
 * PartnerRepository::moveToEndOfCategory()).
 */

declare(strict_types=1);

require __DIR__ . '/../../config/bootstrap.php';
require __DIR__ . '/../../includes/AdminAuth.php';
require __DIR__ . '/../../includes/Uploads.php';
require __DIR__ . '/../../repositories/PartnerRepository.php';

admin_require_login();

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Location: ' . BASE_URL . '/admin/partners/', true, 302);
    exit;
}

if (!admin_verify_csrf($_POST['csrf_token'] ?? null)) {
    admin_flash_set('error', 'Sua sessão expirou. Tente novamente.');
    header('Location: ' . BASE_URL . '/admin/partners/', true, 302);
    exit;
}

$id = isset($_POST['id']) && $_POST['id'] !== '' ? (int) $_POST['id'] : null;
$nome = trim((string) ($_POST['nome'] ?? ''));
$categoria = (string) ($_POST['categoria'] ?? '');
$url = trim((string) ($_POST['url'] ?? ''));
$url = $url !== '' ? $url : null;

$backTo = $id ? (BASE_URL . '/admin/partners/form.php?id=' . $id) : (BASE_URL . '/admin/partners/form.php');

if (!in_array($categoria, ['tools', 'companies'], true)) {
    admin_flash_set('error', 'Categoria inválida.');
    header('Location: ' . $backTo, true, 302);
    exit;
}

if ($nome === '' || mb_strlen($nome) > 190) {
    admin_flash_set('error', 'Informe um nome válido (até 190 caracteres).');
    header('Location: ' . $backTo, true, 302);
    exit;
}

if ($url !== null && (mb_strlen($url) > 500 || !filter_var($url, FILTER_VALIDATE_URL))) {
    admin_flash_set('error', 'Informe uma URL válida, ou deixe em branco (como "Auditto").');
    header('Location: ' . $backTo, true, 302);
    exit;
}

$repository = new PartnerRepository();

$hasUpload = isset($_FILES['logo']) && ($_FILES['logo']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
$newLogoPath = null;

if ($hasUpload) {
    try {
        $newLogoPath = ctprice_admin_handle_logo_upload($_FILES['logo'], 'partners');
    } catch (RuntimeException $e) {
        admin_flash_set('error', $e->getMessage());
        header('Location: ' . $backTo, true, 302);
        exit;
    }
} elseif ($id === null) {
    admin_flash_set('error', 'Envie uma logo para o novo parceiro.');
    header('Location: ' . $backTo, true, 302);
    exit;
}

try {
    if ($id === null) {
        $repository->create($nome, $categoria, (string) $newLogoPath, $url, $repository->nextOrder($categoria));
        admin_flash_set('success', 'Parceiro criado com sucesso.');
    } else {
        $existing = $repository->find($id);
        if (!$existing) {
            admin_flash_set('error', 'Parceiro não encontrado.');
            header('Location: ' . BASE_URL . '/admin/partners/', true, 302);
            exit;
        }

        $categoryChanged = $existing['categoria'] !== $categoria;

        $repository->update($id, $nome, $categoria, $newLogoPath, $url);

        if ($categoryChanged) {
            $repository->moveToEndOfCategory($id, $categoria);
        }

        if ($newLogoPath !== null) {
            ctprice_admin_delete_old_logo_if_owned($existing['logo_path']);
        }

        admin_flash_set('success', 'Parceiro atualizado com sucesso.');
    }
} catch (Throwable $e) {
    error_log('CT Price CMS [admin/partners/save]: falha ao salvar — ' . $e->getMessage());
    admin_flash_set('error', 'Não foi possível salvar agora. Tente novamente em instantes.');
    header('Location: ' . $backTo, true, 302);
    exit;
}

header('Location: ' . BASE_URL . '/admin/partners/', true, 302);
exit;
