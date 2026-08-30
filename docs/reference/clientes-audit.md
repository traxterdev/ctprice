# Auditoria — `/clientes/` ("Nossos Clientes")

Data: 2026-08-30
Referência: https://ctprice.com.br/wp/clientes/ (Elementor `id=632`, 6 seções de nível superior)
Etapa: **somente engenharia reversa e documentação** — nenhuma implementação, nenhum arquivo do
projeto (`clientes/index.php`, componentes, CSS, JS, includes, config, assets) foi alterado.

Componentes globais (topbar, header, menu, footer, bottom bar, WhatsApp flutuante, cookie
banner, fontes) **não foram reauditados** — já aprovados em tarefas anteriores. Apenas
reconfirmados como presentes e na mesma posição relativa.

---

## 1. Estrutura completa da página

6 seções de nível superior no DOM (`.elementor > div`), na ordem:

| # | `data-id` | Função | Y (1440px) | Altura (1440px) |
|---|---|---|---|---|
| 1 | `41e1562a` | Topbar (global) | 0 | 66 |
| 2 | `43472da6` | Header (global) | 66 | 132 |
| 3 | `208472a4` | **Hero interno** ("nossos clientes") | 198 | 400 |
| 4 | `3294fb9` | **Grade de logos de clientes** (widget `gallery.default`, único filho) | 648 | 7627,953125 |
| 5 | `2410a901` | Footer (global) | 8325,953125 | 400 |
| 6 | `7b799ff2` | Bottom bar (global) | 8725,953125 | 78,390625 |

**Apenas duas seções exclusivas desta página: Hero e Grade de logos.** Não há heading/eyebrow
extra, CTA, ou qualquer outra seção entre o Hero e o Footer — confirma diretamente o resumo do
`site-inventory.md` ("Hero interno + grade de logos"), sem `REFERENCE DRIFT`.

Margens: `50px 0` acima E abaixo da seção da grade (gap hero→grade = 648-598=50; gap
grade→footer = 8325,953125-8275,953125=50) — mesmo padrão de margem vertical já usado em
Missão/Visão/Valores (`sobre-nos`).

Altura total da página (1440×900): **8804,34375px**. `scrollWidth === clientWidth` = 1425/1425 —
sem overflow horizontal na referência (diferente do Hero de `/sobre-nos/`, que tem overflow
conhecido).

---

## 2. Hero interno — classificação: **C (estrutura diferente — não reutilizar diretamente)**

| Propriedade | `internal-hero.php` (já implementado) | Hero de `/clientes/` |
|---|---|---|
| Altura | 640px | **400px** |
| Estrutura de conteúdo | `<p>` eyebrow + `<h1>` título com `<strong>` destacado | **dois `<h2>`** empilhados (sem `<strong>`/destaque) |
| Largura/posição do conteúdo | `width:calc(50% - 100px); margin-left:200px` (fórmula assimétrica própria) | **container 1140px centralizado padrão** (`max-width:min(100%,1140px)`, mesmo padrão de História/MVV/Dedicação) |
| Eyebrow | Poppins, uppercase, pequeno | `<h2>` "nossos clientes" — Roboto 700 20px/20px, uppercase, `letter-spacing:-0.9px`, cor `#00222C` |
| Título | Roboto, grande, com trecho em `<strong>` | `<h2>` "Conheça algumas empresas que confiam na CT Price" — Roboto 700 30px/30px, cor `#057038` (verde), sem destaque parcial |
| Background | `url(...)`, `cover`, `50% 50%`, sem gradiente | idêntico mecanismo: `url(clientes.jpg)`, `cover`, `50% 50%`, `no-repeat`, sem gradiente |
| Padding da seção | — | `0px 10px` |
| Responsivo (900/390) | tem defeito conhecido (sem breakpoint, já corrigido na implementação) | **também sem nenhum breakpoint** — altura (400px) e fontes (20px/30px) idênticas em 1440/900/390; **sem overflow** em 390px (390=390), diferente do defeito do Hero de Sobre Nós |
| Alinhamento do texto | left (`start`) | left (`start`) — único ponto igual além do mecanismo de background |

**Conclusão**: mecanismo de background é idêntico (`internal-hero.php` já cobre isso), mas a
estrutura de conteúdo (dois `<h2>` sem destaque parcial, container 1140 centralizado padrão em
vez da fórmula assimétrica `calc(50% - 100px)/margin-left:200px`, altura 400 vs 640) é
suficientemente diferente para não ser um caso de reuso direto (categoria A) nem de modificador
simples (categoria B) sem antes decidir explicitamente o que fazer com o padrão de "duas linhas
de heading" — registrado como **C**, decisão de implementação para a próxima etapa.

---

## 3. Conteúdo de clientes — grade estática (não é carrossel)

**Confirmado explicitamente: grid ESTÁTICO**, não Swiper/carrossel. É o widget nativo do
Elementor **"Gallery"** (`elementor-widget-gallery`, `data-widget_type="gallery.default"`),
renderizado como **galeria justificada** (`e-gallery-justified` — algoritmo de "justified
gallery", como um mosaico de fotos: cada linha é preenchida ajustando a largura de cada item à
sua própria proporção, não uma grade `NxM` regular). Cada item abre um **lightbox** nativo do
Elementor ao clicar (testado: clique abre `.elementor-lightbox` com slideshow entre as 106
imagens).

### 3.1 Quantidade e integridade

- **106 itens** no total (contados via `a.e-gallery-item` — sem clones, pois não há Swiper).
- **Todos os 106 retornam HTTP 200** (testado via `fetch(..., {method:'HEAD'})` em cada URL) —
  **nenhum 404** nesta página (diferente do carrossel da Home, que tem 3 arquivos 404 conhecidos).
- **1 imagem degenerada/corrompida** (não é 404, mas é inutilizável): `WhatsApp-Image-2024-11-22-at-12.49.26-1-e1732542895828.jpeg` — o próprio Elementor reporta
  `data-width="3" data-height="3"` (miniatura de 3×3 pixels), claramente um upload corrompido ou
  um thumbnail do WordPress gerado de um arquivo inválido. Existe uma segunda versão do mesmo
  envio, íntegra, em outro caminho (`.../2025/02/WhatsApp-Image-2024-11-22-at-12.49.26.jpg`,
  1461×352) — ou seja, o cliente enviou a mesma foto duas vezes e uma delas corrompeu.
- **1 duplicidade de conteúdo real**: "Logo Morangos do Vale" aparece duas vezes, com uploads em
  pastas diferentes (`2026/07/Logo-Morangos-do-Vale.png`, 797×426, e
  `2026/08/Logo-Morangos-do-Vale.png`, mesmo nome e dimensões) — parece envio duplicado
  acidental do mesmo logo.

### 3.2 Comparação com o carrossel global (82 logos, `config/clients.php`)

| | Quantidade |
|---|---|
| Total de itens na grade de Clientes | 106 |
| Compartilhados com os 82 logos do carrossel global (mesmo nome de arquivo) | **34** |
| Exclusivos desta página (não estão em `config/clients.php`) | **72** |

Os 34 compartilhados usam os MESMOS arquivos já existentes em
`assets/images/clients/home-carousel/` (ex.: `lopes.jpg`, `capital.jpg`, `sermix.jpg`,
`health-brasil_d0fe5f29.jpeg`, `vitrine.jpg`, `techagro.jpg`, etc. — lista completa
reproduzível a partir do cruzamento de nomes de arquivo). Os 72 exclusivos são, em sua maioria,
uploads muito mais recentes (pastas `2025/`, `2026/`, contra `2024/09/` dos 82 do carrossel),
com nomes de arquivo capitalizados/descritivos (`PavTubo.png`, `Logo-Posto-Figueira.png`,
`RODAN.png`, `Logo-Caiman.png`, `Que-Pao.jpg` — confirma exatamente os exemplos já citados no
`site-inventory.md`: Caiman Transportes, +Q Pão).

**Conclusão direta para a pergunta de arquitetura**: `config/clients.php`, no formato atual (82
registros fixos), **não cobre** a lista de Clientes. Ver seção 11.

### 3.3 Amostra dos itens exclusivos (não exaustiva — lista completa nos dados brutos desta auditoria)

`PavTubo.png`, `logo-cm.jpeg` ("logo c&m"), `LOGO-TECA-1.png`, `Logo-Posto-Figueira.png`,
`KFC.jpg`, `Logo-Vol-Imports.png`, `Terras-Alpha-1.jpg`, `Central-das-Aliancas.png`, `RODAN.png`,
`site_Fevara-1.png`, `Logo-Zelare.png`, `viva-haus-logo-nova22-2-scaled.webp`,
`Bocalan-Logo-scaled.png`, `Kartol-scaled.png`, `Sushi-1.png`, `FIGUEIRA.png`,
`Logo_Estofados-Cpo-Gde.png`, `Rede-Real-Popular-e1738931560334.png`, `Alianca.png`,
`Liquida.png`, `logo-Servicos-03-2-1.png`, `TCM.png`, `Padilha.jpg`, `Rosted-Potato-1.jpg`,
`Logo-Morangos-do-Vale.png` (×2), `Logo-Caiman.png`, `Pizza-Hut.jpg`, `site_Move-1.png`,
`Logo-MS-Ambiental.png`, `Frutelli.png`, `Ponto-Dexter.jpg`, `Cear-scaled.png`, `Emporio.png`,
`logo-RBL-1-1.jpg`, `Compre-da-Vovo-1.png`, `LOGO-REI-DO-TRIGO.png`, `LOGO-ENGETEC...png`,
`Campo-Doce.png`, `Guaranta.jpg`, `Arapongas-e1738931624706.png`, `Studio-VIP.png`,
`LOGO-SO-SAL.png`, `logo-amigao-jpg.jpg`, `LOGO-TRANSLIMA-PNG-SEM-FUNDO.png`, `Zornimat-1.jpg`,
`Endosurgical.png`, `Vo-1.png` ("_Vó"), `site_Velutex-1.png`, `image-24.png`,
`Coto-Figueira-scaled.png`, `BS.jpg`, `Logo-Meneguzzo.png`, `Que-Pao.jpg` ("+ Que Pão"),
`Cheiro-de-Bolo-Logo.jpeg`, `Agrotouro.png`, `DIB.png`, `image-43.png`, além de 6 fotos avulsas
enviadas via WhatsApp/ChatGPT (`WhatsApp-Image-...`, `ChatGPT-Image-...`) sem tratamento visual
de logo (fotos de produto/ambiente, não identidade visual).

---

## 4. Estrutura visual da grade de logos

| Propriedade | Valor medido |
|---|---|
| Container | 1140px (mesmo valor próprio já usado em História/MVV/Dedicação), `padding:0 10px` na seção, `10px 0` no inner |
| Algoritmo de layout | **Justified gallery** (não CSS Grid, não Flexbox uniforme) — cada linha é preenchida ajustando altura/largura de cada item à razão de aspecto real da imagem, via variáveis CSS (`--item-width`, `--item-height`, `--row`) calculadas inline pelo Elementor |
| Gap (`--hgap`/`--vgap`) | **20px** em ≥1024px; **10px** em <1024px (breakpoint real confirmado em 1024/1023 — mesmo breakpoint já usado no projeto) |
| Largura dos itens | variável (não fixa) — depende da proporção de cada imagem e da altura-alvo da linha; ex. em 1440px, item 1 mede 345,9×194,6px |
| Altura dos itens | variável — mesma lógica (ex. 194,6px no exemplo acima; outros itens da mesma linha têm a mesma altura mas larguras diferentes) |
| `object-fit` equivalente | `background-image` com `background-size:cover; background-position:50% 50%; background-repeat:repeat` (repeat é resíduo do Elementor, sem efeito visível pois a imagem cobre 100% da área) — **logos são CORTADOS (cover), não enquadrados (contain)** — diferente da correção já aplicada no carrossel da Home/Sobre Nós |
| Border / radius | nenhum (`border:0`, `border-radius:0`) |
| Background do item | transparente |
| Alinhamento | preenchimento total da linha (justified) |
| Hover | overlay escuro `rgba(0,0,0,0.5)` com fade (via `transition-duration` da variável `--overlay-transition-duration`) — **sem zoom/scale** |
| Link/clique | cada item abre lightbox nativo do Elementor (slideshow entre as 106 imagens em tamanho original) — testado e confirmado funcional |
| Cursor | `pointer` |

### Comportamento por viewport

| Viewport | Gap | Altura do container da grade | Overflow |
|---|---|---|---|
| 1440px | 20px | 7607,953125px | Não |
| 1024px | 20px | — | — |
| 1023px | 10px (breakpoint real) | — | — |
| 900px | 10px | 5028,671875px | Não (885=885) |
| 768px | 10px | — | — |
| 390px | 10px | 13532,453125px | Não (390=390) |

Em 390px, o layout justificado tende a colocar a maioria dos itens em largura cheia (370px, uma
coluna), mas ocasionalmente empareia dois itens de proporção quadrada lado a lado (ex.: dois
itens de ~170px cada) — comportamento inerente ao algoritmo de justificação, não uma regra de
coluna fixa.

**Breakpoint real confirmado**: **1024px** (idêntico ao já usado no projeto para
header/menu/blog/carrossel — não presumido, testado diretamente em 1023/1024). Não há segundo
breakpoint em 768px para este widget especificamente (gap permanece 10px de 1023px até 390px).

---

## 5. Outras seções

Não existem outras seções além do Hero e da grade de logos (confirmado na seção 1) — a página é
exatamente "Hero + grade de clientes", sem CTA, sem texto introdutório adicional, sem cards.

---

## 6. Tipografia exclusiva da página

| Elemento | `font-family` | `font-size` | `font-weight` | `line-height` | `color` | `text-align` | outros |
|---|---|---|---|---|---|---|---|
| Eyebrow do Hero ("nossos clientes") | Roboto | 20px | 700 | 20px | `#00222C` | start | uppercase, `letter-spacing:-0.9px` |
| Título do Hero | Roboto | 30px | 700 | 30px | `#057038` | start | — |

Nenhuma tipografia própria na grade de logos (são apenas imagens).

---

## 7. Assets exclusivos

**Não baixados nesta etapa** (proibido pelo escopo). Já existentes no projeto (34, em
`assets/images/clients/home-carousel/`, mesmos nomes — ver seção 3.2 para a lista de
compartilhados). **72 novos, ainda não baixados**, listados (parcialmente) na seção 3.3 — lista
completa disponível nos dados brutos coletados nesta auditoria (URLs, dimensões e formato de
cada um foram capturados via `data-width`/`data-height`/`href` do widget de galeria). Nenhum
asset quebrado/404 (todos os 106 retornam 200); 1 asset íntegro mas com miniatura corrompida
(3×3px, ver seção 3.1) — precisa de decisão do cliente sobre reenvio.

Asset do Hero: `clientes.jpg` (`https://ctprice.com.br/wp/wp-content/uploads/2024/09/clientes.jpg`) — novo, não baixado.

---

## 8. Responsividade

- **Hero**: sem nenhum breakpoint — altura (400px) e tipografia idênticas em 1440/900/390px.
  Diferente do Hero de Sobre Nós, este **não overflow** em 390px.
- **Grade de logos**: breakpoint real em **1024px** (gap 20px→10px). Sem mudança de "colunas"
  no sentido tradicional — o algoritmo de justificação recalcula width/height de cada item
  continuamente conforme a largura disponível, não há passos discretos de N colunas.
- **Sem overflow horizontal** em nenhum dos três viewports obrigatórios (`scrollWidth ===
  clientWidth` confirmado em 1440/900/390).

---

## 9. Animações e interações confirmadas

- **Hover** nos itens da grade: overlay escuro (`rgba(0,0,0,0.5)`) com transição de opacidade —
  sem zoom/scale.
- **Clique** em qualquer item: abre lightbox nativo do Elementor (slideshow), testado e
  funcional.
- **Nenhum fadeIn/fadeInUp/fadeInLeft/fadeInRight** encontrado nesta página (sem
  `data-settings` de animação de entrada nos widgets do Hero ou da galeria).
- **Nenhum carrossel** (confirmado — é grid estático).

---

## 10. Defeitos e inconsistências

| # | Item | Classificação | Observação |
|---|---|---|---|
| 1 | Miniatura corrompida 3×3px (`WhatsApp-Image-2024-11-22-at-12.49.26-1-e1732542895828.jpeg`) | **D — decisão do cliente** | Existe uma versão íntegra do mesmo envio noutro caminho; decidir se remove a corrompida ou pede reenvio |
| 2 | Logo "Morangos do Vale" duplicado (2 uploads idênticos) | **D — decisão do cliente** | Possível envio duplicado; decidir se mantém 1 ou 2 ocorrências |
| 3 | Logos cortados por `background-size:cover` em vez de enquadrados | **C — defeito conhecido a corrigir** (mesmo padrão já corrigido no carrossel: usar `contain`) — mas aqui os itens têm proporções variáveis por design (justified gallery), então a correção não é tão direta quanto no carrossel; decisão de design a tomar na implementação |
| 4 | WhatsApp da topbar/flutuante desta página (`5567992324097`) diferente do canônico já unificado (`5567992616117`) | **B — comportamento já tratado** (mesmo padrão de divergência por página já documentado em `global-data-conflicts.md`; será unificado na implementação, como já feito em Sobre Nós/Home) |
| 5 | Endereço da topbar/footer desta página inclui "Monte Castelo" e CEP completos | **B — dado de referência para completar a pendência já registrada** ("bairro/CEP ainda pendentes" em Sobre Nós) — não é defeito desta página, é a fonte do dado que falta nas outras |
| 6 | Nenhum link/CTA quebrado encontrado nesta página | — | — |
| 7 | Nenhum problema de acessibilidade grave adicional identificado além dos já conhecidos globalmente (contraste, labels) — itens da galeria não têm texto alternativo visível além do `title` usado no lightbox | **D** | Decidir se `alt` deve ser preenchido com o nome do cliente na implementação |

---

## 11. Reutilização arquitetural

- **Reutilizáveis sem alteração**: nenhum dos componentes existentes serve diretamente para a
  grade de logos (todos pressupõem Swiper/carrossel ou layout de coluna fixa — a galeria
  justificada é um mecanismo de layout diferente de todos os 5 componentes já existentes).
- **`internal-hero.php`**: reutilizável apenas no MECANISMO de background (`url()`, `cover`,
  `50% 50%`); a estrutura de conteúdo (dois headings simples, container 1140 centralizado, sem
  destaque parcial em `<strong>`) exigiria um modificador — mas dado que o padrão de "duas
  headings empilhadas sem highlight" é distinto do padrão "eyebrow `<p>` + `<h1>` com `<strong>`"
  já consolidado, a recomendação é avaliar na próxima etapa se vale um modificador simples
  (categoria B) ou se é mais claro um variante/heading-mode próprio — **não decidido aqui**.
- **Estruturas que exigem componente novo**: a grade de logos (galeria justificada com lightbox)
  não tem equivalente entre os componentes existentes — nem `image-text-section.php`
  (2 colunas fixas), nem `flat-icon-box-section.php` (grid uniforme 3 colunas), nem
  `image-content-cta-section.php` (2 colunas + CTA), nem `clients-carousel-section.php`
  (Swiper com `slidesPerView` fixo) reproduzem um layout justificado de proporções variáveis.
  Um componente novo (`components/clients-grid-section.php` ou nome equivalente, a decidir na
  implementação) parece necessário — **mas essa decisão de nomear/criar fica para a próxima
  etapa**, conforme instruído.
- **Dados dos clientes — fonte própria necessária**: `config/clients.php` (82 registros, ordem
  fixa do carrossel da Home) **não deve ser reaproveitado automaticamente** para `/clientes/` —
  106 itens, 72 exclusivos, ordem e conteúdo diferentes. Recomenda-se uma segunda fonte de dados
  dedicada (ex. `config/clients-gallery.php` ou nome equivalente) quando a página for
  implementada — **não criada nesta etapa**.

---

## REFERENCE DRIFT

Nenhum. O único registro prévio sobre esta página (`docs/reference/site-inventory.md`, linha
32: "Hero interno (NOSSOS CLIENTES) + grade de logos diferente da que aparece na Home") é
confirmado integralmente por esta auditoria, sem contradição.
