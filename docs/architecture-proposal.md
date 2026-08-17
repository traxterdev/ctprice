# Proposta de Arquitetura — Novo Site CT Price

## Status

Documento de **definição de arquitetura** — Revisão 2. Nenhum código foi criado nesta etapa. Nenhum diretório ou arquivo fora de `docs/` foi tocado.

Esta revisão aplica decisões já tomadas pelo cliente em relação à Revisão 1: estrutura de pastas na raiz pública (sem `pages/`), uso do Swiper (sem motor de carrossel próprio) e uma política formal de fidelidade visual com classificação A/B/C/D dos comportamentos do site atual. Ainda precisa de aprovação final antes da implementação (ver seção 14).

## Premissas (herdadas do CLAUDE.md e dos documentos de auditoria)

- Stack: **PHP 8+, HTML5, CSS puro, JavaScript puro**. Sem WordPress, sem React, sem Tailwind, sem Bootstrap, sem framework PHP, sem build obrigatório.
- Referência visual oficial: o site atual (`docs/reference/home-*-audit.md`), com fidelidade máxima — isto **não é um redesign**.
- Base estrutural: `docs/reference/site-inventory.md` — **13 páginas reais** (10 páginas "site" + 3 posts de blog), header/topbar/footer duplicados e divergentes por página no WordPress atual, vários links quebrados/inconsistentes já catalogados.
- Objetivo desta etapa: eliminar a **duplicação** (a causa raiz de bugs como os dois números de WhatsApp diferentes) e formalizar **o que é fidelidade e o que é defeito** — sem ainda corrigir nada.

---

## 1. Árvore de diretórios revisada

```
ctprice/                              (raiz do site — document root do hosting)
├── index.php                         # Home
├── sobre-nos/index.php
├── clientes/index.php
├── parcerias/index.php               # slug real do site atual (menu mostra "Parceiros", URL é /parcerias/)
├── fale-conosco/index.php
├── informacoes/index.php
├── trabalhe-conosco/index.php        # contém a âncora #beneficios
├── ouvidoria/index.php
├── depoimentos/index.php
├── arearestrita/index.php            # slug real do site atual (sem hífen)
│
├── blog/
│   ├── _post-template.php            # layout compartilhado dos posts
│   ├── reforma-trabalhista-volta-a-pauta-do-stf-julgamento-acontece-neste-mes/index.php
│   ├── receita-federal-e-correios-lancam-portal-de-compras-internacionais/index.php
│   └── hello-world/index.php         # slug preservado por padrão — ver decisão pendente (seção 14)
│
├── .htaccess                         # (futuro) redirects /wp/* → /*, normalização http→https — ver seção 11
├── 404.php
├── robots.txt
├── sitemap.xml                       # 13 URLs fixas, mantido manualmente
│
├── config/
│   ├── bootstrap.php                 # constantes (BASE_PATH, SITE_URL...), carrega company.php + menu.php
│   ├── company.php                   # dados globais da empresa — fonte única (ver seção 12)
│   └── menu.php                      # estrutura única do menu — fonte única (ver seção 3)
│
├── includes/                         # presentes em toda página
│   ├── head.php                      # <head>: meta, title/description por página, <link> de CSS, favicon
│   ├── header.php                    # topbar + header + nav
│   ├── footer.php                    # footer principal + footer bottom bar
│   ├── cookie-notice.php
│   ├── whatsapp-button.php           # botão flutuante
│   ├── scripts.php                   # <script> no fim do body
│   ├── page-start.php                # abre <html>, inclui head.php + header.php
│   └── page-end.php                  # inclui footer/cookie/whatsapp/scripts, fecha </html>
│
├── components/                       # blocos visuais reutilizáveis, parametrizados
│   ├── hero-slider.php               # hero-carrossel (só a Home) — instancia Swiper
│   ├── hero-inner.php                # hero interno (eyebrow + título + foto) — páginas institucionais
│   ├── section-heading.php           # padrão eyebrow + divisor + H2
│   ├── icon-box.php                  # 3 variantes: circle | card | plain
│   ├── service-card.php
│   ├── testimonial-carousel.php      # instancia Swiper
│   ├── logo-carousel.php             # instancia Swiper; recebe a lista de logos por parâmetro
│   ├── blog-card.php
│   ├── contact-form.php              # recebe config de campos/destino por parâmetro
│   └── button.php                    # variante pill-outline | filled
│
└── assets/
    ├── vendor/
    │   └── swiper/
    │       ├── swiper-bundle.min.css # armazenado localmente, sem dependência obrigatória de CDN
    │       └── swiper-bundle.min.js
    ├── css/
    │   ├── tokens.css                # cores, fontes, radii, sombras, spacing — valores exatos das auditorias
    │   ├── reset.css
    │   ├── base.css
    │   ├── layout.css                # container boxed 1200px, grids/gutters compartilhados
    │   ├── components/
    │   │   ├── topbar.css
    │   │   ├── header.css            # breakpoint de 1024px (hambúrguer) — próprio deste componente
    │   │   ├── footer.css
    │   │   ├── buttons.css
    │   │   ├── hero-home.css
    │   │   ├── hero-inner.css
    │   │   ├── icon-box.css          # breakpoint de 767px (grid → coluna)
    │   │   ├── cards.css
    │   │   ├── carousel.css          # estilos próprios SOBRE as classes do Swiper (setas, bullets, cores)
    │   │   ├── testimonials.css
    │   │   ├── forms.css
    │   │   ├── cookie-notice.css
    │   │   └── whatsapp-button.css
    │   └── blog.css
    ├── js/
    │   ├── main.js                   # inicializa os componentes presentes na página
    │   └── components/
    │       ├── carousels-init.js     # cria as 3 instâncias Swiper com os parâmetros já auditados
    │       ├── mobile-menu.js        # toggle do hambúrguer + expansão de submenu
    │       ├── cookie-notice.js
    │       ├── contact-form.js       # máscara de telefone + validação client-side
    │       └── smooth-scroll.js      # rolagem até #beneficios e outras âncoras
    ├── fonts/
    │   ├── roboto/ roboto-slab/ roboto-flex/ poppins/     (*.woff2)
    └── images/
        ├── logo/
        ├── icons/                    # sprite.svg com os ícones usados (ver seção 6)
        ├── hero/
        ├── content/
        ├── testimonials/
        ├── blog/
        └── clients/
            ├── home-carousel/        # logos do carrossel da Home (~85–105 arquivos)
            └── clientes-page/        # grade de logos própria da página "Clientes" — conjunto DIFERENTE, confirmado no inventário
```

**Mudança-chave desta revisão:** as 10 páginas institucionais deixam de viver em `pages/{slug}/index.php` e passam a ser pastas diretamente na raiz pública (`/{slug}/index.php`). Isso produz naturalmente URLs como `/sobre-nos/`, `/clientes/`, `/fale-conosco/` **sem roteador central e sem depender de `mod_rewrite` para o funcionamento básico** — qualquer hospedagem PHP tradicional serve `/{pasta}/` através do `index.php` daquela pasta automaticamente. `mod_rewrite` só entra em cena depois, como recurso opcional, exclusivamente para os redirecionamentos 301 de `/wp/*` (seção 11) — o site funciona corretamente mesmo sem essa camada.

Correções de slug feitas nesta revisão para bater com o site real (`site-inventory.md`): **`parcerias/`** (não `parceiros/` — o menu mostra "Parceiros" mas a URL real é `/wp/parcerias/`) e **`arearestrita/`** (sem hífen, igual ao slug real `/wp/arearestrita/`).

---

## 2. Regra de fidelidade visual

> **FIDELIDADE VISUAL NÃO SIGNIFICA REPRODUÇÃO DE DEFEITOS.**

Esta é a política formal do projeto a partir desta revisão. Fidelidade obrigatória se aplica a identidade, composição, estrutura visual, tipografia, cores, espaçamentos, proporções, comportamento, componentes e animações relevantes. **Defeitos técnicos identificados no site original podem ser corrigidos** — mas nenhuma correção é feita nesta etapa; aqui só se registra a classificação.

Todo comportamento do site atual catalogado nas auditorias (`home-desktop-audit.md`, `home-tablet-audit.md`, `home-mobile-audit.md`, `site-inventory.md`) se enquadra em uma de quatro categorias:

### A. Fidelidade obrigatória (preservar exatamente como medido)
- Paleta de cores exata, em hex (`home-desktop-audit.md` §7).
- Famílias tipográficas, tamanhos, pesos e `line-height` de cada elemento (§6).
- Larguras de container (`1200px` boxed), número de colunas e largura de cada uma por seção/breakpoint.
- Paddings, margins, gaps medidos (§5).
- Border-radius, bordas e sombras catalogadas (§9–10).
- Proporções de imagens e posição/recorte (`background-position`, `background-size`) onde não houver defeito associado.
- Estrutura e ordem das seções de cada página.
- Composição visual dos componentes (as 3 variantes de icon-box, os 2 estilos de botão, o padrão eyebrow+divisor+H2).

### B. Comportamento a preservar (funciona assim de forma real e mensurável — não é um bug, mesmo quando parece incomum)
- Header não-sticky (rola junto com a página).
- **Hambúrguer ativando em `1024px` enquanto o conteúdo permanece em layout desktop até `767px`** — o estado híbrido "tablet" descrito em `home-tablet-audit.md`. Não há nada tecnicamente quebrado aqui: são dois breakpoints reais e distintos, cada um controlando um subconjunto diferente de componentes no site de origem. Preservar tal como medido, sem unificar os dois breakpoints.
- Configurações medidas dos 3 carrosséis Swiper: `autoplay_speed` (4000ms hero / 5000ms depoimentos e logos), `loop`, presença/ausência de setas e paginação por carrossel, `space_between` (§12–13 de `home-desktop-audit.md`).
- Menu dropdown mobile/tablet *in-flow* (empurra o conteúdo, não é overlay/modal), expansão inline de submenu.
- Troca do arquivo de logo via `srcset`/`sizes` conforme a largura da viewport.
- Animações de entrada (`fadeInUp`, `fadeInLeft`, `fadeInRight`) e durações de transição de hover (`0.3s`/`0.4s`/`0.25s`) catalogadas em §14 e §11.

### C. Defeito conhecido a corrigir (registrado agora, corrigido apenas em etapa futura — **nada é corrigido nesta revisão**)

| Defeito | Onde foi encontrado | Correção prevista (não aplicada ainda) |
|---|---|---|
| Links 404: `ctprice.com.br/contato` (CTA "Fale Conosco" da Home) e `ctprice.com.br/documentos` (Área Restrita → Clientes) | `site-inventory.md` §3 | Apontar para os destinos reais (`/fale-conosco/`, sistema de documentos correto quando fornecido) |
| WhatsApp divergente entre páginas (`...2616117` na Home vs. `...2324097` nas demais) | `site-inventory.md` §4 | Único número em `config/company.php` (qual é o correto: decisão do cliente, seção 14) |
| Link incorreto de "Benefícios" (nem o do menu nem o do footer apontam para a âncora real, que existe só em `/wp/trabalhe-conosco/`) | `site-inventory.md` §3 | `config/menu.php` apontar para `/trabalhe-conosco/#beneficios` |
| Link incorreto/inconsistente de "Trabalhe Conosco" (header aponta para o sistema externo de recrutamento, footer aponta para a página institucional) | `site-inventory.md` §4 | Escolher um destino único em `config/menu.php` (qual dos dois: decisão do cliente, seção 14) |
| `ctprice.com.br/sh-admin` expondo uma listagem de diretório crua em vez de um sistema funcional | `site-inventory.md` §2 | Fora do controle do site institucional — aguardar destino correto do cliente/fornecedor do sistema |
| Imagens 404 no carrossel de logos: `mv.jpg`, `modelo.jpg`, `logo_0020_Camada16.jpg` | `home-desktop-audit.md` §16 | Remover essas 3 entradas da lista de logos ou obter os arquivos corretos |
| Distorção excessiva dos logos de clientes no mobile (`object-fit:fill` numa caixa de ~350×208px, muito maior que os ~100×65px do desktop) | `home-mobile-audit.md`, Carrosséis | Usar `object-fit:contain` (ou caixa proporcional) em vez de `fill`, e/ou não ampliar tanto a caixa no mobile |
| Colapso da imagem de "Por que nos escolher" no mobile (container cai para ~20px de altura) | `home-mobile-audit.md`, seção 8 / "Outros achados" | Dar altura proporcional à imagem quando empilhada, em vez de herdar uma altura fixa do desktop |
| Quebra extrema de texto do hero no mobile (`padding-left:150px` e `font-size:30px` fixos, não responsivos) | `home-mobile-audit.md`, Hero | Tornar padding e tamanho de fonte responsivos no breakpoint mobile |
| Link do menu "Ouvidoria" em `http://` (sem HTTPS) e sem barra final | `site-inventory.md` §3 | Padronizar como os demais itens do menu (`https://`, com barra final) |

Nenhum item desta tabela é corrigido nesta etapa — a correção é trabalho de implementação futura, item por item, com o mesmo processo do CLAUDE.md (medir → documentar → só então implementar).

### D. Decisão que depende do cliente
Ver seção 14 — são perguntas que a arquitetura não pode responder sozinha (qual WhatsApp é o correto, para onde "Trabalhe Conosco" deve apontar de fato, o que fazer com o slug `hello-world`, os dois destinos quebrados da Área Restrita, etc.).

---

## 3. Includes PHP globais

Elementos que aparecem em **todas as páginas** viram include, com uma única cópia física — isto é o que elimina a duplicação (e os bugs de duplicação, classificados como C acima) do WordPress atual:

| Include | Conteúdo | Substitui, no site atual |
|---|---|---|
| `includes/head.php` | `<head>`, meta tags, título/descrição por página, links de CSS/`assets/vendor/swiper/swiper-bundle.min.css`, favicon | Cópia de `<head>` repetida por página no WP |
| `includes/header.php` | Topbar + header + menu + botão "Área Restrita", lendo `config/menu.php` e `config/company.php` | 13 cópias independentes de topbar/header |
| `includes/footer.php` | Footer principal + footer bottom bar, lendo `config/menu.php` e `config/company.php` | 13 cópias independentes de footer |
| `includes/cookie-notice.php` | Aviso de cookies | Repetido por página |
| `includes/whatsapp-button.php` | Botão flutuante, lendo o WhatsApp canônico de `config/company.php` | Repetido por página |
| `includes/page-start.php` / `page-end.php` | "Casca" comum de página (abre/fecha `<html>`, inclui os itens acima) | — (não existe equivalente no WP) |

Com a nova estrutura de pastas na raiz, uma página institucional em `/{slug}/index.php` fica:
```php
<?php
$pageTitle = '...';
$metaDescription = '...';
require __DIR__ . '/../includes/page-start.php';
?>
  <!-- conteúdo específico da página, via components/ -->
<?php require __DIR__ . '/../includes/page-end.php'; ?>
```
(um nível acima, `../includes/`, já que a página agora está um nível mais raso do que na Revisão 1)

**Ponto crítico de arquitetura, sem exceção:** nenhum telefone, endereço, e-mail, link de menu ou qualquer outro dado global pode ser escrito manualmente dentro de uma página ou de um include específico de página — tudo isso vem de `config/company.php` e `config/menu.php`. Como `header.php` e `footer.php` leem os **mesmos dois arquivos**, a divergência de conteúdo entre páginas (classificada como defeito C acima) deixa de ser possível estruturalmente.

---

## 4. Componentes reutilizáveis (`components/`)

Sem mudanças de fundo em relação à Revisão 1 — mantidos como blocos parametrizados chamados via `require` + variáveis PHP, sem engine de template:

| Componente | Onde é usado | Parâmetros típicos |
|---|---|---|
| `hero-slider.php` | Home | slides (imagem + texto), config de autoplay — instancia um Swiper via `carousels-init.js` |
| `hero-inner.php` | Páginas institucionais (confirmado visualmente em "A CT Price" e "Clientes"; presumido nas demais — decisão pendente, seção 14) | eyebrow, título, foto de fundo |
| `icon-box.php` | "Bem-vindo" (`circle`), "Nossos Serviços" (`card`), "Por que nos escolher" (`plain`) | `$variant`, ícone, título, texto |
| `section-heading.php` | "Nossos Serviços", "Depoimentos", "Por que nos escolher", "Últimas notícias" | eyebrow, título |
| `service-card.php` | "Nossos Serviços" | ícone, título, texto |
| `testimonial-carousel.php` | Home; possivelmente "Depoimentos" | array de depoimentos — instancia Swiper |
| `logo-carousel.php` | Home (um conjunto de logos) e "Clientes" (outro conjunto — não é o mesmo carrossel) | array de logos (path, alt) — instancia Swiper |
| `blog-card.php` | Home ("Últimas notícias") | dados do post |
| `contact-form.php` | Home, "Fale Conosco", "Ouvidoria" | campos, destino, diretório por departamento (só Fale Conosco) |
| `button.php` | Em toda parte | `$variant` (`pill-outline` \| `filled`), texto, href |

---

## 5. Organização de CSS

Sem pré-processador, sem build: arquivos carregados via múltiplas tags `<link rel="stylesheet">` em `head.php`, em ordem fixa:

1. `vendor/swiper/swiper-bundle.min.css` — base do Swiper, sem alterações
2. `tokens.css` — variáveis extraídas literalmente das auditorias
3. `reset.css`
4. `base.css`
5. `layout.css`
6. `components/*.css` — inclui `carousel.css`, que estiliza **por cima** das classes do Swiper (`.swiper-pagination-bullet`, `.swiper-button-next/prev` etc.) com as cores e tamanhos já medidos (ex.: bullet inativo `#EFCB39`, ativo `#2A3855`), sem reescrever a mecânica do carrossel
7. `blog.css`

Regra de responsividade (classificada como **B — comportamento a preservar**, seção 2): cada arquivo de componente usa o breakpoint que a auditoria mediu para aquele componente específico, sem unificar — `header.css` muda em `1024px`, os grids de conteúdo (`icon-box.css`, `cards.css`) mudam em `767px`, `blog.css` tem os dois estágios (`1024px` → 2 colunas, `767px` → 1 coluna). Media queries ficam dentro do próprio arquivo do componente, não centralizadas.

---

## 6. Organização de JavaScript

**Decisão desta revisão: usar Swiper, não construir um motor de carrossel próprio.** O site original já usa Swiper 8.4.5 para os 3 carrosséis, e as três configurações completas já foram levantadas nas auditorias (`home-desktop-audit.md` §12–13: hero com `autoplay_speed:4000`, `navigation:"none"`; depoimentos com `autoplay_speed:5000`, `loop:true`, setas e bullets; logos com `slides_to_show:10`, `autoplay_speed:5000`). Reescrever esse comportamento do zero seria retrabalho desnecessário e risco de divergência de comportamento — o contrário do que a fidelidade pede.

- `assets/vendor/swiper/` — Swiper armazenado localmente no projeto (arquivos `swiper-bundle.min.css`/`.js`), **sem dependência obrigatória de CDN**. Recomenda-se a mesma faixa de versão já validada nas auditorias (Swiper 8.x) para minimizar risco de mudança de comportamento.
- `assets/js/components/carousels-init.js` — **único arquivo próprio relacionado a carrossel**: cria as 3 instâncias (`new Swiper(...)`) com os parâmetros já auditados, um bloco de configuração por carrossel. Não reimplementa nada do Swiper, só configura.
- `main.js` — ponto de entrada único, carregado no fim do body (`includes/scripts.php`), inicializa os demais componentes presentes na página.
- `components/mobile-menu.js` — toggle do hambúrguer e expansão inline de submenu (comportamento *in-flow*, não overlay — classificado B).
- `components/contact-form.js` — máscara de telefone e validação client-side básica (substitui `jquery.mask`, sem precisar de jQuery).
- `components/cookie-notice.js`
- `components/smooth-scroll.js` — necessário para a âncora `#beneficios` funcionar corretamente depois de corrigida (defeito C).

**Dependências de terceiros que permanecem** (integrações necessárias, não "framework"):
- **Google reCAPTCHA v3** — os 3 formulários dependem dele hoje; mantido por ser funcionalidade, não dependência de arquitetura. Requer chave nova para o domínio final (decisão pendente, seção 14).
- **Google Maps** via `<iframe>` simples, sem API JS.
- **GTranslate** (seletor de idioma) — plugin exclusivo de WordPress, sem equivalente direto fora dele; decisão de produto pendente (seção 14).

---

## 7. Organização de imagens, logos, ícones e fontes

Sem mudanças de fundo em relação à Revisão 1 — organização por categoria confirmada, **nenhum download ou migração de asset é feito nesta etapa**:

- `assets/images/logo/` — variantes do logo (o site atual já usa `srcset` com 4 tamanhos; manter o mesmo padrão).
- `assets/images/hero/` — os 4 fundos do hero da Home + fotos de hero interno por página institucional.
- `assets/images/content/` — fotos de conteúdo (vídeo institucional, foto de "Por que nos escolher", etc.).
- `assets/images/testimonials/` — os 4 avatares.
- `assets/images/blog/` — thumbnails e imagens de conteúdo dos 3 posts.
- `assets/images/clients/home-carousel/` e `assets/images/clients/clientes-page/` — duas pastas separadas, porque são dois conjuntos de logos diferentes (confirmado no inventário) — tratar como um único banco seria uma suposição não verificada.
- `assets/images/icons/sprite.svg` — só os ícones realmente usados (catalogados em `home-desktop-audit.md` §15), como sprite SVG único com `<symbol>`, em vez de carregar uma biblioteca de ícones inteira.
- `assets/fonts/{roboto,roboto-slab,roboto-flex,poppins}/*.woff2` — auto-hospedadas, mesma estratégia do site atual.

---

## 8. Estratégia para páginas

**Decidido nesta revisão: uma pasta física por página diretamente na raiz pública, sem roteador central.** `/{slug}/index.php` faz `/{slug}/` funcionar automaticamente em qualquer hospedagem PHP tradicional, sem depender de `mod_rewrite` para o roteamento básico — só os redirecionamentos de `/wp/` (seção 11, ainda não implementados) usam `mod_rewrite`, e são opcionais para o site funcionar (afetam só quem chega por links antigos).

A alternativa de um `router.php` único com array `slug => arquivo` foi descartada como padrão — o site tem exatamente 13 URLs fixas, não é um catálogo dinâmico que justifique essa indireção.

Cada `/{slug}/index.php` segue o padrão mínimo mostrado na seção 3: define variáveis de página, inclui `page-start.php`, chama `components/*.php`, inclui `page-end.php`. Nenhuma duplicação de topbar/header/footer/menu existe nesse arquivo.

---

## 9. Estratégia para os 3 posts do blog

Mantido como na Revisão 1, conforme confirmado pelo cliente: **template compartilhado + arquivo de dados por post**, sem CMS nesta primeira versão. O site atual já faz a coisa certa aqui (os 3 posts compartilham um único template Elementor, `elementor-page-1049`) — a proposta preserva esse padrão em vez de reinventá-lo:

- `blog/_post-template.php` — layout compartilhado (cabeçalho do post, imagem de capa, badge de categoria, corpo do texto, data).
- Cada post vive em `blog/{slug}/index.php`, que só define os dados (título, categoria, data, corpo, imagem) e faz `require '../_post-template.php'`.

Isso é suficiente para **3 posts** e não introduz complexidade agora. A arquitetura permite evolução futura sem retrabalho: se o volume de posts crescer, o mesmo `_post-template.php` pode passar a ser alimentado por um array de dados centralizado (ex.: `blog/_posts-data.php`) ou por um pequeno roteador dedicado só ao blog — mudança isolada, que não afeta `config/`, `includes/`, `components/` nem as páginas institucionais. Não é implementada agora porque o volume atual (3 posts, sem cadência definida) não justifica.

Não existe página de arquivo/índice de blog no site atual (`/wp/blog/` é 404) — a proposta não cria uma por padrão, para não inventar uma estrutura que não existe no site de referência (decisão de produto pendente, seção 14, caso seja desejável no futuro).

---

## 10. Estratégia de URLs para remover `/wp/`

Regra geral: **a mesma slug real do WordPress, um segmento a menos**, já refletida na árvore da seção 1 (com as correções de `parcerias/` e `arearestrita/`):

| Antes | Depois |
|---|---|
| `https://ctprice.com.br/wp/` | `https://ctprice.com.br/` |
| `https://ctprice.com.br/wp/sobre-nos/` | `https://ctprice.com.br/sobre-nos/` |
| `https://ctprice.com.br/wp/clientes/` | `https://ctprice.com.br/clientes/` |
| `https://ctprice.com.br/wp/parcerias/` | `https://ctprice.com.br/parcerias/` |
| `https://ctprice.com.br/wp/fale-conosco/` | `https://ctprice.com.br/fale-conosco/` |
| `https://ctprice.com.br/wp/informacoes/` | `https://ctprice.com.br/informacoes/` |
| `https://ctprice.com.br/wp/trabalhe-conosco/` | `https://ctprice.com.br/trabalhe-conosco/` |
| `https://ctprice.com.br/wp/ouvidoria/` | `https://ctprice.com.br/ouvidoria/` |
| `https://ctprice.com.br/wp/depoimentos/` | `https://ctprice.com.br/depoimentos/` |
| `https://ctprice.com.br/wp/arearestrita/` | `https://ctprice.com.br/arearestrita/` |
| `https://ctprice.com.br/wp/reforma-trabalhista-volta-a-pauta-do-stf-julgamento-acontece-neste-mes/` | mesma slug, sem `/wp/` |
| `https://ctprice.com.br/wp/receita-federal-e-correios-lancam-portal-de-compras-internacionais/` | mesma slug, sem `/wp/` |
| `https://ctprice.com.br/wp/hello-world/` | mesma slug, sem `/wp/` (ver decisão pendente sobre renomear, seção 14) |
| `https://ctprice.com.br/wp/home/` (alias) | vai direto para `https://ctprice.com.br/` (não existe `/home/` como página própria) |

Com o padrão de pasta-por-página, `/sobre-nos` e `/sobre-nos/` resolvem naturalmente (Apache normaliza diretório com barra final), mantendo a mesma convenção de URL com barra final que o WordPress já usa.

---

## 11. Preservação de URLs / estratégia futura de redirecionamento 301 para SEO

**Não implementado nesta etapa** — apenas a estratégia, para ser aplicada quando o novo site for publicado.

Regra: `.htaccess` na raiz com redirecionamentos **301 (permanente)**, nunca 302, mapeando cada URL antiga para a nova (mesma slug, sem `/wp/`):

```
/wp/                                                                          → /
/wp/home/                                                                     → /            (não para /home/, que não existe)
/wp/sobre-nos/                                                                → /sobre-nos/
/wp/clientes/                                                                 → /clientes/
/wp/parcerias/                                                                → /parcerias/
/wp/fale-conosco/                                                             → /fale-conosco/
/wp/informacoes/                                                              → /informacoes/
/wp/trabalhe-conosco/                                                         → /trabalhe-conosco/
/wp/ouvidoria/                                                                → /ouvidoria/
/wp/depoimentos/                                                              → /depoimentos/
/wp/arearestrita/                                                             → /arearestrita/
/wp/reforma-trabalhista-volta-a-pauta-do-stf-julgamento-acontece-neste-mes/   → /reforma-trabalhista-volta-a-pauta-do-stf-julgamento-acontece-neste-mes/
/wp/receita-federal-e-correios-lancam-portal-de-compras-internacionais/      → /receita-federal-e-correios-lancam-portal-de-compras-internacionais/
/wp/hello-world/                                                              → /hello-world/  (ou para a nova slug, se for renomeado — decisão pendente)
```

Pode ser implementado como 13 regras explícitas (mais simples de auditar) ou uma regra genérica `^wp/(.*)$` → `/$1` (301) cobrindo as 10 páginas + 3 posts de uma vez, com uma regra específica para `/wp/home/` → `/` antes dela (já que a regra genérica mandaria para `/home/`, que não existe). Também deve normalizar `http://` → `https://`, preservando o link "Ouvidoria" antigo que usava `http://` sem `s`.

Outras ações de SEO previstas para quando a implementação começar (não fazer agora): `sitemap.xml` com as 13 URLs finais, `robots.txt` atualizado, e correção do CTA "Fale Conosco" da Home (hoje aponta para a URL quebrada `/contato`, sem nada de SEO a preservar ali).

---

## 12. Tratamento da página Área Restrita

A página em si permanece estática (`/arearestrita/index.php`): dois cartões ("Clientes" e "Colaboradores"), cada um com um link de saída para um sistema externo de terceiros. Não há autenticação nem backend a construir no site institucional.

Os dois destinos atuais estão quebrados (`ctprice.com.br/documentos` 404, `ctprice.com.br/sh-admin` expondo listagem de diretório — ambos classificados como defeito C, seção 2). As duas URLs ficam armazenadas em `config/company.php`, não *hardcoded* na página, para que a correção futura seja uma alteração de uma linha. Manter quebrado até o cliente fornecer os destinos corretos, ou desabilitar temporariamente os botões no lançamento, é decisão pendente (seção 14).

---

## 13. Onde armazenar os dados globais da empresa

`config/company.php` retorna um array associativo único, incluído por `bootstrap.php` e consumido por `header.php`, `footer.php`, `whatsapp-button.php` e `contact-form.php`:

- **Identidade**: razão social, nome fantasia, responsável técnico (Marcelo Barbosa da Silva, CRC MS 7986-O).
- **Endereço**: rua, bairro, CEP, cidade, UF, link do Google Maps e URL de embed do iframe.
- **Contato principal**: telefone fixo, WhatsApp principal (canônico único — decisão pendente), e-mail de contato, e-mail de proteção de dados.
- **Contatos por departamento** (só usados na página Fale Conosco): Comercial, Pessoal, Fiscal, Contábil, Central/Empresarial — cada um com telefone/WhatsApp próprio, já existentes e legítimos no site atual.
- **Links de sistemas externos**: recrutamento, Área Restrita (Clientes/Colaboradores), agência de desenvolvimento.
- **Copyright**: ano calculado dinamicamente (`date('Y')`), em vez de fixo como hoje ("© 2024" desatualizado).

`config/menu.php` guarda a estrutura de navegação pelo mesmo princípio (label, URL, filhos) — consumida por `header.php` e pela parte de `footer.php` que replica o menu secundário. **Nenhum telefone, endereço, e-mail ou link de menu é escrito manualmente em nenhuma página** — essa é a regra que elimina estruturalmente os defeitos de divergência classificados como C na seção 2.

---

## 14. Decisões pendentes de aprovação do cliente

### 14.1 Correções classificadas como defeito (C) que precisam de uma decisão de conteúdo antes de serem aplicadas
1. **WhatsApp canônico**: `(67) 99261-6117` (Home) ou `(67) 99232-4097` (demais páginas)?
2. **"Trabalhe Conosco" (item de menu de topo)**: sistema externo de recrutamento ou página institucional própria (`/trabalhe-conosco/`)?
3. **Link "Benefícios"**: confirmar a correção para `/trabalhe-conosco/#beneficios` (destino onde a âncora realmente existe).
4. **Slug `hello-world`**: manter por continuidade de URL/SEO, ou renomear com redirecionamento 301 do slug antigo?
5. **Página de Área Restrita**: manter os dois links de saída como estão (quebrados) até o cliente fornecer os destinos corretos, ou desabilitar temporariamente esses botões no lançamento?
6. **Destinos `documentos` e `sh-admin`**: quais são as URLs corretas dos sistemas de terceiros (fora do controle deste projeto)?

### 14.2 Decisões de escopo/produto para o novo site (não são sobre o site atual)
7. **Hero interno (`hero-inner.php`)**: confirmado visualmente em 2 de 7 páginas institucionais que deveriam usá-lo; as outras 5 precisam de auditoria visual própria antes da implementação de cada página (processo já previsto no CLAUDE.md).
8. **Seletor de idiomas (GTranslate)**: manter alguma forma de troca de idioma (qual mecanismo, já que o plugin atual é exclusivo do WordPress), ou remover essa funcionalidade?
9. **Chave do reCAPTCHA v3**: gerar uma chave nova associada ao domínio final de produção.
10. **Página de arquivo de blog**: criar uma (não existe hoje) ou manter os posts acessíveis só pelos cards da Home?
11. **Volume futuro de posts de blog**: confirmar se 3 posts é o volume esperado (mantendo o padrão simples atual) ou se há intenção de publicar com frequência, o que mudaria a recomendação da seção 9 para um formato dirigido por dados.

---

## Conflitos técnicos ainda em aberto

Nenhum conflito técnico bloqueante foi identificado nesta revisão. Um ponto de atenção não bloqueante: a versão exata do Swiper a empacotar em `assets/vendor/swiper/` (recomendação: 8.x, mesma faixa já validada nas auditorias) só pode ser confirmada quando os arquivos forem efetivamente obtidos na etapa de implementação — não há decisão pendente aqui, só uma verificação a fazer no momento certo.
