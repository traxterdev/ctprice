<?php
/**
 * index.php — Home
 *
 * Nesta etapa: topbar, header e o Hero foram implementados visualmente (ver
 * docs/reference/home-desktop-audit.md, docs/reference/home-tablet-audit.md e
 * docs/reference/home-mobile-audit.md). Demais seções, footer, cookie banner e botão de
 * WhatsApp permanecem como placeholder — ver docs/architecture-proposal.md.
 *
 * O Hero é renderizado por components/hero-slider.php — esta página só define os dados dos
 * slides (conteúdo real e confirmado do site original) e inclui o componente.
 */
require __DIR__ . '/config/bootstrap.php';

$heroSlides = [
    [
        'image' => BASE_URL . '/assets/images/hero/caroussel01.jpg',
        'html' => '<span class="hero-slide__highlight">Cuide da sua empresa,</span> <br>e deixe a contabilidade nas <br>mãos de quem entende',
    ],
    [
        'image' => BASE_URL . '/assets/images/hero/csinicial02.jpg',
        'html' => 'Trabalhamos <span class="hero-slide__highlight">integrados </span>aos<br> colaboradores de sua empresa, <br>para que juntos possamos obter <br><span class="hero-slide__highlight">os melhores resultados</span>',
    ],
    [
        'image' => BASE_URL . '/assets/images/hero/caroussel02.jpg',
        'html' => 'Atuamos nos ramos de<br> contabilidade e planejamento<br> tributário em formato digital <br><span class="hero-slide__highlight">sem papel e sem burocracia</span>.',
    ],
    [
        'image' => BASE_URL . '/assets/images/hero/caroussel03a.jpg',
        'html' => 'Fornecemos informações <br> <span class="hero-slide__highlight">precisas e seguras</span> para <br>que você possa tomar <br>as <span class="hero-slide__highlight">melhores decisões </span><br>para seu negócio',
    ],
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CT Price</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/vendor/swiper/swiper-bundle.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/reset.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/variables.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/header.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/hero.css">
</head>
<body>

<?php require __DIR__ . '/includes/topbar.php'; ?>
<?php require __DIR__ . '/includes/header.php'; ?>

<main>
    <?php require __DIR__ . '/components/hero-slider.php'; ?>

    <!-- TODO: demais seções da Home (ver docs/reference/home-desktop-audit.md) -->
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
<?php require __DIR__ . '/includes/cookie-banner.php'; ?>
<?php require __DIR__ . '/includes/whatsapp-button.php'; ?>

<script src="<?= BASE_URL ?>/assets/vendor/swiper/swiper-bundle.min.js" defer></script>
<script src="<?= BASE_URL ?>/assets/js/header.js" defer></script>
<script src="<?= BASE_URL ?>/assets/js/hero-init.js" defer></script>
</body>
</html>
