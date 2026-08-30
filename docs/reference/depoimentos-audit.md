# Auditoria visual — `/depoimentos/`

Data: 2026-08-30
Referência: `https://ctprice.com.br/wp/depoimentos/` (`data-elementor-id="1839"`, 8 seções,
confirmado em `docs/reference/site-inventory.md`)
Documentação-base: `CLAUDE.md`, `docs/reference/reference-baseline.md`,
`docs/reference/site-inventory.md`, `docs/reference/home-final-validation.md`,
`docs/reference/sobre-nos-final-validation.md`

**Escopo desta etapa: SOMENTE auditoria.** Nenhum arquivo de implementação foi criado ou
alterado. Nenhum asset foi baixado.

Viewports inspecionados: 1440×900, 900×1200, 768×1024, 767×1024, 390×844.

---

## Achado principal (não presumir "Hero + depoimentos da Home")

A página **NÃO reutiliza os 4 depoimentos da Home nem o padrão de carrossel Swiper**. É uma
página inteiramente diferente: **7 depoimentos em vídeo** (não texto puro), apresentados em uma
**grade estática sem paginação** (não um carrossel — todos os 7 ficam visíveis rolando a página),
cada um com foto do cliente, citação curta, uma miniatura de vídeo customizada (com play que abre
lightbox do YouTube) e 2 ícones de rede social (site + Instagram do cliente). **Apenas 1 pessoa
(Mário/Mario Jorge, AgroTouro) aparece em ambas as páginas** — com texto, apresentação e assets
completamente diferentes em cada uma.

---

## 1. Estrutura completa (8 seções de topo, ordem confirmada)

| # | `data-id` | Função | Altura (1440×900) | Container |
|---|---|---|---|---|
| 0 | `5e8a7631` | Topbar | 66px | — |
| 1 | `24492d9f` | Header (logo + menu, "Depoimentos" com sublinhado ativo) | 132px | — |
| 2 | `471ee32e` | Hero ("depoimentos" / "A confiança de quem já conta com a CT Price") | 400px | 1140px |
| 3 | `10f36f2` | Heading "Quem confia, recomenda." + 2 parágrafos de introdução | 249,78px | 1140px |
| 4 | `9378c1c` | **3 depoimentos em vídeo** (Aline Zacarini, Bruno Alessio, Réus Fornari) | 494,75px | 1240px |
| 5 | `d50bb3f` | **4 depoimentos em vídeo** (Mario Jorge, João Francisco, Mário Sérgio Miguel, Walter Ferreira Cruz — 3+1 centralizado) | 993,5px | 1240px |
| 6 | `274e5c27` | Footer (logo, endereço, menu, mapa) | 400px | — |
| 7 | `5d391d19` | Bottom bar (copyright) | 78,39px | — |

Espaçamento entre seções: **0px** entre todas as seções adjacentes (Hero→intro, intro→cards,
cards→cards, cards→footer) — nenhum gap não-zero encontrado nesta página (diferente de
`/ouvidoria/`, que tinha um gap de 25px).

As seções 4 e 5 não são visualmente distintas uma da outra (mesmo fundo branco, sem separador) —
a divisão em duas seções Elementor é só uma particularidade de estrutura interna (3 cards cabem
numa "linha" de container, o 4º precisa de um novo container que then wrap para 3+1); visualmente
o usuário vê uma única grade contínua de 7 cards.

---

## 2. Hero

**Classificação: A — reutilização direta de `components/boxed-hero.php`.**

| Propriedade | Medido | Igual a `boxed-hero.php`? |
|---|---|---|
| Altura | 400px em todos os 5 viewports | Sim |
| Container | `.e-con-inner`, `max-width: min(100%, 1140px)` → 1140px | Sim |
| Estrutura textual | Dois `<h2>`: eyebrow "depoimentos" + título "A confiança de quem já conta com a CT Price", sem `<strong>` | Sim |
| Background | `url(.../informacoes.jpg)` — **mesma imagem já usada em `/informacoes/`, `/parcerias/`, `/trabalhe-conosco/`** — quarta página a reutilizá-la | Mecanismo idêntico |
| `background-size` | `cover` | Sim |
| `background-position` | `0% 0%` (igual a `/parcerias/`, `/trabalhe-conosco/`) | Sim |
| Tipografia eyebrow | Roboto 700, 20px, `rgb(0,34,44)` | Idêntico |
| Tipografia título | Roboto 700, 30px, `rgb(5,112,56)` | Idêntico |
| Responsividade | Sem mudança em nenhum dos 5 viewports | Idêntico |

**Nenhum asset novo é necessário para o Hero** — `informacoes.jpg` já está no projeto
(`assets/images/pages/informacoes/informacoes.jpg`).

---

## 3. Inventário completo dos 7 depoimentos (transcrição literal)

| # | Nome | Citação (texto exato) | Foto | Miniatura de vídeo | URL do vídeo | Site do cliente | Instagram |
|---|---|---|---|---|---|---|---|
| 1 | **Aline Zacarini** | "Contar com a CT price é a certeza de estar sempre com a melhor parceira." | `AlineZacarini-1024x1020.jpeg` | `Thumb_Aline.jpeg` | `youtube.com/watch?v=Vr9EFGVx0T8&list=...` | `http://www.agrososal.com.br/` (HTTP) | `instagram.com/agrososal` |
| 2 | **Bruno Alessio** | "CT Price: nossa parceira há 3 anos, trazendo visão estratégica e gerencial para melhorar nossa performance." | `Bruno-Thumb-1018x1024.png` | `Thumb_Bruno.png` | `youtu.be/Cq4w62rSpyE` | `https://www.soldamaq.com.br/` | `instagram.com/soldamaq/` |
| 3 | **Réus Fornari** | "Há mais de 15 anos com a CT Price: comunicação próxima e o conforto de crescer com segurança." | `Reus-Thumb-1018x1024.png` | `Thumb_Reus.png` | `youtu.be/eyPTwRBjzU0` | `https://cottofigueira.com/` | `instagram.com/cottofigueira/` |
| 4 | **Mario Jorge** | "Há 13 anos com a CT Price, tenho tranquilidade para crescer e desenvolver o meu trabalho." | `ThumbSite-1016x1024.png` | `Thumb-YT.png` | `youtu.be/yNKcg8QHjws` | `https://www.agrotouro.com/` | `instagram.com/agrotouroms/` |
| 5 | **João Francisco** | "Há quase 15 anos, contamos com o trabalho impecável da CT Price, que acompanha o nosso crescimento em cada etapa." | `JF-1024x1024.jpg` | `Thumb-3.jpg` | `youtube.com/watch?v=hVOh8mo_sm0` | `materiais.postofigueira.com.br/postos-figueira` | `http://instagram.com/grupofigueirabr` (HTTP, sem `www`) |
| 6 | **Mário Sérgio Miguel** | "A CT Price inovou e mudou a nossa visão sobre a contabilidade da nossa empresa." | `ThumbMarioCampoDoce-1024x1024.png` | `Thumb-4.png` | `youtube.com/watch?v=3cOzYXDAP7A` | `https://campodoce.com.br/` | `instagram.com/campodocedistribuidora` |
| 7 | **Walter Ferreira Cruz** | "O trabalho da CT Price é excelente. Estamos com eles há 5 anos e estamos muito satisfeitos." | `WhatsApp-Image-2026-07-01-at-11.09.14-1024x1017.jpeg` | `WhatsApp-Image-2026-07-01-at-11.09.14-1.jpeg` | `youtube.com/watch?v=yuv_kedv72I` | *(duplicado — ver §12)* | `instagram.com/saborzitosoficial` |

**Nenhum cargo/empresa é exibido como texto** (diferente da Home, que mostra "ROASTED POTATO",
"AgroTouro" etc. como campo próprio) — a empresa só é identificável indiretamente pela miniatura
de vídeo (que tem o nome/logo da empresa desenhado na própria imagem) e pelo link do site/
Instagram. **Nenhuma estrela/rating** em nenhum dos 7 cards. **Ordem**: exatamente a da tabela
acima (confirmada pela posição no DOM/`top` de cada card).

---

## 4. Comparação com os 4 depoimentos da Home (`components/testimonials-section.php`)

| Depoimento da Home | Aparece em `/depoimentos/`? |
|---|---|
| Edvaldo Cezar Germiniani — ROASTED POTATO | **Não** |
| Mário Jorge — AgroTouro | **Sim, mas com texto e apresentação totalmente diferentes** (ver abaixo) |
| Mauro César Senna — INTELECTA SOLUÇÕES EMPRESARIAIS | **Não** |
| Dieter Augusto Dreyer — PLANER SOLUÇÕES EMPRESARIAIS | **Não** |

**Apenas 1 dos 4 depoimentos da Home tem uma contraparte em `/depoimentos/`** (Mário/Mario Jorge,
mesma empresa AgroTouro) — e mesmo assim:
- **Texto diferente**: Home = `"Quero agradecer à família CT Price pela parceria há mais de 10
  anos. Sempre tivemos um atendimento especial de todos os setores, RH, Fiscal, dentre outros.
  Podemos contar com uma consultoria de alto nível."` × Depoimentos = `"Há 13 anos com a CT
  Price, tenho tranquilidade para crescer e desenvolver o meu trabalho."` — nenhuma frase em
  comum.
- **Assets diferentes**: Home usa `assets/images/testimonials/agrotouro.jpg` (já no projeto) ×
  Depoimentos usa uma miniatura de vídeo customizada nova (`ThumbSite-1016x1024.png`) — nenhum
  arquivo compartilhado.
- **Apresentação diferente**: texto simples em carrossel (Home) × card com vídeo + foto + links
  sociais, sem carrossel (Depoimentos).
- Nenhum dos outros 6 depoimentos de `/depoimentos/` tem qualquer equivalente na Home.

**Nenhum dado é compartilhável entre as duas páginas hoje** — são dois conjuntos de conteúdo
genuinamente distintos, apesar de citarem a mesma empresa em um caso.

### Sobre centralizar os dados da Home em `config/testimonials.php`
Isso é **teoricamente possível e seguiria o mesmo padrão já usado no projeto**
(`config/clients.php`, `config/blog-posts.php`, `config/jobs.php`, `config/benefits.php`) — mas
**não traria nenhum benefício de reuso entre Home e `/depoimentos/`**, já que os conjuntos de
dados não se sobrepõem. A única vantagem seria a convenção arquitetural (dados fora do arquivo de
página), não uma eliminação de duplicação real. Registrado como observação, **nenhuma alteração
feita nesta etapa**.

---

## 5. Estrutura visual

**Não é carrossel, não é masonry** — é uma **grade estática (flex-wrap) sem paginação**, todos os
7 cards visíveis por rolagem normal da página. Sem Swiper, sem biblioteca de carrossel.

| Propriedade | Medido |
|---|---|
| Container das grades (seções 4 e 5) | **1240px** (mesmo valor medido em `/ouvidoria/`, diferente do 1140px do Hero/intro) |
| Layout | `display:flex; flex-wrap:wrap; justify-content:space-evenly` — **não CSS Grid** |
| Colunas (desktop/tablet/768px) | **3 colunas** (372px cada em 1440px; 260px em 900px; 220px em 768px) |
| 4º card da 2ª seção | Quebra para uma 2ª linha, sozinho, **centralizado na coluna do meio** (efeito colateral do `flex-wrap`+`space-evenly`, não uma regra de centralização explícita) |
| Gap entre cards | Não é um `gap` explícito — a distribuição vem de `justify-content:space-evenly` |
| Padding do card | 10px |
| Border/radius do card | Nenhum (`border:none`, `border-radius:0`) |
| Background do card | Transparente (mesmo fundo branco da seção) |
| Sombra do card | `3px 3px 10px rgba(0,0,0,0.5)` — sombra escura (50% opacidade), deslocada, mais "pesada"/datada que o padrão de sombra suave já adotado no projeto (`.logo-card`: `0 2px 6px rgba(0,34,44,0.06)`) |
| Foto do cliente | ~89×89px, `border-radius:5px`, `object-fit:fill` (imagens de origem já são quase quadradas, então a distorção é mínima na prática) |
| Ícone de aspas | `::before` do blockquote, glifo `"` em Times New Roman serifada, cor verde-marca |
| Texto da citação | Roboto 16px/400, `rgb(122,122,122)` (mesmo cinza-corpo do site inteiro) |
| Nome (cite) | Roboto 16px/700, `rgb(84,89,95)` |
| Miniatura de vídeo | 332×186,75px (proporção 16:9), com botão de play sobreposto |
| Ícones sociais | Círculos 30×30px, fundo `rgb(0,114,51)` (verde, próximo mas não idêntico a `--color-brand-green` `#057038`), ícone `rgb(8,64,32)` |
| Alinhamento do texto (blockquote) | Esquerda |
| Navegação/paginação | **Nenhuma** (não há carrossel) |

Heading "Quem confia, recomenda." (seção 3): 30px/700/verde, dentro de uma caixa de largura
própria (467px, auto-dimensionada pelo conteúdo) com `text-align:end` — visualmente o texto do
heading fica alinhado à direita dentro dessa caixa estreita, não é um layout de 2 colunas
(confirmado: `flex-direction:column`, heading empilhado acima do parágrafo, não ao lado).

---

## 6. Carrossel

**Não existe nenhum carrossel nesta página.** Nenhuma biblioteca de carrossel (Swiper ou outra)
carregada, nenhum `slidesPerView`, autoplay, loop, `spaceBetween`, navegação por setas/bolinhas ou
swipe de depoimentos. A comparação direta com o Swiper da Home
(`components/testimonials-section.php`) confirma que são padrões **completamente diferentes**: a
Home usa 1 card visível por vez com transição automática; `/depoimentos/` mostra todos os 7 de
uma vez, numa grade estática.

O único "carrossel" nominal encontrado no HTML da referência (`https://www.youtube.com/watch?v=...&list=PLQhq9pdnKsr86BVYW6xYn51NHHyvGLSZr`,
apenas no vídeo de Aline Zacarini) é uma **playlist do YouTube**, não um carrossel do site — só
afeta o que toca depois que o vídeo do YouTube termina dentro do lightbox.

---

## 7. Qualidade visual (avaliação objetiva)

- **Presentation mais elaborada que outras páginas já auditadas**: as miniaturas de vídeo são
  peças gráficas customizadas e bem produzidas (nome do cliente, cargo/empresa e logo desenhados
  de forma coesa em cada uma) — mais cuidada que os ícones "amadores" já encontrados em
  `/trabalhe-conosco/`.
- **Ícone de "site" incorreto**: o link para o site do cliente usa o **ícone do Google Chrome**
  (`.elementor-social-icon-chrome`, com texto de acessibilidade literal "Chrome") para representar
  "visitar o site" — um mapeamento de ícone genuinamente errado do widget original do Elementor
  (o ícone de navegador não significa "site", é usado aqui por falta de um ícone melhor
  disponível no conjunto padrão), não um link para o navegador Chrome.
- **Sombra de card datada**: `3px 3px 10px rgba(0,0,0,0.5)` é uma sombra escura/pesada,
  inconsistente com o padrão de sombra suave (`0 2px 6px rgba(0,34,44,0.06)`) já adotado em
  `.logo-card` e em outras superfícies "premium" do site reconstruído.
- **Nenhuma indicação textual de empresa/cargo** — diferente da Home (que mostra "ROASTED
  POTATO" etc.), aqui a empresa só aparece dentro da arte da miniatura de vídeo ou implícita no
  link do site — um usuário que não reconhece a marca visualmente (ou que não vê a miniatura,
  ex.: leitor de tela) não sabe qual empresa cada depoente representa.
- **Link duplicado** no card de Walter Ferreira Cruz (site e Instagram apontam para a mesma URL
  do Instagram) — parece um esquecimento de preencher o link do site, não uma decisão.
- **Nenhum heading introduzindo a grade de depoimentos** — a transição do texto "Quem confia,
  recomenda." direto para os cards é um pouco abrupta (mesmo tipo de observação já registrada em
  outras páginas, não um defeito técnico).
- **object-fit:fill na foto do cliente**: mesmo defeito conceitual já visto/corrigido em outras
  páginas (aqui de impacto pequeno, pois as fotos de origem já são quase quadradas).
- Nenhum texto cortado, nenhuma tipografia ilegível, nenhum problema de alinhamento grave
  encontrado.

---

## 8. Assets

| Asset | Formato | Uso | Classificação |
|---|---|---|---|
| `informacoes.jpg` | JPG | Background do Hero | **Já existente** (`assets/images/pages/informacoes/`) — reaproveitável, 5ª página a usá-la |
| `AlineZacarini-1024x1020.jpeg` | JPEG | Foto (card 1) | Novo |
| `Thumb_Aline.jpeg` | JPEG | Miniatura de vídeo (card 1) | Novo |
| `Bruno-Thumb-1018x1024.png` | PNG | Foto (card 2) | Novo |
| `Thumb_Bruno.png` | PNG | Miniatura de vídeo (card 2) | Novo |
| `Reus-Thumb-1018x1024.png` | PNG | Foto (card 3) | Novo |
| `Thumb_Reus.png` | PNG | Miniatura de vídeo (card 3) | Novo |
| `ThumbSite-1016x1024.png` | PNG | Foto (card 4, Mario Jorge) | Novo — **não é o mesmo arquivo** `agrotouro.jpg` já usado na Home para a mesma empresa |
| `Thumb-YT.png` | PNG | Miniatura de vídeo (card 4) | Novo |
| `JF-1024x1024.jpg` | JPG | Foto (card 5) | Novo |
| `Thumb-3.jpg` | JPG | Miniatura de vídeo (card 5) | Novo |
| `ThumbMarioCampoDoce-1024x1024.png` | PNG | Foto (card 6) | Novo |
| `Thumb-4.png` | PNG | Miniatura de vídeo (card 6) | Novo |
| `WhatsApp-Image-2026-07-01-at-11.09.14-1024x1017.jpeg` | JPEG | Foto (card 7) | Novo |
| `WhatsApp-Image-2026-07-01-at-11.09.14-1.jpeg` | JPEG | Miniatura de vídeo (card 7) | Novo |
| Ícones sociais (Chrome/Instagram) | SVG inline (Font Awesome) | Links do card | **Compartilhável** — SVG puro, sem dependência de biblioteca, mesmo padrão já usado no botão WhatsApp global |

**14 imagens novas no total** (2 por card × 7 cards) — nenhuma compartilhada com a Home
(inclusive para o caso de Mario Jorge/AgroTouro, que usa um asset totalmente diferente). Nenhum
asset quebrado ou duplicado encontrado (os dois arquivos do card 7 são fisicamente diferentes,
apenas os *links* de site/Instagram é que estão duplicados — ver §12).

---

## 9. Responsividade

| Elemento | Desktop (≥1024px) | 900×1200 | 768×1024 | **767×1024** | 390×844 |
|---|---|---|---|---|---|
| Hero | 400px, sem mudança | idêntico | idêntico | idêntico | idêntico |
| Grade de depoimentos | 3 colunas (372px) | 3 colunas (260px) | 3 colunas (220px) | **1 coluna** (732px, empilhado) | 1 coluna |
| 4º card (seção 5) | 2ª linha, centralizado | idêntico | idêntico | empilhado na sequência normal | idêntico |

**Breakpoint de conteúdo confirmado em exatamente 767px** (testado 767 vs 768 diretamente) — o
mesmo valor já usado em todo o projeto, não presumido a partir de outra página. **Nenhum
overflow horizontal** (`scrollWidth === clientWidth`) em nenhum dos 5 viewports: 1425/1425,
885/885, 753/753, 752/752, 375/375.

Altura total da página no mobile (390×844): **5975px** — a mais alta entre as páginas já
auditadas, por serem 7 cards empilhados em coluna única, cada um com foto+citação+miniatura de
vídeo+ícones.

---

## 10. Interações

| Interação | Resultado |
|---|---|
| Clique no botão de play da miniatura | Abre um **lightbox real** (modal do Elementor) carregando o vídeo do YouTube via iframe — testado ao vivo, funciona corretamente |
| Fechar o lightbox com `Escape` | **Funciona** — testado ao vivo, o modal fecha |
| Botão "X" / compartilhar dentro do lightbox | Presentes visualmente (não testados a fundo, fora do escopo desta auditoria) |
| Hover na foto do cliente | Classe `elementor-animation-pop` presente (5 das 7 fotos) — efeito de animação padrão do Elementor, não customizado |
| Links de site/Instagram | Navegação normal em nova aba (`target="_blank"`) |
| Animações de entrada por scroll | **Nenhuma** encontrada |
| Autoplay/swipe | Não aplicável (sem carrossel) |

---

## 11. Acessibilidade

- **Hierarquia de headings**: apenas 3 `<h2>` na página inteira (eyebrow, título do Hero, "Quem
  confia, recomenda.") — **nenhum heading para os 7 depoimentos** nem para a seção de cards como
  um todo.
- **16 de 19 imagens sem `alt`** (todas as 14 imagens dos 7 cards — fotos E miniaturas de vídeo —
  mais o logo do header/footer) — proporção pior que outras páginas já auditadas. As miniaturas
  de vídeo, em particular, carregam informação real (nome, cargo, empresa, logo) só de forma
  visual, sem nenhum texto alternativo.
- **Ícones sociais com nome acessível enganoso**: todos os 7 links de "site" são anunciados como
  **"Chrome"** por um leitor de tela (`elementor-screen-only`), não como "site do cliente" ou
  algo equivalente — o mesmo problema já identificado visualmente em §7.
- **Botões de play do vídeo**: `aria-label="Reproduzir vídeo"` presente e correto nos 7 — ponto
  positivo.
- **Nome do depoente semântico**: `<cite>` usado corretamente para o nome do autor da citação.
- **Empresa/cargo não têm nenhuma representação textual/semântica** (nem visível, nem oculta para
  leitor de tela) — só existem visualmente dentro da miniatura de vídeo.
- **Foco/navegação por teclado**: não testado a fundo nesta auditoria (mesmo padrão dos botões de
  play/ícones sociais já usado em outras páginas, sem indicação de comportamento customizado).

---

## 12. Links

| Link | Texto/ícone | `target` | `rel` | HTTPS? | Funciona? |
|---|---|---|---|---|---|
| "Chrome" (site do cliente) ×7 | Ícone Chrome | `_blank` | **ausente** (nenhum `rel`) | 6 de 7 em HTTPS (Aline Zacarini usa `http://`) | Todos 200 |
| Instagram ×7 | Ícone Instagram | `_blank` | **ausente** | 6 de 7 em HTTPS (João Francisco usa `http://instagram.com/...` sem `www`) | Todos 200 |
| Vídeo (YouTube) ×7 | Miniatura + play | Lightbox (não navegação) | N/A | HTTPS | Funciona (testado ao vivo) |

**Achados de segurança**: **nenhum dos 14 links de site/Instagram tem `rel` algum** (nem
`noopener`, nem `noreferrer`) — pior que o padrão já visto em outras páginas (que ao menos tinham
`noopener`). **Classificação: C — defeito conhecido a corrigir** (mesma categoria já aplicada a
outros links `target="_blank"` sem `rel` no projeto).

**Achado de conteúdo**: o card de **Walter Ferreira Cruz** tem os dois links (site e Instagram)
apontando para a **mesma URL do Instagram** — o link de "site" não tem destino próprio, parece um
dado incompleto do conteúdo original, não uma decisão. Registrado, não corrigido nesta etapa.

**Nenhum link quebrado** foi encontrado (todas as 13 URLs únicas retornaram HTTP 200).

---

## 13. Reutilização arquitetural

- **`boxed-hero.php` serve, sem modificação** (Categoria A) — mesmo padrão já usado em 6 páginas
  internas, reaproveitando o asset `informacoes.jpg` já existente.
- **`components/testimonials-section.php` da Home NÃO serve, nem diretamente nem com
  modificador simples** — os dois padrões são estruturalmente incompatíveis: a Home é um
  carrossel Swiper de 1 card com texto puro; `/depoimentos/` é uma grade estática de cards
  foto+vídeo+links sociais. Forçar reuso exigiria adicionar campos (vídeo, links sociais),
  remover o carrossel, e mudar completamente o layout — na prática, um componente novo.
- **Componente novo necessário**: um componente próprio para a grade de depoimentos em vídeo
  (ex.: `video-testimonials-section.php` ou nome equivalente), com dados como array por card
  (foto, citação, nome, URL do vídeo, thumbnail do vídeo, link do site, link do Instagram).
- **CSS/JS novo necessário**: grid/flex-wrap próprio (nenhum componente existente tem esse
  layout), e um mecanismo de lightbox de vídeo (o projeto ainda não tem nenhum lightbox
  implementado — nem em `/clientes/`, que usa lightbox de imagem, nem em nenhuma outra página com
  vídeo). Pode ser reaproveitado conceitualmente o mesmo tipo de interação já usado no lightbox de
  imagens de `/clientes/` (abrir/fechar/Escape), mas adaptado para um player de vídeo em vez de
  imagem — decisão de implementação, não tomada aqui.
- **Heading + parágrafo introdutório** (seção 3): estruturalmente simples o bastante para não
  precisar de componente dedicado — pode ser resolvido com HTML simples na própria página ou um
  componente minúsculo compartilhável, decisão para a implementação.

---

## 14. Potencial futuro de CMS

Depoimentos (principalmente os em vídeo desta página) são um forte candidato a conteúdo dinâmico
futuro:
- **Frequência de atualização**: plausivelmente alta — novos clientes/vídeos tendem a ser
  adicionados ao longo do tempo, diferente de conteúdo institucional estático.
- **Necessidade de ativar/desativar**: sim — um depoimento pode precisar ser removido/pausado
  (ex.: cliente que encerrou a parceria) sem mexer em código.
- **Ordenação**: hoje é só a ordem do array/DOM; um CMS permitiria reordenar sem redeploy.
- **Edição de texto**: a citação é um campo de texto simples, natural para um campo de CMS.
- **Empresa/cargo**: hoje não existe como campo à parte (só na arte da miniatura) — um CMS futuro
  poderia (e provavelmente deveria) adicionar isso como campo estruturado, resolvendo de quebra o
  problema de acessibilidade do §11.
- **Imagem/vídeo**: upload de foto + URL de vídeo + miniatura são exatamente o tipo de campo que
  um painel de CMS gerencia bem.

**Nenhuma arquitetura de CMS foi projetada nesta etapa** — apenas o registro de que esta página,
mais que qualquer outra já auditada, parece um bom candidato a entrar cedo na futura fase de CMS.

---

## 15. REFERENCE DRIFT

Nenhuma divergência de conteúdo/estrutura entre o site ao vivo e nenhum baseline previamente
registrado (primeira auditoria desta página). DRIFT-001 (botão "Área Restrita" ausente do header
ao vivo) foi reconfirmado como **presente** nesta página — mesmo padrão inconsistente entre
páginas já registrado nas auditorias de `/trabalhe-conosco/` e `/ouvidoria/`. Nenhum novo drift.

---

## Screenshots capturados

- `docs/reference/screenshots/depoimentos-desktop-1440-full.png`
- `docs/reference/screenshots/depoimentos-tablet-900-full.png`
- `docs/reference/screenshots/depoimentos-mobile-390-full.png`

Todos capturados após rolagem completa da página em incrementos de ~400px (metodologia já usada
em auditorias anteriores) — nenhuma lacuna visual encontrada nas 3 capturas finais.
