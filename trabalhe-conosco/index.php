<?php
/**
 * /trabalhe-conosco/ — Trabalhe Conosco
 *
 * Estrutura, medições e decisões documentadas em docs/reference/trabalhe-conosco-audit.md.
 *
 * Reconstrução com melhorias deliberadas de arquitetura/UX sobre o original (ver comentário de
 * components/jobs-section.php e components/benefits-grid-section.php para o detalhe de cada
 * uma): sem os 4 containers vazios usados só como espaçador (resolvido com CSS normal), sem o
 * popup Elementor de candidatura (candidatura direciona para o sistema oficial de recrutamento
 * já existente), sem os benefícios soltos/desalinhados (cards com a mesma identidade visual já
 * aprovada em assets/css/logo-card.css).
 */
require __DIR__ . '/../config/bootstrap.php';

$boxedHero = [
    'eyebrow' => 'trabalhe conosco',
    'title' => 'Veja as vagas Disponíveis',
    'image' => BASE_URL . '/assets/images/pages/informacoes/informacoes.jpg',
    'background_position' => '0% 0%',
];

// Vagas — vêm do banco via JobRepository (sprint CMS 02; antes, config/jobs.php). O destino de
// candidatura continua exclusivamente de config/company.php (nunca duplicado em `jobs` nem
// hardcoded aqui) — ver components/jobs-section.php.
require_once __DIR__ . '/../repositories/JobRepository.php';
try {
    $jobsList = (new JobRepository())->allActive();
} catch (Throwable $e) {
    error_log('CT Price [Trabalhe Conosco]: falha ao carregar vagas do banco — ' . $e->getMessage());
    $jobsList = [];
}
$jobsSection = [
    'jobs' => $jobsList,
    'apply_url' => $company['sistemas_externos']['recrutamento'] ?? '',
];

// Benefícios — conteúdo estático em config/benefits.php. `id` aqui vira a âncora real
// (id="beneficios") na faixa de título, mesmo destino já usado por config/menu.php
// ('/trabalhe-conosco/#beneficios').
$benefitsTitleBand = [
    'title' => 'Nossos Benefícios',
    'font_size' => 32,
    'font_weight' => 700,
    'height' => 'auto',
    'container_max_width' => 1140,
    'gradient_stops' => ['15%', '90%'],
    'id' => 'beneficios',
];

// Benefícios — vêm do banco via BenefitRepository (sprint CMS 02; antes, config/benefits.php).
require_once __DIR__ . '/../repositories/BenefitRepository.php';
try {
    $benefitsItems = (new BenefitRepository())->allActive();
} catch (Throwable $e) {
    error_log('CT Price [Trabalhe Conosco]: falha ao carregar benefícios do banco — ' . $e->getMessage());
    $benefitsItems = [];
}
$benefitsGridSection = [
    'items' => $benefitsItems,
];

$pageMeta = [
    'title' => 'Trabalhe Conosco — CT Price',
    'description' => 'Veja as vagas disponíveis na CT Price, candidate-se pelo nosso sistema de recrutamento e conheça os benefícios oferecidos à equipe.',
    'canonical_path' => '/trabalhe-conosco/',
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php require __DIR__ . '/../includes/seo-head.php'; ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/reset.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/fonts.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/variables.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/header.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/boxed-hero.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/jobs-section.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/section-title-band.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/logo-card.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/benefits-grid-section.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/footer.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/whatsapp-button.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/cookie-banner.css">
</head>
<body>

<?php require __DIR__ . '/../includes/topbar.php'; ?>
<?php require __DIR__ . '/../includes/header.php'; ?>

<main>
    <?php require __DIR__ . '/../components/boxed-hero.php'; ?>
    <?php require __DIR__ . '/../components/jobs-section.php'; ?>
    <?php $sectionTitleBand = $benefitsTitleBand; require __DIR__ . '/../components/section-title-band.php'; ?>
    <?php require __DIR__ . '/../components/benefits-grid-section.php'; ?>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
<?php require __DIR__ . '/../includes/cookie-banner.php'; ?>
<?php require __DIR__ . '/../includes/whatsapp-button.php'; ?>

<script src="<?= BASE_URL ?>/assets/js/header.js" defer></script>
<script src="<?= BASE_URL ?>/assets/js/cookie-banner.js" defer></script>
</body>
</html>
