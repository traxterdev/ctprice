<?php
/**
 * components/image-content-cta-section.php
 *
 * Bloco "imagem + heading + texto + CTA" em fundo gradiente — padrão identificado em
 * docs/reference/sobre-nos-audit.md (seção "Dedicação", `/sobre-nos/`).
 *
 * DECISÃO DE COMPONENTE (não reutiliza components/image-text-section.php): embora ambos sejam
 * "imagem + texto em duas colunas", a medição direta mostrou comportamento estrutural
 * genuinamente diferente entre as duas seções, não apenas conteúdo diferente:
 *   - image-text-section (História): sem heading, sem CTA, sem background; imagem
 *     centralizada verticalmente (`justify-content:center`), texto alinhado ao topo.
 *   - esta seção (Dedicação): heading + múltiplos parágrafos + botão, sobre background em
 *     gradiente; imagem alinhada ao topo (`justify-content:normal` — sem sobra de espaço na
 *     coluna, então não faz diferença visual, mas o valor medido é esse, não `center`); a
 *     coluna de texto usa `justify-content:space-around` (distribui heading/parágrafo/CTA com
 *     espaço extra ao redor — mecanismo diferente de simples alinhamento ao topo).
 * Forçar reutilização exigiria acrescentar heading, CTA, background/gradiente e um modificador
 * de alinhamento vertical do texto (`space-around` vs. topo) a um componente hoje simples e
 * sem nenhum desses conceitos — mais condicional do que reaproveitamento real. Por isso, um
 * componente próprio, com o mesmo espírito de dados-como-array, mas nome e forma adequados ao
 * que ele de fato faz.
 *
 * Medições: reinspeção direta via Chrome DevTools MCP em 1440x900/900x1200/390x844. Breakpoint
 * de empilhamento (max-width:767px) confirmado independentemente para esta seção.
 *
 * CORREÇÃO DE DEFEITO CONHECIDO (categoria C — docs/architecture-proposal.md §2): o CTA
 * "Fale Conosco" do original aponta para `https://ctprice.com.br/contato` (404). O destino real
 * usado aqui é definido pelo chamador (não hardcoded neste componente), então cabe a quem monta
 * os dados da página escolher a rota funcional (`/fale-conosco/`) em vez do link quebrado — ver
 * sobre-nos/index.php.
 *
 * Sem animação de entrada por scroll (confirmado: nenhum widget desta seção tem
 * `data-settings`/classe `elementor-invisible` no original) — por isso não usa
 * assets/js/scroll-reveal.js.
 *
 * CTA: visualmente igual ao padrão de botão preenchido já usado na Home (classe `.btn--filled`,
 * definida em assets/css/services-section.css), mas reproduzido aqui com uma classe própria
 * (`image-content-cta-section__cta`) em vez de importar/mover aquele CSS — evita depender de
 * services-section.css ser carregado nesta página e evita mexer num arquivo de outra seção já
 * aprovada só por causa deste componente. Segue a convenção já usada no projeto: cada componente
 * tem seu próprio CSS autocontido.
 *
 * Espera, definidas pelo chamador antes do include:
 *
 *   $imageContentCtaSection = [
 *       'image'        => 'URL da imagem (com BASE_URL)',
 *       'image_alt'    => 'texto alternativo da imagem',
 *       'heading_html' => 'HTML confiável do heading (definido pelo chamador, não entrada de
 *                          usuário) — pode conter <span> coloridos, exatamente como no original',
 *       'content_html' => 'HTML confiável dos parágrafos (idem)',
 *       'cta_label'    => 'texto do botão',
 *       'cta_url'      => 'destino do botão (já corrigido pelo chamador, se necessário)',
 *   ];
 */

$image = $imageContentCtaSection['image'] ?? '';
$imageAlt = $imageContentCtaSection['image_alt'] ?? '';
$headingHtml = $imageContentCtaSection['heading_html'] ?? '';
$contentHtml = $imageContentCtaSection['content_html'] ?? '';
$ctaLabel = $imageContentCtaSection['cta_label'] ?? '';
$ctaUrl = $imageContentCtaSection['cta_url'] ?? '';
?>
<section class="image-content-cta-section">
    <div class="image-content-cta-section__inner">
        <div class="image-content-cta-section__image-col">
            <img class="image-content-cta-section__image" src="<?= htmlspecialchars($image, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($imageAlt, ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
        </div>
        <div class="image-content-cta-section__text-col">
            <h2 class="image-content-cta-section__heading"><?= $headingHtml ?></h2>
            <div class="image-content-cta-section__content"><?= $contentHtml ?></div>
            <div class="image-content-cta-section__cta-wrap">
                <a class="image-content-cta-section__cta" href="<?= htmlspecialchars($ctaUrl, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($ctaLabel, ENT_QUOTES, 'UTF-8') ?></a>
            </div>
        </div>
    </div>
</section>
