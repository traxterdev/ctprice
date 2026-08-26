<?php
/**
 * includes/header.php
 *
 * Logo, navegação principal (com submenus) e botão "Área Restrita".
 *
 * Todos os itens de navegação vêm de $menu (config/menu.php via config/bootstrap.php) —
 * nenhum link é escrito manualmente aqui.
 *
 * Fidelidade medida em docs/reference/home-desktop-audit.md (seção 2.2) e o breakpoint real
 * do menu (max-width:1024px, não 767px) em docs/reference/home-tablet-audit.md. O empilhamento
 * do header em telas estreitas (max-width:767px) é um comportamento próprio deste componente,
 * também medido, e não uma reutilização artificial do breakpoint de conteúdo.
 *
 * Estrutura: um único <nav> compartilhado entre desktop (linha horizontal) e
 * tablet/mobile (painel em coluna, acionado pelo hambúrguer) — a mesma marcação, alternada só
 * por CSS/estado, sem duplicar o menu duas vezes no DOM.
 */

if (!function_exists('ct_slug')) {
    function ct_slug(string $texto): string
    {
        $mapa = ['á'=>'a','à'=>'a','ã'=>'a','â'=>'a','é'=>'e','ê'=>'e','í'=>'i','ó'=>'o','ô'=>'o','õ'=>'o','ú'=>'u','ç'=>'c'];
        $texto = strtolower(strtr($texto, $mapa));
        $texto = preg_replace('/[^a-z0-9]+/', '-', $texto);
        return trim($texto, '-');
    }
}
?>
<header class="site-header">
    <div class="site-header__bar">
        <div class="site-header__logo">
            <a href="<?= BASE_URL ?>/" aria-label="CT Price — página inicial">
                <img
                    src="<?= BASE_URL ?>/assets/images/logo/LogoPreferencialColorida-1024x297.png"
                    srcset="<?= BASE_URL ?>/assets/images/logo/LogoPreferencialColorida-768x223.png 768w, <?= BASE_URL ?>/assets/images/logo/LogoPreferencialColorida-1024x297.png 1024w"
                    sizes="(max-width: 800px) 100vw, 800px"
                    width="1024" height="297"
                    alt="<?= htmlspecialchars($company['razao_social'] ?? 'CT Price', ENT_QUOTES, 'UTF-8') ?>"
                >
            </a>
        </div>

        <div class="site-header__nav">
            <button
                type="button"
                class="menu-toggle"
                id="menu-toggle"
                aria-expanded="false"
                aria-controls="primary-menu"
            >
                <span class="sr-only">Alternar menu</span>
                <svg class="menu-toggle__icon menu-toggle__icon--bars" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M3 6h18M3 12h18M3 18h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <svg class="menu-toggle__icon menu-toggle__icon--close" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M5 5l14 14M19 5L5 19" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>

            <nav class="primary-nav" id="primary-menu" aria-label="Navegação principal">
                <ul class="primary-nav__list">
                    <?php foreach ($menu['primary'] as $item): ?>
                        <?php $temFilhos = !empty($item['children']); ?>
                        <li class="primary-nav__item<?= $temFilhos ? ' has-submenu' : '' ?>">
                            <?php if ($temFilhos): ?>
                                <?php $submenuId = 'submenu-' . ct_slug($item['label']); ?>
                                <button
                                    type="button"
                                    class="primary-nav__link primary-nav__link--toggle"
                                    aria-expanded="false"
                                    aria-controls="<?= $submenuId ?>"
                                >
                                    <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
                                    <svg class="primary-nav__caret" viewBox="0 0 12 8" aria-hidden="true">
                                        <path d="M1 1l5 5 5-5" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                                <ul class="primary-nav__submenu" id="<?= $submenuId ?>">
                                    <?php foreach ($item['children'] as $filho): ?>
                                        <li>
                                            <a href="<?= htmlspecialchars($filho['url'] ?? '#', ENT_QUOTES, 'UTF-8') ?>">
                                                <?= htmlspecialchars($filho['label'], ENT_QUOTES, 'UTF-8') ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <a class="primary-nav__link" href="<?= htmlspecialchars($item['url'] ?? '#', ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
                                </a>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        </div>

        <?php if (!empty($menu['area_restrita'])): ?>
        <div class="site-header__cta">
            <a class="btn btn--pill-outline" href="<?= htmlspecialchars($menu['area_restrita']['url'], ENT_QUOTES, 'UTF-8') ?>">
                <?= htmlspecialchars($menu['area_restrita']['label'], ENT_QUOTES, 'UTF-8') ?>
            </a>
        </div>
        <?php endif; ?>
    </div>
</header>
