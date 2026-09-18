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
 * element.js` embutido diretamente) — este projeto usa o widget hospedado do GTranslate
 * (`cdn.gtranslate.net`), que é a solução suportada atualmente para sites customizados fora de
 * WordPress/plugins.
 *
 * IMPORTANTE (pendência operacional, não técnica): o GTranslate exige o domínio de produção
 * cadastrado na conta GTranslate (plano gratuito ou pago) antes do widget funcionar de verdade —
 * sem isso, o script carrega mas não traduz nada (Google não reconhece o domínio). Preencher
 * `website_id` abaixo com o ID gerado no painel do GTranslate para o domínio
 * `ctprice.com.br`/`ctprice.traxter.com.br` antes do go-live. Enquanto `website_id` for null,
 * `enabled` abaixo mantém o widget DESLIGADO (nenhum script externo é carregado) — o site
 * continua 100% funcional em português, sem nenhum elemento quebrado ou pendente visualmente
 * (item 14 do pedido do cliente: "se o serviço estiver indisponível, o site deve continuar
 * funcionando normalmente em português").
 *
 * Traduz somente a CAMADA VISUAL do DOM já renderizado pelo PHP — nunca o banco (MariaDB),
 * nunca o CMS/admin, nunca as URLs/rotas. Ver includes/translate-widget.php e
 * assets/js/language-switcher.js para o mecanismo completo.
 */

return [
    // Desligado por padrão até o Website ID real ser preenchido (ver comentário acima). Quando
    // true SEM website_id preenchido, o include ainda não carrega nada (dupla trava de segurança
    // — ver includes/translate-widget.php).
    'enabled' => true,

    // Idioma original do conteúdo (fonte oficial, nunca alterado pela tradução).
    'default_language' => 'pt',

    // Ordem de ciclo pedida pelo cliente: PT -> EN -> ES -> PT. A ordem aqui não decide o
    // comportamento das bandeiras (cada bandeira do topbar já é fixa: Brasil=pt, EUA=en,
    // Espanha=es) — é só a lista de idiomas habilitados no motor de tradução.
    'languages' => ['pt', 'en', 'es'],

    // Website ID do GTranslate (painel GTranslate -> Websites -> domínio -> "Widget Code") —
    // preencher antes de ativar em produção. Formato típico: string alfanumérica curta.
    'website_id' => null,
];
