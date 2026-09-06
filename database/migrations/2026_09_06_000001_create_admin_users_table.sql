-- database/migrations/2026_09_06_000001_create_admin_users_table.sql
--
-- Administradores do CMS. Um único perfil nesta sprint ("Administrador") — sem RBAC (ver
-- docs/cms.md). Senha SEMPRE como hash (password_hash()/password_verify() no PHP, nunca texto
-- puro). E-mail único (login).

CREATE TABLE IF NOT EXISTS admin_users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(190) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    ativo TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
    ultimo_login_em DATETIME NULL DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_admin_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
