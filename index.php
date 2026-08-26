<?php
/**
 * index.php — Home
 *
 * Nesta etapa: topbar, header e o Hero foram implementados visualmente (ver
 * docs/reference/home-desktop-audit.md, docs/reference/home-tablet-audit.md e
 * docs/reference/home-mobile-audit.md). Demais seções, footer, cookie banner e botão de
 * WhatsApp permanecem como placeholder — ver docs/architecture-proposal.md.
 *
 * O Hero é renderizado por components/hero-slider.php e a seção "Bem-vindo à CT Price" por
 * components/welcome-section.php — esta página só define os dados de cada um (conteúdo real e
 * confirmado do site original) e inclui os componentes.
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

$welcomeHeading = 'Bem-vindo à CT Price';
$welcomeLead = 'Obtenha consultoria e suporte especializado da CT price, uma empresa de consultoria contábil que está sempre ao seu lado.';
$welcomeItems = [
    [
        'icon' => 'chart-bar',
        'title' => 'Consultoria de negócios',
        'text' => 'Mais do que simples contabilidade, nossa consultoria de negócios oferece estratégias personalizadas e soluções inteligentes para impulsionar o crescimento e o sucesso da sua empresa.',
    ],
    [
        'icon' => 'bell',
        'title' => 'Gerenciamento de riscos',
        'text' => 'Com uma abordagem estratégica de gerenciamento de riscos, ajudamos a sua empresa a identificar, reduzir e prevenir riscos, garantindo uma gestão financeira e tributária, segura e sustentável.',
    ],
    [
        'icon' => 'lightbulb',
        'title' => 'Pesquisa de<br> mercado',
        'text' => 'Combinando uma contabilidade segura e de análises de mercado detalhadas, ajudamos a sua empresa a tomar decisões embasadas em informações estratégicas valiosas, garantindo a maximização e o sucesso do seu negócio.',
    ],
    [
        'icon' => 'headset',
        'title' => 'Serviços de Qualidade',
        'text' => 'Nosso compromisso é oferecer uma prestação de serviço de excelência, agilidade e comprometimento, garantindo que sua empresa tenha um suporte contábil seguro, confiável e eficiente.',
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
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/fonts.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/variables.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/header.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/hero.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/welcome-section.css">
</head>
<body>

<?php require __DIR__ . '/includes/topbar.php'; ?>
<?php require __DIR__ . '/includes/header.php'; ?>

<main>
    <?php require __DIR__ . '/components/hero-slider.php'; ?>
    <?php require __DIR__ . '/components/welcome-section.php'; ?>

    <!-- TODO: demais seções da Home (ver docs/reference/home-desktop-audit.md) -->
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
<?php require __DIR__ . '/includes/cookie-banner.php'; ?>
<?php require __DIR__ . '/includes/whatsapp-button.php'; ?>

<script src="<?= BASE_URL ?>/assets/vendor/swiper/swiper-bundle.min.js" defer></script>
<script src="<?= BASE_URL ?>/assets/js/header.js" defer></script>
<script src="<?= BASE_URL ?>/assets/js/hero-init.js" defer></script>
<script src="<?= BASE_URL ?>/assets/js/scroll-reveal.js" defer></script>
</body>
</html>
