<?php
/**
 * components/ombudsman-intro-section.php
 *
 * Primeiro bloco institucional de `/ouvidoria/`: texto (esquerda) + foto (direita), sobre fundo
 * sólido `--color-dark-teal` (#00222C), full-bleed. Padrão identificado em
 * docs/reference/ouvidoria-audit.md, seção 1 (seção `5eba72d` da referência).
 *
 * NÃO reutiliza components/image-text-section.php: aquele componente não tem background/cor de
 * texto configuráveis (sempre fundo branco/texto escuro) nem o container mais largo medido aqui
 * (1240px, não 1140px/1200px) — forçar reuso exigiria acrescentar props de background e de
 * container a um componente hoje simples, só para um caso de uso. Componente próprio, mesmo
 * espírito de dados-como-array dos demais. Usado uma única vez no projeto (não é um padrão
 * repetido em outra página, ao contrário do que motivou outros componentes compartilhados).
 *
 * Medições: docs/reference/ouvidoria-audit.md, seção 11 — container 1240px, colunas 50/50,
 * parágrafo Roboto 16px/24px `--color-off-white`, breakpoint de empilhamento em 767px (imagem
 * abaixo do texto no mobile, confirmado por medição direta, não presumido).
 *
 * Espera, definidas pelo chamador antes do include:
 *
 *   $ombudsmanIntroSection = [
 *       'content_html' => 'HTML confiável do texto (definido pelo chamador, não entrada de
 *                          usuário) — múltiplos <p>, <strong>/<span> de destaque, exatamente como
 *                          no conteúdo original',
 *       'image'        => 'URL da imagem (com BASE_URL)',
 *       'image_alt'    => 'texto alternativo da imagem',
 *   ];
 */

$contentHtml = $ombudsmanIntroSection['content_html'] ?? '';
$image = $ombudsmanIntroSection['image'] ?? '';
$imageAlt = $ombudsmanIntroSection['image_alt'] ?? '';
?>
<section class="ombudsman-intro-section">
    <div class="ombudsman-intro-section__inner">
        <div class="ombudsman-intro-section__text-col">
            <div class="ombudsman-intro-section__content"><?= $contentHtml ?></div>
        </div>
        <div class="ombudsman-intro-section__image-col">
            <img class="ombudsman-intro-section__image" src="<?= htmlspecialchars($image, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($imageAlt, ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
        </div>
    </div>
</section>
