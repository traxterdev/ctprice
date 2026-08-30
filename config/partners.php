<?php
/**
 * config/partners.php
 *
 * Fonte única e estática dos itens exibidos em `/parcerias/` — duas categorias reais, medidas
 * diretamente na página original (ver docs/reference/parcerias-audit.md): "tools" (11 botões de
 * acesso a sistemas/portais de login usados pelos clientes da CT Price — NÃO são parceiros de
 * negócio) e "companies" (51 empresas/instituições/softwares parceiros de fato).
 *
 * CMS adiado (decisão de escopo do projeto) — nesta fase os dados são estáticos, retornados por
 * este arquivo, no mesmo espírito de config/clients.php e config/company.php. Cada item tem
 * somente os campos realmente necessários: `name`, `image` (nome do arquivo, servido de
 * assets/images/partners/tools/ ou assets/images/partners/companies/ conforme a categoria),
 * `url` (destino externo — `null` quando o item não tem link, ver nota sobre `auditto.png`) e
 * `alt` (texto alternativo — nenhum dos 62 itens tinha `alt` no original; preenchido aqui com
 * base no nome identificado de cada ferramenta/parceiro, nunca deixado vazio).
 *
 * ORDEM: preservada exatamente como capturada na auditoria (mesma ordem em que os itens aparecem
 * no DOM da página original) — nenhum embaralhamento aqui (diferente de config/clients.php +
 * components/clients-grid-section.php, cujo embaralhamento diário é exclusivo daquele contexto e
 * não se aplica a Parceiros).
 *
 * PENDÊNCIAS registradas na auditoria (docs/reference/parcerias-audit.md, seção 3.2) — URLs
 * preservados EXATAMENTE como encontrados no original, sem invenção de destino, apenas marcados
 * abaixo para confirmação futura do cliente:
 *   - `logo-modelo.png` ("Modelo Consultoria"): nome real de empresa, mas o link aponta para o
 *     mesmo destino de autenticação Secullum do item "Ponto" — parece link trocado.
 *   - `agricon-nova-logo-00.png` ("Agricon Consultoria"): mesmo caso do item acima.
 *   - `tech-contratos.png` e `logo-econet4.jpg`: destinos idênticos (econeteditora.com.br) —
 *     pode ser intencional (duas linhas do mesmo fornecedor) ou duplicidade.
 *   - `Copia-de-Logo-atual-Story-22.png`: link aponta para chatgpt.com — não foi possível
 *     identificar a que parceiro real este item pertence; nome mantido genérico.
 *   - `NOVA-LOGO-CT-46.png`: link aponta para um subdomínio (`ctpricems.woulz.com`) de um
 *     serviço não identificado com certeza ("Woulz"); nome mantido como melhor inferência.
 *   - `auditto.png`: único item sem link algum no original — `url` aqui é `null` de propósito
 *     (ver components/logo-grid-section.php: item sem `url` é exibido como card não clicável,
 *     nunca com `href="#"`).
 */

return [
    'tools' => [
        ['name' => 'Email', 'image' => 'WebMail-3.png', 'url' => 'https://server4.acessocpanel.com.br:2096/', 'alt' => 'Email — acesso ao Webmail'],
        ['name' => 'Ponto', 'image' => 'PontoWeb-3.png', 'url' => 'https://autenticador.secullum.com.br/Authorization?response_type=code&client_id=3&redirect_uri=https://pontoweb.secullum.com.br/Auth', 'alt' => 'Ponto — acesso ao PontoWeb (Secullum)'],
        ['name' => 'Sindicatos e Acordos Coletivos', 'image' => 'Sindicatos-e-Acordo-Coletivos-3-scaled.png', 'url' => 'https://app.ineditta.com.br/auth/realms/Ineditta-prod/protocol/openid-connect/auth?client_id=logineditta&redirect_uri=https%3A%2F%2Fapp.ineditta.com.br%2Findex.php&state=13e93942-ed07-411a-98dc-93d90cbfc81f&response_mode=fragment&response_type=code&scope=openid&nonce=475ad9f0-16a2-453b-a793-42edd4cb27ec&code_challenge=YOHiVqFSYaB3cR0DB9L43xrFSOJ_EwxF__NlAyRi87Y&code_challenge_method=S256', 'alt' => 'Sindicatos e Acordos Coletivos — acesso ao Ineditta'],
        ['name' => 'Contra Cheque', 'image' => 'Contra-Cheque-Web-3.png', 'url' => 'https://centraldofuncionario.com.br/23321', 'alt' => 'Contra Cheque — Central do Funcionário'],
        ['name' => 'Gestão de Talentos', 'image' => 'Gestao-de-Talentos-Web-3.png', 'url' => 'https://recrutamento.ctprice.com.br/admin/login', 'alt' => 'Gestão de Talentos — acesso administrativo'],
        ['name' => 'Licenças e Alvarás', 'image' => 'ChatGPT-Image-20-de-ago.-de-2026-10_34_25.png', 'url' => 'https://app.propertydocs.com.br/Account/Login?ReturnUrl=%2F', 'alt' => 'Licenças e Alvarás — acesso ao PropertyDocs'],
        ['name' => 'Conexão Vip', 'image' => 'Conexao-Vip-3.png', 'url' => 'https://passport.nibo.com.br/Account/Login?ReturnUrl=%2Fauthorize%3Fresponse_type%3Dcode%26client_id%3DD2CBFE38-9803-4DA0-8E2C-4E67F26BA9F5%26redirect_uri%3Dhttps%253a%252f%252fempresa.nibo.com.br%252fAuth%252fCallback%253forigin%253d%2526returnUrl%253d%25252fOrganization%2526redirectEmail%253d', 'alt' => 'Conexão Vip — acesso ao Nibo (empresa)'],
        ['name' => 'Folha', 'image' => 'FolhaWeb-3.png', 'url' => 'https://www.dominioweb.com.br/', 'alt' => 'Folha — acesso ao Domínio Web'],
        ['name' => 'Open Finance', 'image' => 'Open-Finance-1.png', 'url' => 'https://passport.nibo.com.br/Account/Login?ReturnUrl=%2Fauthorize%3Fresponse_type%3Dcode%26client_id%3D6D3044D7-2C77-4D15-A443-6ED83614F2EE%26redirect_uri%3Dhttps%253a%252f%252fcontador.nibo.com.br%252fAuth%252fCallback%253freturnurl%253d%25252f%25253f_gl%25253d1%2A109o46r%2A_gcl_au%2ANDE0MDA5OTE3LjE3ODM2MzQ0Mzg', 'alt' => 'Open Finance — acesso ao Nibo (contador)'],
        ['name' => 'Hub de Informações', 'image' => 'HUB.png', 'url' => 'https://app.hubstrom.com/login', 'alt' => 'Hub de Informações — acesso ao Hubstrom'],
        ['name' => 'Hub do Cliente', 'image' => 'ChatGPT-Image-28-de-ago.-de-2026-11_10_14.png', 'url' => 'https://portal.hubstrom.com.br/login/@ctpricems', 'alt' => 'Hub do Cliente — portal Hubstrom CT Price'],
    ],
    'companies' => [
        ['name' => 'Santana Haddad Advogados', 'image' => 'logo-santana-e-haddad.png', 'url' => 'https://csh.adv.br/', 'alt' => 'Santana Haddad Advogados'],
        ['name' => 'Multiconsultores', 'image' => 'logo-multiconsultores.png', 'url' => 'https://multiconsultores.com.br/', 'alt' => 'Multiconsultores'],
        ['name' => 'Modelo Consultoria', 'image' => 'logo-modelo.png', 'url' => 'https://autenticador.secullum.com.br/Authorization?response_type=code&client_id=3&redirect_uri=https://pontoweb.secullum.com.br/Auth', 'alt' => 'Modelo Consultoria'],
        ['name' => 'Econet Editora', 'image' => 'logo-econet4.jpg', 'url' => 'http://www.econeteditora.com.br/', 'alt' => 'Econet Editora'],
        ['name' => 'Meu Atendimento (WhatsApp)', 'image' => 'imagem-whatsapp_69ed101b.jpg', 'url' => 'https://app.meuatendimento.chat/', 'alt' => 'Meu Atendimento — chat via WhatsApp'],
        ['name' => 'Agricon Consultoria', 'image' => 'agricon-nova-logo-00.png', 'url' => 'https://autenticador.secullum.com.br/Authorization?response_type=code&client_id=3&redirect_uri=https://pontoweb.secullum.com.br/Auth', 'alt' => 'Agricon Consultoria'],
        ['name' => 'Tech Contratos', 'image' => 'tech-contratos.png', 'url' => 'http://www.econeteditora.com.br/', 'alt' => 'Tech Contratos'],
        ['name' => 'Auditto', 'image' => 'auditto.png', 'url' => null, 'alt' => 'Auditto'],
        ['name' => '199 Offices', 'image' => 'logo-Transparente.png', 'url' => 'https://199offices.com.br/', 'alt' => '199 Offices'],
        ['name' => 'Onvio (Thomson Reuters)', 'image' => 'onvio4.png', 'url' => 'https://auth.thomsonreuters.com/u/login/identifier?state=hKFo2SBpY2RTOXNsclFBM0VLbXFrWk0xQ0p1UDVYZ2dOemdGU6Fur3VuaXZlcnNhbC1sb2dpbqN0aWTZIDJGYjRSd3hWWnFjOGVLT3BkRXhKb3RaTVZIZ0RkY2VYo2NpZNkgR0JVcFBwT1V3QmY0cGhTdjllSXFGMnhpOExTUHNrdEs', 'alt' => 'Onvio — Thomson Reuters'],
        ['name' => 'SIEG', 'image' => 'sieg_040407c7.png', 'url' => 'https://auth.sieg.com/', 'alt' => 'SIEG'],
        ['name' => 'SOC / SISAI', 'image' => 'sisai2.jpg', 'url' => 'https://sistema.soc.com.br/WebSoc/', 'alt' => 'SOC / SISAI'],
        ['name' => 'HostMF', 'image' => 'hostmf.png', 'url' => 'https://hostmf.com.br/', 'alt' => 'HostMF'],
        ['name' => 'Clicksign', 'image' => 'clicksign.png', 'url' => 'https://www.clicksign.com/', 'alt' => 'Clicksign'],
        ['name' => 'Agronota', 'image' => 'agronota.webp', 'url' => 'https://agronota.com.br/', 'alt' => 'Agronota'],
        ['name' => 'COFECI/CRECI', 'image' => 'creci.webp', 'url' => 'https://www.cofeci.gov.br/', 'alt' => 'COFECI/CRECI'],
        ['name' => 'CFC — Conselho Federal de Contabilidade', 'image' => 'logo-cfc.png', 'url' => 'https://cfc.org.br/', 'alt' => 'CFC — Conselho Federal de Contabilidade'],
        ['name' => 'CRC-MS', 'image' => 'logo-vertical-crcms-1.png', 'url' => 'https://crcms.org.br/', 'alt' => 'CRC-MS'],
        ['name' => 'CRC-MS — Serviços', 'image' => 'logo-ctb2.jpg', 'url' => 'https://servicos.crcms.org.br/spwms/STRT/login.aspx', 'alt' => 'CRC-MS — Serviços'],
        ['name' => 'Gov.br', 'image' => 'gov-br.jpg', 'url' => 'https://www.gov.br/pt-br', 'alt' => 'Gov.br'],
        ['name' => 'eSocial', 'image' => 'esocial-ok.png', 'url' => 'https://login.esocial.gov.br/login.aspx', 'alt' => 'eSocial'],
        ['name' => 'Registro.br', 'image' => 'registro-br2.jpg', 'url' => 'https://registro.br/', 'alt' => 'Registro.br'],
        ['name' => 'JUCEMS — Junta Comercial de MS', 'image' => 'images_1f89b602.jpg', 'url' => 'https://portalservicos.jucems.ms.gov.br/auth/realms/Portalservicos/protocol/openid-connect/auth?response_type=code&client_id=portalexterno&redirect_uri=https%3A%2F%2Fportalservicos.jucems.ms.gov.br%2FPortal%2Fpages%2Fprincipal.jsf&state=df8a1e4c-a2df-43fa', 'alt' => 'JUCEMS — Junta Comercial de Mato Grosso do Sul'],
        ['name' => 'Omie', 'image' => 'omie_62937cda.png', 'url' => 'https://www.omie.com.br/', 'alt' => 'Omie'],
        ['name' => 'Portal e-Fazenda MS', 'image' => 'portal-e-fazenda.png', 'url' => 'https://eservicos.sefaz.ms.gov.br/', 'alt' => 'Portal e-Fazenda MS'],
        ['name' => 'Portal eNFSe', 'image' => 'webviewer.jpg', 'url' => 'https://portalenfse.com.br/Login/Login', 'alt' => 'Portal eNFSe'],
        ['name' => 'Radar — Receita Federal', 'image' => 'consulta-radar.png', 'url' => 'https://servicos.receita.fazenda.gov.br/servicos/radar/consultaSituacaoCpfCnpj.asp', 'alt' => 'Radar — Consulta Receita Federal'],
        ['name' => 'Sintegra', 'image' => 'sintegra.png', 'url' => 'http://www.sintegra.gov.br/', 'alt' => 'Sintegra'],
        ['name' => 'Agência Lester', 'image' => 'logo01.png', 'url' => 'https://agencialester.com.br/', 'alt' => 'Agência Lester'],
        ['name' => 'Possiede Araújo Advogados', 'image' => 'WhatsApp-Image-2024-09-26-at-11.38.08-1-1-1024x383.jpeg', 'url' => 'https://www.possiedearaujo.com.br/', 'alt' => 'Possiede Araújo Advogados'],
        ['name' => 'Parceiro (a confirmar)', 'image' => 'Copia-de-Logo-atual-Story-22.png', 'url' => 'https://chatgpt.com/', 'alt' => 'Parceiro a confirmar'],
        ['name' => 'Consulta CPF/CNPJ — Receita Federal', 'image' => 'image-41.png', 'url' => 'https://servicos.receita.fazenda.gov.br/servicos/radar/consultasituacaocpfcnpj.asp', 'alt' => 'Consulta CPF/CNPJ — Receita Federal'],
        ['name' => 'Komunic', 'image' => 'image-42.png', 'url' => 'https://app.komunic.net/login', 'alt' => 'Komunic'],
        ['name' => 'Empregador Web (MTE)', 'image' => 'NOVA-LOGO-CT-5-e1745607345803.png', 'url' => 'https://servicos.mte.gov.br/empregador/#/login', 'alt' => 'Empregador Web — Ministério do Trabalho'],
        ['name' => 'WebCounter', 'image' => 'image-1.png', 'url' => 'https://app.webcounter.com.br/login', 'alt' => 'WebCounter'],
        ['name' => 'Makro System', 'image' => 'Makro-Web.png', 'url' => 'https://app.makrosystem.com.br/Login.aspx?status=logoff', 'alt' => 'Makro System'],
        ['name' => 'MakroWeb', 'image' => 'APP.jpeg', 'url' => 'https://app.makroweb.cnt.br/', 'alt' => 'MakroWeb'],
        ['name' => 'NFe — Fazenda', 'image' => 'nfe.png', 'url' => 'https://www.nfe.fazenda.gov.br/portal/consultaRecaptcha.aspx?tipoConsulta=resumo&tipoConteudo=7PhJ%20gAVw2g=&AspxAutoDetectCookieSupport=1', 'alt' => 'NFe — Portal da Nota Fiscal Eletrônica'],
        ['name' => 'RI Digital', 'image' => 'Marcelo-Barbosa-400-x-200-px-21.png', 'url' => 'https://ridigital.org.br/', 'alt' => 'RI Digital'],
        ['name' => 'Runrun.it', 'image' => 'Fatura-de-Servicos-Prestados-para-Personal-Trainer-Simples-e-Moderno-Cinza-Preto-e-Amarelo-2.png', 'url' => 'https://runrun.it/pt-BR', 'alt' => 'Runrun.it'],
        ['name' => 'Insirius', 'image' => 'Insirius.png', 'url' => 'https://sistema.insirius.com.br/users/sign_in', 'alt' => 'Insirius'],
        ['name' => 'SOS Reforma', 'image' => 'SOS.png', 'url' => 'https://www.sosreforma.com.br/', 'alt' => 'SOS Reforma'],
        ['name' => 'DET — Domicílio Eletrônico Trabalhista', 'image' => 'NOVA-LOGO-CT-42.png', 'url' => 'https://det.sit.trabalho.gov.br/login?r=%2Fservicos', 'alt' => 'DET — Domicílio Eletrônico Trabalhista'],
        ['name' => 'FGTS Digital', 'image' => 'NOVA-LOGO-CT-43.png', 'url' => 'https://fgtsdigital.sistema.gov.br/portal/login', 'alt' => 'FGTS Digital'],
        ['name' => 'DocNuvem', 'image' => 'Logo-DocNuvem.png', 'url' => 'https://ctprice.docnuvem.com.br/sistema/login/Index', 'alt' => 'DocNuvem'],
        ['name' => 'Confere Leão', 'image' => 'Confere-Leao.png', 'url' => 'https://confereleao.com.br/', 'alt' => 'Confere Leão'],
        ['name' => 'Woulz', 'image' => 'NOVA-LOGO-CT-46.png', 'url' => 'https://ctpricems.woulz.com/', 'alt' => 'Woulz'],
        ['name' => 'Portal do Consumidor — Tributos.gov', 'image' => 'ChatGPT-Image-24-de-jul.-de-2026-16_47_33.png', 'url' => 'https://consumo.tributos.gov.br/', 'alt' => 'Portal do Consumidor — Tributos.gov'],
        ['name' => 'iMídia TV', 'image' => 'ImidiaTV.png', 'url' => 'https://imidiatv.com.br/', 'alt' => 'iMídia TV'],
        ['name' => 'Tech67', 'image' => 'logo.png', 'url' => 'https://app.tech67.com.br/', 'alt' => 'Tech67'],
        ['name' => 'Emitte Contábil', 'image' => 'logo_emitte_b9910a38a1.png', 'url' => 'https://emitte.com.br/contabil/', 'alt' => 'Emitte Contábil'],
    ],
];
