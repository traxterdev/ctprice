<?php
/**
 * config/tracking.php
 *
 * Fonte única do(s) ID(s) de tracking/analytics do site público (Meta Pixel). Nenhum ID deve ser
 * digitado diretamente em nenhuma página, include ou arquivo JS — sempre ler a partir daqui (ver
 * config/bootstrap.php, que carrega este arquivo como $tracking, e includes/cookie-banner.php,
 * que expõe os valores ao JavaScript via window.CTPRICE_TRACKING).
 *
 * Carregamento condicionado ao consentimento de cookies já existente — ver
 * assets/js/cookie-banner.js (evento 'ctprice:cookie-consent') e assets/js/tracking.js.
 *
 * Não é um dado sensível/segredo: o Pixel ID é público por natureza (visível no HTML/rede de
 * qualquer site que o usa).
 */

return [
    'meta_pixel_id' => '1977722152900624',
];
