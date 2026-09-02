<?php
/**
 * config/blog-posts.php
 *
 * Fonte ÚNICA e compartilhada dos metadados dos 3 posts reais do blog — consumida por:
 *   - index.php (Home) e informacoes/index.php, via components/blog-section.php (cards de
 *     "Últimas notícias");
 *   - os 3 posts individuais (raiz do site, um diretório por slug), via blog/_post-template.php
 *     e components/related-posts.php (coluna de relacionados).
 *
 * Conteúdo transcrito literalmente de docs/reference/blog-posts-audit.md. Array estático simples
 * (mesmo espírito de config/jobs.php/config/video-testimonials.php) — não é um CMS.
 *
 * DATA/HORA CANÔNICA: cada post guarda um único valor (`published_at`, formato
 * 'AAAA-MM-DD HH:MM:SS') em vez de dois textos independentes. A auditoria encontrou a MESMA hora
 * exibida com formatação diferente entre a listagem (24h, já usado no config anterior) e o
 * cabeçalho do post original (12h AM/PM, ex. "5:01 pm") — em vez de reproduzir essa inconsistência
 * (mistura de idioma/formato "5:01 pm" dentro de uma página em português), a reconstrução usa um
 * único formato (24h, `ctprice_blog_post_date_text()`/`ctprice_blog_post_time_text()` abaixo) em
 * todos os contextos (card da listagem, cabeçalho do post, relacionados) — diferença consciente,
 * não uma correção de conteúdo editorial.
 *
 * `slug`: identificador real auditado (docs/reference/blog-posts-audit.md, seção 1) — usado para
 * (a) montar a URL pública (`url`, raiz do site, sem `/wp/`) e (b) localizar o arquivo de corpo do
 * artigo em `content/blog/{slug}.php`. O terceiro post preserva o slug histórico `hello-world`
 * (post de exemplo padrão do WordPress, nunca excluído no original, mas com conteúdo 100% real e
 * substituído — ver auditoria, seção 19) — NÃO renomeado, por instrução explícita.
 *
 * `url`: já aponta para o caminho público NOVO (raiz do site, sem `/wp/`) — os 3 posts originais
 * apontavam para `https://ctprice.com.br/wp/...`; essa dependência do WordPress foi removida
 * (docs/architecture-proposal.md, seção 10: "mesma slug, sem /wp/").
 *
 * `category`: usada SOMENTE nos cards de listagem (Home/Informações) — a auditoria confirmou que
 * o post individual original NUNCA exibe a categoria (seção 4), então o cabeçalho do artigo
 * reconstruído também não exibe.
 */

if (!function_exists('ctprice_blog_post_date_text')) {
    /** "agosto 2, 2024" — mesmo formato já usado no config anterior, agora derivado de `published_at`. */
    function ctprice_blog_post_date_text(string $publishedAt): string
    {
        static $meses = [
            1 => 'janeiro', 2 => 'fevereiro', 3 => 'março', 4 => 'abril',
            5 => 'maio', 6 => 'junho', 7 => 'julho', 8 => 'agosto',
            9 => 'setembro', 10 => 'outubro', 11 => 'novembro', 12 => 'dezembro',
        ];
        $dt = new DateTimeImmutable($publishedAt);

        return $meses[(int) $dt->format('n')] . ' ' . $dt->format('j') . ', ' . $dt->format('Y');
    }
}

if (!function_exists('ctprice_blog_post_time_text')) {
    /** "17:01" — 24h, mesmo padrão já usado no config anterior. */
    function ctprice_blog_post_time_text(string $publishedAt): string
    {
        return (new DateTimeImmutable($publishedAt))->format('H:i');
    }
}

// Nomes de variável prefixados (não `$blogPosts`/`$post` etc.) DE PROPÓSITO: `require` executa no
// escopo de quem inclui este arquivo, então uma variável genérica aqui vazaria e poderia
// sobrescrever uma variável de mesmo nome já em uso pelo chamador (achado real da validação de
// components/video-testimonials-section.php — ver docs/reference/depoimentos-final-validation.md).
$ctpriceBlogPostsRaw = [
    [
        'slug' => 'reforma-trabalhista-volta-a-pauta-do-stf-julgamento-acontece-neste-mes',
        'image' => BASE_URL . '/assets/images/blog/blog03-300x155.webp',
        'category' => 'FOLHA DE PAGAMENTO',
        'title' => 'Reforma trabalhista volta à pauta do STF; julgamento acontece neste mês',
        'excerpt' => 'Julgamento será retomado sobre a validade de contrato de trabalho intermitente.',
        'published_at' => '2024-08-02 17:01:00',
    ],
    [
        'slug' => 'receita-federal-e-correios-lancam-portal-de-compras-internacionais',
        'image' => BASE_URL . '/assets/images/blog/blog02-300x155.webp',
        'category' => 'INFORMATIVO',
        'title' => 'Receita Federal e Correios lançam portal de compras internacionais',
        'excerpt' => 'Ferramenta tem como objetivo auxiliar consumidores em questões de importação, desde o rastreamento até a prevenção de fraudes.',
        'published_at' => '2024-08-02 16:59:00',
    ],
    [
        // Slug histórico do WordPress ("hello-world" / post de exemplo padrão, nunca excluído) —
        // preservado exatamente, não renomeado. Conteúdo real, ver comentário acima e a auditoria.
        'slug' => 'hello-world',
        'image' => BASE_URL . '/assets/images/blog/blog01-300x155.webp',
        'category' => 'INFORMATIVO',
        'title' => 'Novo golpe mira em empreendedores e cria sites falsos que simulam a geração de documentos',
        'excerpt' => 'Receita Federal alerta empresários sobre os sites falsos e diz que já está tomando as medidas cabíveis para tirá-los do ar.',
        'published_at' => '2024-07-29 13:53:00',
    ],
];

$ctpriceBlogPosts = array_map(static function (array $post): array {
    $post['url'] = BASE_URL . '/' . $post['slug'] . '/';
    $post['date'] = ctprice_blog_post_date_text($post['published_at']);
    $post['time'] = ctprice_blog_post_time_text($post['published_at']);

    return $post;
}, $ctpriceBlogPostsRaw);
unset($ctpriceBlogPostsRaw);

$ctpriceBlogPostsResult = [
    'heading' => 'Últimas notícias',
    'posts' => $ctpriceBlogPosts,
];
unset($ctpriceBlogPosts);

return $ctpriceBlogPostsResult;
