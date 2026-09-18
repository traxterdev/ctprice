<?php
/**
 * includes/translate-widget.php
 *
 * Motor de tradução do site público — GTranslate Website Translator Widget
 * (https://gtranslate.io), NÃO o antigo Google Website Translator Widget embutido direto.
 *
 * Incluído SOMENTE por includes/footer.php, que por sua vez é incluído SOMENTE pelas páginas do
 * site público (nunca por `/admin/`, que usa admin/includes/layout-footer.php próprio) — ver
 * grep de confirmação no relatório desta tarefa. Nenhuma página administrativa carrega este
 * arquivo nem o script externo que ele referencia.
 *
 * O widget nativo do GTranslate (bandeiras/dropdown próprios) fica escondido via CSS
 * (`.gtranslate_wrapper { display:none }`, ver assets/css/header.css) — o visitante só vê o
 * seletor visual já existente da CT Price no topbar (bandeiras Brasil/EUA/Espanha). A troca real
 * de idioma é disparada por assets/js/language-switcher.js, que aciona o motor de tradução
 * carregado aqui por baixo dos panos.
 *
 * Falha graciosa: se `config/translate.php` não tiver `website_id` preenchido (pendência
 * operacional — precisa do domínio cadastrado no painel do GTranslate antes do go-live), este
 * include não imprime nada — nenhum script externo é solicitado, o site permanece 100%
 * funcional em português. Mesmo com o script carregado, se o serviço do GTranslate estiver fora
 * do ar, o `defer` e a ausência de qualquer dependência de bloqueio garantem que o resto da
 * página carregue normalmente (ver assets/js/language-switcher.js para o fallback de clique).
 */

$translateConfig = require __DIR__ . '/../config/translate.php';

if (empty($translateConfig['enabled']) || empty($translateConfig['website_id'])) {
    return;
}
?>
<div class="gtranslate_wrapper" aria-hidden="true"></div>
<script>
    window.gtranslateSettings = {
        "default_language": "<?= htmlspecialchars($translateConfig['default_language'], ENT_QUOTES, 'UTF-8') ?>",
        "languages": <?= json_encode($translateConfig['languages']) ?>,
        "wrapper_selector": ".gtranslate_wrapper",
        // "dropdown" é o tipo de widget nativo do GTranslate que expõe o elemento
        // `goog-te-combo` de forma mais previsível para controle programático externo — o
        // dropdown em si nunca é exibido (escondido via CSS), só o motor por trás dele é usado.
        "switcher_horizontal_position": "inline",
        "flag_style": "2d"
    };
</script>
<script src="https://cdn.gtranslate.net/widgets/latest/<?= htmlspecialchars($translateConfig['website_id'], ENT_QUOTES, 'UTF-8') ?>.js" defer></script>
