# Validação final — `/parcerias/` + regressão dos componentes compartilhados

Data: 2026-08-30
Documentação-base: `docs/reference/parcerias-audit.md`, `docs/reference/reference-baseline.md`,
`docs/reference/clientes-final-validation.md`, `docs/reference/sobre-nos-final-validation.md`,
`docs/reference/home-final-validation.md`

Escopo: validação definitiva da nova página `/parcerias/` (Hero + faixa Ferramentas + grade
Ferramentas + faixa Parceiros + grade Parceiros) e da regressão nos componentes compartilhados
refatorados durante a implementação (`boxed-hero.php` com `background_position` configurável,
`client-logo-card` → `logo-card` renomeado), antes do checkpoint Git. Nenhum commit foi feito
nesta tarefa.

---

## 1. Estrutura final de `/parcerias/`

Ordem confirmada no DOM em 1440×900, 900×1200, 768×1024, 767×1024 e 390×844: topbar → header →
Hero → faixa "Ferramentas WEB para os Clientes CT Price" → grade Ferramentas (11) → faixa
"Parceiros" → grade Parceiros (51) → footer → bottom bar → WhatsApp flutuante → cookie banner.
Nenhum componente global foi alterado por esta página.

Espaçamentos confirmados (mesmos nos 5 viewports, sem gaps/sobreposições inesperados): 0px entre
Hero e a primeira faixa (`.section-title-band` encosta no Hero, como medido no site original),
50px em todas as demais transições (faixa→grade, grade→faixa, faixa→grade, grade→footer).
`document.documentElement.scrollWidth === clientWidth` confirmado em todos os 5 viewports —
nenhum overflow horizontal.

Conforme instruído, **não foi buscada equivalência de altura total de página com o WordPress** —
a UI foi conscientemente aprimorada (cards premium, grid responsivo com estágio intermediário,
`object-fit:contain`) e uma diferença de altura total é esperada e aceita.

### 1.1 Hero (`components/boxed-hero.php`, reaproveitado de `/clientes/`)

| Propriedade | Medido |
|---|---|
| Altura | 400px (idêntico nos 5 viewports) |
| Imagem de fundo | `assets/images/pages/informacoes/informacoes.jpg` |
| `background-size` | `cover` |
| `background-position` | `0% 0%` (via prop `background_position`, específico desta página) |
| Container | `.boxed-hero__inner`, `max-width:1140px` |
| Conteúdo | eyebrow "parcerias de sucesso" + título "São ainda maiores, quando compartilhado com quem caminha ao nosso lado." |
| Overflow | nenhum nos 5 viewports |

A introdução da prop configurável `background_position` (default `50% 50%`) não introduziu
regressão — ver §6 (regressão de `/clientes/`).

### 1.2 Faixas de título (`components/section-title-band.php`, duas instâncias)

| Propriedade | Ferramentas | Parceiros |
|---|---|---|
| Texto | "Ferramentas WEB<br>para os Clientes CT Price" | "Parceiros" |
| `font-size` | 40px | 50px (medido diferente do original, intencional) |
| Altura | 180px | 180px |
| Gradiente | `linear-gradient(rgb(0,34,44) 0%, rgb(5,112,56) 100%)` | idêntico |
| Container | `max-width:1200px` | idêntico |
| Alinhamento | centralizado, texto branco | idêntico |
| Responsividade | sem overflow nos 5 viewports | idêntico |

As duas instâncias compartilham exatamente a mesma linguagem visual (mesmo componente, mesmo
CSS) — divergem apenas em texto e `font_size`, ambos parametrizados e medidos previamente na
auditoria, não estimados.

---

## 2. Resultado das Ferramentas Web (grade 1)

- **11 itens exatamente** confirmados, 11 imagens únicas — nenhuma duplicação, nenhum item
  perdido, ordem idêntica à auditada em `config/partners.php`.
- Grid: 3 colunas (desktop ≥1024px), 2 colunas (768–1023px, incluindo 900×1200), 1 coluna
  (≤767px, incluindo 767×1024 e 390×844) — confirmado via `getComputedStyle` em todos os
  viewports.
- Cards: identidade visual `.logo-card` (fundo branco, borda sutil, sombra leve, `border-radius`),
  centralizados, `object-fit:contain` — nenhum logo esticado, achatado ou cortado, mesmo os de
  proporção incomum (ex.: `Sindicatos-e-Acordo-Coletivos-3-scaled.png`, bem largo). Padding
  suficiente em todos os breakpoints.
- Hover institucional (`translateY(-4px)`, sombra `0 10px 20px rgba(0,34,44,0.12)`, borda
  `rgba(5,112,56,0.35)`) confirmado via hover real (CDP), sem *layout shift* (usa `transform`, não
  propriedades de layout).
- Links: cada card inteiro é clicável (`<a>` envolvendo toda a área), `target="_blank"`,
  `rel="noopener noreferrer"` presentes em todos os 11, URLs idênticas às auditadas em
  `parcerias-audit.md` — nenhuma URL "corrigida" por iniciativa própria.

---

## 3. Resultado dos Parceiros (grade 2)

- **51 itens exatamente** confirmados, 51 imagens únicas — nenhuma duplicação, nenhum item
  perdido, ordem idêntica à auditada.
- Grid: 5 colunas (desktop ≥1024px), 3 colunas (768–1023px), 2 colunas (≤767px) — confirmado em
  todos os 5 viewports.
- Identidade de card compartilhada com Ferramentas/Clientes/Home/Sobre Nós via `.logo-card`
  (mesmo componente visual, `assets/css/logo-card.css`) — mesma borda, sombra, `border-radius`,
  padding-base, centralização, `object-fit:contain`, hover.
- Logos com proporção/resolução atípica (muito largos, muito altos, baixa resolução) revisados
  individualmente: nenhum aparenta "perdido" ou "aumentado artificialmente" a ponto de ficar
  ruim — o comportamento de não-upscaling do card (mesma regra já aprovada em `/clientes/`)
  mantém proporção e legibilidade em todos os casos observados. Nenhum ajuste de limite de grid
  foi necessário (nenhum problema comprovado que justificasse alterar `columns_desktop` etc.).
- **`auditto.png`** (item sem link): confirmado como `<div class="logo-card logo-card--static">`
  — sem `href`, sem `href="#"`, `cursor:default`, hover neutralizado (permanece visualmente
  estático mesmo com `:hover` ativo). Continua visível e com o mesmo tratamento de card dos
  demais.
- Itens suspeitos documentados na auditoria (`logo-modelo.png`, `agricon-nova-logo-00.png`,
  `tech-contratos.png`, `logo-econet4.jpg`, `Copia-de-Logo-atual-Story-22.png`,
  `NOVA-LOGO-CT-46.png`) permanecem **apenas documentados, sem qualquer alteração** de imagem,
  nome ou URL — pendências de confirmação com o cliente, não corrigidas por iniciativa própria.

---

## 4. Resultado do Hero

Reaproveita `components/boxed-hero.php` de `/clientes/`, com a nova prop `background_position`
setada para `0% 0%` (posiciona o canto superior esquerdo da imagem `informacoes.jpg`, mostrando a
área relevante da foto). Altura 400px, tipografia, container e responsividade idênticos ao
componente já validado — sem qualquer regressão introduzida pela generalização da prop (ver §6).

---

## 5. Resultado da acessibilidade

- **62/62 itens** (11 Ferramentas + 51 Parceiros) possuem `alt` não vazio, coerente com o
  nome/identidade do item — nenhuma imagem relevante com `alt=""`.
- Todos os 61 itens com link são `<a>` nativos, focáveis via Tab, com `:focus-visible` real
  confirmado (elevação idêntica ao hover) em pelo menos um card de cada grade.
- O único item sem URL (`auditto.png`) não expõe nenhuma semântica de link — não é alcançável via
  Tab como link, não tem `role` ou `href` — apenas uma imagem com `alt` dentro de um `<div>`
  visualmente idêntico aos demais cards.
- Nomes acessíveis coerentes: `alt` reflete o nome do parceiro/ferramenta em todos os casos
  auditados.
- Nenhuma correção adicional de acessibilidade foi necessária além do que já estava implementado.

---

## 6. Resultado dos links externos

Todos os 61 links (11 Ferramentas + 50 Parceiros com URL) preservam exatamente as URLs
catalogadas em `parcerias-audit.md` / `config/partners.php`, incluindo os casos sabidamente
duplicados ou não identificados (registrados como pendência em comentário no próprio
`config/partners.php`, não corrigidos). Todos usam `target="_blank" rel="noopener noreferrer"`.
Nenhuma URL foi alterada, "corrigida" ou removida por iniciativa própria.

---

## 7. Resultado do `logo-card.css`

Revisão de `assets/css/logo-card.css` confirma que o arquivo contém **exclusivamente** a
identidade visual compartilhada do card: `display:flex` + centralização, `height`/`padding`
base, `background`, `border`, `border-radius`, `box-shadow`, `transition`, hover
(`@media (hover:hover)`), `:focus-visible`, e `.logo-card__img` com `object-fit:contain` e
não-upscaling (`max-width/max-height:100%; width/height:auto`).

**Não** contém grid, contagem de colunas, Swiper, lightbox, ordenação ou qualquer comportamento
específico de página. Essas responsabilidades permanecem corretamente isoladas em:
- `assets/css/clients-carousel-section.css` (Home/Sobre Nós: carrossel, `slidesPerView`, override
  de tamanho 136px/18px);
- `assets/css/clients-grid-section.css` (Clientes: grid 5/3/2, embaralhamento não afeta CSS);
- `assets/css/logo-grid-section.css` (Parceiros: grid configurável via custom properties,
  modificador `--static`).

**PART E aprovada sem ressalvas.**

---

## 8. Regressão de Home (`/`)

Confirmado, sem nova auditoria completa (apenas ausência de regressão decorrente do rename
`client-logo-card` → `logo-card`):

| Item | Resultado |
|---|---|
| Logos únicos | 82 |
| `slidesPerView` | 6 (desktop) |
| Autoplay | rodando, `loop:true` |
| Altura do card | 136px |
| `object-fit` | `contain` |
| Overflow horizontal | nenhum (1440×900) |
| Ordem | estável entre reloads (primeiros 5 arquivos idênticos) |
| Hover | `translateY(-4px)`, sombra `0 10px 20px rgba(0,34,44,0.12)`, borda `rgba(5,112,56,0.35)` — confirmado via hover real (CDP) em um card ("tcm") |

Nenhuma regressão encontrada.

---

## 9. Regressão de `/sobre-nos/`

Confirmado, sem nova auditoria completa:

| Item | Resultado |
|---|---|
| Logos únicos | 82 |
| `slidesPerView` | 6 / 2 / 1 (1440×900 / 900×1200 / 390×844) |
| Autoplay | rodando (confirmado em carregamento limpo nos 3 viewports), `loop:true` |
| Overflow horizontal | nenhum nos 3 viewports |
| Integração Dedicação → Carrossel → Footer | `.image-content-cta-section` (Dedicação) termina em Y=2401, `.clients-carousel-section` começa em Y=2401 (0px), termina em Y=2601, footer começa em Y=2601 (0px) — mesmo padrão de encaixe já aprovado, sem gap nem sobreposição |

Nenhuma regressão encontrada.

---

## 10. Regressão de `/clientes/`

Confirmado, sem nova auditoria completa:

| Item | Resultado |
|---|---|
| Logos únicos | 82 |
| Grid | 5 / 3 / 2 colunas (1440×900 / 900×1200 / 390×844) |
| Embaralhamento diário | ativo (lógica inalterada em `clients-grid-section.php`) |
| Lightbox | abrir / próximo / fechar confirmados funcionando |
| Ordem | estável entre reloads no mesmo dia |
| Hover | `translateY(-4px)`, sombra e borda idênticas ao padrão institucional — confirmado via hover real em um card do grid |
| `object-fit` | `contain` |
| Hero (`background_position` default) | `50% 50%`, 400px, sem alteração visual |

Nenhuma regressão encontrada.

---

## 11. Correções realizadas

Nenhuma correção foi necessária durante esta validação — todos os itens auditados (PARTS A–I)
passaram nas verificações sem exigir ajuste de código. A implementação já entregue está validada
como está.

---

## 12. Console / rede

- `/parcerias/`: 0 mensagens de console de erro; 139 requisições de rede, todas `200` (CSS, JS,
  fontes, imagens das 2 grades, `informacoes.jpg`); nenhum 404.
- `/` (Home): 1 mensagem de console (`issue`: elemento sem `autocomplete`, pré-existente no
  formulário de contato, não relacionado a esta tarefa); todas as requisições `200`.
- `/sobre-nos/`: 1 mensagem de console (`debug`, endpoint de busca, inofensiva, não relacionada a
  esta tarefa); nenhum erro.
- `/clientes/`: 0 mensagens de console.
- Nenhuma dependência de WordPress, Elementor ou jQuery em nenhuma das 4 páginas. Nenhuma
  biblioteca nova foi introduzida (Swiper já era dependência existente, usado apenas em
  Home/Sobre Nós, não em `/parcerias/`).
- Requisições para `gc.kis.v2.scr.kaspersky-labs.com` observadas em todas as páginas são geradas
  pela extensão do antivírus Kaspersky instalada no navegador de teste — origem externa ao site,
  não fazem parte do código do projeto (mesma natureza dos eventos externos do Google Maps já
  documentados em validações anteriores).

---

## 13. Pendências não bloqueantes

- Confirmação com o cliente sobre os itens com URL suspeita/duplicada:
  `logo-modelo.png`, `agricon-nova-logo-00.png` (mesma URL Secullum de "Ponto"),
  `tech-contratos.png`, `logo-econet4.jpg` (mesma URL econeteditora.com.br),
  `Copia-de-Logo-atual-Story-22.png` (URL chatgpt.com, nome "Parceiro (a confirmar)"),
  `NOVA-LOGO-CT-46.png` (URL ctpricems.woulz.com, nome "Woulz").
- CMS de Parceiros/Ferramentas adiado para fase futura de manutenção de conteúdo (dados estáticos
  em `config/partners.php` por decisão de escopo já registrada).
- `assets/images/pages/informacoes/informacoes.jpg` está posicionado de forma semanticamente
  adequada para reuso futuro por `/informacoes/` (segue o padrão `assets/images/pages/<slug>/` já
  usado em `/clientes/`) — não foi movido, por não haver problema comprovado com o caminho atual.

---

## 14. Arquivos criados/modificados (implementação, não alterados nesta validação)

Criados: `config/partners.php`, `components/logo-grid-section.php`,
`components/section-title-band.php`, `assets/css/logo-grid-section.css`,
`assets/css/section-title-band.css`, `assets/css/logo-card.css` (renomeado de
`client-logo-card.css`), `assets/images/partners/tools/*` (11), `assets/images/partners/companies/*`
(51), `assets/images/pages/informacoes/informacoes.jpg`.

Modificados: `parcerias/index.php`, `components/boxed-hero.php` (prop `background_position`),
`components/clients-carousel-section.php`, `components/clients-grid-section.php`,
`assets/css/boxed-hero.css`, `assets/css/clients-carousel-section.css`,
`assets/css/clients-grid-section.css`, `assets/js/clients-grid-lightbox.js`, `index.php`,
`sobre-nos/index.php`, `clientes/index.php` (todos apenas pelo rename
`client-logo-card` → `logo-card`, sem mudança de comportamento).

Removido: `assets/css/client-logo-card.css`.

Nesta tarefa de validação, nenhum arquivo de código foi criado ou modificado — apenas este
relatório.
