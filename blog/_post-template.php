<?php
/**
 * blog/_post-template.php
 *
 * Layout compartilhado dos posts do blog — banco (`blog_posts`) é a fonte canônica desde a sprint
 * CMS 02 (antes: config/blog-posts.php + content/blog/{slug}.php). Ver docs/cms.md.
 *
 * NÃO é uma página pública própria — chamada por dois caminhos:
 *   1. Os 3 diretórios físicos históricos na raiz (ex.: /hello-world/index.php), que definem
 *      `$postSlug` e incluem este arquivo diretamente — preservados por compatibilidade, mesmo
 *      slug/URL de sempre.
 *   2. `blog-post.php` (raiz), o roteador de posts NOVOS cadastrados só no banco (sem diretório
 *      físico) — ver `.htaccess`, bloco "Roteamento de posts do blog".
 *
 * Post não encontrado OU não publicado (`ativo=0` ou `published_at` no futuro): delega para o
 * MESMO handler 404 de sempre (`404.php`) — nunca lança exceção (diferente da versão anterior a
 * esta sprint, quando $postSlug só podia vir de um diretório físico já confirmado válido; agora
 * também chega aqui via slug arbitrário digitado na URL, então "não encontrado" é um caso
 * ESPERADO, não uma falha de programação).
 *
 * Espera, definida pelo chamador ANTES de incluir este arquivo:
 *
 *   $postSlug = 'slug a buscar no banco';
 *
 * (E, como toda página, `require .../config/bootstrap.php` já deve ter sido feito antes, para que
 * $company/$menu/BASE_URL/Database já existam.)
 */

if (!isset($postSlug)) {
    throw new RuntimeException('blog/_post-template.php requer $postSlug definido pelo chamador.');
}

require_once __DIR__ . '/../repositories/BlogPostRepository.php';

$ctpricePost = null;
try {
    $ctpricePost = (new BlogPostRepository())->findPublishedBySlug($postSlug);
} catch (Throwable $e) {
    error_log('CT Price [blog/_post-template]: falha ao consultar post — ' . $e->getMessage());
}

if ($ctpricePost === null) {
    http_response_code(404);
    require __DIR__ . '/../404.php';
    exit;
}

$ctpriceRelatedItems = [];
try {
    $ctpriceRelatedItems = (new BlogPostRepository())->allPublished();
} catch (Throwable $e) {
    error_log('CT Price [blog/_post-template]: falha ao consultar relacionados — ' . $e->getMessage());
}

$ctpriceAbsoluteUrl = ctprice_absolute_url('/' . $ctpricePost['slug'] . '/');
$ctpriceDateText = BlogPostRepository::dateText($ctpricePost['published_at']);
$ctpriceTimeText = BlogPostRepository::timeText($ctpricePost['published_at']);

$articleHeader = [
    'title' => $ctpricePost['titulo'],
];

$articleContentSection = [
    'date_text' => $ctpriceDateText,
    'time_text' => $ctpriceTimeText,
    'body_html' => $ctpricePost['body_html'],
    'share' => [
        'url' => $ctpriceAbsoluteUrl,
        'title' => $ctpricePost['titulo'],
    ],
    'related' => [
        'current_slug' => $postSlug,
        'items' => $ctpriceRelatedItems,
    ],
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($ctpricePost['titulo'], ENT_QUOTES, 'UTF-8') ?> — CT Price</title>
    <meta name="description" content="<?= htmlspecialchars($ctpricePost['excerpt'], ENT_QUOTES, 'UTF-8') ?>">
    <link rel="canonical" href="<?= htmlspecialchars($ctpriceAbsoluteUrl, ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/reset.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/fonts.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/variables.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/header.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/article.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/footer.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/whatsapp-button.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/cookie-banner.css">
</head>
<body>

<?php require __DIR__ . '/../includes/topbar.php'; ?>
<?php require __DIR__ . '/../includes/header.php'; ?>

<main>
    <?php require __DIR__ . '/../components/article-header.php'; ?>
    <?php require __DIR__ . '/../components/article-content-section.php'; ?>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
<?php require __DIR__ . '/../includes/cookie-banner.php'; ?>
<?php require __DIR__ . '/../includes/whatsapp-button.php'; ?>

<script src="<?= BASE_URL ?>/assets/js/header.js" defer></script>
<script src="<?= BASE_URL ?>/assets/js/cookie-banner.js" defer></script>
</body>
</html>
