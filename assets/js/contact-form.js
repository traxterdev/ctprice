/**
 * assets/js/contact-form.js
 *
 * Envio assíncrono do formulário de `/fale-conosco/` (components/contact-form-section.php) para
 * fale-conosco/fale-conosco-action.php, com feedback inline. JavaScript puro, sem biblioteca.
 *
 * Progressive enhancement: se este script não rodar (JS desabilitado/falha de rede ao carregar o
 * arquivo), o <form> continua funcional via submissão HTML nativa (method="post",
 * action=endpoint) — o endpoint detecta a ausência do cabeçalho `X-Requested-With`/`Accept:
 * application/json` e responde com um redirecionamento (303) de volta para a página com
 * `?status=...`, que components/contact-form-section.php exibe como um banner estático. Ver
 * docs/reference/fale-conosco-audit.md, seção 8.
 *
 * Nunca exibe detalhes técnicos (stack trace, caminho de servidor, exceção) — só as mensagens
 * já preparadas pelo endpoint ou uma mensagem genérica de erro de conexão.
 */
(function () {
    'use strict';

    var form = document.querySelector('.contact-form');
    if (!form) {
        return;
    }

    var feedbackEl = form.querySelector('.contact-form__feedback');
    var submitBtn = form.querySelector('.contact-form__submit-btn');
    var submitLabel = submitBtn ? submitBtn.querySelector('.contact-form__submit-label') : null;
    var GENERIC_ERROR = 'Não foi possível enviar sua mensagem no momento. Tente novamente mais tarde ou use o WhatsApp.';

    // Guarda explícita contra envio concorrente (ex.: duplo clique/Enter repetido antes do botão
    // desabilitar visualmente) — além de `submitBtn.disabled`, que já impede a maior parte dos
    // casos, mas não é a única forma de disparar `submit` num formulário.
    var isSubmitting = false;

    function clearFieldErrors() {
        var errors = form.querySelectorAll('.contact-form__error');
        for (var i = 0; i < errors.length; i++) {
            errors[i].textContent = '';
        }
        var invalidFields = form.querySelectorAll('.contact-form__field--invalid');
        for (var j = 0; j < invalidFields.length; j++) {
            invalidFields[j].classList.remove('contact-form__field--invalid');
        }
    }

    function showFeedback(type, message) {
        if (!feedbackEl) {
            return;
        }
        feedbackEl.textContent = message;
        feedbackEl.classList.remove('contact-form__feedback--success', 'contact-form__feedback--error');
        feedbackEl.classList.add(type === 'success' ? 'contact-form__feedback--success' : 'contact-form__feedback--error');
        feedbackEl.hidden = false;
    }

    function showFieldError(fieldName, message) {
        var errorEl = form.querySelector('#cf-' + fieldName + '-error');
        var fieldWrap = form.querySelector('[data-field="' + fieldName + '"]');
        if (errorEl) {
            errorEl.textContent = message;
        }
        if (fieldWrap) {
            fieldWrap.classList.add('contact-form__field--invalid');
        }
    }

    function setSubmitting(isSubmitting) {
        if (submitBtn) {
            submitBtn.disabled = isSubmitting;
        }
        if (submitLabel) {
            submitLabel.textContent = isSubmitting ? 'Enviando…' : 'Enviar';
        }
        form.classList.toggle('contact-form--submitting', isSubmitting);
    }

    form.addEventListener('submit', function (event) {
        event.preventDefault();

        if (isSubmitting) {
            return;
        }
        isSubmitting = true;

        clearFieldErrors();
        if (feedbackEl) {
            feedbackEl.hidden = true;
        }
        setSubmitting(true);

        fetch(form.getAttribute('action'), {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new FormData(form)
        })
            .then(function (response) {
                return response.json().catch(function () {
                    return null;
                }).then(function (data) {
                    return { ok: response.ok, status: response.status, data: data };
                });
            })
            .then(function (result) {
                var data = result.data;

                if (result.ok && data && data.success) {
                    showFeedback('success', data.message || 'Mensagem enviada com sucesso!');
                    form.reset();
                    return;
                }

                if (result.status === 422 && data && data.errors) {
                    showFeedback('error', data.message || 'Verifique os campos destacados.');
                    Object.keys(data.errors).forEach(function (field) {
                        showFieldError(field, data.errors[field]);
                    });
                    return;
                }

                showFeedback('error', (data && data.message) || GENERIC_ERROR);
            })
            .catch(function () {
                showFeedback('error', 'Falha de conexão. Verifique sua internet e tente novamente.');
            })
            .finally(function () {
                isSubmitting = false;
                setSubmitting(false);
            });
    });
})();
