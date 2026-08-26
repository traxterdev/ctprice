<?php
/**
 * /sobre-nos/ — A CT Price
 *
 * Estrutura mínima do scaffold. Layout, conteúdo e estilos do site original ainda não
 * foram implementados nesta etapa.
 *
 * Ver docs/architecture-proposal.md e docs/reference/site-inventory.md.
 */
require __DIR__ . '/../config/bootstrap.php';

$internalHero = [
    'eyebrow' => 'confie na ct price',
    'title' => '<strong>Ética, agilidade, segurança</strong> nos processos e respeito ao cliente.',
    'image' => BASE_URL . '/assets/images/pages/sobre-nos/img01.jpg',
];

$historySection = [
    'image' => BASE_URL . '/assets/images/pages/sobre-nos/Sala-de-reunioes.jpg',
    'image_alt' => '',
    'content_html' => '<p>A<strong>&nbsp;CT PRICE</strong>&nbsp;iniciou suas atividades desejando inserir no mercado uma organização de serviços contábeis que passaria aos seus clientes os princípios para garantir segurança, fidelidade, atendimento aos preceitos legais e à ética profissional.<br><br>• Sempre atenta a novas tecnologias e as atualizações em todos os seus setores, nossa equipe conta com profissionais altamente qualificados e que são frequentemente treinados, no intuito de melhorar o atendimento aos nossos clientes em todas as áreas: contábil, fiscal, tributária, empresarial, rural e consultoria organizacional e financeira.<br><br>• Estamos aptos a atender prontamente as suas necessidades também quanto às modificações legais e tributárias em quaisquer áreas, seja federal, estadual ou municipal.</p><p>• Buscamos, incansavelmente, ouvir nossos clientes para criar soluções rápidas e efetivas, em sinergia com os nossos departamentos. Por isso, estamos sempre comprometidos com a qualidade e com a ética, para mantermos nosso atendimento em constante melhoria, sendo reconhecidos pelos nossos clientes por essa prontidão.&nbsp;</p>',
    'image_position' => 'left',
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sobre nós — CT Price</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/reset.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/fonts.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/variables.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/header.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/internal-hero.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/image-text-section.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/footer.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/whatsapp-button.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/cookie-banner.css">
</head>
<body>

<?php require __DIR__ . '/../includes/topbar.php'; ?>
<?php require __DIR__ . '/../includes/header.php'; ?>

<main>
    <?php require __DIR__ . '/../components/internal-hero.php'; ?>
    <?php $imageTextSection = $historySection; require __DIR__ . '/../components/image-text-section.php'; ?>

    <!-- TODO: demais seções da página "A CT Price" (ver docs/reference/sobre-nos-audit.md) -->
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
<?php require __DIR__ . '/../includes/cookie-banner.php'; ?>
<?php require __DIR__ . '/../includes/whatsapp-button.php'; ?>

<script src="<?= BASE_URL ?>/assets/js/header.js" defer></script>
<script src="<?= BASE_URL ?>/assets/js/cookie-banner.js" defer></script>
</body>
</html>
