<?php
/**
 * /fale-conosco/ — Fale Conosco
 *
 * Estrutura, medições e decisões de UI documentadas em docs/reference/fale-conosco-audit.md.
 * Formulário processado por fale-conosco/fale-conosco-action.php (endpoint PHP próprio, sem
 * WordPress/Elementor/admin-ajax.php) — ver comentário desse arquivo para a estratégia completa
 * de segurança (CSRF, honeypot, rate limit, validação server-side).
 */
require __DIR__ . '/../config/bootstrap.php';

// Sessão necessária para o token CSRF do formulário e para o rate limit simples do endpoint
// (fale-conosco-action.php) — precisa iniciar antes de qualquer saída HTML. Parâmetros do cookie
// (HttpOnly/SameSite/Secure condicional) centralizados em config/bootstrap.php.
ctprice_configure_session_cookie();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['fale_conosco_csrf'])) {
    $_SESSION['fale_conosco_csrf'] = bin2hex(random_bytes(32));
}
$csrfToken = $_SESSION['fale_conosco_csrf'];

// Banner de fallback sem JavaScript — ver fale-conosco-action.php e
// components/contact-form-section.php ("continuar funcional... caso JavaScript falhe").
$formStatus = null;
$statusParam = $_GET['status'] ?? null;
if ($statusParam === 'success') {
    $formStatus = ['type' => 'success', 'message' => 'Mensagem enviada com sucesso! Em breve entraremos em contato.'];
} elseif ($statusParam === 'rate_limited') {
    $formStatus = ['type' => 'error', 'message' => 'Aguarde alguns segundos antes de enviar novamente.'];
} elseif ($statusParam === 'invalid') {
    $formStatus = ['type' => 'error', 'message' => 'Verifique os dados informados e tente novamente.'];
} elseif ($statusParam === 'error') {
    $formStatus = ['type' => 'error', 'message' => 'Não foi possível enviar sua mensagem no momento. Tente novamente mais tarde ou use o WhatsApp.'];
}

$boxedHero = [
    'eyebrow' => 'fale conosco',
    'title' => 'Tire suas dúvidas ou envie sugestões',
    'image' => BASE_URL . '/assets/images/pages/fale-conosco/pgcontato.jpg',
];

$contactFormSection = [
    'photo' => BASE_URL . '/assets/images/pages/fale-conosco/maosdadas.jpg',
    'decorative_image' => BASE_URL . '/assets/images/logo/Isotipolinear.png',
    'intro_html' => 'Quer tirar dúvidas ou conversar sobre como a <strong>CT Price</strong> pode ajudar sua empresa a crescer? Entre em contato com a gente!',
    'form_action' => BASE_URL . '/fale-conosco/fale-conosco-action.php',
    'csrf_token' => $csrfToken,
    'status' => $formStatus,
];

$departmentsContactSection = [
    'departments' => $company['departamentos'] ?? [],
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Fale Conosco — CT Price</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/reset.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/fonts.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/variables.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/header.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/boxed-hero.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/contact-form-section.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/departments-contact-section.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/footer.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/whatsapp-button.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/cookie-banner.css">
</head>
<body>

<?php require __DIR__ . '/../includes/topbar.php'; ?>
<?php require __DIR__ . '/../includes/header.php'; ?>

<main>
    <?php require __DIR__ . '/../components/boxed-hero.php'; ?>
    <?php require __DIR__ . '/../components/contact-form-section.php'; ?>
    <?php require __DIR__ . '/../components/departments-contact-section.php'; ?>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
<?php require __DIR__ . '/../includes/cookie-banner.php'; ?>
<?php require __DIR__ . '/../includes/whatsapp-button.php'; ?>

<script src="<?= BASE_URL ?>/assets/js/header.js" defer></script>
<script src="<?= BASE_URL ?>/assets/js/cookie-banner.js" defer></script>
<script src="<?= BASE_URL ?>/assets/js/contact-form.js" defer></script>
</body>
</html>
