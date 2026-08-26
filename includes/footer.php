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
 */

$endereco = $company['endereco'] ?? [];
$temBairroCep = !empty($endereco['bairro']) && !empty($endereco['cep']);
$agenciaUrl = $company['sistemas_externos']['agencia_desenvolvimento'] ?? null;
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
                <p>
                    <a href="mailto:<?= htmlspecialchars($company['emails']['contato'] ?? '', ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($company['emails']['contato'] ?? '', ENT_QUOTES, 'UTF-8') ?></a>
                    <br>
                    <a href="mailto:<?= htmlspecialchars($company['emails']['protecao_dados'] ?? '', ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($company['emails']['protecao_dados'] ?? '', ENT_QUOTES, 'UTF-8') ?></a>
                </p>
                <p>
                    Responsável Técnico
                    <br>
                    <?= htmlspecialchars($company['responsavel_tecnico']['nome'] ?? '', ENT_QUOTES, 'UTF-8') ?> | <strong><?= htmlspecialchars($company['responsavel_tecnico']['registro'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
                </p>
            </div>

            <div class="site-footer__menu">
                <ul>
                    <?php foreach ($menu['footer'] as $item): ?>
                    <li>
                        <a href="<?= htmlspecialchars($item['url'] ?? '#', ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?></a>
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

    <div class="site-footer__bottom-bar">
        <div class="site-footer__bottom-bar-container">
            <p class="site-footer__copyright">
                © Copyright <?= htmlspecialchars((string) ($company['copyright_ano'] ?? date('Y')), ENT_QUOTES, 'UTF-8') ?>
                <a href="<?= BASE_URL ?>/"><?= htmlspecialchars($company['razao_social'] ?? '', ENT_QUOTES, 'UTF-8') ?></a>.
            </p>
            <p class="site-footer__credit">
                Desenvolvido por
                <?php if ($agenciaUrl): ?>
                    <a href="<?= htmlspecialchars($agenciaUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">Agência Lester</a>
                <?php else: ?>
                    Agência Lester
                <?php endif; ?>
            </p>
        </div>
    </div>
</footer>
