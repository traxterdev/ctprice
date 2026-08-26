<?php
/**
 * components/why-choose-us-section.php
 *
 * Seção "Por que nos escolher?" da Home, imediatamente após services-section: título + divisor
 * (span full-width) seguidos de duas colunas lado a lado — bloco de imagem (background) e lista
 * de 3 itens (título + texto, sem ícone).
 *
 * Espera, definidas pelo chamador antes do include:
 *
 *   $whyChooseUsHeading (string) — título (H2)
 *   $whyChooseUsImage   (string) — URL da imagem de fundo do bloco visual
 *   $whyChooseUsItems   (array)  — cada item: ['title' => ..., 'text' => ...]
 *
 * Sem ícones (confirmado: nenhum dos 3 itens usa `elementor-icon-box-icon` no original —
 * variante "plain" do icon-box, diferente de welcome-section e services-section).
 *
 * Animações de entrada confirmadas por inspeção direta (dois tipos diferentes na mesma seção):
 *   - bloco de imagem: "fadeIn" (só opacidade)
 *   - cada item de texto: "fadeInRight" (opacidade + deslocamento horizontal)
 * Reproduzidas via CSS + assets/js/scroll-reveal.js (mesma infraestrutura de
 * welcome-section/hero — sem segundo sistema de reveal).
 *
 * Defeito conhecido (categoria C — docs/architecture-proposal.md, seção 2): no site original, no
 * mobile (max-width:767px), o bloco de imagem colapsa para ~390x20px porque sua altura vem só do
 * "stretch" com a coluna de texto (align-items:stretch) — ao empilhar, essa relação desaparece e
 * não sobra altura nenhuma. Corrigido aqui com `aspect-ratio` no bloco de imagem nesse breakpoint
 * (ver assets/css/why-choose-us-section.css) — não reproduzido.
 *
 * Medições: docs/reference/home-desktop-audit.md e reinspeção direta via Chrome DevTools MCP em
 * 1440x900/900x1200/390x844 (ver relatório final).
 */

if (!isset($whyChooseUsItems) || !is_array($whyChooseUsItems)) {
    $whyChooseUsItems = [];
}
?>
<section class="why-choose-us-section">
    <div class="why-choose-us-section__container">
        <div class="why-choose-us-section__heading-block">
            <h2 class="why-choose-us-section__heading"><?= htmlspecialchars($whyChooseUsHeading ?? '', ENT_QUOTES, 'UTF-8') ?></h2>
            <div class="why-choose-us-section__divider" role="presentation"></div>
        </div>

        <div class="why-choose-us-section__image" data-animate-item style="background-image: url('<?= htmlspecialchars($whyChooseUsImage ?? '', ENT_QUOTES, 'UTF-8') ?>');" role="img" aria-label="<?= htmlspecialchars($whyChooseUsHeading ?? '', ENT_QUOTES, 'UTF-8') ?>"></div>

        <div class="why-choose-us-section__list">
            <?php foreach ($whyChooseUsItems as $item): ?>
            <div class="why-choose-us-item" data-animate-item>
                <h3 class="why-choose-us-item__title"><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                <p class="why-choose-us-item__text"><?= htmlspecialchars($item['text'], ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
