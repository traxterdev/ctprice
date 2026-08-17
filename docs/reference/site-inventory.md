# Inventário Estrutural — Site CT Price

## Metodologia

- **URL de referência:** https://ctprice.com.br/wp/
- **Ferramenta:** Chrome DevTools MCP — navegação real a cada destino (não apenas leitura de `href`), leitura de `document.title`, `location.href` final (após redirecionamentos), classes do `<body>` (`postid-`, `page-id-`, `single-post` etc.), `data-elementor-id` de cada página, e captura de tela quando necessário para confirmar a estrutura visual.
- **Data:** 2026-08-17
- **Escopo:** descoberta estrutural apenas — menu principal, submenus, footer e links institucionais/externos relevantes alcançáveis a partir da Home. **Nenhuma auditoria visual detalhada foi feita nas páginas internas** (isso fica para uma etapa futura, por página).
- Todo destino relevante listado abaixo foi **efetivamente aberto e confirmado**, não apenas inferido pela existência de um link.

### Contagem oficial de páginas reais

**13 páginas reais** no domínio `ctprice.com.br/wp/` (confirmadas por navegação direta, cada uma com URL própria e `data-elementor-id`/`postid` distinto):

- **10 páginas "site"** (Home + institucional + contato + página especial): Home, A CT Price, Clientes, Parceiros, Fale Conosco, Informações, Trabalhe Conosco, Ouvidoria, Depoimentos, Área Restrita.
- **3 posts de blog**: "Reforma trabalhista volta à pauta do STF...", "Receita Federal e Correios lançam portal...", "Novo golpe mira em empreendedores..." (slug `hello-world`).

**Não entram nesta contagem** (não são páginas com URL própria):
- `/wp/home/` — **alias/redirecionamento** para a Home (mesma página, mesmo `data-elementor-id=360`). Confirmado novamente nesta revisão: navegar para `/wp/home/` resulta em `location.href = https://ctprice.com.br/wp/` (a Home).
- **"Benefícios"** — **não é uma página**, é uma âncora HTML (`<div class="elementor-menu-anchor" id="beneficios">`) dentro do conteúdo da página `/wp/trabalhe-conosco/`. Confirmado nesta revisão: `document.querySelector('#beneficios')` só retorna elemento em `/wp/trabalhe-conosco/`; em `/wp/informacoes/` não existe; e `/wp/beneficios/` como URL própria **não existe** (404 — "Página não encontrada").
- O erro do resumo anterior (informar "12" e listar apenas 10) veio de contar as 10 páginas "site" mas esquecer de somar os 3 posts de blog no total apresentado — corrigido aqui para **13**.

---

## 1. Tabela de páginas e destinos

| Página | URL final | Tipo | Menu/Submenu | Estrutura aparente | Observações |
|---|---|---|---|---|---|
| Home | `https://ctprice.com.br/wp/` | Home | Item de topo ("Início" aponta para cá) | Elementor `id=360`, 13 seções de topo (topbar, header, hero-slider, 8 seções de conteúdo, form de contato, footer, footer bottom) | Referência já auditada em `home-desktop-audit.md`, `home-mobile-audit.md`, `home-tablet-audit.md` |
| Início | `https://ctprice.com.br/wp/home/` → **redireciona para** `/wp/` | Home (alias, **não contabilizado** como página própria) | Item de topo do menu | — | `/wp/home/` é só um apelido/redirect para a Home (mesmo `data-elementor-id=360`); não é uma página separada — reconfirmado nesta revisão |
| A CT Price | `https://ctprice.com.br/wp/sobre-nos/` | Institucional | Item de topo | Elementor `id=377`, 9 seções. Hero interno próprio ("CONFIE NA CT PRICE" + título sobre foto), tira de logos de clientes reduzida, CTA "Fale Conosco" | Slug (`sobre-nos`) não bate com o texto do menu ("A CT Price") — comum em WP, apenas registrado |
| Clientes | `https://ctprice.com.br/wp/clientes/` | Institucional (submenu) | Submenu de "Clientes e Parceiros" | Elementor `id=632`, 6 seções. Hero interno ("NOSSOS CLIENTES") + grade de logos **diferente** da que aparece na Home (ex.: Caiman Transportes, +Q Pão) | Conteúdo de logos não é o mesmo carrossel da Home — são listas distintas |
| Parceiros | `https://ctprice.com.br/wp/parcerias/` | Institucional (submenu) | Submenu de "Clientes e Parceiros" | Elementor `id=638`, 9 seções | Não auditado visualmente em detalhe nesta etapa |
| Fale Conosco | `https://ctprice.com.br/wp/fale-conosco/` | Contato/Formulário | Item de topo | Elementor `id=480`, 7 seções. Formulário com reCAPTCHA (`name`, `email`, `field_6bad421`, `message`) + **diretório de contatos por WhatsApp por departamento** (Comercial, Pessoal, Fiscal, Contábil, Central/Empresarial — 5 números distintos) | Página de contato mais completa do site |
| Informações | `https://ctprice.com.br/wp/informacoes/` | Institucional | Item de topo | Elementor `id=413`, 8 seções | **Não contém** o elemento âncora `id="beneficios"` (reconfirmado) — ver seção "Links quebrados" |
| Trabalhe Conosco (rodapé) | `https://ctprice.com.br/wp/trabalhe-conosco/` | Institucional | Rodapé (link próprio, texto "Trabalhe Conosco") | Elementor `id=431`, **16 seções** (a maior página do site) | **Contém** o elemento âncora `id="beneficios"` (`<div class="elementor-menu-anchor" id="beneficios">`, widget "Menu Anchor" do Elementor) — reconfirmado nesta revisão. "Benefícios" **não é uma página**, é só essa âncora dentro desta página; `/wp/beneficios/` não existe (404). É o destino correto do link "Benefícios", mas nenhum link do site aponta para cá corretamente (ver abaixo) |
| Vagas / Trabalhe Conosco (menu) | `https://recrutamento.ctprice.com.br/vagas` | **Sistema externo** | Item de topo "Trabalhe Conosco" (link do próprio item pai) + submenu "Vagas" | Aplicação separada: "CT Price - Gestão de Currículos", rodapé próprio "© 2026 CT Price... v1.0.0" | Visual completamente diferente do site institucional (SaaS de recrutamento white-label num subdomínio) |
| Ouvidoria | `http://ctprice.com.br/wp/ouvidoria` → **redireciona para** `https://ctprice.com.br/wp/ouvidoria/` | Institucional/Contato | Item de topo | Elementor `id=1345`, 8 seções, contém formulário | Único item de menu cujo `href` estava em `http://` (sem `s`) e sem barra final — funciona por redirecionamento automático do WordPress, mas é a única inconsistência desse tipo no menu |
| Depoimentos | `https://ctprice.com.br/wp/depoimentos/` | Institucional | Item de topo | Elementor `id=1839`, 8 seções | Não auditado visualmente em detalhe nesta etapa |
| Área Restrita | `https://ctprice.com.br/wp/arearestrita/` | Página especial (portal) | Botão dedicado no header (fora do menu principal) | Elementor `id=647`, 6 seções. Duas chamadas: "Clientes" → botão "Acessar"; "Colaboradores" → "Acesse aqui sua área restrita" | Não é um formulário de login — é uma página de **redirecionamento** para dois sistemas externos, ambos quebrados (ver seção "Links quebrados") |
| Blog — "Reforma trabalhista volta à pauta do STF..." | `https://ctprice.com.br/wp/reforma-trabalhista-volta-a-pauta-do-stf-julgamento-acontece-neste-mes/` | Conteúdo/Blog | Não está no menu — só acessível via card "Últimas notícias" na Home | `post-174`, template Elementor **compartilhado** `elementor-page-1049` (Theme Builder "Single Post"), 6 seções | Categoria: Folha de Pagamento |
| Blog — "Receita Federal e Correios lançam portal..." | `https://ctprice.com.br/wp/receita-federal-e-correios-lancam-portal-de-compras-internacionais/` | Conteúdo/Blog | Idem acima | `post-171`, mesmo template compartilhado | Categoria: Informativo |
| Blog — "Novo golpe mira em empreendedores..." | `https://ctprice.com.br/wp/hello-world/` | Conteúdo/Blog | Idem acima | **`post-1`** (o post de exemplo padrão "Hello World" do WordPress, nunca excluído — apenas teve título/conteúdo substituídos, mas manteve o slug/ID original) | Categoria: Informativo. Slug incoerente com o conteúdo real — resquício de instalação |

---

## 2. Links externos e sistemas separados (fora do site institucional)

| Link | Destino | Tipo | Onde aparece | Status confirmado |
|---|---|---|---|---|
| `https://recrutamento.ctprice.com.br/vagas` | Sistema de vagas/currículos ("CT Price - Gestão de Currículos") | Sistema externo (subdomínio, produto de terceiros) | Item de menu "Trabalhe Conosco" e submenu "Vagas" | **Funcional** — carrega normalmente, com 3 vagas listadas no momento da auditoria |
| `https://ctprice.com.br/documentos` | Portal de documentos (presumido pelo rótulo "Clientes → Acessar" na página Área Restrita) | Sistema externo (fora de `/wp/`) | Botão "Acessar" na página Área Restrita | **QUEBRADO** — retorna 404 puro de servidor (`"The resource requested could not be found on this server!"`), sem o layout do site |
| `https://ctprice.com.br/sh-admin` | Sistema para colaboradores (presumido: ERP/RH, possivelmente ligado ao software "Domínio" citado nas vagas) | Sistema externo (fora de `/wp/`) | Botão "Acesse aqui sua área restrita" (Colaboradores) na página Área Restrita | **QUEBRADO/EXPOSTO** — redireciona para `/sh-admin/` e retorna uma listagem de diretório crua do servidor ("Index of /sh-admin/"), sem nenhuma aplicação de login funcional visível |
| `https://ctprice.com.br/contato` | Provável página de contato alternativa (nunca publicada ou removida) | Página quebrada | Botão "Fale Conosco" da seção "Nossos Serviços" da **Home** (diferente do "Fale Conosco" do menu, que aponta corretamente para `/wp/fale-conosco/`) | **QUEBRADO** — 404 puro de servidor |
| `https://agencialester.com.br/` | Site da agência responsável pelo desenvolvimento | Link externo de terceiro (crédito no rodapé) | Rodapé, "Desenvolvido por Agência Lester" | Não carregou dentro do tempo limite (timeout) durante esta auditoria; fora do escopo institucional da CT Price, não é necessário para a reconstrução do site |
| `https://api.whatsapp.com/send?phone=...` | WhatsApp | Link externo padrão | Topbar de todas as páginas, botão flutuante, "Fale Conosco" | Funcional (padrão `wa.me`/`api.whatsapp.com`); **números inconsistentes entre páginas** — ver seção 4 |
| `https://goo.gl/maps/eYes1Vqbyzw6hBYy8` | Google Maps | Link externo padrão | Endereço no footer de todas as páginas | Funcional, link curto do Google |
| `mailto:contato@ctpricems.com.br` / `mailto:protecaodedados@ctpricems.com.br` | E-mail | Link externo padrão | Topbar e footer | Funcional (protocolo `mailto:`) |
| Bandeiras de idioma (GTranslate) | `#` (JS) | Widget de terceiros | Topbar de todas as páginas | Não navega — troca de idioma via JavaScript do plugin GTranslate, não são páginas |

---

## 3. Páginas ou URLs quebradas / com problema

1. **`https://ctprice.com.br/contato`** — 404 de servidor puro. Usado pelo botão "Fale Conosco" da seção "Nossos Serviços" da Home. O "Fale Conosco" do menu principal, em contraste, funciona (`/wp/fale-conosco/`).
2. **`https://ctprice.com.br/documentos`** — 404 de servidor puro. Destino do botão "Acessar" (Clientes) na página Área Restrita.
3. **`https://ctprice.com.br/sh-admin`** — não retorna 404, mas também não é uma aplicação funcional: mostra uma listagem crua de diretório do servidor ("Index of /sh-admin/"), sem página de login. Destino do link "Colaboradores" na página Área Restrita.
4. **Link "Benefícios" (menu principal, submenu de "Trabalhe Conosco")** — `href="#beneficios"` **relativo** (sem caminho de página). A âncora `id="beneficios"` só existe na página `/wp/trabalhe-conosco/`. Como o link é relativo, clicar em "Benefícios" a partir de **qualquer outra página** (inclusive a Home) resolve para `<página-atual>#beneficios`, uma âncora que não existe ali — o clique não leva a lugar nenhum na prática.
5. **Link "Benefícios" (rodapé)** — usa um `href` absoluto, mas aponta para `https://ctprice.com.br/wp/informacoes/#beneficios`, página que **também não contém** a âncora `id="beneficios"` (ela está em `/wp/trabalhe-conosco/`, não em `/wp/informacoes/`). Ou seja, existem **duas versões diferentes e ambas incorretas** do link "Benefícios" no site — nenhuma das duas aponta para onde a âncora de fato existe.
6. **`/wp/blog/`** — não existe; retorna a página 404 estilizada do próprio tema ("Página não encontrada"). **Não há nenhuma página de arquivo/listagem de blog** acessível pela navegação do site — os 3 posts só são alcançáveis individualmente pelos cards "Últimas notícias" da Home. Os selos de categoria ("FOLHA DE PAGAMENTO", "INFORMATIVO") não são links.
7. **Item de menu "Ouvidoria"** — único link do menu principal com `href="http://..."` (sem HTTPS) e sem barra final; funciona apenas graças ao redirecionamento automático do servidor/WordPress.

---

## 4. Componentes compartilhados (globais) — apenas existência, sem medição

| Componente | Presença confirmada | Observação estrutural importante |
|---|---|---|
| **Topbar** (endereço, telefones, WhatsApp, e-mail, seletor de idioma) | Em todas as páginas visitadas | **Não é um template compartilhado de verdade** — cada página tem sua própria cópia independente dessa seção no Elementor (IDs de elemento diferentes por página, confirmado comparando Home × Sobre Nós). Isso já causou uma divergência de conteúdo real: a Home usa o WhatsApp `(67) 99261-6117`, enquanto todas as demais páginas internas verificadas (Sobre Nós, Clientes, Parcerias, Fale Conosco, Área Restrita) usam `(67) 99232-4097` |
| **Header** (logo + menu + botão "Área Restrita") | Em todas as páginas visitadas | Mesma observação acima: cópia independente por página, não um template global do Elementor Theme Builder. O item de menu correspondente à página atual recebe um sublinhado verde de estado ativo (confirmado em "A CT Price") |
| **Menu principal / submenus** | Em todas as páginas visitadas | 8 itens + 2 submenus, idênticos em texto/ordem em todas as páginas — mas com pequenas divergências de destino entre cópias (ver seção 3, itens "Benefícios", e "Trabalhe Conosco" que aponta para o sistema externo no header mas para `/wp/trabalhe-conosco/` no rodapé) |
| **Footer** (logo, endereço, menu secundário, mapa incorporado) | Em todas as páginas visitadas | Mesmo padrão de cópia independente por página; conteúdo e links parecem consistentes entre as páginas checadas (endereço, e-mails, responsável técnico, mapa) |
| **Rodapé inferior** (copyright + crédito "Agência Lester") | Em todas as páginas visitadas | Consistente |
| **Aviso de cookies** | Em todas as páginas visitadas | Mesmo plugin/estilo em todas |
| **Botão flutuante do WhatsApp** | Confirmado nas páginas com screenshot tirado (Home, Sobre Nós, Clientes, Área Restrita) | Mesmo componente/posição |
| **"Hero" interno de página** (eyebrow + título grande sobre foto de fundo) | **Confirmado visualmente** em "A CT Price" e "Clientes" | Padrão visual claramente reutilizado entre páginas institucionais (eyebrow em caixa alta + H2 grande + foto full-width), mas **não confirmado individualmente** em Parcerias, Fale Conosco, Informações, Ouvidoria, Depoimentos, Trabalhe Conosco, Área Restrita — presumir o mesmo padrão nessas páginas exigiria abrir cada uma (fica para a etapa de auditoria visual) |
| **Formulário de contato (reCAPTCHA)** | Confirmado em: Home (seção de contato), Fale Conosco, Ouvidoria | Mesmo padrão de campos (nome/e-mail/telefone/mensagem + reCAPTCHA v3) reaproveitado nas 3 páginas com formulário |
| **Blocos reutilizados via template Elementor de verdade** | Só confirmado nos **posts de blog** | Os 3 posts de blog compartilham o mesmo template Elementor (`elementor-page-1049`, Theme Builder "Single Post") — é o único caso no site em que o Elementor está de fato reaproveitando uma estrutura, em vez de duplicar conteúdo por página |
| **Breadcrumb** | **Não encontrado** em nenhuma página verificada | Nenhuma página (institucional ou post de blog) exibe trilha de navegação |

---

## 5. Classificação por tipo

- **Home** (1): `/wp/` (com alias `/wp/home/`, não contado à parte)
- **Institucional** (6): Sobre Nós, Clientes, Parceiros, Informações, Depoimentos, Trabalhe Conosco (página do rodapé)
- **Contato/Formulário** (2): Fale Conosco, Ouvidoria (ambas têm formulário próprio); Home também tem formulário de contato embutido, mas já está contada como Home
- **Página especial** (1): Área Restrita (portal de redirecionamento para sistemas externos, ambos quebrados)
- **Conteúdo/Blog** (3): os 3 posts (sem página de arquivo/índice acessível)
- **Total de páginas reais: 1 + 6 + 2 + 1 + 3 = 13**
- **Benefícios**: não é uma página nem entra nesta contagem — é uma âncora (`#beneficios`) dentro da página "Trabalhe Conosco"
- **Sistemas externos** (fora da contagem acima): recrutamento.ctprice.com.br (funcional), ctprice.com.br/documentos (quebrado), ctprice.com.br/sh-admin (quebrado/exposto)
- **Links externos de terceiros** (fora da contagem acima): agencialester.com.br (crédito de desenvolvimento), Google Maps, WhatsApp, e-mails

---

## Observação metodológica final

Esta é uma auditoria de **descoberta estrutural**, não uma auditoria visual. Os números de seção, IDs do Elementor e presença de formulário/hero foram confirmados via DOM; **larguras, espaçamentos, tipografia e cores das páginas internas não foram medidos** — isso é trabalho para uma futura etapa "auditoria desktop/mobile" de cada página interna, seguindo o mesmo processo já aplicado à Home.
