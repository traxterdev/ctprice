<?php
/**
 * components/related-posts.php
 *
 * Coluna de "outras notícias" ao lado do corpo do artigo — substitui o widget WordPress
 * `posts.classic` do original (docs/reference/blog-posts-audit.md, seções 5/11/17), que tinha 3
 * problemas reais corrigidos aqui:
 *   - 3 links redundantes por card (thumbnail + título + "Leia mais »", todos para a mesma URL) →
 *     aqui é UM único `<a>` por card, envolvendo thumbnail + título (nome acessível real, já que
 *     o texto do título está dentro do mesmo link — satisfaz "link da thumbnail com nome
 *     acessível" e "título clicável" ao mesmo tempo, sem duplicar o link);
 *   - `alt=""` em todas as thumbnails → aqui `alt` descreve o post relacionado;
 *   - nenhum heading identificando a seção → aqui um `<h2>` real ("Mais notícias"), coerente com a
 *     hierarquia H1 (artigo) → H2 (relacionados) — o original pulava direto para H3 sem H2 nenhum.
 *
 * Dados vêm de config/blog-posts.php (fonte única, mesma da Home/Informações) — nenhuma segunda
 * lista de posts é criada aqui. O post atual é excluído pelo `slug`; a ordem dos 2 restantes segue
 * a ordem natural do array (determinística, sem embaralhar).
 *
 * Espera, definidas pelo chamador antes do include:
 *
 *   $relatedPosts = [
 *       'current_slug' => 'slug do post atual, para excluir da lista',
 *       'items' => [ // config/blog-posts.php ['posts'], já resolvido (url/date/time incluídos)
 *           ['slug' => ..., 'title' => ..., 'excerpt' => ..., 'image' => ..., 'url' => ...,
 *            'date' => ...],
 *           ...
 *       ],
 *   ];
 */

$currentSlug = $relatedPosts['current_slug'] ?? '';
$allItems = $relatedPosts['items'] ?? [];
$items = array_values(array_filter($allItems, static function (array $post) use ($currentSlug): bool {
    return ($post['slug'] ?? '') !== $currentSlug;
}));
?>
<aside class="related-posts" aria-labelledby="related-posts-heading">
    <h2 id="related-posts-heading" class="related-posts__heading">Mais notícias</h2>
    <ul class="related-posts__list" role="list">
        <?php foreach ($items as $post): ?>
        <?php
            $title = $post['title'] ?? '';
            $excerpt = $post['excerpt'] ?? '';
            $imageUrl = $post['image'] ?? '';
            $url = $post['url'] ?? '#';
            $date = $post['date'] ?? '';
        ?>
        <li class="related-posts__item">
            <a class="related-posts__link" href="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>">
                <img class="related-posts__thumb" src="<?= htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8') ?>" alt="Notícia: <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>" loading="lazy" width="300" height="155">
                <span class="related-posts__body">
                    <span class="related-posts__title"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></span>
                    <span class="related-posts__date"><?= htmlspecialchars($date, ENT_QUOTES, 'UTF-8') ?></span>
                    <span class="related-posts__excerpt"><?= htmlspecialchars($excerpt, ENT_QUOTES, 'UTF-8') ?></span>
                </span>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>
</aside>
