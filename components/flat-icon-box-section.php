<?php
/**
 * components/flat-icon-box-section.php
 *
 * Bloco "heading + grid de icon-boxes planos" — terceiro padrão de icon-box identificado em
 * docs/reference/sobre-nos-audit.md (seção "Missão / Visão / Valores", `/sobre-nos/`), distinto
 * dos dois já usados na Home (círculo com borda em "Bem-vindo"; cartão com borda em "Nossos
 * Serviços"): ícone SVG colorido "solto" (sem borda/círculo/cartão), título e texto centralizados.
 *
 * Reutilizável por qualquer página interna que precise do mesmo padrão simples — não específico
 * de "A CT Price". Sem heading/eyebrow adicional além do único H2 da seção (confirmado: esta
 * seção não tem divisor, diferente de outras seções do site que têm).
 *
 * Medições: reinspeção direta via Chrome DevTools MCP em 1440x900/900x1200/390x844 (ver relatório
 * de implementação). Breakpoint de empilhamento (max-width:767px) confirmado independentemente
 * para este componente — o grid permanece em 3 colunas fluidas (sem coluna fixa) até 767px,
 * onde colapsa direto para 1 coluna (sem estágio intermediário de 2 colunas).
 *
 * Espera, definidas pelo chamador antes do include:
 *
 *   $flatIconBoxSection = [
 *       'heading' => 'texto do H2 da seção',
 *       'items' => [
 *           [
 *               'icon_svg' => 'HTML confiável (definido pelo chamador, não entrada de usuário)
 *                              do SVG inline do ícone — viewBox e path exatamente como no
 *                              original',
 *               'title'    => 'título do card',
 *               'content'  => 'texto do card (verbatim do original)',
 *           ],
 *           ...
 *       ],
 *   ];
 *
 * Nenhum modificador além dos dados em si — evita transformar isto num page builder.
 */

$heading = $flatIconBoxSection['heading'] ?? '';
$items = $flatIconBoxSection['items'] ?? [];
?>
<section class="flat-icon-box-section">
    <div class="flat-icon-box-section__inner">
        <h2 class="flat-icon-box-section__heading"><?= htmlspecialchars($heading, ENT_QUOTES, 'UTF-8') ?></h2>

        <div class="flat-icon-box-section__grid">
            <?php foreach ($items as $item): ?>
            <div class="flat-icon-box">
                <div class="flat-icon-box__icon"><?= $item['icon_svg'] ?? '' ?></div>
                <div class="flat-icon-box__content">
                    <h3 class="flat-icon-box__title"><?= htmlspecialchars($item['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h3>
                    <p class="flat-icon-box__text"><?= htmlspecialchars($item['content'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
