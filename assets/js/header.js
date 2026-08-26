/**
 * assets/js/header.js
 *
 * Comportamento do menu mobile/tablet do header: abrir/fechar pelo hambúrguer e expandir
 * submenus. JavaScript puro, sem jQuery, sem framework, sem dependências.
 *
 * Ativo em max-width:1024px — o mesmo breakpoint real do menu medido em
 * docs/reference/home-tablet-audit.md (o CSS de assets/css/header.css decide o que fica
 * visível em cada largura; este script só controla estado/acessibilidade).
 */
(function () {
    'use strict';

    var toggle = document.getElementById('menu-toggle');
    var nav = document.getElementById('primary-menu');
    var navList = nav ? nav.querySelector('.primary-nav__list') : null;

    if (!toggle || !nav || !navList) {
        return;
    }

    function closeMenu() {
        toggle.setAttribute('aria-expanded', 'false');
        navList.classList.remove('is-open');
    }

    function openMenu() {
        toggle.setAttribute('aria-expanded', 'true');
        navList.classList.add('is-open');
    }

    toggle.addEventListener('click', function () {
        var isOpen = toggle.getAttribute('aria-expanded') === 'true';
        if (isOpen) {
            closeMenu();
        } else {
            openMenu();
        }
    });

    // Suporte a teclado: fecha o menu com Esc e devolve o foco ao hambúrguer.
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
            closeMenu();
            toggle.focus();
        }
    });

    // Submenus ("Clientes e Parceiros", "Trabalhe Conosco"): expandem de forma independente
    // do estado aberto/fechado do menu principal, em qualquer largura de tela.
    var submenuToggles = navList.querySelectorAll('.primary-nav__link--toggle');
    submenuToggles.forEach(function (button) {
        button.addEventListener('click', function () {
            var isExpanded = button.getAttribute('aria-expanded') === 'true';

            // Fecha os demais submenus abertos antes de abrir o clicado.
            submenuToggles.forEach(function (other) {
                if (other !== button) {
                    other.setAttribute('aria-expanded', 'false');
                }
            });

            button.setAttribute('aria-expanded', String(!isExpanded));
        });
    });
})();
