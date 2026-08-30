<?php
/**
 * config/jobs.php
 *
 * Vagas exibidas em `/trabalhe-conosco/` — conteúdo transcrito literalmente de
 * docs/reference/trabalhe-conosco-audit.md, seção 3.2 (as 3 vagas em aberto no site original).
 *
 * Array estático simples (mesmo espírito de config/clients.php/config/blog-posts.php) — não é um
 * schema de CMS. `requirements`/`differentials` são listas de itens (cada item, o texto literal
 * de um marcador "->" do original) em vez de um único bloco de texto com o marcador ASCII
 * embutido, para permitir renderizar uma lista HTML real (`<ul><li>`) em vez do texto solto do
 * WordPress — apenas uma mudança de formato de armazenamento, nenhuma palavra do conteúdo foi
 * reescrita ou resumida.
 *
 * O destino do CTA de candidatura (sistema externo de recrutamento) NÃO fica aqui — vem de
 * `config/company.php['sistemas_externos']['recrutamento']`, lido por `trabalhe-conosco/index.php`
 * e passado ao componente, para não duplicar essa URL em mais de um lugar (ver
 * components/jobs-section.php).
 *
 * Consumido por: trabalhe-conosco/index.php + components/jobs-section.php.
 */

return [
    [
        'title' => 'Analista Contábil',
        'requirements' => [
            'Atuar nas rotinas contábeis das empresas enquadradas nos regimes (Simples Nacional, Lucro Presumido e Lucro Real), lançamentos e análise de demonstrações contábeis, fechamento de balanço, conciliação de fornecedores, bancos, clientes, obrigações acessórias, entre outras atividades pertinentes ao cargo.',
        ],
        'differentials' => [
            'Conhecimento no sistema Domínio',
            'Experiência mínima de 1 ano em escritório de contabilidade',
            'Ser inscrito no CRC ou cursando superior em Ciências Contábeis',
        ],
    ],
    [
        'title' => 'Analista de Departamento Pessoal',
        'requirements' => [
            'Atuar nas rotinas de departamento pessoal de empresas enquadradas nos Regimes (Simples Nacional, Lucro Real e Lucro Presumido).',
            'Processamento de folha, férias, rescisões, acompanhamento de afastamentos, Sefip, DCTF WEB, e E-social, entre outras atividades pertinentes ao cargo.',
        ],
        'differentials' => [
            'Conhecimento no sistema Domínio',
            'Experiência mínima de 1 ano em escritório de contabilidade',
        ],
    ],
    [
        'title' => 'Analista Fiscal',
        'requirements' => [
            'Apuração fiscal das empresas de Regime Lucro Real e Presumido.',
            'Apuração dos impostos municipais, estaduais e federais, entrega de obrigações acessórias (EFD Fiscal., EFD Contábil, EFD REinf, DCTF), entre outras atividades pertinentes ao cargo.',
        ],
        'differentials' => [
            'Conhecimento no sistema Domínio',
            'Experiência mínima de 1 ano em escritório de contabilidade',
        ],
    ],
];
