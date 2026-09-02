# Auditoria — `/arearestrita/`

**Referência:** `https://ctprice.com.br/wp/arearestrita/`
**Data:** 2026-09-02
**Ferramenta:** Chrome DevTools MCP — navegação real, inspeção de DOM/CSS computado, teste de
destinos externos (acesso público, sem login), medição nos 5 viewports obrigatórios.
**Escopo:** auditoria apenas — nenhum arquivo de implementação foi alterado.
`arearestrita/index.php` já existe como **scaffold vazio** (`<!-- TODO: conteúdo da página
"Área Restrita" -->`, ver `docs/reference/site-inventory.md` e `docs/architecture-proposal.md`
§12), confirmado nesta auditoria — nada foi implementado ainda.

Documentos lidos antes desta auditoria: `CLAUDE.md`, `docs/reference/reference-baseline.md`,
`docs/reference/site-inventory.md`, `docs/architecture-proposal.md` (§10, §12, §14), além de
busca por `arearestrita`/`Área Restrita`/`DRIFT-001` em todo o projeto (`config/menu.php`,
`config/company.php`, `includes/header.php`).

---

## 1. Estrutura completa

Elementor `data-elementor-id=647`. **6 seções de topo** (`.e-con` de nível raiz), confirmadas por
inspeção direta do DOM — bate exatamente com o número já registrado em `site-inventory.md`:

| # | `data-id` | Função | Altura (1440px) | Background |
|---|---|---|---|---|
| 1 | `52ae6924` | Topbar (endereço, telefones, WhatsApp, e-mail, idiomas) | 66px | gradiente `linear-gradient(#00222c 0%, #057038 100%)` |
| 2 | `33a457c0` | Header (logo, menu, botão "Área Restrita") | 132px | transparente |
| 3 | `0e005f0` | **Faixa de título da página** — heading "ÁREA RESTRITA" + subtítulo | 110px | branco (sem imagem, sem gradiente) |
| 4 | `2df967c` | **Grade de 2 acessos** (Clientes / Colaboradores), Elementor Flip Box | 680px | branco |
| 5 | `5914380d` | Footer (logo, endereço, menu secundário, mapa) | 400px | `rgb(239,240,241)` |
| 6 | `7430a006` | Rodapé inferior (copyright + crédito) | 78px | `rgb(0,34,44)` |

Nenhum formulário, nenhum popup/modal, nenhum accordion. **Não é uma coleção arbitrária de
botões** — é estruturalmente: topbar → header → título de página → grade de 2 cards
interativos (hover-flip) → footer → bottom bar. Mesmo esqueleto global já usado nas demais
páginas institucionais; as seções 3 e 4 são exclusivas desta página.

---

## 2. Hero

**Não existe Hero nesta página** — nem no sentido de `boxed-hero.php` nem de
`internal-hero.php`. A seção 3 (`0e005f0`) é apenas uma faixa de título simples:

- fundo branco liso (`background-image: none`), sem foto, sem overlay, sem gradiente;
- heading "ÁREA RESTRITA": 32px, `font-weight:700`, `Poppins`, cor `rgb(0,34,44)`,
  `text-transform:uppercase` via CSS (texto-fonte real é `"área restrita"`, minúsculo), centralizado;
- subtítulo: "Área destinada  a clientes e colaboradores da CT Price – Organização Contábil"
  (nota: espaço duplo literal após "destinada", presente no HTML de origem), 16px,
  `rgb(122,122,122)`, centralizado;
- container 1140px (`min(100%, 1140px)`), mesmo valor já usado nas demais páginas do projeto;
- sem eyebrow, sem imagem, sem `background-position`/`background-size` (não há
  `background-image` nenhum).

**Classificação: C — estrutura diferente.** Não é nem `boxed-hero.php` (400px + imagem full-bleed)
nem `internal-hero.php` (640px + imagem + coluna assimétrica) — é mais parecido, em espírito, com
uma faixa de título simples sobre fundo branco, mas também **não bate** com
`components/section-title-band.php` (esse é fundo em gradiente escuro + texto branco; aqui é
fundo branco + texto escuro). **Nenhum componente Hero/título existente no projeto serve sem
modificação** para este padrão — ver recomendação arquitetural, seção 16.

---

## 3. Inventário completo dos acessos

**Exatamente 2 acessos apresentados ao usuário** — não é um portal de login (nenhum campo de
usuário/senha em nenhum lugar da página), é uma página de **redirecionamento** para dois sistemas
externos via dois cards com efeito hover-flip (Elementor "Flip Box").

| | Card "CLIENTES" | Card "COLABORADORES" |
|---|---|---|
| **Texto frente** | "Clientes" | "Colaboradores" |
| **Imagem de frente** (background, `cover`) | `wp-content/uploads/2024/09/5385937.jpg` | `wp-content/uploads/2024/09/6737586.jpg` |
| **Título do verso** | "Acesse aqui sua área restrita" | "Acesse aqui sua área restrita" (texto idêntico) |
| **Descrição do verso** | "Caso não tenha ou esqueceu os seus dados de acesso, fale com a **CT Price**" | "Caso não tenha ou esqueceu os seus dados de acesso, fale com alguém responsável." (texto diferente do card Clientes) |
| **Texto do botão** | "Acessar" | "Acessar" |
| **URL de destino** | `https://ctprice.com.br/documentos` | `https://ctprice.com.br/sh-admin` |
| **Protocolo** | HTTPS | HTTPS |
| **Domínio** | `ctprice.com.br` (mesmo domínio institucional, fora de `/wp/`) | `ctprice.com.br` (idem) |
| **`target`** | nenhum (`""` — mesma aba) | nenhum (`""` — mesma aba) |
| **`rel`** | nenhum | nenhum |
| **Interno/externo** | Externo (sistema separado do site institucional, fora de `/wp/`) | Externo (idem) |
| **Exige autenticação** | Não determinável — a URL não carrega nenhuma aplicação (ver §4) | Não determinável — mesma situação |
| **Redireciona** | Não (URL final = URL clicada) | Sim — `/sh-admin` → `/sh-admin/` (barra final adicionada pelo servidor) |
| **Status atual** | **QUEBRADO** (404 puro de servidor) | **QUEBRADO/EXPOSTO** (listagem de diretório crua, vazia) |

**Estrutura do link em si — inconsistência real entre os dois cards** (confirmado via HTML, não
apenas visual):

- **Card Clientes**: o verso é uma `<div>` comum; **somente o texto "Acessar"** é um `<a>` real
  (`class="elementor-flip-box__button"`). Clicar no título ou na descrição do verso não faz nada.
- **Card Colaboradores**: o verso inteiro **é o próprio `<a href="…sh-admin">`** — título,
  descrição e o "Acessar" (aqui um `<span>`, não um segundo link) estão todos dentro do mesmo
  link. Clicar em qualquer ponto do verso navega.

Ou seja, a área clicável de um card é bem menor (só o botão) que a do outro (o card inteiro) —
diferença real de comportamento entre as duas instâncias do mesmo widget, não uma percepção
visual. Ver §12 e §15.

Nenhum login foi tentado, nenhuma credencial foi usada, nenhum contorno de autenticação foi
tentado — em nenhum dos dois destinos há de fato uma tela de login para testar.

---

## 4. Teste dos destinos

Acesso público direto a cada URL (sem envio de formulário, sem autenticação, sem teste de
vulnerabilidade):

| URL | Requisição | Resultado | URL final |
|---|---|---|---|
| `https://ctprice.com.br/documentos` | `GET` | **404** — corpo: `"404 Not Found — The resource requested could not be found on this server!"` (página crua do servidor, não o 404 estilizado do tema WordPress) | mesma URL, sem redirecionamento |
| `https://ctprice.com.br/sh-admin` | `GET` | **Redirecionamento** (servidor adiciona a barra final) → **200**, corpo: listagem de diretório crua **vazia** (`Index of /sh-admin/`, tabela sem nenhum arquivo listado, rodapé "Proudly Served by LiteSpeed Web Server at ctprice.com.br Port 443") | `https://ctprice.com.br/sh-admin/` |

Classificação conforme a lista da tarefa: `documentos` = **404**; `sh-admin` = **redirecionamento
seguido de conteúdo inesperado** (listagem de diretório do servidor, não uma aplicação — nem
sequer expõe arquivos, a pasta está vazia no momento desta auditoria). Nenhum dos dois é uma tela
de login funcional. Nenhum certificado inválido, nenhum DNS inexistente, nenhuma conexão recusada,
nenhum timeout — ambos os domínios resolvem e respondem normalmente sobre HTTPS, o problema é
exclusivamente de conteúdo/aplicação ausente no servidor.

Estes resultados **confirmam exatamente** o que já estava registrado em
`docs/reference/site-inventory.md` (seção 3, itens 2 e 3) — nenhuma mudança desde aquela
auditoria.

---

## 5. Sistemas identificados

- **`ctprice.com.br/documentos`** (card Clientes): pelo rótulo e pelo texto ("Acesse aqui sua área
  restrita... fale com a CT Price"), presume-se um **portal de documentos para clientes**
  (extrato/relatórios contábeis, prestação de contas). Não é possível confirmar além disso — a URL
  não carrega nenhuma interface.
- **`ctprice.com.br/sh-admin`** (card Colaboradores): pelo rótulo ("Colaboradores") e pelo padrão
  de nome (`sh-admin`, comum em sistemas de RH/folha como "SH-Admin"/"Senior HCM" ou correlatos),
  presume-se um **sistema interno para colaboradores** (RH/folha/portal do funcionário). Também
  não é possível confirmar — a URL não expõe nenhuma aplicação, apenas uma listagem de diretório
  vazia, o que não permite inferir o produto real por trás dela.

Nenhuma inferência além do que a própria interface permite foi feita.

---

## 6. Conteúdo textual (transcrição literal)

- Heading da seção 3: **"ÁREA RESTRITA"** (fonte real em minúsculas: `área restrita`,
  maiúsculas por `text-transform: uppercase`)
- Subtítulo: **"Área destinada  a clientes e colaboradores da CT Price – Organização Contábil"**
  (espaço duplo literal após "destinada")
- Card 1 — frente: **"Clientes"**
- Card 1 — verso — título: **"Acesse aqui sua área restrita"**
- Card 1 — verso — descrição: **"Caso não tenha ou esqueceu os seus dados de acesso, fale com a
  CT Price"** ("CT Price" em destaque/negrito no HTML de origem)
- Card 1 — verso — botão: **"Acessar"**
- Card 2 — frente: **"Colaboradores"**
- Card 2 — verso — título: **"Acesse aqui sua área restrita"** (idêntico ao Card 1)
- Card 2 — verso — descrição: **"Caso não tenha ou esqueceu os seus dados de acesso, fale com
  alguém responsável."** (diferente do Card 1: "alguém responsável" em vez de "a CT Price", e
  termina com ponto final, que o Card 1 não tem)
- Card 2 — verso — botão/span: **"Acessar"**

Nenhum texto foi melhorado, corrigido ou normalizado nesta transcrição.

---

## 7. Cards / blocos de acesso

**Widget identificado: Elementor Flip Box** (`elementor-widget-flip-box`,
`elementor-flip-box--effect-slide elementor-flip-box--direction-left` nos dois cards) — não é
Icon Box, não é Image Box, não é Button avulso. Estrutura: `.elementor-flip-box` (container 3D,
`tabindex="0"`, `overflow:hidden`) → `.elementor-flip-box__front` (imagem de fundo + título) e
`.elementor-flip-box__back` (título + descrição + botão), alternados via `:hover`/`:focus-visible`
(CSS 3D transform).

Medições (1440×900):

| Propriedade | Valor |
|---|---|
| Quantidade | 2 |
| Colunas | 2 (lado a lado) |
| Largura de cada card | 550px |
| Altura de cada card | 640px |
| Gap entre os 2 cards | 0px (colunas adjacentes, sem `gap` explícito — mas cada coluna tem `padding:10px` que cria um respiro visual de ~20px) |
| Padding interno da coluna | 10px |
| Borda | nenhuma |
| Border-radius | nenhum (`0px`) |
| Sombra | nenhuma |
| Background (frente) | imagem (`background-size:cover`, `background-position:50% 50%`) sobre `background-color: rgb(26,188,156)` (verde-água, funciona como tint/overlay) |
| Background (verso) | cor sólida — Card 1: `rgb(0,34,44)` (verde-escuro institucional); Card 2: `rgb(5,112,56)` (verde institucional) — **cores diferentes entre os dois cards** |
| Ícone | wrapper de ícone presente no HTML (`.elementor-icon-wrapper`) mas **vazio** — nenhum ícone configurado nesse widget |
| Alinhamento | título centralizado, verso centralizado |
| CTA | botão "Acessar" (Card 1: link `<a>` só no botão; Card 2: `<span>`, o link é o verso inteiro — ver §3) |

---

## 8. Ícones e assets

| Asset | URL | Formato | Dimensões (arquivo) | Seção | Significado |
|---|---|---|---|---|---|
| Ilustração "Clientes" | `https://ctprice.com.br/wp/wp-content/uploads/2024/09/5385937.jpg` | JPG | fundo do card, `cover` em 550×640 (proporção do arquivo original não confirmada, é um banco de imagens de stock — duas pessoas de negócio) | Card 1 (frente) | Ilustração decorativa de stock, sem `alt` próprio (é `background-image`, não `<img>`) |
| Ilustração "Colaboradores" | `https://ctprice.com.br/wp/wp-content/uploads/2024/09/6737586.jpg` | JPG | idem, ilustração de stock (grupo de pessoas) | Card 2 (frente) | Idem — decorativa |
| Ícone WhatsApp (topbar/flutuante) | já catalogado nas auditorias globais | SVG inline | — | Global | Componente já implementado (`assets/js`/`includes/whatsapp-button.php`) |
| Bandeiras de idioma (GTranslate) | `wp-content/plugins/gtranslate/flags/24/*.png` | PNG | 24×24 | Topbar (global) | Widget de terceiros, já fora do escopo de reconstrução visual própria |
| Logo CT Price | já catalogado | SVG/PNG | — | Header/footer (global) | Componente já implementado |

**Classificação:** as 2 ilustrações JPG são **novas, exclusivas desta página** (nenhuma outra
página do site as utiliza) — precisam ser baixadas/catalogadas quando a implementação for feita.
Nenhum ícone quebrado encontrado. Nenhuma biblioteca de ícones (Font Awesome/SVG sprite) é usada
nos 2 cards — o `.elementor-icon-wrapper` existe no HTML mas está vazio (campo de ícone do widget
não preenchido no Elementor). **NÃO foi feito nenhum download nesta etapa**, conforme instruído.

---

## 9. Responsividade

| Viewport | Colunas | Largura do card | Breakpoint real | Overflow horizontal |
|---|---|---|---|---|
| 1440×900 | 2 | 550px | — | Não (`scrollWidth === clientWidth`) |
| 900×1200 | 2 | 420px | — | Não |
| 768×1024 | 2 | 367px | — | Não |
| **767×1024** | **1** (empilha) | 732px | **Confirmado exatamente em 767px** (`flex-wrap:wrap` no container `.e-con-inner`; abaixo de 768px os 2 cards ocupam 100% cada, um embaixo do outro) | Não |
| 390×844 | 1 | 370px | — (mesmo padrão do breakpoint de 767px) | Não |

**Não é 3/2/1** — é **2/2/2/1/1** (2 colunas até 768px, 1 coluna a partir de 767px), porque a
página só tem 2 acessos, não uma grade maior. O botão "Área Restrita" do header e o hambúrguer de
menu mobile seguem o comportamento já documentado globalmente (não específico desta página).

### Defeito real encontrado: texto "COLABORADORES" cortado (overflow) em telas ≤900px

Confirmado por medição direta (`scrollWidth` vs `clientWidth` do `<h3>` do título da frente do
Card 2, não apenas impressão visual do screenshot):

| Viewport | `scrollWidth` do título | `clientWidth` do título | Cortado? |
|---|---|---|---|
| 1440×900 | 480px | 480px | Não |
| 900×1200 | 401px | 350px | **Sim — 51px cortados** |
| 390×844 | ~401px (mesma palavra) | 280px | **Sim — ~121px cortados** |

Causa raiz: o `<h3>` "Colaboradores" usa `font-size:45px` fixo (não responsivo) e é uma única
palavra (sem espaço), então o navegador não pode quebrar linha nela
(`overflow-wrap`/`word-break` não configurados para permitir quebra); o container
`.elementor-flip-box` tem `overflow:hidden` (necessário para o efeito de flip 3D), então o
excesso de texto é **cortado silenciosamente**, sem reticências — nas capturas em 900px e 390px o
texto aparece como "COLABORADORE" com o final fora da área visível do card. **Reproduzido nos
screenshots de referência** (`arearestrita-tablet-900-full.png`,
`arearestrita-mobile-390-full.png`). O card "Clientes" (8 caracteres) não sofre o mesmo problema
nessas larguras.

**Classificação: defeito conhecido (categoria C, docs/architecture-proposal.md §2)** — não
corrigido nesta etapa, apenas documentado. Decisão de correção (`font-size` responsivo ou permitir
quebra de palavra) fica para a implementação.

---

## 10. Interações

- **Hover** (desktop): passar o mouse sobre qualquer um dos 2 cards dispara um flip 3D (efeito
  "slide", direção "left") que troca a frente (imagem + título curto) pelo verso (título longo +
  descrição + botão "Acessar").
- **Focus** (teclado): confirmado via inspeção de CSS que a mesma transição do `:hover` também
  dispara em `:focus-visible` no container `.elementor-flip-box` (`tabindex="0"`) — ou seja, um
  usuário de teclado que navega até o card também vê o verso aparecer.
- **Popup/modal**: nenhum.
- **Accordion**: nenhum.
- **Abertura em nova aba**: **não** — ambos os links (`documentos` e `sh-admin`) navegam na
  **mesma aba** (`target=""`), sem `rel`.
- **Redirecionamento**: só do lado do servidor de destino (`sh-admin` → `sh-admin/`), não da
  página em si.
- **Loading/aviso antes do acesso**: nenhum — clique navega direto, sem confirmação, sem aviso de
  saída do site institucional.
- Nenhum login foi tentado.

---

## 11. Acessibilidade

Problemas encontrados (documentação apenas, sem correção nesta etapa):

1. **Tab stops "mortos"**: cada `.elementor-flip-box` tem `tabindex="0"` mas **não é** `<a>`,
   `<button>` nem tem `role`/`aria-*` — é um contêiner focável sem nenhuma ação própria (Enter/
   Espaço não fazem nada nele). Um usuário de teclado precisa passar por essa parada vazia antes
   de chegar ao link real.
2. **Nome acessível inconsistente entre os 2 links**: o link do Card 1 é só o botão, nome
   acessível **"Acessar"** (pouco descritivo fora de contexto); o link do Card 2 é o verso inteiro,
   nome acessível o texto concatenado **"Acesse aqui sua área restrita Caso não tenha ou esqueceu
   os seus dados de acesso, fale com alguém responsável. Acessar"** (longo, repetitivo para quem
   usa leitor de tela).
3. **Informação só disponível no verso** (título "Acesse aqui sua área restrita" + descrição) só
   aparece via `:hover`/`:focus-visible` — para leitores de tela que não seguem foco/hover visual
   da mesma forma, o conteúdo está no DOM (confirmado presente e lido pela árvore de
   acessibilidade), então é tecnicamente exposto, mas a experiência depende de o texto do
   link (`aria-label` custom) já entregar contexto suficiente sem depender do estado visual —
   hoje não entrega, no caso do Card 1.
4. **Nenhum `alt` a avaliar** nas ilustrações: são `background-image` (CSS), não `<img>`, então
   não há atributo `alt` ausente — mas também não há nenhum texto alternativo para o conteúdo
   puramente decorativo, o que é aceitável (as imagens são decorativas).
5. **Heading hierarchy**: `h2` (ÁREA RESTRITA) → `h3` (Clientes) → `h3` (Acesse aqui sua área
   restrita) → `h3` (Colaboradores) → `h3` (Acesse aqui sua área restrita) — sem `h1` na página
   (padrão já visto em outras páginas do site, não específico desta).
6. **Contraste**: título/subtítulo da faixa (seção 3) usam cores escuras sobre fundo branco — sem
   problema aparente. Versos dos cards usam texto claro sobre fundo escuro (`#00222c`/`#057038`) —
   sem problema aparente. Não foi calculado um índice formal de contraste (WCAG) nesta etapa.
7. **Área clicável real menor que a área visual do card** no Card 1 (só o botão "Acessar" é
   clicável, não o card inteiro) — pode confundir quem espera que clicar em qualquer parte do card
   funcione, especialmente em touch (mobile), onde não há `:hover` para "avisar" antes do toque.

---

## 12. Segurança dos links

| Item | Card Clientes | Card Colaboradores | Classificação |
|---|---|---|---|
| HTTPS | Sim | Sim | A — fidelidade obrigatória (já é o padrão correto) |
| `target="_blank"` | Não usado (mesma aba) | Não usado (mesma aba) | B — comportamento a preservar (decisão do site original, não um bug a corrigir por conta própria) |
| `rel="noopener noreferrer"` | Ausente (não se aplica sem `target="_blank"`) | Ausente (idem) | B |
| Redirecionamento HTTP→HTTPS | N/A — já é HTTPS desde a origem | N/A — idem | — |
| Certificado aparente | Válido (sem aviso do navegador) | Válido (sem aviso do navegador) | A |
| Domínio coerente | Sim (`ctprice.com.br`, mesmo domínio institucional) | Sim (idem) | A |
| URL obsoleta/quebrada | **Sim — 404** | **Sim — expõe listagem de diretório vazia** | **C — defeito conhecido a corrigir, mas dependente de destino correto (não pode ser "corrigido" sem saber a URL certa)** |

Nenhuma alteração foi feita. As duas URLs quebradas são **decisão dependente da CT Price** (ver
§18) quanto ao destino correto — não uma correção que este projeto possa aplicar sozinho.

---

## 13. Comparação com configurações atuais

- **`config/company.php`** já tem a estrutura preparada: `sistemas_externos.area_restrita_clientes`
  e `sistemas_externos.area_restrita_colaboradores`, **ambos `null`** propositalmente, com
  comentário `TODO` já registrando exatamente os mesmos problemas confirmados nesta auditoria
  (404 em `documentos`, listagem de diretório exposta em `sh-admin`) — **nenhum conflito**, os
  valores centralizados já estão alinhados com o que foi reconfirmado agora.
- **`config/menu.php`**: `area_restrita` (label "Área Restrita", url `/arearestrita/`) já existe e
  aponta para a página interna do novo site (não para os sistemas externos) — correto, é o link do
  botão do header, não os 2 links dos cards.
- **Nenhum hardcode encontrado** fora de `config/company.php` e `config/menu.php` relacionado a
  Área Restrita — `arearestrita/index.php` (scaffold) não contém nenhuma URL própria ainda.
- **Conclusão**: os destinos dos 2 sistemas externos **já estão centralizados** na estrutura de
  config certa, só aguardando os valores corretos (ou uma decisão explícita de manter `null` e
  desabilitar os botões — ver §16/§18). Nenhuma modificação foi feita em nenhum config nesta
  etapa.

---

## 14. REFERENCE DRIFT

- **Botão "Área Restrita" nesta própria página**: **presente e funcional** no header, apontando
  para `https://ctprice.com.br/wp/arearestrita/` (a própria página) — consistente com o
  comportamento que `DRIFT-001` já registra como o **baseline original** (não o estado "vazio"
  encontrado em outras páginas durante a auditoria do header). Ou seja: nesta página específica,
  não há divergência nova — o botão aparece como esperado, tanto em 1440px quanto em 900px e
  390px (mobile, dentro do menu/CTA da barra superior).
- **Nenhum novo `REFERENCE DRIFT` foi identificado** nesta auditoria. Os 2 links quebrados
  (`documentos`, `sh-admin`) **não são drift** — já estavam quebrados na auditoria anterior
  (`site-inventory.md`, mesma data de referência do baseline) e continuam exatamente no mesmo
  estado agora (mesmo tipo de erro, mesma mensagem, mesmo domínio) — é a permanência de um defeito
  já catalogado, não uma mudança do site ao vivo desde então.
- O defeito de texto cortado no card "Colaboradores" (§9) **não é drift** — é um defeito estrutural
  do CSS do site atual, presumivelmente presente desde sempre (não uma mudança recente), apenas
  não documentado nas auditorias anteriores (que não tinham medido esta página em detalhe ainda).

---

## 15. Qualidade visual

- **Aparência dos cards**: visualmente agradável no estado "frente" (ilustrações de stock
  coerentes com o tom institucional, cores da marca no overlay). O efeito de flip é uma escolha de
  interação um pouco datada (padrão comum de temas WordPress ~2020), mas funcional.
  **Somente documentado — decisão de manter ou evoluir fica para a implementação.**
- **Consistência entre os 2 cards**: cores de fundo do verso diferentes entre os 2 (`#00222c` vs
  `#057038`) sem motivo aparente de conteúdo — parece intencional (paleta institucional variada),
  não um erro.
- **Clareza dos nomes**: "Clientes" e "Colaboradores" são claros; o título do verso ("Acesse aqui
  sua área restrita") é redundante em ambos os cards (a página inteira já se chama "Área
  Restrita") — mas é conteúdo original, não deve ser alterado por iniciativa própria.
- **Excesso de ícones**: nenhum — os cards não usam ícones, só as ilustrações fotográficas.
- **Botões genéricos**: o texto "Acessar" é genérico (não diz para onde), mas é o único texto de
  CTA usado nas 2 instâncias — consistente entre si.
- **Sombras**: nenhuma nos cards (visual "flat", sem elevação) — diferente da linguagem de sombra
  leve já usada em outros componentes do projeto novo (`.logo-card`/`.job-card`/etc.), mas isso é
  uma característica do **site original**, a decidir na implementação se será preservada ou
  uniformizada com a linguagem visual já estabelecida no novo site.
- **Espaçamento/alinhamento**: título e subtítulo bem centralizados; os 2 cards ocupam a largura
  total do container 1140px sem gap explícito entre eles (só o padding de 10px de cada coluna cria
  um respiro).
- **Qualidade mobile**: o defeito de texto cortado (§9) é o problema mais sério encontrado nesta
  auditoria — afeta diretamente a legibilidade em tablet/mobile.
- **Hierarquia**: heading da página > cards — hierarquia simples e clara, sem ambiguidade.

Nenhuma decisão de fidelidade × evolução foi tomada nesta etapa — apenas documentado, conforme
instruído.

---

## 16. Arquitetura recomendada para reconstrução (sem implementar)

- **`boxed-hero.php`/`internal-hero.php` NÃO servem** — esta página não tem Hero com imagem de
  fundo (ver §2). Também não é o padrão de `section-title-band.php` (gradiente escuro + texto
  branco) — é fundo branco + texto escuro, centralizado, sem gradiente. **Nenhum componente
  existente serve para a faixa de título desta página sem modificação relevante** — mas dado que é
  um padrão muito simples (heading + parágrafo, ambos centralizados, fundo branco), pode valer
  mais a pena um markup dedicado simples do que forçar reuso de um componente com propósito
  diferente (imagem de fundo).
- **Nenhum componente existente serve para os cards** — o padrão de "flip card" (imagem na frente,
  texto+CTA no verso, revelado por hover/focus) não existe em nenhum componente já implementado no
  projeto (`.logo-card`, `.job-card`, `.benefit-card`, `.video-testimonial-card` são todos cards
  estáticos, sem estado de frente/verso). Um **componente específico de "acesso externo com
  flip"** (ou uma versão mais simples/estática, sem replicar o hover-flip — decisão de fidelidade
  × evolução para a etapa de implementação) faz sentido, dado que só há 2 itens e são exclusivos
  desta página.
- **Centralização em config**: os 2 destinos **já estão preparados** em
  `config/company.php['sistemas_externos']` (`area_restrita_clientes`,
  `area_restrita_colaboradores`, ambos `null` com TODO) — a implementação deve consumir esses 2
  valores, não hardcodar URLs na página. Quando `null`, a implementação precisa decidir como tratar
  o botão (ver abaixo).
- **JS necessário**: só se o efeito de hover-flip for replicado (JS não é estritamente necessário
  para o flip em si — pode ser feito só em CSS com `:hover`/`:focus-within` e `transform: rotateY`,
  como o próprio Elementor faz) — não é necessário nenhum JS para navegação/interação além disso.
- **Acesso quebrado → indisponível em vez de link morto**: como os 2 destinos atuais estão
  confirmados quebrados (404 / listagem de diretório vazia), a implementação **deveria considerar**
  marcar temporariamente os cards como indisponíveis (ex.: texto "Em breve" ou botão desabilitado)
  em vez de apontar para uma URL que garante uma experiência ruim ao usuário real — mas esta é
  **uma decisão de produto, não uma decisão técnica**, e fica classificada como dependência de
  validação da CT Price (§18), não decidida aqua.

---

## 17. Futuro CMS

Os 2 acessos da Área Restrita **fazem sentido como conteúdo configurável** no futuro CMS, com o
seguinte formato conceitual (sem projetar schema/banco nesta etapa):

- `nome` (ex.: "Clientes", "Colaboradores")
- `descrição` (texto do verso)
- `imagem` (ilustração de fundo do card)
- `url` (destino externo)
- `ativo/inativo` (permitiria desabilitar um card sem remover código, exatamente o cenário atual
  em que os 2 destinos estão quebrados)
- `ordem` (qual aparece primeiro)

Isso é consistente com o padrão já estabelecido no projeto para outras listas de conteúdo
(`config/jobs.php`, `config/benefits.php`, `config/video-testimonials.php`) — mesma filosofia de
"config como array", só que como candidato a campo editável pelo cliente no futuro painel
administrativo (explicitamente fora de escopo agora, per `CLAUDE.md` e a "Decisão global" desta
tarefa).

---

## 18. Dependências do cliente (perguntas para a CT Price)

1. **`ctprice.com.br/documentos`** (card Clientes) está retornando 404 puro — qual é a URL correta
   do portal de documentos para clientes? Ou esse sistema foi descontinuado?
2. **`ctprice.com.br/sh-admin`** (card Colaboradores) está expondo uma listagem de diretório vazia,
   sem nenhuma aplicação de login visível — qual é a URL/sistema correto para colaboradores? É um
   sistema desativado, um domínio/caminho que mudou, ou um acesso que deveria ser removido?
3. Enquanto os destinos corretos não são confirmados: os 2 cards devem ficar **temporariamente
   desabilitados/marcados como indisponíveis** no novo site, ou devem continuar apontando para as
   URLs atuais (mesmo sabendo que estão quebradas), replicando fielmente o comportamento (quebrado)
   do site atual?
4. O texto do verso do Card Clientes diz "fale com a CT Price" e o do Card Colaboradores diz "fale
   com alguém responsável" — essa diferença de tom é intencional, ou um dos dois textos deveria ser
   igual ao outro?
5. Confirma-se que **não há necessidade de tela de login própria** no site institucional (ambos os
   acessos são só links de saída para sistemas de terceiros, sem autenticação no site novo)?
6. O botão "Área Restrita" no header — mantido conforme `DRIFT-001` já registra — deve continuar
   existindo no lançamento do novo site, dado que os 2 destinos aos quais ele leva estão quebrados?

Nenhuma resposta foi presumida ou inventada.

---

## Arquivos criados nesta etapa

- `docs/reference/arearestrita-audit.md` (este documento)
- `docs/reference/screenshots/arearestrita-desktop-1440-full.png`
- `docs/reference/screenshots/arearestrita-tablet-900-full.png`
- `docs/reference/screenshots/arearestrita-mobile-390-full.png`

Nenhum arquivo de implementação (`arearestrita/index.php`, `config/*`, `components/*`) foi
alterado.
