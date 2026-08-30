# Auditoria — `/parcerias/` ("Parceiros")

Data: 2026-08-30
Referência: https://ctprice.com.br/wp/parcerias/ (Elementor `id=638`, **9 seções** de nível
superior — confirma `site-inventory.md`, que já registrava 9 seções mas sem detalhamento visual)
Etapa: **somente engenharia reversa e documentação** — nenhum arquivo de implementação foi
alterado (nenhum componente, CSS, JS, include ou config).

Componentes globais (topbar, header, menu, footer, bottom bar, WhatsApp flutuante, cookie
banner, fontes) **não foram reauditados** — apenas reconfirmados presentes e na mesma posição
relativa das demais páginas.

**Confirmação importante**: esta página **não é apenas "Hero + logos"** — tem 9 seções, com
**duas categorias de conteúdo completamente diferentes** (ferramentas web de acesso a sistemas
via login, e parceiros de negócio propriamente ditos), cada uma com sua própria faixa de título.

---

## 1. Estrutura completa (9 seções, ordem exata)

| # | `data-id` | Função | Y (1440px) | Altura (1440px) |
|---|---|---|---|---|
| 1 | `2f47db80` | Topbar (global) | 0 | 66 |
| 2 | `513ae82d` | Header (global) | 66 | 132 |
| 3 | `47b86b84` | **Hero** ("Parcerias de sucesso") | 198 | 400 |
| 4 | `74bb1f6` | **Faixa de título** — "Ferramentas WEB para os Clientes CT Price" (fundo gradiente) | 598 | 180 |
| 5 | `bb8a97a` | **Grade de Ferramentas Web** (11 itens, links de acesso a sistemas) | 793 | 557,1875 |
| 6 | `50c7cc8` | **Faixa de título** — "Parceiros" (fundo gradiente) | 1365,1875 | 180 |
| 7 | `7dba938` | **Grade de Parceiros** (51 itens, logos de empresas/instituições parceiras) | 1595,1875 | 1748 |
| 8 | `3c0f0c39` | Footer (global) | 3393,1875 | 400 |
| 9 | `2fdbb179` | Bottom bar (global) | 3793,1875 | 78,390625 |

Altura total da página (1440×900): **3871,578125px**. `scrollWidth === clientWidth` = 1425/1425
— sem overflow horizontal.

**Margens verticais entre seções (medidas, não presumidas)**: Hero→Faixa1 = 0px;
Faixa1→Grade-Ferramentas = 15px; Grade-Ferramentas→Faixa2 = 15px; Faixa2→Grade-Parceiros = 50px;
Grade-Parceiros→Footer = 50px; Footer→Bottombar = 0px. **Sem padrão único** — cada seção tem
margem própria configurada individualmente no Elementor (0/15/15/50/50/0), não um sistema de
espaçamento uniforme.

---

## 2. Hero — classificação: **B (reutilização com modificador pequeno)**

| Propriedade | `boxed-hero.php` (Clientes) | Hero de Parceiros |
|---|---|---|
| Altura | 400px | **400px — idêntico** |
| Estrutura | dois `<h2>` (eyebrow + título) | **dois `<h2>` — idêntico** |
| Container | `max-width:1140px` centralizado | **1140px — idêntico** |
| Tipografia do eyebrow | Roboto 700 20px/20px, uppercase, `#00222C` | **idêntico** |
| Tipografia do título | Roboto 700 30px/30px, `#057038` | **idêntico** |
| Alinhamento | esquerda (`start`) | **idêntico** |
| Background | `url()`, `cover` | `url()`, `cover` — **mecanismo idêntico** |
| `background-position` | **`50% 50%`** (hardcoded no CSS do componente) | **`0% 0%`** — **DIFERENTE** |
| Conteúdo | "nossos clientes" / "Conheça algumas empresas..." | "PARCERIAS DE SUCESSO" / "São ainda maiores, quando compartilhado com quem caminha ao nosso lado." |
| Imagem de fundo | `clientes.jpg` | `informacoes.jpg` (**mesmo arquivo já usado na página "Informações"** — reuso de asset, não erro) |
| Responsivo | sem breakpoint (400px fixo) | **sem breakpoint** — confirmado idêntico em 1440/900/390 |

**Conclusão**: estrutura, tipografia, container e comportamento responsivo são **idênticos** ao
`boxed-hero.php` já implementado — porém o `background-position` é `0% 0%` aqui contra `50% 50%`
em Clientes, uma diferença real e medida (não estimada). Como `boxed-hero.php` hoje **fixa**
`background-position:50% 50%` no CSS (não é uma prop configurável), reutilizá-lo tal como está
reproduziria a posição errada do enquadramento da imagem nesta página. **Modificador pequeno
necessário**: tornar `background-position` configurável via prop (ou modificador), sem qualquer
outra alteração estrutural. Não há justificativa para um componente novo — a diferença é de uma
única propriedade CSS.

---

## 3. Inventário — duas categorias distintas

### 3.1 "Ferramentas Web" (11 itens) — **NÃO são parceiros de negócio**

Este bloco é uma grade de **botões de acesso rápido a sistemas/portais** usados pelos clientes da
CT Price (área de login), cada um identificado por um ícone com o logo da CT Price e um rótulo
textual **desenhado dentro da própria imagem** (não é texto HTML separado — não há `alt` nem
heading de apoio):

| # | Rótulo (visual, dentro da imagem) | Arquivo | Link de destino |
|---|---|---|---|
| 1 | Email | `WebMail-3.png` | `server4.acessocpanel.com.br` (webmail) |
| 2 | Ponto | `PontoWeb-3.png` | `autenticador.secullum.com.br` (PontoWeb) |
| 3 | Sindicados e Acordos Coletivos | `Sindicatos-e-Acordo-Coletivos-3-scaled.png` | `app.ineditta.com.br` |
| 4 | Contra Cheque | `Contra-Cheque-Web-3.png` | `centraldofuncionario.com.br/23321` |
| 5 | Gestão de Talentos | `Gestao-de-Talentos-Web-3.png` | `recrutamento.ctprice.com.br/admin/login` |
| 6 | Licenças e Alvarás | `ChatGPT-Image-20-de-ago...png` | `app.propertydocs.com.br` |
| 7 | Conexão Vip | `Conexao-Vip-3.png` | `passport.nibo.com.br` (Nibo — empresa) |
| 8 | Folha | `FolhaWeb-3.png` | `dominioweb.com.br` |
| 9 | Open Finance | `Open-Finance-1.png` | `passport.nibo.com.br` (Nibo — contador) |
| 10 | Hub de Informações | `HUB.png` | `app.hubstrom.com/login` |
| 11 | Hub do Cliente | `ChatGPT-Image-28-de-ago...png` | `portal.hubstrom.com.br/login/@ctpricems` |

Todos os 11 arquivos retornam HTTP 200 (nenhum quebrado). Nenhum tem `alt`. Todos abrem em nova
aba (`target="_blank"`, sem `rel="noopener"`).

### 3.2 "Parceiros" (51 itens) — empresas, órgãos e ferramentas parceiras

Lista real, bastante heterogênea — mistura empresas parceiras propriamente ditas (escritório de
advocacia, consultorias, corretoras), softwares/SaaS contábeis (Onvio, Sieg, Clicksign, Omie,
DocNuvem, Emitte, Insirius, Confere Leão, RunRun.it, Komunic, WebCounter, MakroSystem), **órgãos
públicos e conselhos de classe** (Gov.br, e-Social, Receita Federal/Radar, Sintegra, NFe, FGTS
Digital, JUCEMS, CRC-MS, CFC, COFECI/CRECI, Registro.br, DET/MTE) e até **a própria agência que
desenvolveu o site** (`logo01.png` → `agencialester.com.br`). Lista completa (51 arquivos,
dimensões naturais e destino) capturada nos dados brutos desta auditoria; alguns destaques:

| Arquivo | Dimensão natural | Destino |
|---|---|---|
| `logo-santana-e-haddad.png` | 139×80 | `csh.adv.br` (escritório de advocacia) |
| `logo-multiconsultores.png` | 272×102 | `multiconsultores.com.br` |
| `logo-modelo.png` | 401×351 | **mesmo destino Secullum do item "Sindicados e Acordos Coletivos" da seção Ferramentas Web** |
| `logo-econet4.jpg` | 1439×633 | `econeteditora.com.br` |
| `tech-contratos.png` | 500×500 | **mesmo destino `econeteditora.com.br`** que `logo-econet4.jpg` |
| `agricon-nova-logo-00.png` | 243×109 | **mesmo destino Secullum** que `logo-modelo.png` |
| `auditto.png` | 130×130 | **sem link** (único item sem `<a>`) |
| `logo01.png` | 420×167 | `agencialester.com.br` (a agência desenvolvedora) |

Nenhum dos 51 arquivos retorna 404 (confirmado via `fetch HEAD`). Nenhum tem `alt`. 60 dos 61
links abrem em nova aba; `auditto.png` não tem link algum.

**Inconsistências identificadas** (não corrigidas nesta etapa, apenas registradas):

1. **`logo-modelo.png`** — nome de arquivo sugere um placeholder/template genérico do WordPress
   (não um logo de cliente real), e seu link de destino é o MESMO do acesso "Sindicados e Acordos
   Coletivos" da seção de Ferramentas Web — parece ser um item mal configurado, não uma marca
   parceira de fato. **Classificação: D (decisão dependente do cliente)** — confirmar se este
   item deve ser removido, substituído por um logo real, ou se o link está simplesmente errado.
2. **`agricon-nova-logo-00.png`** também aponta para o mesmo link do Secullum PontoWeb — mesma
   observação do item acima. **D**.
3. **`tech-contratos.png` e `logo-econet4.jpg`** apontam para o mesmo destino
   (`econeteditora.com.br`) — pode ser intencional (duas linhas de produto da mesma empresa) ou
   duplicidade. **D**.
4. **`auditto.png`** sem link algum, único caso — inconsistência de padrão. **C** (defeito
   simples a corrigir: adicionar o link correto quando disponível, ou manter sem link
   deliberadamente).
5. **Nenhum `alt` em nenhum dos 62 logos** (11 + 51) — problema de acessibilidade herdado do
   original. **C — defeito conhecido a corrigir** na implementação (usar o nome da
   empresa/ferramenta como `alt`, já disponível pelo nome do arquivo/rótulo visual).
6. **Todos os links abrem em nova aba sem `rel="noopener"`** — falha de segurança/performance
   comum em sites WordPress antigos (referrer leakage, acesso da nova aba ao `window.opener`).
   **C — defeito conhecido a corrigir**: usar `rel="noopener noreferrer"` na implementação.

---

## 4. Forma de apresentação — CSS Grid em ambas as seções (estruturalmente fragmentado)

**Não é carrossel, não é galeria com lightbox, não são cards** — são grades de imagens/links
simples, sem moldura, diretamente sobre fundo branco.

### 4.1 Grade "Ferramentas Web" (11 itens)

| Propriedade | Valor medido |
|---|---|
| Mecanismo | CSS Grid (`display:grid`), **3 colunas** fixas (`409,859px` cada, largura calculada, não `1fr`) |
| Container | `max-width:1331px` (valor próprio — **não coincide** com nenhum container já usado no site: nem 1140, nem 1200) |
| Gap | 20px (linha e coluna) |
| Altura das células | **variável**, sem card — cada linha tem a altura da imagem mais alta daquela linha (85,25px / 117,78px medidos) |
| `object-fit` | **`fill`** (mas sem distorção visível nestes 11 itens específicos, porque a caixa da imagem já é dimensionada na mesma proporção da imagem original pelo próprio Elementor — risco teórico existe, mas não observado aqui) |
| Borda / radius / sombra / background do item | nenhum — imagem "solta" |
| Alinhamento | imagem à esquerda dentro de cada célula (não centralizada) |
| Hover | `elementor-animation-bounce-in` → `transform:scale(1.2)` no hover/focus/active, easing "bounce" (`cubic-bezier(0.47,2.02,0.31,-0.36)`), 0.5s |

### 4.2 Grade "Parceiros" (51 itens)

| Propriedade | Valor medido |
|---|---|
| Mecanismo | CSS Grid, **5 colunas fixas** (208px cada) |
| Container | `max-width:1140px` (mesmo valor já usado em História/MVV/Dedicação/Clientes) |
| Gap | 20px |
| **Fragmentação estrutural**: os 51 itens **não estão em um único grid** — são **6 blocos de grid separados** empilhados (5+4+5+5+5+27 itens), cada um com `grid-template-columns` idêntico (208px×5) e gap 20px entre blocos também de 20px — visualmente contínuo, mas no DOM são 6 containers `.e-grid` distintos. Reflete como o conteúdo foi editado ao longo do tempo (uploads de 2024 a 2026), não um padrão a reproduzir. |
| Altura das células | variável por linha (120px na maioria; 159px/169px nas linhas com imagens mais altas) — comportamento de `grid` padrão (`align-items:stretch` implícito, cada linha se ajusta ao item mais alto) |
| `object-fit` | **`contain`** — sem corte (diferente da seção de Ferramentas Web) |
| Borda / radius / sombra / background do item | nenhum |
| Hover | mesmo `elementor-animation-bounce-in` (`scale(1.2)`) |

---

## 5. Qualidade visual — problemas identificados

| # | Problema | Classificação |
|---|---|---|
| 1 | Logos de tamanhos/proporções muito variados lado a lado sem moldura — hierarquia visual não intencional (algumas marcas "gritam" mais que outras por acidente de proporção da imagem original, não por importância real) | **C** — defeito de apresentação a corrigir |
| 2 | Hover `scale(1.2)` com easing "bounce" — efeito vistoso/pouco institucional, destoante da linguagem premium já aprovada para logos em `/clientes/` | **C** |
| 3 | Ausência total de padding/moldura ao redor de cada logo — logos "soltos" diretamente sobre fundo branco | **C** |
| 4 | `object-fit:fill` na grade de Ferramentas Web (risco de distorção, embora não observado nos 11 itens atuais) | **C** — preventivo |
| 5 | Falta de `alt` em 100% dos logos | **C** |
| 6 | Links sem `rel="noopener"` | **C** |
| 7 | Itens com destino duplicado/suspeito (`logo-modelo.png`, `agricon-nova-logo-00.png`) | **D** |
| 8 | `auditto.png` sem link | **C** ou **D** (depende de haver ou não um link correto disponível) |
| 9 | Container com 4 larguras diferentes na mesma página (1140 / 1200 / 1331 / 1140) sem sistema único | **B** — comportamento a registrar, não necessariamente "corrigir" (decisão de padronização fica para a implementação) |
| 10 | Fragmentação em 6 sub-grids na seção Parceiros | não é um defeito visual (invisível ao usuário) — apenas uma característica do HTML de origem, sem necessidade de reprodução |

**Avaliação da linguagem visual premium de `/clientes/`** (fundo branco, borda sutil, radius,
sombra, padding generoso, `object-fit:contain`, hover institucional): **seria coerente e
recomendável aplicá-la também em Parceiros** — resolveria diretamente os problemas 1, 2, 3, 4 e 9
acima (uniformiza o tratamento de logos com proporções muito diferentes, substitui o hover
"bounce" por algo mais sóbrio, e dá um invólucro consistente independente do container herdado
do original). **Não foi aplicada nesta etapa**, conforme instruído — apenas registrada como
recomendação para a fase de implementação.

---

## 6. Responsividade

Breakpoint real testado diretamente em 1024/768/767/760/700/600/480 (não presumido):

| Viewport | Colunas — Ferramentas Web | Colunas — Parceiros |
|---|---|---|
| ≥768px (1440/1024/900/768) | 3 | 5 |
| ≤767px (767 até 390) | **1** | **1** |

**Breakpoint real: exatamente 767/768px** para AMBAS as grades simultaneamente — mesmo valor já
padrão no projeto (menu, blog, carrossel de clientes, grade de Clientes). Sem estágio
intermediário (não há 2 colunas em nenhuma faixa testada, diferente da grade de Clientes que usa
3 colunas no tablet). Hero sem nenhum breakpoint (400px fixo em 1440/900/390, igual a Clientes).
Sem overflow horizontal em nenhum dos três viewports obrigatórios nem nos intermediários
testados.

---

## 7. Interações confirmadas

- **Hover**: `scale(1.2)` com easing "bounce" em todos os 62 logos (ver seção 4).
- **Links externos**: 61 dos 62 itens têm link, todos abrindo em nova aba.
- **Nenhum lightbox** (não é widget de galeria — são imagens com link direto).
- **Nenhum carrossel/autoplay/swipe**.
- **Nenhuma animação de entrada** (sem `data-settings` de animação, sem `.elementor-invisible`).

---

## 8. Assets exclusivos

**Não baixados nesta etapa.** Nenhum dos 66 arquivos de imagem desta página é reaproveitado de
Clientes/Home (nomes de arquivo completamente distintos dos 82 já centralizados em
`config/clients.php`) — **exceto** a imagem de fundo do Hero (`informacoes.jpg`), que é
reaproveitada de outra página do próprio site original (não do nosso projeto) e ainda não existe
localmente. Resumo:

| Categoria | Quantidade |
|---|---|
| Novos (Ferramentas Web) | 11 |
| Novos (Parceiros) | 51 |
| Imagem do Hero (nova, mas com precedente de reuso no original) | 1 (`informacoes.jpg`) |
| Reaproveitados de Clientes/Home | 0 |
| Quebrados/404 | 0 |
| Duplicados (mesmo destino, arquivos diferentes) | 2 pares (ver seção 3.2, itens 1–3) |

Todos os nomes de arquivo, dimensões naturais e URLs de destino estão nos dados brutos desta
auditoria (capturados via `naturalWidth`/`naturalHeight`/`href` de cada `<img>`/`<a>`).

---

## 9. Dados que futuramente precisarão ser administráveis (sem projetar banco)

Com base apenas no conteúdo real encontrado, cada item das duas grades precisará, no mínimo, de:

- **nome** (hoje inexistente como texto separado — só embutido na imagem ou inferível do nome do
  arquivo/domínio de destino);
- **logo** (arquivo de imagem);
- **link** (URL de destino externo);
- **categoria** ("Ferramenta Web" vs. "Parceiro" — os dois grupos já existem visualmente na
  página original, cada um com sua própria faixa de título);
- **ordem** (posição dentro da grade);
- **ativo/inativo** (para permitir desativar um parceiro sem apagar o registro).

Não é necessário (nem foi pedido) desenhar schema, criar tabela ou Supabase nesta etapa — apenas
o registro do conteúdo real que uma futura tela de administração precisará cobrir.

---

## 10. Reutilização arquitetural

- **Hero**: `boxed-hero.php` serve com um modificador pequeno (parametrizar
  `background-position`, hoje fixo em `50% 50%` no CSS do componente) — ver seção 2.
  Classificação **B**.
- **`client-logo-card.css`**: **recomendado reaproveitar futuramente** a identidade visual
  (fundo branco, borda, radius, sombra, `object-fit:contain`, hover institucional) — resolveria
  diretamente os principais problemas de qualidade visual encontrados (seção 5). Não aplicado
  nesta etapa.
- **`components/clients-carousel-section.php`**: não se aplica — não há carrossel em Parceiros.
- **`components/clients-grid-section.php`**: não reutilizável diretamente — foi desenhado para
  82 itens homogêneos vindos de `config/clients.php`, com embaralhamento diário e lightbox (nenhum
  dos dois faz sentido aqui: os itens são links externos, não abrem imagem ampliada; não há
  necessidade de "justiça" entre 82 clientes documentada para Parceiros). Porém o MECANISMO de
  grid (CSS Grid, colunas fixas por breakpoint, breakpoint 767/768) é um precedente direto
  reaproveitável conceitualmente.
- **Necessidade de componente(s) novo(s)** (a decidir na etapa de implementação, não decidido
  aqui): (a) um componente de "faixa de título em gradiente" (reutilizável entre as duas
  ocorrências desta própria página — mesmo padrão visual, heading maior/menor e texto diferentes)
  e (b) um componente de "grade de logos com link externo" — possivelmente reaproveitando a
  identidade visual de `client-logo-card.css`, mas com estrutura própria (sem lightbox, com
  `target="_blank" rel="noopener"`, e suportando duas categorias/duas instâncias na mesma
  página).

---

## REFERENCE DRIFT

Nenhum. Não existe baseline previamente congelado para `/parcerias/` além do registro breve em
`site-inventory.md` ("Elementor id=638, 9 seções... não auditado visualmente em detalhe") — esta
auditoria confirma as 9 seções e fornece o detalhamento visual que ainda não existia, sem
contradizer nada já congelado.
