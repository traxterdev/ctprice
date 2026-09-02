# Validação final — `/arearestrita/`

Data: 2026-09-02
Documentação-base: `docs/reference/arearestrita-audit.md`, `docs/reference/reference-baseline.md`,
`docs/reference/site-inventory.md`

Escopo: validação final visual/estrutural/funcional da implementação de `/arearestrita/`,
executada ao vivo via Chrome DevTools MCP nos 5 viewports obrigatórios, com testes reais de
config, teclado/foco, rede e segurança de saída — nenhum resultado foi inferido ou simulado.

---

## 1. Full-page — estrutura e métricas

Ordem confirmada nos 5 viewports (`main > *`: `restricted-area-intro`, `restricted-access-section`
— exatamente 2 filhos, sem nenhum Hero): topbar → header → faixa de título → grade de 2 acessos →
footer → bottom bar → WhatsApp flutuante → cookie banner.

| Viewport | `scrollWidth`/`clientWidth` | Colunas | Largura do card |
|---|---|---|---|
| 1440×900 | 1440/1440 | 2 | 555px |
| 900×1200 | 900/900 | 2 | 425px |
| 768×1024 | 753/753 | 2 | 352px |
| 767×1024 | 752/752 | **1** (breakpoint exato) | 732px |
| 390×844 | 390/390 | 1 | 370px |

Nenhum overflow horizontal em nenhum viewport. Nenhum gap inesperado, nenhuma sobreposição — os 2
cards em 1440px têm exatamente a mesma altura (755px), balanceados pelo mesmo conteúdo/estrutura.

**Resultado: aprovado sem ressalvas.**

---

## 2. Faixa de título

Confirmado via inspeção computada: fundo branco (`--color-white`), container `max-width:1140px`,
título "Área Restrita" (Poppins, 700, `--color-dark-teal`), subtítulo (16px, `--color-text-gray`),
ambos centralizados, comportamento estável em todos os viewports (sem quebra ruim, sem overflow).

Confirmado que a faixa **continua inline** em `arearestrita/index.php` (não virou um componente
separado) — revisão do arquivo mostra o bloco como 2 tags simples (`<h2>`/`<p>`) dentro de uma
`<section class="restricted-area-intro">`, com o comentário de decisão já presente no topo do
arquivo. Nenhum componente foi criado só para isso.

**Resultado: aprovado sem ressalvas.**

---

## 3. Dados estáticos (`config/restricted-area.php`)

Revisado diretamente: **exatamente 2 registros** (`clientes`, `colaboradores`), cada um só com os
5 campos necessários (`key`, `title`, `description`, `image`, `url_key`) — nenhum campo extra,
nenhuma URL real embutida. `url_key` de cada item (`area_restrita_clientes`,
`area_restrita_colaboradores`) confirmado batendo exatamente com as chaves existentes em
`config/company.php['sistemas_externos']` (`grep` cruzado nos dois arquivos). Nenhuma referência
aos caminhos quebrados originais (`documentos`/`sh-admin`) em nenhum lugar do arquivo.

**Resultado: aprovado sem ressalvas.**

---

## 4. Configuração dos destinos — estado final do repositório

```
config/company.php:124:        'area_restrita_clientes' => null,
config/company.php:128:        'area_restrita_colaboradores' => null,
```

- `git diff config/company.php` → **vazio**.
- `git status --short config/company.php` → **sem entrada** (arquivo não aparece como modificado).
- Nenhum valor temporário usado nos testes desta validação (seções 6/7 abaixo) permaneceu salvo.

**Resultado: aprovado sem ressalvas.**

---

## 5. Estado indisponível — teste real (config nulo, estado padrão do repositório)

Verificado via inspeção de DOM real (não apenas leitura do código-fonte):

| Verificação | Resultado |
|---|---|
| `<a>` de acesso dentro da grade | **0** (`section.querySelectorAll('a').length === 0`) |
| Elemento focável dentro da grade | **0** (`[tabindex], a, button, input, select, textarea` → 0 elementos) |
| `href="#"` | Ausente na seção |
| `javascript:void` | Ausente na seção |
| Cursor do card | `default` (não `pointer`) nos 2 cards |
| Texto exibido | `"Acesso temporariamente indisponível"` — exato, nos 2 cards |
| `tabindex` no `<span>` de estado | Ausente (`null`) — fora da ordem de tabulação |
| Teste real de `Tab` | Focando o botão "Área Restrita" do header e pressionando `Tab`, o foco pula direto para o próximo elemento focável do DOM **fora** de `.restricted-access-section` — confirmado via `element.closest('.restricted-access-section') === null` |

Visualmente (screenshots desktop/tablet/mobile): o texto "Acesso temporariamente indisponível"
aparece como uma pílula neutra (fundo `--color-off-white`, borda sutil, mesma altura/formato do
botão "Acessar" que aparece no estado disponível) — não parece um erro técnico, não menciona
404/`/documentos`/`/sh-admin`/servidor, é coerente com a identidade visual do site.

**Resultado: aprovado sem ressalvas.**

---

## 6. Estado disponível — teste temporário controlado (não persistente)

Executado em 2 rodadas, cada uma com `config/company.php` restaurado ao original entre uma e
outra (backup local, nunca commitado):

**Rodada 1 — só Clientes com URL de teste:**

| Item | Resultado |
|---|---|
| Card Clientes — `<a>` presente | Sim |
| `href` | `https://portal-clientes.ctprice-test.internal/login` (valor de teste, exato) |
| Texto do CTA | "Acessar" |
| `target` | Ausente (mesma aba, confirmado) |
| `rel` | Ausente (correto — sem `target="_blank"` não é necessário) |
| `aria-label` | `"Acessar área restrita — Clientes"` — identifica o destino |
| Foco visível | Confirmado — `link.matches(':focus-visible') === true`, `background-color` muda para `rgb(0,34,44)` (mesmo comportamento já aprovado de `.btn--pill-outline:focus-visible`) |
| Card Colaboradores | **Continua indisponível** — 0 `<a>`, span "Acesso temporariamente indisponível" inalterado |

**Rodada 2 — invertido (só Colaboradores com URL de teste, Clientes de volta a `null`):**

| Item | Resultado |
|---|---|
| Card Colaboradores — `<a>` presente | Sim, `href` = valor de teste, `aria-label` = `"Acessar área restrita — Colaboradores"` |
| Card Clientes | Voltou a indisponível corretamente |

Screenshot de conferência visual (estado misto — Clientes disponível/focado, Colaboradores
indisponível): os 2 cards permanecem estruturalmente idênticos (mesma altura, mesmo layout, mesma
posição do CTA) — a única diferença visual é o conteúdo da pílula final (botão preenchido escuro
vs. pílula neutra), sem nenhum desalinhamento ou "salto" entre os 2 estados.

**Ao final: revertido integralmente** — `git diff config/company.php` e `git status --short
config/company.php` confirmados vazios após a reversão (ver seção 4).

**Resultado: aprovado sem ressalvas — comportamento futuro confirmado, sem nenhum valor de teste remanescente.**

---

## 7. Segurança dos URLs futuros

Caminho revisado ponta a ponta: `config/company.php['sistemas_externos'][...]` → resolvido em
`arearestrita/index.php` (`$item['url'] = $company['sistemas_externos'][$item['url_key']] ??
null;`) → passado ao componente → `htmlspecialchars($url, ENT_QUOTES, 'UTF-8')` na saída do
atributo `href`.

**Teste real com valor malformado/controlado** (string contendo `" onmouseover="alert(1)`
temporariamente em `area_restrita_colaboradores`, revertida ao final):

- HTML gerado: `href="https://example.test/x&quot; onmouseover=&quot;alert(1)"` — as aspas foram
  escapadas para `&quot;`, o valor inteiro permaneceu como texto literal dentro do atributo
  `href`.
- Confirmado via DOM: `link.hasAttribute('onmouseover') === false`, `link.attributes.length === 3`
  (só `class`, `href`, `aria-label`) — **nenhum atributo novo foi injetado**, nenhum JavaScript
  executou.
- `null`/string vazia → confirmado que resultam no estado indisponível (`is_string($url) &&
  $url !== ''` no componente).

Nenhum sistema de sanitização adicional foi criado — `htmlspecialchars` na saída já é suficiente
e é o mesmo padrão já usado em todo o restante do projeto.

**Resultado: aprovado sem ressalvas.**

---

## 8. Cards — validação visual

Confirmado nos 3 screenshots de implementação (desktop/tablet/mobile) e nas rodadas de teste
temporário (seção 6): imagem 4:3 com overlay institucional (verde-petróleo em Clientes,
verde-marca em Colaboradores — mesmas 2 cores auditadas no verso dos cards do Elementor original),
título colorido por card, descrição sempre visível como texto real, estado/CTA no rodapé do card,
borda sutil (`rgba(0,34,44,0.1)`), radius 12px, sombra leve (`0 2px 6px rgba(0,34,44,0.06)`),
padding consistente, alinhamento à esquerda no corpo do card, alturas equilibradas entre os 2
cards em todos os viewports testados. Confirmado que a estrutura dos 2 cards permanece **idêntica**
entre estado disponível e indisponível — só o conteúdo final da pílula muda, sem alteração de
layout, altura ou alinhamento.

**Resultado: aprovado sem ressalvas.**

---

## 9. Imagens

| Arquivo | Dimensões | Formato | Integridade |
|---|---|---|---|
| `clientes.jpg` | 2000×2000 | JPEG válido (`getimagesize`) | OK, 145.933 bytes |
| `colaboradores.jpg` | 2000×2000 | JPEG válido (`getimagesize`) | OK, 576.519 bytes |

Renderizadas com `object-fit:cover` dentro de uma caixa `aspect-ratio:4/3` — sem distorção em
nenhum viewport testado. Nenhum 404 (confirmado via rede: `reqid=925`/`926`, ambos `[200]`).
`alt` real e descritivo em ambas (`"Ilustração da área restrita — Clientes"`/`"...Colaboradores"`)
— e, mais importante, **nenhuma informação essencial depende exclusivamente da imagem**: nome do
acesso (`<h3>`) e descrição (`<p>`) já são texto real, confirmado também na árvore de
acessibilidade (heading level 3 + `StaticText` para cada card).

**Resultado: aprovado sem ressalvas.**

---

## 10. "COLABORADORES" — revalidação específica

| Viewport | `scrollWidth` do título | `clientWidth` do título | Cortado? |
|---|---|---|---|
| 1440×900 | 170px | 170px | Não |
| 900×1200 | 170px | 170px | Não |
| 768×1024 | 170px | 170px | Não |
| 767×1024 | 170px | 170px | Não |
| 390×844 | 170px | 170px | Não |

Texto completo e idêntico em **todos** os 5 viewports (o `font-size: clamp(22px, 2.4vw, 26px)` já
comporta a palavra inteira mesmo no menor breakpoint, sem precisar reduzir para um tamanho
artificialmente pequeno) — nenhum clipping, nenhum overflow, nenhum `overflow:hidden` mascarando
problema (a regra de segurança `overflow-wrap:break-word` nunca precisou entrar em ação, porque a
tipografia responsiva já resolve sozinha). Confirmado visualmente nos 3 screenshots de
implementação.

**Resultado: corrigido e revalidado — aprovado sem ressalvas.**

---

## 11. Responsividade

Confirmado: 1440→2, 900→2, 768→2, 767→1 (breakpoint exato), 390→1. Gap de 30px entre cards em
todas as larguras com 2 colunas. Imagens sem distorção em nenhum viewport. Textos (título 3,
descrição, estado) legíveis em todos. Footer sem regressão em nenhum viewport (mesma estrutura já
aprovada globalmente).

**Resultado: aprovado sem ressalvas.**

---

## 12. Acessibilidade

Confirmado via árvore de acessibilidade real do navegador:

- Heading real: `heading "Área Restrita" level=2`.
- Região com nome: `region "Área Restrita"` (`aria-labelledby` da seção de cards apontando para o
  heading da faixa de título).
- Título de cada card: `heading "Clientes"/"Colaboradores" level=3`.
- Descrição: `StaticText` real (não depende de imagem nem de hover).
- `alt` das imagens: presente e descritivo nas 2.
- **Zero `tabindex="0"` desnecessário** — nenhum container da grade é artificialmente focável.
- Estado indisponível fora da tabulação: confirmado (`tabindex` ausente, teste real de `Tab`
  pulando a seção inteira).
- Link disponível (teste temporário, seção 6) é `<a>` verdadeiro, com `aria-label` que identifica
  o destino ("Acessar área restrita — Clientes"/"...Colaboradores").
- Foco visível: confirmado (`:focus-visible` ativo, mudança de `background-color` real).
- **Com os 2 configs nulos (estado atual do repositório), a grade possui ZERO controles
  interativos próprios** — confirmado tanto por contagem de elementos focáveis quanto pela árvore
  de acessibilidade (nenhum `link`/`button` dentro da `region "Área Restrita"`).

**Resultado: aprovado sem ressalvas.**

---

## 13. Ausência dos destinos quebrados

Grep no HTML renderizado (estado final, configs nulos): **zero** ocorrências de `documentos`,
`sh-admin`, e zero URLs de teste (`ctprice-test.internal`, `example.test`) remanescentes.

Um `href="#"` **existe** na página, mas pertence ao submenu global "Vagas" (dentro de "Trabalhe
Conosco" no header) — **não é da Área Restrita**, é uma funcionalidade já conhecida e catalogada
em `docs/reference/site-inventory.md` (o link do menu principal para o sistema de recrutamento
externo, comportamento herdado do site atual, fora do escopo desta página). Registrado aqui
separadamente para não ser atribuído por engano a esta implementação.

**Resultado: aprovado sem ressalvas.**

---

## 14. Header global / `DRIFT-001`

Confirmado via árvore de acessibilidade: `link "Área Restrita" url=".../arearestrita/"` presente
no header, no mesmo lugar/estilo já aprovado (`.btn.btn--pill-outline`). `includes/header.php` e
`config/menu.php` **sem diff** (`git status`/`git diff` vazios para os 2 arquivos) — nenhuma
alteração foi feita neles nesta implementação. Nenhuma decisão sobre `DRIFT-001` foi tocada; o
botão continua mantido conforme o baseline congelado (`docs/reference/reference-baseline.md`,
seção 4).

**Resultado: aprovado sem ressalvas.**

---

## 15. Console e rede

- **Console**: nenhum erro JavaScript próprio — única mensagem observada é o mesmo ruído de
  extensão de navegador já documentado em validações anteriores deste projeto
  (`[debug] Search endpoint requested!`).
- **Rede**: todos os assets próprios em `200` (CSS `restricted-access-section.css`, as 2 imagens
  novas, fontes, ícones, JS globais) — nenhum 404, nenhuma chamada às URLs de teste temporárias,
  nenhuma chamada a `/documentos`/`/sh-admin`.
- **Dependências legadas**: `window.jQuery === undefined`; `wp-content`/`elementor` ausentes do
  HTML renderizado; nenhuma biblioteca nova carregada (só `header.js`/`cookie-banner.js`, já
  globais).

**Resultado: aprovado sem ressalvas.**

---

## 16. Decisão arquitetural registrada

> Os acessos permanecem visualmente disponíveis como opções da Área Restrita, porém sem navegação
> enquanto os respectivos destinos em `config/company.php` forem `null`. A ativação futura exige
> apenas o preenchimento dos URLs confirmados pela CT Price, sem alteração do componente ou da
> página.

Confirmada e validada nesta sessão: o teste temporário controlado (seção 6) provou exatamente esse
comportamento — preencher a chave em `config/company.php` é suficiente, em ambas as direções
(Clientes/Colaboradores, juntos ou separados), sem tocar em `components/restricted-access-section.php`,
`config/restricted-area.php` ou `arearestrita/index.php`.

---

## Diferenças conscientes (consolidado)

- Faixa simples inline em vez de Hero (`boxed-hero.php`/`internal-hero.php` não usados).
- Cards estáticos em vez de Elementor Flip Box — conteúdo sempre visível, sem 3D/hover
  obrigatório.
- Tab-stops mortos removidos — zero `tabindex="0"` artificial.
- Área de interação consistente entre os 2 cards (nunca um card inteiro clicável e outro só um
  botão, como no original).
- Corte de "Colaboradores" corrigido via tipografia responsiva (`clamp()`), não redução drástica.
- Responsividade 2/1 (não 3/2/1 — a página só tem 2 itens).
- Destinos quebrados do WordPress (`/documentos`, `/sh-admin`) **não reproduzidos**.
- Estado "temporariamente indisponível" orientado 100% por `config/company.php`, sem link falso.
- Nenhum login próprio criado — continua um hub de redirecionamento para sistemas externos.

---

## Pendências da CT Price (não bloqueantes para a conclusão técnica desta página)

1. URL correta da Área Restrita de Clientes.
2. URL correta da Área Restrita de Colaboradores.
3. Confirmação se algum dos 2 sistemas foi definitivamente descontinuado.
4. Se algum card deve ser removido.
5. Se os textos devem ser revisados.
6. Decisão futura sobre a permanência do botão "Área Restrita" no header (`DRIFT-001`).

Nenhuma resposta foi presumida ou inventada — a página está tecnicamente completa e correta
independentemente de quando essas respostas chegarem.

---

## Screenshots da implementação

- `docs/reference/screenshots/arearestrita-implementation-desktop-1440-full.png`
- `docs/reference/screenshots/arearestrita-implementation-tablet-900-full.png`
- `docs/reference/screenshots/arearestrita-implementation-mobile-390-full.png`

(As screenshots da referência original, em `arearestrita-desktop-1440-full.png` etc., não foram
sobrescritas.)
