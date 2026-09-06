<?php
/**
 * database/seed_editorial_content.php
 *
 * Importa os dados ESTÁTICOS atuais de Vagas, Benefícios, Depoimentos e Blog para o banco —
 * mesmo espírito de database/seed_clients_and_partners.php (sprint anterior).
 *
 * Idempotência:
 *   - `benefits` tem UNIQUE KEY em `imagem_path` (arquivo migrado nunca se repete) → `ON DUPLICATE
 *     KEY UPDATE`, igual à sprint anterior.
 *   - `blog_posts` tem UNIQUE KEY em `slug` → idem.
 *   - `jobs`/`video_testimonials` NÃO têm unique key de negócio artificial (título de vaga e
 *     nome+empresa de depoimento não são, de fato, identificadores únicos garantidos — ver
 *     comentário das migrations) — a idempotência aqui é feita em PHP: procura um registro
 *     equivalente antes de inserir; se existir, atualiza; senão, insere.
 *
 * Uso (CLI, a partir da raiz do projeto):
 *   php database/seed_editorial_content.php
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Este script só pode ser executado via linha de comando.');
}

require __DIR__ . '/../config/bootstrap.php';
require __DIR__ . '/../includes/HtmlSanitizer.php';
require __DIR__ . '/../repositories/JobRepository.php';
require __DIR__ . '/../repositories/BenefitRepository.php';
require __DIR__ . '/../repositories/TestimonialRepository.php';
require __DIR__ . '/../repositories/BlogPostRepository.php';

$pdo = Database::connection();

// --- Vagas (idempotência em PHP: por título) -------------------------------------------------
$jobsData = require __DIR__ . '/../config/jobs.php';
$jobRepo = new JobRepository();
$jobCount = 0;

foreach ($jobsData as $index => $job) {
    $titulo = $job['title'];
    $requisitos = JobRepository::fromListArray($job['requirements'] ?? []);
    $diferenciais = JobRepository::fromListArray($job['differentials'] ?? []);

    $existingId = $pdo->prepare('SELECT id FROM jobs WHERE titulo = :titulo');
    $existingId->execute(['titulo' => $titulo]);
    $id = $existingId->fetchColumn();

    if ($id !== false) {
        $jobRepo->update((int) $id, $titulo, $requisitos, $diferenciais ?: null);
    } else {
        $jobRepo->create($titulo, $requisitos, $diferenciais ?: null, $index);
    }
    $jobCount++;
}
echo "Vagas importadas/atualizadas: $jobCount\n";

// --- Benefícios (idempotência via UNIQUE KEY em imagem_path) ----------------------------------
$benefitsData = require __DIR__ . '/../config/benefits.php';
$stmtBenefit = $pdo->prepare(
    'INSERT INTO benefits (nome, imagem_path, ordem, ativo)
     VALUES (:nome, :imagem_path, :ordem, 1)
     ON DUPLICATE KEY UPDATE nome = VALUES(nome), ordem = VALUES(ordem), ativo = 1'
);
$benefitCount = 0;
foreach ($benefitsData as $index => $benefit) {
    $stmtBenefit->execute([
        'nome' => $benefit['alt'],
        'imagem_path' => 'assets/images/pages/trabalhe-conosco/beneficios/' . $benefit['image'],
        'ordem' => $index,
    ]);
    $benefitCount++;
}
echo "Benefícios importados/atualizados: $benefitCount\n";

// --- Depoimentos (idempotência em PHP: por nome+empresa) --------------------------------------
$testimonialsData = require __DIR__ . '/../config/video-testimonials.php';
$testimonialRepo = new TestimonialRepository();
$testimonialCount = 0;

foreach ($testimonialsData as $index => $t) {
    $fotoPath = 'assets/images/pages/depoimentos/people/' . $t['photo'];
    $thumbPath = 'assets/images/pages/depoimentos/thumbnails/' . $t['thumbnail'];

    $data = [
        'nome' => $t['name'],
        'empresa' => $t['company'],
        'depoimento' => $t['quote'],
        'video_id' => $t['video_id'],
        'video_list' => $t['video_list'] ?? null,
        'site_url' => $t['website_url'] ?? null,
        'instagram_url' => $t['instagram_url'] ?? null,
    ];

    $existingId = $pdo->prepare('SELECT id FROM video_testimonials WHERE nome = :nome AND empresa = :empresa');
    $existingId->execute(['nome' => $data['nome'], 'empresa' => $data['empresa']]);
    $id = $existingId->fetchColumn();

    if ($id !== false) {
        $testimonialRepo->update((int) $id, $data, $fotoPath, $thumbPath);
    } else {
        $testimonialRepo->create($data, $fotoPath, $thumbPath, $index);
    }
    $testimonialCount++;
}
echo "Depoimentos importados/atualizados: $testimonialCount\n";

// --- Blog (idempotência via UNIQUE KEY em slug) -----------------------------------------------
// require de config/blog-posts.php precisa de BASE_URL definido (já vem de config/bootstrap.php)
// e dos 3 arquivos de corpo em content/blog/{slug}.php.
$blogData = require __DIR__ . '/../config/blog-posts.php';
$blogRepo = new BlogPostRepository();
$blogCount = 0;

foreach ($blogData['posts'] as $post) {
    $bodyFile = __DIR__ . '/../content/blog/' . $post['slug'] . '.php';
    if (!is_file($bodyFile)) {
        fwrite(STDERR, "AVISO: corpo não encontrado para o slug '{$post['slug']}' — pulado.\n");
        continue;
    }
    $rawBodyHtml = require $bodyFile;
    $sanitizedBodyHtml = ctprice_sanitize_article_html($rawBodyHtml);

    $existing = $pdo->prepare('SELECT id FROM blog_posts WHERE slug = :slug');
    $existing->execute(['slug' => $post['slug']]);
    $id = $existing->fetchColumn();

    // 'image' já vem com BASE_URL concatenado (ver config/blog-posts.php) — removido aqui porque
    // blog_posts.imagem_path guarda caminho relativo, mesma convenção de clients/partners/benefits.
    $imagemPath = ltrim((string) preg_replace('#^' . preg_quote(BASE_URL, '#') . '#', '', $post['image']), '/');

    if ($id !== false) {
        $blogRepo->update(
            (int) $id, $post['title'], $post['slug'], $post['category'], $post['excerpt'],
            $imagemPath, $sanitizedBodyHtml, $post['published_at']
        );
    } else {
        $blogRepo->create(
            $post['title'], $post['slug'], $post['category'], $post['excerpt'],
            $imagemPath, $sanitizedBodyHtml, $post['published_at']
        );
    }
    $blogCount++;
}
echo "Posts do blog importados/atualizados: $blogCount\n";
