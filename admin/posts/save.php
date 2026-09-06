<?php
/**
 * admin/posts/save.php
 *
 * Regras de slug (ver tarefa da sprint, §7/§8):
 *   - normalizado por BlogPostRepository::slugify() (minúsculas, só [a-z0-9-], sem "/", sem "..");
 *   - único (checado antes de salvar, nunca depende só da UNIQUE KEY do banco);
 *   - nunca um dos nomes reservados abaixo — evitaria criar um post cuja URL nunca seria
 *     alcançada (a pasta física do diretório reservado sempre venceria no `.htaccess`, ver
 *     comentário do bloco de roteamento);
 *   - IMEDIATO na criação, IMUTÁVEL na edição (o campo é `readonly` no formulário — mesmo que
 *     alguém forje o POST, o slug enviado na edição é ignorado, nunca sobrescreve o slug salvo).
 *
 * `body_html` sempre passa por `ctprice_sanitize_article_html()` antes de chegar ao banco — nunca
 * o HTML bruto enviado pelo formulário.
 */

declare(strict_types=1);

require __DIR__ . '/../../config/bootstrap.php';
require __DIR__ . '/../../includes/AdminAuth.php';
require __DIR__ . '/../../includes/Uploads.php';
require __DIR__ . '/../../includes/HtmlSanitizer.php';
require __DIR__ . '/../../repositories/BlogPostRepository.php';

admin_require_login();

// Nomes de diretório físico já existentes na raiz do site — um post com um destes slugs nunca
// seria alcançável (o `.htaccess` só roteia para blog/show.php quando NENHUM arquivo/diretório
// físico responde pela URL — ver comentário no próprio .htaccess).
const CTPRICE_RESERVED_SLUGS = [
    'sobre-nos', 'clientes', 'parcerias', 'fale-conosco', 'informacoes', 'trabalhe-conosco',
    'ouvidoria', 'depoimentos', 'arearestrita', 'admin', 'assets', 'blog', 'config',
    'components', 'includes', 'content', 'database', 'repositories',
];

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Location: ' . BASE_URL . '/admin/posts/', true, 302);
    exit;
}

if (!admin_verify_csrf($_POST['csrf_token'] ?? null)) {
    admin_flash_set('error', 'Sua sessão expirou. Tente novamente.');
    header('Location: ' . BASE_URL . '/admin/posts/', true, 302);
    exit;
}

$id = isset($_POST['id']) && $_POST['id'] !== '' ? (int) $_POST['id'] : null;
$backTo = $id ? (BASE_URL . '/admin/posts/form.php?id=' . $id) : (BASE_URL . '/admin/posts/form.php');

$titulo = trim((string) ($_POST['titulo'] ?? ''));
$categoria = trim((string) ($_POST['categoria'] ?? ''));
$excerpt = trim((string) ($_POST['excerpt'] ?? ''));
$publishedAtRaw = trim((string) ($_POST['published_at'] ?? ''));
$bodyHtmlRaw = (string) ($_POST['body_html'] ?? '');

$repository = new BlogPostRepository();

$errors = [];
if ($titulo === '' || mb_strlen($titulo) > 255) {
    $errors[] = 'Informe um título válido.';
}
if ($categoria === '' || mb_strlen($categoria) > 60) {
    $errors[] = 'Informe uma categoria válida.';
}
if ($excerpt === '' || mb_strlen($excerpt) > 500) {
    $errors[] = 'Informe um resumo válido (até 500 caracteres).';
}
if (trim($bodyHtmlRaw) === '') {
    $errors[] = 'Informe o conteúdo do post.';
}

$publishedAtDt = DateTimeImmutable::createFromFormat('Y-m-d\TH:i', $publishedAtRaw)
    ?: DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $publishedAtRaw);
if (!$publishedAtDt) {
    $errors[] = 'Informe uma data de publicação válida.';
}

// --- Slug: só resolvido/validado na CRIAÇÃO — na edição, o slug existente é sempre preservado ---
if ($id === null) {
    $slugInput = trim((string) ($_POST['slug'] ?? ''));
    $slug = BlogPostRepository::slugify($slugInput !== '' ? $slugInput : $titulo);

    if ($slug === '') {
        $errors[] = 'Não foi possível gerar um slug válido a partir do título — informe um slug manualmente.';
    } elseif (in_array($slug, CTPRICE_RESERVED_SLUGS, true)) {
        $errors[] = 'Este slug é reservado para uma página do site — escolha outro.';
    } elseif (!$repository->isSlugAvailable($slug)) {
        $errors[] = 'Já existe um post com este slug — escolha outro.';
    }
} else {
    $existingForSlug = $repository->find($id);
    $slug = $existingForSlug['slug'] ?? '';
}

if ($errors) {
    admin_flash_set('error', implode(' ', $errors));
    header('Location: ' . $backTo, true, 302);
    exit;
}

$hasUpload = isset($_FILES['imagem']) && ($_FILES['imagem']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
$newImagePath = null;

if ($hasUpload) {
    try {
        $newImagePath = ctprice_admin_handle_logo_upload($_FILES['imagem'], 'posts');
    } catch (RuntimeException $e) {
        admin_flash_set('error', $e->getMessage());
        header('Location: ' . $backTo, true, 302);
        exit;
    }
} elseif ($id === null) {
    admin_flash_set('error', 'Envie uma imagem para o novo post.');
    header('Location: ' . $backTo, true, 302);
    exit;
}

$bodyHtml = ctprice_sanitize_article_html($bodyHtmlRaw);
$publishedAt = $publishedAtDt->format('Y-m-d H:i:s');

try {
    if ($id === null) {
        $repository->create($titulo, $slug, $categoria, $excerpt, (string) $newImagePath, $bodyHtml, $publishedAt);
        admin_flash_set('success', 'Post criado com sucesso.');
    } else {
        $existing = $repository->find($id);
        if (!$existing) {
            admin_flash_set('error', 'Post não encontrado.');
            header('Location: ' . BASE_URL . '/admin/posts/', true, 302);
            exit;
        }
        $repository->update($id, $titulo, $slug, $categoria, $excerpt, $newImagePath, $bodyHtml, $publishedAt);
        if ($newImagePath !== null) {
            ctprice_admin_delete_old_logo_if_owned($existing['imagem_path']);
        }
        admin_flash_set('success', 'Post atualizado com sucesso.');
    }
} catch (Throwable $e) {
    error_log('CT Price CMS [admin/posts/save]: ' . $e->getMessage());
    admin_flash_set('error', 'Não foi possível salvar agora. Tente novamente em instantes.');
    header('Location: ' . $backTo, true, 302);
    exit;
}

header('Location: ' . BASE_URL . '/admin/posts/', true, 302);
exit;
