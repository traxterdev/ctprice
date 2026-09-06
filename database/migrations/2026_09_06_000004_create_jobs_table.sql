-- database/migrations/2026_09_06_000004_create_jobs_table.sql
--
-- Vagas exibidas em /trabalhe-conosco/ (fonte única — antes desta sprint, config/jobs.php; ver
-- database/seed_editorial_content.php). Campos seguem a estrutura REAL do conteúdo atual (não o
-- exemplo genérico "titulo/descricao" da tarefa): cada vaga tem pré-requisitos e diferenciais como
-- LISTAS (renderizadas como <ul><li> por components/jobs-section.php), não um texto único.
--
-- `requisitos`/`diferenciais`: um item por linha (texto simples, sem HTML) — convertidos em lista
-- na leitura (JobRepository), mesma convenção usada no admin (textarea "um item por linha").
-- `diferenciais` é NULLABLE porque é conceitualmente opcional (as 3 vagas atuais têm as duas
-- listas preenchidas, mas o componente já trata a ausência de qualquer uma das duas).

CREATE TABLE IF NOT EXISTS jobs (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    titulo VARCHAR(190) NOT NULL,
    requisitos TEXT NOT NULL,
    diferenciais TEXT NULL DEFAULT NULL,
    ordem INT NOT NULL DEFAULT 0,
    ativo TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_jobs_ativo_ordem (ativo, ordem)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
