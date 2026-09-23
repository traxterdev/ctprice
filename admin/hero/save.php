<?php
/**
 * admin/hero/save.php
 *
 * Processa o POST de admin/hero/form.php — mesmo padrão de admin/partners/save.php.
 *
 * `titulo`: sempre passa por ctprice_sanitize_hero_title_html() antes de chegar ao banco (ver
 * includes/HtmlSanitizer.php) — nunca HTML bruto do formulário chega ao banco.
 *
 * `botao_url`: aceita URL absoluta http/https OU caminho interno começando com "/" (ex.:
 * "/clientes/") — mesma ideia de validação de PartnerRepository, estendida para permitir link
 * interno (não existia precedente de botão no Hero atual, então não há comportamento "atual" a
 * preservar aqui, só a regra de segurança: nunca javascript:/data:/outro esquema).
 */

declare(strict_types=1);

require __DIR__ . '/../../config/bootstrap.php';
require __DIR__ . '/../../includes/AdminAuth.php';
require __DIR__ . '/../../includes/HtmlSanitizer.php';
require __DIR__ . '/../../includes/Uploads.php';
require __DIR__ . '/../../repositories/HeroSlideRepository.php';

admin_require_login();

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Location: ' . BASE_URL . '/admin/hero/', true, 302);
    exit;
}

if (!admin_verify_csrf($_POST['csrf_token'] ?? null)) {
    admin_flash_set('error', 'Sua sessão expirou. Tente novamente.');
    header('Location: ' . BASE_URL . '/admin/hero/', true, 302);
    exit;
}

$id = isset($_POST['id']) && $_POST['id'] !== '' ? (int) $_POST['id'] : null;
$backTo = $id ? (BASE_URL . '/admin/hero/form.php?id=' . $id) : (BASE_URL . '/admin/hero/form.php');

$tituloRaw = trim((string) ($_POST['titulo'] ?? ''));
$titulo = ctprice_sanitize_hero_title_html($tituloRaw);
if ($titulo === '') {
    admin_flash_set('error', 'Informe um título válido.');
    header('Location: ' . $backTo, true, 302);
    exit;
}

$texto = trim((string) ($_POST['texto'] ?? ''));
$texto = $texto !== '' ? $texto : null;
if ($texto !== null && mb_strlen($texto) > 500) {
    admin_flash_set('error', 'O texto (subtítulo) pode ter no máximo 500 caracteres.');
    header('Location: ' . $backTo, true, 302);
    exit;
}

$botaoTexto = trim((string) ($_POST['botao_texto'] ?? ''));
$botaoTexto = $botaoTexto !== '' ? $botaoTexto : null;

$botaoUrl = trim((string) ($_POST['botao_url'] ?? ''));
$botaoUrl = $botaoUrl !== '' ? $botaoUrl : null;

if ($botaoTexto === null) {
    // Sem texto do botão, nenhum botão é renderizado — a URL avulsa não faz sentido sozinha.
    $botaoUrl = null;
} else {
    if (mb_strlen($botaoTexto) > 190) {
        admin_flash_set('error', 'O texto do botão pode ter no máximo 190 caracteres.');
        header('Location: ' . $backTo, true, 302);
        exit;
    }

    $isValidInternalPath = $botaoUrl !== null && str_starts_with($botaoUrl, '/') && !str_starts_with($botaoUrl, '//');
    $isValidExternalUrl = $botaoUrl !== null
        && filter_var($botaoUrl, FILTER_VALIDATE_URL) !== false
        && in_array(strtolower((string) parse_url($botaoUrl, PHP_URL_SCHEME)), ['http', 'https'], true);

    if ($botaoUrl === null || mb_strlen($botaoUrl) > 500 || !($isValidInternalPath || $isValidExternalUrl)) {
        admin_flash_set('error', 'Informe uma URL válida para o botão (link interno começando com "/" ou externo http(s)://).');
        header('Location: ' . $backTo, true, 302);
        exit;
    }
}

$repository = new HeroSlideRepository();

$hasUpload = isset($_FILES['imagem']) && ($_FILES['imagem']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
$newImagePath = null;

if ($hasUpload) {
    try {
        $newImagePath = ctprice_admin_handle_logo_upload($_FILES['imagem'], 'hero');
    } catch (RuntimeException $e) {
        admin_flash_set('error', $e->getMessage());
        header('Location: ' . $backTo, true, 302);
        exit;
    }
} elseif ($id === null) {
    admin_flash_set('error', 'Envie uma imagem para o novo banner.');
    header('Location: ' . $backTo, true, 302);
    exit;
}

try {
    if ($id === null) {
        $repository->create($titulo, $texto, (string) $newImagePath, $botaoTexto, $botaoUrl, $repository->nextOrder());
        admin_flash_set('success', 'Banner criado com sucesso.');
    } else {
        $existing = $repository->find($id);
        if (!$existing) {
            admin_flash_set('error', 'Banner não encontrado.');
            header('Location: ' . BASE_URL . '/admin/hero/', true, 302);
            exit;
        }

        $repository->update($id, $titulo, $texto, $newImagePath, $botaoTexto, $botaoUrl);

        if ($newImagePath !== null) {
            ctprice_admin_delete_old_logo_if_owned($existing['imagem_path']);
        }

        admin_flash_set('success', 'Banner atualizado com sucesso.');
    }
} catch (Throwable $e) {
    error_log('CT Price CMS [admin/hero/save]: falha ao salvar — ' . $e->getMessage());
    admin_flash_set('error', 'Não foi possível salvar agora. Tente novamente em instantes.');
    header('Location: ' . $backTo, true, 302);
    exit;
}

header('Location: ' . BASE_URL . '/admin/hero/', true, 302);
exit;
