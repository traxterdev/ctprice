<?php
/**
 * /informacoes/ — Informações
 *
 * Estrutura, medições e decisões documentadas em docs/reference/informacoes-audit.md.
 *
 * Achado da auditoria: apesar do nome, a página não é uma central de documentos/downloads — é
 * inteiramente composta de conteúdo já existente em outras páginas do site (Hero próprio +
 * mesmos 3 posts de blog da Home + seção "Dedicação" textualmente idêntica à de /sobre-nos/ +
 * mesmo carrossel de clientes). Por isso esta página não cria nenhum componente novo — apenas
 * compõe components/boxed-hero.php, components/blog-section.php,
 * components/image-content-cta-section.php e components/clients-carousel-section.php, todos já
 * aprovados/implementados em outras páginas.
 *
 * MELHORIA CONSCIENTE (vs. original): o widget "Posts" do Elementor usado nesta página no site
 * original tem apresentação visivelmente mais pobre que o card de post já aprovado na Home
 * (sem selo de categoria, thumbnail menor, tipografia genérica) para exatamente o mesmo
 * conteúdo — ver docs/reference/informacoes-audit.md, seções 6 e 13. Em vez de replicar essa
 * inconsistência do próprio site original, esta página reutiliza components/blog-section.php
 * (mesmo componente da Home), preservando os mesmos 3 posts e destinos.
 */
require __DIR__ . '/../config/bootstrap.php';

$boxedHero = [
    'eyebrow' => 'mantenha-se bem informado',
    'title' => 'Acompanhe nossas novidades',
    'image' => BASE_URL . '/assets/images/pages/informacoes/informacoes.jpg',
    'background_position' => '0% 0%',
];

// Mesmos 3 posts/destinos já usados na Home — fonte compartilhada, não duplicada (ver
// config/blog-posts.php).
$blogData = require __DIR__ . '/../config/blog-posts.php';
$blogHeading = $blogData['heading'];
$blogPosts = $blogData['posts'];

// Texto (heading/parágrafos/CTA) idêntico ao já implementado em /sobre-nos/ — fonte
// compartilhada, não duplicada (ver config/dedication-section.php). Só a imagem é própria desta
// página.
$dedicationSection = require __DIR__ . '/../config/dedication-section.php';
$dedicationSection['image'] = BASE_URL . '/assets/images/pages/informacoes/pexels-thepaintedsquare-583847-1024x683.jpg';
$dedicationSection['image_alt'] = '';

// Carrossel de logos de clientes/parceiros — mesmo carrossel da Home/Sobre Nós. Fonte
// compartilhada em config/clients.php (não duplicar a lista aqui).
$clientLogos = require __DIR__ . '/../config/clients.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Informações — CT Price</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/vendor/swiper/swiper-bundle.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/reset.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/fonts.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/variables.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/header.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/boxed-hero.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/blog-section.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/image-content-cta-section.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/logo-card.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/clients-carousel-section.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/footer.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/whatsapp-button.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/cookie-banner.css">
</head>
<body>

<?php require __DIR__ . '/../includes/topbar.php'; ?>
<?php require __DIR__ . '/../includes/header.php'; ?>

<main>
    <?php require __DIR__ . '/../components/boxed-hero.php'; ?>
    <?php require __DIR__ . '/../components/blog-section.php'; ?>
    <?php $imageContentCtaSection = $dedicationSection; require __DIR__ . '/../components/image-content-cta-section.php'; ?>
    <?php require __DIR__ . '/../components/clients-carousel-section.php'; ?>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
<?php require __DIR__ . '/../includes/cookie-banner.php'; ?>
<?php require __DIR__ . '/../includes/whatsapp-button.php'; ?>

<script src="<?= BASE_URL ?>/assets/vendor/swiper/swiper-bundle.min.js" defer></script>
<script src="<?= BASE_URL ?>/assets/js/header.js" defer></script>
<script src="<?= BASE_URL ?>/assets/js/cookie-banner.js" defer></script>
<script src="<?= BASE_URL ?>/assets/js/clients-carousel-init.js" defer></script>
</body>
</html>
