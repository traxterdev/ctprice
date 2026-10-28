<?php
/**
 * components/ombudsman-form-section.php
 *
 * Bloco final de `/ouvidoria/`: heading + texto introdutório + botão de WhatsApp exclusivo da
 * Ouvidoria (esquerda) + vídeo institucional do Dr. Luis Otávio e CTA para o formulário externo
 * (Google Forms, direita), sobre fundo sólido `--color-dark-teal` (#00222C) com marca d'água
 * decorativa (`assets/images/logo/Isotipolinear.png`). Padrão identificado em
 * docs/reference/ouvidoria-audit.md, seções 1, 4 e 11 (seção `821402c` da referência).
 *
 * AJUSTE A PEDIDO DO CLIENTE (2026-10-28): o formulário próprio de manifestação (upload de
 * evidências, endpoint ouvidoria/ouvidoria-action.php) deixou de ser exibido publicamente nesta
 * coluna, substituído pelo vídeo do Dr. Luis Otávio (youtu.be/zNEnqSi1PmQ, embed
 * youtube-nocookie.com) e por um botão que abre o Google Forms oficial em nova aba. O endpoint
 * PHP (ouvidoria/ouvidoria-action.php) e o token CSRF em ouvidoria/index.php foram mantidos
 * intactos (histórico/validação já implementada), apenas sem um `<form>` nesta página para
 * consumi-los — nada foi apagado do backend.
 *
 * Espera, definidas pelo chamador antes do include:
 *
 *   $ombudsmanFormSection = [
 *       'heading'        => 'texto do heading full-width',
 *       'intro_html'     => 'HTML confiável do texto introdutório',
 *       'whatsapp_label' => 'texto visível do botão (ex.: "(67) 99110-3140")',
 *       'whatsapp_url'   => 'URL do WhatsApp exclusivo (vinda de config/company.php, nunca
 *                            hardcoded aqui nem duplicada no componente)',
 *       'video_embed_url' => 'URL do embed do YouTube (youtube-nocookie.com)',
 *       'form_url'       => 'URL do Google Forms externo',
 *       'decorative_image' => 'URL da marca d\'água (com BASE_URL) — ver comentário abaixo',
 *   ];
 *
 * `decorative_image` é recebido como URL (montada pelo chamador com BASE_URL), não hardcoded
 * aqui nem em CSS — mesma convenção já usada em `boxed-hero.php`/`contact-form-section.php` para
 * qualquer asset de imagem, para continuar funcionando corretamente se o site for publicado num
 * subdiretório (ver comentário de BASE_URL em config/bootstrap.php). Por ser uma marca d'água
 * puramente decorativa (mesmo asset de `/fale-conosco/`), é renderizada como `<img aria-hidden>`
 * posicionada via CSS (assets/css/ombudsman-form-section.css), não como `::before` (que não
 * aceitaria receber a URL via atributo HTML).
 */

$heading = $ombudsmanFormSection['heading'] ?? '';
$introHtml = $ombudsmanFormSection['intro_html'] ?? '';
$whatsappLabel = $ombudsmanFormSection['whatsapp_label'] ?? '';
$whatsappUrl = $ombudsmanFormSection['whatsapp_url'] ?? '';
$videoEmbedUrl = $ombudsmanFormSection['video_embed_url'] ?? '';
$formUrl = $ombudsmanFormSection['form_url'] ?? '';
$decorativeImage = $ombudsmanFormSection['decorative_image'] ?? '';
?>
<section class="ombudsman-form-section">
    <?php if ($decorativeImage !== ''): ?>
    <img class="ombudsman-form-section__watermark" src="<?= htmlspecialchars($decorativeImage, ENT_QUOTES, 'UTF-8') ?>" alt="" aria-hidden="true" loading="lazy">
    <?php endif; ?>
    <div class="ombudsman-form-section__inner">
        <h2 class="ombudsman-form-section__heading"><?= htmlspecialchars($heading, ENT_QUOTES, 'UTF-8') ?></h2>

        <div class="ombudsman-form-section__row">
            <div class="ombudsman-form-section__intro-col">
                <div class="ombudsman-form-section__intro"><?= $introHtml ?></div>
                <?php if ($whatsappUrl !== ''): ?>
                <a class="btn btn--pill-outline ombudsman-form-section__whatsapp-btn" href="<?= htmlspecialchars($whatsappUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" aria-label="Canal exclusivo da Ouvidoria pelo WhatsApp: <?= htmlspecialchars($whatsappLabel, ENT_QUOTES, 'UTF-8') ?>">
                    <svg class="ombudsman-form-section__whatsapp-icon" viewBox="0 0 448 512" aria-hidden="true">
                        <path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/>
                    </svg>
                    <?= htmlspecialchars($whatsappLabel, ENT_QUOTES, 'UTF-8') ?>
                </a>
                <?php endif; ?>
            </div>

            <div class="ombudsman-form-section__form-col">
                <?php if ($videoEmbedUrl !== ''): ?>
                <div class="ombudsman-form-section__video">
                    <iframe src="<?= htmlspecialchars($videoEmbedUrl, ENT_QUOTES, 'UTF-8') ?>" title="Mensagem do Dr. Luis Otávio — Ouvidoria CT Price" loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                </div>
                <?php endif; ?>

                <?php if ($formUrl !== ''): ?>
                <a class="btn btn--pill-outline ombudsman-form-section__video-btn" href="<?= htmlspecialchars($formUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer">
                    Preencher o formulário
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
