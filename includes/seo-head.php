<?php
/**
 * includes/seo-head.php
 *
 * Título, meta description e canonical das páginas institucionais — consome $pageMeta (definido
 * pela página chamadora ANTES deste include, no próprio bloco PHP de topo) com as chaves:
 *
 *   'title'          — texto completo de <title> (cada página preserva o próprio texto/sufixo
 *                       já validado antes desta sprint — este partial não decide título nenhum).
 *   'description'    — texto de <meta name="description">.
 *   'canonical_path' — caminho relativo à raiz do site (ex.: '/clientes/'), convertido em URL
 *                       absoluta por ctprice_absolute_url() (config/bootstrap.php) — a mesma
 *                       função já usada por blog/_post-template.php, protegida contra Host Header
 *                       forjado (nunca reflete um Host arbitrário no canonical).
 *
 * NÃO é uma classe/serviço/sistema genérico de SEO — é um partial de <head>, no mesmo padrão de
 * includes/header.php e includes/footer.php já usados em todo o projeto (ver CLAUDE.md, "não
 * criar estrutura sem necessidade"). Sem condicional por URI: cada página traz seus próprios
 * dados via $pageMeta, este arquivo só imprime.
 *
 * Não usado por 404.php (não deve ter canonical/description institucional, ver seu próprio
 * comentário) nem por blog/_post-template.php (já implementa o mesmo padrão inline, com
 * description vinda do excerpt do post — sem motivo para tocar em código já validado nesta
 * sprint).
 */

if (!isset($pageMeta['title'], $pageMeta['description'], $pageMeta['canonical_path'])) {
    throw new RuntimeException("includes/seo-head.php requer \$pageMeta['title'|'description'|'canonical_path'] definido pelo chamador.");
}
?>
<title><?= htmlspecialchars($pageMeta['title'], ENT_QUOTES, 'UTF-8') ?></title>
<meta name="description" content="<?= htmlspecialchars($pageMeta['description'], ENT_QUOTES, 'UTF-8') ?>">
<link rel="canonical" href="<?= htmlspecialchars(ctprice_absolute_url($pageMeta['canonical_path']), ENT_QUOTES, 'UTF-8') ?>">
