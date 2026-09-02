<?php
/**
 * components/article-content-section.php
 *
 * Corpo do artigo + coluna de relacionados — layout medido em docs/reference/blog-posts-audit.md,
 * seção 5: 2 colunas (conteúdo ~66% / relacionados ~34%, proporção do original — 827px/413px em
 * 1440px), empilhando em coluna única no breakpoint de conteúdo já usado em todo o site (767px).
 *
 * CONTAINER: 1140px (não os ~1240px do original) — mesmo valor já usado em toda página deste
 * projeto (boxed-hero, video-testimonials-section, restricted-access-section etc.), por
 * consistência entre páginas. Diferença consciente, documentada aqui e no relatório de
 * implementação — não muda a proporção 2 colunas nem o conteúdo.
 *
 * DATA SEM LINK (correção de defeito conhecido, categoria C): o original faz da data um link para
 * um arquivo por data do WordPress (`/wp/AAAA/MM/DD/`) que a auditoria confirmou vazio/sem
 * conteúdo real (200, mas nenhum post listado). Aqui a data é texto simples, sem link.
 *
 * COMPARTILHAMENTO: reimplementado como links reais (`<a target="_blank" rel="noopener
 * noreferrer">`, URLs públicas e bem conhecidas de cada rede — não uma integração inventada) em
 * vez do widget Elementor original (`<div role="button">`, dependente do JS do Elementor Pro,
 * sem `<a>`/`<button>` reais — ver auditoria, seções 5 e 15). `target="_blank"` aqui é
 * intencional e diferente do padrão "mesma aba" usado no resto do site: compartilhar leva a um
 * domínio de terceiro completamente diferente (rede social), então abrir em nova aba preserva o
 * lugar do leitor no artigo — mesmo raciocínio já usado no link do Google Maps do footer global
 * (`target="_blank" rel="noopener"`, o único outro caso de link explicitamente externo do site).
 *
 * SEM `<h2>`/`<h3>` inventados no corpo do artigo: o HTML confiável de `body_html` já vem pronto
 * de content/blog/{slug}.php — nenhum heading extra é adicionado aqui só por "melhorar SEO".
 *
 * Espera, definidas pelo chamador antes do include:
 *
 *   $articleContentSection = [
 *       'date_text'  => 'ex.: "agosto 2, 2024"',
 *       'time_text'  => 'ex.: "17:01"',
 *       'body_html'  => 'HTML confiável (content/blog/{slug}.php), parágrafos/listas/links já prontos',
 *       'share'      => ['url' => 'URL absoluta do post (ctprice_absolute_url)', 'title' => 'título do post'],
 *       'related'    => ['current_slug' => ..., 'items' => ...], // repassado para components/related-posts.php
 *   ];
 */

$dateText = $articleContentSection['date_text'] ?? '';
$timeText = $articleContentSection['time_text'] ?? '';
$bodyHtml = $articleContentSection['body_html'] ?? '';
$shareUrl = $articleContentSection['share']['url'] ?? '';
$shareTitle = $articleContentSection['share']['title'] ?? '';
$relatedPosts = $articleContentSection['related'] ?? ['current_slug' => '', 'items' => []];

$shareTargets = [
    [
        'network' => 'Facebook',
        'href' => 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode($shareUrl),
        'icon' => '<path d="M22 12a10 10 0 1 0-11.56 9.88v-6.99H7.9V12h2.54V9.8c0-2.5 1.49-3.89 3.77-3.89 1.09 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.78l-.44 2.89h-2.34v6.99A10 10 0 0 0 22 12z"/>',
    ],
    [
        'network' => 'X (Twitter)',
        'href' => 'https://twitter.com/intent/tweet?url=' . rawurlencode($shareUrl) . '&text=' . rawurlencode($shareTitle),
        'icon' => '<path d="M18.9 3H21l-6.98 7.98L22.3 21H16l-4.94-6.46L5.4 21H3.28l7.46-8.53L2 3h6.46l4.46 5.9L18.9 3zm-1.1 16.17h1.17L7.27 4.75H6.02L17.8 19.17z"/>',
    ],
    [
        'network' => 'LinkedIn',
        'href' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . rawurlencode($shareUrl),
        'icon' => '<path d="M4.98 3.5a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5zM3 9h4v12H3zM9 9h3.8v1.64h.05c.53-.98 1.83-2 3.76-2 4.02 0 4.76 2.55 4.76 5.87V21h-4v-5.7c0-1.36-.02-3.1-1.9-3.1-1.9 0-2.19 1.47-2.19 3v5.8H9z"/>',
    ],
    [
        'network' => 'WhatsApp',
        'href' => 'https://api.whatsapp.com/send?text=' . rawurlencode($shareTitle . ' ' . $shareUrl),
        'icon' => '<path d="M12 2a10 10 0 0 0-8.6 15L2 22l5.15-1.35A10 10 0 1 0 12 2zm0 18.2a8.16 8.16 0 0 1-4.17-1.14l-.3-.18-3.06.8.82-2.98-.2-.31A8.2 8.2 0 1 1 12 20.2zm4.5-6.13c-.24-.12-1.44-.71-1.67-.8-.22-.08-.38-.12-.55.12-.16.24-.63.8-.77.96-.14.16-.28.18-.52.06-.24-.12-1-.37-1.9-1.17-.7-.62-1.18-1.4-1.31-1.64-.14-.24-.01-.37.1-.49.11-.11.24-.28.36-.42.12-.14.16-.24.24-.4.08-.16.04-.3-.02-.42-.06-.12-.55-1.32-.75-1.8-.2-.48-.4-.42-.55-.42h-.47c-.16 0-.42.06-.64.3-.22.24-.85.83-.85 2.02 0 1.2.87 2.35.99 2.51.12.16 1.71 2.6 4.14 3.65.58.25 1.03.4 1.38.51.58.18 1.11.16 1.53.1.47-.07 1.44-.59 1.64-1.15.2-.57.2-1.05.14-1.15-.06-.1-.22-.16-.46-.28z"/>',
    ],
];
?>
<section class="article-content-section">
    <div class="article-content-section__container">
        <article class="article-content-section__main">
            <div class="article-meta">
                <span class="article-meta__date"><?= htmlspecialchars($dateText, ENT_QUOTES, 'UTF-8') ?></span>
                <?php if ($timeText !== ''): ?>
                <span class="article-meta__time"><?= htmlspecialchars($timeText, ENT_QUOTES, 'UTF-8') ?></span>
                <?php endif; ?>

                <?php if ($shareUrl !== ''): ?>
                <div class="article-share" role="group" aria-label="Compartilhar este artigo">
                    <?php foreach ($shareTargets as $share): ?>
                    <a class="article-share__link" href="<?= htmlspecialchars($share['href'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" aria-label="Compartilhar no <?= htmlspecialchars($share['network'], ENT_QUOTES, 'UTF-8') ?>">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><?= $share['icon'] ?></svg>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <div class="article-body"><?= $bodyHtml ?></div>
        </article>

        <?php require __DIR__ . '/related-posts.php'; ?>
    </div>
</section>
