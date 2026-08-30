/**
 * assets/js/clients-carousel-init.js
 *
 * Inicializa o Swiper do carrossel de logos de clientes/parceiros com a configuração medida
 * diretamente no `data-settings` do widget original ("Image Carousel"), NÃO reaproveitada do
 * Hero nem de Depoimentos (docs de referência da Home — "Carrossel de logos de clientes"):
 *
 *   autoplay: yes, autoplay_speed: 5000
 *   loop (infinite): yes
 *   speed: 500
 *   navigation: none (sem setas no original)
 *   pagination: nenhuma (não há bolinhas no widget "Image Carousel")
 *   image_spacing_custom: 20px (idêntico em qualquer largura, confirmado)
 *
 * Número de slides visíveis por breakpoint (`slidesPerView`) medido diretamente nos três
 * viewports obrigatórios (ver assets/css/clients-carousel-section.css para a explicação do
 * porquê de usar 768/1024 em vez do ponto de corte não-redondo do widget original):
 *   <768px:      1 slide
 *   768–1023px:  2 slides
 *   >=1024px:    10 slides no original
 *
 * AJUSTE DE UI (etapa de refinamento visual — não fidelidade ao original): com a UI de cards
 * premium (fundo, borda, sombra), 10 logos simultâneos em desktop comprimiam demais cada marca
 * dentro do card. Ajustado visualmente via Chrome DevTools para 6 (melhor equilíbrio entre
 * presença visual de cada logo e densidade do conjunto — testado contra 5 e 7, ver relatório da
 * tarefa). `spaceBetween`, autoplay, loop, swipe e a ordem de `config/clients.php` permanecem
 * inalterados; apenas o NÚMERO de slides visíveis em desktop muda.
 *
 * Biblioteca em assets/vendor/swiper/ (Swiper 8.4.5, MIT) — mesma já usada pelo Hero e por
 * Depoimentos, nenhuma nova dependência.
 */
(function () {
    'use strict';

    if (typeof Swiper === 'undefined') {
        return;
    }

    var el = document.querySelector('.clients-carousel');
    if (!el) {
        return;
    }

    new Swiper(el, {
        loop: true,
        speed: 500,
        spaceBetween: 20,
        slidesPerView: 1,
        autoplay: {
            delay: 5000,
            disableOnInteraction: true,
            pauseOnMouseEnter: true,
        },
        allowTouchMove: true,
        navigation: false,
        pagination: false,
        breakpoints: {
            768: {
                slidesPerView: 2,
            },
            1024: {
                slidesPerView: 6,
            },
        },
    });
})();
