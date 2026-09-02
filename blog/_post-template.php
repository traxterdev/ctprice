<?php
/**
 * blog/_post-template.php
 *
 * Layout compartilhado dos 3 posts do blog — equivalente ao único template Elementor que o site
 * original já usa para os 3 posts (`elementor-page-1049`, confirmado em
 * docs/reference/site-inventory.md e reconfirmado em docs/reference/blog-posts-audit.md, seção 3:
 * classificação A, estrutura 100% comum aos 3 posts).
 *
 * NÃO é uma página pública própria — cada post vive num diretório na RAIZ do site (mesmo padrão
 * de todas as outras páginas do projeto: `/slug/index.php`), que só define `$postSlug` e inclui
 * este arquivo. A localização deste template dentro de `blog/` é só organizacional (não faz parte
 * da URL pública) — reconcilia duas menções conflitantes em docs/architecture-proposal.md: a
 * árvore de diretórios da seção 9 sugeria `blog/{slug}/index.php` (o que resultaria na URL
 * `/blog/slug/`), mas a seção 10 ("Estratégia de URLs") e a tabela de redirecionamentos 301 são
 * explícitas — a URL final é a mesma slug NA RAIZ, sem `/wp/` e sem `/blog/` — e é isso que todas
 * as outras 10 páginas já implementadas neste projeto seguem. Esta implementação segue a seção 10
 * (mais específica, e consistente com o padrão físico já adotado em todo o resto do site).
 *
 * Espera, definida pelo chamador (`/{slug}/index.php`) ANTES de incluir este arquivo:
 *
 *   $postSlug = 'slug real auditado, ex.: "hello-world"';
 *
 * (E, como toda página, `require .../config/bootstrap.php` já deve ter sido feito antes, para que
 * $company/$menu/BASE_URL já existam.)
 */

if (!isset($postSlug)) {
    throw new RuntimeException('blog/_post-template.php requer $postSlug definido pelo chamador.');
}

$ctpriceBlogData = require __DIR__ . '/../config/blog-posts.php';
$ctpricePost = null;
foreach ($ctpriceBlogData['posts'] as $ctpriceCandidate) {
    if ($ctpriceCandidate['slug'] === $postSlug) {
        $ctpricePost = $ctpriceCandidate;
        break;
    }
}
unset($ctpriceCandidate);

if ($ctpricePost === null) {
    throw new RuntimeException('Post não encontrado em config/blog-posts.php para o slug: ' . $postSlug);
}

$ctpriceBodyHtml = require __DIR__ . '/../content/blog/' . $postSlug . '.php';
$ctpriceAbsoluteUrl = ctprice_absolute_url($ctpricePost['url']);

$articleHeader = [
    'title' => $ctpricePost['title'],
];

$articleContentSection = [
    'date_text' => $ctpricePost['date'],
    'time_text' => $ctpricePost['time'],
    'body_html' => $ctpriceBodyHtml,
    'share' => [
        'url' => $ctpriceAbsoluteUrl,
        'title' => $ctpricePost['title'],
    ],
    'related' => [
        'current_slug' => $postSlug,
        'items' => $ctpriceBlogData['posts'],
    ],
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($ctpricePost['title'], ENT_QUOTES, 'UTF-8') ?> — CT Price</title>
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
