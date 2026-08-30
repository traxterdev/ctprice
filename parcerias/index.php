<?php
/**
 * /parcerias/ — Parceiros
 *
 * Nota: slug real do WordPress é "parcerias" (o menu exibe o rótulo "Parceiros") —
 * ver docs/reference/site-inventory.md.
 *
 * Estrutura, medições e decisões de UI documentadas em docs/reference/parcerias-audit.md.
 * CMS adiado (decisão de escopo do projeto) — dados estáticos em config/partners.php.
 */
require __DIR__ . '/../config/bootstrap.php';

$boxedHero = [
    'eyebrow' => 'parcerias de sucesso',
    'title' => 'São ainda maiores, quando compartilhado com quem caminha ao nosso lado.',
    'image' => BASE_URL . '/assets/images/pages/informacoes/informacoes.jpg',
    'background_position' => '0% 0%',
];

$toolsTitleBand = [
    'title' => 'Ferramentas WEB<br>para os Clientes CT Price',
    'font_size' => 40,
];

$partnersTitleBand = [
    'title' => 'Parceiros',
    'font_size' => 50,
];

$partnersData = require __DIR__ . '/../config/partners.php';

$toolsGridSection = [
    'items' => $partnersData['tools'],
    'image_dir' => 'assets/images/partners/tools',
    'columns_desktop' => 3,
    'columns_tablet' => 2,
    'columns_mobile' => 1,
];

$companiesGridSection = [
    'items' => $partnersData['companies'],
    'image_dir' => 'assets/images/partners/companies',
    'columns_desktop' => 5,
    'columns_tablet' => 3,
    'columns_mobile' => 2,
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Parcerias — CT Price</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/reset.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/fonts.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/variables.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/header.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/boxed-hero.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/section-title-band.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/logo-card.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/logo-grid-section.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/footer.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/whatsapp-button.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/cookie-banner.css">
</head>
<body>

<?php require __DIR__ . '/../includes/topbar.php'; ?>
<?php require __DIR__ . '/../includes/header.php'; ?>

<main>
    <?php require __DIR__ . '/../components/boxed-hero.php'; ?>
    <?php $sectionTitleBand = $toolsTitleBand; require __DIR__ . '/../components/section-title-band.php'; ?>
    <?php $logoGridSection = $toolsGridSection; require __DIR__ . '/../components/logo-grid-section.php'; ?>
    <?php $sectionTitleBand = $partnersTitleBand; require __DIR__ . '/../components/section-title-band.php'; ?>
    <?php $logoGridSection = $companiesGridSection; require __DIR__ . '/../components/logo-grid-section.php'; ?>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
<?php require __DIR__ . '/../includes/cookie-banner.php'; ?>
<?php require __DIR__ . '/../includes/whatsapp-button.php'; ?>

<script src="<?= BASE_URL ?>/assets/js/header.js" defer></script>
<script src="<?= BASE_URL ?>/assets/js/cookie-banner.js" defer></script>
</body>
</html>
