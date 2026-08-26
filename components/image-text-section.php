<?php
/**
 * components/image-text-section.php
 *
 * Bloco genérico "imagem + texto" em duas colunas — padrão identificado em
 * docs/reference/sobre-nos-audit.md (seção "História", `/sobre-nos/`): sem heading/eyebrow
 * próprios, imagem centralizada verticalmente numa coluna, texto alinhado ao topo na outra.
 * Reutilizável por qualquer página interna que precise do mesmo padrão simples de conteúdo
 * institucional — não é específico de "A CT Price".
 *
 * Medições: reinspeção direta via Chrome DevTools MCP em 1440x900/900x1200/390x844 (ver relatório
 * de implementação). Breakpoint de empilhamento (max-width:767px) confirmado independentemente
 * para este componente, não presumido a partir de outras seções.
 *
 * Espera, definidas pelo chamador antes do include:
 *
 *   $imageTextSection = [
 *       'image'         => 'URL da imagem (com BASE_URL)',
 *       'image_alt'     => 'texto alternativo da imagem',
 *       'content_html'  => 'HTML confiável do texto (definido pelo chamador, não entrada de
 *                           usuário) — pode conter <strong>, <br>, múltiplos <p>, exatamente
 *                           como no conteúdo original',
 *       'image_position' => 'left' (padrão) | 'right' — de que lado a imagem fica no desktop/
 *                            tablet; no mobile a imagem sempre vem primeiro (empilhado), como no
 *                            original.
 *   ];
 *
 * Modificador estritamente necessário: `image_position` (a única variação estrutural observada
 * até agora — texto e imagem podem trocar de lado entre seções/páginas sem mudar nenhuma outra
 * medida). Nenhum outro modificador foi adicionado para não transformar isto num page builder.
 */

$image = $imageTextSection['image'] ?? '';
$imageAlt = $imageTextSection['image_alt'] ?? '';
$contentHtml = $imageTextSection['content_html'] ?? '';
$imagePosition = $imageTextSection['image_position'] ?? 'left';
$modifierClass = $imagePosition === 'right' ? ' image-text-section--image-right' : '';
?>
<section class="image-text-section<?= $modifierClass ?>">
    <div class="image-text-section__inner">
        <div class="image-text-section__image-col">
            <img class="image-text-section__image" src="<?= htmlspecialchars($image, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($imageAlt, ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
        </div>
        <div class="image-text-section__text-col">
            <div class="image-text-section__content"><?= $contentHtml ?></div>
        </div>
    </div>
</section>
