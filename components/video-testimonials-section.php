<?php
/**
 * components/video-testimonials-section.php
 *
 * Grade de depoimentos em vídeo de `/depoimentos/` — substitui, com melhorias deliberadas de
 * arquitetura e UX, o padrão do site original (docs/reference/depoimentos-audit.md):
 *
 *   - Uma única grade semântica (`<ul role="list">`) para os 7 cards, não duas seções Elementor
 *     fragmentadas (3 + 4) que só coincidem visualmente por acaso do `flex-wrap`.
 *   - Empresa exibida como texto real (nome + cargo/empresa), não só implícita na arte da
 *     miniatura — melhora acessibilidade e hierarquia (ver auditoria, seção 11/12).
 *   - Ícone de "site" correto (link externo genérico), não o ícone do Chrome usado
 *     incorretamente no original.
 *   - `alt` real em todas as fotos (nunca `alt=""`); miniaturas de vídeo continuam decorativas
 *     (`alt=""`) porque o botão que as envolve já tem `aria-label` descrevendo a ação.
 *   - Sombra leve institucional (mesma linguagem de `.logo-card`/`.job-card`/`.benefit-card`:
 *     `0 2px 6px rgba(0,34,44,0.06)` em repouso, elevação + sombra maior + borda verde no hover),
 *     não a sombra escura `3px 3px 10px rgba(0,0,0,0.5)` do original — sem reutilizar a classe
 *     `.logo-card` em si, por a estrutura do card (foto + citação + miniatura + links) ser
 *     completamente diferente de um card de logo.
 *   - Lightbox de vídeo PRÓPRIO (JS puro, ver assets/js/video-testimonials-lightbox.js),
 *     sem depender do Elementor/jQuery. O `<iframe>` do YouTube só é criado quando o usuário abre
 *     o lightbox (nenhum embed carrega nos 7 cards de largada) e é destruído ao fechar, para o
 *     vídeo parar de tocar imediatamente.
 *
 * NÃO reutiliza components/testimonials-section.php (Home): aquele é um carrossel Swiper de
 * texto puro, sem vídeo, sem foto, sem links sociais — conceitos visuais e interativos
 * incompatíveis (ver auditoria, seção 13). Este componente é usado por uma única página.
 *
 * Grid 3/2/1 via flexbox (`flex-wrap` + `justify-content:center`, ver
 * assets/css/video-testimonials-section.css) — mesma técnica já usada em
 * components/benefits-grid-section.php para centralizar a última linha parcial (1 de 7 cards)
 * sem nenhum seletor `nth-child` dependente da quantidade atual de itens.
 *
 * Espera, definidas pelo chamador antes do include:
 *
 *   $videoTestimonialsSection = [
 *       'heading'    => 'texto do H2 (também funciona como título da seção inteira)',
 *       'intro_html' => 'HTML confiável do texto introdutório (definido pelo chamador)',
 *       'items'      => [ // config/video-testimonials.php
 *           ['name' => ..., 'company' => ..., 'quote' => ..., 'photo' => ..., 'thumbnail' => ...,
 *            'video_id' => ..., 'video_list' => ..., 'website_url' => ..., 'instagram_url' => ...],
 *           ...
 *       ],
 *   ];
 */

$heading = $videoTestimonialsSection['heading'] ?? '';
$introHtml = $videoTestimonialsSection['intro_html'] ?? '';
$items = $videoTestimonialsSection['items'] ?? [];
?>
<section class="video-testimonials-section">
    <div class="video-testimonials-section__inner">
        <h2 id="video-testimonials-heading" class="video-testimonials-section__heading"><?= htmlspecialchars($heading, ENT_QUOTES, 'UTF-8') ?></h2>
        <div class="video-testimonials-section__intro"><?= $introHtml ?></div>

        <ul class="video-testimonials-grid" role="list" aria-labelledby="video-testimonials-heading">
            <?php foreach ($items as $t): ?>
            <?php
                $name = $t['name'] ?? '';
                $clientCompany = $t['company'] ?? '';
                $quote = $t['quote'] ?? '';
                $photoUrl = BASE_URL . '/assets/images/pages/depoimentos/people/' . ($t['photo'] ?? '');
                $thumbUrl = BASE_URL . '/assets/images/pages/depoimentos/thumbnails/' . ($t['thumbnail'] ?? '');
                $videoId = $t['video_id'] ?? '';
                $videoList = $t['video_list'] ?? '';
                $websiteUrl = $t['website_url'] ?? '';
                $instagramUrl = $t['instagram_url'] ?? '';
                $watchLabel = 'Assistir depoimento em vídeo de ' . $name . ', ' . $clientCompany;
                $videoTitle = 'Depoimento de ' . $name . ' (' . $clientCompany . ') para a CT Price';
            ?>
            <li class="video-testimonial-card">
                <button
                    type="button"
                    class="video-testimonial-card__thumb-btn"
                    data-video-id="<?= htmlspecialchars($videoId, ENT_QUOTES, 'UTF-8') ?>"
                    <?php if ($videoList !== ''): ?>data-video-list="<?= htmlspecialchars($videoList, ENT_QUOTES, 'UTF-8') ?>"<?php endif; ?>
                    data-video-title="<?= htmlspecialchars($videoTitle, ENT_QUOTES, 'UTF-8') ?>"
                    aria-label="<?= htmlspecialchars($watchLabel, ENT_QUOTES, 'UTF-8') ?>"
                >
                    <img class="video-testimonial-card__thumb-img" src="<?= htmlspecialchars($thumbUrl, ENT_QUOTES, 'UTF-8') ?>" alt="" loading="lazy" width="1280" height="720">
                    <span class="video-testimonial-card__play" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </span>
                </button>

                <div class="video-testimonial-card__body">
                    <div class="video-testimonial-card__person">
                        <img class="video-testimonial-card__photo" src="<?= htmlspecialchars($photoUrl, ENT_QUOTES, 'UTF-8') ?>" alt="Foto de <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" loading="lazy" width="200" height="200">
                        <div class="video-testimonial-card__identity">
                            <span class="video-testimonial-card__name"><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?></span>
                            <span class="video-testimonial-card__company"><?= htmlspecialchars($clientCompany, ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                    </div>

                    <blockquote class="video-testimonial-card__quote">
                        <p><?= htmlspecialchars($quote, ENT_QUOTES, 'UTF-8') ?></p>
                    </blockquote>

                    <?php if ($websiteUrl !== '' || $instagramUrl !== ''): ?>
                    <div class="video-testimonial-card__links">
                        <?php if ($websiteUrl !== ''): ?>
                        <a class="video-testimonial-card__link" href="<?= htmlspecialchars($websiteUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" aria-label="Visitar site de <?= htmlspecialchars($clientCompany, ENT_QUOTES, 'UTF-8') ?>">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 3h6v6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M10 14L21 3" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </a>
                        <?php endif; ?>
                        <?php if ($instagramUrl !== ''): ?>
                        <a class="video-testimonial-card__link" href="<?= htmlspecialchars($instagramUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram de <?= htmlspecialchars($clientCompany, ENT_QUOTES, 'UTF-8') ?>">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5" ry="5" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="4.5" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="17.3" cy="6.7" r="1.15" fill="currentColor"/></svg>
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="video-testimonial-modal" id="video-testimonial-modal" hidden>
        <div class="video-testimonial-modal__backdrop" data-video-modal-close></div>
        <div class="video-testimonial-modal__dialog" role="dialog" aria-modal="true" aria-label="Vídeo de depoimento">
            <button type="button" class="video-testimonial-modal__close" data-video-modal-close aria-label="Fechar vídeo">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18.3 5.71L12 12l6.3 6.29-1.41 1.42L10.59 13.4 4.3 19.7l-1.41-1.42L9.17 12 2.89 5.71 4.3 4.29l6.29 6.3 6.3-6.3z"/></svg>
            </button>
            <div class="video-testimonial-modal__frame" id="video-testimonial-modal-frame"></div>
        </div>
    </div>
</section>
