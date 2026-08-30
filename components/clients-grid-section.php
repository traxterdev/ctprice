<?php
/**
 * components/clients-grid-section.php
 *
 * Grade estática de logos de clientes/parceiros — seção exclusiva de `/clientes/`
 * (ver docs/reference/clientes-audit.md, seções 3 e 4).
 *
 * DIFERENÇA TEMPORÁRIA CONHECIDA (decisão consciente de escopo, não regressão): o original em
 * WordPress usa 106 logos numa galeria justificada (Elementor "Gallery" widget). O CMS que
 * permitiria gerenciar esse catálogo completo foi adiado para uma etapa futura de manutenção de
 * conteúdo. Nesta fase, a página usa os MESMOS 82 logos já centralizados em `config/clients.php`
 * (mesma fonte do carrossel da Home/Sobre Nós) — os 72 logos exclusivos da página original
 * (uploads de 2025/2026, ver auditoria) não foram baixados nem reproduzidos.
 *
 * DECISÃO DE LAYOUT (revisada na etapa de refinamento de UI): o original usa uma galeria
 * "justificada" (cada item com largura/altura próprias, calculadas por um algoritmo de
 * preenchimento de linha baseado na proporção de cada imagem). A primeira versão desta página
 * reproduzia esse efeito com uma grade `auto-fill` "solta" (sem card). Nesta revisão, a
 * prioridade deixou de ser copiar literalmente a galeria do WordPress — decisão explícita do
 * cliente para uma apresentação mais premium: cada logo agora vive num CARD (classe
 * `.logo-card`, aparência em assets/css/logo-card.css — fundo branco, borda sutil, sombra leve,
 * `border-radius`, `object-fit:contain`, hover), em grade fixa de 5/3/2 colunas por
 * breakpoint (ver assets/css/clients-grid-section.css). A mesma classe/aparência de card é
 * reaproveitada pelo carrossel da Home (components/clients-carousel-section.php) — apenas a
 * altura/padding do card mudam por contexto, não a identidade visual.
 *
 * CORREÇÃO INTENCIONAL DE DEFEITO CONHECIDO (categoria C — clientes-audit.md, seção 10, item 3):
 * o original usa `background-size:cover` nos itens da galeria, cortando logos com proporção
 * diferente da célula. Aqui cada logo usa `object-fit:contain`, preservando a identidade visual
 * completa de cada marca (mesmo princípio já aplicado em clients-carousel-section.php).
 *
 * LIGHTBOX: o original abre um lightbox nativo do Elementor ao clicar em qualquer logo
 * (slideshow entre as imagens). Mantido aqui como interação real e útil (permite ver o logo em
 * tamanho maior), implementado em JavaScript puro (assets/js/clients-grid-lightbox.js), sem
 * biblioteca externa — não é uma feature nova inventada, é a reprodução de um comportamento já
 * confirmado no original.
 *
 * Reaproveita a MESMA fonte de dados do carrossel (config/clients.php) — não duplica os 82
 * registros. Consumido tanto por components/clients-carousel-section.php (Home, Sobre Nós) quanto
 * por este componente (Clientes), cada um com sua própria apresentação visual.
 *
 * Espera, definida pelo chamador antes do include:
 *
 *   $clientLogos = [
 *       ['file' => 'nome-do-arquivo.ext', 'alt' => 'texto alternativo'],
 *       ...
 *   ];
 *
 * Cada arquivo é servido de assets/images/clients/home-carousel/ (BASE_URL montado aqui, mesma
 * convenção de clients-carousel-section.php).
 */

if (!isset($clientLogos) || !is_array($clientLogos)) {
    $clientLogos = [];
}

/*
 * ORDEM DE EXIBIÇÃO — embaralhamento determinístico por dia.
 *
 * Nenhum cliente deve parecer "mais importante" só por aparecer sempre em primeiro. Ao mesmo
 * tempo, a ordem não pode mudar a cada recarregamento (ficaria com aparência de bagunça) nem
 * exigir sessão/cookie/estado no servidor (mantém a página estática e simples).
 *
 * Solução: cada logo recebe uma chave de ordenação = crc32(nome_do_arquivo + data_de_hoje). Como
 * a data entra na chave, a ordem inteira gira uma vez por dia (mesma ordem para todos os
 * visitantes no mesmo dia, diferente no dia seguinte) — sem tocar no gerador aleatório global do
 * PHP (evita side effects em qualquer outro código que use rand()/shuffle() na mesma requisição)
 * e sem exigir localStorage/sessão no cliente. Os 82 itens são sempre os mesmos; só a ordem gira.
 *
 * Desempate: se dois arquivos colidirem no crc32 do mesmo dia (extremamente improvável com 82
 * itens, mas não impossível), o nome do arquivo em si é usado como critério secundário
 * (`strcmp`), para que o resultado seja sempre determinístico por si só — sem depender da
 * garantia de estabilidade do `usort` do PHP 8 (que já bastaria sozinha, mas o desempate
 * explícito deixa a ordenação correta mesmo se essa suposição mudar).
 */
$today = date('Y-m-d');
$displayLogos = $clientLogos;
usort($displayLogos, function ($a, $b) use ($today) {
    $fileA = $a['file'] ?? '';
    $fileB = $b['file'] ?? '';
    $cmp = crc32($fileA . $today) <=> crc32($fileB . $today);
    return $cmp !== 0 ? $cmp : strcmp($fileA, $fileB);
});
?>
<section class="clients-grid-section" aria-label="Nossos clientes">
    <div class="clients-grid-section__inner">
        <div class="clients-grid">
            <?php foreach ($displayLogos as $logo): ?>
            <button type="button" class="logo-card" data-full="<?= BASE_URL ?>/assets/images/clients/home-carousel/<?= htmlspecialchars($logo['file'], ENT_QUOTES, 'UTF-8') ?>" data-alt="<?= htmlspecialchars($logo['alt'], ENT_QUOTES, 'UTF-8') ?>">
                <img
                    class="logo-card__img"
                    src="<?= BASE_URL ?>/assets/images/clients/home-carousel/<?= htmlspecialchars($logo['file'], ENT_QUOTES, 'UTF-8') ?>"
                    alt="<?= htmlspecialchars($logo['alt'], ENT_QUOTES, 'UTF-8') ?>"
                    loading="lazy"
                >
            </button>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<div class="clients-grid-lightbox" hidden>
    <button type="button" class="clients-grid-lightbox__close" aria-label="Fechar">&times;</button>
    <button type="button" class="clients-grid-lightbox__prev" aria-label="Anterior">&lsaquo;</button>
    <img class="clients-grid-lightbox__img" src="" alt="">
    <button type="button" class="clients-grid-lightbox__next" aria-label="Próximo">&rsaquo;</button>
</div>
