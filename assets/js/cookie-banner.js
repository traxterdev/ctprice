/**
 * assets/js/cookie-banner.js
 *
 * Exibição, escolha do usuário e persistência do aviso de cookies
 * (includes/cookie-banner.php). JavaScript puro, sem dependências.
 *
 * Escopo desta etapa: guardar a escolha do visitante (aceitar/recusar), não mostrar o
 * banner de novo depois disso, e avisar o resto da página sobre a escolha (evento
 * 'ctprice:cookie-consent', detail: { choice }) — disparado tanto quando o visitante clica
 * em uma opção quanto, numa página nova, quando já existe uma escolha válida salva. Este
 * arquivo em si não carrega nenhum script de terceiros; quem decide o que fazer com o
 * evento é assets/js/tracking.js (Meta Pixel).
 *
 * Persistência: localStorage, chave versionada — mudar STORAGE_VERSION invalida escolhas
 * salvas anteriormente e volta a exibir o banner para todos os visitantes.
 */
(function () {
    'use strict';

    var STORAGE_KEY = 'ctprice_cookie_consent';
    var STORAGE_VERSION = 1;

    var banner = document.getElementById('cookie-banner');
    if (!banner) {
        return;
    }

    function getStoredChoice() {
        try {
            var raw = window.localStorage.getItem(STORAGE_KEY);
            if (!raw) {
                return null;
            }
            var data = JSON.parse(raw);
            if (data && data.version === STORAGE_VERSION && (data.choice === 'accept' || data.choice === 'refuse')) {
                return data.choice;
            }
            return null;
        } catch (e) {
            return null;
        }
    }

    function dispatchConsent(choice) {
        document.dispatchEvent(new CustomEvent('ctprice:cookie-consent', { detail: { choice: choice } }));
    }

    function storeChoice(choice) {
        try {
            window.localStorage.setItem(STORAGE_KEY, JSON.stringify({
                choice: choice,
                version: STORAGE_VERSION,
                timestamp: Date.now()
            }));
        } catch (e) {
            // Sem localStorage disponível (ex.: navegação privada restrita): a escolha
            // simplesmente não persiste entre carregamentos; o banner continua funcional.
        }
    }

    function hideBanner(choice) {
        storeChoice(choice);
        banner.hidden = true;
        dispatchConsent(choice);
    }

    var storedChoice = getStoredChoice();
    if (storedChoice) {
        dispatchConsent(storedChoice);
    } else {
        banner.hidden = false;
    }

    banner.addEventListener('click', function (event) {
        var target = event.target.closest('[data-cookie-choice]');
        if (target) {
            hideBanner(target.getAttribute('data-cookie-choice'));
        }
    });
})();
