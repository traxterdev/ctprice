<?php
/**
 * components/boxed-hero.php
 *
 * Hero interno "simples" — imagem de fundo full-bleed + duas linhas de heading (eyebrow + título,
 * sem `<strong>` de destaque parcial) dentro de um container boxed de 1140px centralizado. Padrão
 * identificado em docs/reference/clientes-audit.md (seção 2, Hero de `/clientes/`), medido e
 * comparado diretamente contra components/internal-hero.php.
 *
 * NÃO é o mesmo padrão de internal-hero.php — a auditoria confirmou diferenças estruturais reais,
 * não apenas de conteúdo:
 *   - altura 400px (não 640px);
 *   - container 1140px centralizado padrão (`margin:0 auto`), não a fórmula assimétrica
 *     `width:calc(50% - 100px); margin-left:200px` do internal-hero;
 *   - conteúdo em duas headings simples (eyebrow + título), sem `<strong>` de destaque parcial;
 *   - eyebrow em Roboto (não Poppins/font-secondary como no internal-hero).
 * Forçar reuso do internal-hero.php exigiria sobrescrever altura, container, ausência de
 * destaque e fonte do eyebrow — mais modificador do que reaproveitamento real. Por isso, um
 * componente próprio, mesmo espírito de dados-como-array.
 *
 * O mecanismo de background (`url()`, `background-size:cover`, sem overlay/gradiente) é idêntico
 * ao já usado em internal-hero.php — não há necessidade de nova infraestrutura de background
 * aqui, apenas as mesmas propriedades CSS.
 *
 * `background-position` é CONFIGURÁVEL (categoria B — reutilização com modificador pequeno,
 * ver docs/reference/parcerias-audit.md, seção 2): medido como `50% 50%` em /clientes/ mas
 * `0% 0%` em /parcerias/ — mesma imagem de fundo full-bleed em ambas, só o enquadramento muda.
 * Valor padrão `50% 50%` preserva o comportamento já aprovado em /clientes/ sem exigir que esse
 * chamador passe a propriedade explicitamente.
 *
 * Sem breakpoint próprio no original (altura e tipografia idênticas em 1440/900/390, confirmado
 * na auditoria) — nenhuma regra `@media` de redução aplicada aqui.
 *
 * Espera, definidas pelo chamador antes do include:
 *
 *   $boxedHero = [
 *       'eyebrow'             => 'texto curto (maiúsculas aplicadas via CSS)',
 *       'title'               => 'texto do título (HTML confiável, definido pelo chamador)',
 *       'image'               => 'URL da imagem de fundo (com BASE_URL)',
 *       'background_position' => 'opcional — padrão "50% 50%" (ver /parcerias/, que usa "0% 0%")',
 *   ];
 */

$eyebrow = $boxedHero['eyebrow'] ?? '';
$title = $boxedHero['title'] ?? '';
$image = $boxedHero['image'] ?? '';
$backgroundPosition = $boxedHero['background_position'] ?? '50% 50%';
?>
<section class="boxed-hero" style="background-image: url('<?= htmlspecialchars($image, ENT_QUOTES, 'UTF-8') ?>'); background-position: <?= htmlspecialchars($backgroundPosition, ENT_QUOTES, 'UTF-8') ?>;">
    <div class="boxed-hero__inner">
        <h2 class="boxed-hero__eyebrow"><?= htmlspecialchars($eyebrow, ENT_QUOTES, 'UTF-8') ?></h2>
        <h2 class="boxed-hero__title"><?= $title ?></h2>
    </div>
</section>
