<?php
/**
 * components/hero-slider.php
 *
 * Carrossel de destaque (Hero) — componente reutilizável baseado em Swiper.
 *
 * Espera receber, definida pelo chamador antes do include, a variável:
 *
 *   $heroSlides = [
 *       [
 *           'image' => 'caminho completo (com BASE_URL) da imagem de fundo do slide',
 *           'alt'   => 'texto alternativo da imagem, para leitores de tela',
 *           'html'  => 'HTML confiável (definido internamente, não entrada de usuário) do
 *                       texto do slide — pode conter <br> e <span class="hero-slide__highlight">
 *                       para os trechos em destaque, exatamente como no conteúdo original',
 *       ],
 *       ...
 *   ];
 *
 * Este arquivo só monta a marcação e as classes que o Swiper (assets/vendor/swiper/) espera
 * (swiper, swiper-wrapper, swiper-slide) — a instância é criada por assets/js/hero-init.js.
 * Nenhuma classe ou estrutura do Elementor foi copiada.
 *
 * Configuração e medições: docs/reference/home-desktop-audit.md (seção 12),
 * docs/reference/home-tablet-audit.md e docs/reference/home-mobile-audit.md (seção Hero).
 */

if (!isset($heroSlides) || !is_array($heroSlides)) {
    $heroSlides = [];
}
?>
<section class="hero-slider swiper" aria-label="Destaques CT Price" aria-roledescription="carrossel">
    <div class="swiper-wrapper">
        <?php foreach ($heroSlides as $slide): ?>
        <div
            class="swiper-slide hero-slide"
            style="background-image: url('<?= htmlspecialchars($slide['image'], ENT_QUOTES, 'UTF-8') ?>');"
        >
            <div class="hero-slide__inner">
                <p class="hero-slide__text"><?= $slide['html'] ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
