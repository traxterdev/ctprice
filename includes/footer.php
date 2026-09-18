<?php
/**
 * includes/footer.php
 *
 * Footer global e reutilizável — usado por todas as páginas do site. Duas partes:
 * footer principal (logo + 3 colunas: endereço, menu, mapa) e bottom bar (copyright + crédito).
 *
 * Todos os dados vêm de $company (config/company.php) e $menu (config/menu.php) — nada é
 * digitado manualmente aqui, eliminando a duplicação existente no WordPress atual.
 *
 * Estrutura confirmada por inspeção direta em 1440x900/900x1200/390x844 (ver relatório final):
 * logo (25% da largura da coluna, em todos os breakpoints — regra CSS única, sem media query)
 * + 3 colunas de ~360px lado a lado, empilhando em max-width:767px (mesmo breakpoint de
 * conteúdo já usado nas demais seções da Home).
 *
 * Dado pendente (bairro/CEP): config/company.php mantém 'bairro' e 'cep' como null por
 * divergência real confirmada entre o texto do site e a URL do mapa incorporado (ver
 * docs/reference/global-data-conflicts.md, seção 3, e reference-baseline.md, seção 5). A linha
 * de bairro/CEP só é impressa quando ambos os valores existem — não é inventada, e a ausência
 * dela aqui é o comportamento esperado e já documentado, não um defeito desta implementação.
 *
 * Faixa social (2026-09-17, pedido explícito do cliente): entre o footer principal e a bottom
 * bar, uma faixa discreta própria com os 3 canais oficiais agora confirmados
 * ($company['redes_sociais'], ver config/company.php) — não redesenha as 3 colunas já medidas
 * do footer original, só adiciona uma faixa nova. Ícones em SVG inline (mesma convenção já usada
 * em includes/topbar.php e components/video-testimonials-section.php) — sem Font Awesome nem
 * nenhuma biblioteca de ícones nova.
 *
 * Crédito "Desenvolvido por" (ajuste pontual, 2026-09-17): fonte única em
 * $company['desenvolvido_por'] (config/company.php) — substituiu "Agência Lester" por TRAXTER.
 * Mesmo posicionamento de sempre na bottom bar, só texto + link (sem logo).
 *
 * Tradução automática (2026-09-17, pedido explícito do cliente): includes/footer.php é incluído
 * SOMENTE pelas páginas do site público (nunca por `/admin/`, que usa
 * admin/includes/layout-footer.php próprio) — por isso é o único ponto necessário para carregar
 * o motor de tradução (includes/translate-widget.php) e o script que liga as bandeiras do
 * topbar a ele (assets/js/language-switcher.js), cobrindo as 12 páginas públicas sem editar cada
 * uma. Marcas próprias (razão social, crédito de desenvolvimento) recebem `translate="no"` —
 * ver item 11 do pedido ("CT Price"/"TRAXTER" não devem ser traduzidos).
 */

$endereco = $company['endereco'] ?? [];
$temBairroCep = !empty($endereco['bairro']) && !empty($endereco['cep']);
$desenvolvidoPor = $company['desenvolvido_por'] ?? null;
$redesSociais = array_filter($company['redes_sociais'] ?? []);
?>
<footer class="site-footer">
    <div class="site-footer__main">
        <div class="site-footer__container">
            <div class="site-footer__logo">
                <a href="<?= BASE_URL ?>/" aria-label="CT Price — página inicial">
                    <img
                        src="<?= BASE_URL ?>/assets/images/logo/LogoPreferencialColorida-1024x297.png"
                        srcset="<?= BASE_URL ?>/assets/images/logo/LogoPreferencialColorida-300x87.png 300w, <?= BASE_URL ?>/assets/images/logo/LogoPreferencialColorida-768x223.png 768w, <?= BASE_URL ?>/assets/images/logo/LogoPreferencialColorida-1024x297.png 1024w"
                        sizes="(max-width: 800px) 100vw, 800px"
                        width="1024" height="297"
                        alt="<?= htmlspecialchars($company['razao_social'] ?? 'CT Price', ENT_QUOTES, 'UTF-8') ?>"
                    >
                </a>
            </div>

            <div class="site-footer__address">
                <p>
                    <a href="<?= htmlspecialchars($endereco['google_maps_url'] ?? '#', ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">
                        <strong><?= htmlspecialchars($endereco['logradouro'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
                    </a>
                    <?php if ($temBairroCep): ?>
                        <br><small><?= htmlspecialchars($endereco['bairro'], ENT_QUOTES, 'UTF-8') ?> – CEP: <?= htmlspecialchars($endereco['cep'], ENT_QUOTES, 'UTF-8') ?></small>
                    <?php endif; ?>
                    <br><small><?= htmlspecialchars($endereco['cidade'] ?? '', ENT_QUOTES, 'UTF-8') ?> – <strong><?= htmlspecialchars($endereco['uf'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong></small>
                </p>
                <p class="notranslate" translate="no">
                    <a href="mailto:<?= htmlspecialchars($company['emails']['contato'] ?? '', ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($company['emails']['contato'] ?? '', ENT_QUOTES, 'UTF-8') ?></a>
                    <br>
                    <a href="mailto:<?= htmlspecialchars($company['emails']['protecao_dados'] ?? '', ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($company['emails']['protecao_dados'] ?? '', ENT_QUOTES, 'UTF-8') ?></a>
                </p>
                <p>
                    Responsável Técnico
                    <br>
                    <span class="notranslate" translate="no"><?= htmlspecialchars($company['responsavel_tecnico']['nome'] ?? '', ENT_QUOTES, 'UTF-8') ?> | <strong><?= htmlspecialchars($company['responsavel_tecnico']['registro'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong></span>
                </p>
            </div>

            <div class="site-footer__menu">
                <ul>
                    <?php foreach ($menu['footer'] as $item): ?>
                    <li>
                        <a href="<?= htmlspecialchars($item['url'] ?? '#', ENT_QUOTES, 'UTF-8') ?>"><?= ct_protect_brand($item['label']) ?></a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="site-footer__map">
                <iframe
                    src="<?= htmlspecialchars($endereco['google_maps_embed_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    loading="lazy"
                    title="<?= htmlspecialchars($endereco['logradouro'] ?? 'Mapa', ENT_QUOTES, 'UTF-8') ?>"
                ></iframe>
            </div>
        </div>
    </div>

    <?php if (!empty($redesSociais)): ?>
    <div class="site-footer__social">
        <div class="site-footer__social-container">
            <span class="site-footer__social-label">Acompanhe a CT Price nas Redes Sociais</span>
            <ul class="site-footer__social-list">
                <?php if (!empty($redesSociais['instagram'])): ?>
                <li>
                    <a class="site-footer__social-link" href="<?= htmlspecialchars($redesSociais['instagram'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram da CT Price">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2c2.7 0 3.06.01 4.12.06 1.06.05 1.79.22 2.43.47.66.26 1.21.6 1.76 1.15.5.5.84 1 1.1 1.65.25.63.42 1.36.47 2.43.05 1.06.06 1.42.06 4.24s-.01 3.18-.06 4.24c-.05 1.06-.22 1.79-.47 2.43-.26.66-.6 1.21-1.15 1.76-.5.5-1 .84-1.65 1.1-.63.25-1.36.42-2.43.47-1.06.05-1.42.06-4.24.06s-3.18-.01-4.24-.06c-1.06-.05-1.79-.22-2.43-.47-.66-.26-1.21-.6-1.76-1.15-.5-.5-.84-1-1.1-1.65-.25-.63-.42-1.36-.47-2.43C2.01 15.18 2 14.82 2 12s.01-3.18.06-4.24c.05-1.06.22-1.79.47-2.43.26-.66.6-1.21 1.15-1.76.5-.5 1-.84 1.65-1.1.63-.25 1.36-.42 2.43-.47C8.82 2.01 9.18 2 12 2zm0 1.8c-2.67 0-2.99.01-4.04.06-.87.04-1.34.18-1.65.3-.41.16-.71.35-1.02.66-.31.31-.5.6-.66 1.02-.12.31-.26.78-.3 1.65C4.28 8.51 4.27 8.83 4.27 12s.01 3.49.06 4.51c.04.87.18 1.34.3 1.65.16.41.35.71.66 1.02.31.31.6.5 1.02.66.31.12.78.26 1.65.3 1.05.05 1.37.06 4.04.06s2.99-.01 4.04-.06c.87-.04 1.34-.18 1.65-.3.41-.16.71-.35 1.02-.66.31-.31.5-.6.66-1.02.12-.31.26-.78.3-1.65.05-1.02.06-1.34.06-4.51s-.01-3.49-.06-4.51c-.04-.87-.18-1.34-.3-1.65-.16-.41-.35-.71-.66-1.02-.31-.31-.6-.5-1.02-.66-.31-.12-.78-.26-1.65-.3C14.99 3.81 14.67 3.8 12 3.8zm0 3.5a4.7 4.7 0 110 9.4 4.7 4.7 0 010-9.4zm0 1.8a2.9 2.9 0 100 5.8 2.9 2.9 0 000-5.8zm5.98-3.2a1.1 1.1 0 110 2.2 1.1 1.1 0 010-2.2z"/></svg>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (!empty($redesSociais['youtube'])): ?>
                <li>
                    <a class="site-footer__social-link" href="<?= htmlspecialchars($redesSociais['youtube'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" aria-label="YouTube da CT Price">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M22.5 7.2c-.24-1.02-.97-1.82-1.9-2.08C18.9 4.6 12 4.6 12 4.6s-6.9 0-8.6.52c-.93.26-1.66 1.06-1.9 2.08C1 8.98 1 12 1 12s0 3.02.5 4.8c.24 1.02.97 1.79 1.9 2.06 1.7.54 8.6.54 8.6.54s6.9 0 8.6-.54c.93-.27 1.66-1.04 1.9-2.06.5-1.78.5-4.8.5-4.8s0-3.02-.5-4.8zM9.8 15.4V8.6L15.8 12l-6 3.4z"/></svg>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (!empty($redesSociais['facebook'])): ?>
                <li>
                    <a class="site-footer__social-link" href="<?= htmlspecialchars($redesSociais['facebook'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook da CT Price">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M13.5 21v-7.6h2.6l.4-3H13.5V8.4c0-.87.24-1.46 1.5-1.46h1.6V4.24C16.06 4.17 15.03 4 13.83 4 11.3 4 9.6 5.49 9.6 8.1v2.3H7v3h2.6V21h3.9z"/></svg>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>

    <div class="site-footer__bottom-bar">
        <div class="site-footer__bottom-bar-container">
            <p class="site-footer__copyright">
                © Copyright <?= htmlspecialchars((string) ($company['copyright_ano'] ?? date('Y')), ENT_QUOTES, 'UTF-8') ?>
                <a href="<?= BASE_URL ?>/" class="notranslate" translate="no"><?= htmlspecialchars($company['razao_social'] ?? '', ENT_QUOTES, 'UTF-8') ?></a>.
            </p>
            <p class="site-footer__credit">
                Desenvolvido por
                <?php if (!empty($desenvolvidoPor['url'])): ?>
                    <a href="<?= htmlspecialchars($desenvolvidoPor['url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= htmlspecialchars($desenvolvidoPor['nome'] ?? '', ENT_QUOTES, 'UTF-8') ?> (abre em nova aba)" class="notranslate" translate="no"><?= htmlspecialchars($desenvolvidoPor['nome'] ?? '', ENT_QUOTES, 'UTF-8') ?></a>
                <?php else: ?>
                    <span class="notranslate" translate="no"><?= htmlspecialchars($desenvolvidoPor['nome'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                <?php endif; ?>
            </p>
        </div>
    </div>

    <?php require __DIR__ . '/translate-widget.php'; ?>
</footer>
<script src="<?= BASE_URL ?>/assets/js/language-switcher.js" defer></script>
