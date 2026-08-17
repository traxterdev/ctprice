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
 * Nesta etapa (scaffold estrutural) os valores NÃO foram preenchidos, para não inventar dados.
 * A auditoria (docs/reference/site-inventory.md, seção 4) encontrou divergências reais entre
 * páginas do site atual para telefone/WhatsApp — cada campo abaixo está marcado com TODO
 * explicando o que falta confirmar antes de preencher.
 */

return [

    'razao_social' => null, // TODO: confirmar grafia oficial (site atual usa "CT Price Organização Contábil")

    'whatsapp_principal' => null,
    // TODO: DIVERGÊNCIA ENCONTRADA NA AUDITORIA — a Home usa (67) 99261-6117, enquanto todas as
    // demais páginas internas verificadas (Sobre Nós, Clientes, Parcerias, Fale Conosco, Área
    // Restrita) usam (67) 99232-4097. Confirmar número canônico com o cliente antes de preencher.
    // Ver docs/architecture-proposal.md, seção 14.1, item 1.

    'telefone_fixo' => null,
    // TODO: (67) 3313-7300 aparece no site atual — confirmar se é o número oficial antes de preencher.

    'endereco' => [
        'logradouro' => null,        // TODO: confirmar formatação oficial completa
        'bairro' => null,            // TODO
        'cep' => null,               // TODO
        'cidade' => null,            // TODO
        'uf' => null,                // TODO
        'google_maps_url' => null,       // TODO: link curto (goo.gl/maps/...)
        'google_maps_embed_url' => null, // TODO: URL de embed do iframe
    ],

    'emails' => [
        'contato' => null,         // TODO
        'protecao_dados' => null,  // TODO
    ],

    'responsavel_tecnico' => [
        'nome' => null,     // TODO
        'registro' => null, // TODO (ex.: CRC)
    ],

    'departamentos' => [
        // TODO: no site atual, a página "Fale Conosco" lista contatos próprios por departamento
        // (Comercial, Pessoal, Fiscal, Contábil, Central/Empresarial), cada um com telefone/WhatsApp
        // distinto. Confirmar todos os números antes de preencher esta lista.
    ],

    'redes_sociais' => [
        // TODO: nenhuma rede social foi identificada nas auditorias realizadas até o momento.
        // Confirmar com o cliente se existem perfis oficiais a incluir.
    ],

    'sistemas_externos' => [
        'recrutamento' => null,
        // TODO: https://recrutamento.ctprice.com.br/vagas no site atual — confirmar se é mantido.

        'area_restrita_clientes' => null,
        // TODO: destino atual (ctprice.com.br/documentos) está QUEBRADO (404) — aguardando URL correta.

        'area_restrita_colaboradores' => null,
        // TODO: destino atual (ctprice.com.br/sh-admin) está QUEBRADO/EXPOSTO — aguardando URL correta.

        'agencia_desenvolvimento' => null,
        // TODO: agencialester.com.br no site atual (crédito de rodapé) — confirmar se mantém.
    ],

    // Ano de copyright: calculado dinamicamente para não "envelhecer" como o site atual (que
    // mostra "© 2024" desatualizado). Não precisa de TODO — não é um dado a confirmar.
    'copyright_ano' => date('Y'),

];
