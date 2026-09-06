<?php
/**
 * admin/includes/layout-header.php
 *
 * `<head>` + estrutura (sidebar + área principal) de toda página PRIVADA do admin. NÃO usado por
 * admin/login.php (tela pública de login tem seu próprio `<head>` minimalista, sem navegação —
 * ver admin/login.php).
 *
 * REORGANIZAÇÃO VISUAL: sidebar vertical fixa (era barra horizontal) — mesma filosofia do
 * dashboard do sistema de RH da CT Price/Traxter (sidebar escura institucional + conteúdo amplo à
 * direita), adaptada a este CMS. Puramente visual/estrutural — nenhuma rota, nenhum dado, nenhuma
 * regra de negócio mudou; cada página continua só definindo `$adminPageTitle`/`$adminActiveMenu`/
 * `$adminCurrentUser` e emitindo seu conteúdo entre este include e layout-footer.php, exatamente
 * como antes.
 *
 * Sidebar centralizada AQUI (não duplicada em cada página) — os itens de navegação (rótulo, ícone,
 * URL, chave de `$adminActiveMenu`) vêm de um único array `$adminNavItems`, no mesmo espírito de
 * `config/menu.php` (fonte única de navegação, nunca links soltos por página). Vagas/Benefícios
 * não aparecem — já removidos do CMS por decisão de arquitetura (ver docs/cms.md).
 *
 * Ícones: SVG inline simples (traço, sem preenchimento) — nenhuma biblioteca externa instalada,
 * mesmo padrão já usado em outros ícones do projeto (ex.: components/video-testimonials-section.php).
 *
 * Espera, definidas pelo chamador ANTES do include:
 *   $adminPageTitle    — string, título da aba/página (ex.: "Clientes").
 *   $adminActiveMenu   — 'dashboard'|'clients'|'partners'|'testimonials'|'posts'|'users', para
 *                         destacar o item ativo da sidebar.
 *   $adminCurrentUser  — array retornado por admin_require_login() (nome/email/id).
 */

$adminNavItems = [
    [
        'key' => 'dashboard',
        'label' => 'Dashboard',
        'url' => BASE_URL . '/admin/',
        'icon' => '<rect x="3" y="3" width="7.5" height="7.5" rx="1.5"/><rect x="13.5" y="3" width="7.5" height="7.5" rx="1.5"/><rect x="13.5" y="13.5" width="7.5" height="7.5" rx="1.5"/><rect x="3" y="13.5" width="7.5" height="7.5" rx="1.5"/>',
    ],
    [
        'key' => 'clients',
        'label' => 'Clientes',
        'url' => BASE_URL . '/admin/clients/',
        'icon' => '<rect x="2" y="7" width="20" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>',
    ],
    [
        'key' => 'partners',
        'label' => 'Parceiros',
        'url' => BASE_URL . '/admin/partners/',
        'icon' => '<circle cx="8.5" cy="12" r="5.5"/><circle cx="15.5" cy="12" r="5.5"/>',
    ],
    [
        'key' => 'testimonials',
        'label' => 'Depoimentos',
        'url' => BASE_URL . '/admin/testimonials/',
        'icon' => '<path d="M21 11.5a8.4 8.4 0 0 1-4.5 7.4 8.5 8.5 0 0 1-8.9-.5L3 20l1.6-4.7a8.4 8.4 0 0 1-1-4 8.5 8.5 0 0 1 8.5-8.5h.3a8.5 8.5 0 0 1 8.5 8.4v.3z"/>',
    ],
    [
        'key' => 'posts',
        'label' => 'Notícias',
        'url' => BASE_URL . '/admin/posts/',
        'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="8" y1="13" x2="16" y2="13"/><line x1="8" y1="17" x2="14" y2="17"/>',
    ],
    [
        'key' => 'users',
        'label' => 'Administradores',
        'url' => BASE_URL . '/admin/users/',
        'icon' => '<path d="M16.5 20.5v-1.8a3.6 3.6 0 0 0-3.6-3.6H5.6A3.6 3.6 0 0 0 2 18.7v1.8"/><circle cx="9" cy="7.5" r="3.6"/><path d="M21.5 20.5v-1.8a3.6 3.6 0 0 0-2.7-3.5"/><path d="M15 3.7a3.6 3.6 0 0 1 0 7"/>',
    ],
];
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

<div class="admin-mobile-bar">
    <button type="button" class="admin-mobile-bar__toggle" id="admin-sidebar-toggle" aria-expanded="false" aria-controls="admin-sidebar">
        <span class="sr-only">Abrir menu</span>
        <svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>
    <span class="admin-mobile-bar__title"><?= htmlspecialchars($adminPageTitle ?? 'Admin', ENT_QUOTES, 'UTF-8') ?></span>
</div>

<div class="admin-shell">
    <div class="admin-sidebar-overlay" id="admin-sidebar-overlay"></div>

    <aside class="admin-sidebar" id="admin-sidebar">
        <div class="admin-sidebar__brand">
            <span class="admin-sidebar__logo-chip">
                <img src="<?= BASE_URL ?>/assets/images/logo/LogoPreferencialColorida-768x223.png" alt="CT Price" width="768" height="223">
            </span>
            <span class="admin-sidebar__brand-label">Administração</span>
        </div>

        <nav class="admin-sidebar__nav" aria-label="Navegação administrativa">
            <?php foreach ($adminNavItems as $item): ?>
            <a href="<?= htmlspecialchars($item['url'], ENT_QUOTES, 'UTF-8') ?>" class="admin-sidebar__link<?= ($adminActiveMenu ?? '') === $item['key'] ? ' is-active' : '' ?>">
                <svg class="admin-sidebar__icon" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><?= $item['icon'] ?></svg>
                <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
            </a>
            <?php endforeach; ?>
        </nav>

        <div class="admin-sidebar__footer">
            <p class="admin-sidebar__user"><?= htmlspecialchars($adminCurrentUser['nome'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
            <a href="<?= BASE_URL ?>/admin/logout.php" class="admin-sidebar__link">
                <svg class="admin-sidebar__icon" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                Sair
            </a>
            <p class="admin-sidebar__tag">CT Price — CMS</p>
        </div>
    </aside>

    <main class="admin-main">
        <?php $flash = admin_flash_get(); ?>
        <?php if ($flash): ?>
        <div class="admin-alert admin-alert--<?= $flash['type'] === 'success' ? 'success' : 'error' ?>">
            <?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?>
        </div>
        <?php endif; ?>
