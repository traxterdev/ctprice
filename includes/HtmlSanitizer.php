<?php
/**
 * includes/HtmlSanitizer.php
 *
 * Sanitização do corpo dos artigos do blog (`blog_posts.body_html`) — NÃO é um framework de
 * sanitização genérico: uma única função, uma allowlist fixa de tags baseada no conteúdo REAL dos
 * 3 artigos atuais (`<p>`, `<a>`, `<ol>`, `<li>`) mais um pequeno conjunto de formatação básica
 * igualmente segura e claramente útil para textos futuros (`<strong>`, `<em>`, `<ul>`, `<h2>`,
 * `<h3>`, `<br>`) — a mesma lista sugerida na tarefa desta sprint.
 *
 * Estratégia: parseia o HTML com `DOMDocument` (nunca regex — regex não consegue tratar HTML
 * aninhado/malformado com segurança) e percorre a árvore:
 *   - tags da allowlist: mantidas, mas com TODOS os atributos removidos (exceto `href`/`rel` em
 *     `<a>`, tratados à parte);
 *   - tags perigosas (`script`, `style`, `iframe`, `object`, `embed`, `form`, `input`, `button`,
 *     `svg`, `math`, `link`, `meta`, `base`, `head`, `title`): removidas COM todo o conteúdo —
 *     nunca sobra texto de dentro de um `<script>` na página;
 *   - qualquer outra tag não listada (ex.: `<div>`, `<span>`, `<b>`, `<i>` de uma colagem futura):
 *     "desembrulhada" — a tag some, o texto/filhos permanecem (menos destrutivo que apagar
 *     conteúdo editorial legítimo por engano);
 *   - comentários HTML: removidos.
 *
 * Links (`<a>`): só `href` com esquema `http`/`https` sobrevive (nunca `javascript:`, `data:`,
 * `vbscript:` nem qualquer outro esquema) — um link inválido é desembrulhado (mantém o texto,
 * perde o link, nunca é apagado). Link para um host diferente de `CTPRICE_CANONICAL_HOST` recebe
 * `rel="noopener noreferrer"` (atributo de segurança contra reverse tabnabbing) — `target` NÃO é
 * adicionado (preserva o comportamento visual/de navegação exatamente como os 3 artigos atuais já
 * têm: mesma aba).
 */

declare(strict_types=1);

const CTPRICE_ARTICLE_ALLOWED_TAGS = ['p', 'strong', 'em', 'ul', 'ol', 'li', 'a', 'h2', 'h3', 'br'];

const CTPRICE_ARTICLE_STRIP_WITH_CONTENT = [
    'script', 'style', 'iframe', 'object', 'embed', 'form', 'input', 'button',
    'svg', 'math', 'link', 'meta', 'base', 'head', 'title', 'noscript',
];

function ctprice_sanitize_article_html(string $html): string
{
    if (trim($html) === '') {
        return '';
    }

    $doc = new DOMDocument();
    libxml_use_internal_errors(true);
    // UTF-8 explícito (sem isso, DOMDocument assume Latin-1 e corrompe acentuação) + wrapper com
    // id próprio para conseguir extrair só o fragmento depois, sem <html><body> ao redor.
    $doc->loadHTML(
        '<?xml encoding="utf-8"?><div id="ctprice-sanitize-root">' . $html . '</div>',
        LIBXML_NOERROR | LIBXML_NOWARNING
    );
    libxml_clear_errors();

    $root = $doc->getElementById('ctprice-sanitize-root');
    if (!$root instanceof DOMElement) {
        return '';
    }

    ctprice_sanitize_children($doc, $root);

    $result = '';
    foreach (iterator_to_array($root->childNodes) as $child) {
        $result .= $doc->saveHTML($child);
    }

    return trim($result);
}

function ctprice_sanitize_children(DOMDocument $doc, DOMNode $parent): void
{
    foreach (iterator_to_array($parent->childNodes) as $node) {
        if ($node instanceof DOMComment) {
            $parent->removeChild($node);
            continue;
        }

        if ($node instanceof DOMText) {
            continue; // texto puro — sempre preservado como está
        }

        if (!$node instanceof DOMElement) {
            $parent->removeChild($node);
            continue;
        }

        $tag = strtolower($node->tagName);

        if (in_array($tag, CTPRICE_ARTICLE_STRIP_WITH_CONTENT, true)) {
            $parent->removeChild($node);
            continue;
        }

        // Sempre sanitiza os filhos PRIMEIRO (recursivo) — necessário tanto para o caso
        // "permitida" (limpar o que está dentro) quanto para o caso "desembrulhar" (os filhos já
        // saneados são o que sobra no lugar da tag removida).
        ctprice_sanitize_children($doc, $node);

        if (!in_array($tag, CTPRICE_ARTICLE_ALLOWED_TAGS, true)) {
            // Tag fora da allowlist: desembrulha (mantém os filhos já saneados, remove só a tag).
            while ($node->firstChild) {
                $parent->insertBefore($node->firstChild, $node);
            }
            $parent->removeChild($node);
            continue;
        }

        ctprice_strip_all_attributes_except($node, $tag === 'a' ? ['href'] : []);

        if ($tag === 'a') {
            ctprice_sanitize_link_attributes($node);
        }
    }
}

function ctprice_strip_all_attributes_except(DOMElement $node, array $keep): void
{
    foreach (iterator_to_array($node->attributes) as $attr) {
        if (!in_array(strtolower($attr->name), $keep, true)) {
            $node->removeAttribute($attr->name);
        }
    }
}

function ctprice_sanitize_link_attributes(DOMElement $node): void
{
    $href = trim($node->getAttribute('href'));
    $parts = $href !== '' ? parse_url($href) : false;
    $scheme = is_array($parts) ? strtolower($parts['scheme'] ?? '') : '';
    $host = is_array($parts) ? strtolower($parts['host'] ?? '') : '';

    $isValidHttpUrl = $href !== ''
        && in_array($scheme, ['http', 'https'], true)
        && $host !== ''
        && filter_var($href, FILTER_VALIDATE_URL) !== false;

    if (!$isValidHttpUrl) {
        // href ausente/inválido/esquema perigoso (javascript:, data:, ...) — desembrulha o link
        // em vez de apagar o texto (o conteúdo editorial não é destruído, só deixa de ser um link).
        $parent = $node->parentNode;
        if ($parent !== null) {
            while ($node->firstChild) {
                $parent->insertBefore($node->firstChild, $node);
            }
            $parent->removeChild($node);
        }
        return;
    }

    $node->setAttribute('href', $href);

    $canonicalHost = defined('CTPRICE_CANONICAL_HOST') ? strtolower(CTPRICE_CANONICAL_HOST) : '';
    $isExternal = $canonicalHost === '' || ($host !== $canonicalHost && $host !== 'www.' . $canonicalHost);

    if ($isExternal) {
        // Só o atributo de segurança — sem `target="_blank"` (preserva o comportamento de
        // navegação exatamente como os artigos atuais: mesma aba).
        $node->setAttribute('rel', 'noopener noreferrer');
    }
}
