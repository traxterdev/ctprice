<?php
/**
 * config/blog-posts.php
 *
 * Fonte compartilhada dos 3 posts de blog exibidos em "Últimas notícias" — extraída de index.php
 * (Home) para evitar duplicar o mesmo bloco de dados em `/informacoes/`, que exibe exatamente os
 * mesmos 3 posts e os mesmos destinos (confirmado por auditoria:
 * docs/reference/informacoes-audit.md, seção 3.2 — apresentação visual diferente no original,
 * mas conteúdo/links 100% idênticos).
 *
 * Consumido por: index.php (Home), informacoes/index.php — ambos junto com
 * components/blog-section.php. Não é um sistema de conteúdo genérico nem CMS — apenas um array
 * estático movido para um único lugar.
 */

return [
    'heading' => 'Últimas notícias',
    'posts' => [
        [
            'image' => BASE_URL . '/assets/images/blog/blog03-300x155.webp',
            'category' => 'FOLHA DE PAGAMENTO',
            'title' => 'Reforma trabalhista volta à pauta do STF; julgamento acontece neste mês',
            'excerpt' => 'Julgamento será retomado sobre a validade de contrato de trabalho intermitente.',
            // Original: https://ctprice.com.br/wp/reforma-trabalhista-volta-a-pauta-do-stf-julgamento-acontece-neste-mes/
            // Sem defeito conhecido documentado (ao contrário do CTA de services-section) e a página
            // de post da nova arquitetura ainda não existe — link reproduzido como no original.
            'url' => 'https://ctprice.com.br/wp/reforma-trabalhista-volta-a-pauta-do-stf-julgamento-acontece-neste-mes/',
            'date' => 'agosto 2, 2024',
            'time' => '17:01',
        ],
        [
            'image' => BASE_URL . '/assets/images/blog/blog02-300x155.webp',
            'category' => 'INFORMATIVO',
            'title' => 'Receita Federal e Correios lançam portal de compras internacionais',
            'excerpt' => 'Ferramenta tem como objetivo auxiliar consumidores em questões de importação, desde o rastreamento até a prevenção de fraudes.',
            'url' => 'https://ctprice.com.br/wp/receita-federal-e-correios-lancam-portal-de-compras-internacionais/',
            'date' => 'agosto 2, 2024',
            'time' => '16:59',
        ],
        [
            'image' => BASE_URL . '/assets/images/blog/blog01-300x155.webp',
            'category' => 'INFORMATIVO',
            'title' => 'Novo golpe mira em empreendedores e cria sites falsos que simulam a geração de documentos',
            'excerpt' => 'Receita Federal alerta empresários sobre os sites falsos e diz que já está tomando as medidas cabíveis para tirá-los do ar.',
            // Slug "hello-world" é o post de exemplo padrão do WordPress, nunca excluído no
            // original (site-inventory.md, seção 2) — reproduzido como está, não é um link quebrado.
            'url' => 'https://ctprice.com.br/wp/hello-world/',
            'date' => 'julho 29, 2024',
            'time' => '13:53',
        ],
    ],
];
