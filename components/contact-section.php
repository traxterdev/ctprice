<?php
/**
 * components/contact-section.php
 *
 * Seção "Quer receber um contato?" da Home, imediatamente após blog-section: full-width, fundo
 * verde-escuro, duas colunas lado a lado (caixa de convite ao WhatsApp + formulário de contato).
 *
 * Espera, definidas pelo chamador antes do include:
 *
 *   $contactHeading  (string) — título da caixa esquerda (H3)
 *   $contactText     (string) — texto da caixa esquerda; impresso sem escapar (conteúdo estático
 *                     confiável, definido em index.php) porque reproduz quebras de linha manuais
 *                     (<br>) presentes no HTML original — mesmo padrão já usado em outras seções
 *   $contactWhatsapp (string) — URL do link do ícone do WhatsApp (ex.: https://api.whatsapp.com/send?phone=...)
 *
 * Estrutura confirmada por inspeção direta: container flex row (30%/50%, ~20% de gap
 * proporcional — medido em 1440/900 e igual nos dois, portanto percentual, não px fixo),
 * empilha em max-width:767px (mesmo breakpoint de conteúdo das demais seções — confirmado
 * testando 767/768, diferente do breakpoint próprio de blog-section).
 *
 * Formulário: reproduzido apenas como marcação estática (Nome, E-mail, Telefone, Mensagem,
 * botão "Enviar"), fiel ao original visualmente. NÃO inclui reCAPTCHA nem processamento de
 * envio — o projeto ainda não tem backend definido (docs/architecture-proposal.md) e o campo de
 * reCAPTCHA do original ocupa apenas 1px de layout (v3, invisível), então omiti-lo não altera a
 * fidelidade visual. O <form> não tem `action` funcional até uma decisão de backend.
 *
 * Sem animação de entrada: nenhum data-settings de animação nos widgets desta seção (confirmado
 * por inspeção direta) — não usa assets/js/scroll-reveal.js. O ícone do WhatsApp tem uma
 * animação de HOVER no original (elementor-animation-bounce-in, biblioteca de animações do
 * Elementor) — reproduzida com uma keyframe CSS simples própria, sem carregar a biblioteca.
 *
 * Medições: docs/reference/home-desktop-audit.md e reinspeção direta via Chrome DevTools MCP em
 * 1440x900/900x1200/390x844 (ver relatório final).
 */
?>
<section class="contact-section">
    <div class="contact-section__container">
        <div class="contact-section__whatsapp-box">
            <h3 class="contact-section__heading"><?= htmlspecialchars($contactHeading ?? '', ENT_QUOTES, 'UTF-8') ?></h3>
            <p class="contact-section__text"><?= $contactText ?? '' ?></p>
            <a class="contact-section__whatsapp-icon" href="<?= htmlspecialchars($contactWhatsapp ?? '', ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" aria-label="Enviar mensagem no WhatsApp">
                <svg viewBox="0 0 448 512" aria-hidden="true">
                    <path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 55.3 81.2 55.3 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/>
                </svg>
            </a>
        </div>

        <div class="contact-section__form-box">
            <form class="contact-form">
                <div class="contact-form__field">
                    <label for="contact-name">Nome <span class="contact-form__required">*</span></label>
                    <input type="text" id="contact-name" name="name" placeholder="Nome" required>
                </div>
                <div class="contact-form__field">
                    <label for="contact-email">E-mail <span class="contact-form__required">*</span></label>
                    <input type="email" id="contact-email" name="email" placeholder="E-mail" required>
                </div>
                <div class="contact-form__field">
                    <label for="contact-phone">Telefone <span class="contact-form__required">*</span></label>
                    <input type="tel" id="contact-phone" name="phone" placeholder="Telefone" required>
                </div>
                <div class="contact-form__field">
                    <label for="contact-message">Mensagem <span class="contact-form__required">*</span></label>
                    <textarea id="contact-message" name="message" rows="4" placeholder="Mensagem" required></textarea>
                </div>
                <div class="contact-form__submit">
                    <button type="submit" class="btn btn--filled">Enviar</button>
                </div>
            </form>
        </div>
    </div>
</section>
