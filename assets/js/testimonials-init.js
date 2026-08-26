/**
 * assets/js/testimonials-init.js
 *
 * Inicializa o Swiper da seção "O que dizem nossos clientes" com a configuração medida
 * diretamente no `data-settings` do widget original ("Testimonial Carousel"), NÃO reaproveitada
 * do Hero (docs de referência da Home, seção 13 — "Depoimentos"):
 *
 *   show_arrows: yes      -> setas prev/next
 *   pagination: bullets   -> paginação por bolinhas
 *   speed: 500
 *   autoplay: yes, autoplay_speed: 5000
 *   loop: yes
 *   pause_on_hover: yes
 *   pause_on_interaction: yes
 *   space_between: 10px (idêntico em desktop/tablet/mobile no data-settings original — sem
 *                         breakpoint responsivo próprio para este valor)
 *
 * Biblioteca em assets/vendor/swiper/ (Swiper 8.4.5, MIT) — mesma já usada pelo Hero, nenhuma
 * nova dependência.
 */
(function () {
    'use strict';

    if (typeof Swiper === 'undefined') {
        return;
    }

    var el = document.querySelector('.testimonials-swiper');
    if (!el) {
        return;
    }

    var wrap = el.closest('.testimonials-swiper-wrap');
    var section = el.closest('.testimonials-section');

    new Swiper(el, {
        loop: true,
        speed: 500,
        spaceBetween: 10,
        autoplay: {
            delay: 5000,
            disableOnInteraction: true,
            pauseOnMouseEnter: true,
        },
        allowTouchMove: true,
        navigation: {
            prevEl: wrap.querySelector('.testimonials-swiper__nav--prev'),
            nextEl: wrap.querySelector('.testimonials-swiper__nav--next'),
        },
        pagination: {
            el: section.querySelector('.testimonials-swiper__pagination'),
            clickable: true,
        },
    });
})();
