/**
 * assets/js/video-testimonials-lightbox.js
 *
 * Lightbox de vídeo dos depoimentos de `/depoimentos/`
 * (components/video-testimonials-section.php). JavaScript puro, sem biblioteca de lightbox —
 * substitui o modal do Elementor do site original.
 *
 * Carregamento sob demanda: nenhum <iframe> do YouTube existe no HTML da página. O iframe é
 * criado dinamicamente só quando o usuário clica em um botão de thumbnail, e é DESTRUÍDO
 * (elemento removido do DOM) ao fechar o modal — o vídeo para de tocar imediatamente, nenhum
 * áudio/vídeo continua invisível em segundo plano.
 *
 * Domínio: `youtube-nocookie.com` (modo de privacidade avançada do YouTube) — mesmo vídeo,
 * menos cookies de rastreamento antes de qualquer interação do usuário com o player.
 *
 * Foco: ao abrir, o foco vai para o botão de fechar do modal; ao fechar, o foco volta para o
 * botão de thumbnail que abriu o vídeo (guardado em `lastTrigger`). `Escape` e clique no backdrop
 * fecham o modal.
 */
(function () {
    'use strict';

    var modal = document.getElementById('video-testimonial-modal');
    if (!modal) {
        return;
    }

    var frame = document.getElementById('video-testimonial-modal-frame');
    var closeButtons = modal.querySelectorAll('[data-video-modal-close]');
    var triggers = document.querySelectorAll('.video-testimonial-card__thumb-btn');

    var lastTrigger = null;

    function openModal(trigger) {
        var videoId = trigger.getAttribute('data-video-id');
        if (!videoId) {
            return;
        }
        var videoList = trigger.getAttribute('data-video-list');
        var videoTitle = trigger.getAttribute('data-video-title') || 'Vídeo de depoimento';

        var src = 'https://www.youtube-nocookie.com/embed/' + encodeURIComponent(videoId) + '?autoplay=1&rel=0';
        if (videoList) {
            src += '&list=' + encodeURIComponent(videoList);
        }

        var iframe = document.createElement('iframe');
        iframe.setAttribute('src', src);
        iframe.setAttribute('title', videoTitle);
        iframe.setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture');
        iframe.setAttribute('allowfullscreen', '');
        iframe.setAttribute('referrerpolicy', 'strict-origin-when-cross-origin');
        frame.appendChild(iframe);

        lastTrigger = trigger;
        modal.hidden = false;
        // A classe vai no <html>, não no <body>: nesta página o elemento de rolagem real é
        // <html> (document.scrollingElement), então `overflow:hidden` só no body não bloqueia o
        // scroll — achado da validação final (ver assets/css/video-testimonials-section.css).
        document.documentElement.classList.add('video-testimonial-modal-open');
        document.addEventListener('keydown', onKeydown);

        var closeBtn = modal.querySelector('.video-testimonial-modal__close');
        if (closeBtn) {
            closeBtn.focus();
        }
    }

    function closeModal() {
        if (modal.hidden) {
            return;
        }
        modal.hidden = true;
        document.documentElement.classList.remove('video-testimonial-modal-open');
        document.removeEventListener('keydown', onKeydown);

        // Remove o iframe — para o vídeo imediatamente, não deixa tocando invisível.
        frame.innerHTML = '';

        if (lastTrigger) {
            lastTrigger.focus();
            lastTrigger = null;
        }
    }

    function onKeydown(event) {
        if (event.key === 'Escape' || event.key === 'Esc') {
            closeModal();
            return;
        }
        if (event.key === 'Tab') {
            // Manter o foco dentro do modal enquanto ele estiver aberto (só há um elemento
            // focável dentro do dialog — o botão de fechar — então Tab/Shift+Tab sempre volta
            // para ele, sem precisar de uma lista de elementos focáveis).
            event.preventDefault();
            var closeBtn = modal.querySelector('.video-testimonial-modal__close');
            if (closeBtn) {
                closeBtn.focus();
            }
        }
    }

    triggers.forEach(function (trigger) {
        trigger.addEventListener('click', function () {
            openModal(trigger);
        });
    });

    closeButtons.forEach(function (btn) {
        btn.addEventListener('click', closeModal);
    });
})();
