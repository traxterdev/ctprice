# Validação final full-page — `/informacoes/`

Data: 2026-08-30
Referência: `https://ctprice.com.br/wp/informacoes/`
Implementação: `/informacoes/` local (PHP 8+, sem WordPress/Elementor/jQuery/Font Awesome)
Documentação-base: `docs/reference/informacoes-audit.md`, `docs/reference/reference-baseline.md`,
`docs/reference/home-final-validation.md`, `docs/reference/sobre-nos-final-validation.md`

Escopo desta validação: comparação **full-page** da composição de `/informacoes/` (não
implementação de nada novo) nos cinco viewports do projeto (1440×900, 900×1200, 768×1024,
767×1024, 390×844), mais regressão rápida (não full-page) de Home e Sobre Nós, cujas fontes de
dados (`config/blog-posts.php`, `config/dedication-section.php`) passaram a ser compartilhadas
com esta página.

---

## 1. Estrutura confirmada (ordem no DOM, nos 5 viewports)

topbar → header → Hero (`boxed-hero.php`) → "Últimas notícias" (`blog-section.php`) →
"Dedicação" (`image-content-cta-section.php`) → carrossel de clientes
(`clients-carousel-section.php`) → footer → bottom bar → WhatsApp flutuante (fixo) → cookie
banner (fixo). Ordem idêntica à especificada, sem seção ausente, duplicada ou fora de posição em
nenhum dos 5 viewports.

---

## 2. Medições full-page

Tabela de composição acumulada (Y/altura de cada seção, `getBoundingClientRect()` +
`window.scrollY`, medido após o carregamento completo — em 390×844 a página foi rolada
integralmente em incrementos de ~400px antes da medição, para evitar o artefato de
`img loading="lazy"` já documentado em validações anteriores):

| Seção | 1440×900 | 900×1200 | 768×1024 | 767×1024 | 390×844 |
|---|---|---|---|---|---|
| Topbar | 0–66 (66) | 0–110 (110) | 0–110 (110) | 0–110 (110) | 0–132 (132) |
| Header | 66–198 (132) | 110–183 (73) | 110–183 (73) | 110–364,63 (254,63) | 132–377,77 (245,77) |
| Hero | 198–598 (400) | 183–583 (400) | 183–583 (400) | 364,63–764,63 (400) | 377,77–777,77 (400) |
| Últimas notícias | 648–1332,59 (684,59) | 633–1950,48 (1317,48) | 633–1905,36 (1272,36) | 814,63–2822,42 (2007,80) | 827,77–2480,08 (1652,31) |
| Dedicação | 1382,59–1839,44 (456,84) | 2000,48–2430,27 (429,78) | 1955,36–2433,14 (477,78) | 2872,42–3242,20 (369,78) | 2530,08–3264,64 (734,56) |
| Carrossel | 1839,44–2039,44 (200) | 2430,27–2630,27 (200) | 2433,14–2633,14 (200) | 3242,20–3442,20 (200) | 3264,64–3464,64 (200) |
| Footer + bottom bar | 2039,44–2517,83 (478,39) | 2630,27–3108,66 (478,39) | 2633,14–3135,53 (502,39) | 3442,20–4289,83 (847,63) | 3464,64–4308,91 (844,27) |
| **Altura total** | **2517,83** | **3108,66** | **3135,53** | **4289,83** | **4308,91** |

- **`scrollWidth === clientWidth`** confirmado nos 5 viewports (1425/1425, 885/885, 753/753,
  752/752, 375/375) — **nenhum overflow horizontal**.
- **Nenhuma sobreposição**: em todos os viewports, o `top` de cada seção é ≥ o `bottom` da
  seção anterior.
- **Gap de 50px consistente** antes e depois de "Últimas notícias" (Hero→Blog e Blog→Dedicação)
  em todos os 5 viewports: vem de `margin: 50px 0` já definido em `.blog-section`
  (`assets/css/blog-section.css`), medido e aprovado na Home — **não é um gap introduzido por
  esta página**, é o comportamento inerente do componente reutilizado sem alteração. Nenhum
  outro gap inesperado foi encontrado.
- Header/topbar mais altos em 767×1024 (`354,63`/`254,63`) e crescimento de "Últimas notícias"
  em telas estreitas (reflow de texto/menu) são o mesmo comportamento já documentado e aceito em
  Home/Sobre Nós — não específicos desta página.
- Nenhuma tentativa foi feita de igualar a altura total à referência WordPress — conforme
  instruído, as melhorias conscientes (cards de post da Home, carrossel 6/2/1) mudam a altura
  por design.

---

## 3. Hero

| Propriedade | Confirmado |
|---|---|
| Componente | `components/boxed-hero.php`, sem modificação |
| Altura | 400px nos 5 viewports (sem regressão responsiva) |
| Imagem | `assets/images/pages/informacoes/informacoes.jpg` (`background-image` contém `informacoes.jpg`) |
| `background-position` | `0% 0%` |
| `background-size` | `cover` |
| Eyebrow | "mantenha-se bem informado" |
| Título | "Acompanhe nossas novidades" |
| Container (`.boxed-hero__inner`) | `max-width: 1140px` |

Todos os valores conferem exatamente com `informacoes-audit.md`, seção 2. **Resultado: OK, sem
regressão.**

---

## 4. Últimas notícias

- **Exatamente 3 posts**, 3 destinos únicos (`Set` de URLs com tamanho 3 = sem duplicata).
- Mesmos títulos, categorias/badges (`FOLHA DE PAGAMENTO`, `INFORMATIVO`, `INFORMATIVO`),
  thumbnails (`blog03/02/01-300x155.webp`) e datas da Home — comparado item a item via DOM.
- Grid responsivo do componente já aprovado (`blog-section.php`, inalterado): 3 colunas
  (desktop) → 2 (tablet/768–1023) → 1 (≤767px, confirmado exatamente no breakpoint) — sem o
  2+1 desequilibrado do widget original (melhoria já aprovada, não regressão).
- **`config/blog-posts.php` é a única fonte dos dados**: `grep` confirma que nenhuma das duas
  páginas (`index.php`, `informacoes/index.php`) define o array `$blogPosts` inline — ambas
  fazem `require .../config/blog-posts.php` e leem `heading`/`posts` de lá.

**Resultado: OK, sem duplicata, sem item ausente, fonte de dados única confirmada.**

---

## 5. Dedicação

| Item | Confirmado |
|---|---|
| Componente | `components/image-content-cta-section.php`, sem modificação |
| Texto (heading/parágrafos) | Idêntico, caractere a caractere, ao de `/sobre-nos/` (comparado via `innerHTML` dos dois DOMs) |
| Imagem | `assets/images/pages/informacoes/pexels-thepaintedsquare-583847-1024x683.jpg` — própria desta página |
| Distorção | Nenhuma — `naturalWidth/naturalHeight` (1024×683) batem com o arquivo fonte, `object-fit`/proporção preservados |
| CTA | "Fale Conosco" → `/fale-conosco/` (não `https://ctprice.com.br/contato`, 404 conhecido) |
| Responsivo | `row` (2 colunas) acima de 767px, `column` (empilhado) em ≤767px — confirmado exatamente no breakpoint |

`config/dedication-section.php` inspecionado diretamente: contém **somente** `heading_html`,
`content_html`, `cta_label`, `cta_url` — nenhuma chave `image`/`image_alt`. Cada `index.php`
(`sobre-nos/`, `informacoes/`) define sua própria imagem após o `require`, confirmado via DOM
(`/sobre-nos/` → `01-1024x684.jpg`; `/informacoes/` → `pexels-thepaintedsquare-...jpg`).

**Resultado: OK — reuso correto, sem duplicar conteúdo, imagem própria por página preservada.**

---

## 6. Carrossel de clientes

| Item | Confirmado |
|---|---|
| Logos únicos | 82 (`Set` de `src` sem os slides duplicados do `loop`) |
| `slidesPerView` | 6 (desktop), 2 (900×1200 e 768×1024), 1 (767×1024 e 390×844) |
| `spaceBetween` | 20 |
| Autoplay | `params.autoplay` truthy e `autoplay.running === true` |
| Loop | `params.loop === true` |
| Swipe | `allowTouchMove === true` |
| `object-fit` | `contain` |
| Cards premium | `.logo-card` presente em cada slide |
| Ordem | Não alterada (mesmo array de `config/clients.php`, sem `sort`/`shuffle`) |
| Arquivos quebrados reintroduzidos | Nenhum (`mv.jpg`, `modelo.jpg`, `logo_0020_Camada16.jpg` ausentes; `brokenCount: 0`) |

**Resultado: OK, configuração idêntica ao padrão já aprovado da Home/Sobre Nós.**

---

## 7. Configs compartilhados

- **`config/blog-posts.php`**: única fonte dos 3 posts, consumida por `index.php` (Home) e
  `informacoes/index.php`. Confirmado por `grep` que nenhuma página mantém uma segunda cópia do
  array.
- **`config/dedication-section.php`**: única fonte do texto/CTA da Dedicação, consumida por
  `sobre-nos/index.php` e `informacoes/index.php`. Confirmado que o arquivo não contém nenhuma
  imagem — cada página define a sua própria antes de incluir o componente.
- Nenhum dos dois arquivos introduz um sistema de conteúdo genérico ou CMS — são arrays estáticos
  `return [...]`, no mesmo padrão já usado por `config/company.php`/`config/clients.php`.

**Resultado: OK — dados verdadeiramente compartilhados, sem duplicação e sem abstração além do
necessário.**

---

## 8. Regressão rápida — Home

Verificado em 1440×900 (não full-page, apenas o que depende dos dados compartilhados):

- 3 posts intactos, mesmos títulos/categorias/URLs de antes da extração para
  `config/blog-posts.php`.
- Altura da seção "Últimas notícias": **684,59px**, idêntica ao valor já registrado em
  `home-final-validation.md` (684,59px) — nenhuma mudança visual.
- Console: nenhum erro novo (o único item, aviso de acessibilidade de `autocomplete` no
  formulário de contato, já era uma pendência conhecida e não relacionada a esta mudança).

**Resultado: sem regressão.**

---

## 9. Regressão rápida — Sobre Nós

Verificado em 1440×900:

- Seção Dedicação intacta: heading "Dedicação aos resultados e Compromisso com nossos
  clientes.", CTA → `/fale-conosco/`.
- Imagem própria preservada: `assets/images/pages/sobre-nos/01-1024x684.jpg` (não trocada pela
  nova foto de Informações).
- Altura da seção: **457,375px**, idêntica ao valor já registrado em
  `sobre-nos-final-validation.md` (457,375px) — nenhuma mudança visual.
- Console: nenhum erro novo.

**Resultado: sem regressão.**

---

## 10. Assets

| Asset | Status |
|---|---|
| `assets/images/pages/informacoes/informacoes.jpg` | Já existente, reutilizado — **não baixado novamente** (data de modificação do arquivo confirma que não foi tocado nesta etapa) |
| `assets/images/pages/informacoes/pexels-thepaintedsquare-583847-1024x683.jpg` | Novo — 97.418 bytes, idêntico ao `content-length` do original; `getimagesize()` confirma 1024×683, JPEG |

Nenhum download duplicado, nenhuma requisição de imagem retornou 404, nenhuma imagem quebrada
(`naturalWidth === 0`) em nenhum dos 5 viewports.

---

## 11. Console e rede

- **Console**: nenhum erro JavaScript do próprio código em nenhum dos 5 viewports (as únicas
  mensagens observadas são ruído de extensão de navegador/antivírus local, já documentado em
  validações anteriores — não originadas pelo código do projeto).
- **Rede**: nenhum 404 em nenhum asset próprio (CSS, JS, fontes, imagens) — 93 requisições
  verificadas em 1440×900, todas 200 (exceto chamadas externas esperadas do Google Maps do
  footer).
- **Fontes**: carregadas localmente (`assets/fonts/*.woff2`), nenhuma requisição a Google Fonts
  remoto.
- **Dependências legadas**: varredura do HTML renderizado confirma ausência de
  `wp-content`/`wp-includes`, Elementor e jQuery (`window.jQuery === undefined`). Único script de
  terceiro: Swiper (`assets/vendor/swiper/`, já usado em Home/Sobre Nós/Clientes) — nenhuma
  biblioteca nova.

---

## 12. Diferenças conscientes reconfirmadas (não tratadas como regressão)

- Cards de "Últimas notícias" reaproveitados de `blog-section.php` (com selo de categoria),
  substituindo o widget "Posts" mais pobre do original.
- Grid responsivo consistente em vez do 2+1 desequilibrado do WordPress.
- Carrossel 6/2/1 com `object-fit:contain` e cards premium (`.logo-card`).
- 82 logos válidos (3 arquivos 404 do original não reproduzidos).
- CTA da Dedicação corrigido para `/fale-conosco/` (categoria C).
- `config/dedication-section.php`/`config/blog-posts.php` como fonte única de conteúdo
  compartilhado entre páginas.

---

## 13. REFERENCE DRIFT

Nenhum novo. Nenhuma divergência entre o site ao vivo e o baseline (`informacoes-audit.md`) foi
observada nesta validação — DRIFT-001 ("Área Restrita" ausente no header ao vivo) permanece
válido e não foi reavaliado aqui (mesmo padrão de todas as outras páginas já validadas).

---

## 14. Pendências não bloqueantes

- Nenhuma pendência nova introduzida por esta página.
- Pendências já conhecidas e não específicas de `/informacoes/` (atributo `autocomplete` no
  formulário de contato, `endereco.bairro`/`endereco.cep` pendentes em `config/company.php`,
  botão "Área Restrita" mantido por decisão de baseline) continuam registradas em
  `home-final-validation.md`/`sobre-nos-final-validation.md`/`reference-baseline.md` — não
  repetidas aqui como novidade.
