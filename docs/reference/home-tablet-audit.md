# Auditoria — Home (Tablet / estado intermediário) — ctprice.com.br/wp

## Metodologia

- **URL analisada:** https://ctprice.com.br/wp/
- **Viewport principal:** 900×1200, `devicePixelRatio: 1` (via CDP `Emulation.setDeviceMetricsOverride`, `isMobile:false`, `hasTouch:false` — sem UA móvel, já que o objetivo é a faixa de layout "tablet", não simular um dispositivo touch específico).
- **Data da auditoria:** 2026-08-17
- **Escopo:** somente página Home, somente a faixa **768px–1024px** (com testes pontuais exatamente em 1025, 1024, 768 e 767px para confirmar limites). Não é uma auditoria completa — complementa `home-desktop-audit.md` e `home-mobile-audit.md`, focando no que muda de forma diferente nesse intervalo.
- Todos os valores foram **medidos** (`getComputedStyle`, `getBoundingClientRect`, `data-settings`, classes do Elementor, e leitura direta do CSS publicado via `fetch`), incluindo a extração de regras `@media` para confirmar breakpoints exatos — não estimados visualmente.

Captura de referência: `docs/reference/screenshots/home-tablet-900-full.png`. Altura total do documento em 900px: **7.841px**. A ordem das 13 seções de nível superior é a mesma dos outros dois relatórios.

---

## Achado central: não existe um único "breakpoint mobile"

A CT Price (via Elementor) não usa um sistema de breakpoint único e consistente. **Componentes diferentes respondem a breakpoints diferentes**, dependendo de qual widget/sistema do Elementor os constrói:

1. **Containers `e-grid` (Flexbox/Grid do Elementor 4.x)** — usados nos grids de ícones ("Bem-vindo", "Nossos Serviços") — só têm uma regra responsiva registrada no CSS gerado da página: `@media(max-width:767px)`. **Não existe nenhuma regra em `@media(max-width:1024px)` alterando `grid-template-columns` para esses elementos.** Por isso, em 900px eles continuam com o número de colunas do desktop.
2. **Widget clássico "Posts" (blog "Últimas notícias")** — usa o sistema nativo de colunas responsivas do Elementor (`cards_columns` / `cards_columns_tablet` / `cards_columns_mobile`, refletido nas classes `elementor-grid-3 elementor-grid-tablet-2 elementor-grid-mobile-1`). Este widget **tem** uma configuração explícita para tablet (2 colunas) e as regras correspondentes vêm do CSS genérico do Elementor (`frontend.min.css`): `.elementor-grid-tablet-2 .elementor-grid{grid-template-columns:repeat(2,1fr)}` dentro de `@media(max-width:1024px)`, e `.elementor-grid-mobile-1...` dentro de `@media(max-width:767px)`.
3. **Widget "Nav Menu" do header** — usa a configuração própria "Dropdown Breakpoint: Tablet" (classe `elementor-nav-menu--dropdown-tablet`), com regras em `@media(max-width:1024px)` no CSS do plugin (`widget-nav-menu.min.css`).
4. **Carrossel de logos de clientes** (widget "Image Carousel") — não tem `slides_to_show_tablet` definido no `data-settings` (só existe o campo vazio para espaçamento), mas mesmo assim o número de slides visíveis muda em 900px — provavelmente por um valor padrão interno do Swiper/Elementor para a camada "tablet", não por uma regra CSS de `max-width` explícita e legível estaticamente.

Ou seja: **1024px e 767px coexistem como breakpoints reais e independentes no mesmo site**, cada um controlando um subconjunto diferente de componentes — não há um raciocínio "mobile-first" único aplicado de forma consistente a toda a página.

---

## Header

### Confirmação exata do breakpoint do menu
Testado nos 4 limites solicitados:

| Largura | `.elementor-menu-toggle` | `.elementor-nav-menu--main` |
|---|---|---|
| 1025px | `display:none` | `display:flex` (menu horizontal visível) |
| **1024px** | **`display:flex`** | **`display:none`** |
| 768px | `display:flex` | `display:none` |
| 767px | `display:flex` | `display:none` |

**O menu desktop desaparece exatamente em `max-width: 1024px`** (ou seja, em qualquer largura ≤1024px, incluindo todo o intervalo tablet), regra vinda de `elementor-pro/assets/css/widget-nav-menu.min.css`, associada à classe `elementor-nav-menu--dropdown-tablet` já presente no widget (não é uma regra criada especificamente para este site — é a opção padrão "Tablet" do controle "Dropdown Breakpoint" do Elementor Pro).

### Comportamento do hambúrguer em 900px
- Ao contrário do mobile (390px), onde o **header inteiro** passa a `flex-wrap:wrap` (empilhando logo / menu / botão em coluna), em 900px o header **continua em uma única linha** (`flex-direction:row`, `flex-wrap:nowrap`) — logo à esquerda, hambúrguer ao centro, botão "Área Restrita" à direita. Visualmente é uma barra "desktop" com o menu trocado por um ícone.
- **Confirmação da causa:** o container do header (`.e-con.e-flex`) só recebe `flex-wrap:wrap` dentro de `@media(max-width:767px){ .e-con.e-flex{ --flex-wrap:var(--flex-wrap-mobile) } }` (regra genérica do `frontend.min.css`). Em 900px essa média query não se aplica, então o `flex-wrap` permanece `nowrap` — o header em linha é, na prática, **governado pelo mesmo breakpoint de 767px do resto do conteúdo**, não por um breakpoint de header dedicado.
- **O topbar**, por outro lado, tem `--flex-wrap: wrap` fixado diretamente no CSS do próprio widget (`elementor-element-3aac555d{...--flex-wrap:wrap}`), **sem condicional de media query** — ele sempre pode quebrar linha quando o conteúdo não cabe, em qualquer largura. Em 1440px cabe em 1 linha; em 900px quebra em **2 linhas** ("endereço + telefone" / "WhatsApp + e-mail", bandeiras na mesma linha do topo); em 390px quebra em 3 linhas. Isso não é um "breakpoint" no sentido de media query — é overflow natural de flexbox.

### Painel do menu aberto (dropdown) em 900px
- **Largura: 499px**, `position: static` (fluxo normal, empurra o conteúdo abaixo — mesmo princípio do mobile, **não é overlay/modal**).
- **Comportamento visualmente híbrido e não muito comum:** o painel abre **centralizado horizontalmente sob o ícone do hambúrguer** (x≈206px de 900px de largura), não ocupando a largura total do header como faz no mobile (350px, praticamente full-width). Logo e botão "Área Restrita" permanecem nas extremidades.
- **Efeito colateral:** como o header usa `align-items:center` na linha e o painel do menu (320px de altura quando aberto) se torna o item mais alto da linha, **o logo e o botão "Área Restrita" são recentralizados verticalmente** junto com o painel aberto — ambos descem visivelmente da posição original (logo: y=115px fechado → y=267px aberto; botão: y=105px fechado → y=256,5px aberto). A altura do header cresce de 100px para **403px** quando o menu está aberto. Esse comportamento é diferente do mobile, onde logo e botão simplesmente ficam empilhados acima/abaixo do menu sem se moverem de posição relativa.
- Itens do menu: Roboto **13px / peso 500**, `padding: 10px 20px` — **idêntico ao mobile** (não é um terceiro valor tipográfico; o dropdown usa a mesma tipografia em toda a faixa ≤1024px).
- Submenu ("Clientes e Parceiros", "Trabalhe Conosco"): mesmo comportamento de expansão inline visto no mobile (não testado novamente em detalhe nesta etapa por já estar coberto no relatório mobile e não apresentar diferença).

### Logo
- Renderizado a **135,7×39,3px** em 900px (bem menor que os 232,9×67,5px do desktop e os 350×101,6px do mobile — a largura do logo acompanha a largura da própria coluna do header, que aqui é compartilhada com hambúrguer+botão numa única linha, sobrando menos espaço).
- **`currentSrc` volta a ser `LogoPreferencialColorida-1024x297.png`** (o mesmo arquivo do desktop) — diferente do mobile, que usava `-768x223.png`. Isso **não** é controlado pelos breakpoints de 767/1024 do site: é o próprio atributo `sizes="(max-width: 800px) 100vw, 800px"` da tag `<img>` que, em 900px (>800px), cai no ramo `800px` fixo e o navegador escolhe o candidato `1024w` do `srcset` por ser o mais próximo acima de 800. **Um terceiro "breakpoint" (800px), independente dos outros dois, definido dentro do próprio atributo `sizes` da imagem do logo.**

### Botão "Área Restrita"
- 109,75×60px, mesmo estilo visual (pill outline `#057038`, radius 40px) do desktop — sem mudança de aparência, apenas de posição/altura por causa do `space-evenly` do header.

### Elementos escondidos
- Nenhum elemento com classes `elementor-hidden-*` foi encontrado (mesma conclusão do relatório mobile) — a única alternância é `.elementor-nav-menu--main` (desktop) ↔ `.elementor-nav-menu--dropdown` (tablet/mobile), ambos sempre no DOM.

---

## Hero

- **Altura: 660px** — idêntica ao desktop e ao mobile (nenhum breakpoint altera este valor).
- `background-size: cover`, `background-position: 50% 50%` — idêntico em todas as larguras testadas.
- **`padding-left: 150px` e `font-size: 30px` (Poppins) do texto continuam exatamente os mesmos** do desktop e do mobile — **confirmado via CSS que não existe nenhuma regra `@media` para `.elementor-slide-description` nem para o container do slide** (`elementor-element-714c81f`) em nenhum dos breakpoints (a única regra em `@media(max-width:1024px)` para esse elemento é `--width:100%`, que não afeta o padding interno do texto).
- **Consequência prática em 900px:** como a largura disponível (900 − 150px de padding ≈ 750px úteis) é bem maior que em 390px, **o bug de quebra de linha extrema do mobile não se manifesta aqui** — o texto do slide ("Fornecemos informações precisas e seguras para que você possa tomar as melhores decisões para seu negócio") aparece em ~4 linhas, de forma legível. **O "bug" identificado no relatório mobile não é causado por nenhum breakpoint — é resultado de um padding/font-size fixos que só quebram visualmente quando a largura da viewport é pequena o suficiente** (o problema é de grau, não de configuração condicional).
- **CTA:** não há botão/CTA dentro do hero em nenhum breakpoint (a interação do hero é só o slide de texto).
- **Swiper:** sem setas nem paginação em 900px — idêntico às outras duas larguras (`navigation:"none"` no `data-settings`, valor estático).

---

## Grids e containers — o que muda (e o que não muda) em 900px

| Seção | Colunas em 1440px (desktop) | Colunas em **900px** | Colunas em 390px (mobile) | Breakpoint real de colapso |
|---|---|---|---|---|
| 3 — Bem-vindo (4 icon-boxes) | 4 × 280px | **4 × 196,25px** (ainda desktop) | 1 × 350px | `max-width:767px` |
| 5 — Nossos Serviços (6 cards) | 3 × 393,3px | **3 × 267px** (ainda desktop) | 1 × 346px | `max-width:767px` |
| 8 — Por que nos escolher (imagem + lista) | 2 col (600/600px) | **2 col (442,5/442,5px)**, imagem renderiza normalmente | 1 col empilhada, **imagem colapsa para ~20px de altura (bug)** | `max-width:767px` (é aqui que o bug de altura zerada aparece — em 900px a imagem tem 498px de altura normal) |
| 9 — Últimas notícias (blog) | 3 × 353,3px | **2 × 407,5px** ⚠️ já reflui | 1 × 350px | **`max-width:1024px`** (2 col) e depois `max-width:767px` (1 col) — dois estágios |
| 10 — Formulário de contato | 2 col (342/570px) | **2 col (265,5/442,5px)** (ainda desktop) | 1 col empilhada | `max-width:767px` |
| 11 — Footer principal | 3 col (~360px) | **3 col (259,5px)** (ainda desktop) | 4 blocos empilhados | `max-width:767px` |

O container "boxed" (`max-width`) confirma o mesmo padrão: mede **1200px** em 900px e em 768px (`containerMaxWidth: "1200px"`, já que 900/768 < 1200 o container só ocupa a largura disponível) e só passa a `min(100%, 767px)` **exatamente em ≤767px**.

**Conclusão desta seção:** de todos os grids de conteúdo da Home, **apenas o grid do blog ("Últimas notícias") tem um estágio intermediário real de 2 colunas em tablet**; todos os outros grids constrouídos com `e-grid` (ícones, serviços, formulário, footer) pulam diretamente de desktop para 1 coluna em 767px, sem passar por um estágio de 2 colunas.

---

## Carrosséis em 900px

| | Hero (Slides) | Depoimentos (Testimonial) | Clientes/Parceiros (Image Carousel) |
|---|---|---|---|
| Slides visíveis (medido) | 1 (sempre) | **1** (idêntico a desktop e mobile) | **2** (vs 10 no desktop, vs ~1 no mobile) |
| Largura do slide | full-bleed | 822px (quase o container inteiro, 845px) | 412,5px cada |
| `spaceBetween` | n/a | `10px` (mesmo `data-settings` estático de sempre) | `image_spacing_custom: 20px` (mesmo valor estático) |
| Autoplay | `4000ms` (igual) | `5000ms` (igual) | `5000ms` (igual) |
| Setas | nenhuma (igual) | 2 setas, visíveis (igual) | nenhuma (`navigation:"none"`, igual) |
| Paginação | nenhuma | 4 bullets (igual) | nenhuma |
| Breakpoints no `data-settings` | nenhum (`autoplay_speed`/`transition_speed` fixos) | nenhum (`space_between_tablet` existe mas com valor igual ao desktop: 10px) | **nenhum campo `slides_to_show_tablet` presente no objeto** — o valor "2" observado em 900px não vem de uma configuração explícita do site, é um comportamento padrão do widget para essa faixa de largura |
| Diferença vs desktop | nenhuma | nenhuma | **de 10 para 2 logos visíveis por vez** — degrau intermediário real entre os 10 do desktop e o ~1 do mobile |
| Diferença vs mobile | nenhuma (bug de padding só aparece <900px aprox.) | nenhuma | de 2 para ~1 — a mudança de 2→1 provavelmente ocorre também perto de 767px, mas não foi possível confirmar isso por regra CSS explícita (é comportamento do Swiper/JS do widget, não uma `@media` legível estaticamente) |

---

## Tipografia — apenas o que muda em 768–1024px

| Elemento | Desktop (1440px) | 900px (tablet) | Mudou? |
|---|---|---|---|
| H2 "Bem-vindo à CT Price" | 50px/900 | **50px/900** | Não — herda do desktop |
| H2 "Ética, agilidade..." | 35px/900 | **35px/900** | Não — herda do desktop |
| Eyebrow "NOSSOS SERVIÇOS" / H2 "Deixe a contabilidade..." | 20px/700 · 32px/700 | **idêntico** | Não |
| Botão "Fale Conosco" | 15px/500 | **15px/500** | Não |
| Texto do slide do hero | Poppins 30px/400 | **30px/400** | Não |
| **Menu do header (item)** | Roboto 16px/600 | **Roboto 13px/500** | **Sim — mesmo valor do mobile**, muda junto com a troca para hambúrguer em ≤1024px |

Não há `<h1>` na página em nenhum breakpoint (confirmado nos três relatórios). Nenhum `<h3>` de conteúdo (títulos de card/serviço) muda de tamanho nesta faixa. **Toda a tipografia de conteúdo (headings, parágrafos, botões) permanece idêntica entre 1440px e 900px** — a única tipografia que muda no intervalo tablet é a do menu, e muda exatamente no mesmo ponto (1024px) e para o mesmo valor final (13px/500) que no mobile.

---

## Footer em 900px

- **Continua em colunas** — não empilha ainda. 3 colunas lado a lado (259,5px cada: endereço/e-mails · menu de links · mapa), com o logo centralizado acima ocupando a largura total (865px).
- Larguras reduzidas proporcionalmente (259,5px vs ~360px no desktop) mas **sem mudança de estrutura** — mesmo `display:flex`/`flex-direction:row` do desktop.
- **Mapa:** `<iframe>` redimensiona para 239,5×200px (mobile: 350×200px — largura menor no tablet porque a coluna do mapa em 3-colunas é mais estreita que a largura total disponível no mobile de 1 coluna).
- Alinhamentos e espaçamentos internos permanecem os mesmos do desktop (nenhuma regra de breakpoint encontrada para os elementos do footer entre 1024px e 768px — o footer só reflui em `max-width:767px`, junto com o restante do conteúdo).
- Footer bottom bar (copyright): permanece em **1 linha, 78,4px de altura** em 900px — mesmo comportamento do desktop; só quebra em múltiplas linhas (160,8px) no mobile a 390px.

---

## Confirmação direta dos breakpoints no CSS (resumo)

Testado nos limites exatos solicitados, com resultado consistente com a análise do CSS-fonte:

| Largura | Menu (`toggle`/`main-nav`) | Grid ícones (4 col) | Grid blog (3 col) | `flex-wrap` do header | Container boxed |
|---|---|---|---|---|---|
| **1025px** | desktop (nav visível) | 4 col (desktop) | 3 col (desktop) | `nowrap` | `1200px` |
| **1024px** | **tablet (hambúrguer)** | 4 col (desktop) | **2 col (tablet)** | `nowrap` | `1200px` |
| 900px | tablet (hambúrguer) | 4 col (desktop) | 2 col (tablet) | `nowrap` | `1200px` |
| 768px | tablet (hambúrguer) | 4 col (desktop) | 2 col (tablet) | `nowrap` | `1200px` |
| **767px** | tablet (hambúrguer) | **1 col (mobile)** | **1 col (mobile)** | **`wrap`** | **`min(100%,767px)`** |

Regras-fonte confirmadas por `fetch` + inspeção de `@media`:
- `wp-content/uploads/elementor/css/post-360.css` → `@media(max-width:1024px){...}` e `@media(max-width:767px){...}` (grids `e-grid`, container boxed).
- `wp-content/plugins/elementor/assets/css/frontend.min.css` → `@media (max-width:1024px){.elementor-grid-tablet-2...}` e `@media (max-width:767px){.elementor-grid-mobile-1...}` (grid do widget Posts) e `@media (max-width:767px){.e-con.e-flex{--flex-wrap:var(--flex-wrap-mobile)}}` (wrap do header).
- `wp-content/plugins/elementor-pro/assets/css/widget-nav-menu.min.css` → `@media (max-width:1024px){.elementor-nav-menu--dropdown-tablet .elementor-nav-menu--main{display:none}}` (menu hambúrguer).

Nenhum outro breakpoint relevante para mudanças visuais foi encontrado dentro do intervalo 768–1024px além dos listados acima.

---

## Inconsistências / comportamentos estranhos específicos deste intervalo

1. **Menu já é hambúrguer (≤1024px), mas o corpo da página ainda está em layout de desktop (>767px)** — na faixa 768–1024px inteira, o usuário vê um cabeçalho "mobile" acima de um conteúdo "desktop" (grids de 3–4 colunas). Não há nada de errado tecnicamente (são dois breakpoints diferentes por design de dois sistemas diferentes do Elementor), mas o resultado visual é uma mistura pouco coerente.
2. **O grid do blog é o único que tem 3 estágios reais (3 → 2 → 1 coluna)**; todos os outros grids da Home pulam diretamente de desktop para 1 coluna — inconsistência de tratamento responsivo entre seções que, à primeira vista, parecem seguir o mesmo padrão visual.
3. **O painel do menu aberto empurra e recentraliza logo + botão "Área Restrita"** em 900px (ambos descem ~150px), um efeito colateral do `align-items:center` na linha do header que não ocorre da mesma forma no mobile (lá os itens já estão em coluna, então "descer" é o comportamento natural esperado).
4. **O logo troca de arquivo-fonte com base num breakpoint próprio da tag `<img>` (800px, via atributo `sizes`)**, que não coincide com nenhum dos dois breakpoints estruturais do site (767px/1024px) — em 900px usa o arquivo de 1024×297, igual ao desktop, enquanto no mobile (390px) usa o de 768×223.
5. **O carrossel de logos de clientes muda de 10 → 2 → ~1 itens visíveis** sem que exista uma configuração explícita de `slides_to_show_tablet` no `data-settings` — o valor "2" em tablet parece ser um comportamento padrão do próprio widget, não uma decisão de design registrada.

Todos os pontos acima são registrados como estão no site de referência, sem qualquer correção.

---

## Arquivos de referência visual

- `docs/reference/screenshots/home-tablet-900-full.png` — captura de página inteira em 900px de largura.
