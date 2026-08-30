# Validação final consolidada — `/clientes/` + regressão Home e Sobre Nós

Data: 2026-08-30
Documentação-base: `docs/reference/clientes-audit.md`, `docs/reference/reference-baseline.md`,
`docs/reference/home-final-validation.md`, `docs/reference/sobre-nos-final-validation.md`

Escopo: validação definitiva da nova página `/clientes/` (Hero + grade premium de logos) e da
regressão nas duas páginas que compartilham o componente de carrossel (`/` e `/sobre-nos/`),
antes do checkpoint Git. Nenhum commit foi feito nesta tarefa.

---

## 1. Estrutura final de `/clientes/`

Ordem confirmada no DOM (1440×900, 900×1200, 390×844): topbar → header → Hero Clientes → grade
premium de logos → footer → bottom bar → WhatsApp flutuante → cookie banner. Nenhum componente
global foi alterado.

### 1.1 Hero (`components/boxed-hero.php`)

| Propriedade | Medido |
|---|---|
| Altura | 400px (idêntico nos 3 viewports) |
| Imagem de fundo | `assets/images/pages/clientes/clientes.jpg` |
| `background-size` | `cover` |
| `background-position` | `50% 50%` |
| Container | `.boxed-hero__inner`, `max-width:1140px` |
| Tipografia | eyebrow Roboto 700 20px/20px `#00222C`; título Roboto 700 30px/30px `#057038` |
| Alinhamento | esquerda (`text-align:left` / `start`) |
| Overflow | nenhum (`scrollWidth === clientWidth` nos 3 viewports) |

### 1.2 Grade premium (`components/clients-grid-section.php` + `.client-logo-card`)

| Viewport | Colunas | Altura do card | Overflow |
|---|---|---|---|
| 1440×900 | 5 | 150px | Não |
| 900×1200 | 3 | 130px | Não |
| 390×844 | 2 | 110px | Não |

- **82 itens exatamente** confirmados (`itemCount === 82`), **82 arquivos únicos**
  (`uniqueCount === 82`) — nenhuma duplicação por renderização, nenhum item perdido.
- Card: fundo `#FFFFFF`, borda `1px solid rgba(0,34,44,0.1)`, `border-radius:12px`, sombra
  `0 2px 6px rgba(0,34,44,0.06)`, padding 24/18/14px por breakpoint — confirmado via
  `getComputedStyle`.
- Logos: `object-fit:contain`, `max-width/max-height:100%` — nenhum logo cortado, achatado ou
  esticado em nenhum breakpoint testado.
- Hover: `translateY(-4px)`, sombra `0 10px 20px rgba(0,34,44,0.12)`, borda
  `rgba(5,112,56,0.35)` — confirmado via hover real (CDP) e via `:focus-visible` (Tab real).

---

## 2. Diferença 82 × 106 (decisão já aprovada, não regressão)

O catálogo original em WordPress usa **106 logos** (galeria justificada, Elementor "Gallery").
Esta fase usa os **82 logos já centralizados em `config/clients.php`** (mesma fonte do carrossel
da Home/Sobre Nós) — os 72 logos exclusivos da página original (uploads 2025/2026, listados em
`clientes-audit.md` §3.3) não foram baixados nem reproduzidos. **Motivo**: o CMS que permitirá
gerenciar esse catálogo completo, e a expansão de conteúdo que ele viabiliza, ficaram adiados
para a fase de manutenção de conteúdo pós-conclusão do site. Registrado como diferença
temporária consciente, não como regressão.

---

## 3. Embaralhamento diário — revisão da implementação

Lógica em `components/clients-grid-section.php`:

```php
$today = date('Y-m-d');
$displayLogos = $clientLogos;
usort($displayLogos, function ($a, $b) use ($today) {
    $fileA = $a['file'] ?? '';
    $fileB = $b['file'] ?? '';
    $cmp = crc32($fileA . $today) <=> crc32($fileB . $today);
    return $cmp !== 0 ? $cmp : strcmp($fileA, $fileB);
});
```

Confirmado nesta validação:

- **Ordem estável no mesmo dia**: 3 requisições HTTP separadas (`curl`) ao vivo no mesmo dia
  retornaram exatamente a mesma sequência dos 82 arquivos.
- **Ordem muda de forma determinística em outro dia**: teste isolado em PHP (mesma função de
  ordenação, chamada com `date('Y-m-d')` de hoje e de amanhã) confirmou sequências diferentes
  para os dois dias, mas com o **mesmo conjunto de 82 arquivos** em ambos (nenhum item
  adicionado/removido, só reordenado).
- **Não modifica `config/clients.php`**: a função opera sobre uma cópia local
  (`$displayLogos = $clientLogos`), nunca sobre o array original.
- **Não influencia Home ou Sobre Nós**: `components/clients-carousel-section.php` (usado por
  essas duas páginas) consome `$clientLogos` diretamente, sem nenhum `sort`/`usort`/`shuffle` —
  confirmado por leitura de código e por reconfirmação em 3 reloads consecutivos da Home
  (sequência de arquivos idêntica nos três).
- **CORREÇÃO APLICADA — desempate estável**: a implementação anterior não tinha critério
  secundário para o caso (extremamente improvável, mas não impossível) de dois arquivos
  colidirem no mesmo `crc32` no mesmo dia. Embora o `usort` do PHP 8+ já seja estável por
  garantia da linguagem, foi adicionado um desempate explícito por `strcmp` do nome do arquivo,
  tornando a ordenação determinística por si só, sem depender dessa garantia específica de
  versão.

---

## 4. Lightbox e acessibilidade

Todas as interações testadas via Chrome DevTools MCP (clique real, eventos de teclado reais via
`press_key`, e disparo de evento para backdrop/Escape):

| Interação | Resultado |
|---|---|
| Clique abre o lightbox | ✅ |
| Fechar pelo X | ✅ |
| Fechar pelo backdrop (clique fora da imagem) | ✅ |
| Fechar com Escape | ✅ |
| Anterior / Próximo | ✅ (imagem muda corretamente) |
| Wraparound (duplo "anterior" a partir do primeiro item) | ✅ volta ao último dos 82 |
| Navegação por teclado (`ArrowLeft`/`ArrowRight`) | ✅ |
| Mobile/touch (390×844) | ✅ abre, controles permanecem dentro da viewport, sem overflow |

Acessibilidade:

- Cada logo é um `<button>` nativo — focável por teclado sem necessidade de `tabindex`
  (`tabIndex === 0` confirmado).
- **Foco visível confirmado via Tab real** (não só `.focus()` programático): `:focus-visible`
  aplica a mesma elevação/sombra/borda do hover (`matches(':focus-visible') === true` após Tab
  real).
- **Ativação por teclado confirmada**: `Enter` sobre um card focado abre o lightbox (comportamento
  nativo de `<button>`, sem JS adicional necessário).
- Botões do lightbox têm `aria-label` (“Fechar”, “Anterior”, “Próximo”).
- `<img>` do lightbox sempre recebe `alt` (herdado de `data-alt` de cada logo).

Nenhuma correção adicional de acessibilidade foi necessária além da já descrita na seção 3
(desempate do embaralhamento, que também melhora a robustez geral do componente).

---

## 5. Regressão da Home (`/`)

Validado em 1440×900, 1280×900, 900×1200 e 390×844.

| Item | Resultado |
|---|---|
| 82 logos únicos | ✅ (`uniqueCount === 82`) |
| Ordem original de `config/clients.php` | ✅ estável — reconfirmado em 3 reloads consecutivos (sequência de arquivos idêntica) |
| Slides visíveis | 6 (≥1024px) / 2 (tablet) / 1 (mobile) — confirmado nos 4 viewports |
| `spaceBetween` | 20px |
| Altura do card | 136px |
| Altura da seção | 200px (inalterada) em todos os viewports, incluindo 1280px |
| Logos reconhecíveis, `object-fit:contain` | ✅ sem corte/distorção |
| Autoplay / loop / swipe | ✅ `autoplay.running`, `loop`, `allowTouchMove` todos `true` |
| Hover | ✅ confirmado via hover real (CDP) num slide visível — elevação/sombra/borda |
| Overflow | Nenhum em nenhum dos 4 viewports |
| Outras seções da Home | Ordem/estrutura das seções (`hero-slider` → `welcome-section` → ... → `contact-section`) confirmada intacta; nenhuma verificação profunda refeita por não haver alteração relacionada |

Console: apenas o "issue" pré-existente de `autocomplete` (não relacionado a esta mudança).
Nenhum erro novo.

---

## 6. Regressão de `/sobre-nos/`

Validado em 1440×900, 900×1200 e 390×844.

- Nova UI do card confirmada aplicada (mesmo `.client-logo-card`, altura 136px, fundo/borda/
  radius/sombra idênticos ao da Home).
- Slides visíveis: 6 / 2 / 1, confirmado nos três viewports.
- Autoplay, loop e `allowTouchMove` confirmados `true`.
- **Integração confirmada**: seção do carrossel inicia exatamente onde termina a seção Dedicação
  (`y=2400.75` em 1440×900, sem gap) e o footer inicia exatamente onde termina o carrossel
  (`y=2600.75`) — sem alteração na composição geral da página.
- Nenhum overflow em nenhum dos três viewports.
- Console limpo (nenhuma mensagem) na verificação desta rodada.

**Nota sobre baseline anterior**: `docs/reference/sobre-nos-final-validation.md` não registra
"10 logos" como número esperado desta seção especificamente (a validação anterior tratava
sobretudo de altura/posição da seção, não da contagem de slides) — nenhuma atualização foi
necessária nesse documento. `docs/reference/home-final-validation.md`, que registrava
explicitamente "10 logos desktop", foi atualizado com uma nota pontual indicando a evolução para
6, sem reescrever o restante do documento (ver §7).

---

## 7. Atualização de documentação anterior

Duas menções pontuais e objetivamente desatualizadas em `docs/reference/home-final-validation.md`
(ambas descreviam "10 logos" como o número atual em desktop) receberam uma nota curta indicando a
revisão para 6, com link para este documento — sem reescrever o restante do arquivo. Nenhuma
outra alteração foi feita em documentação já existente.

---

## 8. CSS compartilhado (`assets/css/client-logo-card.css`)

Revisado e confirmado que contém **somente** responsabilidades visuais do card: fundo, borda,
`border-radius`, sombra, padding-base, alinhamento, `object-fit`, transição e hover/foco. **Não**
contém grid, número de colunas, Swiper, `slidesPerView`, lightbox, ordenação ou breakpoints de
layout — essas responsabilidades permanecem em `assets/css/clients-grid-section.css` (grid,
lightbox) e `assets/css/clients-carousel-section.css` (Swiper, dimensão compacta do card,
`.client-logo-card__img` com `width/height:100%` local ao carrossel). Home/Sobre Nós usam a
dimensão compacta (136px/18px) e Clientes usa a dimensão-base (150/130/110px conforme
breakpoint) — identidade visual compartilhada, dimensão própria por contexto, confirmado sem
duplicação de regras de identidade entre os três arquivos.

---

## 9. Console e rede — as três páginas

| Página | Erros JS próprios | 404 próprios | Fontes | Dependências novas |
|---|---|---|---|---|
| `/clientes/` | Nenhum | Nenhum | Locais | Nenhuma |
| `/` (Home) | Nenhum (apenas "issue" pré-existente de autocomplete) | Nenhum | Locais | Nenhuma |
| `/sobre-nos/` | Nenhum | Nenhum | Locais | Nenhuma |

Requisições a `gc.kis.v2.scr.kaspersky-labs.com` continuam sendo injetadas pelo antivírus local
(fora do site). O evento intermitente `ReferenceError: google is not defined` (script externo do
Google Maps Embed) permanece documentado separadamente em `sobre-nos-final-validation.md` §7.4 —
não reproduzido nesta rodada.

---

## 10. Correções realizadas nesta tarefa

1. **Desempate estável no embaralhamento diário** (`components/clients-grid-section.php`):
   adicionado `strcmp` do nome do arquivo como critério secundário de ordenação, para tornar o
   resultado determinístico por si só (ver §3).
2. **Documentação**: nota pontual em `docs/reference/home-final-validation.md` (duas menções a
   "10 logos"), sem reescrita do documento.

Nenhuma outra correção foi necessária — todos os demais itens verificados já estavam corretos.

---

## 11. Diferenças conscientes restantes (não regressões)

- 82 × 106 logos em `/clientes/` (§2).
- Grid premium em vez de galeria justificada em `/clientes/`.
- 6 logos em desktop no carrossel (Home e Sobre Nós), em vez dos 10 originais do WordPress —
  evolução visual aprovada.
- 3 logos 404 do carrossel original (`mv.jpg`, `modelo.jpg`, `logo_0020_Camada16.jpg`) continuam
  não reproduzidos.
- WhatsApp canônico unificado, bairro/CEP pendente, botão "Área Restrita", indicador de página
  ativa no menu — itens já registrados em validações anteriores, inalterados nesta tarefa.

## 12. Pendências não bloqueantes

- Miniatura corrompida (3×3px) e logo duplicado ("Morangos do Vale") identificados na auditoria
  original de 106 logos — não aplicável aos 82 atuais, só relevante quando o catálogo completo
  for migrado.
- Evento intermitente do Google Maps (§9), não determinístico, fora do nosso código.
