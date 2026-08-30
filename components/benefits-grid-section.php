<?php
/**
 * components/benefits-grid-section.php
 *
 * Grade "Nossos Benefícios" de `/trabalhe-conosco/#beneficios` — substitui, com melhorias
 * deliberadas, as 14 imagens soltas e desalinhadas do original
 * (docs/reference/trabalhe-conosco-audit.md, seções 4, 8 e 11): sem card/borda/sombra, largura
 * fixa de 172px com altura proporcional variável (92px a 250px) sem `object-fit`, misturando
 * logos de marca e ilustrações caseiras sem nenhum tratamento visual comum.
 *
 * REUTILIZAÇÃO DELIBERADA: cada item usa a mesma identidade visual de card já aprovada em
 * assets/css/logo-card.css (`.logo-card`/`.logo-card__img` — fundo branco, borda sutil, radius,
 * sombra leve, `object-fit:contain`, hover com elevação + sombra maior + borda verde) — a mesma
 * classe já usada no carrossel de clientes e na grade de `/clientes/`. Um "benefício" aqui é,
 * estruturalmente, o mesmo conceito visual que um "logo em um card": uma imagem única que precisa
 * ser exibida inteira, sem distorção, com identidade consistente com o resto do site. Só a altura
 * do card é maior que o valor-base de `.logo-card` (ver assets/css/benefits-grid-section.css),
 * porque as imagens de benefício têm proporções mais extremas (até 372×497) que os logos de
 * cliente.
 *
 * ACESSIBILIDADE: o nome de cada benefício está desenhado dentro da própria imagem no material
 * original — por isso cada item carrega um `alt` descritivo real (definido em
 * config/benefits.php, a partir de inspeção visual direta de cada arquivo), nunca `alt=""`.
 *
 * Layout: flexbox com `flex-wrap` + `justify-content:center` (não CSS Grid) — a última linha, com
 * menos itens que as anteriores (2 de 14, numa grade de 3 colunas), fica automaticamente
 * centralizada por esse mecanismo, sem precisar calcular "é o último item?" nem depender da
 * quantidade total de benefícios.
 *
 * Espera, definida pelo chamador antes do include:
 *
 *   $benefitsGridSection = [
 *       'id' => 'beneficios', // âncora HTML real da seção (id="beneficios")
 *       'items' => [ // config/benefits.php
 *           ['image' => 'ben01.png', 'alt' => '...'],
 *           ...
 *       ],
 *   ];
 *
 * Cada arquivo é servido de assets/images/pages/trabalhe-conosco/beneficios/ (BASE_URL montado
 * aqui, não pelo chamador, para manter os registros de dados enxutos — mesma convenção de
 * components/clients-carousel-section.php).
 */

$anchorId = $benefitsGridSection['id'] ?? null;
$items = $benefitsGridSection['items'] ?? [];
?>
<section class="benefits-grid-section"<?= $anchorId ? ' id="' . htmlspecialchars($anchorId, ENT_QUOTES, 'UTF-8') . '"' : '' ?>>
    <div class="benefits-grid-section__container">
        <div class="benefits-grid-section__grid">
            <?php foreach ($items as $item): ?>
            <div class="logo-card benefit-card">
                <img
                    class="logo-card__img"
                    src="<?= BASE_URL ?>/assets/images/pages/trabalhe-conosco/beneficios/<?= htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8') ?>"
                    alt="<?= htmlspecialchars($item['alt'], ENT_QUOTES, 'UTF-8') ?>"
                    loading="lazy"
                >
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
