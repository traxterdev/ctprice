# Auditoria visual — `/informacoes/`

Data: 2026-08-30
Referência: `https://ctprice.com.br/wp/informacoes/` (`data-elementor-id="413"`, `page-id-413`,
confirmado em `docs/reference/site-inventory.md`)
Documentação-base: `CLAUDE.md`, `docs/reference/reference-baseline.md`,
`docs/reference/site-inventory.md`, `docs/reference/parcerias-audit.md`,
`docs/reference/parcerias-final-validation.md`, `docs/reference/fale-conosco-final-validation.md`

**Escopo desta etapa: SOMENTE auditoria.** Nenhum arquivo de implementação foi criado ou
alterado. Nenhum asset foi baixado (o único já existente, `informacoes.jpg`, não foi
re-baixado).

Viewports inspecionados: 1440×900, 900×1200, 768×1024, 767×1024, 390×844.

---

## Achado principal (não presumir pelo nome da página)

Ao contrário do que o nome "Informações" sugere (central de documentos/atalhos), a página **não
contém nenhum card, botão, download, PDF, planilha, accordion, tab ou link exclusivo de
conteúdo**. Ela é uma composição de **conteúdo já existente em outras páginas do próprio site**,
reaproveitado verbatim, mais um Hero próprio:

1. Hero próprio (usa `informacoes.jpg`, já presente no projeto).
2. Um bloco de "Últimas notícias" com os **mesmos 3 posts de blog já usados na Home**, mas com uma
   apresentação visual diferente (widget "Posts" padrão do Elementor, não o card customizado da
   Home).
3. A seção "Dedicação aos resultados e Compromisso com nossos clientes" — **texto, HTML e CTA
   100% idênticos** ao já implementado em `/sobre-nos/` (`components/image-content-cta-section.php`),
   diferindo **apenas na foto**.
4. O mesmo carrossel de logos de clientes/parceiros já usado na Home e em `/sobre-nos/` (mesmo
   container 1200px, mesmo `object-fit:fill` sem correção, mesmos 3 logos quebrados).

Nenhum conteúdo textual, link ou asset exclusivo de "informações" (no sentido de documentos/
orientações) foi encontrado.

---

## 1. Estrutura completa

**8 seções de topo** confirmadas dentro de `.elementor.elementor-413` (mesma contagem já
registrada em `site-inventory.md`), na ordem:

| # | `data-id` | Função | Altura (1440×900) |
|---|---|---|---|
| 0 | `3a5ebafc` | Topbar | 66px |
| 1 | `30c2daec` | Header (logo + menu) | 132px |
| 2 | `1f17ae7` | Hero ("mantenha-se bem informado") | 400px |
| 3 | `1228abc` | "Últimas notícias" (3 posts, widget Elementor "Posts") | 1119px |
| 4 | `bc912be` | "Dedicação..." (imagem + heading + texto + CTA, gradiente) | 480px |
| 5 | `2dd98198` | Carrossel de logos de clientes/parceiros | 200px |
| 6 | `692ee56f` | Footer (logo, endereço, menu, mapa) | 400px |
| 7 | `699a5581` | Bottom bar (copyright) | 78px |

Nenhuma seção de cards/downloads/accordion existe. Topbar/Header/Footer/Bottom bar são as mesmas
cópias já auditadas/implementadas globalmente — não reauditadas em detalhe aqui, exceto pelos
dados específicos desta página (idênticos aos já vistos em `/fale-conosco/`, ver §11).

---

## 2. Hero

**Classificação: A — reutilização direta de `components/boxed-hero.php`** (não
`internal-hero.php` — estrutura, altura e tipografia batem com o padrão `boxed-hero`, não com o
padrão de coluna assimétrica do `internal-hero`).

| Propriedade | Medido | Igual a `boxed-hero.php`? |
|---|---|---|
| Altura | 400px em todos os 5 viewports (sem breakpoint próprio) | Sim |
| Container | `.e-con-inner`, `max-width: min(100%, 1140px)` → 1140px | Sim |
| Estrutura textual | Dois `<h2>`: eyebrow "mantenha-se bem informado" + título "Acompanhe nossas novidades", sem `<strong>` de destaque parcial | Sim |
| Background | `url(.../informacoes.jpg)` | Mecanismo idêntico |
| `background-size` | `cover` | Sim |
| **`background-position`** | **`0% 0%`** | Igual ao já usado em `/parcerias/` (mesma imagem, mesmo enquadramento) |
| Tipografia eyebrow | Roboto 700, 20px, `rgb(0,34,44)`, uppercase | Idêntico |
| Tipografia título | Roboto 700, 30px, `rgb(5,112,56)` | Idêntico |
| Alinhamento | Esquerda | Idêntico |
| Responsividade | Sem mudança de altura/tipografia em nenhum viewport testado | Idêntico |

**Confirmado explicitamente**: `assets/images/pages/informacoes/informacoes.jpg` (já presente no
projeto, adicionado durante a implementação de `/parcerias/`) é de fato o **Hero desta página no
site original** — mesma imagem, mesmo `background-position:0% 0%` já usado em `/parcerias/`. Não
é necessário baixar nada; o asset já existente pode ser reaproveitado diretamente. Nenhum
modificador novo é necessário em `boxed-hero.php` — o prop `background_position` já existe e já
cobre exatamente este caso.

---

## 3. Conteúdo informativo

### 3.1 Hero
- Eyebrow: "mantenha-se bem informado"
- Título: "Acompanhe nossas novidades"

### 3.2 "Últimas notícias" (mesmos 3 posts da Home, sem heading próprio de seção — o Hero acima
funciona como introdução visual)

| Post | Data | Excerto |
|---|---|---|
| "Reforma trabalhista volta à pauta do STF; julgamento acontece neste mês" | agosto 2, 2024 | "Julgamento será retomado sobre a validade de contrato de trabalho intermitente." |
| "Receita Federal e Correios lançam portal de compras internacionais" | agosto 2, 2024 | "Ferramenta tem como objetivo auxiliar consumidores em questões de importação, desde o rastreamento até a prevenção de fraudes." |
| "Novo golpe mira em empreendedores e cria sites falsos que simulam a geração de documentos" | julho 29, 2024 | "Receita Federal alerta empresários sobre os sites falsos e diz que já está tomando as medidas cabíveis para tirá-los do ar." |

Sem categoria/badge visível nesta apresentação (diferente da Home, que mostra "FOLHA DE
PAGAMENTO"/"INFORMATIVO"). Cada card: thumbnail no topo, título (`<h3>`), data, excerto, link
"Leia mais »".

### 3.3 "Dedicação..." — **texto idêntico ao já implementado em `/sobre-nos/`**

- Heading: "**Dedicação** aos resultados e **Compromisso** com nossos clientes." (mesmo HTML,
  mesmos `<span style="color:#10E36B;font-weight:bold">`, confirmado caractere a caractere contra
  `sobre-nos/index.php`).
- Parágrafo 1: "Temos um **compromisso** com os resultados excepcionais e total dedicação ao
  sucesso dos **nossos clientes**."
- Parágrafo 2: "**Trabalhamos incansavelmente** para atender suas necessidades e superar
  expectativas, garantindo que cada detalhe seja tratado com o **máximo cuidado e eficiência**."
- CTA: "Fale Conosco" → `https://ctprice.com.br/contato` (mesmo link quebrado já classificado
  como defeito C em `/sobre-nos/`).

**Única diferença de conteúdo real: a foto** — aqui `pexels-thepaintedsquare-583847-1024x683.jpg`
(laptop + celular + caderno sobre mesa de madeira), em `/sobre-nos/` é `01-1024x684.jpg` (outra
foto, mesmas dimensões aproximadas).

### 3.4 Carrossel de logos
Sem heading/texto próprio — segue diretamente após a seção "Dedicação".

Nenhum outro texto auxiliar, orientação ou categoria foi encontrado em toda a página.

---

## 4. Links e recursos externos

Todos os links da página são os globais (topbar/header/footer, já auditados em
`/fale-conosco/`) **mais** os 3 links dos posts de blog e o CTA "Fale Conosco" da seção
Dedicação. Nenhum link exclusivo de "central de informações" existe.

| Texto | URL | `target` | Categoria | Funciona? |
|---|---|---|---|---|
| Título/thumbnail/"Leia mais »" dos 3 posts (9 links, 3 destinos únicos) | `ctprice.com.br/wp/reforma-trabalhista-.../`, `.../receita-federal-e-correios.../`, `.../hello-world/` | — | Página interna (posts de blog) | Sim (200) |
| "Fale Conosco" (Dedicação) | `https://ctprice.com.br/contato` | — | Página quebrada | **Não — 404** (já classificado como defeito C na auditoria de `/sobre-nos/`) |
| WhatsApp (topbar + flutuante) | `https://api.whatsapp.com/send?phone=5567992324097` | — | Sistema externo | Sim |
| E-mails (topbar/footer) | `mailto:contato@...`, `mailto:protecaodedados@...` | — | E-mail | Sim |
| Endereço (footer) | `https://goo.gl/maps/eYes1Vqbyzw6hBYy8` | `_blank` | Site externo (Google Maps) | Sim |
| Menu/submenu completos (Início, A CT Price, Clientes, Parceiros, Fale Conosco, Informações, Trabalhe Conosco/Vagas, Benefícios, Ouvidoria, Depoimentos) | ver `site-inventory.md` | variado | Páginas internas/sistema externo | Já auditados, sem novidade nesta página |
| "Agência Lester" (footer) | `https://agencialester.com.br/` | — | Site externo (crédito) | Já auditado |

**Nenhum link exclusivo desta página** (download, PDF, planilha, portal) foi encontrado — a
"central de informações" não existe estruturalmente.

---

## 5. Downloads e documentos

**Nenhum encontrado.** Nenhuma requisição de rede para PDF/DOCX/XLSX/ZIP ou qualquer outro tipo
de arquivo para download foi observada em nenhum dos 5 viewports. A página não contém nenhum
elemento (link, botão, ícone) que sugira um documento para baixar.

---

## 6. Cards, botões ou atalhos

**Nenhum card/atalho exclusivo de "informações" existe.** Os únicos elementos com aparência de
"card" são:

- **Cards de post** (widget Elementor "Posts", 3 itens): thumbnail 300×155 (`object-fit`
  implícito do WordPress, formato `.webp`), título `<h3>` cinza (`rgb(84,89,95)`) 18px/600,
  data 14px cinza claro, excerto, link "Leia mais »" verde (`rgb(97,206,112)`) — sem borda, sem
  sombra, sem `border-radius`, sem ícone. Grid 2 colunas (545px cada, `gap:35px 30px`) em
  desktop/tablet, 1 coluna em mobile — 3 itens ficam 2+1 (segundo card da segunda linha vazio).
- **Botão "Fale Conosco"** da seção Dedicação: `#61CE70` (`--color-off-white` no texto? não —
  texto `rgb(8,64,32)`, fundo `rgb(97,206,112)`), `elementor-animation-bounce-in` no hover (biblioteca
  de animações do Elementor).

**Qualidade visual objetiva**: os cards de post desta página são visualmente **mais pobres** que
os da Home (`components/blog-section.php`, já implementado): sem selo de categoria colorido, thumbnail
menor com `aspect-ratio` diferente, tipografia mais genérica (skin padrão "Classic" do widget
Elementor Posts). Como o conteúdo é **exatamente o mesmo** (mesmos 3 posts), esta é uma
inconsistência de apresentação dentro do próprio site original — uma boa oportunidade de
melhoria (reaproveitar `blog-section.php`, já auditado/implementado, em vez de reproduzir a
apresentação mais pobre desta página) — apenas registrado, decisão para a implementação.

---

## 7. Ícones

Nenhum ícone de biblioteca (Font Awesome/SVG custom) exclusivo desta página. Os únicos ícones
presentes são os globais já auditados (WhatsApp flutuante, bandeiras de idioma, ícones do
topbar). Nenhuma nova biblioteca de ícones é necessária.

---

## 8. Responsividade

Breakpoints reais medidos (nenhum presumido):

| Elemento | Desktop (≥1024px) | Tablet (768–1023px) | Mobile (≤767px) |
|---|---|---|---|
| Hero | 400px, sem mudança | idêntico | idêntico (sem breakpoint próprio, confirmado em 390px) |
| Grid de posts | 2 colunas (545px) | 2 colunas (407,5px em 900px; 341,5px em 768px) | **1 coluna** (confirmado exatamente em 767px, `712px`/`335px` conforme largura) |
| Seção Dedicação | 2 colunas (imagem+texto) | 2 colunas | **1 coluna** (empilha em 767px, mesmo breakpoint) |
| Carrossel | `slidesPerView:10` | `slidesPerView:2` (confirmado em 900px e 768px) | `slidesPerView:1` (confirmado em 767px e 390px) |

**Breakpoint de conteúdo confirmado em exatamente 767px** (testado 767 vs 768) — mesmo valor já
usado em todo o projeto, não presumido. O carrossel já usa breakpoints próprios do Elementor
(`slidesPerView` 10→2→1) diferentes dos breakpoints de conteúdo da página, mas **idênticos aos já
confirmados para o mesmo carrossel na Home** (não uma configuração nova desta página).

Nenhum overflow horizontal (`scrollWidth === clientWidth`) em nenhum dos 5 viewports.

---

## 9. Interações

| Interação | Resultado |
|---|---|
| Hover nos títulos/`Leia mais »` dos posts | Mudança de cor simples (padrão do link, sem transform) |
| Hover no botão "Fale Conosco" | `elementor-animation-bounce-in` (biblioteca de animação do Elementor — já substituída por CSS próprio simples em `/sobre-nos/`, mesmo padrão a seguir aqui) |
| Carrossel — autoplay | `true` |
| Carrossel — loop | `true` |
| Animações de entrada por scroll | **Nenhuma** (`elementor-invisible`/`data-settings` de animação: 0 elementos) |
| Accordion/tabs/modal/lightbox | **Nenhum** — não existem nesta página |

---

## 10. Segurança dos links

| Link | `target="_blank"`? | `rel="noopener noreferrer"`? | Classificação |
|---|---|---|---|
| Endereço (Google Maps, footer) | Sim | Só `noopener` (sem `noreferrer`) | B — mesmo padrão já visto/aceito em todas as páginas já auditadas, não específico desta página |
| Posts de blog, CTA Dedicação, menu | Não usam `target="_blank"` (navegação normal) | N/A | A |
| WhatsApp/e-mail | Não usam `target="_blank"` | N/A | A |

**Links quebrados/suspeitos**:
- `https://ctprice.com.br/contato` (CTA "Fale Conosco" da Dedicação) — **404**, já classificado
  como defeito conhecido categoria **C** em `docs/architecture-proposal.md`/`sobre-nos-audit.md`.
  Não é uma novidade desta auditoria, apenas reconfirmado aqui no mesmo texto reaproveitado.

Nenhum endpoint administrativo, HTTP sem HTTPS, ou URL claramente maliciosa foi encontrado nesta
página.

---

## 11. Dados globais × `config/company.php`

Idênticos aos já confirmados em `docs/reference/fale-conosco-final-validation.md`/
`global-data-conflicts.md` — nenhuma divergência nova:

| Dado | Nesta página | `config/company.php` | Divergência? |
|---|---|---|---|
| Endereço/CEP/bairro | "Monte Castelo", "79.010-190" (footer) | `null`/`null` (pendente, já registrado) | Confirma o TODO já existente |
| WhatsApp da topbar | `(67) 99232-4097` | Canônico `(67) 99261-6117` | Já documentado (não novo) |
| E-mails | Idênticos | Idênticos | Não |
| Google Maps embed | Idêntico | Idêntico | Não |

Nenhum departamento, telefone adicional ou rede social exclusivo desta página foi encontrado.

---

## 12. Assets exclusivos

| Asset | URL original | Formato | Dimensões | Uso | Classificação |
|---|---|---|---|---|---|
| `informacoes.jpg` | `.../wp-content/uploads/2024/09/informacoes.jpg` | JPG | 1200×600 (já confirmado em `parcerias-audit.md`) | Background do Hero | **Já existente no projeto** (`assets/images/pages/informacoes/informacoes.jpg`) — confirmado nesta auditoria como pertencente ao Hero desta página; **não baixado novamente** |
| `pexels-thepaintedsquare-583847-1024x683.jpg` | `.../wp-content/uploads/2024/09/pexels-thepaintedsquare-583847-1024x683.jpg` | JPG | 1024×683 (via atributos `width`/`height`) | Imagem da seção Dedicação | **Novo, necessário** para implementação |
| `blog03/02/01-300x155.webp` | já usados na Home | WEBP | 300×155 | Thumbnails dos 3 posts | **Já existente/compartilhável** (mesmos arquivos de `assets/images/blog/` já usados no projeto) |
| 82 logos do carrossel | já usados na Home/Sobre Nós | diversos | diversos | Carrossel de clientes | **Já existente/compartilhável** (`config/clients.php`) |
| `mv.jpg`, `modelo.jpg`, `logo_0020_Camada16.jpg` | — | — | — | Logos do carrossel | **Quebrados (404)** — mesmos 3 já excluídos de `config/clients.php`, confirmado idêntico, não reproduzir |

**Nenhum asset novo exclusivo de "informações" (documento/planilha/ícone) existe** — o único
asset genuinamente novo necessário para implementar esta página é a foto da seção Dedicação
(`pexels-thepaintedsquare-583847-1024x683.jpg`), ainda não baixada.

---

## 13. Qualidade visual

- **Cards de post inconsistentes com a Home**: mesma informação (3 posts), apresentação visual
  mais pobre (sem selo de categoria, thumbnail proporção diferente, tipografia genérica do
  widget padrão do Elementor) — ver §6.
- **Gap grande entre o Hero e a grade de posts**: medido no site original — não há heading
  "Últimas notícias" introduzindo a seção (diferente da Home, que tem esse H2), o que deixa a
  transição um pouco abrupta visualmente (mitigado apenas pelo espaço em branco).
  - Nota: nesta auditoria isso é só um registro de percepção visual, não uma medição de defeito
    técnico.
- **object-fit:fill no carrossel**: mesmo defeito já conhecido e já corrigido na implementação
  da Home/Sobre Nós (logos esticados em vez de `contain`) — se reaproveitar o componente já
  implementado (`clients-carousel-section.php`), este problema já vem resolvido.
- **Card 2+1 desalinhado**: com apenas 3 posts em um grid de 2 colunas, o terceiro card fica
  sozinho à esquerda da segunda linha, deixando um espaço vazio à direita — layout um pouco
  desequilibrado visualmente.
- Nenhum problema de contraste, hover exagerado ou área clicável pequena foi encontrado além do
  já registrado/corrigido em outras páginas.

---

## 14. Acessibilidade

- Hierarquia de headings: H2 (Hero) → H2 (Hero) → H3×3 (posts) → H2 (Dedicação) — sem H1 na
  página (mesmo padrão já presente em todas as páginas do site, não específico desta).
- 9 de 96 imagens sem `alt`: logo do header/footer (2×, já com `alt` correto na implementação
  global), 3 thumbnails de post (decorativos, ao lado de título linkado — padrão aceitável), 1
  foto da Dedicação (decorativa), 3 logos quebrados (irrelevantes, não reproduzidos).
- Links dos posts têm nome acessível claro (título completo do post).
- Nenhum uso indevido de elemento puramente visual como interativo foi encontrado.
- Foco/contraste: não testados em profundidade nesta etapa (página só de conteúdo reaproveitado,
  sem formulário) — os mesmos padrões já aprovados em Home/Sobre Nós (cards de post, CTA) se
  aplicam.

---

## 15. Possível reutilização arquitetural

- **`boxed-hero.php` serve, sem modificação** — reutilização direta (Categoria A), mesmo padrão
  já usado em `/clientes/`, `/parcerias/` e `/fale-conosco/`. `background_position:'0% 0%'`
  reaproveitando o mesmo prop já existente.
- **`section-title-band.php` NÃO se aplica** — esta página não tem nenhuma faixa de título em
  gradiente separada; o "Últimas notícias" não tem heading de seção própria (o Hero já cumpre
  esse papel visualmente).
- **`image-content-cta-section.php` serve, sem modificação** — a seção "Dedicação" é
  conteúdo-idêntica à de `/sobre-nos/` (mesmo heading, mesmo texto, mesmo CTA), variando apenas a
  imagem — reutilização direta (Categoria A/B), só passando uma `image` diferente.
- **`clients-carousel-section.php` serve, sem modificação** — mesmo carrossel/dados
  (`config/clients.php`, 82 logos) já usado na Home/Sobre Nós.
- **Padrão de card de post**: `components/blog-section.php` (Home) é candidato natural a
  reutilização aqui em vez de replicar a apresentação mais pobre do widget Elementor "Posts" —
  mesmo conteúdo (3 posts), já implementado e testado; decisão de design a confirmar na etapa de
  implementação (ver §6/§13).
- **Nenhum componente novo é necessário** para implementar esta página — é inteiramente composta
  de componentes já existentes e já validados no projeto.

### Potencial futuro de manutenção/CMS

Como esta página só reaproveita conteúdo (posts de blog, texto/imagem "Dedicação", carrossel de
clientes) que **já teria** um motivo de entrar em um futuro CMS por conta de outras páginas
(gerenciar posts do blog é já um caso de uso natural de CMS, independente desta página existir),
não há necessidade nova de manutenção dinâmica introduzida por `/informacoes/` especificamente —
ela apenas consome o mesmo conteúdo gerenciável em outro lugar. Nenhum CMS foi projetado nesta
etapa, apenas este registro.

---

## 16. REFERENCE DRIFT

Nenhum novo. Reconfirmado (consistente com o já registrado, não uma novidade específica desta
página): o botão/widget "Área Restrita" continua ausente do header ao vivo desta página, mesmo
padrão do **DRIFT-001** já documentado em `reference-baseline.md`.

---

## Screenshots capturados

- `docs/reference/screenshots/informacoes-desktop-1440-full.png`
- `docs/reference/screenshots/informacoes-tablet-900-full.png`
- `docs/reference/screenshots/informacoes-mobile-390-full.png`

**Nota metodológica**: a primeira tentativa de screenshot full-page no Hero (background CSS) e
na seção Dedicação (gradiente + texto) em mobile mostrou lacunas/texto ausente — artefato de
temporização da captura full-page composta (o mesmo tipo de problema já documentado em auditorias
anteriores para `<img loading="lazy">`, aqui ocorrendo com `background-image`/gradiente CSS).
Corrigido rolando a página inteira em incrementos antes de capturar — os 3 screenshots salvos
já refletem a correção, sem lacunas.
