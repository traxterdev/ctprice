-- database/migrations/2026_09_06_000002_create_clients_table.sql
--
-- Clientes exibidos no carrossel da Home/Sobre Nós/Informações e na grade de /clientes/ (fonte
-- única — antes desta sprint, config/clients.php; ver database/seed_clients_and_partners.php).
--
-- `logo_path`: caminho relativo à raiz pública do site (ex.:
-- "assets/images/clients/home-carousel/vitrine.jpg" para os registros migrados do config
-- estático, ou "assets/uploads/clients/<nome-aleatorio>.ext" para logos enviados pelo admin
-- depois desta sprint — ver includes/Uploads.php). Nunca uma URL absoluta, nunca com BASE_URL.
--
-- `site_url`: nullable — nenhum dos 82 registros atuais tem site próprio associado (o carrossel
-- da Home nunca foi clicável); campo preparado para uso futuro, não obrigatório.
--
-- `ordem`: preserva a ordem original de config/clients.php (0, 1, 2, ...) — usada pelo carrossel
-- da Home (ordem fixa) e ignorada de propósito pela grade de /clientes/, que já teria seu próprio
-- embaralhamento determinístico por dia (ver components/clients-grid-section.php, inalterado).

CREATE TABLE IF NOT EXISTS clients (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome VARCHAR(190) NOT NULL,
    logo_path VARCHAR(255) NOT NULL,
    -- 500, não 255: mesma margem usada em partners.url (ver aquela migration) — nenhum cliente
    -- atual usa este campo hoje, mas um site futuro poderia vir com querystring longa.
    site_url VARCHAR(500) NULL DEFAULT NULL,
    ordem INT NOT NULL DEFAULT 0,
    ativo TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    -- Evita duplicar o mesmo logo se o importador (database/seed_clients_and_partners.php) for
    -- executado mais de uma vez — não é uma regra de negócio, é a chave de idempotência da seed.
    UNIQUE KEY uq_clients_logo_path (logo_path),
    KEY idx_clients_ativo_ordem (ativo, ordem)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
