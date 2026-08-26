<?php
/**
 * components/blog-section.php
 *
 * Seção "Últimas notícias" da Home, imediatamente após why-choose-us-section: título (H2) +
 * divisor seguidos de uma grade de cards de post (thumbnail + badge de categoria + título +
 * resumo + "Leia mais" + data/hora).
 *
 * Espera, definidas pelo chamador antes do include:
 *
 *   $blogHeading (string) — título (H2)
 *   $blogPosts   (array)  — cada item: ['image' => URL, 'category' => ..., 'title' => ...,
 *                 'excerpt' => ..., 'url' => ..., 'date' => ..., 'time' => ...]
 *
 * Estrutura do original: widget Elementor "Posts" com breakpoints RESPONSIVOS PRÓPRIOS,
 * diferentes do breakpoint de conteúdo (767px) usado nas demais seções — confirmado por medição
 * direta: 3 colunas acima de 1024px, 2 colunas entre 768px e 1024px, 1 coluna a partir de 767px
 * (mesmos valores 1024/767 já usados em outros pontos do projeto — header.css e demais seções —
 * apenas usados aqui num segundo breakpoint próprio desta seção, não reaproveitando a lógica de
 * nenhuma outra).
 *
 * Links: no original, "Leia mais" e o título apontam para as URLs reais do post no site atual
 * (https://ctprice.com.br/wp/...). Não há defeito conhecido documentado para esses links (ao
 * contrário do CTA de services-section) e as páginas de post da nova arquitetura ainda não
 * existem — por isso os hrefs são reproduzidos exatamente como no original, para não introduzir
 * um 404 novo apontando para uma estrutura que ainda não foi construída.
 *
 * Sem animação de entrada: nenhum data-settings de animação encontrado no widget ou nos cards
 * (confirmado por inspeção direta) — não usa assets/js/scroll-reveal.js.
 *
 * Medições: docs/reference/home-desktop-audit.md e reinspeção direta via Chrome DevTools MCP em
 * 1440x900/900x1200/390x844 (ver relatório final).
 */

if (!isset($blogPosts) || !is_array($blogPosts)) {
    $blogPosts = [];
}
?>
<section class="blog-section">
    <div class="blog-section__container">
        <div class="blog-section__heading-block">
            <h2 class="blog-section__heading"><?= htmlspecialchars($blogHeading ?? '', ENT_QUOTES, 'UTF-8') ?></h2>
            <div class="blog-section__divider" role="presentation"></div>
        </div>

        <div class="blog-section__grid">
            <?php foreach ($blogPosts as $post): ?>
            <article class="blog-card">
                <a class="blog-card__thumb-link" href="<?= htmlspecialchars($post['url'], ENT_QUOTES, 'UTF-8') ?>" tabindex="-1">
                    <img class="blog-card__thumb" src="<?= htmlspecialchars($post['image'], ENT_QUOTES, 'UTF-8') ?>" alt="" loading="lazy" width="300" height="155">
                </a>
                <span class="blog-card__badge"><?= htmlspecialchars($post['category'], ENT_QUOTES, 'UTF-8') ?></span>
                <div class="blog-card__body">
                    <h3 class="blog-card__title">
                        <a href="<?= htmlspecialchars($post['url'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') ?></a>
                    </h3>
                    <div class="blog-card__excerpt">
                        <p><?= htmlspecialchars($post['excerpt'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <a class="blog-card__read-more" href="<?= htmlspecialchars($post['url'], ENT_QUOTES, 'UTF-8') ?>" aria-label="Leia mais sobre <?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') ?>" tabindex="-1">Leia mais »</a>
                </div>
                <div class="blog-card__meta">
                    <span class="blog-card__date"><?= htmlspecialchars($post['date'], ENT_QUOTES, 'UTF-8') ?></span>
                    <span class="blog-card__time"><?= htmlspecialchars($post['time'], ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
