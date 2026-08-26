/**
 * assets/js/scroll-reveal.js
 *
 * Ativa a classe "is-visible" em qualquer elemento marcado com [data-animate-item] quando ele
 * entra na viewport, usando IntersectionObserver nativo — sem biblioteca de animação. O próprio
 * CSS do componente (ex.: assets/css/welcome-section.css) decide o que a classe "is-visible"
 * faz visualmente.
 *
 * Reutilizável por qualquer seção futura que precise do mesmo tipo de animação de entrada por
 * scroll — não é específico da seção "Bem-vindo".
 */
(function () {
    'use strict';

    var items = document.querySelectorAll('[data-animate-item]');
    if (!items.length) {
        return;
    }

    if (!('IntersectionObserver' in window)) {
        items.forEach(function (el) {
            el.classList.add('is-visible');
        });
        return;
    }

    var observer = new IntersectionObserver(
        function (entries, obs) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    obs.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.2 }
    );

    items.forEach(function (el) {
        observer.observe(el);
    });
})();
