/**
 * assets/js/language-switcher.js
 *
 * Liga o seletor visual JÁ EXISTENTE no topbar (bandeiras Brasil/Estados Unidos/Espanha —
 * includes/topbar.php) à API pública do GTranslate Website Translator Widget (widget gratuito
 * "dropdown.js", carregado escondido por includes/translate-widget.php —
 * `.gtranslate_wrapper { display:none }`). O visitante nunca vê o dropdown nativo do serviço —
 * só o seletor da CT Price.
 *
 * CORREÇÃO (2026-09-17): a versão anterior deste arquivo reimplementava manualmente o mecanismo
 * de troca de idioma (procurando um `<select class="goog-te-combo">` e disparando eventos
 * `change`), replicando uma versão antiga/desatualizada do widget. A build atual do GTranslate
 * (dropdown.js) já expõe `window.doGTranslate(lang_pair)` pronta — é essa API pública oficial
 * que este arquivo chama agora, sem depender da estrutura interna do DOM deles (que já mudou
 * pelo menos uma vez e pode mudar de novo sem aviso).
 *
 * PERSISTÊNCIA (item 8 do pedido — "usar mecanismo compatível com o próprio widget"): a build
 * atual do GTranslate guarda o idioma escolhido em `localStorage['__GT_TRANSLATE_LANGS']`
 * (`{"srcLang":"pt","tgtLang":"en"}`) e o PRÓPRIO script já relê essa chave sozinho a cada
 * carregamento de página, reaplicando a tradução automaticamente — confirmado navegando entre
 * páginas sem nenhuma chamada manual daqui. Este arquivo só LÊ essa mesma chave (nunca duplica o
 * estado em outro lugar) para saber qual bandeira marcar como ativa.
 *
 * RETORNO AO PORTUGUÊS (item 9): clicar na bandeira do Brasil chama doGTranslate('pt|pt') —
 * padrão documentado do GTranslate para restaurar o idioma original (source === target desfaz a
 * camada de tradução). Nenhum reload de domínio, nenhuma rota /pt/.
 *
 * FALHA GRACIOSA (itens 14/17): se o widget do GTranslate estiver desligado
 * (config/translate.php['enabled'] = false — ver includes/translate-widget.php, que nesse caso
 * nem imprime o script) ou o CDN estiver indisponível, `window.doGTranslate` nunca aparece — o
 * clique é ignorado em silêncio após um tempo limite, sem nenhum erro no console, e o site
 * continua 100% utilizável em português.
 */
(function () {
    'use strict';

    var LANG_ATTR = 'data-lang';
    var STORAGE_KEY = '__GT_TRANSLATE_LANGS';
    var DEFAULT_LANG = 'pt';
    var RETRY_DELAY_MS = 300;
    var MAX_ATTEMPTS = 15; // ~4.5s de tentativas antes de desistir silenciosamente

    function callDoGTranslate(langPair, attempt) {
        attempt = attempt || 0;
        if (typeof window.doGTranslate === 'function') {
            window.doGTranslate(langPair);
            return;
        }
        // Script do widget ainda não terminou de carregar (ou não está configurado/disponível —
        // ver includes/translate-widget.php). Tenta de novo por um tempo limitado; depois
        // desiste em silêncio (falha graciosa, sem erro de console).
        if (attempt < MAX_ATTEMPTS) {
            setTimeout(function () { callDoGTranslate(langPair, attempt + 1); }, RETRY_DELAY_MS);
        }
    }

    function currentLangFromStorage() {
        try {
            var raw = window.localStorage.getItem(STORAGE_KEY);
            if (!raw) {
                return DEFAULT_LANG;
            }
            var parsed = JSON.parse(raw);
            return parsed && parsed.tgtLang ? parsed.tgtLang : DEFAULT_LANG;
        } catch (e) {
            // localStorage indisponível (modo privado restrito, etc.) — trata como português,
            // nunca quebra a leitura do estado visual das bandeiras.
            return DEFAULT_LANG;
        }
    }

    function setActiveButton(buttons, lang) {
        buttons.forEach(function (btn) {
            var isActive = btn.getAttribute(LANG_ATTR) === lang;
            btn.classList.toggle('is-active', isActive);
            btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });
    }

    function init() {
        var buttons = Array.prototype.slice.call(document.querySelectorAll('[' + LANG_ATTR + ']'));
        if (buttons.length === 0) {
            return;
        }

        setActiveButton(buttons, currentLangFromStorage());

        buttons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var lang = btn.getAttribute(LANG_ATTR);
                if (!lang) {
                    return;
                }
                callDoGTranslate(DEFAULT_LANG + '|' + lang);
                setActiveButton(buttons, lang);
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
