<?php
/**
 * /trabalhe-conosco/ — Trabalhe conosco
 *
 * Estrutura mínima do scaffold. Layout, conteúdo e estilos do site original ainda não
 * foram implementados nesta etapa.
 *
 * Nota: esta página contém a âncora #beneficios referenciada pelo submenu "Trabalhe Conosco"
 * (ver config/menu.php e docs/reference/site-inventory.md).
 *
 * Ver docs/architecture-proposal.md e docs/reference/site-inventory.md.
 */
require __DIR__ . '/../config/bootstrap.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trabalhe conosco — CT Price</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/reset.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/fonts.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/variables.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/header.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/footer.css">
</head>
<body>

<?php require __DIR__ . '/../includes/topbar.php'; ?>
<?php require __DIR__ . '/../includes/header.php'; ?>

<main>
    <!-- TODO: conteúdo da página "Trabalhe Conosco", incluindo a âncora id="beneficios"
         (ver docs/reference/site-inventory.md) -->
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
<?php require __DIR__ . '/../includes/cookie-banner.php'; ?>
<?php require __DIR__ . '/../includes/whatsapp-button.php'; ?>

<script src="<?= BASE_URL ?>/assets/js/header.js" defer></script>
</body>
</html>
