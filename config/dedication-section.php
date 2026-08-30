<?php
/**
 * config/dedication-section.php
 *
 * Texto compartilhado da seção "Dedicação aos resultados e Compromisso com nossos clientes"
 * (heading, parágrafos, CTA) — extraído de sobre-nos/index.php para evitar duplicar o mesmo bloco
 * textual em `/informacoes/`, onde o texto é 100% idêntico (confirmado caractere a caractere na
 * auditoria: docs/reference/informacoes-audit.md, seção 3.3). Apenas a imagem muda por página —
 * cada `index.php` requer este arquivo e define sua própria `image`/`image_alt` antes de incluir
 * components/image-content-cta-section.php.
 *
 * Não é um sistema de conteúdo genérico nem CMS — apenas um array estático movido para um único
 * lugar para não copiar um parágrafo grande de HTML entre dois arquivos.
 *
 * CORREÇÃO DE DEFEITO CONHECIDO (categoria C, já aplicada em /sobre-nos/): `cta_url` aponta para
 * `/fale-conosco/`, não para `https://ctprice.com.br/contato` (404 no site original).
 */

return [
    'heading_html' => '<span style="color:#10E36B;font-weight:bold">Dedicação</span> aos resultados e <span style="color:#10E36B;font-weight:bold">Compromisso</span> com nossos clientes.',
    'content_html' => '<p>Temos um <strong><span style="color:#10E36B">compromisso</span></strong> com os resultados excepcionais e total dedicação ao sucesso dos <strong><span style="color:#10E36B">nossos clientes</span></strong>.</p><p><strong><span style="color:#10E36B">Trabalhamos incansavelmente</span></strong> para atender suas necessidades e superar expectativas, garantindo que cada detalhe seja tratado com o <strong><span style="color:#10E36B">máximo cuidado e eficiência</span></strong>.</p>',
    'cta_label' => 'Fale Conosco',
    'cta_url' => '/fale-conosco/',
];
