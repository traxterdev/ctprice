<?php
/**
 * /depoimentos/ — Depoimentos
 *
 * Estrutura, medições e decisões documentadas em docs/reference/depoimentos-audit.md.
 *
 * Reconstrução com melhorias deliberadas sobre o original (ver comentário de
 * components/video-testimonials-section.php para o detalhe de cada uma): uma única grade
 * semântica para os 7 depoimentos (não duas seções Elementor fragmentadas 3+4), empresa exibida
 * como texto real, ícone de "site" correto, `alt` real nas fotos, sombra leve institucional,
 * lightbox de vídeo próprio (sem Elementor/jQuery, iframe do YouTube só criado sob demanda).
 */
require __DIR__ . '/../config/bootstrap.php';

$boxedHero = [
    'eyebrow' => 'depoimentos',
    'title' => 'A confiança de quem já conta com a CT Price',
    'image' => BASE_URL . '/assets/images/pages/informacoes/informacoes.jpg',
    'background_position' => '0% 0%',
];

// Os 7 depoimentos em vídeo — conteúdo estático em config/video-testimonials.php (independente
// dos 4 depoimentos da Home, ver comentário desse arquivo).
$videoTestimonialsSection = [
    'heading' => 'Quem confia, recomenda.',
    'intro_html' => '<p>Há anos a <strong>CT Price</strong> constrói relações de confiança com nossos clientes oferecendo soluções transparentes, atendimento dedicado e resultados que realmente fazem a diferença.</p><p>Acreditamos que o respeito, a ética e a responsabilidade em cada projeto são os pilares do nosso trabalho — e é isso que faz nossos clientes continuarem escolhendo a CT Price e recomendando nossos serviços.</p>',
    'items' => require __DIR__ . '/../config/video-testimonials.php',
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Depoimentos — CT Price</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/reset.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/fonts.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/variables.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/header.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/boxed-hero.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/video-testimonials-section.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/footer.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/whatsapp-button.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/cookie-banner.css">
</head>
<body>

<?php require __DIR__ . '/../includes/topbar.php'; ?>
<?php require __DIR__ . '/../includes/header.php'; ?>

<main>
    <?php require __DIR__ . '/../components/boxed-hero.php'; ?>
    <?php require __DIR__ . '/../components/video-testimonials-section.php'; ?>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
<?php require __DIR__ . '/../includes/cookie-banner.php'; ?>
<?php require __DIR__ . '/../includes/whatsapp-button.php'; ?>

<script src="<?= BASE_URL ?>/assets/js/header.js" defer></script>
<script src="<?= BASE_URL ?>/assets/js/cookie-banner.js" defer></script>
<script src="<?= BASE_URL ?>/assets/js/video-testimonials-lightbox.js" defer></script>
</body>
</html>
