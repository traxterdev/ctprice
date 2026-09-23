/**
 * assets/js/admin.js
 *
 * Comportamento mínimo do painel administrativo — JavaScript puro, sem framework/dependência.
 * Três responsabilidades, sem relação com nenhum CRUD:
 *   1) confirma exclusões físicas antes de enviar o formulário (§12/§13 de sprints anteriores:
 *      "se implementar exclusão física, exigir confirmação clara");
 *   2) abre/fecha a sidebar em telas estreitas (reorganização visual — a sidebar vira off-canvas
 *      abaixo do breakpoint definido em assets/css/admin.css; em desktop/notebook ela já é sempre
 *      visível via CSS, este script não faz nada além de alternar uma classe).
 *   3) toolbar de edição assistida do título dos Banners da Home (admin/hero/form.php) —
 *      "Destaque"/"Quebra de linha" inserem/removem exatamente o markup que
 *      ctprice_sanitize_hero_title_html() já permite (`<span class="hero-slide__highlight">`,
 *      `<br>`), para o administrador não precisar escrever HTML à mão. Só ativa em páginas com
 *      `[data-hero-toolbar]` — não afeta nenhum outro formulário do admin. NÃO substitui a
 *      validação/sanitização do servidor, que continua sendo a única fonte de verdade sobre o
 *      que é salvo.
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

    var HERO_HIGHLIGHT_OPEN = '<span class="hero-slide__highlight">';
    var HERO_HIGHLIGHT_CLOSE = '</span>';
    var HERO_LINE_BREAK = '<br>';

    function heroToggleHighlight(textarea) {
        var value = textarea.value;
        var start = textarea.selectionStart;
        var end = textarea.selectionEnd;
        if (start === end) {
            // Nada selecionado — sem um trecho para destacar, não há o que fazer (sem parser
            // complexo, como pedido).
            return;
        }

        var before = value.slice(Math.max(0, start - HERO_HIGHLIGHT_OPEN.length), start);
        var after = value.slice(end, end + HERO_HIGHLIGHT_CLOSE.length);

        if (before === HERO_HIGHLIGHT_OPEN && after === HERO_HIGHLIGHT_CLOSE) {
            // A seleção já é exatamente o conteúdo de um destaque existente — remove o markup
            // (mesmo texto, sem o span ao redor).
            var withoutTags = value.slice(0, start - HERO_HIGHLIGHT_OPEN.length)
                + value.slice(start, end)
                + value.slice(end + HERO_HIGHLIGHT_CLOSE.length);
            textarea.value = withoutTags;
            var newStart = start - HERO_HIGHLIGHT_OPEN.length;
            textarea.setSelectionRange(newStart, newStart + (end - start));
        } else {
            var selected = value.slice(start, end);
            textarea.value = value.slice(0, start) + HERO_HIGHLIGHT_OPEN + selected + HERO_HIGHLIGHT_CLOSE + value.slice(end);
            var highlightedStart = start + HERO_HIGHLIGHT_OPEN.length;
            textarea.setSelectionRange(highlightedStart, highlightedStart + selected.length);
        }

        textarea.focus();
    }

    function heroInsertLineBreak(textarea) {
        var value = textarea.value;
        var start = textarea.selectionStart;
        var end = textarea.selectionEnd;

        // Com seleção, substitui pelo <br> (nunca perde o restante do texto); sem seleção,
        // insere na posição do cursor.
        textarea.value = value.slice(0, start) + HERO_LINE_BREAK + value.slice(end);
        var cursorPos = start + HERO_LINE_BREAK.length;
        textarea.setSelectionRange(cursorPos, cursorPos);
        textarea.focus();
    }

    document.querySelectorAll('[data-hero-toolbar]').forEach(function (toolbar) {
        var textarea = document.getElementById(toolbar.getAttribute('data-hero-toolbar') || '');
        if (!textarea) {
            return;
        }

        toolbar.querySelectorAll('[data-hero-action]').forEach(function (button) {
            button.addEventListener('click', function () {
                var action = button.getAttribute('data-hero-action');
                if (action === 'highlight') {
                    heroToggleHighlight(textarea);
                } else if (action === 'linebreak') {
                    heroInsertLineBreak(textarea);
                }
            });
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
