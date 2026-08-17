# Auditoria — Home (Desktop) — ctprice.com.br/wp

## Metodologia

- **URL analisada:** https://ctprice.com.br/wp/
- **Viewport:** 1440×900 (largura útil de renderização: 1425px — 15px consumidos pela scrollbar do navegador)
- **Ferramenta:** Chrome DevTools MCP (navegação, `getComputedStyle`, `getBoundingClientRect`, inspeção de `data-settings` do Elementor, inspeção de `document.styleSheets` para regras `:hover`, aba de rede)
- **Data da auditoria:** 2026-08-17
- **Escopo:** somente página Home, somente desktop (1440×900). Não cobre tablet/mobile.
- Todos os valores abaixo foram **medidos**, não estimados. Onde uma medição não foi possível (ex.: durações de easing não expostas via computed style), isso é sinalizado explicitamente.

### Stack identificada (apenas diagnóstico — não é para ser usada como arquitetura do novo site)

WordPress + Elementor 4.2.2 + Elementor Pro 4.1.2 + tema **Hello Elementor** 3.4.6, jQuery 3.7.1, Swiper 8.4.5, plugins: Cookie Notice 2.5.13, GTranslate 3.1.1, Mask Form Elementor 4.4.5, Pojo Accessibility 4.1.0. Todas as seções da Home são construídas com o sistema de **containers Flexbox/Grid do Elementor** (`e-con`, `e-grid`), não com o antigo sistema de `section/row/column`.

Captura de referência em página inteira: `docs/reference/screenshots/home-desktop-1440-full.png`.

---

## 1. Estrutura completa e ordem das seções

A Home é um único `<div class="elementor elementor-360">` com 13 containers de nível superior (top-level), em ordem:

| # | Conteúdo | Tipo de container | y (top) | Altura | Margin vertical |
|---|---|---|---|---|---|
| 0 | Topbar (contato + idiomas) | full-width | 0 | 66px | 0 |
| 1 | Header (logo + menu + CTA) | full-width | 66 | 132px | 0 |
| 2 | Hero — slider institucional | full-width | 198 | 660px | 0 |
| 3 | "Bem-vindo à CT Price" + 4 icon-boxes | boxed 1200px | 908 | 566.6px | 50px |
| 4 | "Ética, agilidade..." + vídeo institucional | boxed 1200px | 1524.6 | 837.4px | 25px |
| 5 | "Nossos Serviços" (6 cards) | boxed 1200px | 2387 | 1090.4px | 0 |
| 6 | Depoimentos (carrossel) | boxed 1200px | 3527.3 | 486.3px | 50px |
| 7 | Carrossel de clientes/parceiros (logos) | boxed 1200px* | 4063.7 | 200px | 0 |
| 8 | "Por que nos escolher?" | boxed 1200px | 4263.7 | 602px | 0 |
| 9 | "Últimas notícias" (blog) | boxed 1200px | 4915.7 | 688.5px | 50px |
| 10 | "Quer receber um contato?" (form) | full-width, bg verde-escuro | 5654.2 | 471px | 0 |
| 11 | Footer principal (endereço/menu/mapa) | boxed 1200px, bg cinza-claro | 6125.2 | 400px | 0 |
| 12 | Footer bottom bar (copyright) | boxed 1200px, bg quase-preto | 6525.2 | 78.4px | 0 |

\* o container do carrossel de logos é `e-con-boxed` mas seu conteúdo (imagens) usa `object-fit: fill` numa faixa de 200px de altura.

**Altura total do documento:** 6.603,6px em 1440×900.

Margens verticais entre seções colapsam normalmente (CSS padrão): o espaço visível entre duas seções é o **maior** dos dois `margin` adjacentes, não a soma.

---

## 2. Header e navegação

### 2.1 Topbar (seção 0)
- Full-width, `padding: 10px`.
- **Background:** `linear-gradient(rgb(0, 34, 44) 0%, rgb(5, 112, 56) 100%)` → `linear-gradient(#00222C 0%, #057038 100%)`, aplicado num `::before`/wrapper interno (não no elemento raiz).
- Conteúdo real (linha única): ícone + "R. José Antônio, 2.777 Monte Castelo - Campo Grande - MS" · ícone + "(67) 3313-7300" · ícone + link WhatsApp "(67) 99261-6117" (`https://api.whatsapp.com/send?phone=5567992616117`) · ícone + "contato@ctpricems.com.br" · 3 bandeiras de idioma (GTranslate: pt-br, en-us, es — 24×24px cada, `<img>`).
- Texto: `Roboto Flex, sans-serif`, 15px / peso 500 / cor `rgb(254,254,254)` (`#FEFEFE`).
- Ícones: SVG (Font Awesome via Elementor, ex. `e-fas-map-marker-alt`), área 16px, `fill: rgb(16,227,107)` (`#10E36B`).

### 2.2 Header/nav (seção 1)
- Full-width, `padding: 10px`, `background-color: transparent` (fica sobre o branco do body), `position: relative`, `box-shadow: none`.
- **Logo:** `LogoPreferencialColorida-1024x297.png`, natural 800×232px, renderizado 232.9×67.5px.
- **Menu** (`ul.elementor-nav-menu`, 8 itens de topo): Início · A CT Price · Clientes e Parceiros (dropdown: Clientes, Parceiros) · Fale Conosco · Informações · Trabalhe Conosco (dropdown: Vagas, Benefícios) · Ouvidoria · Depoimentos.
  - Link: `Roboto, sans-serif`, 16px / peso 600 / `line-height: 20px` / cor `rgb(5,112,56)` (`#057038`).
  - Padding do item: `10px 20px` (ou `13px 20px` no seletor `:hover/:focus`, conforme a folha de estilo).
  - `transition: 0.4s` (propriedade não detalhada no computed style; herdado do CSS do Elementor Nav Menu).
  - Dropdown (`.sub-menu`, oculto até hover/foco): item em hover → `background-color: rgb(0,34,44)` (`#00222C`), `color: rgb(16,227,107)` (`#10E36B`).
- **Botão "Área Restrita"** (`elementor-button`, widget `7b41d49c`): pill outline — `border: 3px solid #057038`, `border-radius: 40px`, `padding: 12px 24px`, `background: transparent`, texto `Roboto` 15px/500 cor `#057038`.
  - **Hover/focus:** `background-color: #00222C`, `color: #10E36B`, `border-color: transparent`; ícone SVG interno (se houver) `fill: #10E36B`. `transition: 0.3s`.

### 2.3 Comportamento do header
- `position: relative` — **o header NÃO é sticky/fixo**. Ao rolar a página ele se move junto com o conteúdo normalmente (confirmado via scroll + leitura do computed style).
- Não há nenhuma classe de "header encolhido"/scrolled aplicada via JS neste template.

---

## 3. Larguras de container

- Container "boxed" padrão do Elementor usado em quase todas as seções de conteúdo: **`max-width: 1200px`**, centralizado (`padding-left/right: 0`, margin automático).
  - Confirma-se pela matemática: viewport útil 1425px → gutter de `(1425-1200)/2 = 112.5px` de cada lado.
- Seções full-width (sem limite de largura do conteúdo): Topbar, Header, Hero (slider), seção de contato (fundo verde-escuro), seção de logos de clientes usa boxed mas a faixa do carrossel ocupa toda a largura do container.
- Grid de 4 colunas (seção "Bem-vindo"): colunas de **280px** fixas, gap 20px, total 4×280+3×20=1180px (dentro do container 1200px, sobra 20px de padding via `e-grid` com `padding: 10px`).
- Grid de 3 colunas (seção "Nossos Serviços"): colunas de **393.33px**, gap 20px.
- Grid de 3 colunas (blog "Últimas notícias"): colunas de **353.33px**, gap **35px 30px** (row-gap / column-gap distintos).
- Coluna de imagem em "Por que nos escolher": **600px** (50% do container) por **450px** de altura.

---

## 4. Grids e colunas (resumo)

| Seção | Tipo | Colunas | Gap |
|---|---|---|---|
| Bem-vindo (4 icon-boxes) | `display: grid` | 4 × 280px | 20px |
| Nossos Serviços (6 cards) | `display: grid` | 3 × 393.33px (2 linhas) | 20px |
| Por que nos escolher | flex 2 colunas | 600px / 600px | — |
| Últimas notícias (3 posts) | `display: grid` | 3 × 353.33px | 35px (linha) / 30px (coluna) |
| Formulário de contato | flex 2 colunas | 342px / 570px | — |
| Footer principal | flex 3 colunas | ~360px cada | — |

---

## 5. Paddings, margins e gaps relevantes

- Ritmo vertical entre seções de conteúdo: **50px** (padrão) ou **25px** (entre "Bem-vindo" e "Ética..."), por `margin-top`/`margin-bottom` nos containers — ver tabela da seção 1.
- `e-grid` da seção "Nossos Serviços": `padding: 10px`.
- Cards de serviço (`.elementor-widget-container`): `border: 1px solid #057038`, `border-radius: 15px` (padding interno não isolado à parte — o texto centralizado ocupa a largura do card).
- Inputs do formulário: `padding: 8px 16px`.
- Botões (`.elementor-button`): `padding: 12px 24px` (Fale Conosco/Área Restrita) ou `padding: 0px 24px` (Enviar, altura controlada por `line-height`).
- Badge de categoria do blog: `padding: 7.2px 14.4px`.

---

## 6. Tipografia

### Famílias carregadas (Google Fonts hospedadas localmente pelo Elementor)
- **Roboto** (pesos usados: 400, 500, 600, 900)
- **Roboto Slab** (peso 400)
- **Roboto Flex** (peso 500)
- **Poppins** (pesos 400, 600)
- Fallback do tema/reset (`-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, ...`) aparece em elementos genéricos (`elementor-360`, containers) por herança do reset padrão do Elementor — não é uma escolha de design intencional do conteúdo.

### Tamanhos / pesos / line-height medidos por elemento

| Elemento | Fonte | Tamanho | Peso | Line-height | Cor |
|---|---|---|---|---|---|
| Menu (header) | Roboto | 16px | 600 | 20px | `#057038` |
| Topbar texto | Roboto Flex | 15px | 500 | — | `#FEFEFE` |
| Botão (Área Restrita / Fale Conosco / Enviar) | Roboto | 15px | 500 | — | varia |
| Hero — descrição do slide | Poppins | 30px | 400 | — | `#00222C` (destaques em `#2A3855` bold inline) |
| H2 "Bem-vindo à CT Price" | Roboto | 50px | 900 | 50px | `#084020` |
| Lead ("Obtenha consultoria...") | Roboto | 16px | 400 | 24px | `#7A7A7A` |
| Título icon-box (seção Bem-vindo) | Roboto | 28px | 600 | 33.6px | `#057038` |
| H2 "Ética, agilidade..." | Roboto | 35px | 900 | 35px | `#084020` (centralizado) |
| Parágrafo (seção Ética) | Roboto | 16px | 400 | 24px | `#7A7A7A` (centralizado) |
| Eyebrow "NOSSOS SERVIÇOS" | Roboto | 20px | 700 | — | `#00222C` |
| H2 "Deixe a contabilidade..." | Roboto | 32px | 700 | — | `#057038` |
| Título card de serviço | Poppins | 28px | 600 | — | `#10E36B` |
| Descrição card de serviço | Roboto | 16px | 400 | 24px | `#7A7A7A` |
| H2 "O que dizem nossos clientes" | Roboto | 35px | 600 | — | `#084020` (centralizado) |
| Texto do depoimento | Roboto | 20.8px | 400, *italic* | 31.2px | `#7A7A7A` (centralizado) |
| Nome do depoente | Roboto | 14px | 600 | — | `#2A3855` |
| Empresa do depoente | Roboto Slab | 14px | 400 | — | `#3D4C6F` |
| H2 "Por que nos escolher?" | Roboto | 35px | 600 | — | `#084020` |
| Título item (Por que nos escolher) | Roboto | 25px | 600 | — | `#084020` |
| Texto item (Por que nos escolher) | Roboto | 16px | 400 | — | `#7A7A7A` |
| H2 "Últimas notícias" | Roboto | (herdado 35px/600, mesmo padrão dos H2 acima) | — | — | `#084020` |
| Título do post (blog) | Roboto | 21px | 600 | — | `#084020` |
| Badge de categoria (blog) | Roboto | 12px | 500 | — | branco sobre cor da categoria |
| "Quer receber um contato?" (heading) | Roboto | — (bloco em card verde) | 700-ish | — | branco |
| Labels do formulário | Roboto | 15px (aprox., herdado) | — | — | `#057038` |
| Placeholder dos inputs | Roboto | 15px | 400 | — | cinza |

---

## 7. Cores reais (paleta observada)

Cores de texto/ícone mais usadas (contagem de ocorrências no DOM computado):

| Cor | Hex | Uso típico |
|---|---|---|
| `rgb(51,51,51)` | `#333333` | cor "default" herdada do reset (corpo de texto genérico) |
| `rgb(0,34,44)` | `#00222C` | verde-petróleo escuro — headings secundários, ícones de serviço, footer bottom bar, hover de menu/botão |
| `rgb(8,64,32)` | `#084020` | verde-escuro — headings principais (H2), fundo da seção de contato |
| `rgb(5,112,56)` | `#057038` | verde-marca — links de menu, bordas de ícones/cards, botão outline |
| `rgb(16,227,107)` | `#10E36B` | verde-vivo (accent) — ícones do topbar, borda da moldura "Nossos Serviços", título dos cards de serviço, cor de hover |
| `rgb(122,122,122)` | `#7A7A7A` | cinza — texto de parágrafo/descrição em quase todas as seções |
| `rgb(255,255,255)` | `#FFFFFF` | branco — textos sobre fundo escuro |
| `rgb(254,254,254)` | `#FEFEFE` | branco (quase-puro) — texto do topbar |
| `rgb(42,56,85)` | `#2A3855` | azul-marinho — nome do depoente, destaques em negrito no hero |
| `rgb(61,76,111)` | `#3D4C6F` | azul-acinzentado — subtítulo (empresa) do depoente |
| `rgb(97,206,112)` | `#61CE70` | verde-médio — background de botões preenchidos (Fale Conosco, Enviar) |
| `rgb(239,203,57)` | `#EFCB39` | amarelo — bullet inativo do carrossel de depoimentos |
| `rgb(166,111,46)` | `#A66F2E` | marrom/dourado — badge de categoria "FOLHA DE PAGAMENTO" (cor varia por categoria do post) |
| `rgb(239,240,241)` | `#EFF0F1` | cinza muito claro — background do footer principal |
| `rgb(63,194,22)` | `#3FC216` | verde — hover do botão "Enviar" |
| `rgb(0,169,157)` | `#00A99D` | teal — botões "Ok"/"Não" do aviso de cookies |
| `rgb(105,114,125)` | `#69727D` | cinza-azulado — borda dos inputs do formulário |
| `rgb(63,68,75)` | `#3F444B` | cinza-escuro — hover de item de dropdown do menu |

**Gradiente:** topbar usa `linear-gradient(#00222C 0%, #057038 100%)` (vertical, de cima para baixo).

---

## 8. Backgrounds por seção

| Seção | Background |
|---|---|
| Topbar | gradiente `#00222C → #057038` |
| Header | transparente (branco do body) |
| Hero | imagem de fundo por slide (`background-image`, cover), sem overlay de cor detectado além do gradiente do texto |
| Bem-vindo / Ética / Serviços / Depoimentos / Logos / Por que escolher / Blog | branco (`#FFFFFF`, herdado do `body`) |
| Contato (form) | `#084020` (verde-escuro sólido) |
| Footer principal | `#EFF0F1` |
| Footer bottom bar | `#00222C` |
| Cookie notice | `rgba(50,50,58,0.85)` |

`document.body { background-color: rgb(255,255,255) }`; `<html>` sem cor própria (transparente).

---

## 9. Bordas e border-radius

| Elemento | Border | Radius |
|---|---|---|
| Botão "Área Restrita" | `3px solid #057038` | `40px` (pill) |
| Ícone circular (seção Bem-vindo) | `3px solid #057038` | `50%` |
| Card de serviço | `1px solid #057038` | `15px` |
| Moldura da seção "Nossos Serviços" (container inteiro) | `2px solid #10E36B` | `20px` |
| Caixa "Quer receber um contato?" | `2px solid #10E36B` | `15px` |
| Botões preenchidos (Fale Conosco / Enviar) | nenhuma | `3px` |
| Badge de categoria (blog) | nenhuma | `999px` (pill) |
| Input do formulário | `1px solid #69727D` | `3px` |
| Imagem de "Por que nos escolher" | nenhuma | `15px` |
| Avatar do depoimento | nenhuma | `10px` |
| Botão do cookie notice (Ok/Não) | nenhuma | `32px` (pill) |
| Imagens do hero, thumbs do blog, logos de clientes | nenhuma | `0px` |

Valores de `border-radius` distintos observados no site inteiro: `2px, 3px, 10px, 15px, 20px, 40px, 50%, 999px`.

---

## 10. Sombras (box-shadow)

O site é predominantemente **flat** — quase nenhum elemento usa sombra:

- `.elementor-post__card` (cards do blog): `box-shadow: rgba(0,0,0,0.15) 0px 0px 10px 0px`; no **hover**: `rgba(0,0,0,0.15) 0px 0px 30px 0px` (regra `.elementor-card-shadow-yes .elementor-post__card:hover`).
- `.grecaptcha-badge` (widget do Google reCAPTCHA v3, fora do controle de design do site): `rgb(128,128,128) 0px 0px 5px 0px`.
- Nenhum outro elemento medido (botões, cards de serviço, header, hero) possui `box-shadow`.

---

## 11. Botões e seus estados

| Botão | Normal | Hover/Focus | Transition |
|---|---|---|---|
| **Área Restrita** (header) | `bg: transparent`, `border: 3px solid #057038`, `color: #057038`, `radius: 40px` | `bg: #00222C`, `color: #10E36B`, `border-color: transparent` | `0.3s` |
| **Fale Conosco** (Nossos Serviços) | `bg: #61CE70`, `color: #084020`, `radius: 3px` | `bg: #10E36B` | `0.3s` |
| **Enviar** (formulário) | `bg: #61CE70`, `color: #FFFFFF`, `radius: 3px` | `bg: #3FC216`, `color: #FFFFFF`, ícone `fill: #FFFFFF` | (não medido individualmente; herda `.elementor-button`) |
| **Item de menu (topo)** | `color: #057038` | `padding: 13px 20px` no estado hover/focus (mesma cor de texto, sem mudança de cor detectada no seletor genérico) | `0.4s` |
| **Item de dropdown** | `color` padrão do tema | `bg: #3F444B` (genérico) / `bg: #00222C`, `color: #10E36B` (customizado deste menu) | — |
| **Ícone genérico `.elementor-icon`** | cor própria do widget | `color: #69727D` | — |
| **Cookie notice — Ok/Não** | `bg: #00A99D`, `color: #FFFFFF`, `radius: 32px` | não medido (biblioteca de terceiros) | — |

---

## 12. Hero — slider institucional

- Widget **Elementor Pro "Slides"** sobre **Swiper 8.4.5**.
- **4 slides reais** (o DOM contém 12 `<div class="swiper-slide">` porque o Swiper triplica os slides em modo loop).
- Configuração (`data-settings` do widget):
  ```json
  {
    "navigation": "none",
    "autoplay_speed": 4000,
    "autoplay": "yes",
    "infinite": "yes",
    "transition": "slide",
    "transition_speed": 500
  }
  ```
  → sem setas nem bullets visíveis; troca automática a cada 4s; transição de deslize de 500ms.
- Altura do slide: 640px (dentro da seção de 660px).
- Cada slide = `div.swiper-slide-bg` (imagem de fundo, `background-size: cover` implícito) + `div.swiper-slide-contents` (classe de animação `animated fadeInUp`) + `.elementor-slide-description`.
- **Conteúdo das 4 slides:**
  1. `caroussel01.jpg` — "**Cuide da sua empresa,** e deixe a contabilidade nas mãos de quem entende" (trecho em negrito `#2A3855`)
  2. `csinicial02.jpg` — "Trabalhamos **integrados** aos colaboradores de sua empresa, para que juntos possamos obter **os melhores resultados**"
  3. `caroussel02.jpg` — "Atuamos nos ramos de contabilidade e planejamento tributário em formato digital **sem papel e sem burocracia**."
  4. `caroussel03a.jpg` — "Fornecemos informações **precisas e seguras** para que você possa tomar as **melhores decisões** para seu negócio"

---

## 13. Outros sliders/carrosséis

### Depoimentos (Elementor Pro "Testimonial Carousel")
```json
{
  "show_arrows": "yes",
  "pagination": "bullets",
  "speed": 500,
  "autoplay": "yes",
  "autoplay_speed": 5000,
  "loop": "yes",
  "pause_on_hover": "yes",
  "pause_on_interaction": "yes",
  "space_between": "10px"
}
```
- 4 depoimentos reais. Setas existem no DOM (`.elementor-swiper-button-prev/next`) mas com estilo discreto. Bullets: 6×6px, círculo, inativo `#EFCB39`, ativo `#2A3855`.

### Carrossel de logos de clientes (Elementor "Image Carousel")
```json
{
  "slides_to_show": "10",
  "slides_to_scroll": "10",
  "navigation": "none",
  "autoplay": "yes",
  "pause_on_hover": "yes",
  "pause_on_interaction": "yes",
  "autoplay_speed": 5000,
  "infinite": "yes",
  "speed": 500,
  "image_spacing_custom": "20px"
}
```
- ~97 logos (ver seção 16 — Imagens). Cada logo renderiza em ~100×65.5px, `object-fit: fill`, 10 visíveis por vez.

---

## 14. Animações

- Classe global `.animated { animation-duration: 1.25s; }` (base do Elementor, estilo animate.css) aplicada via JS quando o elemento entra no viewport (scroll-triggered). Elementos chegam com classe `elementor-invisible` até a animação disparar.
- Animações de entrada observadas por seção:
  - Hero: `fadeInUp` no conteúdo de cada slide.
  - Icon-boxes "Bem-vindo à CT Price": `fadeInLeft`.
  - "Por que nos escolher": `fadeInRight`.
  - Botão "Fale Conosco": `elementor-animation-bounce-in` (efeito de hover, biblioteca de animações do Elementor, não scroll-in).
  - Ícone WhatsApp flutuante: `elementor-animation-push` (efeito de hover).
  - Ícones dos icon-boxes: classe `elementor-animation-grow` (efeito de hover — cresce ao passar o mouse).
- Transições de hover medidas via computed style: botões `0.3s`, menu `0.4s`, card de post `0.25s` (propriedade exata/`easing` não exposta por `getComputedStyle` de forma agregada — apenas a duração).

---

## 15. Ícones

- Fonte: SVGs inline gerados pelo Elementor a partir do conjunto **Font Awesome** (classes `e-fas-*`, `e-fab-whatsapp`) e do conjunto próprio **eicon** (`e-eicon-play`).
- Ícones identificados: `map-marker-alt`, `phone`/telefone, `whatsapp` (fab), `envelope`, `chart-bar`, `building`, `money-bill` (planejamento tributário), `hat-cowboy` (produtor rural), `users` (folha de pagamento), `handshake` (consultoria), `play` (vídeo).
- Cores de ícone variam por contexto: `#10E36B` (topbar), `#057038` (icon-box "Bem-vindo"), `#00222C` (icon-box "Nossos Serviços"), branco (WhatsApp flutuante, sobre fundo verde).

---

## 16. Imagens utilizadas (Home)

### Logo
- `LogoPreferencialColorida-1024x297.png` — usada no header (232.9×67.5px) e no footer (tamanho próprio).

### Hero (4 imagens de fundo)
- `caroussel01.jpg`, `csinicial02.jpg`, `caroussel02.jpg`, `caroussel03a.jpg`

### Vídeo institucional
- `capavideoinstitucional.jpg` (capa/overlay, 1120×630px)

### "Por que nos escolher"
- `pexels-karolina-grabowska-7876708-768x512.jpg`

### Depoimentos (avatares, 65×65px)
- `agrotouro.jpg` e mais 3 fotos de depoentes (carrossel com 4 itens)

### Blog (thumbnails, formato `.webp`)
- `blog01-300x155.webp`, `blog02-300x155.webp`, `blog03-300x155.webp`

### Idiomas (GTranslate, 24×24px)
- `pt-br.png`, `en-us.png`, `es.png`

### Carrossel de clientes/parceiros (~97 logos, pasta `wp-content/uploads/2024/09/`)
Lista completa (nomes de arquivo, sem caminho — todos sob `.../wp-content/uploads/2024/09/` exceto onde indicado):

`agriseiva_057e47b7.png`, `agro-buso_fc9ec0bc.jpeg`, `alfa.jpg`, `alumix.jpg`, `arkad.jpeg`, `artpan.jpg`, `capital.jpg`, `carol-nathan_724ef560.jpg`, `casa-da-sementes.jpg`, `close-up-person-working-call-center-scaled.jpg`, `comak.jpg`, `corujao_9251c69f.jpeg`, `dale.jpg`, `dib.png`, `dimaq.jpg`, `domine_b50ea254.jpg`, `estoque_627cc681.jpg`, `eco-park_b21bb592.jpg`, `farmacias-associadas.jpeg`, `figueira.png`, `g.png`, `genos.jpg`, `giocondo.jpg`, `gmad_4c26933a.png`, `groupacj.jpg`, `health-brasil_d0fe5f29.jpeg`, `hm_80f2d712.png`, `homeo.jpeg`, `hr-rodan.jpg`, `hvm_9ecc1df3.png`, `ifa-1.png`, `image-3_8b445a2e.png`, `image-4_c1b84f9b.png`, `image-9.png`, `image-10_1bc2a900.png`, `image-11_e554fb56.png`, `image-12_2a5c8312.png`, `image-13_785c1623.png`, `image-14.png`, `imbra.jpg`, `infoarkad-1.jpeg`, `infoendosurgical-1.png`, `infofruteli-1.jpeg`, `infogala-1.jpeg`, `infomacal-1.jpeg`, `js-distribuidora_6a23f49f.jpeg`, `kardol.png`, `ki-karnes.jpeg`, `lider-aco.jpg`, `liquida-1.png`, `logo_0002_Camada34.jpg`, `logo_0003_Camada33.jpg`, `logo_0014_Camada22.jpg`, `logo_0017_Camada19.jpg`, `logo_0018_Camada18.jpg`, `logo_0022_Camada14.jpg`, `logo_0024_Camada12.jpg`, `logo_0034_Camada2.jpg`, `logo-zornimat_2ae545f4.png`, `lopes.jpg`, `macal.jpg`, `meta.jpg`, `mix.jpg`, `multi-coisas.jpg`, `natus_f072d16c.png`, `omegamed_bacc5628.png`, `paoetal.jpg`, `pro-nutri.jpg`, `saborzitos-removebg-preview_c63b3438.png`, `santana-haddad.jpg`, `sermix.jpg`, `smartfit.jpg`, `soman.jpg`, `so-sal.jpg`, `studio-vip.jpg`, `suprimed.jpeg`, `tcm.jpg`, `techagro.jpg`, `termo-truck.jpg`, `uniao.jpeg`, `vitrine.jpg`, `agrotouro.jpg` (também usada em depoimentos), `Mauro-Cesar-Senna-e1727373460431.png`, `image-37.png` *(em `/2024/10/`)*, `Rosted-Potato.jpg` *(em `/2024/10/`)*.

> Observação: durante a auditoria, 3 requisições de imagem retornaram **404** (arquivos ausentes no servidor de referência): `mv.jpg`, `modelo.jpg`, `logo_0020_Camada16.jpg`. Isso indica logos "quebrados"/placeholders no carrossel do site atual — registrar, não corrigir por iniciativa própria.

---

## 17. Componentes que se repetem

- **Icon-box** em 3 variações visuais: (a) círculo com contorno + ícone, usado em "Bem-vindo"; (b) card com borda 1px + ícone grande sólido, usado em "Nossos Serviços"; (c) sem ícone, só título+texto, usado em "Por que nos escolher".
- **Heading com eyebrow + divider fino**: padrão "rótulo pequeno em caixa alta + linha + H2 grande", usado em "Nossos Serviços", "Depoimentos", "Por que nos escolher", "Últimas notícias".
- **Botão pill outline** (header) vs **botão preenchido radius 3px** (CTAs de conteúdo/formulário) — dois estilos de botão distintos coexistindo.
- **Swiper carousel** reaproveitado 3 vezes (hero, depoimentos, logos) com configurações de autoplay diferentes.
- **Card com moldura verde arredondada** (`border 2px solid #10E36B`, `radius 15–20px`) usado tanto na seção "Nossos Serviços" (moldura externa) quanto na caixa "Quer receber um contato?".

---

## 18. Footer

### Footer principal (seção 11, bg `#EFF0F1`, altura 400px)
- Logo CT Price centralizado no topo.
- 3 colunas (~360px cada):
  1. Endereço ("R. José Antônio, 2.777 / Monte Castelo – CEP: 79.010-190 / Campo Grande – MS"), e-mails (`contato@ctpricems.com.br`, `protecaodedados@ctpricems.com.br`), Responsável Técnico (Marcelo Barbosa da Silva | CRC MS 7986-O).
  2. Menu secundário (Início, A CT Price, Nossos Clientes, Nossos Parceiros, Fale Conosco, Informações, Trabalhe Conosco, Benefícios).
  3. Mapa incorporado via `<iframe>` do Google Maps (`maps.google.com/maps?q=...&t=m&z=15&output=embed`), com link "Abrir no Maps".

### Footer bottom bar (seção 12, bg `#00222C`, altura 78.4px)
- "© Copyright 2024 CT Price Organização Contábil." + "Desenvolvido por **Agência Lester**" (link para `agencialester.com.br`).

---

## 19. Elementos flutuantes / globais

- **Botão WhatsApp fixo**: `position: fixed`, `bottom: 50px; left: 35px`, ícone `e-fab-whatsapp`, link `https://api.whatsapp.com/send?phone=5567992616117`, efeito hover "push" (biblioteca de animação do Elementor).
- **reCAPTCHA v3 badge** (Google, fixo no canto inferior direito) — visual e comportamento controlados pelo Google, fora do design do site.
- **Aviso de cookies**: barra fixa inferior, `background: rgba(50,50,58,0.85)`, texto 13px `#333`, botões "Ok"/"Não" pill (`bg #00A99D`, `radius 32px`).

---

## 20. Requisições de rede relevantes

### CSS principais (ordem de carregamento)
1. `wp-content/plugins/cookie-notice/css/front.min.css`
2. `wp-content/plugins/pojo-accessibility/assets/build/skip-link.css`
3. `wp-content/themes/hello-elementor/assets/css/reset.css`
4. `wp-content/themes/hello-elementor/assets/css/theme.css`
5. `wp-content/themes/hello-elementor/assets/css/header-footer.css`
6. `wp-content/plugins/elementor/assets/css/frontend.min.css`
7. `wp-content/uploads/elementor/css/post-43.css` (CSS gerado pelo Elementor para o template do header/página) e `post-360.css` (CSS gerado especificamente para o conteúdo da Home, id 360 — confirma `elementor-360`)
8. Vários `widget-*.min.css` do Elementor/Elementor Pro (icon-list, image, nav-menu, heading, icon-box, video, divider, slides, testimonial-carousel, carousel-module-base, image-carousel, posts, form, google_maps)
9. Animações: `fadeInUp`, `fadeInLeft`, `fadeInRight`, `fadeIn`, `e-animation-grow`, `e-animation-pop`, `e-animation-bounce-in`, `e-animation-push`
10. `swiper.min.css` (v8.4.5) + `e-swiper.min.css`
11. Google Fonts locais: `roboto.css`, `robotoslab.css`, `robotoflex.css`, `poppins.css`
12. `mask-form-elementor/assets/css/mask-frontend.css`
13. `gstatic.com/recaptcha/.../styles__ltr.css`

### Fontes (woff2, hospedadas localmente em `wp-content/uploads/elementor/google-fonts/fonts/`)
- Roboto (2 variações estáticas), Roboto Slab, Roboto Flex, Poppins (3 variações estáticas) + 1 arquivo Roboto servido via `fonts.gstatic.com` (fallback dinâmico do Google Fonts API).

### Bibliotecas JS relevantes ao comportamento visual
- `jquery.min.js` 3.7.1 + `jquery-migrate.min.js` 3.4.1 + `jquery-ui/core.min.js` 1.13.3
- `swiper.min.js` 8.4.5 — motor de todos os carrosséis (hero, depoimentos, logos)
- `smartmenus.min.js` 1.2.1 — menu dropdown do header
- `imagesloaded.min.js` 5.0.0
- `elementor/assets/js/frontend.min.js` + `frontend-modules.min.js` + `webpack.runtime.min.js`
- `elementor-pro/assets/js/frontend.min.js` + `elements-handlers.min.js` + módulos bundle: `nav-menu`, `slides`, `carousel`, `posts`, `form`, `popup`
- `elementor/assets/js/image-carousel...bundle.min.js`, `video...bundle.min.js`, `text-editor...bundle.min.js`
- `mask-form-elementor` (jquery.mask + scripts custom) — máscara do campo telefone
- `cookie-notice/js/front.min.js`
- `gtranslate/js/flags.js` — seletor de idioma
- Google reCAPTCHA v3 (`recaptcha/api.js?render=explicit`) — validação do formulário de contato
- Widget externo Kaspersky (`gc.kis.v2.scr.kaspersky-labs.com/.../main.js`) — **não pertence ao site**, é injetado pelo antivírus/extensão do navegador local usado na auditoria; ignorar na reprodução.

### Imagens
Ver seção 16 (lista completa por categoria).

---

## Arquivo de referência visual

- `docs/reference/screenshots/home-desktop-1440-full.png` — captura de página inteira em 1440px de largura, usada como apoio visual desta auditoria.
