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

$dedicationSection = [
    'image' => BASE_URL . '/assets/images/pages/sobre-nos/01-1024x684.jpg',
    'image_alt' => '',
    'heading_html' => '<span style="color:#10E36B;font-weight:bold">Dedicação</span> aos resultados e <span style="color:#10E36B;font-weight:bold">Compromisso</span> com nossos clientes.',
    'content_html' => '<p>Temos um <strong><span style="color:#10E36B">compromisso</span></strong> com os resultados excepcionais e total dedicação ao sucesso dos <strong><span style="color:#10E36B">nossos clientes</span></strong>.</p><p><strong><span style="color:#10E36B">Trabalhamos incansavelmente</span></strong> para atender suas necessidades e superar expectativas, garantindo que cada detalhe seja tratado com o <strong><span style="color:#10E36B">máximo cuidado e eficiência</span></strong>.</p>',
    'cta_label' => 'Fale Conosco',
    'cta_url' => '/fale-conosco/',
];

// Carrossel de logos de clientes/parceiros — mesmo carrossel da Home (confirmado idêntico por
// inspeção direta: mesmo data-settings do widget, mesma altura/background/container). Fonte
// compartilhada em config/clients.php (não duplicar a lista aqui).
$clientLogos = require __DIR__ . '/../config/clients.php';

$missionVisionValuesSection = [
    'heading' => 'Deixe a contabilidade nas mãos de quem entende!',
    'items' => [
        [
            'icon_svg' => '<svg viewBox="0 0 512 512" aria-hidden="true"><path d="M495 225.06l-17.22 1.08c-5.27-39.49-20.79-75.64-43.86-105.84l12.95-11.43c6.92-6.11 7.25-16.79.73-23.31L426.44 64.4c-6.53-6.53-17.21-6.19-23.31.73L391.7 78.07c-30.2-23.06-66.35-38.58-105.83-43.86L286.94 17c.58-9.21-6.74-17-15.97-17h-29.94c-9.23 0-16.54 7.79-15.97 17l1.08 17.22c-39.49 5.27-75.64 20.79-105.83 43.86l-11.43-12.95c-6.11-6.92-16.79-7.25-23.31-.73L64.4 85.56c-6.53 6.53-6.19 17.21.73 23.31l12.95 11.43c-23.06 30.2-38.58 66.35-43.86 105.84L17 225.06c-9.21-.58-17 6.74-17 15.97v29.94c0 9.23 7.79 16.54 17 15.97l17.22-1.08c5.27 39.49 20.79 75.64 43.86 105.83l-12.95 11.43c-6.92 6.11-7.25 16.79-.73 23.31l21.17 21.17c6.53 6.53 17.21 6.19 23.31-.73l11.43-12.95c30.2 23.06 66.35 38.58 105.84 43.86L225.06 495c-.58 9.21 6.74 17 15.97 17h29.94c9.23 0 16.54-7.79 15.97-17l-1.08-17.22c39.49-5.27 75.64-20.79 105.84-43.86l11.43 12.95c6.11 6.92 16.79 7.25 23.31.73l21.17-21.17c6.53-6.53 6.19-17.21-.73-23.31l-12.95-11.43c23.06-30.2 38.58-66.35 43.86-105.83l17.22 1.08c9.21.58 17-6.74 17-15.97v-29.94c-.01-9.23-7.8-16.54-17.01-15.97zM281.84 98.61c24.81 4.07 47.63 13.66 67.23 27.78l-42.62 48.29c-8.73-5.44-18.32-9.54-28.62-11.95l4.01-64.12zm-51.68 0l4.01 64.12c-10.29 2.41-19.89 6.52-28.62 11.95l-42.62-48.29c19.6-14.12 42.42-23.71 67.23-27.78zm-103.77 64.33l48.3 42.61c-5.44 8.73-9.54 18.33-11.96 28.62l-64.12-4.01c4.07-24.81 13.66-47.62 27.78-67.22zm-27.78 118.9l64.12-4.01c2.41 10.29 6.52 19.89 11.95 28.62l-48.29 42.62c-14.12-19.6-23.71-42.42-27.78-67.23zm131.55 131.55c-24.81-4.07-47.63-13.66-67.23-27.78l42.61-48.3c8.73 5.44 18.33 9.54 28.62 11.96l-4 64.12zM256 288c-17.67 0-32-14.33-32-32s14.33-32 32-32 32 14.33 32 32-14.33 32-32 32zm25.84 125.39l-4.01-64.12c10.29-2.41 19.89-6.52 28.62-11.96l42.61 48.3c-19.6 14.12-42.41 23.71-67.22 27.78zm103.77-64.33l-48.29-42.62c5.44-8.73 9.54-18.32 11.95-28.62l64.12 4.01c-4.07 24.82-13.66 47.64-27.78 67.23zm-36.34-114.89c-2.41-10.29-6.52-19.89-11.96-28.62l48.3-42.61c14.12 19.6 23.71 42.42 27.78 67.23l-64.12 4z"/></svg>',
            'title' => 'Nossa Missão',
            'content' => 'Nossa razão de ser está pautada em uma organização de serviços contábeis que promove aos nossos clientes segurança e fidelidade, baseado aos preceitos legais exigidos e também à ética profissional.',
        ],
        [
            'icon_svg' => '<svg viewBox="0 0 576 512" aria-hidden="true"><path d="M288 144a110.94 110.94 0 0 0-31.24 5 55.4 55.4 0 0 1 7.24 27 56 56 0 0 1-56 56 55.4 55.4 0 0 1-27-7.24A111.71 111.71 0 1 0 288 144zm284.52 97.4C518.29 135.59 410.93 64 288 64S57.68 135.64 3.48 241.41a32.35 32.35 0 0 0 0 29.19C57.71 376.41 165.07 448 288 448s230.32-71.64 284.52-177.41a32.35 32.35 0 0 0 0-29.19zM288 400c-98.65 0-189.09-55-237.93-144C98.91 167 189.34 112 288 112s189.09 55 237.93 144C477.1 345 386.66 400 288 400z"/></svg>',
            'title' => 'Nossa Visão',
            'content' => 'Ser uma empresa de referência na área de Gestão Contábil, reconhecida como a melhor opção por clientes, colaboradores e fornecedores, pela qualidade de nossos serviços, soluções rápidas e bons atendimentos.',
        ],
        [
            'icon_svg' => '<svg viewBox="0 0 576 512" aria-hidden="true"><path d="M464 0H112c-4 0-7.8 2-10 5.4L2 152.6c-2.9 4.4-2.6 10.2.7 14.2l276 340.8c4.8 5.9 13.8 5.9 18.6 0l276-340.8c3.3-4.1 3.6-9.8.7-14.2L474.1 5.4C471.8 2 468.1 0 464 0zm-19.3 48l63.3 96h-68.4l-51.7-96h56.8zm-202.1 0h90.7l51.7 96H191l51.6-96zm-111.3 0h56.8l-51.7 96H68l63.3-96zm-43 144h51.4L208 352 88.3 192zm102.9 0h193.6L288 435.3 191.2 192zM368 352l68.2-160h51.4L368 352z"/></svg>',
            'title' => 'Nossos Valores',
            'content' => 'Ética, Agilidade, Segurança, Valorização e respeito aos nossos clientes e colaboradores. São as pessoas o grande diferencial para que tudo se torne sempre possível.',
        ],
    ],
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sobre nós — CT Price</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/vendor/swiper/swiper-bundle.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/reset.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/fonts.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/variables.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/header.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/internal-hero.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/image-text-section.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/flat-icon-box-section.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/image-content-cta-section.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/client-logo-card.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/clients-carousel-section.css">
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
    <?php $flatIconBoxSection = $missionVisionValuesSection; require __DIR__ . '/../components/flat-icon-box-section.php'; ?>
    <?php $imageContentCtaSection = $dedicationSection; require __DIR__ . '/../components/image-content-cta-section.php'; ?>
    <?php require __DIR__ . '/../components/clients-carousel-section.php'; ?>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
<?php require __DIR__ . '/../includes/cookie-banner.php'; ?>
<?php require __DIR__ . '/../includes/whatsapp-button.php'; ?>

<script src="<?= BASE_URL ?>/assets/vendor/swiper/swiper-bundle.min.js" defer></script>
<script src="<?= BASE_URL ?>/assets/js/header.js" defer></script>
<script src="<?= BASE_URL ?>/assets/js/cookie-banner.js" defer></script>
<script src="<?= BASE_URL ?>/assets/js/clients-carousel-init.js" defer></script>
</body>
</html>
