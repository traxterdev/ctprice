<?php
/**
 * components/logo-grid-section.php
 *
 * Grade de logos com link externo — usado duas vezes em `/parcerias/` (Ferramentas Web e
 * Parceiros, ver docs/reference/parcerias-audit.md) com conjuntos de dados e configuração de
 * colunas diferentes. Reaproveita a mesma identidade visual de card já aprovada em `/clientes/`
 * (classe `.logo-card`, assets/css/logo-card.css) — fundo branco, borda sutil, sombra leve,
 * `border-radius`, `object-fit:contain`, hover institucional.
 *
 * DIFERENÇA em relação a components/clients-grid-section.php (que esta página NÃO reutiliza
 * diretamente): os itens aqui são LINKS EXTERNOS (ferramenta/parceiro com site próprio), não
 * logos que abrem um lightbox interno — por isso cada card inteiro é um `<a target="_blank"
 * rel="noopener noreferrer">` quando há `url`, sem lightbox algum. Quando não há `url` (caso
 * único: "Auditto", ver config/partners.php), o item é um `<div>` com a mesma aparência visual,
 * SEM comportamento de clique falso (sem `href="#"`, sem cursor de link, sem hover de
 * interatividade).
 *
 * Número de colunas por breakpoint é CONFIGURÁVEL via custom properties CSS (`--logo-grid-cols-*`)
 * porque as duas instâncias desta página usam contagens diferentes (Ferramentas: 3/2/1;
 * Parceiros: 5/3/2 — ver assets/css/logo-grid-section.css), sem duplicar o arquivo CSS.
 *
 * FONTE DE DADOS (sprint CMS): cada item vem de `PartnerRepository::allActiveByCategory()` —
 * `logo_path` já é o caminho relativo à raiz do site (ex.:
 * "assets/images/partners/companies/logo-cfc.png" para os itens migrados de config/partners.php,
 * ou "assets/uploads/partners/<nome-aleatorio>.ext" para logos enviados pelo admin depois desta
 * sprint) — este componente só concatena BASE_URL, nunca decide o diretório por categoria.
 *
 * Espera, definidas pelo chamador antes do include:
 *
 *   $logoGridSection = [
 *       'items' => [
 *           ['nome' => 'usado como alt', 'logo_path' => 'assets/.../arquivo.ext',
 *            'url' => 'https://... ou null'],
 *           ...
 *       ],
 *       'columns_desktop'  => int, opcional (padrão 5),
 *       'columns_tablet'   => int, opcional (padrão 3),
 *       'columns_mobile'   => int, opcional (padrão 2),
 *   ];
 */

$items = $logoGridSection['items'] ?? [];
$colsDesktop = (int) ($logoGridSection['columns_desktop'] ?? 5);
$colsTablet = (int) ($logoGridSection['columns_tablet'] ?? 3);
$colsMobile = (int) ($logoGridSection['columns_mobile'] ?? 2);
?>
<section class="logo-grid-section" style="--logo-grid-cols-desktop: <?= $colsDesktop ?>; --logo-grid-cols-tablet: <?= $colsTablet ?>; --logo-grid-cols-mobile: <?= $colsMobile ?>;">
    <div class="logo-grid-section__inner">
        <div class="logo-grid">
            <?php foreach ($items as $item):
                $url = $item['url'] ?? null;
                $imgSrc = BASE_URL . '/' . ltrim($item['logo_path'], '/');
                $alt = htmlspecialchars($item['nome'] ?? '', ENT_QUOTES, 'UTF-8');
            ?>
            <?php if ($url): ?>
            <a class="logo-card" href="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer">
                <img class="logo-card__img" src="<?= htmlspecialchars($imgSrc, ENT_QUOTES, 'UTF-8') ?>" alt="<?= $alt ?>" loading="lazy">
            </a>
            <?php else: ?>
            <div class="logo-card logo-card--static">
                <img class="logo-card__img" src="<?= htmlspecialchars($imgSrc, ENT_QUOTES, 'UTF-8') ?>" alt="<?= $alt ?>" loading="lazy">
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
