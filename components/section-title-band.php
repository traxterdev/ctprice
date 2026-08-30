<?php
/**
 * components/section-title-band.php
 *
 * Faixa de título em fundo gradiente — padrão identificado em `/parcerias/`
 * (docs/reference/parcerias-audit.md, seção 1, itens 4 e 6: "Ferramentas WEB para os Clientes CT
 * Price" e "Parceiros"), usado duas vezes na mesma página para introduzir cada categoria de
 * conteúdo. Medido diretamente: altura 180px, container 1200px, texto branco centralizado,
 * mesmo gradiente já usado na topbar (`assets/css/header.css`).
 *
 * SIMPLIFICAÇÃO DELIBERADA: o original também exibe um selo/logo "CT Price" (imagem decorativa,
 * sem função de conteúdo) ao lado do título em cada faixa — puramente decorativo e redundante
 * com o logo já presente no header da página. Omitido aqui para manter o componente focado no
 * único dado que realmente importa (o título) e não virar um page builder — fácil de adicionar
 * depois caso seja pedido explicitamente.
 *
 * `font_size` é configurável porque foi medido DIFERENTE nas duas ocorrências reais da página
 * (40px em "Ferramentas WEB...", 50px em "Parceiros") — não uma suposição, um valor realmente
 * encontrado em cada instância.
 *
 * Espera, definidas pelo chamador antes do include:
 *
 *   $sectionTitleBand = [
 *       'title'     => 'texto do título (HTML confiável, definido pelo chamador)',
 *       'font_size' => 'opcional, em px — padrão 40 (ver /parcerias/, que usa 50 na segunda faixa)',
 *   ];
 */

$title = $sectionTitleBand['title'] ?? '';
$fontSize = $sectionTitleBand['font_size'] ?? 40;
?>
<section class="section-title-band">
    <div class="section-title-band__inner">
        <h2 class="section-title-band__title" style="font-size: <?= (int) $fontSize ?>px; line-height: <?= (int) $fontSize ?>px;"><?= $title ?></h2>
    </div>
</section>
