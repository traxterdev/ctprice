<?php
/**
 * admin/testimonials/save.php — mesmo padrão de admin/clients/save.php.
 *
 * Upload ÚNICO ("foto") alimenta os dois campos (foto_path/thumbnail_path) — mesma convenção real
 * dos 7 depoimentos migrados, todos com o mesmo arquivo nos dois campos (ver
 * database/seed_editorial_content.php).
 */

declare(strict_types=1);

require __DIR__ . '/../../config/bootstrap.php';
require __DIR__ . '/../../includes/AdminAuth.php';
require __DIR__ . '/../../includes/Uploads.php';
require __DIR__ . '/../../repositories/TestimonialRepository.php';

admin_require_login();

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Location: ' . BASE_URL . '/admin/testimonials/', true, 302);
    exit;
}

if (!admin_verify_csrf($_POST['csrf_token'] ?? null)) {
    admin_flash_set('error', 'Sua sessão expirou. Tente novamente.');
    header('Location: ' . BASE_URL . '/admin/testimonials/', true, 302);
    exit;
}

$id = isset($_POST['id']) && $_POST['id'] !== '' ? (int) $_POST['id'] : null;
$backTo = $id ? (BASE_URL . '/admin/testimonials/form.php?id=' . $id) : (BASE_URL . '/admin/testimonials/form.php');

$nome = trim((string) ($_POST['nome'] ?? ''));
$empresa = trim((string) ($_POST['empresa'] ?? ''));
$depoimento = trim((string) ($_POST['depoimento'] ?? ''));
$videoId = trim((string) ($_POST['video_id'] ?? ''));
$videoList = trim((string) ($_POST['video_list'] ?? ''));
$siteUrl = trim((string) ($_POST['site_url'] ?? ''));
$instagramUrl = trim((string) ($_POST['instagram_url'] ?? ''));

$errors = [];
if ($nome === '' || mb_strlen($nome) > 190) {
    $errors[] = 'Informe um nome válido.';
}
if ($empresa === '' || mb_strlen($empresa) > 190) {
    $errors[] = 'Informe uma empresa válida.';
}
if ($depoimento === '') {
    $errors[] = 'Informe o texto do depoimento.';
}
if (!preg_match('/^[A-Za-z0-9_-]{5,32}$/', $videoId)) {
    $errors[] = 'ID do vídeo do YouTube inválido.';
}
if ($videoList !== '' && !preg_match('/^[A-Za-z0-9_-]{5,64}$/', $videoList)) {
    $errors[] = 'ID da playlist do YouTube inválido.';
}
if ($siteUrl !== '' && (mb_strlen($siteUrl) > 500 || !filter_var($siteUrl, FILTER_VALIDATE_URL))) {
    $errors[] = 'Informe uma URL válida para o site, ou deixe em branco.';
}
if ($instagramUrl !== '' && (mb_strlen($instagramUrl) > 500 || !filter_var($instagramUrl, FILTER_VALIDATE_URL))) {
    $errors[] = 'Informe uma URL válida para o Instagram, ou deixe em branco.';
}

if ($errors) {
    admin_flash_set('error', implode(' ', $errors));
    header('Location: ' . $backTo, true, 302);
    exit;
}

$repository = new TestimonialRepository();
$hasUpload = isset($_FILES['foto']) && ($_FILES['foto']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
$newFotoPath = null;

if ($hasUpload) {
    try {
        $newFotoPath = ctprice_admin_handle_logo_upload($_FILES['foto'], 'testimonials');
    } catch (RuntimeException $e) {
        admin_flash_set('error', $e->getMessage());
        header('Location: ' . $backTo, true, 302);
        exit;
    }
} elseif ($id === null) {
    admin_flash_set('error', 'Envie uma foto para o novo depoimento.');
    header('Location: ' . $backTo, true, 302);
    exit;
}

$data = [
    'nome' => $nome,
    'empresa' => $empresa,
    'depoimento' => $depoimento,
    'video_id' => $videoId,
    'video_list' => $videoList !== '' ? $videoList : null,
    'site_url' => $siteUrl !== '' ? $siteUrl : null,
    'instagram_url' => $instagramUrl !== '' ? $instagramUrl : null,
];

try {
    if ($id === null) {
        $repository->create($data, (string) $newFotoPath, (string) $newFotoPath, $repository->nextOrder());
        admin_flash_set('success', 'Depoimento criado com sucesso.');
    } else {
        $existing = $repository->find($id);
        if (!$existing) {
            admin_flash_set('error', 'Depoimento não encontrado.');
            header('Location: ' . BASE_URL . '/admin/testimonials/', true, 302);
            exit;
        }
        $repository->update($id, $data, $newFotoPath, $newFotoPath);
        if ($newFotoPath !== null) {
            ctprice_admin_delete_old_logo_if_owned($existing['foto_path']);
            if ($existing['thumbnail_path'] !== $existing['foto_path']) {
                ctprice_admin_delete_old_logo_if_owned($existing['thumbnail_path']);
            }
        }
        admin_flash_set('success', 'Depoimento atualizado com sucesso.');
    }
} catch (Throwable $e) {
    error_log('CT Price CMS [admin/testimonials/save]: ' . $e->getMessage());
    admin_flash_set('error', 'Não foi possível salvar agora. Tente novamente em instantes.');
    header('Location: ' . $backTo, true, 302);
    exit;
}

header('Location: ' . BASE_URL . '/admin/testimonials/', true, 302);
exit;
