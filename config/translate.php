<?php
/**
 * config/translate.php
 *
 * Configuração central da tradução automática do SITE PÚBLICO (GTranslate — Website Translator
 * Widget, https://gtranslate.io). Nunca carregado em `/admin/` (o admin não inclui
 * includes/footer.php, que é o único ponto que lê este arquivo — ver
 * includes/translate-widget.php).
 *
 * NÃO é o antigo "Google Website Translator Widget" (`translate.google.com/translate_a/
 * element.js` embutido diretamente, hoje descontinuado) — este projeto usa o widget HTML
 * gratuito do GTranslate (`cdn.gtranslate.net/widgets/latest/dropdown.js`), a solução atual
 * documentada para sites customizados fora de WordPress/plugins:
 * https://gtranslate.io/blog/google-translate-website-widget-discontinued
 *
 * CORREÇÃO (2026-09-17): a versão anterior desta config condicionava o carregamento do script a
 * um `website_id` (ex.: `cdn.gtranslate.net/widgets/latest/<ID>.js`), supondo que o widget
 * exigisse uma conta/domínio cadastrado no GTranslate antes de traduzir — por isso o motor nunca
 * chegava a carregar e a tradução não acontecia de verdade (só o estado visual das bandeiras e o
 * cookie mudavam). O widget HTML GRATUITO do GTranslate para tradução on-the-fly NÃO usa
 * website_id na URL do script — é sempre `.../widgets/latest/dropdown.js` (nome de arquivo
 * fixo), sem cadastro prévio. Não é necessário assinar plano pago para PT/EN/ES funcionar.
 *
 * Traduz somente a CAMADA VISUAL do DOM já renderizado pelo PHP — nunca o banco (MariaDB),
 * nunca o CMS/admin, nunca as URLs/rotas. Ver includes/translate-widget.php e
 * assets/js/language-switcher.js para o mecanismo completo.
 */

return [
    // Liga/desliga o motor de tradução no site público. `false` aqui (ou remover este arquivo)
    // volta o site a 100% português sem nenhum script externo carregado — não depende de
    // nenhuma credencial (item 14 do pedido do cliente: falha graciosa quando desligado).
    'enabled' => true,

    // Idioma original do conteúdo (fonte oficial, nunca alterado pela tradução).
    'default_language' => 'pt',

    // Ordem de ciclo pedida pelo cliente: PT -> EN -> ES -> PT. A ordem aqui não decide o
    // comportamento das bandeiras (cada bandeira do topbar já é fixa: Brasil=pt, EUA=en,
    // Espanha=es) — é a lista de idiomas habilitados no motor de tradução (window.gtranslateSettings.languages).
    'languages' => ['pt', 'en', 'es'],
];
