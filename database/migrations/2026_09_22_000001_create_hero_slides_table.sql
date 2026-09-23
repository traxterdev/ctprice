-- database/migrations/2026_09_22_000001_create_hero_slides_table.sql
--
-- Slides do Hero/banner principal da Home — antes desta migration, conteúdo estático em
-- index.php ($heroSlides). MariaDB passa a ser a fonte canônica (ver database/seed_hero_slides.php
-- para a importação idempotente dos 4 slides atuais).
--
-- `titulo`: TEXT, não VARCHAR — guarda o texto principal do slide já SANITIZADO (ver
-- includes/HtmlSanitizer.php, ctprice_sanitize_hero_title_html()) antes de chegar ao banco, nunca
-- HTML bruto não tratado (mesmo espírito de blog_posts.body_html). Permite só `<br>` e
-- `<span class="hero-slide__highlight">` — o único HTML que os 4 slides atuais realmente usam
-- para destacar trechos da frase (ver components/hero-slider.php) — não é um campo de rich text
-- genérico.
-- `texto`: subtítulo/descrição opcional — NENHUM dos 4 slides atuais tem um, então nullable;
-- texto puro (sem HTML), escapado normalmente na renderização.
-- `botao_texto`/`botao_url`: opcionais — não existe botão no Hero atual; campo novo pedido
-- nesta tarefa. Sem botao_texto, nenhum botão é renderizado (ver components/hero-slider.php).
-- `imagem_path`: mesma convenção de clients.logo_path — caminho relativo à raiz, sem BASE_URL.
CREATE TABLE IF NOT EXISTS hero_slides (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    titulo TEXT NOT NULL,
    texto TEXT NULL DEFAULT NULL,
    imagem_path VARCHAR(255) NOT NULL,
    botao_texto VARCHAR(190) NULL DEFAULT NULL,
    botao_url VARCHAR(500) NULL DEFAULT NULL,
    ordem INT NOT NULL DEFAULT 0,
    ativo TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    -- Chave de idempotência da seed (database/seed_hero_slides.php) — mesmo padrão de
    -- clients.logo_path/partners.(categoria, logo_path).
    UNIQUE KEY uq_hero_slides_imagem_path (imagem_path),
    KEY idx_hero_slides_ativo_ordem (ativo, ordem)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
