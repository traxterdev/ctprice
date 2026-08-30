<?php
/**
 * components/clients-carousel-section.php
 *
 * Carrossel de logos de clientes/parceiros da Home — 7ª seção de nível superior no DOM original
 * (posição confirmada por inspeção direta: entre "O que dizem nossos clientes" e "Por que nos
 * escolher?"). Apenas o carrossel — sem heading, eyebrow, divisor ou texto no original.
 *
 * Swiper (assets/vendor/swiper/) com configuração PRÓPRIA desta seção, medida diretamente no
 * `data-settings` do widget original ("Image Carousel"), NÃO reaproveitada do Hero nem de
 * Depoimentos: `autoplay:yes` (5000ms), `loop:yes` (`infinite`), `speed:500`,
 * `image_spacing_custom:20px` (idêntico em qualquer largura, confirmado). O número de slides
 * visíveis (`slides_to_show:10` no original) não é replicável literalmente porque o
 * comportamento responsivo do widget original vem de um cálculo interno do Elementor não
 * exposto no `data-settings` (confirmado: sem `slides_to_show_tablet`/`_mobile`) — reproduzido
 * aqui via breakpoints próprios do Swiper calibrados para o mesmo resultado medido diretamente
 * nos três viewports obrigatórios (≥1024px: 10: 768–1023px: 2; <768px: 1) — ver
 * assets/js/clients-carousel-init.js.
 *
 * CORREÇÃO INTENCIONAL DE DEFEITO CONHECIDO (categoria C, docs/architecture-proposal.md §2):
 * no original, cada logo escala apenas pela largura (`object-fit:fill` sem altura própria
 * definida), o que no mobile — onde o slide chega a ~350px de largura — faz os logos ficarem
 * enormes e, para logos com proporção diferente de ~1.68:1, visivelmente espremidos/alongados.
 * Aqui cada slide tem uma altura fixa (`assets/css/clients-carousel-section.css`) com
 * `object-fit:contain`, preservando a proporção original de cada logo e mantendo um tamanho
 * geral coerente em qualquer largura — sem alterar quantidade de logos visíveis, ordem, gaps,
 * autoplay ou velocidade.
 *
 * 3 imagens do carrossel original retornam 404 no site de referência (defeito conhecido
 * categoria C, já registrado em docs/reference/home-desktop-audit.md, seção 16: `mv.jpg`,
 * `modelo.jpg`, `logo_0020_Camada16.jpg`) — não baixadas nem reproduzidas; os 82 logos restantes
 * mantêm a ordem relativa exata do original.
 *
 * UI DO SLIDE (unificação de identidade visual com /clientes/): cada slide usa a mesma classe de
 * card `.logo-card` (aparência em assets/css/logo-card.css — fundo branco, borda sutil, sombra
 * leve, `border-radius`, hover) já aprovada na grade de clientes, para que os logos
 * tenham a mesma identidade visual em qualquer lugar do site. A DIMENSÃO do card aqui é mais
 * compacta que em /clientes/ (ver assets/css/clients-carousel-section.css) para caber na seção de
 * ~200px de altura sem crescer a seção nem reduzir os 10 logos visíveis em desktop — apenas
 * tamanho/padding mudam por contexto, não a identidade.
 *
 * ORDEM: `$clientLogos` chega deste componente EXATAMENTE como o chamador passou (nenhum sort/
 * usort/shuffle é aplicado aqui) — ao contrário de components/clients-grid-section.php, que
 * embaralha uma CÓPIA local (`$displayLogos`) apenas para a página `/clientes/`. A Home continua
 * exibindo a ordem original de `config/clients.php`.
 *
 * Espera, definida pelo chamador antes do include:
 *
 *   $clientLogos = [
 *       ['file' => 'nome-do-arquivo.ext', 'alt' => 'texto alternativo'],
 *       ...
 *   ];
 *
 * Cada arquivo é servido de assets/images/clients/home-carousel/ (BASE_URL montado aqui, não
 * pelo chamador, para manter os 82 registros de dados enxutos).
 */

if (!isset($clientLogos) || !is_array($clientLogos)) {
    $clientLogos = [];
}
?>
<section class="clients-carousel-section" aria-label="Clientes e parceiros">
    <div class="clients-carousel-section__inner">
        <div class="clients-carousel swiper">
            <div class="swiper-wrapper">
                <?php foreach ($clientLogos as $logo): ?>
                <div class="swiper-slide client-logo-slide logo-card">
                    <img
                        class="logo-card__img"
                        src="<?= BASE_URL ?>/assets/images/clients/home-carousel/<?= htmlspecialchars($logo['file'], ENT_QUOTES, 'UTF-8') ?>"
                        alt="<?= htmlspecialchars($logo['alt'], ENT_QUOTES, 'UTF-8') ?>"
                        loading="lazy"
                    >
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
