<?php
/**
 * /arearestrita/ — Área restrita
 *
 * Estrutura, medições e decisões documentadas em docs/reference/arearestrita-audit.md.
 *
 * Achado da auditoria: a página não tem Hero (nenhuma imagem de fundo) — é uma faixa de título
 * simples (fundo branco, heading + subtítulo) seguida de uma grade com 2 acessos externos
 * (Clientes / Colaboradores). NÃO é uma tela de login — é um hub de redirecionamento para 2
 * sistemas de terceiros, sem autenticação/sessão própria neste projeto.
 *
 * Reconstrução com melhorias deliberadas sobre o original (ver comentário de
 * components/restricted-access-section.php para o detalhe de cada uma): cards estáticos em vez
 * de Elementor Flip Box (conteúdo sempre visível, sem tab-stop morto, área clicável consistente
 * entre os 2 cards, sem corte de "Colaboradores").
 *
 * FAIXA DE TÍTULO NÃO VIROU COMPONENTE: são só 2 tags (heading + parágrafo), específicas desta
 * página, sem nenhum reuso previsto em outra página do site — criar components/*.php só para
 * isso seria abstração sem benefício real (CLAUDE.md, "não crie estrutura sem necessidade").
 *
 * DESTINOS DOS ACESSOS: config/company.php['sistemas_externos']['area_restrita_clientes'] e
 * ['area_restrita_colaboradores'] permanecem `null` propositalmente — a auditoria confirmou que
 * os 2 destinos atuais do site original (ctprice.com.br/documentos e ctprice.com.br/sh-admin)
 * estão quebrados (404 / listagem de diretório exposta) e NÃO devem ser restaurados (ver
 * docs/reference/arearestrita-audit.md, seções 4 e 18 — pendências para a CT Price). Enquanto
 * `null`, cada card renderiza o estado "Acesso temporariamente indisponível"
 * (components/restricted-access-section.php) em vez de um link quebrado.
 */
require __DIR__ . '/../config/bootstrap.php';

// URL de cada acesso resolvida AQUI, a partir da única fonte de verdade
// (config/company.php['sistemas_externos']) — nunca hardcoded, nunca duplicada em
// config/restricted-area.php. Enquanto a chave correspondente for `null`, o item chega ao
// componente com `url = null` e ele decide renderizar o estado indisponível.
$restrictedAccessItems = require __DIR__ . '/../config/restricted-area.php';
foreach ($restrictedAccessItems as &$item) {
    $item['url'] = $company['sistemas_externos'][$item['url_key']] ?? null;
}
unset($item);

$restrictedAccessSection = [
    'items' => $restrictedAccessItems,
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Área restrita — CT Price</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/reset.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/fonts.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/variables.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/header.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/restricted-access-section.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/footer.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/whatsapp-button.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/cookie-banner.css">
</head>
<body>

<?php require __DIR__ . '/../includes/topbar.php'; ?>
<?php require __DIR__ . '/../includes/header.php'; ?>

<main>
    <section class="restricted-area-intro">
        <div class="restricted-area-intro__container">
            <h2 id="restricted-area-heading" class="restricted-area-intro__title">Área Restrita</h2>
            <p class="restricted-area-intro__subtitle">Área destinada  a clientes e colaboradores da CT Price – Organização Contábil</p>
        </div>
    </section>
    <?php require __DIR__ . '/../components/restricted-access-section.php'; ?>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
<?php require __DIR__ . '/../includes/cookie-banner.php'; ?>
<?php require __DIR__ . '/../includes/whatsapp-button.php'; ?>

<script src="<?= BASE_URL ?>/assets/js/header.js" defer></script>
<script src="<?= BASE_URL ?>/assets/js/cookie-banner.js" defer></script>
</body>
</html>
