-- database/migrations/2026_09_06_000008_create_admin_login_attempts_table.sql
--
-- Rate limit PERSISTENTE do login administrativo (sprint 03) — o rate limit por sessão já
-- existente (includes/AdminAuth.php, backoff exponencial) continua ativo para o caso comum
-- (desestimula clique repetido na mesma aba), mas sozinho não resiste a uma aba anônima/nova
-- sessão, que zera o contador. Esta tabela registra CADA tentativa (sucesso ou falha), por e-mail
-- normalizado E por IP, para que o bloqueio sobreviva a uma sessão nova.
--
-- `email_normalizado`: sempre minúsculo/trim (nunca o valor bruto digitado) — usado só para
-- CONTAR tentativas, nunca para revelar se o e-mail existe (a mensagem ao usuário continua
-- genérica em qualquer cenário, ver includes/AdminAuth.php).
-- `ip`: `$_SERVER['REMOTE_ADDR']` — suporta IPv6 (VARCHAR(45)). Sem suporte a
-- `X-Forwarded-For`/proxy reverso nesta sprint (cabeçalho forjável pelo cliente; tratar isso
-- corretamente exige saber em que proxy confiar, decisão de infraestrutura de produção, fora do
-- escopo desta sprint — ver docs/cms.md).
-- Sem UPDATE nesta tabela — cada tentativa é uma linha nova (INSERT-only); limpeza de linhas
-- antigas é feita por `admin_login_prune_attempts()` (includes/AdminAuth.php), chamada a cada
-- tentativa nova, sem exigir cron.

CREATE TABLE IF NOT EXISTS admin_login_attempts (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    email_normalizado VARCHAR(190) NOT NULL,
    ip VARCHAR(45) NOT NULL,
    sucesso TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_admin_login_attempts_email (email_normalizado, created_at),
    KEY idx_admin_login_attempts_ip (ip, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
