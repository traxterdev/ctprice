/**
 * assets/js/admin.js
 *
 * Comportamento mínimo do painel administrativo — JavaScript puro, sem framework. Hoje só
 * confirma exclusões físicas antes de enviar o formulário (ver §12/§13 da tarefa: "se implementar
 * exclusão física, exigir confirmação clara").
 */
(function () {
    'use strict';

    document.querySelectorAll('[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            var message = form.getAttribute('data-confirm') || 'Confirma esta ação?';
            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    });
})();
