/**
 * assets/js/ouvidoria-form.js
 *
 * Envio assíncrono do formulário de `/ouvidoria/` (components/ombudsman-form-section.php) para
 * ouvidoria/ouvidoria-action.php, com feedback inline. JavaScript puro, sem biblioteca — mesma
 * estratégia de assets/js/contact-form.js (fetch + JSON, fallback de formulário nativo), mas
 * usando `FormData` do próprio `<form>` (necessário para o upload de anexos via
 * `multipart/form-data`, que `contact-form.js` não precisa lidar). Não é uma generalização de
 * `contact-form.js` — arquivo próprio, por instrução explícita da tarefa de implementação.
 *
 * Progressive enhancement: se este script não rodar, o <form> continua funcional via submissão
 * HTML nativa (method="post", enctype="multipart/form-data") — o endpoint detecta a ausência do
 * cabeçalho `X-Requested-With`/`Accept: application/json` e responde com um redirecionamento
 * (303) de volta para a página com `?status=...`, exibido como banner estático por
 * ouvidoria/index.php.
 *
 * Nunca exibe detalhes técnicos — só as mensagens já preparadas pelo endpoint ou uma mensagem
 * genérica de erro de conexão.
 */
(function () {
    'use strict';

    var form = document.querySelector('.ombudsman-form');
    if (!form) {
        return;
    }

    var feedbackEl = form.querySelector('.ombudsman-form__feedback');
    var submitBtn = form.querySelector('.ombudsman-form__submit-btn');
    var submitLabel = submitBtn ? submitBtn.querySelector('.ombudsman-form__submit-label') : null;
    var GENERIC_ERROR = 'Não foi possível enviar sua manifestação no momento. Tente novamente mais tarde ou use o WhatsApp exclusivo.';

    // Guarda explícita contra envio concorrente (duplo clique/Enter repetido antes do botão
    // desabilitar visualmente) — mesmo cuidado já validado em contact-form.js.
    var isSubmitting = false;

    function clearFieldErrors() {
        var errors = form.querySelectorAll('.ombudsman-form__error');
        for (var i = 0; i < errors.length; i++) {
            errors[i].textContent = '';
        }
        var invalidFields = form.querySelectorAll('.ombudsman-form__field--invalid');
        for (var j = 0; j < invalidFields.length; j++) {
            invalidFields[j].classList.remove('ombudsman-form__field--invalid');
        }
    }

    function showFeedback(type, message) {
        if (!feedbackEl) {
            return;
        }
        feedbackEl.textContent = message;
        feedbackEl.classList.remove('ombudsman-form__feedback--success', 'ombudsman-form__feedback--error');
        feedbackEl.classList.add(type === 'success' ? 'ombudsman-form__feedback--success' : 'ombudsman-form__feedback--error');
        feedbackEl.hidden = false;
    }

    function showFieldError(fieldName, message) {
        var errorEl = form.querySelector('#ouv-' + fieldName + '-error');
        var fieldWrap = form.querySelector('[data-field="' + fieldName + '"]');
        if (errorEl) {
            errorEl.textContent = message;
        }
        if (fieldWrap) {
            fieldWrap.classList.add('ombudsman-form__field--invalid');
        }
    }

    function setSubmitting(submitting) {
        if (submitBtn) {
            submitBtn.disabled = submitting;
        }
        if (submitLabel) {
            submitLabel.textContent = submitting ? 'Enviando…' : 'Enviar';
        }
        form.classList.toggle('ombudsman-form--submitting', submitting);
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

        // FormData a partir do próprio <form> já inclui o(s) arquivo(s) do campo de upload no
        // formato multipart/form-data correto — não é preciso montá-lo manualmente.
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
                    showFeedback('success', data.message || 'Manifestação enviada com sucesso!');
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
