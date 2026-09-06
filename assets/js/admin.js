/**
 * assets/js/admin.js
 *
 * Comportamento mínimo do painel administrativo — JavaScript puro, sem framework/dependência.
 * Duas responsabilidades, sem relação com nenhum CRUD:
 *   1) confirma exclusões físicas antes de enviar o formulário (§12/§13 de sprints anteriores:
 *      "se implementar exclusão física, exigir confirmação clara");
 *   2) abre/fecha a sidebar em telas estreitas (reorganização visual — a sidebar vira off-canvas
 *      abaixo do breakpoint definido em assets/css/admin.css; em desktop/notebook ela já é sempre
 *      visível via CSS, este script não faz nada além de alternar uma classe).
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

    var toggle = document.getElementById('admin-sidebar-toggle');
    var sidebar = document.getElementById('admin-sidebar');
    var overlay = document.getElementById('admin-sidebar-overlay');

    if (!toggle || !sidebar || !overlay) {
        return;
    }

    function closeSidebar() {
        sidebar.classList.remove('is-open');
        overlay.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
    }

    function openSidebar() {
        sidebar.classList.add('is-open');
        overlay.classList.add('is-open');
        toggle.setAttribute('aria-expanded', 'true');
    }

    toggle.addEventListener('click', function () {
        var isOpen = toggle.getAttribute('aria-expanded') === 'true';
        if (isOpen) {
            closeSidebar();
        } else {
            openSidebar();
        }
    });

    overlay.addEventListener('click', closeSidebar);

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
            closeSidebar();
            toggle.focus();
        }
    });
})();
