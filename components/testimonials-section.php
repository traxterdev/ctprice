<?php
/**
 * components/testimonials-section.php
 *
 * Seção "O que dizem nossos clientes" da Home, entre services-section e o carrossel de
 * clientes/parceiros (posição confirmada no DOM original: 6ª seção de nível superior).
 *
 * AJUSTE (2026-09-17, pedido explícito do cliente — ver "Alterações para o site da CT Price"):
 * deixou de ser um carrossel Swiper de 1 slide de texto estático por vez (comportamento
 * original desta seção) e passou a seguir o MESMO conceito visual já aprovado em
 * components/video-testimonials-section.php (/depoimentos/): foto do depoente, nome, empresa,
 * depoimento e links de site/Instagram quando existirem — em cards lado a lado (3 no desktop,
 * 2 no tablet, 1 no mobile), com paginação discreta ("aqueles botões embaixo", nas palavras do
 * cliente) abaixo do carrossel.
 *
 * NÃO recria a arquitetura de depoimentos: os dados vêm da MESMA fonte de
 * components/video-testimonials-section.php (`video_testimonials` via
 * TestimonialRepository::allActive(), ver index.php) — este componente só monta o card de
 * texto/foto/links; a miniatura de vídeo com lightbox continua exclusiva de /depoimentos/ (ver
 * comentário de video-testimonials-section.php sobre por que os dois não compartilham
 * componente: aqui é só a citação, sem vídeo).
 *
 * Continua usando o Swiper já carregado pela Home (assets/vendor/swiper/) — nenhuma biblioteca
 * nova. Setas + paginação por bolinhas preservadas (mesmo padrão visual já existente nesta
 * seção); só o número de slides visíveis e o conteúdo do card mudam — ver
 * assets/js/testimonials-init.js e assets/css/testimonials-section.css.
 *
 * AJUSTE (2026-09-17, ajuste pontual do cliente): cada card ganhou um ícone discreto de "ver
 * depoimento" (olho, mesmo acabamento visual dos ícones de site/Instagram — círculo
 * `.testimonial-card__link` já existente, não um terceiro estilo novo), e a seção ganhou um CTA
 * "Ver todos os depoimentos" centralizado abaixo da paginação — ambos apontam para a página
 * geral `/depoimentos/` (não existe página individual por depoimento, então nenhuma URL/slug
 * individual é inventada aqui). Um segundo ajuste (mesma data) removeu a versão anterior deste
 * ícone como link de TEXTO sublinhado ("Ver depoimento") — destoava do padrão visual do site,
 * parecendo link HTML cru dentro do card.
 *
 * Espera, definida pelo chamador antes do include:
 *
 *   $testimonials = [ // mesmo formato retornado por TestimonialRepository::allActive()
 *       [
 *           'name'          => 'nome do depoente',
 *           'company'       => 'empresa do depoente',
 *           'quote'         => 'depoimento (texto puro, sem aspas — adicionadas via CSS/markup)',
 *           'photo'         => 'caminho relativo completo da foto (ex.: assets/images/pages/
 *                                depoimentos/people/arquivo.jpg)',
 *           'website_url'   => 'URL do site do cliente, ou "" quando não houver',
 *           'instagram_url' => 'URL do Instagram do cliente, ou "" quando não houver',
 *       ],
 *       ...
 *   ];
 *   $testimonialsCta = ['label' => ..., 'url' => ...]; // opcional — CTA abaixo do carrossel
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
                        <?php
                            $name = $t['name'] ?? '';
                            $clientCompany = $t['company'] ?? '';
                            $quote = $t['quote'] ?? '';
                            $photoUrl = ($t['photo'] ?? '') !== '' ? BASE_URL . '/' . $t['photo'] : '';
                            $websiteUrl = $t['website_url'] ?? '';
                            $instagramUrl = $t['instagram_url'] ?? '';
                        ?>
                        <div class="swiper-slide">
                            <article class="testimonial-card">
                                <div class="testimonial-card__person">
                                    <?php if ($photoUrl !== ''): ?>
                                    <img class="testimonial-card__avatar" src="<?= htmlspecialchars($photoUrl, ENT_QUOTES, 'UTF-8') ?>" alt="Foto de <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" loading="lazy" width="56" height="56">
                                    <?php endif; ?>
                                    <cite class="testimonial-card__cite">
                                        <span class="testimonial-card__name"><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?></span>
                                        <span class="testimonial-card__company"><?= htmlspecialchars($clientCompany, ENT_QUOTES, 'UTF-8') ?></span>
                                    </cite>
                                </div>

                                <p class="testimonial-card__text">&ldquo;<?= htmlspecialchars($quote, ENT_QUOTES, 'UTF-8') ?>&rdquo;</p>

                                <div class="testimonial-card__links">
                                    <?php if ($websiteUrl !== ''): ?>
                                    <a class="testimonial-card__link" href="<?= htmlspecialchars($websiteUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" aria-label="Visitar site de <?= htmlspecialchars($clientCompany, ENT_QUOTES, 'UTF-8') ?>">
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 3h6v6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M10 14L21 3" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </a>
                                    <?php endif; ?>
                                    <?php if ($instagramUrl !== ''): ?>
                                    <a class="testimonial-card__link" href="<?= htmlspecialchars($instagramUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram de <?= htmlspecialchars($clientCompany, ENT_QUOTES, 'UTF-8') ?>">
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5" ry="5" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="4.5" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="17.3" cy="6.7" r="1.15" fill="currentColor"/></svg>
                                    </a>
                                    <?php endif; ?>
                                    <a class="testimonial-card__link" href="/depoimentos/" aria-label="Ver depoimento" title="Ver depoimento">
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="3" fill="none" stroke="currentColor" stroke-width="2"/></svg>
                                    </a>
                                </div>
                            </article>
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

        <?php if (!empty($testimonialsCta['url'])): ?>
        <div class="testimonials-section__cta">
            <a class="btn btn--filled" href="<?= htmlspecialchars($testimonialsCta['url'], ENT_QUOTES, 'UTF-8') ?>">
                <?= htmlspecialchars($testimonialsCta['label'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            </a>
        </div>
        <?php endif; ?>
    </div>
</section>
