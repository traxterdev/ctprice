<?php
/**
 * index.php — Home
 *
 * Nesta etapa: apenas topbar + header foram implementados visualmente (ver
 * docs/reference/home-desktop-audit.md, docs/reference/home-tablet-audit.md e
 * docs/reference/home-mobile-audit.md). Conteúdo, hero, footer e demais seções da Home
 * permanecem como placeholder — ver docs/architecture-proposal.md.
 */
require __DIR__ . '/config/bootstrap.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CT Price</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/reset.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/variables.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/header.css">
</head>
<body>

<?php require __DIR__ . '/includes/topbar.php'; ?>
<?php require __DIR__ . '/includes/header.php'; ?>

<main>
    <!-- TODO: conteúdo da Home (ver docs/reference/home-desktop-audit.md) -->
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
<?php require __DIR__ . '/includes/cookie-banner.php'; ?>
<?php require __DIR__ . '/includes/whatsapp-button.php'; ?>

<script src="<?= BASE_URL ?>/assets/js/header.js" defer></script>
</body>
</html>
