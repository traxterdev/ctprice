# Auditoria — Página "A CT Price" (`/sobre-nos/`) — ctprice.com.br/wp/sobre-nos/

## Metodologia

- **URL analisada:** https://ctprice.com.br/wp/sobre-nos/
- **Viewports:** 1440×900, 900×1200 (com testes pontuais em 767/768 para confirmar breakpoints
  de conteúdo), 390×844
- **Ferramenta:** Chrome DevTools MCP (`getBoundingClientRect`, `getComputedStyle`, DOM,
  `document.styleSheets`, `data-settings`, rede)
- **Escopo:** somente o conteúdo exclusivo desta página (entre header e footer). Topbar, header,
  menu, footer, bottom bar, WhatsApp flutuante, cookie banner e fontes **não foram reauditados**
  — já implementados e aprovados como componentes globais.
- Todos os valores abaixo foram **medidos**, não estimados.

Screenshots de referência (página inteira): `docs/reference/screenshots/sobre-nos-desktop-1440-full.png`,
`docs/reference/screenshots/sobre-nos-tablet-900-full.png`, `docs/reference/screenshots/sobre-nos-mobile-390-full.png`.

---

## 1. Estrutura completa (entre header e footer)

6 containers de nível superior próprios da página, nesta ordem (confirmada no DOM, `elementor-377`):

| # | `data-id` | Conteúdo | Altura (1440px) |
|---|---|---|---|
| 1 | `2a7a4e8` | **Hero interno** — eyebrow + título sobre imagem de fundo | 640px |
| 2 | `4b3e460` | Imagem + texto institucional (história da empresa) | 524,78px |
| 3 | `feba983` | Heading + grid de 3 icon-boxes: Missão / Visão / Valores | 445,59px |
| 4 | `ffaf33c` | Imagem + heading + texto + CTA, fundo em gradiente | 457,38px |
| 5 | `29e3a05` | **Carrossel de logos de clientes/parceiros** (idêntico ao da Home) | 200px (cresce em telas estreitas, ver seção 5) |

Depois do container 5 vêm diretamente o footer principal (`43f59478`) e o bottom bar
(`55d4b35c`) — sem nenhuma seção adicional. Nenhuma seção foi ocultada, reordenada ou duplicada
entre os três viewports (mesma ordem em 1440/900/390).

---

## 2. Hero interno

### Estrutura
Container full-width (`e-con-full`) com `background-image` na própria seção (não é slider/Swiper
— **estático**, sem `data-settings` de carrossel, sem classe `swiper` em nenhum elemento
interno). Dois containers-filho lado a lado: o primeiro contém eyebrow (H2) + título
(text-editor), o segundo é um `<div>` vazio (sem conteúdo, sem `background-image` própria —
serve apenas para empurrar o texto para a metade esquerda e deixar a metade direita da imagem de
fundo visível sem sobreposição de texto).

### Medições (1440×900)
| Propriedade | Valor |
|---|---|
| Altura da seção | **640px** (idêntica em 900×1200 e 390×844 — ver "Responsividade" abaixo) |
| Largura da seção | 1425px (full-bleed, igual à Home) |
| `padding` da seção | `10px` |
| `background-image` | `url(.../2024/07/img01.jpg)` — 1200×600px nativos (JPG) |
| `background-size` / `position` / `repeat` | `cover` / `0% 0%` (ancorado no canto superior-esquerdo) / `repeat` (irrelevante com `cover`) |
| Overlay de cor | **nenhum** (`::before`/`::after` sem `content`, sem camada extra) |
| Coluna de texto (`margin-left`) | `200px` fixo (não responsivo — ver defeito na seção 8) |
| Largura da coluna de texto | 602,5px (desktop) |
| Eyebrow ("confie na ct price") | Poppins, 20px, peso 700, `line-height:20px`, `text-transform:uppercase`, `letter-spacing:-1.2px`, cor `#00222C` |
| Título ("Ética, agilidade, segurança nos processos e respeito ao cliente.") | Roboto, 40px, peso 400, `line-height:35px`, cor `#057038`; trecho "Ética, agilidade, segurança" em `<strong>` (peso 700, mesma cor) |
| Alinhamento do texto | `text-align:start` (esquerda) — diferente da Home, que centraliza |
| Animação | nenhuma (`data-settings` sem chave de animação; sem classe `elementor-invisible`) |

### Reusabilidade como componente
**Parcialmente genérico.** Estrutura (eyebrow + título sobre imagem de fundo full-bleed, texto
alinhado à esquerda numa coluna com margem fixa) é simples e repetível — mas os valores medidos
(imagem 1200×600, `margin-left:200px` fixo, título 40px) são **específicos desta página e
quebrados no mobile** (ver seção 8). Antes de virar componente reutilizável (`internal-hero`),
qualquer nova página interna auditada precisa confirmar se usa a mesma estrutura E o mesmo bug —
ou se esse padrão varia por página. Recomendação: tratar como candidato a componente, mas **não
promover a componente compartilhado até auditar pelo menos mais 1–2 páginas internas** e decidir
ali mesmo se a correção do bug de mobile (Categoria C, já com um padrão de fix comprovado no Hero
da Home) deve ser parte do componente-base ou aplicada por página.

---

## 3. Conteúdo institucional (mapeado verbatim, não reescrito)

### Seção 2 — História (imagem + texto, `data-id="4b3e460"`)
Imagem: `Sala-de-reunioes.jpg` (foto de sala de reunião), sem heading próprio nesta seção.
Texto (Roboto 16px/400/24px, cor `#7A7A7A`, alinhado à esquerda):

> A **CT PRICE** iniciou suas atividades desejando inserir no mercado uma organização de
> serviços contábeis que passaria aos seus clientes os princípios para garantir segurança,
> fidelidade, atendimento aos preceitos legais e à ética profissional.
>
> • Sempre atenta a novas tecnologias e as atualizações em todos os seus setores, nossa equipe
> conta com profissionais altamente qualificados e que são frequentemente treinados, no intuito
> de melhorar o atendimento aos nossos clientes em todas as áreas: contábil, fiscal, tributária,
> empresarial, rural e consultoria organizacional e financeira.
>
> • Estamos aptos a atender prontamente as suas necessidades também quanto às modificações
> legais e tributárias em quaisquer áreas, seja federal, estadual ou municipal.
>
> • Buscamos, incansavelmente, ouvir nossos clientes para criar soluções rápidas e efetivas, em
> sinergia com os nossos departamentos. Por isso, estamos sempre comprometidos com a qualidade e
> com a ética, para mantermos nosso atendimento em constante melhoria, sendo reconhecidos pelos
> nossos clientes por essa prontidão.

Os marcadores "•" são **caracteres literais** dentro do texto (não `<ul><li>`), separados por
`<br><br>` — confirmado no HTML-fonte.

### Seção 3 — Missão / Visão / Valores (`data-id="feba983"`)
Heading: "Deixe a contabilidade nas mãos de quem entende!" (mesmo texto usado como heading da
seção "Nossos Serviços" da Home — reutilização de conteúdo, não um erro).

| Item | Ícone | Título | Texto |
|---|---|---|---|
| 1 | `e-fas-dharmachakra` (roda/engrenagem) | Nossa Missão | "Nossa razão de ser está pautada em uma organização de serviços contábeis que promove aos nossos clientes segurança e fidelidade, baseado aos preceitos legais exigidos e também à ética profissional." |
| 2 | `e-far-eye` (olho) | Nossa Visão | "Ser uma empresa de referência na área de Gestão Contábil, reconhecida como a melhor opção por clientes, colaboradores e fornecedores, pela qualidade de nossos serviços, soluções rápidas e bons atendimentos." |
| 3 | `e-far-gem` (gema) | Nossos Valores | "Ética, Agilidade, Segurança, Valorização e respeito aos nossos clientes e colaboradores. São as pessoas o grande diferencial para que tudo se torne sempre possível." |

### Seção 4 — Dedicação/Compromisso (`data-id="ffaf33c"`, fundo em gradiente)
Imagem: `01-1024x684.jpg` (foto de dois profissionais em reunião). Heading (Poppins 32px/600,
`line-height:28px`, cor `#FEFEFE`):

> **Dedicação** aos resultados e **Compromisso** com nossos clientes.

(trechos "Dedicação" e "Compromisso" em `<span style="color:#10E36B;font-weight:bold">`)

Texto (Roboto 16px/400/24px, cor `#FEFEFE`, trechos em verde `#10E36B` negrito via `<strong><span>`):

> Temos um **compromisso** com os resultados excepcionais e total dedicação ao sucesso dos
> **nossos clientes**.
>
> **Trabalhamos incansavelmente** para atender suas necessidades e superar expectativas,
> garantindo que cada detalhe seja tratado com o **máximo cuidado e eficiência**.

CTA: botão "Fale Conosco" → `https://ctprice.com.br/contato` (**link quebrado — ver seção 8**).

### Seção 5 — Carrossel de logos
Sem texto/heading — apenas o carrossel (ver seção 6).

---

## 4. Medições principais (resumo)

| Elemento | Container | Colunas | `font-family` | Cores principais |
|---|---|---|---|---|
| Hero | full-width, 1425px | 2 (602,5 / 602,5, 1 vazia) | Poppins (eyebrow) / Roboto (título) | `#00222C`, `#057038` |
| História | boxed, **1140px** (não 1200) | 2 × 570px | Roboto | `#7A7A7A` |
| Missão/Visão/Valores | boxed, 1140px | grid 3 × 360px, gap 20px | Poppins (heading/título) / Roboto (texto) | `#057038`, `#00222C` (ícone), `#10E36B` (título do item), `#7A7A7A` |
| Dedicação/CTA | boxed, 1140px, `margin:0` | 2 × 570px | Poppins (heading) / Roboto (texto/botão) | `#FEFEFE`, `#10E36B`, gradiente `linear-gradient(90deg, #00222C 16%, #057038 100%)` |
| Carrossel de logos | boxed, 1200px (`--container-width`) | Swiper, 10/2/1 slides | — | fundo `#F2F2F2` |

Margens verticais entre seções (`margin` do container, medidas): História `35px 0` (valor
próprio, diferente dos 25/50 usados na Home), Missão/Visão/Valores `50px 0`, Dedicação/CTA `0`,
Carrossel `0` (herda o padrão já usado na Home). Nenhuma seção tem `border`, `border-radius` ou
`box-shadow` própria — os icon-boxes de Missão/Visão/Valores são **planos, sem borda/cartão**
(um terceiro padrão de icon-box, diferente do círculo com borda da seção "Bem-vindo" da Home e do
cartão com borda da seção "Nossos Serviços").

Botão "Fale Conosco" (seção Dedicação): `background:#61CE70`, `color:#084020`,
`border-radius:3px`, `padding:12px 24px`, Roboto 15px/500 — **idêntico ao componente de botão
preenchido já usado na Home** (`.btn--filled` equivalente), com a mesma classe de animação de
hover `elementor-animation-bounce-in`.

---

## 5. Responsividade

**Não foram presumidos os breakpoints da Home.** Testado diretamente:

- **Hero interno:** **sem nenhum breakpoint** — altura (640px), `margin-left` (200px fixo) e
  `font-size` do título (40px) permanecem **idênticos em 1440/900/390px**. Isso causa o defeito
  descrito na seção 8 (não é um comportamento responsivo ausente por design; é ausência total de
  qualquer regra `@media` para este widget).
- **Seção "História" (imagem + texto):** empilha em **`max-width:767px`** — confirmado testando
  767px (empilhado) e 768px (ainda 2 colunas lado a lado) — mesmo breakpoint de conteúdo já usado
  na Home, mas confirmado independentemente para esta seção, não presumido.
- **Carrossel de logos:** mesmo widget "Image Carousel" da Home, mesmo `data-settings`
  (`slides_to_show:10`), mesmo comportamento de redução de slides visíveis em telas estreitas
  (o `min-height:200px` da seção é mantido, mas a altura real cresce para acomodar as imagens
  quando a largura do slide aumenta — mesmo padrão já documentado e já corrigido na
  implementação da Home).

---

## 6. Assets exclusivos desta página

| Asset | URL original | Dimensões nativas | Formato | Uso |
|---|---|---|---|---|
| Imagem de fundo do Hero | `https://ctprice.com.br/wp/wp-content/uploads/2024/07/img01.jpg` | 1200×600 | JPG | `background-image` do Hero interno |
| Foto da sala de reunião | `https://ctprice.com.br/wp/wp-content/uploads/2024/09/Sala-de-reunioes.jpg` | 1920×1280 | JPG | seção "História" |
| Foto de reunião (Dedicação) | `https://ctprice.com.br/wp/wp-content/uploads/2024/08/01-1024x684.jpg` (variante `-large`; original teria até 2048px via `srcset`) | 800×534 (variante carregada) | JPG | seção "Dedicação/Compromisso" |
| Ícone Missão | `e-fas-dharmachakra` (Font Awesome, SVG inline gerado pelo Elementor) | 80×80px renderizado | SVG | icon-box "Nossa Missão" |
| Ícone Visão | `e-far-eye` | 80×80px renderizado | SVG | icon-box "Nossa Visão" |
| Ícone Valores | `e-far-gem` | 80×80px renderizado | SVG | icon-box "Nossos Valores" |

O carrossel de logos (seção 5) **não tem asset exclusivo** — reutiliza exatamente as mesmas 85
imagens (82 válidas + 3 quebradas) já baixadas para `assets/images/clients/home-carousel/` na
implementação da Home. Nenhum download foi feito nesta etapa (conforme instrução).

---

## 7. Animações

- **Hero:** nenhuma (confirmado — sem `data-settings` de animação, sem classe
  `elementor-invisible`).
- **Icon-boxes (Missão/Visão/Valores):** ícone com classe `elementor-animation-pop` — efeito de
  **hover** (cresce/"pop" ao passar o mouse), mesma biblioteca de animação de hover já usada em
  outros ícones da Home (não é scroll-reveal).
- **Botão "Fale Conosco":** classe `elementor-animation-bounce-in` — efeito de **hover**, mesma
  classe já usada no CTA da Home.
- **Nenhuma animação de entrada por scroll** (`fadeIn`/`fadeInLeft`/`fadeInRight`/`fadeInUp`) foi
  encontrada em nenhuma das 5 seções desta página — diferente da Home, que usa essas animações em
  várias seções.

---

## 8. Defeitos identificados (classificados)

| # | Defeito | Categoria | Descrição |
|---|---|---|---|
| 1 | Hero interno sem responsividade | **C — defeito conhecido a corrigir** | `margin-left:200px` fixo e título `font-size:40px` sem redução em nenhum breakpoint causam quebra de linha extrema no mobile (confirmado visualmente em 390px — texto ocupa ~315px de altura em uma coluna de 150px úteis). Mesmo padrão de bug já corrigido no Hero da Home (padding responsivo + redução tipográfica) — mesma estratégia de correção deve se aplicar aqui quando a página for implementada. |
| 2 | CTA "Fale Conosco" (seção Dedicação) aponta para `https://ctprice.com.br/contato` | **C — defeito conhecido a corrigir** | Mesmo link quebrado já documentado e corrigido na Home (`architecture-proposal.md`, seção 2) — resolver com o mesmo destino já padronizado (`/fale-conosco/`) quando implementado. |
| 3 | 3 logos do carrossel retornam 404 (`mv.jpg`, `modelo.jpg`, `logo_0020_Camada16.jpg`) | **C — defeito conhecido, já resolvido** | Idênticos aos 3 já identificados e não-reproduzidos na implementação da Home — a mesma lista de 82 logos válidos já baixada pode ser reaproveitada sem nova investigação. |
| 4 | Número de WhatsApp do topbar nesta página (`(67) 99232-4097`) diverge do número canônico já usado na Home (`(67) 99261-6117`) | **D — decisão dependente do cliente** (já registrado) | Mesma classe de divergência de dados globais já documentada em `docs/reference/global-data-conflicts.md` — a implementação já usa um número canônico único (`config/company.php`) e não deve replicar esta variação por página. |
| 5 | "Coluna vazia" no Hero (segundo container-filho sem conteúdo) | **B — comportamento a preservar** | Não é um erro: é o mecanismo usado para deixar metade da imagem de fundo visível sem texto sobreposto. Deve ser reproduzido como estrutura (ex.: uma coluna de texto + um espaço reservado), não como bug. |
| 6 | Reutilização do texto "Deixe a contabilidade nas mãos de quem entende!" (heading da seção Missão/Visão/Valores, idêntico ao heading da seção "Nossos Serviços" da Home) | **A — fidelidade obrigatória** | Não é um erro de conteúdo — é o texto real usado nas duas páginas no site original; deve ser reproduzido como está, sem "corrigir" para um texto diferente. |

Nenhuma imagem quebrada foi encontrada nas imagens exclusivas desta página (Hero, sala de
reunião, foto de dedicação) — apenas as 3 já conhecidas do carrossel de logos (item 3).

---

## 9. Componentes reutilizáveis vs. padrões novos

### Podem reutilizar componentes já existentes (sem nenhuma alteração)
- **Carrossel de logos de clientes/parceiros** (seção 5): é **literalmente o mesmo widget**, com
  o mesmo `data-settings` (`slides_to_show:10`, `autoplay_speed:5000`, `speed:500`,
  `image_spacing_custom:20px`) e a mesma sequência de 85 imagens (82 válidas + 3 quebradas) já
  implementado em `components/clients-carousel-section.php` para a Home. Reaproveitável **sem
  nenhuma mudança** — mesmo componente PHP, mesmo CSS, mesmo JS de inicialização.
- **Botão preenchido "Fale Conosco"**: mesmo estilo (`#61CE70`/`#084020`/`radius:3px`/`padding:12px 24px`,
  hover bounce-in) já usado na Home — reaproveitável via a mesma classe de botão já estabelecida.

### Padrões que parecem novos (candidatos a futuros componentes compartilhados, não implementar ainda)
- **Hero interno estático** (imagem de fundo full-bleed + eyebrow + título alinhado à esquerda,
  sem carrossel): estruturalmente diferente do Hero da Home (que é um slider Swiper com 4 slides,
  texto centralizado). Se outras páginas internas usarem o mesmo padrão, vale a pena promovê-lo a
  `components/internal-hero.php` — mas só após confirmar em pelo menos mais 1–2 páginas internas
  (ver seção 2).
- **Icon-box "flat"** (ícone SVG colorido de 80×80px, sem borda/círculo, título Poppins 28px/600
  colorido, texto Roboto 16px/400 cinza, sem cartão): um terceiro padrão de icon-box, distinto dos
  dois já implementados na Home (círculo com borda em "Bem-vindo"; cartão com borda em "Nossos
  Serviços"). Candidato a um componente `icon-box--flat` compartilhado se o padrão se repetir em
  outras páginas.
- **Bloco "imagem + texto institucional"** (2 colunas, sem heading, container 1140px): padrão
  simples que pode se repetir em outras páginas de conteúdo institucional (ex.: possivelmente
  "Informações"). Vale reavaliar como componente após auditar mais páginas.
- **Bloco "imagem + heading + texto + CTA com fundo em gradiente"**: também pode se repetir,
  mas com um detalhe específico (o gradiente `90deg, #00222C 16%, #057038 100%`) que precisa ser
  confirmado como reutilizado (ou próprio desta página) antes de generalizar.

---

## Arquivos de referência visual

- `docs/reference/screenshots/sobre-nos-desktop-1440-full.png`
- `docs/reference/screenshots/sobre-nos-tablet-900-full.png`
- `docs/reference/screenshots/sobre-nos-mobile-390-full.png`
