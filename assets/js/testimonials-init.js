/**
 * assets/js/testimonials-init.js
 *
 * Inicializa o Swiper da seção "O que dizem nossos clientes".
 *
 * AJUSTE (2026-09-17, pedido explícito do cliente): a seção deixou de mostrar 1 depoimento de
 * texto por vez e passou a mostrar os cards lado a lado — 3 no desktop (>=1024px), 2 no tablet
 * (>=768px), 1 no mobile (<768px, valor padrão do Swiper) — mesmos breakpoints de conteúdo já
 * usados no restante do site (header.css/clients-carousel-init.js). `speed`, `autoplay`,
 * `loop`, `pause_on_hover`/`pause_on_interaction` e a paginação/setas permanecem os mesmos já
 * medidos no widget original ("Testimonial Carousel") — só o número de slides visíveis e o
 * espaçamento entre eles (`spaceBetween`, ajustado de 10px para 24px para caber a respiração do
 * card mais completo) mudam.
 *
 * Biblioteca em assets/vendor/swiper/ (Swiper 8.4.5, MIT) — mesma já usada pelo Hero e por
 * Depoimentos, nenhuma nova dependência.
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
        spaceBetween: 24,
        slidesPerView: 1,
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
        breakpoints: {
            768: {
                slidesPerView: 2,
            },
            1024: {
                slidesPerView: 3,
            },
        },
    });
})();
