# Auditoria — Posts do blog (3 posts reais)

**Referência:** `https://ctprice.com.br/wp/`
**Data:** 2026-09-02
**Ferramenta:** Chrome DevTools MCP — navegação real, inspeção de DOM/CSS computado, medição nos
3 viewports obrigatórios (+ checagem de breakpoint em 768/767px), teste de status HTTP de um link
interno.
**Escopo:** auditoria apenas — nenhum componente, config ou página foi criado/alterado.

Documentos lidos antes desta auditoria: `CLAUDE.md`, `docs/reference/reference-baseline.md`,
`docs/reference/site-inventory.md`, `docs/reference/home-final-validation.md`,
`docs/reference/informacoes-final-validation.md`, `config/blog-posts.php`.

---

## 1. Identificação dos 3 posts reais

Os 3 destinos de `config/blog-posts.php` foram confirmados como os 3 posts reais do inventário —
nenhuma divergência de URL, título ou data entre o config e o site ao vivo:

| # | Título exato | Slug | `postid-` | Categoria (na listagem) | Data/hora exibida no post | Imagem da listagem |
|---|---|---|---|---|---|---|
| 1 | Reforma trabalhista volta à pauta do STF; julgamento acontece neste mês | `reforma-trabalhista-volta-a-pauta-do-stf-julgamento-acontece-neste-mes` | **174** | FOLHA DE PAGAMENTO | agosto 2, 2024 — **5:01 pm** | `blog03-300x155.webp` |
| 2 | Receita Federal e Correios lançam portal de compras internacionais | `receita-federal-e-correios-lancam-portal-de-compras-internacionais` | **171** | INFORMATIVO | agosto 2, 2024 — **4:59 pm** | `blog02-300x155.webp` |
| 3 | Novo golpe mira em empreendedores e cria sites falsos que simulam a geração de documentos | `hello-world` | **1** | INFORMATIVO | julho 29, 2024 — **1:53 pm** | `blog01-300x155.webp` |

**Achado real: formato de hora divergente entre `config/blog-posts.php` e o site ao vivo.** O
config armazena hora em formato 24h (`17:01`, `16:59`, `13:53`), mas o post individual no site ao
vivo exibe em formato 12h AM/PM (`5:01 pm`, `4:59 pm`, `1:53 pm`) — mesmo valor de horário, só o
formato de exibição muda entre o card da listagem (Home/Informações, 24h) e o cabeçalho do post
em si (12h). Isso é uma característica genuína do template do post (widget `post-info` do
Elementor, que usa o formato de data/hora padrão do WordPress/locale), não um erro de digitação
no config. Registrado aqui, não corrigido.

Todos os 3 são publicações individuais reais (`single-post`, `post-template-default`), confirmado
via classe do `<body>` — nenhum é uma página estática disfarçada de post.

---

## 2. Comparação com `config/blog-posts.php`

| Campo | Home/Informações (`config/blog-posts.php`) | Post real | Divergência |
|---|---|---|---|
| Título (3 posts) | Idêntico | Idêntico | Nenhuma |
| Categoria (3 posts) | Idêntico (exibida só na listagem) | **Não exibida no post individual** (nenhum selo de categoria no cabeçalho do post — confirmado via seletor específico) | Categoria é conteúdo exclusivo da listagem, não do post |
| Thumbnail (3 posts) | `blog03/02/01-300x155.webp` | Mesmos arquivos reaproveitados como thumbnail nos cards de "posts relacionados" dentro de cada post | Nenhuma |
| URL (3 posts) | Idêntica | Idêntica | Nenhuma |
| Data | Formato 24h nos 3 | Formato 12h (AM/PM) nos 3 | Só formatação, mesmo valor (ver seção 1) |
| Excerpt | Idêntico texto | Idêntico texto (widget `theme-post-excerpt` reproduz o mesmo resumo) | Nenhuma |

Nenhuma alteração foi feita em `config/blog-posts.php` nesta etapa.

---

## 3. Template geral — classificação

**Classificação: A — estrutura comum aos 3 posts.**

Confirmado por inspeção direta do DOM: os 3 posts compartilham o **mesmo template Elementor**
(`elementor-page-1049`, Theme Builder "Single Post" — já registrado em `site-inventory.md`).
Verificação objetiva: os 6 `data-id` de seção de nível superior e a composição de widgets dentro
de cada seção são **byte-idênticos** nos 3 posts:

| # | `data-id` | Altura (varia por post 3/4) | Widgets |
|---|---|---|---|
| 1 | `75604fbd` | 66px | topbar (`icon-list`, `shortcode`) |
| 2 | `67c13940` | 132px | header (`image`, `nav-menu`, `button`) |
| 3 | `2ef9a64d` | **400px, fixa nos 3** | `theme-post-title` (cabeçalho do artigo) |
| 4 | `e7e6742` | Variável (1032/1061/904px — conforme o tamanho do conteúdo) | `theme-post-excerpt`, `post-info`, `share-buttons`, `divider`, `theme-post-content`, `posts.classic` |
| 5 | `5493f5c7` | 400px | footer |
| 6 | `bc497b9` | 78px | bottom bar |

As únicas diferenças reais entre os 3 posts são de **conteúdo** (parágrafos vs. lista numerada —
ver seção 6), nunca de estrutura/widget. Isso confirma que um único template reutilizável é
suficiente para a reconstrução.

---

## 4. Cabeçalho do artigo (seção `theme-post-title`, `data-id=2ef9a64d`)

Medido no post 1, reconfirmado idêntico (mesmo `data-id`, mesma altura, mesma cor) nos posts 2 e 3:

| Propriedade | Valor |
|---|---|
| Altura | **400px** (fixa, não varia por título) |
| Background | `linear-gradient(rgb(0,34,44) 0%, rgb(5,112,56) 100%)` — **mesmo gradiente institucional já usado na topbar e em `components/section-title-band.php`** |
| Decoração | Formas geométricas decorativas (contornos finos verdes, poligonais) sobrepostas ao gradiente, à direita do título — **padrão visual não encontrado em nenhum componente já implementado no projeto** |
| Container | `max-width: min(100%, 1140px)` |
| Título | `<h1>`, 30px, `font-weight:700`, `Roboto`, cor `rgb(254,254,254)` (branco/off-white), alinhado à esquerda (`text-align:start`), **não centralizado** |
| Eyebrow | Nenhum |

**Comparação com componentes existentes:**

- **`boxed-hero.php`**: usa imagem de fundo full-bleed (`background-image`), altura 400px (igual!),
  mas NUNCA gradiente — estrutura de background incompatível.
- **`internal-hero.php`**: imagem de fundo + coluna assimétrica, altura 640px — incompatível.
- **`components/section-title-band.php`**: **o mais próximo estruturalmente** (mesmo gradiente
  institucional, mesmo espírito "faixa colorida + título branco"), mas os valores medidos aqui
  divergem dos já usados nesse componente: altura fixa 400px (não 180px nem "auto"), texto
  **alinhado à esquerda** (não centralizado), fonte **Roboto** (não a família já usada nas 2
  instâncias de `/parcerias/`/`/trabalhe-conosco/`), e a presença de decoração geométrica que não
  existe em nenhuma instância já aprovada desse componente.

**Não é reutilização direta de nenhum componente existente** — na reconstrução, provavelmente
exigiria um novo modificador de `section-title-band.php` (altura 400px, alinhamento à esquerda,
decoração opcional) ou um componente de cabeçalho de artigo próprio. Nenhuma decisão foi tomada
nesta etapa (auditoria apenas).

---

## 5. Estrutura completa por seção — corpo do artigo (`data-id=e7e6742`)

Layout de 2 colunas via flexbox (`display:flex; flex-direction:row`, sem `gap`) dentro da mesma
seção:

| Coluna | Largura (1440px) | Conteúdo |
|---|---|---|
| Esquerda | 827px (~66%) | Excerpt em negrito, meta (data + hora, ícones), 4 botões de compartilhamento, divisor verde, corpo do artigo |
| Direita | 413px (~34%) | Widget "Posts" (WordPress `posts.classic`) — os **outros 2** posts (nunca o post atual), cada um com thumbnail + título (link) + data + excerpt + "Leia mais »" |

Nenhum `gap` explícito entre as colunas.

### Cabeçalho de metadados (dentro da coluna esquerda)

- Excerpt: parágrafo em negrito, reproduz o mesmo texto de `config/blog-posts.php`.
- Data: link (`<a>`) para uma URL de arquivo por data do WordPress (`/wp/AAAA/MM/DD/`) — **ver
  achado de link quebrado, seção 9**.
- Hora: texto simples, formato 12h AM/PM (não link).
- Autor: **não exibido** — nenhum nome de autor aparece em nenhum dos 3 posts (confirmado ausência
  de qualquer elemento de autoria no DOM).
- 4 botões de compartilhamento (Facebook/Twitter/LinkedIn/WhatsApp): **não são `<a>`**, são
  `<div role="button" tabindex="0">` com SVG interno — widget de compartilhamento social do
  Elementor Pro, dependente de JavaScript próprio do Elementor para funcionar (não foram clicados
  nesta auditoria, conforme instrução de não interagir além do necessário).
- Divisor: linha horizontal verde simples (`divider` widget), separando o cabeçalho do corpo.

### Corpo do artigo (`theme-post-content`)

Confirmado: **nenhum `<h2>`/`<h3>` dentro do corpo de nenhum dos 3 posts** — só parágrafos (e, no
post 2, uma lista numerada — ver seção 6). Nenhuma imagem, nenhum vídeo/embed, nenhuma tabela,
nenhuma citação (`blockquote`) em nenhum dos 3 posts.

### Sidebar / relacionados (`posts.classic`, coluna direita)

Não é uma sidebar no sentido tradicional do WordPress (sem busca, sem lista de categorias, sem
arquivo, sem widgets de terceiros) — é **apenas** o widget "Posts" listando os outros 2 posts do
site (o post atual nunca aparece na própria lista). Ver classificação completa na seção 11.

### Ausências confirmadas (nos 3 posts)

- **Comentários**: nenhum formulário, nenhum comentário publicado, nenhum contador — confirmado
  via busca por qualquer ocorrência da palavra "comment" no HTML renderizado (`0` ocorrências).
- **Landmark `<main>`**: ausente — o conteúdo do post não está dentro de nenhum elemento
  `<main>`/`role="main"` (diferente do padrão já usado em todas as páginas institucionais
  reconstruídas neste projeto, que sempre envolvem o conteúdo em `<main>`).
- **`<article>`**: presente (elemento semântico correto para o post em si).
- Open Graph / meta de compartilhamento social: ausente (ver seção 16).
- Schema.org / JSON-LD (`Article`): ausente (ver seção 16).

---

## 6. Conteúdo completo — inventário por post

### Post 1 — "Reforma trabalhista volta à pauta do STF..."

- **11 parágrafos** de texto corrido, nenhuma lista, nenhuma imagem, nenhuma citação.
- **2 links internos ao corpo** (para domínio externo `contabeis.com.br`, ver seção 9/10):
  - `"reforma trabalhista,"` → `https://www.contabeis.com.br/trabalhista/reforma-trabalhista/`
  - `"estoque"` → `https://www.contabeis.com.br/empresarial/estoque/`
- Última linha do corpo: `"Com informações do Valor Econômico"` — atribuição de fonte, texto
  simples (não é link).

### Post 2 — "Receita Federal e Correios lançam portal de compras internacionais"

- **4 parágrafos** (o último é uma `<p>` vazia — artefato de edição do WordPress, sem conteúdo
  visível) + **1 lista ordenada (`<ol>`) com 13 itens** — o único dos 3 posts com uma lista real:
  1. Rastreamento de encomendas
  2. Resolução de problemas comuns
  3. Prevenção de golpes
  4. Soluções para encomendas não recebidas
  5. Informações sobre pagamento de impostos
  6. Motivos de devolução de encomendas
  7. Lista de produtos proibidos
  8. Importação de medicamentos
  9. Programa Remessa Conforme
  10. Novas regras para importações
  11. Calculadora de impostos
  12. Chatbot LEO
  13. Manual de encomendas internacionais
  (texto completo de cada item transcrito literalmente na saída bruta desta auditoria — disponível
  para reconstrução sem depender do WordPress).
- **0 links internos ao corpo do texto.**

### Post 3 — "Novo golpe mira em empreendedores..." (slug `hello-world`)

- **7 parágrafos** (último também vazio, mesmo artefato do post 2), nenhuma lista, nenhuma imagem.
- **3 links internos ao corpo** (todos para `contabeis.com.br`):
  - `"(Darf)"` → `https://www.contabeis.com.br/tributario/darf/`
  - `"golpe que criava páginas fraudulentas"` → `https://www.contabeis.com.br/noticias/66261/golpistas-criam-sites-falsos-do-pgmei-para-enganar-contribuintes/`
  - `"Simples Nacional"` → `https://www.contabeis.com.br/tributario/simples-nacional/`
- Contém uma citação entre aspas de uma nota oficial da Receita Federal (texto corrido dentro do
  parágrafo, não um `<blockquote>` semântico).

**Achado real (visual/estrutural, não urgente): parágrafo vazio residual.** Os posts 2 e 3 têm uma
`<p></p>` vazia ao final do corpo — resíduo do editor do WordPress, sem efeito visual perceptível
(altura desprezível), mas confirmado no DOM. Não é erro de auditoria, é conteúdo publicado assim.

---

## 7. Imagens / assets

| Asset | URL | Formato | Uso | `alt` | Classificação |
|---|---|---|---|---|---|
| `blog03-300x155.webp` | `wp-content/uploads/2024/08/blog03-300x155.webp` | WEBP | Thumbnail do post 1 nos cards de "relacionados" dos posts 2 e 3 | `""` (vazio) | Já existente no projeto (`assets/images/blog/blog03-300x155.webp`, já usado por `blog-section.php`) |
| `blog02-300x155.webp` | idem | WEBP | Thumbnail do post 2 nos cards de "relacionados" dos posts 1 e 3 | `""` (vazio) | Já existente no projeto |
| `blog01-300x155.webp` | idem | WEBP | Thumbnail do post 3 nos cards de "relacionados" dos posts 1 e 2 | `""` (vazio) | Já existente no projeto |
| `LogoPreferencialColorida-1024x297.png` | `wp-content/uploads/2024/08/...` | PNG | Logo do header (e duplicado 1x mais no DOM, provavelmente footer) | `""` (vazio) | Já existente no projeto (`assets/images/logo/`) |
| Bandeiras de idioma (pt-br/en-us/es, 24×24) | `wp-content/plugins/gtranslate/flags/24/*.png` | PNG | Topbar (global) | `"pt"`/`"en"`/`"es"` | Já existente/compartilhado |

**Nenhuma imagem nova, própria de post, foi encontrada em nenhum dos 3 posts** — nenhum dos 3
tem imagem destacada exibida no corpo/cabeçalho do artigo em si (confirmado: 0 `<img>` dentro do
widget `theme-post-content` nos 3 posts, e a seção de título usa gradiente, não foto). As únicas
imagens usadas são as 3 thumbnails de listagem **já catalogadas e já baixadas** neste projeto
(`assets/images/blog/`) e o logo/bandeiras globais. Nenhuma imagem quebrada, nenhuma duplicada
além do reuso intencional das 3 thumbnails entre si (esperado, é o widget de relacionados).
**Nenhum asset novo precisa ser baixado para reconstruir os 3 posts.**

**Achado de acessibilidade**: todas as imagens têm `alt=""` — incluindo as thumbnails dos
"relacionados" e o logo, nenhuma imagem tem texto alternativo real.

---

## 8. Links internos

| Link | Destino | Post(s) onde aparece | Observação |
|---|---|---|---|
| Data do post (`agosto 2, 2024` etc.) | `https://ctprice.com.br/wp/AAAA/MM/DD/` | Todos os 3 | **Link para arquivo de data do WordPress — confirmado quebrado/inútil** (ver seção 9) |
| Título dos "relacionados" (posts.classic) | URL do outro post | Todos os 3 (2 links por post) | Funcional, aponta corretamente para os outros posts reais |
| Thumbnail dos "relacionados" | Mesma URL do título (link duplicado envolvendo só a imagem) | Todos os 3 | Funcional, mas **sem nome acessível** (imagem com `alt=""` dentro de um link sem texto — ver seção 15) |
| "Leia mais »" dos "relacionados" | Mesma URL novamente (3º link redundante para o mesmo destino) | Todos os 3 | Funcional — 3 links diferentes apontando para a mesma URL dentro do mesmo card |
| Menu principal / footer / topbar | Páginas institucionais já auditadas (`/wp/sobre-nos/`, `/wp/fale-conosco/` etc.) | Todos os 3 | Mesmos padrões (e mesmas inconsistências, ex. "Benefícios") já documentados em `site-inventory.md` — não reauditados aqui |

Nenhuma referência a `/contato` (o link 404 já conhecido da Home) foi encontrada dentro dos posts.
Nenhum caminho `/wp/...` quebrado novo foi encontrado além do já registrado (arquivo de data).

**Tradução de caminhos WordPress necessária na reconstrução**: nenhuma — os posts não usam nenhum
caminho `/wp-content/`/`/wp-json/`/shortcode que precise de tradução especial além do já resolvido
para as páginas institucionais (mover slug para a raiz, sem `/wp/`).

---

## 9. Achado real — link da data quebrado (arquivo por data do WordPress)

Testado diretamente (`https://ctprice.com.br/wp/2024/08/02/`, destino do link de data do post 1):

- **Status HTTP: 200** (não é um 404).
- **Conteúdo: praticamente vazio/quebrado** — `document.title` = `"agosto 2, 2024 – CT Price"`,
  mas o corpo da página renderiza apenas o texto solto `"10/00/1122"` (sem nenhum post listado,
  `postCount: 0`) — é uma página de arquivo por data padrão do WordPress, sem nenhum template
  Elementor aplicado a ela, então aparece sem estilo e sem conteúdo útil.

**Classificação: C — defeito conhecido do site original, não replicado na reconstrução.** O link
"funciona" tecnicamente (200), mas não entrega nenhum conteúdo real ao usuário — é uma
funcionalidade nativa do WordPress (arquivo por data) que nunca foi desabilitada/estilizada,
mesma categoria dos outros pequenos defeitos WP já catalogados em `docs/architecture-proposal.md`
(seção 2). Não corrigido nesta etapa — apenas documentado. Recomendação natural para a
reconstrução: a data do post não precisa ser um link (ou, se for, deve apontar para algo real).

---

## 10. Links externos

| Link | Destino | Post(s) | HTTPS | `target`/`rel` | Funcionamento |
|---|---|---|---|---|---|
| `"reforma trabalhista,"` | `contabeis.com.br/trabalhista/reforma-trabalhista/` | Post 1 | Sim | Nenhum (mesma aba, sem `rel`) | Link de referência jornalística, não testado quanto a status (fora do escopo institucional CT Price) |
| `"estoque"` | `contabeis.com.br/empresarial/estoque/` | Post 1 | Sim | Nenhum | Idem |
| `"(Darf)"` | `contabeis.com.br/tributario/darf/` | Post 3 | Sim | Nenhum | Idem |
| `"golpe que criava páginas fraudulentas"` | `contabeis.com.br/noticias/...` | Post 3 | Sim | Nenhum | Idem |
| `"Simples Nacional"` | `contabeis.com.br/tributario/simples-nacional/` | Post 3 | Sim | Nenhum | Idem |

Todos os 5 links externos de conteúdo apontam para o mesmo domínio de terceiros
(`contabeis.com.br`, fonte jornalística especializada em contabilidade) — nenhum usa
`target="_blank"`/`rel="noopener noreferrer"`, ou seja, o usuário sai do site da CT Price na mesma
aba. Nenhuma autenticação/formulário foi testado, conforme instruído.

---

## 11. Sidebar

**Não existe uma sidebar no sentido de área de widgets do WordPress** (sem busca, sem "posts
recentes" genérico, sem lista de categorias, sem arquivo mensal, sem banners de terceiros).

O que existe, ocupando visualmente a posição de uma sidebar (coluna direita, ~34% da largura, ao
lado do corpo do artigo): o **widget "Posts" (`posts.classic`)** listando os 2 outros posts reais
do site, cada um com thumbnail + título + data + excerpt + "Leia mais »".

**Avaliação**: é útil no sentido de manter o usuário navegando dentro do conteúdo do site (só 3
posts existem, então a lista é sempre "os outros 2"), mas não é uma sidebar típica de WordPress
com múltiplos widgets — é um único widget de conteúdo relacionado. **Consistente entre os 3
posts** (mesmo widget, mesmos 2 outros posts em cada caso, nunca o post atual). Decisão sobre
reproduzir ou não fica para a etapa de implementação.

---

## 12. Comentários

**Ausentes nos 3 posts** — nenhum formulário de comentário, nenhum comentário publicado, nenhum
contador, nenhuma referência à palavra "comment" em nenhum lugar do HTML renderizado de nenhum dos
3 posts. Isso indica que a funcionalidade de comentários do WordPress está desabilitada
globalmente (ou pelo menos nestes 3 posts) — **não é conteúdo real perdido**, é ausência de uma
funcionalidade padrão do WordPress que nunca foi usada/habilitada. Não precisa ser reproduzida.

---

## 13. Responsividade

| Viewport | Colunas (corpo/relacionados) | `scrollWidth`/`clientWidth` | Observação |
|---|---|---|---|
| 1440×900 | 2 (827px / 413px) | Sem overflow (testado nos 3 posts) | — |
| 900×1200 | 2 (587px / 293px) | Sem overflow | Testado no post 1 e 2 |
| 768×1024 | 2 (489px / 244px) | Sem overflow | Testado no post 1 — ainda 2 colunas |
| **767×1024** | **1** (colunas empilham, relacionados abaixo do corpo) | Sem overflow | **Breakpoint exato em 767px**, mesmo padrão já usado em todo o site (não um valor novo) |
| 390×844 | 1 | Sem overflow (testado nos 3 posts) | Relacionados empilham como cards de largura total, abaixo do texto |

Tipografia, imagens (thumbnails dos relacionados) e o cabeçalho gradiente/decoração se comportam
bem em todos os viewports testados — nenhum overflow horizontal, nenhuma quebra de layout, nenhum
elemento cortado. A lista numerada do post 2 permanece legível e bem indentada em todos os
viewports.

---

## 14. Problemas visuais

- **Botões de compartilhamento social**: dependem de JS do Elementor Pro, `<div role="button">`
  sem `<a>`/`<button>` reais — arquitetura frágil e específica do WordPress/Elementor, não deve
  ser copiada como arquitetura (conforme `CLAUDE.md`).
  Se mantidos na reconstrução, deveriam ser reimplementados como elementos semânticos reais.
- **Link de data para arquivo quebrado** (seção 9) — leva a uma página vazia/sem estilo.
- **Parágrafo vazio residual** nos posts 2 e 3 (sem impacto visual perceptível, mas presente no
  HTML).
- **`alt=""` em todas as imagens**, incluindo logo e thumbnails de conteúdo real (ver seção 15).
- **Cabeçalho do artigo sem eyebrow/categoria**: a categoria (ex. "FOLHA DE PAGAMENTO") só aparece
  na listagem (Home/Informações), nunca no próprio post — um usuário que chega direto ao post não
  sabe a categoria dele.
- **Nenhum elemento "genérico de WordPress" óbvio além dos botões de compartilhamento** (o post em
  si é limpo — sem widgets de propaganda, sem newsletter, sem banners).
- **Largura de leitura**: 827px de coluna de texto em 1440px é uma largura confortável para
  leitura (não excessivamente larga); nenhum problema de legibilidade identificado.

---

## 15. Acessibilidade

- **Hierarquia de headings**: `H1` (título do post) → `H3` (títulos dos 2 posts relacionados) —
  **pula de H1 direto para H3** (sem H2 em nenhum lugar), tanto no cabeçalho quanto no corpo do
  artigo (que não usa nenhum heading interno). Hierarquia tecnicamente incorreta (salto de nível),
  mas sem impacto prático grave dado que não há conteúdo real de nível H2 nos 3 posts.
- **`alt=""` generalizado**: nenhuma imagem (logo, bandeiras à parte) tem texto alternativo real —
  as 3 thumbnails de post têm conteúdo informativo (identificam visualmente o post relacionado) e
  deveriam ter `alt` descritivo.
- **Links de imagem sem nome acessível**: no widget de relacionados, o link que envolve só a
  thumbnail (`<a><img alt=""></a>`) não tem `aria-label` nem texto — para um leitor de tela, esse
  link específico não tem nenhum nome anunciável (ainda que os outros 2 links redundantes do mesmo
  card, com texto, cubram a mesma função).
- **Botões de compartilhamento**: `<div role="button" tabindex="0" aria-label="Compartilhar no facebook">`
  — tecnicamente focável e nomeado, mas não é um `<button>` real (comportamento de teclado
  depende inteiramente do JavaScript do Elementor responder a Enter/Espaço corretamente).
- **Ausência de `<main>`**: o conteúdo do post não está dentro de nenhuma landmark `<main>` —
  diferente do padrão já usado (e correto) em todas as páginas institucionais já reconstruídas
  neste projeto.
- **Contraste**: título branco sobre gradiente escuro (cabeçalho) — sem problema aparente. Texto
  do corpo em cor escura sobre fundo branco — sem problema aparente. Não foi calculado um índice
  formal de contraste nesta etapa.
- **Foco**: não testado interativamente nesta auditoria (auditoria de descoberta, não de
  interação/teclado completa) — os elementos padrão (links, `role="button"`) devem ter foco
  visível herdado do tema, não verificado em detalhe aqui.
- **Embeds**: não há embeds (vídeo/iframe) em nenhum dos 3 posts — nada a avaliar.

---

## 16. SEO observável

Confirmado (testado individualmente no post 1; posts 2 e 3 usam o mesmo template — presume-se o
mesmo padrão, não retestado widget a widget nos 3, apenas confirmado `<title>`/meta description
individualmente para os 3):

| Item | Post 1 | Post 2 | Post 3 |
|---|---|---|---|
| `<title>` | `"Reforma trabalhista volta à pauta do STF; julgamento acontece neste mês – CT Price"` | `"Receita Federal e Correios lançam portal de compras internacionais – CT Price"` | `"Novo golpe mira em empreendedores e cria sites falsos que simulam a geração de documentos – CT Price"` |
| Meta description | Igual ao excerpt do config | Igual ao excerpt do config | Igual ao excerpt do config |
| Canonical | Aponta para a própria URL (`/wp/...`) | Idem | Idem (`/wp/hello-world/`) |
| Open Graph (`og:title`/`og:image`/etc.) | **Ausente** | Não retestado (mesmo template) | Não retestado (mesmo template) |
| Schema.org / JSON-LD `Article` | **Ausente** (`0` scripts `application/ld+json`) | Não retestado (mesmo template) | Não retestado (mesmo template) |
| Slug | Descritivo (post 1/2) / **`hello-world` (post 3, incoerente com o conteúdo real)** | — | — |

Nenhum SEO novo foi inventado ou sugerido nesta etapa — apenas o que já existe foi registrado.

---

## 17. Estrutura recomendada para reconstrução (conceitual, sem implementar)

- **Um único template reutilizável de artigo faz sentido** — os 3 posts confirmaram estrutura
  100% compartilhada (seção 3); as diferenças são só de conteúdo (parágrafos vs. lista).
- **Os dados podem ficar em config estático inicialmente** — mesmo padrão já usado no projeto
  (`config/jobs.php`, `config/video-testimonials.php` etc.): um registro por post com
  título/slug/excerpt/categoria/data, mais o corpo do artigo.
- **Conteúdo extenso (corpo do artigo) provavelmente deve ficar em arquivo próprio por post** (não
  misturado no mesmo array que os metadados) — o post 2, por exemplo, tem uma lista de 13 itens
  com textos longos; manter isso dentro de um único array PHP grande junto dos outros 2 posts
  tende a ficar difícil de manter. Um arquivo dedicado por post (ou um campo HTML confiável maior)
  é mais coerente com o volume real de conteúdo.
- **Componentes específicos de conteúdo editorial são necessários**: nenhum componente existente
  no projeto lida com "corpo de texto rico com parágrafos/listas/links" — seria um componente
  novo (`article-body` ou equivalente), além de um cabeçalho de artigo (ver seção 4) e um
  componente de "posts relacionados" (reaproveitável, já existe conceito parecido em
  `components/blog-section.php`, mas em formato de card horizontal com texto, não grade).
- **Elementos compartilháveis com o restante do site**: topbar/header/footer/bottom-bar (globais,
  já implementados), e possivelmente as thumbnails já existentes em `assets/images/blog/`.
- Botões de compartilhamento social, se mantidos, devem ser reconstruídos como elementos
  semânticos reais (não replicar a arquitetura `div[role=button]` do Elementor).

Nenhuma decisão de implementação foi tomada — isto é só o levantamento conceitual pedido.

---

## 18. Campos reais candidatos ao futuro CMS (só o comprovado pela auditoria)

- `título`
- `slug`
- `excerpt` (resumo, já usado na listagem)
- `categoria` (usada só na listagem, não no post)
- `imagem_thumbnail` (usada na listagem e no widget de relacionados — nenhuma imagem destacada
  maior/separada foi encontrada)
- `data` / `hora` (formatos de exibição diferentes confirmados entre listagem e post — ver
  seção 1)
- `corpo` (parágrafos; pelo menos 1 post usa lista ordenada — o campo de corpo precisa suportar
  HTML rico, não só texto simples)
- `fonte` (atribuição textual ao final do post 1 — "Com informações do Valor Econômico"; presente
  em apenas 1 dos 3 posts, então não é obrigatório)

Nenhum campo de autor real foi encontrado (nenhum dos 3 posts exibe autoria). Nenhum campo de
status foi observável publicamente (não há rascunhos visíveis). Nenhum schema foi criado nesta
etapa — lista apenas o que foi comprovado.

---

## 19. Classificação do `hello-world`

- **URL**: `https://ctprice.com.br/wp/hello-world/` — ativa, retorna 200, conteúdo completo.
- **Estado**: publicado, com título e corpo totalmente substituídos pelo conteúdo real ("Novo
  golpe mira em empreendedores...") — **nenhum texto padrão do WordPress ("Welcome to WordPress...",
  "Mr WordPress" etc.) permanece em lugar nenhum da página.**
- **Conteúdo**: 7 parágrafos reais + 3 links externos reais — editorial genuíno, mesmo padrão de
  qualidade e extensão dos outros 2 posts.
- **Data**: julho 29, 2024 — mais antiga que os outros 2 (agosto 2, 2024), consistente com ser o
  primeiro post publicado (reaproveitando o post-ID/slug de exemplo padrão `postid-1`, que o
  WordPress sempre cria automaticamente na instalação).
- **Autor**: não exibido (mesmo padrão dos outros 2 — não é uma característica exclusiva deste
  post).
- **Aparece nas listagens**: sim — nos 3 cards de "Últimas notícias" da Home e de `/informacoes/`
  (via `config/blog-posts.php`), e no widget de relacionados dos outros 2 posts.
- **Aparece nos 3 cards atuais**: sim, é um dos 3.
- **Valor institucional**: sim — é uma notícia de alerta de segurança genuinamente relevante para
  os clientes/leitores da CT Price (golpes contra empreendedores), mesmo padrão editorial dos
  outros 2 posts.

**Classificação: A — conteúdo legítimo**, com uma ressalva técnica: o **slug/ID é residual do
WordPress** (`hello-world`/`postid-1`, o post de exemplo padrão que toda instalação nova do
WordPress cria automaticamente, nunca excluído — apenas teve título e conteúdo substituídos). O
conteúdo em si não é residual nem técnico — só o identificador da URL é. Isso já estava
corretamente registrado em `site-inventory.md` (seção "Links quebrados", item 43) e é reconfirmado
aqui sem alteração.

**Não removido, não ignorado.** Decisão sobre manter o slug `hello-world` ou renomear (com
redirecionamento 301) na reconstrução já está registrada como pendência aberta em
`docs/architecture-proposal.md` (seção 14.1, item 4) — não decidida nesta auditoria.

---

## 20. REFERENCE DRIFT

Nenhum `REFERENCE DRIFT` novo foi identificado nos 3 posts em si — títulos, URLs, conteúdo e
estrutura batem exatamente com o que já estava registrado em `site-inventory.md`/
`config/blog-posts.php`.

**Um achado novo, fora do escopo do baseline já registrado, mas dentro do template dos posts —
registrado aqui como descoberta desta auditoria (não como drift, pois nunca foi medido antes):**

### Divergência de dados institucionais no template do post (nova, não documentada antes)

O rodapé/bottom-bar dos 3 posts usa valores diferentes dos já catalogados em
`docs/reference/global-data-conflicts.md` para as páginas institucionais:

| Campo | Páginas institucionais (já documentado) | Template do post (achado desta auditoria) |
|---|---|---|
| WhatsApp do topbar | `(67) 99232-4097` (não-Home) / `(67) 99261-6117` (Home) | `(67) 99232-4097` — **igual ao padrão não-Home**, sem divergência aqui |
| WhatsApp do ícone flutuante/rodapé inferior | `(67) 99232-4097` ou `(67) 99261-6117` (conforme a página) | **`(67) 99204-1134`** — **um TERCEIRO número, nunca antes catalogado**, confirmado em 2 dos 3 posts (post 1 e post 2) |
| Crédito "Desenvolvido por Agência Lester" | `https://agencialester.com.br/` | **`https://w.app/AgenciaLester`** — URL diferente da já catalogada |
| Copyright — link do nome da empresa | `https://ctprice.com.br/wp/home/` (com `/wp/`) | **`https://ctprice.com.br/`** (sem `/wp/`) |
| Menu secundário do footer | "A CT Price", "Nossos Clientes", "Nossos Parceiros", "Trabalhe Conosco", "Benefícios" | **"Sobre nós", "Nossos Serviços" (`#nossosservicos`), "Clientes", "Parceiros", "Fale Conosco", "Informações", "Vagas"** — rótulos e itens diferentes |

Isso **não é classificado como `REFERENCE DRIFT`** (drift é uma mudança do site ao vivo *depois*
de já ter sido medido) — é uma divergência de dados que **já existia** e simplesmente não havia
sido auditada antes, porque os posts de blog nunca tinham sido auditados em detalhe. Registrado
aqui como **achado novo de `global-data-conflicts.md`**, recomendando que seja incorporado àquele
documento numa próxima atualização (fora do escopo desta tarefa, que é só auditoria de posts).
Nenhum valor foi alterado em nenhum config.

---

## 21. Screenshots

Salvos (referência, não sobrescrevendo nada existente):

- `docs/reference/screenshots/blog-reforma-trabalhista-desktop-1440-full.png`
- `docs/reference/screenshots/blog-reforma-trabalhista-tablet-900-full.png`
- `docs/reference/screenshots/blog-reforma-trabalhista-mobile-390-full.png`
- `docs/reference/screenshots/blog-receita-federal-correios-desktop-1440-full.png`
- `docs/reference/screenshots/blog-receita-federal-correios-tablet-900-full.png`
- `docs/reference/screenshots/blog-receita-federal-correios-mobile-390-full.png`
- `docs/reference/screenshots/blog-hello-world-desktop-1440-full.png`
- `docs/reference/screenshots/blog-hello-world-tablet-900-full.png`
- `docs/reference/screenshots/blog-hello-world-mobile-390-full.png`

---

## 22. Verificação final do inventário global

Reconferido `docs/reference/site-inventory.md`:

- **Páginas institucionais**: **10** — Home, A CT Price, Clientes, Parceiros, Fale Conosco,
  Informações, Trabalhe Conosco, Ouvidoria, Depoimentos, Área Restrita (todas já implementadas e
  validadas antes desta tarefa).
- **Posts**: **3** — confirmados nesta auditoria (Reforma trabalhista, Receita Federal/Correios,
  Novo golpe/`hello-world`).
- **Total público real**: **13** — confirmado, sem alteração.

Nenhuma URL pública genuína adicional foi encontrada durante esta auditoria. O único novo destino
acessado (`https://ctprice.com.br/wp/2024/08/02/`, arquivo por data) **não é uma página de
conteúdo real** — é uma saída automática/vazia do WordPress (mesma categoria já usada para excluir
`/wp/blog/` e a "página" Benefícios do inventário) e **não deve ser contada** como 14ª página.

**Inventário confirmado sem alteração: 10 + 3 = 13 páginas públicas reais.**
