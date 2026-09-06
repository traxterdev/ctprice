-- database/migrations/2026_09_06_000003_create_partners_table.sql
--
-- Itens de /parcerias/ — DUAS categorias reais e semanticamente distintas, preservadas de
-- config/partners.php (ver seu comentário de topo): "tools" (acessos/portais de sistemas usados
-- pelos clientes da CT Price) e "companies" (parceiros de negócio de fato). ENUM porque são
-- exatamente 2 grupos conhecidos e estáveis — não uma taxonomia genérica.
--
-- `logo_path`: mesma convenção de clients.logo_path (caminho relativo à raiz, sem BASE_URL) —
-- ex.: "assets/images/partners/companies/logo-cfc.png" (migrado) ou
-- "assets/uploads/partners/<nome-aleatorio>.ext" (upload novo pelo admin).
--
-- `url`: nullable — preserva o caso real "Auditto" (config/partners.php), o único item sem link
-- no site original; components/logo-grid-section.php já trata ausência de url como card não
-- clicável (comportamento inalterado).

CREATE TABLE IF NOT EXISTS partners (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome VARCHAR(190) NOT NULL,
    categoria ENUM('tools', 'companies') NOT NULL,
    logo_path VARCHAR(255) NOT NULL,
    -- 500, não 255: vários portais/sistemas de terceiros usados pela CT Price geram URLs de
    -- autenticação (OAuth/SSO) com querystring longa — medido no config atual: até 390
    -- caracteres (ver config/partners.php, item "Sindicatos e Acordos Coletivos"/Ineditta).
    url VARCHAR(500) NULL DEFAULT NULL,
    ordem INT NOT NULL DEFAULT 0,
    ativo TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    -- Chave de idempotência da seed (database/seed_clients_and_partners.php) — o mesmo arquivo de
    -- logo pode em tese repetir entre categorias (não ocorre hoje), por isso o par
    -- (categoria, logo_path) é único, não logo_path isolado.
    UNIQUE KEY uq_partners_categoria_logo_path (categoria, logo_path),
    KEY idx_partners_categoria_ativo_ordem (categoria, ativo, ordem)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
