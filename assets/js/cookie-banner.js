/**
 * assets/js/cookie-banner.js
 *
 * Exibição, escolha do usuário e persistência do aviso de cookies
 * (includes/cookie-banner.php). JavaScript puro, sem dependências.
 *
 * Escopo desta etapa: apenas guardar a escolha do visitante (aceitar/recusar) e não
 * mostrar o banner de novo depois disso. Não carrega nem bloqueia nenhum script de
 * terceiros — o projeto ainda não possui scripts de analytics/marketing para controlar;
 * essa integração é tratada em uma etapa futura separada.
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

    function hasStoredChoice() {
        try {
            var raw = window.localStorage.getItem(STORAGE_KEY);
            if (!raw) {
                return false;
            }
            var data = JSON.parse(raw);
            return data && data.version === STORAGE_VERSION && (data.choice === 'accept' || data.choice === 'refuse');
        } catch (e) {
            return false;
        }
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
    }

    if (!hasStoredChoice()) {
        banner.hidden = false;
    }

    banner.addEventListener('click', function (event) {
        var target = event.target.closest('[data-cookie-choice]');
        if (target) {
            hideBanner(target.getAttribute('data-cookie-choice'));
        }
    });
})();
