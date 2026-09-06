-- database/migrations/2026_09_06_000007_create_blog_posts_table.sql
--
-- Artigos do blog (fonte única — antes desta sprint, config/blog-posts.php + content/blog/*.php).
--
-- `categoria`: campo REAL do conteúdo atual (badge exibido nos cards de "Últimas notícias" —
-- components/blog-section.php), não estava na lista de sugestão da tarefa mas é exigido pelo
-- componente já existente ("FOLHA DE PAGAMENTO"/"INFORMATIVO" nos 3 posts atuais) — adicionado por
-- necessidade real, não inventado.
-- `imagem_path`: idem — a thumbnail do card (300x155) é conteúdo real, sempre exibida.
-- `body_html`: HTML já SANITIZADO antes de chegar ao banco (ver includes/HtmlSanitizer.php) —
-- nunca HTML bruto não tratado.
-- `published_at`: data/hora de publicação exibida (cabeçalho do post + cards) — também usada como
-- filtro público (`ativo = 1 AND published_at <= NOW()`), permitindo agendar um post para o
-- futuro sem lógica adicional de agendamento.
-- `ativo`: alternado por "Publicar"/"Despublicar" no admin — não remove o registro.
-- Sem `ordem`: a listagem pública é sempre cronológica (`published_at DESC`), como já era com
-- config/blog-posts.php — nenhuma reordenação manual faz sentido para um blog.

CREATE TABLE IF NOT EXISTS blog_posts (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    titulo VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    categoria VARCHAR(60) NOT NULL,
    excerpt VARCHAR(500) NOT NULL,
    imagem_path VARCHAR(255) NOT NULL,
    body_html LONGTEXT NOT NULL,
    published_at DATETIME NOT NULL,
    ativo TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_blog_posts_slug (slug),
    KEY idx_blog_posts_ativo_published_at (ativo, published_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
