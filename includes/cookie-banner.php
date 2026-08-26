<?php
/**
 * includes/cookie-banner.php
 *
 * Aviso de cookies global — mesmo banner em todas as páginas.
 *
 * Estrutura, texto, cores e medições reproduzidos por reinspeção direta do plugin
 * "Cookie Notice" (WordPress) em 1440x900/900x1200/390x844 — ver
 * docs/reference/home-desktop-audit.md (seção 19). Nenhum link para política de
 * privacidade/cookies existe no aviso original; nada foi inventado aqui.
 *
 * Esta etapa controla apenas exibição, escolha do usuário (aceitar/recusar) e
 * persistência dessa escolha (assets/js/cookie-banner.js). O projeto ainda não possui
 * nenhum script de analytics/marketing para carregar ou bloquear — o código não afirma
 * o contrário.
 *
 * Correção de acessibilidade (permitida sem alterar o visual, ver instruções da tarefa):
 * no original, o botão de fechar (o "X") tem aria-label="Não" mesmo executando a mesma
 * ação do botão "Ok" (data-cookie-set="accept") — um bug real do plugin de terceiros.
 * Aqui o rótulo do botão de fechar descreve a ação que ele realmente executa.
 */
?>
<div id="cookie-banner" class="cookie-banner" role="dialog" aria-label="Aviso de cookies" hidden>
    <div class="cookie-banner__container">
        <span class="cookie-banner__text">Nós utilizamos cookies para garantir que você tenha a melhor experiência em nosso site. Se você continua a usar este site, assumimos que você está satisfeito.</span>
        <span class="cookie-banner__buttons">
            <button type="button" id="cookie-banner-accept" class="cookie-banner__button" data-cookie-choice="accept">Ok</button>
            <button type="button" id="cookie-banner-refuse" class="cookie-banner__button" data-cookie-choice="refuse">Não</button>
        </span>
        <button type="button" id="cookie-banner-close" class="cookie-banner__close" data-cookie-choice="accept" aria-label="Fechar e aceitar"></button>
    </div>
</div>
