<?php
/**
 * components/video-section.php
 *
 * Seção "Ética, agilidade, segurança nos processos e respeito ao cliente" da Home, entre
 * welcome-section e services-section (posição confirmada no DOM original: 4ª seção de nível
 * superior, logo após "Bem-vindo à CT Price").
 *
 * Estrutura: título (H2) + parágrafo centralizado + vídeo institucional autoexibido (self-hosted,
 * não YouTube/Vimeo — confirmado via `data-settings` do widget original: `video_type: "hosted"`),
 * com capa/overlay clicável e botão de reproduzir; sem controles nativos e sem autoplay
 * (comportamento medido no original, inclusive a ausência de controles após iniciar a reprodução —
 * preservado como está, não "corrigido").
 *
 * Sem animação de entrada por scroll: confirmado que nenhum widget desta seção tem `data-settings`
 * de animação no original (diferente de welcome-section/why-choose-us-section) — por isso não usa
 * `data-animate-item`/scroll-reveal.js.
 *
 * Espera, definidas pelo chamador antes do include:
 *
 *   $videoSectionHeading (string) — título (H2)
 *   $videoSectionHtml    (string) — HTML do parágrafo, já com o destaque em negrito embutido
 *                                   (mesmo padrão de $heroSlides['html'] em index.php — conteúdo
 *                                   confiável, definido só pelo chamador, nunca por entrada do
 *                                   usuário; não passa por htmlspecialchars)
 *   $videoSectionSrc     (string) — URL do arquivo de vídeo (self-hosted)
 *   $videoSectionCover   (string) — URL da imagem de capa/overlay
 *
 * Medições: reinspeção direta via Chrome DevTools MCP em 1440x900/900x1200/390x844 (ver relatório
 * final de validação da Home).
 */
?>
<section class="video-section">
    <div class="video-section__inner">
        <div class="video-section__body">
            <h2 class="video-section__heading"><?= htmlspecialchars($videoSectionHeading ?? '', ENT_QUOTES, 'UTF-8') ?></h2>

            <p class="video-section__text"><?= $videoSectionHtml ?? '' ?></p>

            <div class="video-section__player" id="video-section-player">
                <video
                    class="video-section__video"
                    src="<?= htmlspecialchars($videoSectionSrc ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    loop
                    preload="metadata"
                    controlslist="nodownload"
                ></video>
                <div
                    class="video-section__cover"
                    style="background-image: url('<?= htmlspecialchars($videoSectionCover ?? '', ENT_QUOTES, 'UTF-8') ?>');"
                >
                    <button type="button" class="video-section__play" aria-label="Reproduzir vídeo">
                        <svg class="video-section__play-icon" viewBox="0 0 1000 1000" aria-hidden="true">
                            <path d="M838 162C746 71 633 25 500 25 371 25 258 71 163 162 71 254 25 367 25 500 25 633 71 746 163 837 254 929 367 979 500 979 633 979 746 933 838 837 929 746 975 633 975 500 975 367 929 254 838 162M808 192C892 279 933 379 933 500 933 621 892 725 808 808 725 892 621 938 500 938 379 938 279 896 196 808 113 725 67 621 67 500 67 379 108 279 196 192 279 108 383 62 500 62 621 62 721 108 808 192M438 392V642L642 517 438 392Z"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>
