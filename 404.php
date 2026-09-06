<?php
/**
 * 404.php — Página não encontrada
 *
 * Handler próprio da CT Price para qualquer URL inexistente, configurado no Apache via
 * `ErrorDocument 404 /404.php` (.htaccess, raiz). Correção do P1 registrado em
 * docs/reference/global-final-audit.md, seção 39 ("nenhuma página 404 customizada existe").
 * Detalhe completo da sprint: docs/reference/404-redirects-server-validation.md.
 *
 * NÃO é um front controller/router: esta página não reescreve nenhuma URL para si mesma nem
 * decide o que exibir a partir do caminho pedido — o Apache é quem decide chamá-la (qualquer
 * URL física real continua indo para seu próprio diretório/index.php, normalmente). Por isso
 * este arquivo não lê $_SERVER['REQUEST_URI'] em nenhum momento (item 11 da sprint: não expor a
 * URL solicitada, evita a necessidade de escapá-la e qualquer risco de refleti-la sem escape).
 *
 * Acesso DIRETO a este arquivo (ex.: alguém digitando /404.php) também deve responder HTTP 404 —
 * por isso http_response_code(404) é chamado incondicionalmente logo abaixo, antes de qualquer
 * saída, e não depende de o Apache ter chegado aqui via ErrorDocument.
 *
 * Reaproveita os MESMOS globais de qualquer outra página pública (topbar/header/footer/bottom
 * bar/WhatsApp flutuante/cookie banner, via includes/*.php) — nenhum header/footer/menu/WhatsApp
 * próprio foi criado (item 7 da sprint).
 *
 * `noindex,follow`: não deve ser indexada nem aparecer em resultados de busca, mas os links que
 * ela contém (Início/Fale Conosco) continuam sendo rastreáveis normalmente. Sem `canonical` —
 * uma 404 não tem uma URL "correta" alternativa para apontar (item 10 da sprint).
 */
require __DIR__ . '/config/bootstrap.php';

http_response_code(404);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Página não encontrada — CT Price</title>
    <meta name="robots" content="noindex,follow">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/reset.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/fonts.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/variables.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/header.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/error-page.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/footer.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/whatsapp-button.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/cookie-banner.css">
</head>
<body>

<?php require __DIR__ . '/includes/topbar.php'; ?>
<?php require __DIR__ . '/includes/header.php'; ?>

<main>
    <section class="error-page">
        <div class="error-page__container">
            <p class="error-page__code" aria-hidden="true">404</p>
            <h1 class="error-page__title">Página não encontrada</h1>
            <p class="error-page__text">O endereço informado não foi encontrado ou pode ter sido alterado.</p>
            <div class="error-page__actions">
                <a class="btn btn--filled" href="<?= BASE_URL ?>/">Voltar para o início</a>
                <a class="error-page__secondary-link" href="<?= BASE_URL ?>/fale-conosco/">Precisa de ajuda? Fale Conosco</a>
            </div>
        </div>
    </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
<?php require __DIR__ . '/includes/cookie-banner.php'; ?>
<?php require __DIR__ . '/includes/whatsapp-button.php'; ?>

<script src="<?= BASE_URL ?>/assets/js/header.js" defer></script>
<script src="<?= BASE_URL ?>/assets/js/cookie-banner.js" defer></script>
</body>
</html>
