<?php
/**
 * admin/includes/layout-header.php
 *
 * `<head>` + barra de navegação superior de toda página PRIVADA do admin. NÃO usado por
 * admin/login.php (tela pública de login tem seu próprio `<head>` minimalista, sem navegação —
 * ver admin/login.php).
 *
 * Espera, definidas pelo chamador ANTES do include:
 *   $adminPageTitle    — string, título da aba/página (ex.: "Clientes").
 *   $adminActiveMenu   — 'dashboard'|'clients'|'partners', para destacar o item ativo do menu.
 *   $adminCurrentUser  — array retornado por admin_require_login() (nome/email/id).
 */
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title><?= htmlspecialchars($adminPageTitle ?? 'Admin', ENT_QUOTES, 'UTF-8') ?> — CT Price Admin</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/reset.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/fonts.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/variables.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css">
</head>
<body class="admin-body">
<header class="admin-topbar">
    <div class="admin-topbar__inner">
        <a class="admin-topbar__brand" href="<?= BASE_URL ?>/admin/">CT Price <span>Admin</span></a>
        <nav class="admin-nav" aria-label="Navegação administrativa">
            <span class="admin-nav__user"><?= htmlspecialchars($adminCurrentUser['nome'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
            <a href="<?= BASE_URL ?>/admin/" class="<?= ($adminActiveMenu ?? '') === 'dashboard' ? 'is-active' : '' ?>">Dashboard</a>
            <a href="<?= BASE_URL ?>/admin/clients/" class="<?= ($adminActiveMenu ?? '') === 'clients' ? 'is-active' : '' ?>">Clientes</a>
            <a href="<?= BASE_URL ?>/admin/partners/" class="<?= ($adminActiveMenu ?? '') === 'partners' ? 'is-active' : '' ?>">Parceiros</a>
            <a href="<?= BASE_URL ?>/admin/logout.php">Sair</a>
        </nav>
    </div>
</header>
<main class="admin-main">
    <?php $flash = admin_flash_get(); ?>
    <?php if ($flash): ?>
    <div class="admin-alert admin-alert--<?= $flash['type'] === 'success' ? 'success' : 'error' ?>">
        <?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php endif; ?>
