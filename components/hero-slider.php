<?php
/**
 * components/hero-slider.php
 *
 * Carrossel de destaque (Hero) — componente reutilizável baseado em Swiper.
 *
 * Espera receber, definida pelo chamador antes do include, a variável:
 *
 *   $heroSlides = [ // mesmo formato retornado por HeroSlideRepository::activeOrdered()
 *       [
 *           'imagem_path' => 'caminho relativo (sem BASE_URL) da imagem de fundo do slide',
 *           'titulo'      => 'HTML já SANITIZADO (ctprice_sanitize_hero_title_html(), nunca
 *                             confiado bruto) — pode conter <br> e
 *                             <span class="hero-slide__highlight"> para os trechos em destaque',
 *           'texto'       => 'subtítulo/descrição opcional, texto puro, ou null/"" quando ausente',
 *           'botao_texto' => 'texto do botão, ou null/"" — sem isso, nenhum botão é renderizado',
 *           'botao_url'   => 'destino do botão (interno ou externo), ou null/""',
 *       ],
 *       ...
 *   ];
 *
 * AJUSTE (2026-09-22): fonte de dados passou de um array estático (index.php) para o MariaDB via
 * HeroSlideRepository — ver admin/hero/ para o CRUD. `imagem_path` chega SEM BASE_URL agora
 * (formato de banco, igual clients/partners/etc.); este componente prefixa o BASE_URL na
 * renderização. `titulo` continua HTML confiável (agora sanitizado no admin, não mais escrito
 * direto no código) — texto/botão são elementos novos, ausentes nos 4 slides originais e só
 * aparecem quando o admin preenche.
 *
 * Este arquivo só monta a marcação e as classes que o Swiper (assets/vendor/swiper/) espera
 * (swiper, swiper-wrapper, swiper-slide) — a instância é criada por assets/js/hero-init.js.
 * Nenhuma classe ou estrutura do Elementor foi copiada.
 *
 * Configuração e medições: docs/reference/home-desktop-audit.md (seção 12),
 * docs/reference/home-tablet-audit.md e docs/reference/home-mobile-audit.md (seção Hero).
 */

if (!isset($heroSlides) || !is_array($heroSlides)) {
    $heroSlides = [];
}
?>
<?php if ($heroSlides): ?>
<section class="hero-slider swiper" aria-label="Destaques CT Price" aria-roledescription="carrossel">
    <div class="swiper-wrapper">
        <?php foreach ($heroSlides as $slide): ?>
        <?php
            $imagemUrl = ($slide['imagem_path'] ?? '') !== '' ? BASE_URL . '/' . $slide['imagem_path'] : '';
            $texto = trim((string) ($slide['texto'] ?? ''));
            $botaoTexto = trim((string) ($slide['botao_texto'] ?? ''));
            $botaoUrl = trim((string) ($slide['botao_url'] ?? ''));
            $isExternalButton = $botaoUrl !== '' && preg_match('#^https?://#i', $botaoUrl) === 1;
        ?>
        <div
            class="swiper-slide hero-slide"
            style="background-image: url('<?= htmlspecialchars($imagemUrl, ENT_QUOTES, 'UTF-8') ?>');"
        >
            <div class="hero-slide__inner">
                <p class="hero-slide__text"><?= $slide['titulo'] ?? '' ?></p>
                <?php if ($texto !== ''): ?>
                <p class="hero-slide__subtitle"><?= nl2br(htmlspecialchars($texto, ENT_QUOTES, 'UTF-8')) ?></p>
                <?php endif; ?>
                <?php if ($botaoTexto !== '' && $botaoUrl !== ''): ?>
                <a class="hero-slide__cta btn btn--filled" href="<?= htmlspecialchars($botaoUrl, ENT_QUOTES, 'UTF-8') ?>" <?= $isExternalButton ? 'rel="noopener noreferrer"' : '' ?>>
                    <?= htmlspecialchars($botaoTexto, ENT_QUOTES, 'UTF-8') ?>
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>
