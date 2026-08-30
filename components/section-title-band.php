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
 * EVOLUÇÃO (docs/reference/trabalhe-conosco-audit.md, seção 15): a faixa "Nossos Benefícios" de
 * `/trabalhe-conosco/` usa o mesmo conceito visual (gradiente + título branco centralizado), mas
 * com 4 valores medidos diferentes dos de `/parcerias/`: altura determinada pelo conteúdo (não
 * fixa), container 1140px (não 1200px), gradiente com stops em 15%/90% (não 0%/100%, mesmas
 * cores) e `font-weight:700` (não 800). Em vez de um componente novo só por causa disso, os 4
 * valores viraram props opcionais — cada uma com o padrão igual ao já usado em `/parcerias/`, de
 * modo que essa página continua funcionando sem passar nenhuma delas (nenhuma mudança visual,
 * reconfirmado por regressão). `id` também é opcional, para permitir uma âncora HTML real
 * (`id="beneficios"`) na própria seção, sem precisar de um `<div>` extra só para isso.
 *
 * Espera, definidas pelo chamador antes do include:
 *
 *   $sectionTitleBand = [
 *       'title'               => 'texto do título (HTML confiável, definido pelo chamador)',
 *       'font_size'           => 'opcional, em px — padrão 40 (ver /parcerias/, que usa 50 na segunda faixa)',
 *       'font_weight'         => 'opcional — padrão 800 (ver /trabalhe-conosco/, que usa 700)',
 *       'height'              => 'opcional — padrão 180 (px); aceita a string "auto" para altura
 *                                 determinada pelo conteúdo (ver /trabalhe-conosco/)',
 *       'container_max_width' => 'opcional, em px — padrão 1200 (ver /trabalhe-conosco/, que usa 1140)',
 *       'gradient_stops'      => 'opcional — padrão ["0%", "100%"] (ver /trabalhe-conosco/, que usa ["15%", "90%"])',
 *       'id'                  => 'opcional — atributo id da seção, para uma âncora HTML real (ex.: "beneficios")',
 *   ];
 */

$title = $sectionTitleBand['title'] ?? '';
$fontSize = (int) ($sectionTitleBand['font_size'] ?? 40);
$fontWeight = (int) ($sectionTitleBand['font_weight'] ?? 800);
$height = $sectionTitleBand['height'] ?? 180;
$containerMaxWidth = (int) ($sectionTitleBand['container_max_width'] ?? 1200);
$gradientStops = $sectionTitleBand['gradient_stops'] ?? ['0%', '100%'];
$id = $sectionTitleBand['id'] ?? null;

$heightStyle = $height === 'auto' ? 'height: auto; padding-top: 30px; padding-bottom: 30px;' : 'height: ' . (int) $height . 'px;';
$gradientStyle = 'background: linear-gradient(180deg, var(--color-dark-teal) ' . htmlspecialchars((string) $gradientStops[0], ENT_QUOTES, 'UTF-8') . ', var(--color-brand-green) ' . htmlspecialchars((string) $gradientStops[1], ENT_QUOTES, 'UTF-8') . ');';
?>
<section class="section-title-band"<?= $id ? ' id="' . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . '"' : '' ?> style="<?= $heightStyle . ' ' . $gradientStyle ?>">
    <div class="section-title-band__inner" style="max-width: <?= $containerMaxWidth ?>px;">
        <h2 class="section-title-band__title" style="font-size: <?= $fontSize ?>px; line-height: <?= $fontSize ?>px; font-weight: <?= $fontWeight ?>;"><?= $title ?></h2>
    </div>
</section>
