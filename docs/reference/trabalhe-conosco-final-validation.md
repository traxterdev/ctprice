# Validação final full-page — `/trabalhe-conosco/` + regressão de componentes compartilhados

Data: 2026-08-30
Referência: `https://ctprice.com.br/wp/trabalhe-conosco/`
Documentação-base: `docs/reference/trabalhe-conosco-audit.md`, `docs/reference/reference-baseline.md`,
`docs/reference/parcerias-final-validation.md`, `docs/reference/clientes-final-validation.md`

Escopo: validação definitiva de `/trabalhe-conosco/` (Hero + Vagas em aberto + faixa "Nossos
Benefícios" + grade de 14 benefícios) e regressão dos componentes compartilhados alterados na
implementação (`components/section-title-band.php` + `assets/css/section-title-band.css`).
Nenhum arquivo de código foi criado ou modificado nesta tarefa — apenas este relatório. Nenhum
commit foi feito.

---

## PARTE A — Full-page (5 viewports)

Ordem confirmada no DOM em 1440×900, 900×1200, 768×1024, 767×1024 e 390×844: topbar → header →
Hero → "Vagas em aberto" (`jobs-section`) → faixa "Nossos Benefícios" (`id="beneficios"`) → grade
de 14 benefícios (`benefits-grid-section`) → footer → bottom bar → WhatsApp flutuante → cookie
banner.

- **Nenhum container vazio**: `document.querySelectorAll('main > div').length === 0` — os 4
  containers Elementor sem conteúdo do original não existem na implementação; todo espaçamento é
  `padding`/`gap` de CSS.
- **Sem sobreposição/gaps acidentais**: em todos os 5 viewports, o `top` de cada seção é
  exatamente igual ao `bottom` da seção anterior (Hero→Vagas, Vagas→faixa, faixa→grade,
  grade→footer) — nenhuma folga nem sobreposição em nenhum par.
- **Footer corretamente posicionado**: `footerTop === ` bottom exato da grade de benefícios nos 5
  viewports (ex.: 2663px em 1440×900, 5899px em 390×844).
- **`scrollWidth === clientWidth`** confirmado nos 5 viewports: 1425/1425, 885/885, 753/753,
  752/752, 375/375 — nenhum overflow horizontal.
- Nenhuma tentativa foi feita de igualar a altura total à referência WordPress, conforme
  instruído — as melhorias conscientes (sem espaçadores vazios, cards profissionais, grid 3/2/1)
  mudam a altura por design.

**Resultado: PARTE A aprovada sem ressalvas.**

---

## PARTE B — Hero

| Propriedade | Confirmado |
|---|---|
| Componente | `components/boxed-hero.php`, **sem nenhuma alteração** (`git diff` vazio) |
| Altura | 400px nos 5 viewports |
| Imagem | `assets/images/pages/informacoes/informacoes.jpg` (reaproveitada, não duplicada) |
| `background-position` | `0% 0%` |
| `background-size` | `cover` |
| Eyebrow | "trabalhe conosco" |
| Título | "Veja as vagas Disponíveis" |

**Resultado: PARTE B aprovada sem ressalvas.**

---

## PARTE C — Vagas

Conteúdo renderizado comparado item a item contra `trabalhe-conosco-audit.md` §3.2: as 3 vagas
(Analista Contábil, Analista de Departamento Pessoal, Analista Fiscal), na ordem correta, com
todos os pré-requisitos e diferenciais **idênticos** — nenhum texto omitido, duplicado, reescrito
ou trocado de vaga. Único formato alterado: o marcador ASCII "->" virou item de lista real
(`<li>`), sem alteração de nenhuma palavra.

- **`config/jobs.php` confirmado como única fonte**: `grep` em `components/jobs-section.php` e
  `trabalhe-conosco/index.php` não encontra nenhum texto de vaga hardcoded (só os rótulos
  estruturais "Pré-requisitos"/"Diferencial", que são labels de seção, não conteúdo de vaga).
- **Card**: fundo branco (`rgb(255,255,255)`), borda `1px solid rgba(0,34,44,0.12)`,
  `border-radius:12px`, sombra `0 2px 6px rgba(0,34,44,0.06)`, padding 28px — confirmado via
  `getComputedStyle`.
- **Alturas não forçadas iguais**: 533px / 516px / 466px nos 3 cards (1440×900) — o card de
  Analista Fiscal (menos conteúdo) fica visivelmente mais baixo, sem espaço vazio artificial,
  conforme instruído.

### Responsividade
| Viewport | Colunas |
|---|---|
| 1440×900 | 3 |
| 900×1200 | 2 |
| 768×1024 | 2 |
| **767×1024** | **1** (confirmado exatamente no breakpoint) |
| 390×844 | 1 |

**Resultado: PARTE C aprovada sem ressalvas.**

---

## PARTE D — CTAs das vagas

| Vaga | Texto visível | Nome acessível | `href` | `target` | `rel` |
|---|---|---|---|---|---|
| Analista Contábil | "Candidatar-se" | "Candidatar-se para a vaga de Analista Contábil — abre o sistema de recrutamento em uma nova guia" | `https://recrutamento.ctprice.com.br/vagas` | `_blank` | `noopener noreferrer` |
| Analista de Departamento Pessoal | "Candidatar-se" | idem, com o nome da vaga correspondente | idem | `_blank` | `noopener noreferrer` |
| Analista Fiscal | "Candidatar-se" | idem | idem | `_blank` | `noopener noreferrer` |

- **URL confirmada vinda exclusivamente de `config/company.php['sistemas_externos']['recrutamento']`**
  (`trabalhe-conosco/index.php:28`) — `grep` confirma que `config/jobs.php` e
  `components/jobs-section.php` nunca contêm a URL literal, apenas a recebem via `apply_url`.
- **Clique real confirmado**: o primeiro CTA foi clicado (Chrome DevTools MCP) e abriu uma nova
  aba em `https://recrutamento.ctprice.com.br/vagas`, título "CT Price - Gestão de Currículos",
  conteúdo carregado normalmente — o portal responde corretamente. A aba foi fechada sem
  preencher nem enviar nenhuma candidatura.

**Resultado: PARTE D aprovada sem ressalvas.**

---

## PARTE E — Benefícios

- **14 registros / 14 cards** confirmados (`document.querySelectorAll('.benefit-card').length === 14`).
- **14 `src` únicos**, exatamente `ben01.png` a `ben14.png`, na ordem correta — nenhum duplicado,
  nenhum perdido.
- `object-fit: contain` confirmado (herdado de `.logo-card__img`); todas as proporções
  preservadas exatamente (ex.: `ben03.png` 768×512 → exibido 267×178, mesma razão 1,5; `ben14.png`
  372×497 → exibido 133×178, mesma razão ≈0,75) — nenhuma distorção.
- **Nenhuma imagem ampliada além do tamanho original**: todas as dimensões exibidas são ≤ às
  dimensões naturais (redução, nunca upscale) — nenhuma arte "borrada" por ampliação excessiva.
- **Nenhum corte**: `object-fit:contain` garante a imagem inteira sempre visível dentro do card.
- Card: padding 20px, borda `1px solid rgba(0,34,44,0.1)`, `border-radius:12px`, sombra
  `0 2px 6px rgba(0,34,44,0.06)` — idêntico à identidade `.logo-card` já aprovada.
- **Hover confirmado via hover real (CDP)**: `translateY(-4px)`, sombra
  `0 10px 20px rgba(0,34,44,0.12)`, borda `rgba(5,112,56,0.35)` — mesmo padrão institucional já
  usado em Home/Sobre Nós/Clientes/Parceiros.
- **Legibilidade das artes com texto**: verificada visualmente em desktop (1440), tablet (900) e
  mobile (390) — textos como "Indicação de Empresas", "Premiação Desempenho", "Programa
  Desenvolvimento ao Colaborador" e "Ginástica Laboral" permanecem nitidamente legíveis em todos
  os viewports (no mobile o card ocupa a largura toda, tornando a arte proporcionalmente maior,
  não menor). **Nenhum ajuste local foi necessário** — nenhuma arte ficou pequena demais.

**Resultado: PARTE E aprovada sem ressalvas.**

---

## PARTE F — Última linha

- **3/3/3/3/2 confirmado** em desktop via agrupamento por `top` de cada card (4 linhas com `top`
  distintos contendo 3 cards cada, 1 linha com 2).
- **Centralização confirmada matematicamente**: centro do grupo dos 2 últimos cards = centro do
  container, diferença de **0px**.
- **Nenhum seletor `nth-child`/`nth-of-type`** encontrado em `assets/css/benefits-grid-section.css`
  (`grep` confirma) — a centralização vem inteiramente de `display:flex; flex-wrap:wrap;
  justify-content:center`, uma propriedade estrutural do layout, não uma regra amarrada à
  posição/quantidade atual de itens. Solução mantida como está — já é a alternativa mais limpa.

**Resultado: PARTE F aprovada sem ressalvas.**

---

## PARTE G — Âncora

- Navegação direta para `http://127.0.0.1:8099/trabalhe-conosco/#beneficios` carrega a página e
  posiciona o topo da seção exatamente no topo da viewport (`anchorTop === 0` após o scroll
  automático do navegador) — nenhum erro de console.
- A âncora é o próprio `<section id="beneficios" class="section-title-band">` (elemento
  semântico real), não um `<div>` vazio como no original.
- Header confirmado `position: relative` (não fixo) — sem sobreposição da âncora.
- **`config/menu.php`**: já apontava corretamente para `/trabalhe-conosco/#beneficios` tanto no
  submenu principal (`$primary[5]['children'][1]['url']`) quanto no footer
  (`$footer[7]['url']`, que reaproveita o mesmo valor) — confirmado via `grep`, **nenhuma
  alteração foi necessária** (a correção já havia sido aplicada na etapa de auditoria/config,
  antes desta implementação).

**Resultado: PARTE G aprovada sem ressalvas.**

---

## PARTE H — Revisão de `section-title-band.php`

Revisão do componente evoluído (props: `title`, `font_size`, `font_weight`, `height`,
`container_max_width`, `gradient_stops`, `id`):

| Critério | Resultado |
|---|---|
| Defaults seguros | ✅ `font_size=40`, `font_weight=800`, `height=180`, `container_max_width=1200`, `gradient_stops=['0%','100%']` — todos idênticos aos valores medidos em `/parcerias/` |
| `/parcerias/` sem precisar passar os novos parâmetros | ✅ confirmado — `parcerias/index.php` só passa `title`/`font_size`, comportamento idêntico ao anterior |
| Sem condicionais específicas de página | ✅ nenhum `if` relacionado a "trabalhe-conosco" ou qualquer slug no componente — tudo puramente orientado a dados |
| Não virou mini page builder | ✅ todos os 7 parâmetros representam propriedades reais e já medidas do mesmo padrão visual (texto, tamanho/peso de fonte, altura, largura do container, gradiente, âncora) — nenhum knob arbitrário de CSS livre, nenhuma composição de layout genérica |
| Simplificação possível sem perder reuso legítimo | Nenhuma identificada — cada parâmetro corresponde a uma diferença medida e documentada entre as duas instâncias reais (`/parcerias/` × `/trabalhe-conosco/`); removê-lo obrigaria hardcodar um valor ou estimar em vez de medir, contrariando a diretriz do projeto |

**Nenhuma refatoração foi feita** — a implementação já estava clara, pequena e com defaults
seguros; revisão não encontrou motivo para alteração.

**Resultado: PARTE H aprovada — evolução do componente mantida como está.**

---

## PARTE I — Regressão de `/parcerias/`

| Item | Antes (baseline) | Agora | Igual? |
|---|---|---|---|
| Altura das 2 faixas | 180px | 180px | ✅ |
| Container | 1200px | 1200px | ✅ |
| Gradiente | `linear-gradient(rgb(0,34,44) 0%, rgb(5,112,56) 100%)` | idêntico | ✅ |
| `font-weight` | 800 | 800 | ✅ |
| `font-size` (Ferramentas / Parceiros) | 40px / 50px | 40px / 50px | ✅ |
| Texto das 2 faixas | "Ferramentas WEB para os Clientes CT Price" / "Parceiros" | idêntico | ✅ |
| Overflow horizontal | nenhum | nenhum (1425/1425) | ✅ |
| Console | 0 mensagens de erro | 0 mensagens | ✅ |

**Nenhuma mudança visual detectada — regressão rápida aprovada sem ressalvas.**

---

## PARTE J — `logo-card`

- `git diff assets/css/logo-card.css` **vazio** — o arquivo não foi tocado nesta implementação
  (confirmado também via `git log`, última alteração pertence a uma etapa anterior, de
  Parceiros).
- `assets/css/benefits-grid-section.css` **não contém nenhuma regra `.logo-card`** (`grep`
  confirma) — define apenas `.benefits-grid-section*`/`.benefit-card` (uma classe adicional
  aplicada junto de `.logo-card` no mesmo elemento, não uma sobrescrita global).
- **Isolamento confirmado.**
- Regressão rápida de `/clientes/` executada mesmo assim: 82 logos, card-base `height:150px`,
  `padding:24px`, borda/`border-radius` idênticos aos já validados em
  `clientes-final-validation.md` — nenhuma regressão. Console limpo.

**Resultado: PARTE J aprovada sem ressalvas.**

---

## PARTE K — Acessibilidade

- **Hierarquia de headings coerente**: H2 (eyebrow Hero) → H2 (título Hero) → H3 (cada título de
  vaga) → H4 (Pré-requisitos) → H4 (Diferencial) → ... → H2 ("Nossos Benefícios") — sem saltos de
  nível.
- Listas de requisitos/diferenciais são `<ul><li>` reais (confirmado via árvore de acessibilidade
  — cada item aparece como `StaticText` dentro da estrutura de lista, lido corretamente).
- **CTAs acessíveis**: nome acessível de cada botão identifica a vaga específica e avisa sobre a
  nova guia, mesmo fora do contexto visual do card.
- **Foco visível confirmado** via `Tab` real (não só `.focus()` programático): outline
  `2px solid var(--color-dark-teal)` visível no segundo CTA após a navegação por teclado.
- **14/14 imagens de benefício com `alt` não vazio** — nenhum `alt=""`. Revisão de cada um
  confirma que descreve o benefício em si (ex.: "Plano de saúde Hapvida", "Programa de
  desenvolvimento ao colaborador", "Código de vestimenta (dress code)"), não apenas o nome do
  arquivo (`ben01.png` etc.) nem o nome de marca sozinho quando o benefício é mais que um logo
  (ex.: "Comemoração de aniversário dos colaboradores" para a arte "Happy Birthday").
- **Âncora sem ARIA desnecessária**: `#beneficios` não tem `role`, `aria-label` nem `tabindex` —
  é um `<section>` semântico simples.

**Resultado: PARTE K aprovada sem ressalvas.**

---

## PARTE L — Assets

- **14 novos PNGs** em `assets/images/pages/trabalhe-conosco/beneficios/` — confirmado
  (`ben01.png`…`ben14.png`, nenhum a mais nem a menos).
- **Nenhum 404**: todas as imagens (Hero + 14 benefícios) retornaram HTTP 200.
- **Nenhum arquivo quebrado**: `naturalWidth > 0` em todas as 14.
- **Nenhum asset do popup antigo**: `LogoSecundariaColorida02.png` confirmado ausente do projeto
  (busca por nome de arquivo não encontra nenhuma cópia).
- **Hero reutilizado sem duplicação**: `informacoes.jpg` existe em uma única localização
  (`assets/images/pages/informacoes/`), reaproveitada por `/informacoes/`, `/parcerias/` e agora
  `/trabalhe-conosco/` — nenhuma cópia adicional foi criada.

**Resultado: PARTE L aprovada sem ressalvas.**

---

## PARTE M — Console e rede

- **Console**: nenhum erro JavaScript próprio em nenhum dos 5 viewports (a única mensagem
  observada, `[debug] Search endpoint requested!`, é ruído de extensão de navegador já
  documentado em validações anteriores, não originado pelo código do projeto).
- **Rede**: nenhum 404 em nenhum asset próprio (CSS, JS, imagens, fontes) — confirmado em
  1440×900 e reconfirmado nas passagens de PARTE A/E nos demais viewports.
- **Fontes**: `roboto-flex-400.woff2`/`roboto-variable.woff2` carregadas localmente, sem Google
  Fonts remoto para o conteúdo do próprio site.
- **Dependências legadas**: `wp-content`/`wp-includes`/`elementor` ausentes do HTML renderizado;
  `window.jQuery === undefined`.
- **Nenhuma biblioteca nova**: apenas `header.js`/`cookie-banner.js` (globais, já usados em todas
  as páginas) — esta página não usa Swiper (sem carrossel).
- Requisições a `gc.kis.v2.scr.kaspersky-labs.com` e ao Google Maps do footer são externas ao
  projeto (antivírus local / mapa incorporado), já documentadas em validações anteriores.

**Resultado: PARTE M aprovada sem ressalvas.**

---

## Correções realizadas nesta validação

**Nenhuma.** Todos os itens auditados (PARTES A–M) passaram nas verificações sem exigir ajuste de
código — a implementação já entregue está validada como está.

---

## Diferenças conscientes reconfirmadas (não regressões)

- Remoção dos 4 containers vazios do Elementor (espaçamento por CSS).
- Cards profissionais para as 3 vagas, com listas semânticas reais.
- CTA "Candidatar-se" (substitui "Clique aqui" do original).
- Candidatura direcionada ao sistema oficial externo de recrutamento — popup Elementor de
  cadastro de currículo (10 campos + upload) não reproduzido.
- Benefícios apresentados em cards com identidade visual já aprovada (`.logo-card`), em vez de
  imagens soltas e desalinhadas.
- `object-fit:contain`, grid 3/2/1, `alt` útil em todas as 14 imagens.
- Última linha de benefícios centralizada via flexbox (sem seletor frágil).
- Ausência de CMS/banco/painel administrativo nesta fase — dados estáticos em
  `config/jobs.php`/`config/benefits.php`, decisão de escopo já registrada.

---

## Pendências / decisões futuras (não bloqueantes)

- **Vagas e benefícios são candidatos naturais a um futuro CMS**: ambos são conteúdo genuinamente
  próprio desta página (não reaproveitado de outra, ao contrário de `/informacoes/`), com
  frequência de mudança plausível (vagas mudam com regularidade; benefícios raramente) — quando a
  fase de CMS/manutenção dinâmica for iniciada (após a conclusão do site público, conforme
  decisão global já registrada), `config/jobs.php`/`config/benefits.php` são os candidatos óbvios
  a migrar para uma fonte de dados dinâmica.
- **URLs individuais por vaga**: hoje as 3 vagas compartilham o mesmo destino de candidatura
  (`config/company.php['sistemas_externos']['recrutamento']`), pois o sistema externo não expõe
  (ou não foi confirmado que expõe) uma URL própria por vaga. Quando/se o sistema oficial de
  recrutamento passar a oferecer destinos individuais por vaga, `config/jobs.php` pode ganhar um
  campo `apply_url` próprio por item, sobrescrevendo o destino padrão — não implementado agora por
  não haver essa informação disponível, para não inventar URLs.
- Pendências já conhecidas e não específicas desta página (WhatsApp não-canônico na topbar,
  bairro/CEP pendentes em `config/company.php`, botão "Área Restrita" mantido por decisão de
  baseline) continuam registradas em seus documentos de origem — não repetidas aqui como
  novidade.
