-- database/migrations/2026_09_06_000006_create_video_testimonials_table.sql
--
-- Depoimentos em vídeo de /depoimentos/ (fonte única — antes desta sprint,
-- config/video-testimonials.php). Campos seguem exatamente os dados reais existentes — nenhuma
-- rede social além de site/Instagram foi inventada (o original não tem Facebook/LinkedIn/etc.
-- neste componente).
--
-- `foto_path`/`thumbnail_path`: dois campos distintos porque o componente público
-- (video-testimonials-section.php) os usa em dois elementos visuais diferentes (foto circular
-- 200x200 da pessoa vs. miniatura 1280x720 do vídeo) — hoje os 7 registros reais usam o MESMO
-- arquivo para os dois (mesma convenção do config antigo), mas o schema preserva a distinção real
-- do componente, não a colapsa.
-- `video_list`: nullable — só 1 dos 7 depoimentos reais tem playlist do YouTube na URL original.
-- `site_url`: nullable — Walter Ferreira Cruz não tem site próprio real (ver comentário original
-- de config/video-testimonials.php: o "site" do card apontava para o mesmo Instagram).

CREATE TABLE IF NOT EXISTS video_testimonials (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome VARCHAR(190) NOT NULL,
    empresa VARCHAR(190) NOT NULL,
    depoimento TEXT NOT NULL,
    video_id VARCHAR(50) NOT NULL,
    video_list VARCHAR(100) NULL DEFAULT NULL,
    foto_path VARCHAR(255) NOT NULL,
    thumbnail_path VARCHAR(255) NOT NULL,
    site_url VARCHAR(500) NULL DEFAULT NULL,
    instagram_url VARCHAR(500) NULL DEFAULT NULL,
    ordem INT NOT NULL DEFAULT 0,
    ativo TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_video_testimonials_ativo_ordem (ativo, ordem)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
