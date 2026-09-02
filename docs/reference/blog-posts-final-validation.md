# Validação final consolidada — 3 posts do blog

Data: 2026-09-02
Documentação-base: `docs/reference/blog-posts-audit.md`, `docs/reference/reference-baseline.md`,
`docs/reference/home-final-validation.md`, `docs/reference/informacoes-final-validation.md`

Escopo: validação final visual/estrutural/de conteúdo/segurança dos 3 posts públicos, executada ao
vivo via Chrome DevTools MCP nos 5 viewports obrigatórios, com comparação texto a texto contra a
auditoria e um teste real de ataque (Host Header) contra `ctprice_absolute_url()`.

**Um problema real de segurança foi encontrado e corrigido nesta validação** (Host Header não
validado, refletido em `canonical` e nas URLs de compartilhamento) — ver Parte N. Um segundo ajuste
defensivo (normalização de `/` em `ctprice_absolute_url()`) também foi aplicado.

---

## Parte A — Full-page (5 viewports × 3 posts)

| Post | 1440×900 | 900×1200 | 768×1024 | 767×1024 | 390×844 |
|---|---|---|---|---|---|
| Reforma trabalhista | 1440/1440 | 900/900 (885/885 real) | 753/753 | 752/752 | 390/390 |
| Receita Federal/Correios | 1440/1440 | 900/900 | 753/753 | 752/752 | 390/390 |
| Hello World (Novo golpe) | 1440/1440 | 900/900 | 753/753 | 752/752 | 390/390 |

`scrollWidth === clientWidth` confirmado nos 15 pares post×viewport — **zero overflow horizontal**.
Ordem de seções idêntica nos 3: topbar → header → cabeçalho editorial (gradiente) → corpo (meta +
compartilhamento + divisor + texto) + relacionados (2 colunas ≥768px) → footer → bottom bar →
WhatsApp flutuante → cookie banner. Nenhuma sobreposição, nenhum gap acidental identificado em
nenhum dos 3 screenshots de referência (1440/900/390) nem nas checagens intermediárias (768/767).

Métricas principais (1440×900, idênticas nos 3 por template compartilhado): cabeçalho 400px;
container do corpo 1140px; coluna principal ~715–827px; coluna de relacionados ~375–413px
(proporção ~66/34%, igual à do original).

---

## Parte B — URLs e slugs

| URL | Status | Query string | Router | Dependência WP |
|---|---|---|---|---|
| `/reforma-trabalhista-volta-a-pauta-do-stf-julgamento-acontece-neste-mes/` | 200 | Nenhuma | Nenhum | Nenhuma |
| `/receita-federal-e-correios-lancam-portal-de-compras-internacionais/` | 200 | Nenhuma | Nenhum | Nenhuma |
| `/hello-world/` | 200 | Nenhuma | Nenhum | Nenhuma |

`/hello-world/` confirmado **não renomeado** — slug histórico preservado exatamente, conforme
`config/blog-posts.php`. Barra final funciona no mesmo padrão de todas as outras páginas do
projeto (diretório + `index.php`).

---

## Parte C — Arquitetura compartilhada

Confirmado por inspeção de código: os 3 diretórios (`{slug}/index.php`) só definem `$postSlug` (3
linhas cada, diferindo apenas no valor do slug) e delegam a `blog/_post-template.php` — **nenhuma
cópia de template**. `grep` por `hello-world|reforma-trabalhista|receita-federal` em
`components/*.php` e em `blog/_post-template.php`: **zero ocorrências de lógica condicional por
slug** (a única ocorrência textual é um comentário de exemplo no docblock, não código). Os 3
componentes (`article-header.php`, `article-content-section.php`, `related-posts.php`) são
genuinamente compartilhados, orientados só por dados. **Nenhuma refatoração foi necessária.**

---

## Parte D — `config/blog-posts.php`

Confirmado: **exatamente 3 registros**, cada um com `slug`/`title`/`excerpt`/`category`/`image`/
`published_at` corretos (conferidos contra a auditoria, ver Parte E). `url` é **derivado**
(`BASE_URL . '/' . slug . '/'`), nunca hardcoded, nunca `/wp/...`. `date`/`time` também derivados
de `published_at` por 2 funções puras — **nenhuma segunda data/hora independente**. Nenhuma
duplicação de metadados nos diretórios dos posts (eles só carregam `$postSlug`).

Funções auxiliares (`ctprice_blog_post_date_text`, `ctprice_blog_post_time_text`,
`ctprice_absolute_url`, `ctprice_configure_session_cookie`) — `grep` por `function ctprice_` em
todo o projeto confirma **nomes únicos**, sem colisão com as demais funções já existentes
(`ctprice_ouvidoria_*`, `ctprice_fale_conosco_*`, `ctprice_clean_line`, `ctprice_department_*`).

---

## Parte E — Fidelidade integral do conteúdo (texto a texto, não só contagem)

Extração automatizada do HTML de cada `content/blog/{slug}.php` (via `require` real, não parsing
manual) comparada parágrafo a parágrafo, item a item, contra o texto transcrito em
`blog-posts-audit.md` §6.

### Post 1 — Reforma trabalhista
**11/11 parágrafos**, mesma ordem, mesmo texto — confirmado idêntico, palavra por palavra, aos 11
parágrafos documentados na auditoria (incluindo a linha final "Com informações do Valor
Econômico"). **2/2 links externos**, mesmos textos-âncora e destinos exatos
(`contabeis.com.br/trabalhista/reforma-trabalhista/`, `contabeis.com.br/empresarial/estoque/`).
Nenhuma palavra omitida, nenhuma adicionada.

### Post 2 — Receita Federal/Correios
Auditoria registrou 4 `<p>` no DOM original, o 4º vazio. Confirmado nesta validação: o `content/
blog/...php` tem **exatamente 3 parágrafos com conteúdo** ("A Receita Federal, em parceria...",
"Principais funcionalidades do portal:", "O Portal Compras Internacionais visa..."), idênticos ao
texto auditado — **nenhuma informação editorial foi removida**, só o 4º elemento (já confirmado
vazio na própria auditoria, via `textContent.trim() === ''` extraído diretamente do DOM original,
não uma suposição) foi omitido. `<ol>` real confirmado (`document.querySelectorAll('.article-body
ol').length === 1`), **exatamente 13 `<li>`** (`liCount: 13`), conteúdo e ordem de cada um dos 13
itens conferidos e idênticos ao texto auditado (rastreamento, resolução de problemas, prevenção de
golpes, encomendas não recebidas, pagamento de impostos, devolução de encomendas, produtos
proibidos, importação de medicamentos, Remessa Conforme, novas regras, calculadora, Chatbot LEO,
manual de encomendas — nessa ordem exata).

### Post 3 — Hello World (Novo golpe)
Auditoria registrou 7 `<p>` no DOM original, o 7º vazio. Confirmado: **exatamente 6 parágrafos com
conteúdo**, idênticos ao texto auditado, incluindo a citação entre aspas da nota da Receita Federal
preservada literalmente. **3/3 links externos** confirmados: `(Darf)` →
`contabeis.com.br/tributario/darf/`; `golpe que criava páginas fraudulentas` →
`contabeis.com.br/noticias/66261/...`; `Simples Nacional` →
`contabeis.com.br/tributario/simples-nacional/`. Nenhuma informação editorial removida, só o 7º
elemento (já confirmado vazio na auditoria original) foi omitido.

**Nenhum dos elementos classificados como "vazio" continha texto invisível ou semanticamente
relevante** — a própria auditoria já havia extraído `textContent.trim()` diretamente do DOM do site
original antes desta implementação, retornando string vazia (`""`) para os 2 casos; esta validação
não encontrou motivo para reabrir essa conclusão.

---

## Parte F — Cabeçalho editorial

Confirmado nos 3 posts, nos 5 viewports: altura 400px (desktop/tablet) / altura automática com
`padding:60px 0` (≤767px, sem forçar 400px fixos num espaço menor); gradiente
`linear-gradient(180deg, #00222C 0%, #057038 100%)`; container 1140px; `<h1>` branco
(`--color-off-white`), alinhado à esquerda. Título mais longo testado especificamente em 390px
(Post 1, "Reforma trabalhista volta à pauta do STF; julgamento acontece neste mês"): `scrollHeight
=== clientHeight` (101px ambos) — **quebra de linha natural, sem corte, sem `overflow:hidden`
mascarando nada, sem invadir a seção seguinte** (confirmado via posição real dos elementos
subsequentes no DOM).

---

## Parte G — Hierarquia semântica

Confirmado nos 3 posts via `querySelectorAll('h1'|'h2'|'h3')`:

| Post | `<h1>` | `<h2>` | `<h3>` |
|---|---|---|---|
| Reforma trabalhista | 1 (título do post) | 1 ("Mais notícias") | 0 |
| Receita Federal/Correios | 1 | 1 | 0 |
| Hello World | 1 | 1 | 0 |

**Exatamente 1 `<h1>` por artigo, "Mais notícias" como `<h2>` real, zero `<h3>` — nenhum salto
artificial H1→H3, nenhum heading inventado dentro do corpo** (o HTML de `content/blog/*.php` não
insere nenhum heading — só parágrafos/lista/links, fiel ao conteúdo original que também não tinha
subtítulos). `<main>` presente e envolvendo cabeçalho + corpo nos 3 (confirmado por inspeção do
`blog/_post-template.php`). Estrutura de lista real (`<ol><li>`) confirmada no post 2.

---

## Parte H — Layout editorial

≥768px: 2 colunas (corpo à esquerda, relacionados à direita) confirmado nos 3 posts em 1440/900/768.
≤767px: empilhamento (corpo primeiro, relacionados depois) confirmado nos 3 posts em 767/390 — via
posição vertical real dos elementos (`top` do corpo sempre menor que `top` dos relacionados).

Tipografia editorial (`.article-body`): 17px, `line-height:1.75`, cor `#333333` — largura de
leitura confortável (coluna principal ~715–827px em desktop, nunca a largura total da página).
Parágrafos com `margin-bottom:22px`; lista numerada com recuo (`padding-left:24px`) e espaçamento
entre itens (`12px`); links sublinhados, cor institucional, foco visível. Padding mobile
verificado sem aperto visual nos 3 screenshots de 390px.

**Container 1140px confirmado como diferença consciente aprovada** — visualmente equilibrado nos 3
posts, mesmo valor já usado em toda a página (não introduz inconsistência nova).

---

## Parte I — Posts relacionados

Confirmado nos 3 posts: **exatamente 2 itens**, nunca o próprio artigo, sempre os outros 2, ordem
determinística (ordem natural do array de `config/blog-posts.php`, excluindo o slug atual):

- Reforma trabalhista → mostra Receita Federal/Correios + Hello World
- Receita Federal/Correios → mostra Reforma trabalhista + Hello World
- Hello World → mostra Reforma trabalhista + Receita Federal/Correios

Cada card com thumbnail + `alt` real + título + data + excerpt + URL corretos (conferido via DOM).

**Sobre o link único por card**: confirmado `linkCountInCard: 0` (nenhuma âncora aninhada dentro da
âncora — HTML inválido teria sido gerado se houvesse, o que não ocorre: os elementos internos são
`<span>`). O link é semanticamente válido (`<a>` real, não `<div role="button">`), o nome acessível
inclui título + data + excerpt (verboso, mas completo e útil — verificado via `textContent` do
próprio link em foco real de teclado), não há elementos interativos aninhados, não há links
redundantes (1 por card, contra os 3 redundantes do original).

---

## Parte J — Thumbnails

`blog01/02/03-300x155.webp`, todas já existentes em `assets/images/blog/` — **nenhum asset novo**
foi adicionado (confirmado: nenhum arquivo novo em `assets/images/`). `object-fit:cover` sem
distorção (confirmado visualmente nos 9 screenshots). `alt` real em **100%** das thumbnails de
relacionados nos 3 posts (`"Notícia: {título}"`) — **zero** `alt=""` (correção confirmada do
defeito do original, onde as mesmas imagens tinham `alt=""`).

---

## Parte K — Datas

`published_at` confirmado como única origem real — `date`/`time` (Home, Informações, artigo,
relacionados) são todos **derivados** da mesma função, nunca valores independentes. Verificado que
Home/Informações/artigo mostram a mesma data/hora para cada post (ex.: Reforma trabalhista —
"agosto 2, 2024 • 17:01" idêntico nos 3 contextos). Formato 24h unificado confirmado — diferença
consciente aprovada (não reproduz o "5:01 pm" em inglês do original). **Nenhum link de data para
`/wp/AAAA/MM/DD/`** — confirmado via grep: `href="/wp/` não existe em nenhum dos 3 posts; a data é
`<span>` de texto simples nos 3.

---

## Parte L — Links externos editoriais

Os 5 links (todos `contabeis.com.br`) verificados com destino idêntico ao auditado, `<a>` real,
mesma aba (sem `target`), sem `rel` (correto, já que não há `target="_blank"` aqui — só os botões
de compartilhamento, que abrem domínio de terceiro, usam `target`/`rel`). Foco visível herdado do
estilo global `.article-body a:focus-visible`. Nenhum destino foi alterado. Nenhuma navegação real
foi feita além do necessário para conferir o `href`.

---

## Parte M — Compartilhamento

Validado estruturalmente e por interação real (foco de teclado) nos 4 botões:

| Rede | `<a>` real | `target="_blank"` | `rel="noopener noreferrer"` | SVG local | `aria-label` |
|---|---|---|---|---|---|
| Facebook | ✅ | ✅ | ✅ | ✅ (inline, sem biblioteca) | "Compartilhar no Facebook" |
| X (Twitter) | ✅ | ✅ | ✅ | ✅ | "Compartilhar no X (Twitter)" |
| LinkedIn | ✅ | ✅ | ✅ | ✅ | "Compartilhar no LinkedIn" |
| WhatsApp | ✅ | ✅ | ✅ | ✅ | "Compartilhar no WhatsApp" |

Foco real testado (`.focus()` + `matches(':focus-visible')` + verificação de `background-color`
computado mudando) — confirmado visível nos 4.

**Teste com título acentuado** (Post 1, "Reforma trabalhista volta à pauta do STF; julgamento
acontece neste mês") — HTML gerado inspecionado diretamente:
```
https://twitter.com/intent/tweet?url=http%3A%2F%2F...%2F&text=Reforma%20trabalhista%20volta%20%C3%A0%20pauta%20do%20STF%3B%20julgamento%20acontece%20neste%20m%C3%AAs
```
`à` → `%C3%A0`, `ê` → `%C3%AA`, `;` → `%3B`, espaço → `%20`; o `&` entre parâmetros aparece
corretamente como `&amp;` no HTML (a URL de compartilhamento em si não foi quebrada — o `&amp;`
faz parte da sintaxe do atributo HTML, o navegador decodifica de volta para `&` antes de navegar).
**Nenhum caractere quebrado por `&`, acentos, `;` ou espaços.** Só URL e título são usados — nenhum
conteúdo adicional foi inventado para o compartilhamento. Nenhum compartilhamento real foi
enviado/publicado — apenas a URL gerada foi inspecionada.

---

## Parte N — `ctprice_absolute_url()` (revisão de segurança)

### Problema real encontrado e corrigido: Host Header não validado

A versão original da função usava `$_SERVER['HTTP_HOST']` **diretamente**, sem validação — um
`Host` forjado pelo cliente seria refletido no `rel="canonical"` e nas 4 URLs de compartilhamento
de cada post. `htmlspecialchars` na saída impede XSS, mas **não** impede esse tipo de
"canonical/share poisoning": um atacante poderia obter uma versão do artigo cujo `canonical` e
cujos links de compartilhamento apontam para um domínio de terceiro, com o título/conteúdo real da
CT Price — relevante tanto para SEO (canonical incorreto) quanto para engenharia social
(compartilhar um link que parece da CT Price mas leva a outro lugar).

**Teste real do ataque, antes da correção** (`Host: evil.com` contra o servidor local ao vivo):
`canonical` e os 4 links de compartilhamento continham `evil.com` no lugar do domínio real.

**Correção aplicada**: `CTPRICE_CANONICAL_HOST` (constante única, `'ctprice.com.br'`, definida uma
única vez) + validação: o host da requisição só é aceito se (ignorando porta) for
`ctprice.com.br`, `www.ctprice.com.br`, `localhost` ou `127.0.0.1` (ambiente local) — qualquer
outro valor cai no fallback para `CTPRICE_CANONICAL_HOST`. Solução pequena (uma função, uma
constante), sem sistema de configuração novo.

**Teste real do ataque, depois da correção** (mesmo `Host: evil.com`, servidor ao vivo):
```
<link rel="canonical" href="http://ctprice.com.br/hello-world/">
```
`evil.com` não aparece em nenhum lugar da resposta. Reconfirmado nos 4 links de compartilhamento
(todos usando `ctprice.com.br`).

**Suíte de testes automatizados** (função isolada, `php -r`), cobrindo host legítimo, `www.`,
ambiente local (`127.0.0.1:8099`, preservado — continua funcionando para desenvolvimento),
`evil.com` (com e sem porta) e a tentativa de contornar por prefixo (`ctprice.com.br.evil.com`,
que **não** engana a comparação por ser `in_array` exata, não `str_contains`/`strpos`) — todos os
8 casos testados se comportaram corretamente.

### Segundo achado (defensivo, não uma vulnerabilidade): duplicação de barra

Testado especificamente conforme pedido ("aceita caminho com ou sem `/` de forma previsível"):
`ctprice_absolute_url('hello-world/')` (sem `/` inicial) produzia
`https://ctprice.com.brhello-world/` — host e caminho colados, sem barra separadora. **Corrigido**
com `'/' . ltrim($path, '/')` antes de montar a URL final — testado com `/hello-world/`,
`hello-world/` e `//hello-world/`, os 3 agora produzem exatamente
`https://ctprice.com.br/hello-world/`. Não chegou a afetar nenhuma página real (todos os `url` de
`config/blog-posts.php` já vêm com `/` inicial), mas a função em si não era robusta a chamadas
futuras sem essa garantia — corrigido preventivamente.

### Demais confirmações da Parte N
- Não gera `/wp/` em nenhum caso (a constante/fallback e todos os `url` de origem já são as
  slugs novas).
- HTTPS detectado corretamente em produção (mesma lógica já usada e aprovada em
  `ctprice_configure_session_cookie()`); HTTP puro funciona em ambiente local (sem forçar
  `https://` onde a conexão real é HTTP).
- Escapada corretamente onde usada em HTML: `htmlspecialchars($ctpriceAbsoluteUrl, ENT_QUOTES,
  'UTF-8')` no `canonical`; `rawurlencode()` nos parâmetros das URLs de compartilhamento, com o
  resultado final também passando por `htmlspecialchars` no atributo `href`.

---

## Parte O — Canonical

Confirmado nos 3 posts: absoluto (`https://`/`http://` + host + caminho), aponta para a URL
reconstruída (nunca `/wp/`), slug correto em cada um, **exatamente 1 `<link rel="canonical">` por
artigo** (`grep -c 'rel="canonical"'` = 1 nos 3). Teste de Host Header (Parte N) confirma que não
muda para host arbitrário/malicioso.

---

## Parte P — Meta description / title

| Post | `<title>` | Meta description |
|---|---|---|
| Reforma trabalhista | "Reforma trabalhista volta à pauta do STF; julgamento acontece neste mês — CT Price" | Igual ao excerpt auditado |
| Receita Federal/Correios | "Receita Federal e Correios lançam portal de compras internacionais — CT Price" | Igual ao excerpt auditado |
| Hello World | "Novo golpe mira em empreendedores e cria sites falsos que simulam a geração de documentos — CT Price" | Igual ao excerpt auditado |

Exatamente 1 `<title>` e 1 `meta description` por post — nome "CT Price" presente nos 3 (padrão
"— CT Price" já usado em todas as outras páginas do projeto). Caracteres especiais/acentos
corretamente escapados via `htmlspecialchars`. Nenhum OG/JSON-LD criado nesta etapa (conforme
instrução).

---

## Parte Q — Regressão dos globais (`config/bootstrap.php` foi alterado)

| Página | Status | Warning/Notice/Fatal/Deprecated no HTML |
|---|---|---|
| `/` | 200 | 0 |
| `/fale-conosco/` | 200 | 0 |
| `/ouvidoria/` | 200 | 0 |
| `/arearestrita/` | 200 | 0 |

Header/footer/WhatsApp carregam normalmente nas 4. Token CSRF do formulário de Fale Conosco
confirmado presente no HTML (sessão/CSRF não afetados pelo helper novo — `ctprice_absolute_url()`
não interfere em `session_start()`/cookies). Nenhum erro de função/redeclaração (confirmado:
`grep 'function ctprice_'` não mostra nomes duplicados).

---

## Parte R — Home

3 cards confirmados: títulos/categorias/excerpts/thumbnails/datas idênticos ao já aprovado.
`href` dos 3 (título e "Leia mais »") apontando para os caminhos locais novos — **zero** `/wp/`.
Clique real (não apenas leitura de `href`) testado no card 1: navegação real da Home para
`/reforma-trabalhista-.../` confirmada (não 404, não WordPress).

---

## Parte S — Informações

Mesma regressão: os 3 links (`a[href*="reforma-trabalhista"]`, `a[href*="receita-federal"]`,
`a[href*="hello-world"]`) apontam para os caminhos locais — `grep` por `a[href*="/wp/"]` na página
retorna **0** resultados. Nenhuma alteração visual (dados idênticos aos já usados, só a URL de
destino mudou).

---

## Parte T — Ausência de WordPress

Inspeção consolidada do HTML renderizado (3 posts + Home + Informações): **zero** ocorrências de
`/wp/`, `admin-ajax.php`, `elementor`, `jquery` (case-insensitive), e zero links para o arquivo de
data (`/wp/AAAA/MM/DD/`) nos 3 posts. Os 5 links externos para `contabeis.com.br` (fonte
jornalística) não foram confundidos com dependência interna — são destinos editoriais legítimos,
fora do domínio do projeto.

---

## Parte U — Console e rede

Console: nos 3 posts, só o mesmo ruído de extensão de navegador já documentado em todas as
validações anteriores deste projeto (`[debug] Search endpoint requested!`) — **zero erro
JavaScript próprio**. Rede: todos os assets próprios (CSS `article.css`, thumbnails, logo, fontes,
ícones) em 200 nos 3 posts — **zero 404, zero fonte/imagem quebrada**. Nenhuma dependência
WordPress/Elementor/jQuery carregada.

---

## Correções realizadas nesta validação

1. **Segurança real — Host Header não validado em `ctprice_absolute_url()`** (`config/
   bootstrap.php`): corrigido com `CTPRICE_CANONICAL_HOST` + validação de host permitido,
   confirmado com teste de ataque real antes/depois (Parte N).
2. **Robustez — normalização de barra em `ctprice_absolute_url()`**: `$path` sem `/` inicial
   produzia host e caminho colados; corrigido com `ltrim`, testado nos 3 formatos de entrada.

Nenhuma outra correção foi necessária — conteúdo, template, relacionados, thumbnails, datas,
links externos, compartilhamento e SEO básico já estavam corretos como entregues na implementação.

---

## Diferenças conscientes (consolidado)

- `<main>` semântico (ausente no original).
- Hierarquia H1 → H2 ("Mais notícias") correta, sem salto para H3.
- Data não clicável (corrige o link quebrado `/wp/AAAA/MM/DD/` do original).
- Formato de data/hora 24h unificado (não reproduz o "5:01 pm" em inglês do original).
- Parágrafos vazios residuais do WordPress omitidos (conteúdo real preservado integralmente).
- `alt` real e útil em todas as thumbnails (não `alt=""`).
- Relacionados com 1 link por card, sem redundância de 3 links do widget original.
- Globais (WhatsApp, crédito, menu do footer) unificados com o resto do site — não os valores
  divergentes do template WordPress antigo (já registrados como achado em `blog-posts-audit.md`).
- Container 1140px (não os ~1240px do original) — consistência com o resto do site reconstruído.
- Compartilhamento reimplementado com links/SVG reais, sem Elementor.
- URLs totalmente fora de `/wp/` — slugs preservados exatamente, incluindo `hello-world`.

---

## Screenshots da implementação

- `docs/reference/screenshots/blog-reforma-trabalhista-implementation-{desktop-1440,tablet-900,mobile-390}-full.png`
- `docs/reference/screenshots/blog-receita-federal-correios-implementation-{desktop-1440,tablet-900,mobile-390}-full.png`
- `docs/reference/screenshots/blog-hello-world-implementation-{desktop-1440,tablet-900,mobile-390}-full.png`

(As screenshots da referência original não foram sobrescritas.)
