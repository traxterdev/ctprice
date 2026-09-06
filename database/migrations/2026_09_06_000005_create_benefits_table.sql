-- database/migrations/2026_09_06_000005_create_benefits_table.sql
--
-- Benefícios de /trabalhe-conosco/#beneficios (fonte única — antes desta sprint,
-- config/benefits.php). `nome` substitui o antigo campo solto `alt` (mesmo texto real, nome de
-- campo consistente com `clients.nome`/`partners.nome` — o nome de cada benefício já é usado
-- como `alt` da imagem, mesma convenção da sprint anterior).
--
-- `imagem_path`: mesma convenção de clients.logo_path/partners.logo_path (caminho relativo à
-- raiz do site, sem BASE_URL) — ex.: "assets/images/pages/trabalhe-conosco/beneficios/ben01.png"
-- (migrado) ou "assets/uploads/benefits/<nome-aleatorio>.ext" (upload novo pelo admin).

CREATE TABLE IF NOT EXISTS benefits (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome VARCHAR(190) NOT NULL,
    imagem_path VARCHAR(255) NOT NULL,
    ordem INT NOT NULL DEFAULT 0,
    ativo TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_benefits_imagem_path (imagem_path),
    KEY idx_benefits_ativo_ordem (ativo, ordem)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
