/**
 * assets/js/tracking.js
 *
 * Carrega o Meta Pixel (https://connect.facebook.net/en_US/fbevents.js) no site público, só
 * depois que assets/js/cookie-banner.js confirmar o consentimento do visitante ('accept') via
 * evento 'ctprice:cookie-consent' — nunca antes disso. Sem consentimento, este arquivo não
 * carrega nenhum script de terceiros.
 *
 * Pixel ID vem de window.CTPRICE_TRACKING.metaPixelId, exposto por includes/cookie-banner.php a
 * partir de config/tracking.php — ÚNICO lugar onde o ID é definido no projeto. Não incluído em
 * /admin/ (admin usa admin/includes/layout-header.php, que nunca requer includes/cookie-banner.php
 * nem este script).
 *
 * Guard `loaded`/`window.fbq` evita inicializar o Pixel mais de uma vez na mesma página (ex.: se
 * o evento de consentimento disparar mais de uma vez).
 */
(function () {
    'use strict';

    var config = window.CTPRICE_TRACKING || {};
    var pixelId = config.metaPixelId;
    var loaded = false;

    function loadMetaPixel() {
        if (loaded || !pixelId || window.fbq) {
            return;
        }
        loaded = true;

        /* eslint-disable */
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        /* eslint-enable */

        window.fbq('init', pixelId);
        window.fbq('track', 'PageView');
    }

    document.addEventListener('ctprice:cookie-consent', function (event) {
        if (event.detail && event.detail.choice === 'accept') {
            loadMetaPixel();
        }
    });
})();
