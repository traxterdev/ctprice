# Auditoria — Home (Mobile) — ctprice.com.br/wp

## Metodologia

- **URL analisada:** https://ctprice.com.br/wp/
- **Viewport principal:** 390×844, `devicePixelRatio: 1`
- **Ferramenta:** Chrome DevTools MCP, com emulação de dispositivo via CDP (`Emulation.setDeviceMetricsOverride`: `isMobile: true`, `hasTouch: true`) + User-Agent móvel (iPhone/Safari), pois o simples redimensionamento da janela do navegador não desceu abaixo de ~500px neste ambiente.
- **Data da auditoria:** 2026-08-17
- **Escopo:** somente página Home, somente comportamento mobile (390×844). Não inclui tablet.
- Todos os valores foram **medidos** (`getComputedStyle`, `getBoundingClientRect`, `data-settings`, CSS carregado via `fetch`), não estimados.

Este relatório **complementa** `docs/reference/home-desktop-audit.md` — não repete valores que não mudam entre desktop e mobile, e foca nas diferenças, quebras de layout e comportamento específico do mobile. Captura de referência: `docs/reference/screenshots/home-mobile-390-full.png`.

A estrutura de 13 containers de nível superior (topbar → footer bottom bar) é **idêntica em ordem** à do desktop — nenhuma seção foi reordenada, ocultada ou duplicada para mobile. A altura total do documento passa de 6.603,6px (desktop) para **12.042,8px** (mobile), pelo empilhamento vertical do conteúdo.

---

## Breakpoint

### Breakpoint principal: `max-width: 767px`

Confirmado via inspeção do CSS gerado para a página (`wp-content/uploads/elementor/css/post-360.css`), não apenas visualmente. Exemplo concreto (grid de 4 colunas da seção "Bem-vindo à CT Price", elemento `elementor-element-ddc17f9`):

```css
/* base (desktop) */
.elementor-element-ddc17f9{ --e-con-grid-template-columns: repeat(4, 1fr); }

/* @media(max-width:1024px) — nenhuma alteração de colunas, só --grid-auto-flow */

/* @media(max-width:767px) — É AQUI que a grade vira 1 coluna */
.elementor-element-ddc17f9{ --e-con-grid-template-columns: repeat(1, 1fr); }
```
O mesmo padrão se repete para o grid de 3 colunas de "Nossos Serviços" (`elementor-element-990c2df`) e para o grid do blog. Também é neste breakpoint que os containers "boxed" trocam `max-width: 1200px` por `max-width: min(100%, 767px)`.

**É o breakpoint de 767px que causa a reestruturação principal do conteúdo (grid → coluna única, mudança de padding, empilhamento de colunas em flex).**

### Breakpoint intermediário: `max-width: 1024px` (menu do header)

O widget de menu do header usa a classe `elementor-nav-menu--dropdown-tablet` (configuração "Dropdown Breakpoint: Tablet" do Elementor Pro). A regra vem do CSS genérico do plugin (`wp-content/plugins/elementor-pro/assets/css/widget-nav-menu.min.css`):

```css
@media (max-width:1024px){
  .elementor-nav-menu--dropdown-tablet .elementor-nav-menu--main{ display:none }
}
@media (min-width:1025px){
  .elementor-nav-menu--dropdown-tablet .elementor-menu-toggle,
  .elementor-nav-menu--dropdown-tablet .elementor-nav-menu--dropdown{ display:none }
}
```

Ou seja: **o menu horizontal vira hambúrguer já em ≤1024px**, bem antes do resto do layout colapsar em ≤767px. Isso foi confirmado empiricamente testando o viewport em **900×844** (faixa "tablet"): o botão hambúrguer já aparece (`display:flex`) e o menu horizontal já está oculto (`display:none`), **mas o grid de 4 colunas da seção "Bem-vindo" continua com 4 colunas** (`grid-template-columns: 196.25px × 4`) — o conteúdo ainda está no layout "desktop". Isso significa que, entre 768px e 1024px, o site exibe uma combinação incomum: **cabeçalho com hambúrguer + corpo da página ainda em layout multi-coluna de desktop**. Registrado aqui como breakpoint intermediário relevante; não foi feita auditoria completa de tablet.

### Outros breakpoints presentes no CSS carregado (apenas registrados, não investigados em profundidade)
`max-width:479px`, `max-width:480px`, `max-width:575px/576px` (utilitários genéricos do Elementor/tema, não ligados a nenhuma mudança visível identificada nesta página), `max-width:900px`, `max-width:991px/992px`, `min-width:1200px` (larguras "widescreen" genéricas do framework).

---

## Header

### Topbar (seção 0)
- Altura: **176px** (vs 66px no desktop) — cresce porque a lista de ícones (endereço, telefone, WhatsApp, e-mail) quebra em várias linhas.
- `flex-wrap: wrap`, `justify-content: center` na lista de ícones — todos os itens centralizados, empilhados/quebrados em ~3 linhas dentro da faixa com gradiente.
- Mesmo gradiente (`#00222C → #057038`), mesma tipografia (Roboto Flex 15px/500, `#FEFEFE`), mesmos ícones SVG `fill:#10E36B`.
- 3 bandeiras de idioma mantidas, mesma posição relativa (linha própria, centralizada).

### Header/nav (seção 1)
- Altura: **269,6px** (vs 132px no desktop).
- `flex-direction: column` (era `row`) — logo, botão hambúrguer, botão "Área Restrita" empilhados e **centralizados horizontalmente**.
- **Logo:** renderizado a 350×101,6px (largura do container menos 2×20px de padding). O `<img>` usa `srcset`/`sizes` (`sizes="(max-width: 800px) 100vw, 800px"`) e nesta largura o navegador escolhe o candidato **`LogoPreferencialColorida-768x223.png`** (arquivo real, verificado por carregamento direto: 768×223px) em vez do `-1024x297.png` servido no desktop — **é um arquivo de imagem diferente, não apenas CSS reescalando o mesmo arquivo**.
- **Botão hambúrguer** (`.elementor-menu-toggle`): 33×33px, ícone `e-eicon-menu-bar` (SVG), `font-size: 22px`, cor **`#00222C`** fechado → cor **`#10E36B`** quando aberto/expandido (o ícone também troca visualmente para um "X", classe `elementor-menu-toggle__icon--open`). Nome acessível: "Alternar menu" (`aria-expanded`).
- **Botão "Área Restrita"**: 141,4×45px, centralizado, mesmo estilo visual do desktop (pill outline `#057038`, radius 40px).
- Menu horizontal (`.elementor-nav-menu--main`) fica `display:none`; o menu mobile é um elemento **irmão** já presente no DOM (`nav.elementor-nav-menu--dropdown`), não gerado dinamicamente ao clicar.

### Comportamento de abertura do menu
- Clique no hambúrguer alterna a visibilidade do `nav.elementor-nav-menu--dropdown`.
- **Não é overlay nem tela cheia**: o `position` do painel é `static`, ou seja, ele **empurra o conteúdo abaixo** (o botão "Área Restrita" é literalmente deslocado para baixo do menu aberto) — comportamento "accordion in-flow", não um drawer/modal.
- Painel aberto: `background: #FFFFFF`, sem sombra (`box-shadow: none`), 350px de largura, ocupando a mesma coluna do conteúdo.
- 8 itens de menu (mesmos do desktop: Início, A CT Price, Clientes e Parceiros, Fale Conosco, Informações, Trabalhe Conosco, Ouvidoria, Depoimentos).
- Item de menu: Roboto **13px / peso 500** (desktop: 16px/600), cor `#00222C`, `padding: 10px 20px`.

### Submenus (mobile)
- "Clientes e Parceiros" e "Trabalhe Conosco" têm submenu (setinha `e-fas-caret-down`, 13×13px, cor `#00222C`).
- Ao tocar no item pai, o submenu expande **inline** (não é um segundo nível de overlay): o item pai ganha `background-color: #00222C` e `color: #10E36B` (mesmo par de cores do hover de dropdown do desktop, aqui usado como estado "expandido").
- Subitens: fonte **11,05px** (valor não-redondo — indício de tipografia fluida/`clamp()` herdada do global typography do Elementor), `padding-left: 20px`, `border-left: 8px solid transparent`.

### Elementos escondidos no mobile / exclusivos do mobile
- Nenhum elemento foi encontrado com classes de visibilidade explícitas do Elementor (`elementor-hidden-*`) — ou seja, **não há conteúdo exclusivo de desktop que suma no mobile por regra de visibilidade**; a única troca é a alternância nativa do próprio widget de menu entre `.elementor-nav-menu--main` (horizontal) e `.elementor-nav-menu--dropdown` (hambúrguer), ambos sempre presentes no DOM.
- Não existe nenhum elemento ou bloco exclusivo do mobile (sem equivalente no desktop) na Home.

---

## Hero (slider)

- **Altura: 660px — idêntica ao desktop.** O hero **não** reduz de altura no mobile.
- Fundo: mesmas 4 imagens (`caroussel01.jpg`, `csinicial02.jpg`, `caroussel02.jpg`, `caroussel03a.jpg`), `background-size: cover`, `background-position: 50% 50%` (centralizado) — sem `srcset` (é `background-image`, arquivo único, mesmo do desktop).
- **Comportamento estranho identificado:** o wrapper de conteúdo do slide mantém o **mesmo padding do desktop, `padding: 0 0 0 150px`**, e o texto mantém **`font-size: 30px` (Poppins, `line-height: 30px`)** — nenhum dos dois é reduzido para a tela de 390px. O resultado é uma coluna de texto de apenas **~132–240px de largura útil**, forçando quebras de linha extremas (palavra a palavra) e empurrando o texto quase até a base da seção de 660px. Ver screenshot da seção (capturado durante a auditoria) — texto "Trabalhamos integrados aos colaboradores..." quebra em 9 linhas curtas dentro do hero. **Este é um bug de responsividade do site original**, não uma escolha de design — documentado, sem correção nesta etapa.
- `textAlign: start`, cor do texto `#00222C`, trechos em negrito `#2A3855` inline (mesmos do desktop).
- **Sem setas nem bullets** no mobile (idem desktop — `navigation: "none"` no `data-settings`, atributo estático, não responsivo).
- **Autoplay/transição idênticos ao desktop**: `autoplay_speed: 4000`, `transition_speed: 500`, `infinite: yes`. O widget "Slides" do Elementor Pro não expõe breakpoints responsivos para esses valores — o mesmo objeto `data-settings` é usado em qualquer largura.
- Touch: `touch-action: auto` no container do Swiper — arrasto/swipe funciona nativamente (delegado ao JS do Swiper, sem bloqueio de scroll vertical percebido).

---

## Seções (3–12) — mudanças de layout desktop → mobile

| # | Seção | Grid/colunas desktop → mobile | Largura da coluna | Diferença notável |
|---|---|---|---|---|
| 3 | Bem-vindo à CT Price | grid 4 col (280px) → **1 col (350px)** | 350px | H2 mantém **50px** (não reduz), quebra em 2 linhas, `text-align:start`; lead vira `text-align:center`; ícone circular 106×106 inalterado |
| 4 | Ética/vídeo institucional | 1 col (já era boxed único) | 350px | H2 mantém **35px**; overlay do vídeo **escala corretamente** (350×196,875, mesma proporção 16:9) — ao contrário do hero, este widget de vídeo reflui de forma proporcional |
| 5 | Nossos Serviços | grid 3×2 (393px) → **1 col (346px)**, 6 linhas | 346px | moldura externa (`border 2px solid #10E36B`, `radius 20px`) mantida; título do card mantém **28px**; botão "Fale Conosco" mesmo tamanho |
| 6 | Depoimentos | 1 slide visível (igual desktop) | ~352px (slide) | texto do depoimento mantém **20,8px/31,2px**; setas prev/next **visíveis e com `display:flex`** (no desktop existem no DOM mas são discretas); bullets mantidos |
| 7 | Carrossel de clientes/parceiros | **10 logos visíveis por vez → efetivamente 1 por vez** | ~350×208px por logo | ver seção "Carrosséis" abaixo — mudança mais impactante da auditoria mobile |
| 8 | Por que nos escolher | 2 colunas (imagem 600×450 + lista) → **empilhado** | 390px / 370px | **bug**: o bloco da foto colapsa para **390×20px** (praticamente invisível, uma tira fina) em vez de empilhar como imagem de altura proporcional acima do texto; título de item mantém **25px** |
| 9 | Últimas notícias | grid 3 col (353px) → **1 col (350px)**, 3 linhas | 350px | thumb ~354×183px; título do post mantém **21px**; badge mantém **12px** |
| 10 | Formulário de contato | 2 colunas (342px/570px) → **empilhado** (caixa WhatsApp em cima, formulário embaixo) | 390px cada | inputs com `width: 320px` (mais estreito que o container de 350–390px, por padding interno); botão Enviar mesmo tamanho |
| 11 | Footer principal | 3 colunas lado a lado → **4 blocos empilhados** (logo / endereço / links / mapa) | 370px | lista de links mantém um **divisor vertical à esquerda** (`border-left`) mesmo empilhada — herdado do layout de coluna original, visualmente redundante em coluna única |
| 12 | Footer bottom bar | 1 linha (78,4px) → **múltiplas linhas (160,8px)** | 390px | mesmo texto, `flex-direction: column`, cresce por quebra de linha |

Nenhuma seção teve elemento removido/ocultado no mobile — todas mantêm o mesmo conteúdo textual e de imagem do desktop, apenas reempilhadas.

---

## Imagens

| Imagem | Comportamento no mobile |
|---|---|
| Logo do header | **Troca de arquivo real** via `srcset`/`sizes`: usa `LogoPreferencialColorida-768x223.png` (confirmado 768×223px reais) em vez do `-1024x297.png` do desktop. Renderizado a 350×101,6px. |
| Logo do footer | Mesmo comportamento de `srcset` do header (mesma tag `<img>` reaproveitada), renderizado a 87,5×25,4px. |
| Thumbnails do blog | **Mesmo arquivo** em ambos os breakpoints: `srcset` oferece 300w/768w/870w, mas `sizes="(max-width: 300px) 100vw, 300px"` fixa a escolha em `blog0X-300x155.webp` independente da largura da viewport (300px é sempre ≥ necessário em DPR 1). Renderizado maior em CSS (~354×183px) que o arquivo fonte (300×155) — leve upscale. |
| Slides do hero | Sem `srcset` — `background-image` único (mesmo arquivo do desktop), `background-size:cover` ajusta o corte. |
| Overlay do vídeo institucional | Sem `srcset` — mesmo arquivo, escala proporcionalmente (350×196,875). |
| Imagem de "Por que nos escolher" | Sem `srcset` — mesmo arquivo, mas o container **colapsa para 20px de altura** (ver bug na seção 8) em vez de escalar. |
| Avatares de depoimento | Sem `srcset` — mesmo arquivo, renderizado no **mesmo tamanho fixo** (65×65px, `radius:10px`) em ambos os breakpoints. |
| Logos do carrossel de clientes | Sem `srcset` — mesmos arquivos, mas renderizados **muito maiores** no mobile (~350×208px vs ~100×65,5px no desktop) com `object-fit: fill` mantido — a distorção do "fill" fica bem mais perceptível nesse tamanho. |
| Bandeiras de idioma (GTranslate) | Mesmo arquivo 24×24px em ambos. |

Nenhuma imagem foi encontrada oculta (`display:none`/`visibility:hidden`) exclusivamente no mobile.

---

## Carrosséis (os 3 Swipers)

| | Hero (Slides) | Depoimentos (Testimonial Carousel) | Clientes/Parceiros (Image Carousel) |
|---|---|---|---|
| `slidesPerView` (visual, mobile) | 1 (sempre foi 1 — full-bleed) | 1 (igual desktop) | **~1** (vs 10 no desktop) |
| `spaceBetween` | n/a (fundo único) | `10px` (mesmo valor do `data-settings`, idêntico ao desktop) | configurado `image_spacing_custom: 20px`, mas irrelevante com 1 item visível |
| Autoplay | `4000ms`, mesmo valor do desktop | `5000ms`, mesmo valor do desktop | `5000ms`, mesmo valor do desktop |
| Navegação (setas) | nenhuma (`navigation:"none"`), igual desktop | presentes e visíveis (`display:flex`), igual desktop | nenhuma (`navigation:"none"`), igual desktop |
| Paginação (bullets) | nenhuma | 4 bullets, mesmo estilo (6px, inativo `#EFCB39`, ativo `#2A3855`) | nenhuma |
| Touch/swipe | funciona nativamente (`touch-action:auto`) | idem | idem |
| Breakpoints configurados no widget | nenhum encontrado no `data-settings` (objeto estático, mesmos valores em qualquer largura) | idem | o widget **Image Carousel** do Elementor expõe controles `slides_to_show_tablet`/`slides_to_show_mobile`, mas **não aparecem no `data-settings`** — ou seja, foram deixados no padrão do widget, e o padrão claramente não é "10" no mobile (renderiza como ~1 por vez) |
| Total de itens reais | 4 slides | 4 depoimentos | **85 logos reais** (confirmado via `aria-label` de acessibilidade, ex. "45 / 85") |
| Diferença-chave vs desktop | nenhuma funcional — só o bug de texto/padding já descrito | nenhuma funcional relevante | **maior diferença dos 3 carrosséis**: de 10 logos pequenos por vez (desktop) para 1 logo grande por vez (mobile), cada um esticado (`object-fit:fill`) para ~350×208px |

---

## Tipografia — mobile vs desktop

Não existe nenhum `<h1>` na página (nem desktop, nem mobile) — os headings usados são todos `<h2>`/`<h3>`.

| Elemento | Desktop | Mobile | Mudou? |
|---|---|---|---|
| H2 "Bem-vindo à CT Price" | Roboto 50px/900, `line-height:50px` | **idêntico** (50px/900/50px) | Não |
| H2 "Ética, agilidade..." | Roboto 35px/900 | **idêntico** | Não |
| Eyebrow "NOSSOS SERVIÇOS" | Roboto 20px/700 | **idêntico** | Não |
| H2 "Deixe a contabilidade..." | Roboto 32px/700 | **idêntico** | Não |
| Título card de serviço | Poppins 28px/600 | **idêntico** | Não |
| H2 "O que dizem nossos clientes" | Roboto 35px/600 | **idêntico** | Não |
| Texto do depoimento | Roboto 20,8px itálico, `line-height:31,2px` | **idêntico** | Não |
| H2 "Por que nos escolher?" / título de item | Roboto 35px / 25px | **idêntico** | Não |
| Título do post (blog) | Roboto 21px/600 | **idêntico** | Não |
| Hero — descrição do slide | Poppins 30px/400 | **idêntico** (30px — causa o bug de wrap, ver seção Hero) | Não |
| **Menu do header** | Roboto **16px / peso 600** | Roboto **13px / peso 500** | **Sim** |
| **Submenu do header** | herda do dropdown desktop (14px, ver auditoria desktop) | **11,05px** | **Sim** |
| Botões (Fale Conosco/Enviar/Área Restrita) | Roboto 15px/500 | **idêntico** | Não |
| Rodapé (texto/links) | Roboto, tamanhos herdados do reset | **idêntico** | Não |

**Achado principal:** a responsividade tipográfica do site é praticamente inexistente — a adaptação para mobile depende quase inteiramente do **reflow de layout** (grid → coluna), não da redução de `font-size`. As únicas duas exceções medidas (menu e submenu do header) usam tipografia "fluida" (valores não-redondos como 11,05px sugerem `clamp()`/`rem` calculado, herdado da tipografia global "accent" do Elementor). O restante do conteúdo (incluindo o H2 de 50px da seção "Bem-vindo") permanece do mesmo tamanho absoluto em 390px que em 1440px, o que degrada a leitura em vários pontos (ex.: H2 quebra em 2 linhas ocupando boa parte da tela).

---

## Footer

- **Empilhamento:** os 3 blocos de conteúdo do desktop (endereço/e-mails/responsável técnico · menu de links · mapa) mais o logo (que no desktop fica centralizado acima das 3 colunas) formam **4 blocos verticais** de 370px de largura cada, em coluna única.
- **Alinhamento:** logo centralizado (87,5×25,4px); demais blocos alinhados à esquerda (`text-align:start`), mesmo padrão de cor do desktop (`#057038`/`#00222C` para labels, texto padrão para valores).
- **Espaçamento:** `padding: 0 10px` no container geral (10px de cada lado, herdado do padrão dos containers boxed), blocos empilhados sem gap adicional explícito além da altura natural de cada bloco.
- **Divisor da lista de links:** mantém um `border-left` decorativo à esquerda da lista, um resquício do layout em coluna do desktop — visualmente pouco funcional numa coluna única de largura total, mas presente e deve ser documentado como está.
- **Mapa:** `<iframe>` do Google Maps redimensiona para 350×200px (era maior no desktop), mesmo link "Abrir no Maps".
- **Footer bottom bar:** o texto de copyright + "Desenvolvido por Agência Lester" que cabia em uma linha no desktop (78,4px de altura) agora quebra em múltiplas linhas (**160,8px** de altura), `flex-direction: column`, mesma cor de fundo (`#00222C`) e mesmos links.
- Nenhum elemento do footer foi ocultado no mobile.

---

## Outros achados / comportamentos estranhos (resumo consolidado)

1. **Hero:** texto do slide não adapta `font-size` (30px) nem `padding-left` (150px fixo) para telas estreitas — quebra em muitas linhas curtas dentro da altura fixa de 660px. Ver seção Hero.
2. **"Por que nos escolher":** o bloco de imagem colapsa para ~20px de altura (praticamente some) em vez de empilhar como uma imagem de altura proporcional — muito provavelmente um erro de configuração responsiva no Elementor original (altura fixa/`vh` não ajustada), não uma decisão de design.
3. **Carrossel de logos de clientes:** passa de 10 logos pequenos por vez (desktop) para ~1 logo grande por vez no mobile, ampliando bastante a distorção do `object-fit:fill` em logos com proporções diferentes de ~350:208.
4. **Header vs. conteúdo dessincronizados entre 768–1024px:** o menu já é hambúrguer nessa faixa, mas o corpo da página ainda usa grids multi-coluna de desktop — um estado híbrido "tablet" não intencionalmente desenhado, e sim resultado de dois breakpoints diferentes (1024px no menu, 767px no conteúdo) configurados independentemente.
5. **Tipografia quase toda fixa:** praticamente nenhum heading/parágrafo reduz de tamanho no mobile (ver seção Tipografia) — o único mecanismo de adaptação é o reflow do grid.

Estes pontos são registrados como estão no site de referência, sem qualquer correção — cabe a decisão futura de reproduzi-los fielmente ou não.

---

## Arquivos de referência visual

- `docs/reference/screenshots/home-mobile-390-full.png` — captura de página inteira em 390px de largura.
