# Validação visual final — `/depoimentos/`

Data: 2026-09-02
Documentação-base: `docs/reference/depoimentos-audit.md`, `docs/reference/reference-baseline.md`

Escopo: validação final visual/interativa real da implementação de `/depoimentos/`, pendente da
sessão anterior (Chrome DevTools MCP ficou indisponível). Executada ao vivo, via Chrome DevTools
MCP, nos 5 viewports obrigatórios, com testes reais de clique, teclado e rede — nenhum resultado
foi inferido ou simulado.

**Um bug real foi encontrado e corrigido durante esta validação** (colisão de nome de variável
que zerava os dados institucionais do footer/WhatsApp global) — ver "Correções realizadas".

---

## 1. Composição full-page

Ordem confirmada nos 5 viewports: topbar → header → Hero → seção única "Quem confia, recomenda."
(heading + intro + grade de 7 cards) → footer → bottom bar → WhatsApp flutuante → cookie banner.

| Viewport | `scrollWidth`/`clientWidth` | Resultado |
|---|---|---|
| 1440×900 | 1425/1425 | Sem overflow |
| 900×1200 | 885/885 | Sem overflow |
| 768×1024 | 753/753 | Sem overflow |
| 767×1024 | 752/752 | Sem overflow |
| 390×844 | 375/375 | Sem overflow |

Medições em 1440×900: container 1140px; heading "Quem confia, recomenda." em Y=648; grade inicia
em Y=881; gap de 30px entre linhas de cards (confirmado: 881+437=1318, próxima linha em 1348 —
30px; 1348+461=1809, linha seguinte em 1840 — 31px, mesma tolerância de arredondamento); footer
inicia exatamente onde a grade termina + padding (2347, sem gap acidental). Alturas de card por
linha: linha 1 e 3 → 437px (estica para a mais alta da linha); linha 2 → 461px (card de João
Francisco, citação um pouco mais longa) — variação pequena (24px), não "altura enorme".

**Resultado: aprovado sem ressalvas.**

---

## 2. Hero

| Item | Confirmado |
|---|---|
| Altura | 400px |
| Imagem | `informacoes.jpg` |
| `background-position` | `0% 0%` |
| Eyebrow | "depoimentos" |
| Título | "A confiança de quem já conta com a CT Price" |
| Container | 1140px |
| Regressão | Nenhuma — `boxed-hero.php` não foi alterado |

**Resultado: aprovado sem ressalvas.**

---

## 3. Grid — confirmado ao vivo nos 5 viewports

| Viewport | Colunas | Largura do card | 7º card |
|---|---|---|---|
| 1440×900 | 3 | 360px | Sozinho na 3ª linha, **centralizado exatamente** (centro do card = centro do container, diferença de 0px) |
| 900×1200 | 2 | 418px | Centralizado (mesma verificação: 0px de diferença) |
| 768×1024 | 2 | 352px | — |
| **767×1024** | **1** (confirmado exatamente no breakpoint) | 732px | — |
| 390×844 | 1 | — | — |

Nenhuma regra `nth-child` foi usada — confirmado por revisão de código (a centralização vem
inteiramente de `flex-wrap`+`justify-content:center`, mesma técnica de
`benefits-grid-section.css`).

**Resultado: aprovado sem ressalvas.**

---

## 4. Qualidade visual dos cards (revisão individual via screenshot full-page nos 3 viewports)

Todos os 7 cards revisados nos screenshots de implementação (desktop/tablet/mobile): thumbnail
16:9 nítida e sem distorção, foto circular da pessoa, nome + empresa como texto real, citação
completa e legível (sem truncamento), ícones de link alinhados, borda/radius/sombra consistentes
entre os 7. Nenhum espaço vazio excessivo, nenhum texto apertado, nenhuma foto deformada, nenhum
link desalinhado. Card de Walter Ferreira Cruz corretamente mostra só o ícone do Instagram (sem
segundo ícone de site enganoso).

**Nenhuma correção visual foi necessária** nos cards em si — a única correção desta validação foi
o bug de dados (§ Correções realizadas), não algo relacionado à apresentação dos cards.

---

## 5. Imagens (computed style, 1440×900)

| Item | Resultado |
|---|---|
| `object-fit` das 7 fotos | `cover`, 56×56px, nenhuma quebrada (`naturalWidth` > 0 em todas) |
| `object-fit` das 7 thumbnails | `cover`, proporção exata **1.778** (16:9) nas 7, nenhuma quebrada |

**Resultado: aprovado sem ressalvas.**

---

## 6. Lightbox — teste real

| Ação | Resultado |
|---|---|
| Clicar no play (mouse) | Modal abre, `<iframe>` criado dinamicamente |
| Vídeo correto | Confirmado nos 4 vídeos testados (IDs batendo exatamente com o card clicado, inclusive título real do YouTube retornado pela própria página, ex.: "Réus Fornari Proprietário da Cotto Figueira e cliente CT Price - YouTube") |
| Autoplay | `autoplay=1` no `src`, player carrega e inicia |
| Botão "X" | Fecha o modal, remove o iframe, devolve foco ao card |
| `Escape` | Fecha o modal, remove o iframe, devolve foco ao card (testado 3×) |
| Clique no backdrop | Fecha o modal (testado em desktop e mobile) |
| Toque no backdrop (mobile) | Fecha o modal (testado via `click()` no elemento em viewport 390×844) |
| Vídeo para ao fechar | **Confirmado por remoção do DOM** (`frame.children.length === 0` logo após fechar) — não é pausado, é destruído, então não há como continuar tocando invisível |

**Resultado: aprovado sem ressalvas.**

---

## 7. Foco e teclado — teste real

| Ação | Resultado |
|---|---|
| `Tab` chega ao botão de play | Confirmado (foco real via `Tab`, não `.focus()` programático — elemento seguinte no DOM recebido corretamente, `:focus-visible` ativo) |
| `Enter` abre | Confirmado — modal abre, vídeo correto carrega |
| `Espaço` abre | Confirmado (comportamento nativo de `<button>`) |
| Foco vai para o botão fechar ao abrir | Confirmado (`document.activeElement` = `.video-testimonial-modal__close`) |
| `Tab` não escapa para conteúdo atrás | Confirmado — após `Tab` dentro do modal aberto, o foco permanece no botão fechar (armadilha de foco de um único elemento focável, sem framework) |
| `Escape` fecha | Confirmado |
| Foco retorna exatamente ao botão que abriu | Confirmado — testado com 3 cards diferentes (Aline Zacarini, Réus Fornari, João Francisco), em todos os casos `aria-label` do elemento focado após fechar bate exatamente com o card de origem |
| Foco visível | Confirmado (`:focus-visible` ativo no botão de thumbnail via `Tab` real) |

**Resultado: aprovado sem ressalvas.**

---

## 8. YouTube sob demanda — confirmado via Network real

- **Antes de qualquer clique**: lista completa de requisições da carga inicial da página revisada
  — **zero requisições** para `youtube.com`/`youtube-nocookie.com`/`ytimg.com`. Único iframe
  presente no HTML inicial é o mapa do footer (`src=""`, componente global já aprovado).
- **Depois de clicar**: cada clique gerou exatamente uma sequência de requisições para o vídeo
  correspondente (`youtube-nocookie.com/embed/{id}`), confirmado para os 4 vídeos testados
  (`eyPTwRBjzU0`, `yNKcg8QHjws`, `hVOh8mo_sm0` ×2) — nenhum vídeo não solicitado foi carregado.
- **Domínio**: `www.youtube-nocookie.com` em 100% das requisições de embed.
- **Playlist de Aline Zacarini**: `?autoplay=1&rel=0&list=PLQhq9pdnKsr86BVYW6xYn51NHHyvGLSZr`
  confirmado presente na URL do iframe ao abrir esse card especificamente — os demais 6 (sem
  playlist auditada) corretamente **não** têm o parâmetro `list`.
- **Ao fechar**: iframe removido do DOM (confirmado), nenhuma requisição de vídeo continua em
  andamento visível na aba de rede após o fechamento.

**Resultado: aprovado sem ressalvas.**

---

## 9. Scroll do modal / mobile (390×844)

- **Achado real corrigido nesta validação**: o modal, como implementado originalmente, adicionava
  a classe de bloqueio de scroll no `<body>`, mas o elemento de rolagem real da página
  (`document.scrollingElement`) é o `<html>` — resultado: `overflow:hidden` no `body` não tinha
  nenhum efeito prático, e a página por trás continuava rolável com o modal aberto.
- **Correção aplicada**: classe `video-testimonial-modal-open` movida para `document.documentElement`
  (`<html>`) tanto no JS quanto no seletor CSS correspondente.
- **Confirmado após a correção**: um evento de scroll real (`WheelEvent`, que reflete rolagem por
  mouse/trackpad — diferente de uma chamada direta a `window.scrollTo()`, que não é uma interação
  de usuário e não é bloqueada por `overflow:hidden` em nenhum navegador) não move mais
  `window.scrollY` enquanto o modal está aberto.
- **Sem overflow horizontal** em 390×844 com o modal aberto (`scrollWidth === clientWidth` =
  390/390).
- **Modal cabe na viewport em 390×844**: `dialogRect` inteiramente dentro de `[0, 390]`
  horizontalmente, botão de fechar visível e clicável.
- **Vídeo responsivo**: `aspect-ratio:16/9` no frame do modal, escala corretamente sem distorção.

### Nota metodológica — artefato de captura de screenshot (não um defeito real)
O primeiro screenshot do modal aberto em 390×844 mostrou o fundo da página (texto/cards)
aparentemente sem o véu escuro semitransparente do backdrop. Investigação direta (não presumida):
`getComputedStyle` do backdrop confirmou `position:absolute; inset:0; background-color:rgba(0,
34, 44, 0.85); opacity:1` cobrindo exatamente os 390×844px da viewport — e, mais decisivo,
`document.elementFromPoint()` em coordenadas que "pareciam" mostrar conteúdo de fundo sem véu
retornou o próprio `.video-testimonial-modal__backdrop` (ou seja, ele está de fato ali,
recebendo cliques, na frente de tudo). Isso confirma que a renderização real (o que um usuário
de fato vê e com o que interage) está correta — o que a ferramenta de screenshot capturou é um
artefato de composição do próprio pipeline de captura (mesmo tipo de artefato de temporização já
documentado em auditorias anteriores para `background-image`/gradiente CSS, aqui ocorrendo com
uma cor de fundo semitransparente). Não foi feita nenhuma alteração de código por causa disso —
não há nada a corrigir.

**Resultado: aprovado, com 1 correção real aplicada (bloqueio de scroll).**

---

## 10. Links sociais/site

- Estrutura confirmada nos 7 cards via árvore de acessibilidade: `link "Visitar site de {empresa}"`
  e `link "Instagram de {empresa}"`, `target="_blank"`, `rel="noopener noreferrer"` (revisão de
  código + confirmação na árvore de acessibilidade).
- **Walter Ferreira Cruz**: confirmado visualmente (screenshot) e estruturalmente (árvore de
  acessibilidade) que o card mostra **somente** o link do Instagram — nenhum botão de site
  duplicado/enganoso.
- Nenhuma URL foi alterada em relação ao array já auditado.

**Resultado: aprovado sem ressalvas.**

---

## 11. Acessibilidade (confirmado ao vivo via árvore de acessibilidade real do navegador)

- Heading da seção: `H2 "Quem confia, recomenda."` presente e agrupando a seção via
  `aria-labelledby`.
- Nome e empresa como texto real (não apenas visual) em todos os 7 cards — confirmado como
  `StaticText` na árvore de acessibilidade.
- Fotos com `alt="Foto de {nome}"` real (nunca vazio) nas 7.
- Thumbnails decorativas (`alt=""`) corretamente cobertas pelo `aria-label` do botão de play que
  as envolve — confirmado: cada botão aparece na árvore como
  `button "Assistir depoimento em vídeo de {nome}, {empresa}"`.
- Modal com semântica correta: `dialog "Vídeo de depoimento" modal` (equivalente a
  `role="dialog" aria-modal="true"`) confirmado na árvore de acessibilidade ao abrir.
- Botão fechar com nome acessível ("Fechar vídeo"), `focusable focused` confirmado ao abrir.
- Links sociais identificáveis individualmente (nunca só "ícone", sempre com o nome da empresa no
  rótulo).
- Nenhum `id` duplicado (revisão de código já feita na implementação, reconfirmada nesta sessão).

**Resultado: aprovado sem ressalvas.**

---

## Correções realizadas nesta validação

1. **Bug real: colisão de nome de variável `$company`** (`components/video-testimonials-section.php`).
   O `foreach` do componente usava `$company` como variável local para o nome da empresa de cada
   depoimento — como `require` executa no mesmo escopo do arquivo que inclui (não cria escopo
   próprio), isso **sobrescrevia o array global `$company`** (carregado por `config/bootstrap.php`
   a partir de `config/company.php`) para todo o restante da requisição. Como o componente é
   incluído *antes* de `includes/footer.php` e `includes/whatsapp-button.php` em
   `depoimentos/index.php`, o footer e o botão de WhatsApp flutuante passavam a ler
   `$company` como a *string* do último depoimento do loop ("Saborzitos") em vez do array
   institucional — resultando em endereço/e-mails/WhatsApp global todos vazios/quebrados no
   footer e no botão flutuante.
   **Correção**: variável renomeada para `$clientCompany` em todo o componente (6 ocorrências).
   **Confirmado corrigido**: footer e WhatsApp flutuante voltaram a exibir os dados corretos de
   `config/company.php` (endereço, e-mails, responsável técnico, URL do WhatsApp canônico),
   reconfirmado via inspeção do DOM antes e depois da correção.
2. **Bug real: bloqueio de scroll do modal não funcionava** (ver PARTE 9 acima) — classe de
   bloqueio movida de `document.body` para `document.documentElement`, porque o elemento de
   rolagem real desta página é o `<html>`. Corrigido em
   `assets/js/video-testimonials-lightbox.js` e `assets/css/video-testimonials-section.css`.

Nenhuma outra correção foi necessária — todos os demais itens (Hero, grid, imagens, lightbox,
teclado/foco, YouTube sob demanda, links, acessibilidade) já estavam corretos como entregues na
implementação original.

---

## Console e rede

- **Console**: nenhum erro JavaScript próprio em nenhum teste realizado (a única mensagem
  observada em todas as verificações foi o mesmo ruído de extensão de navegador já documentado em
  validações anteriores — `[debug] Search endpoint requested!`).
- **Rede**: nenhum 404 em nenhum asset próprio (verificado em carga limpa em 900×1200: 87
  requisições, todas 200/301, exceto as chamadas externas esperadas do Google Maps do footer e da
  extensão Kaspersky do ambiente de teste).
- **Dependências legadas**: `wp-content`/`elementor` ausentes do HTML renderizado;
  `window.jQuery === undefined`; apenas 3 scripts próprios carregados
  (`header.js`/`cookie-banner.js`/`video-testimonials-lightbox.js`) — nenhuma biblioteca nova.

---

## Screenshots da implementação

- `docs/reference/screenshots/depoimentos-implementation-desktop-1440-full.png`
- `docs/reference/screenshots/depoimentos-implementation-tablet-900-full.png`
- `docs/reference/screenshots/depoimentos-implementation-mobile-390-full.png`

(As screenshots da referência original, em `depoimentos-desktop-1440-full.png` etc., não foram
sobrescritas.)
