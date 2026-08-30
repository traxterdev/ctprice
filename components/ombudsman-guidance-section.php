<?php
/**
 * components/ombudsman-guidance-section.php
 *
 * Segundo bloco institucional de `/ouvidoria/`: texto de confidencialidade (esquerda) + heading
 * "Quando utilizar a Ouvidoria?" com lista real (direita), fundo branco. Padrão identificado em
 * docs/reference/ouvidoria-audit.md, seção 1/3.3 (seção `838af24` da referência).
 *
 * Os 3 itens originais já formam conceitualmente uma lista (marcadores "•" no texto original) —
 * reproduzidos aqui como `<ul><li>` real, não como texto solto, por serem genuinamente uma lista
 * de itens paralelos (não uma decisão de reescrever o conteúdo).
 *
 * Container 1240px (mesmo valor medido do bloco anterior, `ombudsman-intro-section.php`) —
 * componente próprio pelo mesmo motivo (nenhum componente existente tinha esse container antes
 * desta página).
 *
 * Espera, definidas pelo chamador antes do include:
 *
 *   $ombudsmanGuidanceSection = [
 *       'content_html' => 'HTML confiável do texto de confidencialidade (múltiplos <p>, <strong>)',
 *       'heading'      => 'texto do heading da coluna direita',
 *       'items'        => ['item 1', 'item 2', 'item 3'], // texto simples, sem HTML
 *   ];
 */

$contentHtml = $ombudsmanGuidanceSection['content_html'] ?? '';
$heading = $ombudsmanGuidanceSection['heading'] ?? '';
$items = $ombudsmanGuidanceSection['items'] ?? [];
?>
<section class="ombudsman-guidance-section">
    <div class="ombudsman-guidance-section__inner">
        <div class="ombudsman-guidance-section__text-col">
            <div class="ombudsman-guidance-section__content"><?= $contentHtml ?></div>
        </div>
        <div class="ombudsman-guidance-section__list-col">
            <h2 class="ombudsman-guidance-section__heading"><?= htmlspecialchars($heading, ENT_QUOTES, 'UTF-8') ?></h2>
            <ul class="ombudsman-guidance-section__list">
                <?php foreach ($items as $item): ?>
                <li><?= htmlspecialchars($item, ENT_QUOTES, 'UTF-8') ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</section>
