<?php
/**
 * includes/topbar.php
 *
 * Barra superior global: endereço, telefone fixo, WhatsApp, e-mail e seletor de idioma
 * (visual, sem funcionalidade de tradução implementada ainda — ver
 * docs/architecture-proposal.md, seção 14.2, item 8).
 *
 * Todos os dados vêm de $company (config/company.php via config/bootstrap.php) — nada é
 * digitado manualmente aqui.
 *
 * Fidelidade medida em docs/reference/home-desktop-audit.md (seção 2.1),
 * docs/reference/home-tablet-audit.md e docs/reference/home-mobile-audit.md (quebra de linha
 * do topbar em telas estreitas — o topbar sempre pode quebrar linha, não depende de
 * breakpoint, e por isso não fixamos altura aqui: ela decorre do conteúdo).
 *
 * Endereço: o bairro e o CEP têm divergência não resolvida entre páginas do site atual
 * (ver docs/reference/global-data-conflicts.md, seção 3) e ficam de fora até serem
 * confirmados em config/company.php — o texto é montado só com as partes já confirmadas.
 */

$enderecoPartes = array_filter([
    $company['endereco']['logradouro'] ?? null,
    $company['endereco']['bairro'] ?? null,
]);
$enderecoLinha = implode(', ', $enderecoPartes);
if (!empty($company['endereco']['cidade'])) {
    $enderecoLinha .= ' - ' . $company['endereco']['cidade'];
    if (!empty($company['endereco']['uf'])) {
        $enderecoLinha .= ' - ' . $company['endereco']['uf'];
    }
}
?>
<div class="topbar">
    <div class="topbar__inner">
        <ul class="topbar__contacts">
            <?php if ($enderecoLinha !== ''): ?>
            <li class="topbar__item">
                <svg class="topbar__icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2C7.86 2 4.5 5.36 4.5 9.5c0 5.25 6.19 11.51 6.46 11.78a1.4 1.4 0 0 0 2.08 0C13.31 21 19.5 14.75 19.5 9.5 19.5 5.36 16.14 2 12 2zm0 10.25a2.75 2.75 0 1 1 0-5.5 2.75 2.75 0 0 1 0 5.5z"/></svg>
                <span><?= htmlspecialchars($enderecoLinha, ENT_QUOTES, 'UTF-8') ?></span>
            </li>
            <?php endif; ?>

            <?php if (!empty($company['telefone_fixo'])): ?>
            <li class="topbar__item">
                <svg class="topbar__icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M6.6 10.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1C10.6 21 3 13.4 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.4 0 .8-.2 1L6.6 10.8z"/></svg>
                <span><?= htmlspecialchars($company['telefone_fixo'], ENT_QUOTES, 'UTF-8') ?></span>
            </li>
            <?php endif; ?>

            <?php if (!empty($company['whatsapp_principal']['url'])): ?>
            <li class="topbar__item">
                <a href="<?= htmlspecialchars($company['whatsapp_principal']['url'], ENT_QUOTES, 'UTF-8') ?>">
                    <svg class="topbar__icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5.1-1.3A10 10 0 1 0 12 2zm0 18.2c-1.6 0-3.1-.4-4.4-1.2l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2zm4.5-6.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1-.2.2-.7.8-.8.9-.1.2-.3.2-.5.1-.2-.1-1-.4-1.9-1.2-.7-.6-1.2-1.4-1.3-1.6-.1-.2 0-.4.1-.5.1-.1.2-.3.4-.4.1-.1.2-.2.2-.4.1-.2 0-.3 0-.4-.1-.1-.6-1.4-.8-1.9-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 1.9s.8 2.2.9 2.4c.1.2 1.6 2.5 4 3.5.6.2 1 .4 1.3.5.6.2 1.1.1 1.5.1.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2-.1-.1-.2-.2-.4-.3z"/></svg>
                    <span><?= htmlspecialchars($company['whatsapp_principal']['numero'], ENT_QUOTES, 'UTF-8') ?></span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (!empty($company['emails']['contato'])): ?>
            <li class="topbar__item">
                <a href="mailto:<?= htmlspecialchars($company['emails']['contato'], ENT_QUOTES, 'UTF-8') ?>">
                    <svg class="topbar__icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h16a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1zm8 9L4.6 6.3A1 1 0 0 0 4 7v.3L12 14l8-6.7V7a1 1 0 0 0-.6-.7L12 13z"/></svg>
                    <span><?= htmlspecialchars($company['emails']['contato'], ENT_QUOTES, 'UTF-8') ?></span>
                </a>
            </li>
            <?php endif; ?>
        </ul>

        <?php
        // Seletor de idioma: reprodução só visual do widget do site atual (GTranslate).
        // Sem funcionalidade de tradução — decisão de produto pendente (ver
        // docs/architecture-proposal.md, seção 14.2, item 8). Por isso os itens não são links.
        $idiomas = [
            ['codigo' => 'pt', 'label' => 'Português', 'arquivo' => 'pt-br.png'],
            ['codigo' => 'en', 'label' => 'English', 'arquivo' => 'en-us.png'],
            ['codigo' => 'es', 'label' => 'Español', 'arquivo' => 'es.png'],
        ];
        ?>
        <ul class="topbar__languages" aria-label="Idiomas disponíveis">
            <?php foreach ($idiomas as $idioma): ?>
            <li class="topbar__language">
                <span aria-label="<?= htmlspecialchars($idioma['label'], ENT_QUOTES, 'UTF-8') ?>">
                    <img src="<?= BASE_URL ?>/assets/images/icons/flags/<?= $idioma['arquivo'] ?>" alt="<?= htmlspecialchars($idioma['label'], ENT_QUOTES, 'UTF-8') ?>" width="24" height="24" loading="lazy">
                </span>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
