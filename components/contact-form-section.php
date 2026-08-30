<?php
/**
 * components/contact-form-section.php
 *
 * Bloco principal de `/fale-conosco/`: duas colunas — foto institucional + logomarca decorativa
 * sobreposta à esquerda, texto introdutório + formulário de contato à direita, sobre fundo
 * `--color-dark-teal` (#00222C). Padrão identificado em docs/reference/fale-conosco-audit.md,
 * seção 2 (seção `9aaac0e` da referência).
 *
 * NÃO reutiliza components/image-text-section.php nem components/image-content-cta-section.php:
 * nenhum dos dois tem formulário, campo honeypot/CSRF ou logomarca sobreposta — forçar reuso
 * exigiria condicionais artificiais para um comportamento que só existe aqui. Componente próprio,
 * mesmo espírito de dados-como-array dos demais.
 *
 * DIFERENÇAS CONSCIENTES em relação à referência (WordPress), registradas como melhoria, não como
 * infidelidade — ver docs/reference/fale-conosco-final-validation.md quando existir:
 *   - a foto de fundo usa `background-size:cover` (preenche toda a coluna), não `contain` com
 *     bleed da cor sólida do fundo como no original — mesma foto, mesma proporção de colunas,
 *     sem a faixa de cor lisa aparecendo ao lado dela;
 *   - em `max-width:767px` a foto vira um bloco de altura fixa reduzida ACIMA do formulário (não
 *     atrás dele) — no original a foto ficava atrás dos campos "Nome"/"E-mail" nesse breakpoint,
 *     reduzindo o contraste dos labels; aqui os dois nunca se sobrepõem;
 *   - a logomarca decorativa (`Isotipolinear.png`) é omitida em `max-width:767px` — é puramente
 *     decorativa (sem função de conteúdo) e sua ausência simplifica a composição mobile sem perda
 *     de informação;
 *   - formulário com honeypot, token CSRF, `autocomplete`, indicação visual de campo obrigatório
 *     (não só cor), foco mais evidente, envio via endpoint PHP próprio (ver
 *     fale-conosco/fale-conosco-action.php) em vez de Elementor Forms/admin-ajax.php — ver
 *     seção 6 da auditoria.
 *
 * Espera, definidas pelo chamador antes do include:
 *
 *   $contactFormSection = [
 *       'photo'                 => 'URL da foto de fundo da coluna esquerda (com BASE_URL)',
 *       'decorative_image'      => 'URL da logomarca decorativa sobreposta (com BASE_URL)',
 *       'intro_html'            => 'HTML confiável do texto introdutório (definido pelo chamador)',
 *       'form_action'           => 'URL do endpoint de envio (com BASE_URL)',
 *       'csrf_token'            => 'token da sessão atual, gerado pelo chamador (fale-conosco/index.php)',
 *       'status'                => null | ['type' => 'success'|'error', 'message' => string]
 *                                  — banner de fallback sem JavaScript (ver §8 da auditoria:
 *                                  "continuar funcional... caso JavaScript falhe"), preenchido a
 *                                  partir de `?status=` após um redirecionamento do endpoint.
 *   ];
 */

$photo = $contactFormSection['photo'] ?? '';
$decorativeImage = $contactFormSection['decorative_image'] ?? '';
$introHtml = $contactFormSection['intro_html'] ?? '';
$formAction = $contactFormSection['form_action'] ?? '';
$csrfToken = $contactFormSection['csrf_token'] ?? '';
$status = $contactFormSection['status'] ?? null;
?>
<section class="contact-form-section">
    <div class="contact-form-section__inner">
        <div class="contact-form-section__photo-col" style="background-image: url('<?= htmlspecialchars($photo, ENT_QUOTES, 'UTF-8') ?>');">
            <?php if ($decorativeImage): ?>
            <img class="contact-form-section__decorative-img" src="<?= htmlspecialchars($decorativeImage, ENT_QUOTES, 'UTF-8') ?>" alt="" aria-hidden="true" loading="lazy">
            <?php endif; ?>
        </div>

        <div class="contact-form-section__text-col">
            <div class="contact-form-section__intro"><?= $introHtml ?></div>

            <?php if ($status): ?>
            <p class="contact-form__static-banner contact-form__static-banner--<?= htmlspecialchars($status['type'], ENT_QUOTES, 'UTF-8') ?>" role="status">
                <?= htmlspecialchars($status['message'], ENT_QUOTES, 'UTF-8') ?>
            </p>
            <?php endif; ?>

            <form class="contact-form" method="post" action="<?= htmlspecialchars($formAction, ENT_QUOTES, 'UTF-8') ?>" novalidate>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                <!-- Honeypot anti-spam: campo invisível para humanos, visível para bots que
                     preenchem todo campo de formulário indiscriminadamente. `aria-hidden` +
                     `tabindex="-1"` removem qualquer efeito para leitores de tela/teclado; o
                     nome do campo é genérico o bastante para não parecer óbvio a um bot simples. -->
                <div class="contact-form__hp" aria-hidden="true">
                    <label for="cf-website">Deixe este campo em branco</label>
                    <input type="text" id="cf-website" name="website" tabindex="-1" autocomplete="off">
                </div>

                <div class="contact-form__field" data-field="name">
                    <label for="cf-name">Nome</label>
                    <input type="text" id="cf-name" name="name" autocomplete="name" placeholder="Seu nome" maxlength="150" aria-describedby="cf-name-error">
                    <span class="contact-form__error" id="cf-name-error" role="alert"></span>
                </div>

                <div class="contact-form__field" data-field="email">
                    <label for="cf-email">
                        E-mail
                        <span class="contact-form__required" aria-hidden="true">*</span>
                        <span class="sr-only">(obrigatório)</span>
                    </label>
                    <input type="email" id="cf-email" name="email" required autocomplete="email" placeholder="seuemail@exemplo.com" maxlength="190" aria-describedby="cf-email-error">
                    <span class="contact-form__error" id="cf-email-error" role="alert"></span>
                </div>

                <div class="contact-form__field" data-field="company">
                    <label for="cf-company">Empresa</label>
                    <input type="text" id="cf-company" name="company" autocomplete="organization" placeholder="Nome da sua empresa" maxlength="150" aria-describedby="cf-company-error">
                    <span class="contact-form__error" id="cf-company-error" role="alert"></span>
                </div>

                <div class="contact-form__field" data-field="message">
                    <label for="cf-message">Mensagem</label>
                    <textarea id="cf-message" name="message" rows="4" placeholder="Informe como podemos te ajudar" maxlength="5000" aria-describedby="cf-message-error"></textarea>
                    <span class="contact-form__error" id="cf-message-error" role="alert"></span>
                </div>

                <div class="contact-form__feedback" role="status" aria-live="polite" hidden></div>

                <div class="contact-form__submit-wrap">
                    <button type="submit" class="contact-form__submit-btn">
                        <span class="contact-form__submit-label">Enviar</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
