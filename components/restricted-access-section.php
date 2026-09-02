<?php
/**
 * components/restricted-access-section.php
 *
 * Grade dos 2 acessos de `/arearestrita/` — substitui o Elementor Flip Box do original
 * (docs/reference/arearestrita-audit.md, seções 6, 7, 8, 11) por cards estáticos, sem
 * dependência de hover/3D para expor a informação:
 *
 *   - Título e descrição SEMPRE visíveis (não escondidos atrás de um flip que só aparece no
 *     hover/focus) — corrige o problema de acessibilidade "conteúdo só disponível por
 *     transformação 3D" já registrado na auditoria.
 *   - Nenhum `tabindex="0"` artificial: o card em si não é focável; só o CTA real ("Acessar",
 *     quando disponível) entra na ordem de tabulação — corrige os "tab-stops mortos" da
 *     auditoria (containers focáveis sem nenhuma ação própria).
 *   - Área clicável CONSISTENTE entre os dois cards: quando o acesso está disponível, só o botão
 *     "Acessar" é o link (mesmo padrão nos dois cards) — a auditoria encontrou os dois cards do
 *     original com escopo de link diferente entre si (um só no botão, outro no card inteiro);
 *     aqui os dois seguem a mesma regra.
 *   - Correção do corte de "COLABORADORES": título em `font-size` responsivo (não fixo em 45px)
 *     e com `overflow-wrap: break-word` como rede de segurança — nunca cortado silenciosamente
 *     por um `overflow:hidden` ancestral (ver assets/css/restricted-access-section.css).
 *
 * ESTADO DISPONÍVEL/INDISPONÍVEL ORIENTADO PELO CONFIG: cada item já chega aqui com `url`
 * resolvida (ou `null`) a partir de config/company.php['sistemas_externos'] — este componente só
 * decide COMO renderizar, nunca de onde vem o dado. Enquanto `url` for `null` (destino ainda não
 * confirmado pela CT Price — ver docs/reference/arearestrita-audit.md, seção 18), renderiza um
 * `<span>` "Acesso temporariamente indisponível", nunca um `<a href="#">`/`javascript:void(0)`/
 * link para o destino quebrado original. Quando a URL for preenchida em config/company.php, o
 * mesmo componente passa a renderizar o `<a>` real automaticamente, sem nenhuma alteração aqui.
 *
 * NÃO é um sistema de login: os cards só apontam para sistemas externos de terceiros (mesmo
 * comportamento do original) — nenhuma autenticação/sessão é criada por este projeto.
 *
 * Espera, definidas pelo chamador antes do include:
 *
 *   $restrictedAccessSection = [
 *       'items' => [ // config/restricted-area.php, com `url` já resolvida pelo chamador
 *           [
 *               'key'         => 'clientes',
 *               'title'       => 'Clientes',
 *               'description' => '...',
 *               'image'       => 'clientes.jpg',
 *               'url'         => 'https://...' ou null,
 *           ],
 *           ...
 *       ],
 *   ];
 *
 * Cada imagem é servida de assets/images/pages/arearestrita/ (BASE_URL montado aqui, não pelo
 * chamador — mesma convenção de components/benefits-grid-section.php).
 */

$items = $restrictedAccessSection['items'] ?? [];
?>
<section class="restricted-access-section" aria-labelledby="restricted-area-heading">
    <div class="restricted-access-section__container">
        <ul class="restricted-access-grid" role="list">
            <?php foreach ($items as $item): ?>
            <?php
                $key = $item['key'] ?? '';
                $title = $item['title'] ?? '';
                $description = $item['description'] ?? '';
                $imageUrl = BASE_URL . '/assets/images/pages/arearestrita/' . ($item['image'] ?? '');
                $url = $item['url'] ?? null;
                $available = is_string($url) && $url !== '';
                $cardClass = 'restricted-access-card restricted-access-card--' . htmlspecialchars($key, ENT_QUOTES, 'UTF-8')
                    . ($available ? ' restricted-access-card--available' : ' restricted-access-card--unavailable');
            ?>
            <li class="<?= $cardClass ?>">
                <div class="restricted-access-card__image-wrap">
                    <img class="restricted-access-card__image" src="<?= htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8') ?>" alt="Ilustração da área restrita — <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>" loading="lazy" width="800" height="800">
                </div>
                <div class="restricted-access-card__body">
                    <h3 class="restricted-access-card__title"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h3>
                    <p class="restricted-access-card__description"><?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?></p>
                    <?php if ($available): ?>
                    <a class="btn btn--pill-outline restricted-access-card__cta" href="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>" aria-label="Acessar área restrita — <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>">
                        Acessar
                    </a>
                    <?php else: ?>
                    <span class="restricted-access-card__unavailable">Acesso temporariamente indisponível</span>
                    <?php endif; ?>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
