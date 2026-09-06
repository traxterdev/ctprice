<?php
/**
 * blog-post.php — raiz
 *
 * Ponto de entrada para posts do blog cadastrados no CMS (banco) SEM diretório físico próprio —
 * ver `.htaccess` (bloco "Roteamento de posts do blog") e docs/cms.md. Os 3 posts históricos
 * continuam com seu próprio diretório (ex.: /hello-world/index.php) e nunca passam por aqui.
 *
 * NÃO é um front controller genérico: o `.htaccess` só encaminha para cá quando NENHUM arquivo/
 * diretório físico responde pela URL — qualquer página real (institucional, asset, os 3 posts
 * antigos) continua sendo servida diretamente pelo Apache, sem tocar neste arquivo.
 *
 * `$_GET['slug']` já chega pré-filtrado pelo padrão da própria regra do `.htaccess`
 * (`[a-z0-9-]+`), mas é revalidado aqui mesmo assim — este arquivo nunca confia apenas na regra do
 * servidor (defesa em profundidade, mesmo padrão já usado no restante do projeto).
 */

require __DIR__ . '/config/bootstrap.php';

$postSlug = (string) ($_GET['slug'] ?? '');

if ($postSlug === '' || !preg_match('/^[a-z0-9]+(-[a-z0-9]+)*$/', $postSlug)) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    exit;
}

require __DIR__ . '/blog/_post-template.php';
