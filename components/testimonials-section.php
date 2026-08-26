<?php
/**
 * components/testimonials-section.php
 *
 * Seção "O que dizem nossos clientes" da Home, entre services-section e why-choose-us-section
 * (posição confirmada no DOM original: 6ª seção de nível superior — logo após "Nossos Serviços"
 * e antes do carrossel de clientes/parceiros, ainda não implementado).
 *
 * Carrossel de depoimentos via Swiper (assets/vendor/swiper/) — configuração própria desta
 * seção, medida diretamente no `data-settings` do widget original ("Testimonial Carousel"), NÃO
 * reaproveitada do Hero: `speed:500`, `autoplay: 5000ms`, `loop:yes`, setas + paginação por
 * bolinhas visíveis, `space_between:10px` (idêntico em desktop/tablet/mobile — sem breakpoint
 * responsivo próprio no `data-settings`). Inicializado por assets/js/testimonials-init.js.
 *
 * Espera, definida pelo chamador antes do include:
 *
 *   $testimonials = [
 *       [
 *           'text'    => 'depoimento completo, com as aspas já incluídas como no original
 *                         (caracteres literais, não geradas por CSS) — pode conter "\n" para
 *                         quebras de parágrafo do texto original; sem white-space especial no
 *                         CSS, colapsam visualmente como no site original (confirmado)',
 *           'avatar'  => 'URL da foto do depoente',
 *           'name'    => 'nome do depoente',
 *           'company' => 'cargo/empresa do depoente',
 *       ],
 *       ...
 *   ];
 *
 * Tipografia: nome em Roboto (--font-primary); cargo/empresa em Roboto Slab
 * (--font-tertiary) — confirmado via inspeção direta que SOMENTE o campo de cargo/empresa usa
 * essa família nesta seção (widget original usa a tipografia "secundária" do Elementor só nesse
 * elemento). Não aplicada a mais nada na seção.
 *
 * Medições: reinspeção direta via Chrome DevTools MCP em 1440x900/900x1200/390x844 (ver relatório
 * final de validação da Home).
 */

if (!isset($testimonials) || !is_array($testimonials)) {
    $testimonials = [];
}
?>
<section class="testimonials-section">
    <div class="testimonials-section__inner">
        <h2 class="testimonials-section__heading">O que dizem nossos clientes</h2>
        <div class="testimonials-section__divider" role="presentation"></div>

        <div class="testimonials-section__carousel">
            <div class="testimonials-swiper-wrap">
                <div class="testimonials-swiper swiper" aria-label="Depoimentos de clientes" aria-roledescription="carrossel">
                    <div class="swiper-wrapper">
                        <?php foreach ($testimonials as $t): ?>
                        <div class="swiper-slide testimonial-card">
                            <p class="testimonial-card__text"><?= htmlspecialchars($t['text'], ENT_QUOTES, 'UTF-8') ?></p>
                            <div class="testimonial-card__footer">
                                <img class="testimonial-card__avatar" src="<?= htmlspecialchars($t['avatar'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($t['name'], ENT_QUOTES, 'UTF-8') ?>" loading="lazy" width="65" height="65">
                                <cite class="testimonial-card__cite">
                                    <span class="testimonial-card__name"><?= htmlspecialchars($t['name'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <span class="testimonial-card__company"><?= htmlspecialchars($t['company'], ENT_QUOTES, 'UTF-8') ?></span>
                                </cite>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <button type="button" class="testimonials-swiper__nav testimonials-swiper__nav--prev" aria-label="Depoimento anterior">
                    <svg viewBox="0 0 1000 1000" aria-hidden="true"><path d="M646 125C629 125 613 133 604 142L308 442C296 454 292 471 292 487 292 504 296 521 308 533L604 854C617 867 629 875 646 875 663 875 679 871 692 858 704 846 713 829 713 812 713 796 708 779 692 767L438 487 692 225C700 217 708 204 708 187 708 171 704 154 692 142 675 129 663 125 646 125Z"/></svg>
                </button>
                <button type="button" class="testimonials-swiper__nav testimonials-swiper__nav--next" aria-label="Próximo depoimento">
                    <svg viewBox="0 0 1000 1000" aria-hidden="true"><path d="M696 533C708 521 713 504 713 487 713 471 708 454 696 446L400 146C388 133 375 125 354 125 338 125 325 129 313 142 300 154 292 171 292 187 292 204 296 221 308 233L563 492 304 771C292 783 288 800 288 817 288 833 296 850 308 863 321 871 338 875 354 875 371 875 388 867 400 854L696 533Z"/></svg>
                </button>
            </div>

            <div class="testimonials-swiper__pagination" aria-hidden="true"></div>
        </div>
    </div>
</section>
