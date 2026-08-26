<?php
/**
 * config/company.php
 *
 * Fonte única dos dados globais da empresa (endereço, telefones, e-mails, redes sociais,
 * contatos por departamento, sistemas externos).
 *
 * Consumido por: includes/topbar.php, includes/header.php, includes/footer.php,
 * includes/whatsapp-button.php e pelo componente de formulário de contato (quando implementado).
 *
 * IMPORTANTE: nenhum destes dados deve ser digitado manualmente em nenhuma página ou include —
 * sempre ler a partir deste arquivo. Ver docs/architecture-proposal.md (seções 3 e 13).
 *
 * Consolidação de dados (2026-08-17): valores confirmados por inspeção direta do site atual
 * (Chrome DevTools MCP) em todas as 10 páginas institucionais e nos 3 posts de blog foram
 * preenchidos abaixo. Campos com divergência real entre páginas permanecem `null`/TODO — ver
 * o levantamento completo em docs/reference/global-data-conflicts.md antes de decidir um valor.
 */

return [

    // Confirmado sem divergência: idêntico no <title>/copyright de todas as páginas verificadas
    // e corroborado pela ficha do Google Maps (ver docs/reference/global-data-conflicts.md, seção 3).
    'razao_social' => 'CT Price Organização Contábil',

    // Definido pelo cliente como o WhatsApp oficial/canônico da CT Price (2026-08-17).
    // A auditoria original encontrou 3 números diferentes no site atual (topbar/botão flutuante
    // variando por página) — esse levantamento permanece registrado apenas como histórico em
    // docs/reference/global-data-conflicts.md, seção 1. Nenhum dos outros números é mais
    // candidato a valor deste campo.
    'whatsapp_principal' => [
        'numero' => '(67) 99261-6117',
        'url' => 'https://api.whatsapp.com/send?phone=5567992616117',
    ],

    // Confirmado sem divergência: idêntico no topbar de todas as 10 páginas institucionais e dos
    // 3 posts de blog verificados.
    'telefone_fixo' => '(67) 3313-7300',

    'endereco' => [
        // Confirmado sem divergência (logradouro/cidade/uf idênticos em todas as páginas e no
        // link do Google Maps).
        'logradouro' => 'R. José Antônio, 2.777',
        'cidade' => 'Campo Grande',
        'uf' => 'MS',

        'bairro' => null,
        // TODO: DIVERGÊNCIA CONFIRMADA — texto do site diz "Monte Castelo"; a URL do mapa
        // incorporado no footer usa "Vila Rosa Pires". Ver global-data-conflicts.md, seção 3.

        'cep' => null,
        // TODO: DIVERGÊNCIA CONFIRMADA — texto do site diz "79.010-190"; a URL do mapa
        // incorporado no footer usa "79002-400". Ver global-data-conflicts.md, seção 3.

        // Confirmado funcional (link aberto e verificado — resolve para a ficha do Google Maps
        // "CT Price Organização Contábil").
        'google_maps_url' => 'https://goo.gl/maps/eYes1Vqbyzw6hBYy8',

        // Registrado como está usado no site atual (dado funcional). O texto humano do endereço
        // embutido nesta URL diverge do texto exibido no topbar/footer — ver TODOs de bairro/cep acima.
        'google_maps_embed_url' => 'https://maps.google.com/maps?q=R.%20Jos%C3%A9%20Ant%C3%B4nio%2C%202777%20-%20Vila%20Rosa%20Pires%2C%20Campo%20Grande%20-%20MS%2C%2079002-400&t=m&z=15&output=embed&iwloc=near',
    ],

    // Confirmado sem divergência: idêntico no topbar/footer de todas as páginas verificadas.
    'emails' => [
        'contato' => 'contato@ctpricems.com.br',
        'protecao_dados' => 'protecaodedados@ctpricems.com.br',
    ],

    // Confirmado sem divergência: idêntico no footer de todas as páginas verificadas.
    'responsavel_tecnico' => [
        'nome' => 'Marcelo Barbosa da Silva',
        'registro' => 'CRC MS 7986-O',
    ],

    // Confirmado sem divergência: cada contato aparece uma única vez, só na página Fale Conosco
    // (diretório por departamento). Números em formato de telefone; usar com o prefixo 55 + DDD
    // ao montar o link de WhatsApp, como já é feito no site atual.
    'departamentos' => [
        'comercial' => [
            'label' => 'Comercial',
            'telefone' => '(67) 99232-4097',
        ],
        'pessoal' => [
            'label' => 'Pessoal',
            'telefone' => '(67) 3313-7301',
        ],
        'fiscal' => [
            'label' => 'Fiscal',
            'telefone' => '(67) 3313-7302',
        ],
        'contabil' => [
            'label' => 'Contábil',
            'telefone' => '(67) 3313-7304',
        ],
        'central_empresarial' => [
            'label' => 'Central/Empresarial',
            'telefone' => '(67) 3313-7300',
        ],
    ],

    // Investigado nesta revisão: nenhuma rede social oficial da CT Price foi encontrada em
    // nenhuma página do site atual. Os únicos links de redes sociais existentes no site (na
    // página Depoimentos) pertencem a clientes individuais citados nos depoimentos, não à CT
    // Price — não devem ser confundidos com este campo. Ver global-data-conflicts.md, seção 4.
    'redes_sociais' => [],

    'sistemas_externos' => [
        // Confirmado funcional (aberto nesta revisão — carrega normalmente, título
        // "CT Price - Gestão de Currículos").
        'recrutamento' => 'https://recrutamento.ctprice.com.br/vagas',

        'area_restrita_clientes' => null,
        // TODO: destino atual (ctprice.com.br/documentos) confirmado QUEBRADO (404 puro de
        // servidor) nesta revisão — não registrado como válido. Aguardando URL correta.

        'area_restrita_colaboradores' => null,
        // TODO: destino atual (ctprice.com.br/sh-admin) confirmado QUEBRADO/EXPOSTO (listagem
        // crua de diretório, sem aplicação funcional) nesta revisão — não registrado como válido.
        // Aguardando URL correta.

        'agencia_desenvolvimento' => null,
        // TODO: agencialester.com.br — tentativa de acesso resultou em timeout de navegação
        // nesta revisão (mesmo resultado da auditoria anterior); não confirmado como destino
        // válido, portanto não registrado.
    ],

    // Ano de copyright: calculado dinamicamente para não "envelhecer" como o site atual (que
    // mostra "© 2024" desatualizado). Não precisa de TODO — não é um dado a confirmar.
    'copyright_ano' => date('Y'),

];
