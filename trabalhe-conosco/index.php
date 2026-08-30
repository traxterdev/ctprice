<?php
/**
 * /trabalhe-conosco/ — Trabalhe Conosco
 *
 * Estrutura, medições e decisões documentadas em docs/reference/trabalhe-conosco-audit.md.
 *
 * Reconstrução com melhorias deliberadas de arquitetura/UX sobre o original (ver comentário de
 * components/jobs-section.php e components/benefits-grid-section.php para o detalhe de cada
 * uma): sem os 4 containers vazios usados só como espaçador (resolvido com CSS normal), sem o
 * popup Elementor de candidatura (candidatura direciona para o sistema oficial de recrutamento
 * já existente), sem os benefícios soltos/desalinhados (cards com a mesma identidade visual já
 * aprovada em assets/css/logo-card.css).
 */
require __DIR__ . '/../config/bootstrap.php';

$boxedHero = [
    'eyebrow' => 'trabalhe conosco',
    'title' => 'Veja as vagas Disponíveis',
    'image' => BASE_URL . '/assets/images/pages/informacoes/informacoes.jpg',
    'background_position' => '0% 0%',
];

// Vagas — conteúdo estático em config/jobs.php. O destino de candidatura vem exclusivamente de
// config/company.php (nunca duplicado em config/jobs.php nem hardcoded aqui) — ver
// components/jobs-section.php.
$jobsSection = [
    'jobs' => require __DIR__ . '/../config/jobs.php',
    'apply_url' => $company['sistemas_externos']['recrutamento'] ?? '',
];

// Benefícios — conteúdo estático em config/benefits.php. `id` aqui vira a âncora real
// (id="beneficios") na faixa de título, mesmo destino já usado por config/menu.php
// ('/trabalhe-conosco/#beneficios').
$benefitsTitleBand = [
    'title' => 'Nossos Benefícios',
    'font_size' => 32,
    'font_weight' => 700,
    'height' => 'auto',
    'container_max_width' => 1140,
    'gradient_stops' => ['15%', '90%'],
    'id' => 'beneficios',
];

$benefitsGridSection = [
    'items' => require __DIR__ . '/../config/benefits.php',
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trabalhe Conosco — CT Price</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/reset.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/fonts.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/variables.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/header.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/boxed-hero.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/jobs-section.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/section-title-band.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/logo-card.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/benefits-grid-section.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/footer.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/whatsapp-button.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/cookie-banner.css">
</head>
<body>

<?php require __DIR__ . '/../includes/topbar.php'; ?>
<?php require __DIR__ . '/../includes/header.php'; ?>

<main>
    <?php require __DIR__ . '/../components/boxed-hero.php'; ?>
    <?php require __DIR__ . '/../components/jobs-section.php'; ?>
    <?php $sectionTitleBand = $benefitsTitleBand; require __DIR__ . '/../components/section-title-band.php'; ?>
    <?php require __DIR__ . '/../components/benefits-grid-section.php'; ?>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
<?php require __DIR__ . '/../includes/cookie-banner.php'; ?>
<?php require __DIR__ . '/../includes/whatsapp-button.php'; ?>

<script src="<?= BASE_URL ?>/assets/js/header.js" defer></script>
<script src="<?= BASE_URL ?>/assets/js/cookie-banner.js" defer></script>
</body>
</html>
