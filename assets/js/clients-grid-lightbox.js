/**
 * assets/js/clients-grid-lightbox.js
 *
 * Lightbox em JavaScript puro para a grade de clientes (components/clients-grid-section.php) —
 * reproduz a interação já confirmada no original (clique num logo abre visualização ampliada,
 * com navegação entre os itens), sem adicionar biblioteca externa só para isso.
 *
 * Ver docs/reference/clientes-audit.md, seção 9 (interações confirmadas).
 *
 * Seletor escopado a `.clients-grid .logo-card` (não `.logo-card` sozinho) porque essa mesma
 * classe de card também é usada no carrossel da Home/Sobre Nós
 * (components/clients-carousel-section.php) e nas grades de Parceiros/Ferramentas — o carrossel
 * e as grades de link externo não devem ganhar lightbox.
 */
(function () {
    'use strict';

    var items = Array.prototype.slice.call(document.querySelectorAll('.clients-grid .logo-card'));
    var lightbox = document.querySelector('.clients-grid-lightbox');
    if (!items.length || !lightbox) {
        return;
    }

    var img = lightbox.querySelector('.clients-grid-lightbox__img');
    var closeBtn = lightbox.querySelector('.clients-grid-lightbox__close');
    var prevBtn = lightbox.querySelector('.clients-grid-lightbox__prev');
    var nextBtn = lightbox.querySelector('.clients-grid-lightbox__next');
    var currentIndex = -1;

    function show(index) {
        if (index < 0) {
            index = items.length - 1;
        } else if (index >= items.length) {
            index = 0;
        }
        currentIndex = index;
        var item = items[currentIndex];
        img.src = item.getAttribute('data-full');
        img.alt = item.getAttribute('data-alt') || '';
        lightbox.hidden = false;
    }

    function hide() {
        lightbox.hidden = true;
        img.src = '';
        currentIndex = -1;
    }

    items.forEach(function (item, index) {
        item.addEventListener('click', function () {
            show(index);
        });
    });

    closeBtn.addEventListener('click', hide);
    prevBtn.addEventListener('click', function () {
        show(currentIndex - 1);
    });
    nextBtn.addEventListener('click', function () {
        show(currentIndex + 1);
    });

    lightbox.addEventListener('click', function (event) {
        if (event.target === lightbox) {
            hide();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (lightbox.hidden) {
            return;
        }
        if (event.key === 'Escape') {
            hide();
        } else if (event.key === 'ArrowLeft') {
            show(currentIndex - 1);
        } else if (event.key === 'ArrowRight') {
            show(currentIndex + 1);
        }
    });
})();
