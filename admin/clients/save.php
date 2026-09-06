<?php
/**
 * admin/clients/save.php
 *
 * Processa o POST de admin/clients/form.php — cria ou atualiza (conforme a presença de `id`).
 * Nunca chamado diretamente por navegação (só recebe POST); qualquer outro método é rejeitado.
 */

declare(strict_types=1);

require __DIR__ . '/../../config/bootstrap.php';
require __DIR__ . '/../../includes/AdminAuth.php';
require __DIR__ . '/../../includes/Uploads.php';
require __DIR__ . '/../../repositories/ClientRepository.php';

admin_require_login();

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Location: ' . BASE_URL . '/admin/clients/', true, 302);
    exit;
}

if (!admin_verify_csrf($_POST['csrf_token'] ?? null)) {
    admin_flash_set('error', 'Sua sessão expirou. Tente novamente.');
    header('Location: ' . BASE_URL . '/admin/clients/', true, 302);
    exit;
}

$id = isset($_POST['id']) && $_POST['id'] !== '' ? (int) $_POST['id'] : null;
$nome = trim((string) ($_POST['nome'] ?? ''));
$siteUrl = trim((string) ($_POST['site_url'] ?? ''));
$siteUrl = $siteUrl !== '' ? $siteUrl : null;

$backTo = $id ? (BASE_URL . '/admin/clients/form.php?id=' . $id) : (BASE_URL . '/admin/clients/form.php');

if ($nome === '' || mb_strlen($nome) > 190) {
    admin_flash_set('error', 'Informe um nome válido (até 190 caracteres).');
    header('Location: ' . $backTo, true, 302);
    exit;
}

if ($siteUrl !== null && (mb_strlen($siteUrl) > 500 || !filter_var($siteUrl, FILTER_VALIDATE_URL))) {
    admin_flash_set('error', 'Informe uma URL válida para o site do cliente, ou deixe em branco.');
    header('Location: ' . $backTo, true, 302);
    exit;
}

$repository = new ClientRepository();

$hasUpload = isset($_FILES['logo']) && ($_FILES['logo']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
$newLogoPath = null;

if ($hasUpload) {
    try {
        $newLogoPath = ctprice_admin_handle_logo_upload($_FILES['logo'], 'clients');
    } catch (RuntimeException $e) {
        admin_flash_set('error', $e->getMessage());
        header('Location: ' . $backTo, true, 302);
        exit;
    }
} elseif ($id === null) {
    admin_flash_set('error', 'Envie uma logo para o novo cliente.');
    header('Location: ' . $backTo, true, 302);
    exit;
}

try {
    if ($id === null) {
        $repository->create($nome, (string) $newLogoPath, $siteUrl, $repository->nextOrder());
        admin_flash_set('success', 'Cliente criado com sucesso.');
    } else {
        $existing = $repository->find($id);
        if (!$existing) {
            admin_flash_set('error', 'Cliente não encontrado.');
            header('Location: ' . BASE_URL . '/admin/clients/', true, 302);
            exit;
        }

        $repository->update($id, $nome, $newLogoPath, $siteUrl);

        if ($newLogoPath !== null) {
            ctprice_admin_delete_old_logo_if_owned($existing['logo_path']);
        }

        admin_flash_set('success', 'Cliente atualizado com sucesso.');
    }
} catch (Throwable $e) {
    error_log('CT Price CMS [admin/clients/save]: falha ao salvar — ' . $e->getMessage());
    admin_flash_set('error', 'Não foi possível salvar agora. Tente novamente em instantes.');
    header('Location: ' . $backTo, true, 302);
    exit;
}

header('Location: ' . BASE_URL . '/admin/clients/', true, 302);
exit;
