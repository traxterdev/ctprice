<?php
/**
 * /ouvidoria/ — Ouvidoria
 *
 * Estrutura, medições e decisões documentadas em docs/reference/ouvidoria-audit.md.
 *
 * Reconstrução com o mesmo padrão de segurança já validado em `/fale-conosco/` (endpoint PHP
 * próprio, CSRF por sessão, honeypot, rate limit, validação server-side), adaptado para os
 * campos e o upload de evidências próprios desta página — ver comentários de
 * ouvidoria/ouvidoria-action.php e components/ombudsman-form-section.php.
 *
 * IDENTIFICAÇÃO OBRIGATÓRIA PRESERVADA: Nome, Contato, E-mail e Empresa continuam obrigatórios,
 * exatamente como no original — não existe opção de manifestação anônima nesta etapa.
 * Possibilidade de manifestação anônima depende de decisão formal da CT Price (não implementada
 * por falta de autorização, ver docs/reference/ouvidoria-audit.md, seção 5).
 */
require __DIR__ . '/../config/bootstrap.php';

// Sessão necessária para o token CSRF do formulário e para o rate limit do endpoint
// (ouvidoria-action.php) — precisa iniciar antes de qualquer saída HTML. Parâmetros do cookie
// centralizados em config/bootstrap.php. Chave de sessão PRÓPRIA desta página
// ('ouvidoria_csrf') — nunca a mesma de Fale Conosco ('fale_conosco_csrf').
ctprice_configure_session_cookie();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['ouvidoria_csrf'])) {
    $_SESSION['ouvidoria_csrf'] = bin2hex(random_bytes(32));
}
$csrfToken = $_SESSION['ouvidoria_csrf'];

// Banner de fallback sem JavaScript — ver ouvidoria-action.php e ombudsman-form-section.php.
$formStatus = null;
$statusParam = $_GET['status'] ?? null;
if ($statusParam === 'success') {
    $formStatus = ['type' => 'success', 'message' => 'Manifestação enviada com sucesso! Em breve entraremos em contato.'];
} elseif ($statusParam === 'rate_limited') {
    $formStatus = ['type' => 'error', 'message' => 'Aguarde alguns segundos antes de enviar novamente.'];
} elseif ($statusParam === 'invalid') {
    $formStatus = ['type' => 'error', 'message' => 'Verifique os dados informados (e os anexos, se houver) e tente novamente.'];
} elseif ($statusParam === 'error') {
    $formStatus = ['type' => 'error', 'message' => 'Não foi possível enviar sua manifestação no momento. Tente novamente mais tarde ou use o WhatsApp exclusivo.'];
}

$boxedHero = [
    'eyebrow' => 'ouvidoria',
    'title' => 'Na CT Price, sua voz é nossa prioridade.',
    'image' => BASE_URL . '/assets/images/pages/ouvidoria/ouvidoria.png',
];

$ombudsmanIntroSection = [
    'content_html' => '<p>Na <strong><span style="color:#10E36B">CT Price</span></strong>, acreditamos que a comunicação transparente e o respeito ao cliente são pilares fundamentais de uma gestão eficiente e ética.</p>'
        . '<p>Por essa razão, foi disponibilizado um novo canal de atendimento para que nossos clientes e parceiros possam expressar sua insatisfação por problemas ligados ao seu atendimento, ou mesmo, manifestar sua satisfação sobre a qualidade do atendimento recebido.</p>'
        . '<p>Esse novo canal, trata-se da <strong><span style="color:#10E36B">OUVIDORIA</span></strong>, o qual, é um espaço exclusivo aos nossos clientes que já utilizam outros canais de atendimento e não se sentem plenamente atendidos.</p>',
    'image' => BASE_URL . '/assets/images/pages/ouvidoria/atendente.png',
    'image_alt' => '',
];

$ombudsmanGuidanceSection = [
    'content_html' => '<p>As informações registradas neste canal, são sigilosas e analisadas somente pela diretoria da empresa e pelo time de ouvidoria interna, onde, o objetivo é a promoção da melhoria contínua dos nossos serviços, processos e condutas.</p>'
        . '<p>Reclamações, sugestões, elogios, denúncias ou solicitações especiais são tratadas com seriedade, dentro de prazos previamente estabelecidos.</p>'
        . '<p>Todas as manifestações são registradas, analisadas e respondidas pelo nosso <strong>Canal de Ouvidoria</strong>, garantindo que cada voz seja ouvida e respeitada.</p>'
        . '<p>Ao recorrer à Ouvidoria, você está contribuindo diretamente para a evolução da <strong><span style="color:#10E36B">CT Price</span></strong>, fortalecendo nosso propósito de entregar excelência contábil com ética, qualidade e foco no cliente.</p>'
        . '<p>Na <strong><span style="color:#10E36B">CT Price</span></strong>, sua voz é a nossa prioridade!!</p>',
    'heading' => 'Quando utilizar a Ouvidoria?',
    'items' => [
        'Quando os demais canais não resolveram sua demanda.',
        'Para registrar denúncias, reclamações, sugestões ou elogios.',
        'Para contribuir com a melhoria contínua dos nossos serviços.',
    ],
];

$ombudsmanFormSection = [
    'heading' => 'CANAIS EXCLUSIVOS PARA RECLAMAÇÕES OU ELOGIOS',
    'intro_html' => '<p><strong>Envie sua manifestação</strong> pelo formulário ao lado ou pelo nosso <strong>Canal Exclusivo</strong> de atendimento.</p>',
    'whatsapp_label' => $company['ouvidoria']['whatsapp']['numero'] ?? '',
    'whatsapp_url' => $company['ouvidoria']['whatsapp']['url'] ?? '',
    'form_action' => BASE_URL . '/ouvidoria/ouvidoria-action.php',
    'csrf_token' => $csrfToken,
    'decorative_image' => BASE_URL . '/assets/images/logo/Isotipolinear.png',
    'status' => $formStatus,
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ouvidoria — CT Price</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/reset.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/fonts.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/variables.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/header.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/boxed-hero.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/ombudsman-intro-section.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/ombudsman-guidance-section.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/ombudsman-form-section.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/footer.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/whatsapp-button.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/cookie-banner.css">
</head>
<body>

<?php require __DIR__ . '/../includes/topbar.php'; ?>
<?php require __DIR__ . '/../includes/header.php'; ?>

<main>
    <?php require __DIR__ . '/../components/boxed-hero.php'; ?>
    <?php require __DIR__ . '/../components/ombudsman-intro-section.php'; ?>
    <?php require __DIR__ . '/../components/ombudsman-guidance-section.php'; ?>
    <?php require __DIR__ . '/../components/ombudsman-form-section.php'; ?>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
<?php require __DIR__ . '/../includes/cookie-banner.php'; ?>
<?php require __DIR__ . '/../includes/whatsapp-button.php'; ?>

<script src="<?= BASE_URL ?>/assets/js/header.js" defer></script>
<script src="<?= BASE_URL ?>/assets/js/cookie-banner.js" defer></script>
<script src="<?= BASE_URL ?>/assets/js/ouvidoria-form.js" defer></script>
</body>
</html>
