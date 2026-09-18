<?php
/**
 * includes/translate-widget.php
 *
 * Motor de tradução do site público — GTranslate Website Translator Widget, versão HTML
 * GRATUITA (https://gtranslate.io/website-translator-widget), NÃO o antigo Google Website
 * Translator Widget embutido direto (descontinuado).
 *
 * Incluído SOMENTE por includes/footer.php, que por sua vez é incluído SOMENTE pelas páginas do
 * site público (nunca por `/admin/`, que usa admin/includes/layout-footer.php próprio). Nenhuma
 * página administrativa carrega este arquivo nem o script externo que ele referencia.
 *
 * O widget nativo do GTranslate (dropdown próprio) fica escondido via CSS
 * (`.gtranslate_wrapper { display:none }`, ver assets/css/header.css) — o visitante só vê o
 * seletor visual já existente da CT Price no topbar (bandeiras Brasil/EUA/Espanha). A troca real
 * de idioma é disparada por assets/js/language-switcher.js, que aciona o motor de tradução
 * carregado aqui por baixo dos panos.
 *
 * CORREÇÃO (2026-09-17): a versão anterior deste arquivo só imprimia o script quando
 * `config/translate.php` tinha um `website_id` preenchido, e montava a URL do script como
 * `.../widgets/latest/<website_id>.js` — supondo (incorretamente) que o widget gratuito
 * exigisse uma conta/domínio cadastrado no GTranslate. O widget HTML GRATUITO real não usa
 * website_id: a URL do script é sempre `.../widgets/latest/dropdown.js` (nome de arquivo fixo),
 * e a tradução on-the-fly funciona sem nenhum cadastro prévio — confirmado no snippet oficial
 * atual do GTranslate (ver gtranslate.io/blog/google-translate-website-widget-discontinued).
 * Por isso o motor nunca carregava antes e nada era realmente traduzido, mesmo com as bandeiras
 * e o cookie `googtrans` funcionando visualmente.
 *
 * Falha graciosa: `config/translate.php['enabled'] = false` (ou o arquivo ausente) faz este
 * include não imprimir nada — nenhum script externo é solicitado, o site permanece 100%
 * funcional em português. Com o script carregado, se o CDN do GTranslate estiver fora do ar, o
 * `defer` e a ausência de qualquer dependência de bloqueio garantem que o resto da página
 * carregue normalmente (ver assets/js/language-switcher.js para o fallback de clique).
 */

$translateConfig = @require __DIR__ . '/../config/translate.php';

if (empty($translateConfig['enabled'])) {
    return;
}
?>
<div class="gtranslate_wrapper" aria-hidden="true"></div>
<script>
    window.gtranslateSettings = {
        "default_language": "<?= htmlspecialchars($translateConfig['default_language'], ENT_QUOTES, 'UTF-8') ?>",
        "languages": <?= json_encode($translateConfig['languages']) ?>,
        "wrapper_selector": ".gtranslate_wrapper"
    };
</script>
<script src="https://cdn.gtranslate.net/widgets/latest/dropdown.js" defer></script>
