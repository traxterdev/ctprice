/**
 * assets/js/language-switcher.js
 *
 * Liga o seletor visual JÁ EXISTENTE no topbar (bandeiras Brasil/Estados Unidos/Espanha —
 * includes/topbar.php) ao motor de tradução do GTranslate (includes/translate-widget.php),
 * carregado escondido (`.gtranslate_wrapper { display:none }`). O visitante nunca vê o seletor
 * nativo do serviço — só o da CT Price.
 *
 * `doGTranslate`/`GTranslateFireEvent`/`GTranslateSetCookie`/`GTranslateGetCookie` abaixo
 * reproduzem o mecanismo padrão e publicamente documentado pelo GTranslate para disparar a troca
 * de idioma a partir de um controle próprio (em vez do dropdown nativo deles) — não é o antigo
 * "Google Website Translator Widget" usado diretamente; é a forma suportada de customizar o
 * widget do GTranslate. Por baixo, o motor de tradução ainda é o elemento `goog-te-combo`
 * injetado pelo script deles — só a UI é 100% da CT Price.
 *
 * PERSISTÊNCIA (item 8 do pedido — "usar mecanismo compatível com o próprio widget"): o cookie
 * `googtrans` (formato "/pt/en") é o mecanismo NATIVO do motor de tradução por trás do
 * GTranslate — lido automaticamente em cada carregamento de página, sem precisar de sessão PHP
 * nem de um localStorage próprio. Este arquivo só o lê/escreve, nunca duplica o estado em outro
 * lugar.
 *
 * RETORNO AO PORTUGUÊS (item 9): clicar na bandeira do Brasil chama doGTranslate('pt|pt') —
 * padrão documentado do GTranslate para restaurar o idioma original (source === target desfaz a
 * camada de tradução). Nenhum reload de domínio, nenhuma rota /pt/.
 *
 * FALHA GRACIOSA (itens 14/17): se o widget do GTranslate não estiver configurado
 * (config/translate.php sem `website_id` — ver includes/translate-widget.php, que nesse caso
 * nem imprime o script) ou o serviço externo estiver indisponível, `doGTranslate` desiste em
 * silêncio após um tempo limite — nenhum erro é lançado no console, nenhuma função trava, e o
 * site continua 100% utilizável em português (as bandeiras simplesmente não produzem efeito
 * visível até o serviço responder).
 */
(function () {
    'use strict';

    var LANG_ATTR = 'data-lang';
    var COOKIE_NAME = 'googtrans';
    var DEFAULT_LANG = 'pt';
    var RETRY_DELAY_MS = 400;
    var MAX_ATTEMPTS = 12; // ~4.8s de tentativas antes de desistir silenciosamente

    function GTranslateGetCookie(name) {
        var value = '; ' + document.cookie;
        var parts = value.split('; ' + name + '=');
        if (parts.length === 2) {
            return decodeURIComponent(parts.pop().split(';').shift());
        }
        return null;
    }

    function GTranslateSetCookie(name, value) {
        // Cookie "host-only" (sem atributo `domain`) — mesmo padrão do cookie `googtrans` nativo
        // do motor de tradução. Bug corrigido nesta rodada: uma tentativa anterior de também
        // setar em `domain=.<2 últimos segmentos do host>` duplicava o cookie e, para domínios
        // com TLD composto (ex.: "ctprice.com.br"), calculava um domínio-base ERRADO
        // ("com.br", um sufixo público — o navegador rejeita ou o valor fica incorreto). Sem
        // subdomínios envolvidos neste projeto, host-only já é suficiente e correto.
        document.cookie = name + '=' + value + ';path=/';
    }

    function GTranslateFireEvent(element, event) {
        try {
            if (document.createEventObject) {
                var evt = document.createEventObject();
                element.fireEvent('on' + event, evt);
            } else {
                var evt2 = document.createEvent('HTMLEvents');
                evt2.initEvent(event, true, true);
                element.dispatchEvent(evt2);
            }
        } catch (e) { /* ambiente sem o motor de tradução carregado — tratado pelo timeout de doGTranslate */ }
    }

    function findGoogleCombo() {
        var selects = document.getElementsByTagName('select');
        for (var i = 0; i < selects.length; i++) {
            if (selects[i].className && selects[i].className.indexOf('goog-te-combo') !== -1) {
                return selects[i];
            }
        }
        return null;
    }

    function doGTranslate(langPair, attempt) {
        attempt = attempt || 0;
        if (!langPair) {
            return;
        }
        var target = langPair.split('|')[1];

        GTranslateSetCookie(COOKIE_NAME, '/' + DEFAULT_LANG + '/' + target);

        var combo = findGoogleCombo();
        if (!combo) {
            // Motor ainda não carregou (ou o widget não está configurado — ver
            // includes/translate-widget.php). Tenta de novo por um tempo limitado; depois
            // desiste em silêncio (falha graciosa, sem erro de console).
            if (attempt < MAX_ATTEMPTS) {
                setTimeout(function () { doGTranslate(langPair, attempt + 1); }, RETRY_DELAY_MS);
            }
            return;
        }

        combo.value = target;
        GTranslateFireEvent(combo, 'change');
        GTranslateFireEvent(combo, 'change');
    }

    function currentLangFromCookie() {
        var raw = GTranslateGetCookie(COOKIE_NAME);
        if (!raw) {
            return DEFAULT_LANG;
        }
        var parts = raw.split('/').filter(Boolean); // ["pt", "en"]
        var target = parts[1] || DEFAULT_LANG;
        return target === DEFAULT_LANG ? DEFAULT_LANG : target;
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

        var persistedLang = currentLangFromCookie();
        setActiveButton(buttons, persistedLang);

        // Persistência entre páginas (item 8 do pedido): o motor de tradução por trás do
        // GTranslate normalmente já lê o cookie `googtrans` sozinho ao inicializar, mas disparar
        // aqui também garante a reaplicação em CADA página nova mesmo que essa leitura automática
        // não aconteça a tempo — sem isso, a Home em inglês -> Clientes poderia voltar para
        // português brevemente até o motor reagir por conta própria.
        if (persistedLang !== DEFAULT_LANG) {
            doGTranslate(DEFAULT_LANG + '|' + persistedLang);
        }

        buttons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var lang = btn.getAttribute(LANG_ATTR);
                if (!lang) {
                    return;
                }
                doGTranslate(DEFAULT_LANG + '|' + lang);
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
