<?php
/**
 * config/menu.php
 *
 * Estrutura única do menu do site — fonte usada por includes/header.php e pela parte de
 * includes/footer.php que replica o menu secundário.
 *
 * Reflete a estrutura real confirmada em docs/reference/site-inventory.md (tabela da seção 1
 * e menu principal descrito em docs/reference/home-desktop-audit.md, seção 2.2): 8 itens de
 * topo, 2 deles com submenu, mais o botão "Área Restrita" fora do menu principal.
 *
 * IMPORTANTE: esta é a ÚNICA estrutura de navegação do projeto — nenhum link de menu deve ser
 * escrito manualmente em nenhuma página. Isso é o que elimina, por construção, a divergência de
 * destinos encontrada no site atual (ex.: "Trabalhe Conosco" apontando para lugares diferentes
 * no header e no footer).
 *
 * URLs usam a nova estrutura sem "/wp/" definida em docs/architecture-proposal.md (seção 10).
 * Onde o destino real ainda é incerto/quebrado no site atual, o valor fica marcado com TODO
 * em vez de ser inventado — ver docs/architecture-proposal.md, seção 14.1.
 */

return [

    'primary' => [
        [
            'label' => 'Início',
            'url' => '/',
        ],
        [
            'label' => 'A CT Price',
            'url' => '/sobre-nos/',
        ],
        [
            'label' => 'Clientes e Parceiros',
            'url' => '#',
            // No site atual este item pai não navega, só abre o submenu (href="#") — comportamento
            // preservado aqui (classificado como B — comportamento a preservar — em
            // docs/architecture-proposal.md, seção 2).
            'children' => [
                ['label' => 'Clientes', 'url' => '/clientes/'],
                ['label' => 'Parceiros', 'url' => '/parcerias/'],
            ],
        ],
        [
            'label' => 'Fale Conosco',
            'url' => '/fale-conosco/',
        ],
        [
            'label' => 'Informações',
            'url' => '/informacoes/',
        ],
        [
            'label' => 'Trabalhe Conosco',
            'url' => null,
            // TODO: DIVERGÊNCIA ENCONTRADA NA AUDITORIA — no site atual, o item pai do header aponta
            // para o sistema externo de recrutamento (https://recrutamento.ctprice.com.br/vagas),
            // enquanto o link equivalente no footer aponta para a página institucional
            // /trabalhe-conosco/. Confirmar destino único com o cliente antes de preencher.
            // Ver docs/architecture-proposal.md, seção 14.1, item 2.
            'children' => [
                [
                    'label' => 'Vagas',
                    'url' => null,
                    // TODO: mesmo destino do item pai "Trabalhe Conosco" — a confirmar.
                ],
                [
                    'label' => 'Benefícios',
                    'url' => '/trabalhe-conosco/#beneficios',
                    // TODO: no site atual, nenhuma das duas versões existentes do link "Benefícios"
                    // funciona de fato (nem a do menu — href relativo "#beneficios" — nem a do
                    // footer, que aponta para /informacoes/#beneficios, página sem essa âncora).
                    // A âncora real existe em /trabalhe-conosco/#beneficios (confirmado em
                    // docs/reference/site-inventory.md). Valor aqui já reflete a correção proposta
                    // classificada como defeito C em docs/architecture-proposal.md — confirmar com
                    // o cliente antes de publicar.
                ],
            ],
        ],
        [
            'label' => 'Ouvidoria',
            'url' => '/ouvidoria/',
        ],
        [
            'label' => 'Depoimentos',
            'url' => '/depoimentos/',
        ],
    ],

    // Botão "Área Restrita" — fora do menu principal, exibido separadamente no header
    // (ver docs/reference/site-inventory.md e docs/reference/home-desktop-audit.md, seção 2.2).
    'area_restrita' => [
        'label' => 'Área Restrita',
        'url' => '/arearestrita/',
    ],

];
