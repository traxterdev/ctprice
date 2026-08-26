/**
 * assets/js/video-section.js
 *
 * Comportamento de "clique para reproduzir" do vídeo institucional (components/video-section.php):
 * ao clicar na capa/botão de reproduzir, a capa é removida e o vídeo começa a tocar.
 *
 * Sem autoplay (o vídeo só toca após interação do usuário, igual ao original) e sem controles
 * nativos após iniciar — comportamento medido no original (o vídeo, uma vez iniciado, não expõe
 * nenhum controle de pausa; ver components/video-section.php). Não é um comportamento
 * "corrigido" aqui, é reproduzido como está.
 */
(function () {
    'use strict';

    var player = document.getElementById('video-section-player');
    if (!player) {
        return;
    }

    var video = player.querySelector('.video-section__video');
    var cover = player.querySelector('.video-section__cover');

    function play() {
        cover.classList.add('is-hidden');
        video.play();
    }

    cover.addEventListener('click', play);
})();
