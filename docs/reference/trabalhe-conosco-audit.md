# Auditoria visual — `/trabalhe-conosco/`

Data: 2026-08-30
Referência: `https://ctprice.com.br/wp/trabalhe-conosco/` (`data-elementor-id="431"`, 16 seções —
a maior página do site, confirmado em `docs/reference/site-inventory.md`)
Documentação-base: `CLAUDE.md`, `docs/reference/reference-baseline.md`,
`docs/reference/site-inventory.md`, `docs/reference/informacoes-final-validation.md`,
`docs/reference/fale-conosco-final-validation.md`, `docs/reference/sobre-nos-final-validation.md`

**Escopo desta etapa: SOMENTE auditoria.** Nenhum arquivo de implementação foi criado ou
alterado. Nenhum asset foi baixado (dimensões/formato verificados via `getimagesize()` direto na
URL remota, sem gravar nenhum arquivo local).

Viewports inspecionados: 1440×900, 900×1200, 768×1024, 767×1024, 390×844.

---

## Achado principal

Ao contrário do que "Trabalhe Conosco" isoladamente sugere, esta página **não é só uma lista de
vagas** — ela reaproveita a mesma imagem de Hero já usada em `/informacoes/`
(`informacoes.jpg`, mesmo `background-position:0% 0%`) e concentra o conteúdo realmente exclusivo
em dois blocos: (1) **3 vagas em aberto** com um formulário de candidatura em popup (currículo +
upload de arquivo), e (2) uma seção **"Nossos Benefícios" com 14 itens**, cada um representado
por uma imagem PNG isolada **sem nenhum título ou descrição em texto/HTML** — o nome do
benefício está desenhado dentro da própria imagem. Isso tem implicação direta de acessibilidade
(todo `alt=""`) e de arquitetura (nenhum componente existente do projeto foi feito para esse
padrão "grade de imagens sem texto").

---

## 1. Estrutura completa (16 seções de topo, ordem confirmada)

| # | `data-id` | Função | Altura (1440×900) |
|---|---|---|---|
| 0 | `443b883c` | Topbar | 66px |
| 1 | `513582b` | Header (logo + menu + **botão "Área Restrita" presente** — ver §16) | 132px |
| 2 | `4a5aae41` | Hero ("trabalhe conosco" / "Veja as vagas Disponíveis") | 400px |
| 3 | `46754cb` | **Espaçador vazio** (container Elementor sem nenhum widget, só para abrir espaço) | 20px |
| 4 | `e393084` | **3 vagas em aberto** (cards com pré-requisitos + botão "Clique aqui") | 655,78px |
| 5 | `4f931b8` | Espaçador vazio | 20px |
| 6 | `015c431` | Espaçador vazio | 20px |
| 7 | `6702516` | Espaçador vazio | 20px |
| 8 | `f18d3b4` | Faixa de título em gradiente "Nossos Benefícios" (contém a âncora `id="beneficios"`) | 102px |
| 9 | `4f03d16` | Benefícios, linha 1 (3 itens) | 300px |
| 10 | `da64094` | Benefícios, linha 2 (3 itens) | 316,45px |
| 11 | `b5c09b1` | Benefícios, linha 3 (3 itens) | 300px |
| 12 | `c777cce` | Benefícios, linha 4 (3 itens) | 300px |
| 13 | `e83f0dd` | Benefícios, linha 5 (2 itens) | 300px |
| 14 | `343404a5` | Footer (logo, endereço, menu, mapa) | 400px |
| 15 | `6afda33f` | Bottom bar (copyright) | 78,39px |

**Observação estrutural relevante**: os benefícios NÃO estão num único container de grid — são
**5 seções de nível superior separadas** (linhas de até 3 itens cada), contíguas sem gap entre
si (o `top` de cada linha é exatamente igual ao `bottom` da anterior, confirmado nos 5
viewports). Isso é uma particularidade do Elementor (cada "linha" é um container independente
solto no canvas, não uma única grade), não uma decisão de design — não precisa ser reproduzido
literalmente na implementação (uma única grade de 14 itens é estruturalmente equivalente e mais
simples).

Também há **4 seções de topo inteiramente vazias** (idx 3, 5, 6, 7), usadas como espaçamento
vertical em vez de `margin`/`padding` — outro padrão amador do Elementor, não deve ser copiado
como arquitetura (usar espaçamento CSS normal).

---

## 2. Hero

**Classificação: A — reutilização direta de `components/boxed-hero.php`.**

| Propriedade | Medido | Igual a `boxed-hero.php`? |
|---|---|---|
| Altura | 400px em todos os 5 viewports (sem breakpoint próprio) | Sim |
| Container | `.e-con-inner`, `max-width: min(100%, 1140px)` → 1140px | Sim |
| Estrutura textual | Dois `<h2>`: eyebrow "trabalhe conosco" + título "Veja as vagas Disponíveis", sem `<strong>` de destaque parcial | Sim |
| Background | `url(.../informacoes.jpg)` — **mesma imagem já usada em `/informacoes/` e `/parcerias/`**, já existente no projeto (`assets/images/pages/informacoes/informacoes.jpg`) | Mecanismo idêntico, asset já disponível |
| `background-size` | `cover` | Sim |
| `background-position` | `0% 0%` | Igual ao já usado em `/parcerias/` e `/informacoes/` |
| Tipografia eyebrow | Roboto 700, 20px, `rgb(0,34,44)`, uppercase | Idêntico |
| Tipografia título | Roboto 700, 30px, `rgb(5,112,56)` | Idêntico |
| Alinhamento | Esquerda | Idêntico |
| Responsividade | Sem mudança de altura/tipografia em nenhum dos 5 viewports | Idêntico |

**Nenhum asset novo é necessário para o Hero** — `informacoes.jpg` já está no projeto e pode ser
reaproveitado diretamente (terceira página a usar a mesma imagem/enquadramento, depois de
`/parcerias/` e `/informacoes/`).

---

## 3. Conteúdo institucional

### 3.1 Hero
- Eyebrow: "trabalhe conosco"
- Título: "Veja as vagas Disponíveis"

### 3.2 Vagas em aberto (3 cards, transcrição literal)

**VAGA PARA ANALISTA CONTÁBIL**
- Pré-requisitos: "Atuar nas rotinas contábeis das empresas enquadradas nos regimes (Simples
  Nacional, Lucro Presumido e Lucro Real), lançamentos e análise de demonstrações contábeis,
  fechamento de balanço, conciliação de fornecedores, bancos, clientes, obrigações acessórias,
  entre outras atividades pertinentes ao cargo."
- Diferencial: "Conhecimento no sistema Domínio"; "Experiência mínima de 1 ano em escritório de
  contabilidade"; "Ser inscrito no CRC ou cursando superior em Ciências Contábeis"
- CTA: "Clique aqui" → abre popup de candidatura (ver §6)

**VAGA PARA ANALISTA DE DEPARTAMENTO PESSOAL**
- Pré-requisitos: "Atuar nas rotinas de departamento pessoal de empresas enquadradas nos Regimes
  (Simples Nacional, Lucro Real e Lucro Presumido)."; "Processamento de folha, férias, rescisões,
  acompanhamento de afastamentos, Sefip, DCTF WEB, e E-social, entre outras atividades
  pertinentes ao cargo."
- Diferencial: "Conhecimento no sistema Domínio"; "Experiência mínima de 1 ano em escritório de
  contabilidade"
- CTA: "Clique aqui" → mesmo popup

**VAGA PARA ANALISTA FISCAL**
- Pré-requisitos: "Apuração fiscal das empresas de Regime Lucro Real e Presumido."; "Apuração dos
  impostos municipais, estaduais e federais, entrega de obrigações acessórias (EFD Fiscal., EFD
  Contábil, EFD REinf, DCTF), entre outras atividades pertinentes ao cargo."
- Diferencial: "Conhecimento no sistema Domínio"; "Experiência mínima de 1 ano em escritório de
  contabilidade"
- CTA: "Clique aqui" → mesmo popup

Cada rótulo ("Pré-requisitos:"/"Diferencial:") aparece em `#057038` (verde-marca) e cada marcador
"->" em `#00222C` (verde-petróleo escuro) — via `<span style="color:...">` inline no HTML
original, não uma classe CSS.

### 3.3 "Nossos Benefícios" (heading da faixa em gradiente)
Nenhum texto de introdução além do próprio título — os 14 itens são só imagens (ver §4).

Nenhum outro texto institucional (nenhuma "sobre trabalhar aqui", nenhuma cultura/valores) foi
encontrado nesta página — o conteúdo textual é inteiramente as 3 vagas.

---

## 4. Seção Benefícios (`#beneficios`)

**A âncora `#beneficios` existe de fato no DOM**: `<div class="elementor-menu-anchor"
id="beneficios"></div>`, dentro da mesma seção 8 (faixa de título), imediatamente antes do H2
"Nossos Benefícios" — confirmado por `document.querySelector('#beneficios')`. O link do submenu
desta própria página (`href="#beneficios"`, relativo) funciona corretamente **apenas quando o
usuário já está em `/trabalhe-conosco/`** — a partir de qualquer outra página, o link relativo
não leva a lugar nenhum (defeito já registrado em `site-inventory.md`, não específico desta
auditoria).

### Quantidade e composição
**14 benefícios**, cada um é **uma única imagem PNG**, sem nenhum `<h3>`/`<p>` de título ou
descrição — a auditoria confirmou por `querySelectorAll('h3, .elementor-image-box-description')`
dentro de cada linha: **zero elementos de texto**. O "nome" de cada benefício está desenhado
dentro do próprio PNG.

| # | Arquivo | Dimensões reais | Conteúdo visual |
|---|---|---|---|
| 1 | `ben01.png` | 200×200 | Logo "LILLIUM" (fundo preto) |
| 2 | `ben02.png` | 229×229 | Logo "Amiste Café" (círculo vermelho) |
| 3 | `ben03.png` | 768×512 | Logo "hapvida" (plano de saúde/odontológico) |
| 4 | `ben04.png` | 224×224 | Logo "caju" (cartão de benefícios VR/VA, fundo vermelho) |
| 5 | `ben05.png` | 192×256 | Ilustração "Happy Birthday" (laço/presente) |
| 6 | `ben06.png` | 220×321 | Ícone + texto "Onvio App / Portal do Empregado" |
| 7 | `ben07.png` | 346×309 | Ícone + texto "Indicação de Empresas" |
| 8 | `ben08.png` | 187×249 | Ícone (medalha) + texto "Premiação Desempenho" |
| 9 | `ben09.png` | 220×233 | Ícone (aperto de mãos) + texto "Programa Desenvolvimento ao Colaborador" |
| 10 | `ben10.png` | 328×332 | Selo + texto "Indicação Talentos" |
| 11 | `ben11.png` | 408×218 | Logo "Unimed" + texto "Plano de Saúde" |
| 12 | `ben12.png` | 255×251 | Ícone "B Day" (fundo vermelho) |
| 13 | `ben13.png` | 339×340 | Ícone + texto "Dress Code" (gravata, contorno azul) |
| 14 | `ben14.png` | 372×497 | Selo circular + texto "Ginástica Laboral" |

Confirma-se uma **mistura genuína de dois tipos de conteúdo** na mesma grade: logos de marcas
parceiras reais (Lillium, Amiste Café, Hapvida, Caju, Unimed — provavelmente convênios/parcerias
comerciais oferecidas como benefício) e ilustrações caseiras texto+ícone (Happy Birthday, Onvio
App, Indicação de Empresas, Premiação Desempenho, Desenvolvimento ao Colaborador, Indicação
Talentos, B Day, Dress Code, Ginástica Laboral) — sem nenhum tratamento visual unificado entre
os dois grupos (cores de fundo, tipografia, proporção e estilo de borda diferentes em cada uma).

### Estrutura / container / grid
- Container: 1140px (igual às demais seções da página).
- 5 linhas (containers Elementor separados, ver §1), 3 itens por linha (exceto a última, com 2).
- Cada item ocupa 1/3 da largura do container (1140/3 ≈ 363–370px medido).
- Gap horizontal entre itens na mesma linha: **25px** (`gap` do flex container).
- Gap vertical entre linhas: **0px** — as seções são contíguas, sem margem entre si.
- Alinhamento: `elementor-position-top` — imagem alinhada ao topo da célula, sem centralização
  vertical.
- Nenhuma borda, nenhum background, nenhum `border-radius`, nenhuma sombra em nenhum item —
  são só imagens soltas sobre fundo branco.
- Altura de cada linha: determinada pela imagem mais alta da linha (300px na maioria; 316,45px
  na linha 2, cuja imagem mais alta mede 250px de exibição).
- Cada imagem é exibida com **largura fixa de 172px** e altura proporcional variável (92px a
  250px, conforme a proporção original de cada PNG) — **sem `object-fit`/caixa uniforme**, o que
  produz uma grade visualmente desalinhada (ver §11).

### Hover
Nenhuma regra CSS de `:hover` específica encontrada nas imagens de benefício (nenhuma mudança de
cursor além do padrão, nenhuma transição, nenhum link — as imagens não são clicáveis).

### Responsividade dos benefícios
| Viewport | Layout |
|---|---|
| 1440×900 | 3 colunas (172px de imagem, 363px de célula) |
| 900×1200 | 3 colunas (126px de imagem, 278px de célula) — sem estágio de 2 colunas |
| 768×1024 | 3 colunas (mesma lógica, célula ~234px) |
| **767×1024** | **1 coluna** (empilhamento total, cada item 100% da largura) — breakpoint confirmado exatamente em 767px |
| 390×844 | 1 coluna, mesma lógica de 767px |

**Breakpoint real: 767px** (idêntico ao padrão já usado no resto do projeto) — confirmado
testando 767 vs 768 diretamente; não presumido.

Nenhum problema de empilhamento fora do já esperado (ordem preservada, sem reflow estranho) —
mas a ausência de título/texto em cada item continua idêntica em qualquer viewport (não é um
problema introduzido pelo mobile, é estrutural).

---

## 5. Ícones

**Nenhum ícone SVG inline nem Font Awesome** nesta página. Todos os 14 "ícones" de benefício são,
na verdade, **imagens PNG raster completas** (ver tabela §4) — nenhuma tem `viewBox` (não são
SVG), nenhuma vem de biblioteca de ícones. Cada uma tem dimensões/proporção próprias e
inconsistentes entre si (ver §11). O `alt` de todas é `""` (vazio) — nenhuma tem texto
alternativo, apesar de várias carregarem informação textual real (nome do benefício) dentro da
própria imagem (ver §12).

Nenhum outro ícone (Hero, vagas, footer) é exclusivo desta página — os já usados no header/topbar/
footer/WhatsApp são os componentes globais já auditados.

---

## 6. Recrutamento / vagas

| Link/Recurso | Texto | URL | `target` | Onde aparece | Status | Bate com `config/company.php`? |
|---|---|---|---|---|---|---|
| Sistema externo de recrutamento | "Trabalhe Conosco" (item de menu) / "Vagas" (submenu) | `https://recrutamento.ctprice.com.br/vagas` | — | Header (menu principal, em toda página) | **200, funcional** (reconfirmado nesta auditoria) | **Sim** — `config/company.php['sistemas_externos']['recrutamento']` já tem exatamente este valor |
| "Clique aqui" ×3 (uma por vaga) | "Clique aqui" | `#elementor-action:action=popup:open&settings=...` (abre popup id 442) | — | Dentro de cada card de vaga | Abre popup local (não é navegação) | N/A — não é um link, é um gatilho de popup |

**Nenhum link direto** dentro do conteúdo desta página aponta para o sistema externo de
recrutamento — o único ponto de acesso a ele é o menu/submenu global (já usado em todas as
páginas). O conteúdo próprio desta página (as 3 vagas) usa exclusivamente o popup de candidatura
descrito abaixo, não o sistema externo.

### Popup de candidatura (id Elementor `442`, mesmo popup para as 3 vagas)
Formulário Elementor Pro ("Cadastro de Currículos") com os campos:

| Campo | Tipo | Obrigatório |
|---|---|---|
| Nome | texto | Sim |
| E-mail | e-mail | Sim |
| Telefone de contato | telefone | Sim |
| *(sem rótulo visível)* — select de cargo | `<select>`: Auxiliar Empresarial / Analista de Departamento Pessoal / Analista Contábil / Analista Fiscal / Vendedor Externo de Serviços de Contabilidade / Agente de Segurança | Sim |
| Pretensão Salarial | texto | Sim |
| Empresa onde trabalhou, ou trabalha atualmente | texto | Sim |
| Data de início | texto | Sim |
| Data fim (em branco se atual) | texto | Não |
| Descreva brevemente as atividades desenvolvidas | textarea | Sim |
| *(sem rótulo visível)* — upload de currículo | arquivo | Sim |

Botão: "Enviar". **Nenhuma candidatura foi enviada nem nenhum arquivo foi anexado durante esta
auditoria** — o formulário foi apenas inspecionado estruturalmente (DOM), conforme instruído.

**Achados relevantes (registro, sem correção nesta etapa)**:
- O popup é **idêntico para as 3 vagas** e o `<select>` de cargo **não vem pré-selecionado** com
  a vaga que o usuário clicou (sempre abre com "Auxiliar Empresarial", a primeira opção da
  lista) — o candidato precisa lembrar de trocar manualmente.
- O `<select>` de cargo e o campo de upload **não têm `<label>` nem `aria-label`** — defeito de
  acessibilidade (ver §13).
- Este formulário depende do backend de formulários do Elementor Pro (armazenamento/e-mail via
  `admin-ajax.php` do WordPress) — não é algo replicável como está; uma futura implementação
  precisaria de um backend próprio (upload de arquivo + envio de e-mail/registro), fora do
  escopo desta auditoria.

---

## 7. CTA

| Botão | Texto | Destino | Visual | Tamanho | Alinhamento | Interno/Externo |
|---|---|---|---|---|---|---|
| Vaga 1 | "Clique aqui" | Abre popup 442 | Fundo `#61CE70`, texto `#084020`, `border-radius:3px`, padding `12px 24px`, `elementor-size-sm` | Pequeno | Centralizado na célula do card | Interno (popup na própria página) |
| Vaga 2 | "Clique aqui" | Abre popup 442 | Idêntico | Idêntico | Idêntico | Idêntico |
| Vaga 3 | "Clique aqui" | Abre popup 442 | Idêntico | Idêntico | Idêntico | Idêntico |

- **Hover**: `transition: 0.3s` presente no botão (sem classe `elementor-animation-*` — ao
  contrário do CTA "Fale Conosco" da seção Dedicação em `/sobre-nos/`/`/informacoes/`, que usa
  `elementor-animation-bounce-in`). O efeito exato de hover (provavelmente escurecimento leve do
  verde) não foi medido pixel a pixel — comportamento padrão do widget `button.default` do
  Elementor.
- **Breakpoint**: os 3 botões mantêm o mesmo estilo/tamanho em todos os 5 viewports — só a
  largura do card ao redor muda.
- Nenhum botão desta página tem `target="_blank"` nem é link externo — os únicos links externos
  em nova aba são os globais já auditados (endereço no footer → Google Maps, `rel="noopener"`
  sem `noreferrer`, já classificado B em auditorias anteriores).
- Não há nenhum outro CTA nesta página além dos 3 "Clique aqui" — a seção de Benefícios não tem
  nenhum botão/link.

---

## 8. Assets

| Asset | URL original | Formato | Dimensões | Seção | Classificação |
|---|---|---|---|---|---|
| `informacoes.jpg` | `.../wp-content/uploads/2024/09/informacoes.jpg` | JPG | 1200×600 (já confirmado em auditorias anteriores) | Background do Hero | **Já existente no projeto** (`assets/images/pages/informacoes/informacoes.jpg`) — reaproveitável direto, **não precisa ser baixado** |
| `ben01.png` … `ben14.png` | `.../wp-content/uploads/2024/09/ben{01..14}.png` | PNG | Ver tabela §4 (200×200 até 768×512, todas diferentes) | Seção "Nossos Benefícios" | **Novos, necessários** (14 arquivos) — nenhum já existe no projeto |
| `LogoSecundariaColorida02.png` | `.../wp-content/uploads/2024/08/LogoSecundariaColorida02-1024x297.png` | PNG | 1024×297 (via nome do arquivo/srcset; atributos `width`/`height` do HTML — 800×232 — não batem com o arquivo real, inconsistência do próprio WordPress) | Logo dentro do popup de candidatura | **Novo, necessário somente se o popup for reproduzido** — mesma logomarca secundária, verificar se já existe uma cópia equivalente em `assets/images/logo/` antes de baixar |
| Ícones do Hero (engrenagem/gráficos, decorativos) | parte da própria `informacoes.jpg` (não são elementos HTML separados) | — | — | Hero | Não é um asset separado — já incluído na imagem de fundo |

**Nenhum arquivo quebrado ou duplicado** foi encontrado nesta página. **Nenhum download foi
realizado nesta etapa** — dimensões/formato de todos os `ben*.png` foram confirmados via
`getimagesize()` direto na URL remota (sem gravar arquivo local), e os atributos `width`/`height`
do HTML já batiam exatamente com o resultado.

---

## 9. Responsividade

Breakpoints reais medidos (nenhum presumido):

| Elemento | Desktop (≥1024px) | 900×1200 | 768×1024 | **767×1024** | 390×844 |
|---|---|---|---|---|---|
| Hero | 400px, sem mudança | idêntico | idêntico | idêntico | idêntico (sem breakpoint próprio) |
| Cards de vaga | 3 colunas (370px) | 3 colunas (278px) | 3 colunas (234px) | **1 coluna** (722px, empilhado) | 1 coluna |
| Benefícios | 3 colunas (172px de imagem) | 3 colunas (126px) | 3 colunas | **1 coluna** (empilhado total) | 1 coluna |
| Espaçadores vazios | 20px fixos | idêntico | idêntico | idêntico | idêntico |

**Breakpoint de conteúdo confirmado em exatamente 767px** (testado 767 vs 768 diretamente) para
os dois grids desta página (vagas e benefícios) — o mesmo valor já usado em todo o projeto, não
uma configuração nova. **Nenhum overflow horizontal** (`scrollWidth === clientWidth`) em nenhum
dos 5 viewports (1425/1425, 885/885, 753/753, 752/752, 375/375).

Diferente de `/informacoes/`'s "Últimas notícias" (que tinha um estágio intermediário de 2
colunas em tablet), aqui **tanto vagas quanto benefícios pulam diretamente de 3 para 1 coluna**,
sem estágio de 2 — confirmado explicitamente em 900×1200 e 768×1024 (ainda 3 colunas em ambos).

---

## 10. Interações e animações

| Interação | Resultado |
|---|---|
| Hover nos cards de vaga | Nenhuma transição/transform encontrada no card em si (só no botão) |
| Hover no botão "Clique aqui" | `transition: 0.3s` (sem classe de animação especial) |
| Clique em "Clique aqui" | Abre popup modal (Elementor `dialog-widget`) com o formulário de candidatura |
| Hover nas imagens de benefício | Nenhum (não são links, sem `:hover` definido) |
| Animações de entrada por scroll | **Nenhuma** (nenhum `elementor-invisible`/`data-settings` de animação encontrado em nenhuma seção) |
| Accordion/tabs/carrossel | **Nenhum** — não existem nesta página |
| Âncora `#beneficios` | Funcional a partir desta própria página (`href="#beneficios"` relativo); header não é `position:fixed`, então o scroll para a âncora não fica coberto |

---

## 11. Qualidade visual (avaliação objetiva, sem corrigir)

- **Seção de Benefícios sem nenhum tratamento de card**: 14 imagens soltas, sem borda, sem
  fundo, sem sombra, sem `border-radius` — parecem "coladas" na página, não elementos de UI
  desenhados.
- **Inconsistência de estilo entre os 14 benefícios**: mistura logos de marca (fundo colorido,
  tipografia própria de cada marca) com ilustrações caseiras (fundo transparente, fontes
  script/decorativas variadas) — nenhuma paleta, tipografia ou proporção comum entre os itens.
  Isso é visualmente o achado de "cards amadores" mais evidente encontrado até agora no site.
- **Proporção das imagens completamente heterogênea** (de 187×249 a 768×512) exibida numa grade
  de largura fixa (172px) sem `object-fit`/caixa uniforme — produz alturas de célula muito
  diferentes lado a lado (ex.: uma imagem de 92px de altura ao lado de outra de 250px na mesma
  linha), quebrando o alinhamento visual da grade.
- **Nenhum título/legenda em HTML para os benefícios** — o nome de cada benefício só existe
  "gravado" na imagem, o que é ruim tanto para consistência visual (tipografia de cada PNG é
  diferente) quanto para acessibilidade (ver §13).
- **4 seções vazias usadas só como espaçador** (idx 3, 5, 6, 7) — sintoma de um site que usa
  containers em vez de margin/padding para espaçamento; não afeta a aparência final, mas é
  arquitetura amadora que não deve ser copiada.
- **Cards de vaga são o elemento mais bem resolvido da página**: contorno consistente, tipografia
  hierárquica clara (título/rótulo/lista), botão padronizado — não há problema visual relevante
  nesta parte.
- **Popup de candidatura genérico**: não destaca para qual vaga o candidato está se candidatando
  (mesmo título "Faça parte da nossa equipe!" e mesmo `<select>` não pré-selecionado para as 3
  vagas) — ambiguidade de UX, não um bug técnico.

---

## 12. Acessibilidade

- **Hierarquia de headings**: H2 (Hero, eyebrow) → H2 (Hero, título) → H3×3 (títulos das vagas) →
  H2 ("Nossos Benefícios") — sem H1 na página, mesmo padrão já visto em todas as outras páginas
  do site (não específico desta).
- **14 imagens de benefício com `alt=""`**: diferente de casos já vistos de `alt=""` em imagens
  puramente decorativas (ex.: fotos de fundo, thumbnails ao lado de um título linkado), aqui as
  imagens carregam a **única fonte de informação** do benefício (nome/marca) — um leitor de tela
  não tem como saber que existem "14 benefícios" nem quais são. Isto é uma lacuna de conteúdo,
  não só de rótulo.
- **Formulário do popup**: 2 campos sem `<label>`/`aria-label` (o `<select>` de cargo e o
  `<input type="file">` do currículo) — depende só da posição visual/placeholder implícito para
  ser entendido.
- **Links dos cards de vaga ("Clique aqui")**: texto do link não é autoexplicativo por si só
  (identifica a ação "clique aqui" mas não menciona "para qual vaga" — só fica claro pelo
  contexto visual do card ao redor, o que é insuficiente para navegação por lista de links de
  leitor de tela).
- **Âncora `#beneficios`**: é um `<div>` vazio sem `tabindex`, sem papel ARIA — funciona para
  scroll, mas não seria anunciado por um leitor de tela como um marco de conteúdo.
- **Contraste**: título/rótulos dos cards de vaga (`#00222C`/`#057038` sobre branco) têm
  contraste alto, sem problema aparente. O texto embutido nos PNGs de benefício não foi testado
  individualmente (contraste depende de cada imagem).
- **Áreas de toque**: botão "Clique aqui" (`padding:12px 24px`) tem tamanho adequado. As imagens
  de benefício não são clicáveis, então não se aplica área de toque mínima a elas.

---

## 13. Segurança dos links externos

| Link | `target="_blank"`? | `rel`? | Classificação |
|---|---|---|---|
| Endereço (Google Maps, footer) | Sim | Só `noopener` (sem `noreferrer`) | **B** — mesmo padrão já visto/aceito em todas as páginas já auditadas, não específico desta |
| Sistema externo de recrutamento (menu/submenu) | Não (mesma aba) | N/A | **A** — comportamento a reproduzir como está (link funcional, já usado assim em todo o site) |
| "Clique aqui" (popup) | N/A (não é navegação) | N/A | **A** |
| Menu "Benefícios" (`#beneficios`, relativo) | Não | N/A | **B** — funciona só nesta página; comportamento a preservar exatamente como está pois é o mesmo link relativo já usado no header global |
| Footer "Benefícios" (`.../informacoes/#beneficios`) | Não | N/A | **C** — mesmo defeito já conhecido (aponta para a página errada); correção já prevista em `config/menu.php` (usa `/trabalhe-conosco/#beneficios`) |
| Item de menu "Ouvidoria" (`http://`, sem HTTPS) | Não | N/A | **C** — mesmo defeito já conhecido, global, não específico desta página |

Nenhum endpoint administrativo exposto, nenhum redirecionamento suspeito e nenhum link
recém-descoberto quebrado foi encontrado nesta página além dos já catalogados em
`site-inventory.md`.

---

## 14. Dados globais × `config/company.php`

| Dado | Nesta página | `config/company.php` | Divergência? |
|---|---|---|---|
| Sistema externo de recrutamento | `https://recrutamento.ctprice.com.br/vagas` | Idêntico (`sistemas_externos.recrutamento`) | **Não** |
| Telefone fixo | `(67) 3313-7300` | Idêntico | Não |
| WhatsApp da topbar | `(67) 99232-4097` | Canônico é `(67) 99261-6117` | Já documentado (mesmo padrão de todas as páginas internas, não novo) |
| E-mails | `contato@ctpricems.com.br` / `protecaodedados@ctpricems.com.br` | Idênticos | Não |
| Endereço/bairro/CEP | "Monte Castelo", "79.010-190" (footer) | `null`/`null` (pendente, já registrado) | Confirma o TODO já existente, não novo |
| Google Maps embed | Mesma query já vista em outras páginas | Idêntico | Não |

Nenhum departamento, telefone adicional ou rede social exclusivo desta página foi encontrado.

---

## 15. Reutilização arquitetural

- **Hero**: `components/boxed-hero.php` **serve, sem modificação** (Categoria A) — mesmo padrão
  já usado em `/clientes/`, `/parcerias/`, `/fale-conosco/`, `/informacoes/`, reaproveitando o
  asset `informacoes.jpg` já existente.
- **Faixa "Nossos Benefícios"**: o conceito visual (gradiente + título branco centralizado) é o
  mesmo de `components/section-title-band.php`, mas com valores medidos diferentes: altura
  **automática pelo conteúdo** (102px) em vez de fixa (180px no componente atual); container
  1140px (não 1200px); gradiente com stops em 15%/90% (não 0%/100%, mesma direção/cores —
  `--color-dark-teal`/`--color-brand-green`, já definidas em `variables.css`); `font-weight:700`
  (não 800). **Classificação: B — reutilização com modificadores** (viável tornando altura,
  container, stops do gradiente e peso da fonte configuráveis), mas com mais parâmetros do que o
  componente atual aceita hoje — decisão de implementação, não tomada aqui.
- **Cards de vaga**: nenhum componente existente encaixa bem. `flat-icon-box-section.php` é o
  mais próximo conceitualmente (card com contorno, grid de 3 colunas, breakpoint 767px idêntico),
  mas essa página precisa de: texto rico multi-parágrafo com destaques coloridos (não um único
  parágrafo simples), alinhamento à esquerda (não centralizado), um botão por card (o componente
  atual não tem CTA), e nenhum ícone (o componente atual exige um). **Precisaria de um componente
  novo ou de uma extensão not-trivial de `flat-icon-box-section.php`** — decisão para a etapa de
  implementação.
- **Grade "Nossos Benefícios"**: **nenhum componente existente serve.** Não há hoje, no projeto,
  um padrão de "grade de imagens sem título/descrição HTML, larguras heterogêneas". Seria
  necessário um componente novo e simples (grade responsiva de imagens com `alt` textual
  reconstituído a partir do conteúdo visual de cada PNG, resolvendo de quebra o problema de
  acessibilidade §12) — decisão para a etapa de implementação, não tomada aqui.
- **Popup de candidatura**: não há hoje nenhum padrão de modal/popup no projeto, nem um backend
  de upload de arquivo. Seria trabalho novo significativo (formulário com `multipart/form-data`,
  armazenamento ou envio do currículo) — fora do escopo de reprodução simples; decisão de escopo
  para o cliente (ver "Decisão global" no pedido desta tarefa: CMS/backend dinâmico vem depois do
  site público estar completo).
- **`image-text-section.php`/`image-content-cta-section.php`**: **não se aplicam** — não existe
  nenhum bloco de "foto + texto corrido" nesta página.

### Potencial futuro de manutenção/CMS
As 3 vagas e os 14 benefícios são, em espírito, os dois conteúdos desta página mais plausíveis
para um CMS futuro (vagas mudam com frequência natural; benefícios mudam raramente, mas ambos são
dados de conteúdo, não estrutura). **Nenhum CMS foi projetado nesta etapa** — apenas o registro
de que, ao contrário de `/informacoes/` (que só reaproveitava conteúdo já existente), esta página
introduz conteúdo genuinamente próprio que precisaria de uma fonte de dados própria (mesmo que
estática, como um array PHP) quando implementada.

---

## 16. REFERENCE DRIFT

**Nenhuma divergência nova de conteúdo/estrutura** entre o site ao vivo e o que seria esperado
pela documentação. Porém, um ponto exige registro explícito:

### Observação sobre DRIFT-001 ("Área Restrita")
`reference-baseline.md` (DRIFT-001) documenta que o botão "Área Restrita" foi **removido** do
header ao vivo (container vazio), e essa ausência foi **reconfirmada** nas auditorias/validações
de `/parcerias/`, `/fale-conosco/` e `/informacoes/`. **Nesta página, o botão "Área Restrita"
está presente e funcional** no header ao vivo (`href="https://ctprice.com.br/wp/arearestrita/"`,
visível nos 3 screenshots capturados) — o oposto do que as páginas já auditadas mostraram.

Isso **não é um novo DRIFT em relação ao baseline** (o baseline original documentava o botão
como presente — esta página bate com o baseline, não diverge dele). O que fica registrado é uma
**inconsistência do próprio site ao vivo entre suas páginas**: como o header não é um template
compartilhado de verdade (cada página tem sua cópia independente no Elementor, confirmado em
`site-inventory.md`), a remoção do botão parece ter sido aplicada em algumas páginas e não em
outras. **Nenhuma ação**: a implementação já mantém o botão "Área Restrita" em
`includes/header.php` (decisão já tomada, consistente com o baseline e agora também com esta
página) — nenhuma mudança necessária.

---

## Screenshots capturados

- `docs/reference/screenshots/trabalhe-conosco-desktop-1440-full.png`
- `docs/reference/screenshots/trabalhe-conosco-tablet-900-full.png`
- `docs/reference/screenshots/trabalhe-conosco-mobile-390-full.png`

Todos capturados após rolagem completa da página em incrementos de ~400px (metodologia já usada
em auditorias anteriores para evitar lacunas de `background-image`/`<img loading="lazy">` na
captura full-page) — nenhuma lacuna visual encontrada nas 3 capturas finais.
