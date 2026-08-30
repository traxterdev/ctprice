<?php
/**
 * components/ombudsman-form-section.php
 *
 * Bloco final de `/ouvidoria/`: heading + texto introdutório + botão de WhatsApp exclusivo da
 * Ouvidoria (esquerda) + formulário de manifestação com upload de evidências (direita), sobre
 * fundo sólido `--color-dark-teal` (#00222C) com marca d'água decorativa
 * (`assets/images/logo/Isotipolinear.png`, via `::before` em CSS — ver
 * assets/css/ombudsman-form-section.css). Padrão identificado em
 * docs/reference/ouvidoria-audit.md, seções 1, 4 e 11 (seção `821402c` da referência).
 *
 * NÃO reutiliza components/contact-form-section.php: campos diferentes (Contato/telefone e
 * Empresa obrigatórios aqui, upload de arquivo, nenhuma foto/logomarca sobreposta do mesmo jeito),
 * layout diferente (heading full-width + WhatsApp exclusivo em vez de foto), e processado por um
 * endpoint próprio (ouvidoria/ouvidoria-action.php) com regras de validação/segurança distintas
 * (rate limit e token CSRF em chaves de sessão próprias, nunca compartilhadas com Fale Conosco) —
 * ver docs/reference/ouvidoria-audit.md, seção 16. Reaproveita, sim, a MESMA filosofia de
 * segurança já validada em Fale Conosco (CSRF por sessão, honeypot, rate limit, validação
 * server-side, envio via endpoint PHP próprio sem WordPress/Elementor/admin-ajax.php).
 *
 * IDENTIFICAÇÃO OBRIGATÓRIA PRESERVADA (decisão desta fase, não corrigida por iniciativa
 * própria): Nome, Contato, E-mail e Empresa continuam obrigatórios, exatamente como no original
 * — não existe opção de manifestação anônima. "Possibilidade de manifestação anônima depende de
 * decisão formal da CT Price" (ver docs/reference/ouvidoria-final-validation.md quando existir).
 *
 * UPLOAD: cliente informa apenas os limites já aplicados pelo endpoint (PDF/JPG/PNG, até 3
 * arquivos, 5MB cada, 10MB no total) — a validação real e definitiva é sempre server-side (MIME
 * verificado via `finfo`, nunca a extensão/`type` informados pelo navegador). O texto de limites
 * é informativo, não uma promessa de UX avançada (sem barra de progresso, sem preview de
 * imagem).
 *
 * Espera, definidas pelo chamador antes do include:
 *
 *   $ombudsmanFormSection = [
 *       'heading'        => 'texto do heading full-width',
 *       'intro_html'     => 'HTML confiável do texto introdutório',
 *       'whatsapp_label' => 'texto visível do botão (ex.: "(67) 99110-3140")',
 *       'whatsapp_url'   => 'URL do WhatsApp exclusivo (vinda de config/company.php, nunca
 *                            hardcoded aqui nem duplicada no componente)',
 *       'form_action'    => 'URL do endpoint (ouvidoria/ouvidoria-action.php)',
 *       'csrf_token'     => 'token da sessão atual',
 *       'decorative_image' => 'URL da marca d\'água (com BASE_URL) — ver comentário abaixo',
 *       'status'         => null | ['type' => 'success'|'error', 'message' => '...'] — banner de
 *                           fallback sem JavaScript (ver ouvidoria/index.php e
 *                           ouvidoria/ouvidoria-action.php)
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
$formAction = $ombudsmanFormSection['form_action'] ?? '';
$csrfToken = $ombudsmanFormSection['csrf_token'] ?? '';
$decorativeImage = $ombudsmanFormSection['decorative_image'] ?? '';
$status = $ombudsmanFormSection['status'] ?? null;
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
                <?php if ($status): ?>
                <div class="ombudsman-form__banner ombudsman-form__banner--<?= htmlspecialchars($status['type'], ENT_QUOTES, 'UTF-8') ?>" role="status">
                    <?= htmlspecialchars($status['message'], ENT_QUOTES, 'UTF-8') ?>
                </div>
                <?php endif; ?>

                <form class="ombudsman-form" action="<?= htmlspecialchars($formAction, ENT_QUOTES, 'UTF-8') ?>" method="post" enctype="multipart/form-data" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                    <!-- Honeypot: campo isca invisível para humanos e leitores de tela (ver
                         ouvidoria/ouvidoria-action.php) — um humano nunca preenche isto. -->
                    <div class="ombudsman-form__hp" aria-hidden="true">
                        <label for="ouv-website">Deixe este campo em branco</label>
                        <input type="text" id="ouv-website" name="website" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="ombudsman-form__field" data-field="name">
                        <label for="ouv-name">Nome <span class="ombudsman-form__required" aria-hidden="true">*</span><span class="sr-only"> (obrigatório)</span></label>
                        <input type="text" id="ouv-name" name="name" autocomplete="name" maxlength="150" required aria-describedby="ouv-name-error">
                        <span class="ombudsman-form__error" id="ouv-name-error" role="alert"></span>
                    </div>

                    <div class="ombudsman-form__row-split">
                        <div class="ombudsman-form__field" data-field="contact">
                            <label for="ouv-contact">Contato <span class="ombudsman-form__required" aria-hidden="true">*</span><span class="sr-only"> (obrigatório)</span></label>
                            <input type="tel" id="ouv-contact" name="contact" autocomplete="tel" maxlength="30" required aria-describedby="ouv-contact-error">
                            <span class="ombudsman-form__error" id="ouv-contact-error" role="alert"></span>
                        </div>

                        <div class="ombudsman-form__field" data-field="email">
                            <label for="ouv-email">E-mail <span class="ombudsman-form__required" aria-hidden="true">*</span><span class="sr-only"> (obrigatório)</span></label>
                            <input type="email" id="ouv-email" name="email" autocomplete="email" maxlength="190" required aria-describedby="ouv-email-error">
                            <span class="ombudsman-form__error" id="ouv-email-error" role="alert"></span>
                        </div>
                    </div>

                    <div class="ombudsman-form__field" data-field="company">
                        <label for="ouv-company">Empresa <span class="ombudsman-form__required" aria-hidden="true">*</span><span class="sr-only"> (obrigatório)</span></label>
                        <input type="text" id="ouv-company" name="company" autocomplete="organization" maxlength="150" required aria-describedby="ouv-company-error">
                        <span class="ombudsman-form__error" id="ouv-company-error" role="alert"></span>
                    </div>

                    <div class="ombudsman-form__field" data-field="message">
                        <label for="ouv-message">Mensagem <span class="ombudsman-form__required" aria-hidden="true">*</span><span class="sr-only"> (obrigatório)</span></label>
                        <textarea id="ouv-message" name="message" rows="4" maxlength="5000" required aria-describedby="ouv-message-error" placeholder="Descreva sua manifestação"></textarea>
                        <span class="ombudsman-form__error" id="ouv-message-error" role="alert"></span>
                    </div>

                    <div class="ombudsman-form__field" data-field="attachments">
                        <label for="ouv-attachments">Anexar evidências (opcional)</label>
                        <input type="file" id="ouv-attachments" name="anexos[]" multiple accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png" aria-describedby="ouv-attachments-hint ouv-attachments-error">
                        <span class="ombudsman-form__hint" id="ouv-attachments-hint">Formatos aceitos: PDF, JPG ou PNG. Máximo de 3 arquivos, até 5&nbsp;MB cada (10&nbsp;MB no total).</span>
                        <span class="ombudsman-form__error" id="ouv-attachments-error" role="alert"></span>
                    </div>

                    <div class="ombudsman-form__feedback" role="status" aria-live="polite" hidden></div>

                    <button class="ombudsman-form__submit-btn" type="submit">
                        <span class="ombudsman-form__submit-label">Enviar</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
