<?php
/**
 * components/article-header.php
 *
 * Cabeçalho editorial dos 3 posts do blog — medido em docs/reference/blog-posts-audit.md, seção 4:
 * altura 400px, gradiente institucional `linear-gradient(#00222C 0%, #057038 100%)` (mesmo
 * gradiente já usado na topbar), container 1140px, `<h1>` branco alinhado à esquerda, Roboto,
 * 30px/700.
 *
 * NÃO reutiliza `boxed-hero.php`/`internal-hero.php`: os dois usam imagem de fundo (`background-
 * image`); este cabeçalho usa gradiente, sem foto — a auditoria confirmou que o post individual
 * original nunca exibe imagem destacada (seção 18 da tarefa de implementação). É estruturalmente
 * mais próximo de `components/section-title-band.php` (mesmo gradiente), mas os valores medidos
 * aqui divergem dos já aprovados nesse componente (altura fixa 400px não 180/auto, alinhamento à
 * esquerda não centralizado, fonte Roboto não a já usada lá) — modificá-lo para caber aqui seria
 * mais parâmetro do que reaproveitamento real, mesmo raciocínio já registrado para outros
 * componentes deste projeto (ver boxed-hero.php).
 *
 * SIMPLIFICAÇÃO DELIBERADA: o original tem formas geométricas decorativas (contornos finos)
 * sobrepostas ao gradiente — puramente decorativas, sem função de conteúdo. Omitidas aqui pelo
 * mesmo motivo já registrado em `section-title-band.php` (selo "CT Price" omitido lá): manter o
 * componente focado no que importa, fácil de adicionar depois se pedido explicitamente.
 *
 * Sem categoria/eyebrow: a auditoria confirmou que o post original nunca exibe a categoria no
 * cabeçalho do artigo em si (só nos cards de listagem) — não inventado aqui.
 *
 * Espera, definidas pelo chamador antes do include:
 *
 *   $articleHeader = [
 *       'title' => 'texto do <h1> (HTML confiável, definido pelo chamador, não entrada de usuário)',
 *   ];
 */

$title = $articleHeader['title'] ?? '';
?>
<section class="article-header">
    <div class="article-header__container">
        <h1 class="article-header__title"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h1>
    </div>
</section>
