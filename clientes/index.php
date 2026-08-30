<?php
/**
 * /clientes/ — Clientes
 *
 * Estrutura mínima do scaffold. Layout, conteúdo e estilos do site original ainda não
 * foram implementados nesta etapa.
 *
 * Ver docs/architecture-proposal.md e docs/reference/site-inventory.md.
 */
require __DIR__ . '/../config/bootstrap.php';

$boxedHero = [
    'eyebrow' => 'nossos clientes',
    'title' => 'Conheça algumas empresas que confiam na CT Price',
    'image' => BASE_URL . '/assets/images/pages/clientes/clientes.jpg',
];

// Grade de clientes — DIFERENÇA TEMPORÁRIA CONHECIDA (não é regressão): o original em WordPress
// usa 106 logos (galeria justificada, ver docs/reference/clientes-audit.md). O CMS que
// permitiria gerenciar esse catálogo completo foi adiado para uma fase futura de manutenção de
// conteúdo; nesta fase, a página reaproveita os mesmos 82 logos já centralizados em
// config/clients.php (mesma fonte do carrossel da Home/Sobre Nós) — os 72 logos exclusivos da
// página original não foram baixados nem reproduzidos.
$clientLogos = require __DIR__ . '/../config/clients.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Clientes — CT Price</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/reset.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/fonts.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/variables.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/header.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/boxed-hero.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/logo-card.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/clients-grid-section.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/footer.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/whatsapp-button.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/cookie-banner.css">
</head>
<body>

<?php require __DIR__ . '/../includes/topbar.php'; ?>
<?php require __DIR__ . '/../includes/header.php'; ?>

<main>
    <?php require __DIR__ . '/../components/boxed-hero.php'; ?>
    <?php require __DIR__ . '/../components/clients-grid-section.php'; ?>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
<?php require __DIR__ . '/../includes/cookie-banner.php'; ?>
<?php require __DIR__ . '/../includes/whatsapp-button.php'; ?>

<script src="<?= BASE_URL ?>/assets/js/header.js" defer></script>
<script src="<?= BASE_URL ?>/assets/js/cookie-banner.js" defer></script>
<script src="<?= BASE_URL ?>/assets/js/clients-grid-lightbox.js" defer></script>
</body>
</html>
