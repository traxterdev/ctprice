/**
 * assets/js/hero-init.js
 *
 * Inicializa o Swiper do Hero da Home com a configuração medida no site original
 * (docs/reference/home-desktop-audit.md, seção 12 — data-settings do widget "Slides"):
 *
 *   navigation: none    -> sem setas
 *   pagination: none    -> sem bolinhas (não medido no data-settings, confirmado ausente no DOM)
 *   autoplay_speed: 4000
 *   infinite (loop): yes
 *   transition: slide
 *   transition_speed: 500
 *
 * Não reimplementa nada do Swiper — só configura a instância. Biblioteca em
 * assets/vendor/swiper/ (Swiper 8.4.5, MIT).
 */
(function () {
    'use strict';

    if (typeof Swiper === 'undefined') {
        return;
    }

    var el = document.querySelector('.hero-slider');
    if (!el) {
        return;
    }

    new Swiper(el, {
        loop: true,
        speed: 500,
        autoplay: {
            delay: 4000,
            disableOnInteraction: false,
        },
        navigation: false,
        pagination: false,
        allowTouchMove: true,
    });
})();
