<?php
/**
 * components/internal-hero.php
 *
 * Hero interno reutilizável para páginas de conteúdo institucional (ex.: /sobre-nos/) — padrão
 * identificado em docs/reference/sobre-nos-audit.md, seção 2: imagem de fundo full-bleed estática
 * (sem carrossel) + eyebrow + título, texto alinhado à esquerda numa coluna, com uma faixa da
 * imagem sempre visível ao lado (sem overlay de cor).
 *
 * Genérico por design — recebe os dados de cada página, sem conteúdo fixo de "A CT Price":
 *
 *   $internalHero = [
 *       'eyebrow' => 'texto curto, maiúsculas aplicadas via CSS (text-transform)',
 *       'title'   => 'HTML confiável (definido pelo chamador, não entrada de usuário) do
 *                     título — pode conter <strong> para o trecho em negrito, exatamente como
 *                     no conteúdo original',
 *       'image'   => 'URL da imagem de fundo (com BASE_URL)',
 *   ];
 *
 * Medições: reinspeção direta via Chrome DevTools MCP em 1440x900/900x1200/390x844 (ver relatório
 * de implementação). Estrutura simplificada em relação ao original: o original usa uma segunda
 * coluna vazia (`e-con-full` sem conteúdo) só para dividir o espaço com a coluna de texto; aqui
 * essa coluna foi omitida porque, com larguras fixas medidas para a coluna de texto, o espaço
 * restante já fica visualmente idêntico (mostra a imagem de fundo) sem precisar de um elemento
 * vazio no DOM — não é uma cópia da arquitetura do Elementor, é a mesma composição visual medida.
 *
 * CORREÇÃO INTENCIONAL DE DEFEITO CONHECIDO (categoria C — ver sobre-nos-audit.md, seção 8): o
 * original mantém `margin-left:200px` fixo e `font-size` do título fixo (40px) em qualquer
 * largura, causando quebra de linha extrema e overflow horizontal no mobile (confirmado:
 * `scrollWidth > clientWidth` em 390px). Aqui, em `max-width:767px`
 * (assets/css/internal-hero.css), a margem fixa é substituída por padding responsivo e a
 * tipografia é reduzida — preservando imagem, altura geral, eyebrow, título e a composição de
 * texto ocupando uma faixa da imagem, mas permitindo leitura sem overflow.
 */

$eyebrow = $internalHero['eyebrow'] ?? '';
$title = $internalHero['title'] ?? '';
$image = $internalHero['image'] ?? '';
?>
<section class="internal-hero" style="background-image: url('<?= htmlspecialchars($image, ENT_QUOTES, 'UTF-8') ?>');">
    <div class="internal-hero__content">
        <p class="internal-hero__eyebrow"><?= htmlspecialchars($eyebrow, ENT_QUOTES, 'UTF-8') ?></p>
        <h1 class="internal-hero__title"><?= $title ?></h1>
    </div>
</section>
