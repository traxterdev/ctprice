# Auditoria visual — `/fale-conosco/`

Data: 2026-08-30
Referência: `https://ctprice.com.br/wp/fale-conosco/` (`data-elementor-id="480"`, `page-id-480`,
confirmado em `docs/reference/site-inventory.md`)
Documentação-base: `CLAUDE.md`, `docs/reference/reference-baseline.md`,
`docs/reference/site-inventory.md`, `docs/reference/clientes-final-validation.md`,
`docs/reference/parcerias-final-validation.md`, `docs/reference/sobre-nos-final-validation.md`

**Escopo desta etapa: SOMENTE auditoria.** Nenhum arquivo de implementação foi criado ou
alterado. Nenhuma interação destrutiva foi realizada (o formulário não foi efetivamente
submetido).

Viewports inspecionados: 1440×900, 900×1200, 768×1024, 767×1024, 390×844.

---

## 1. Estrutura completa

7 seções de topo confirmadas dentro de `.elementor.elementor-480` (mesma contagem já registrada
em `site-inventory.md`), na ordem:

| # | Elemento (`data-id`) | Função | Altura (1440×900) |
|---|---|---|---|
| 0 | `458fb0d2` | Topbar | 66px |
| 1 | `5df066d5` | Header (logo + menu) | 132px |
| 2 | `4a5aae41` | Hero interno ("fale conosco") | 400px |
| 3 | `9aaac0e` | Bloco foto + texto + formulário | 650px |
| 4 | `ed4ab37` | Faixa de contatos por departamento (5 WhatsApp) | ~96px |
| 5 | `5145ffc2` | Footer (logo, endereço, menu, mapa) | 400px |
| 6 | `30e3fd66` | Bottom bar (copyright) | 78px |

**Não é apenas Hero + formulário** — há uma seção extra e distinta entre o formulário e o footer
(faixa de contatos por departamento, fundo sólido escuro, sem imagem/heading próprios), e a
seção do formulário em si é mais complexa que um formulário isolado: combina foto de fundo,
imagem decorativa sobreposta, texto introdutório e o widget de formulário, tudo em duas colunas.

Altura total da página (1440×900): 1822px (`scrollHeight`). Sem overflow horizontal em nenhum
dos 5 viewports (`scrollWidth === clientWidth`).

Topbar/Header/Footer/Bottom bar são as mesmas cópias já auditadas/implementadas globalmente
(mesmo padrão de `includes/topbar.php`, `includes/header.php`, `includes/footer.php`) — não
reauditados aqui em detalhe, exceto pelos dados de contato específicos desta página (§7).

---

## 2. Hero

**Classificação: A — reutilização direta de `components/boxed-hero.php`.**

| Propriedade | Medido | Igual a `boxed-hero.php`? |
|---|---|---|
| Altura | 400px (idêntico em todos os 5 viewports — sem breakpoint próprio) | Sim |
| Container | `.e-con-inner`, `max-width: min(100%, 1140px)`, 1140px renderizado | Sim (`.boxed-hero__inner`, 1140px) |
| Estrutura textual | Dois `<h2>`: eyebrow "fale conosco" + título "Tire suas dúvidas ou envie sugestões", sem `<strong>` de destaque parcial | Sim (mesmo padrão de duas headings simples) |
| Background | `url(.../pgcontato.jpg)` | Mecanismo idêntico (`url()` inline) |
| `background-size` | `cover` | Sim |
| `background-position` | `50% 50%` | Igual ao padrão já usado em `/clientes/` (valor default do componente) |
| Tipografia eyebrow | Roboto 700, 20px/20px, `rgb(0,34,44)`, uppercase | Idêntico a `/clientes/`/`/parcerias/` |
| Tipografia título | Roboto 700, 30px/30px, `rgb(5,112,56)` | Idêntico |
| Alinhamento | Esquerda (`text-align:start`, `align-items:flex-start`, coluna) | Idêntico |
| Responsividade | Sem mudança de altura/tipografia em nenhum viewport testado (igual ao comportamento já confirmado de `boxed-hero.php`, "sem breakpoint próprio no original") | Idêntico |

Nenhum modificador novo é necessário. `background_position` pode ser omitido na chamada desta
página (usa o padrão `50% 50%` do componente, igual a `/clientes/`) — não precisa do valor `0%
0%` usado em `/parcerias/`.

---

## 3. Formulário — auditoria em profundidade

### 3.1 Contêiner (seção `9aaac0e`, "Bloco foto + texto + formulário")

Estrutura de duas colunas dentro de `.e-con-inner` (`max-width:1200px`):

- **Coluna esquerda** (600×600 em 1440px): imagem decorativa `Isotipolinear.png` (1080×1080,
  logomarca CT Price em contorno/outline verde, fundo transparente) renderizada em 580×580,
  `object-fit:fill`, estática (sem link/animação). Por trás dela, a **própria seção** tem
  `background-image: url(.../maosdadas.jpg)` (foto 816×1080, mãos dadas/aperto de mão),
  `background-size:contain`, `background-position:0% 50%`, `no-repeat`, sobre
  `background-color: rgb(0,34,44)`. O efeito visual é a foto aparecendo por trás/ao redor do
  contorno decorativo.
- **Coluna direita**: texto introdutório centralizado (`"Quer tirar dúvidas ou conversar sobre
  como a CT Price pode ajudar sua empresa a crescer? Entre em contato com a gente!"`, Roboto
  16px/21px, `rgb(254,254,254)`, `<strong>CT Price</strong>` sem estilo diferenciado) + o
  formulário.
- Fundo da seção como um todo: `rgb(0,34,44)` (mesmo `--color-dark-teal` já usado em
  header/footer/faixas de título do projeto).

### 3.2 Campos do formulário (`<form class="elementor-form" method="post">`, sem atributo
`action` — ver §5)

| Ordem | Nome exibido | `name` HTML | `id` | Tipo | Obrigatório | Placeholder | `autocomplete` |
|---|---|---|---|---|---|---|---|
| 1 | Nome | `form_fields[name]` | `form-field-name` | `text` | Não | (nenhum) | Ausente |
| 2 | E-mail | `form_fields[email]` | `form-field-email` | `email` | **Sim** (`required` HTML5) | (nenhum) | Ausente |
| 3 | Empresa | `form_fields[field_6bad421]` | `form-field-field_6bad421` | `text` | Não | (nenhum) | Ausente |
| 4 | Mensagem | `form_fields[message]` | `form-field-message` | `textarea`, `rows="4"` | Não | "Informe como podemos te ajudar" | Ausente |
| — | (reCAPTCHA v3 invisível) | `g-recaptcha-response` | — | oculto | — | — | — |
| 5 | Enviar | — | — | `button[type=submit]` | — | — | — |

Campos ocultos adicionais (padrão Elementor Forms, não visíveis ao usuário): `post_id` (480),
`form_id` (`976bffe`), `referer_title` ("Fale Conosco"), `queried_id` (480). Nenhum campo
honeypot anti-spam visível no HTML estático.

Nenhum `<select>`, nenhum checkbox de consentimento/LGPD, nenhum campo de telefone (diferente do
formulário de contato da Home, que tem campo telefone — ver observação em §13).

Labels acima dos campos (`elementor-labels-above`), Roboto 16px/400, cor `rgb(16,227,107)`
(verde vibrante, diferente do `--color-brand-green` #057038 usado no resto do site).

### 3.3 Estilo visual medido

| Elemento | Medida |
|---|---|
| Input (altura) | 40px |
| Input (padding) | `8px 16px` |
| Input (border) | `1px solid rgb(105,114,125)`, `border-radius:3px` |
| Input (background) | `#FFFFFF` |
| Input (placeholder color) | `rgb(122,122,122)` |
| Textarea (altura) | 96px, `resize:vertical` |
| Espaço entre campos | `margin-bottom:10px` por grupo |
| Botão "Enviar" (tamanho) | ~89×40px |
| Botão (background) | `rgb(97,206,112)` |
| Botão (hover) | `rgb(63,194,22)` (mais escuro/saturado), `transition:0.3s`, sem `transform` |
| Botão (texto) | branco, 15px, weight 500 |

### 3.4 Foco (teclado real, `Tab` + `:focus-visible`)

Campo "Nome" em foco real: `outline:none` (não removido via CSS reset agressivo, apenas não
definido), indicador visual é só `box-shadow: 0 0 0 1px inset rgba(0,0,0,0.1)` +
`border-color` mudando de `rgb(105,114,125)` para `rgb(51,51,51)`. **Indicador de foco fraco** —
ver §13.

### 3.5 Validação visível

Só existe validação nativa do navegador: `email` é o único campo com `required`. Ao tentar
submeter com e-mail vazio, `form.checkValidity() === false` (confirmado via script, sem disparar
envio real) — o navegador bloqueia e mostra seu balão de validação nativo; não há mensagem de
erro customizada da Elementor visível sem tentar o envio completo (não testado para não enviar
dados reais).

### 3.6 reCAPTCHA

**reCAPTCHA v3 invisível** (não v2 com checkbox/imagens). `data-sitekey =
"6Lfqs68qAAAAAFH0odzLFC02EJlV8rISafPwKKWD"`, `data-badge="bottomright"`, `data-size="invisible"`.
O selo padrão do Google ("protegido pelo reCAPTCHA") aparece fixo no canto inferior direito da
tela (`grecaptcha-badge`, `position:fixed`). Nenhuma interação do usuário é exigida antes do
envio — o desafio roda em segundo plano no momento do submit.

### 3.7 O formulário realmente envia?

`method="post"`, **sem `action`** — comportamento padrão do widget Elementor Pro Forms: o envio é
feito via JavaScript (AJAX para `wp-admin/admin-ajax.php`, `action=elementor_pro_forms_send_form`)
pelo bundle `elementor-pro/assets/js/form....bundle.min.js` (confirmado carregado nesta página).
**Não foi disparado um envio real** para não gerar dados de teste — mas a presença desse bundle,
do hidden `form_id`/`post_id`/`queried_id` e do reCAPTCHA configurado indica uma interface
funcional (não decorativa), consistente com o restante do site (mesmo padrão já observado na Home
e em Ouvidoria, ver `site-inventory.md`).

---

## 4. Campos e conteúdo (resumo objetivo)

| Campo | Nome exibido | Tipo | Obrigatório | Tamanho (desktop) | Placeholder | Comportamento mobile |
|---|---|---|---|---|---|---|
| 1 | Nome | texto livre | Opcional | 530px de largura (coluna inteira) | — | 100% da coluna, empilha abaixo do texto introdutório em ≤767px |
| 2 | E-mail | e-mail (validação de formato pelo navegador) | **Obrigatório** | 530px | — | idem |
| 3 | Empresa | texto livre | Opcional | 530px | — | idem |
| 4 | Mensagem | texto longo (`textarea`, 4 linhas visíveis, redimensionável verticalmente) | Opcional | 530×96px | "Informe como podemos te ajudar" | idem, mesma altura |

Nenhum campo tem `maxlength`, `pattern` ou `data-*` de validação customizada além das máscaras
genéricas do plugin `mask-form-elementor` (atributos `data-input_mask`, `data-moneymask-*` etc.
presentes em todos os inputs de texto, mas vazios/sem configuração ativa neste formulário
específico — plugin carregado globalmente, não usado de fato aqui).

---

## 5. Backend atual do formulário

- **Elementor Forms (Elementor Pro)**, confirmado por: classe `elementor-widget-form`,
  `data-widget_type="form.default"`, scripts `elementor-pro/assets/js/webpack-pro.runtime.min.js`,
  `elementor-pro/assets/js/form....bundle.min.js`.
- **Envio via WordPress AJAX** (`wp-admin/admin-ajax.php`), não um endpoint próprio — padrão do
  plugin, não há `action` customizado no `<form>`.
- **reCAPTCHA v3 invisível** integrado ao próprio widget (campo `field_5111897`), não um serviço
  externo à parte.
- **Destino do e-mail**: não é possível confirmar sem acesso ao painel administrativo do
  WordPress (fora do escopo desta auditoria — instrução explícita de não acessar áreas
  administrativas). Presumível que o Elementor Pro Forms está configurado com a ação "E-mail"
  padrão, mas isso não foi e não pode ser verificado publicamente.
- **Redirecionamento após envio**: não testado (evitado para não enviar dados reais). O padrão do
  Elementor Pro é exibir uma mensagem de sucesso inline (sem redirecionar), salvo configuração
  explícita em contrário — não confirmável sem envio real.
- **Webhook**: nenhuma evidência pública de webhook (não visível no HTML/JS carregado no
  cliente).

### Recomendação conceitual para o novo PHP (sem programar)

Uma abordagem simples e coerente com o restante do projeto (PHP puro, sem framework, sem
dependências desnecessárias):

- **Endpoint PHP próprio** (`/fale-conosco/enviar.php` ou similar), recebendo `POST` do mesmo
  formulário, sem depender de nenhum plugin de terceiros.
- **Validação server-side** dos campos (e-mail com `filter_var(FILTER_VALIDATE_EMAIL)`, mensagem
  não vazia), reproduzindo como mínimo o que hoje é obrigatório (e-mail) — decisão de tornar Nome/
  Mensagem obrigatórios também é uma melhoria possível (ver §13), não uma correção de defeito.
- **Honeypot** (campo oculto adicional, ignorado por humanos mas preenchido por bots) como
  proteção anti-spam simples, sem dependência de serviço externo — o site atual não tem honeypot
  algum, só reCAPTCHA.
- **CSRF**: token de sessão/nonce próprio gerado no `GET` da página e validado no `POST` — o
  formulário atual não expõe nenhum token CSRF visível no HTML estático (o Elementor pode
  injetar algo via JS antes do envio, não confirmável sem disparar o envio real).
- **Rate limit** simples (ex.: por IP + janela de tempo, em arquivo/sessão, sem exigir banco de
  dados) para conter abuso, já que o CMS/backend administrativo está fora do escopo desta fase do
  projeto.
- **Envio por SMTP** usando a função nativa `mail()` do PHP ou, se o hosting permitir, SMTP
  autenticado (evitar dependência de biblioteca externa se não for estritamente necessário — a
  função `mail()` nativa pode bastar dependendo do ambiente de produção).
- **CAPTCHA**: manter alguma proteção é razoável dado que o site já usa reCAPTCHA hoje, mas a
  escolha de manter reCAPTCHA v3 (requer chave/conta Google), trocar por alternativa mais simples
  (ex.: honeypot + rate limit apenas) ou omitir é uma decisão do cliente/projeto — categoria D,
  não decidida aqui.
- **Feedback de sucesso/erro**: mensagem inline no mesmo formulário (sem redirecionar), replicando
  a experiência atual percebida pelo usuário, com estados visuais claros de sucesso/erro
  (melhoria de acessibilidade sobre o comportamento atual, que depende só de JS/reCAPTCHA para
  feedback).

Nenhuma dessas escolhas foi implementada nesta etapa — apenas recomendadas.

---

## 6. Segurança e privacidade

| Item | Encontrado | Classificação |
|---|---|---|
| Anti-spam | reCAPTCHA v3 invisível apenas; **nenhum honeypot** | C — pode ser melhorado (honeypot é simples de adicionar e não depende de serviço externo) |
| CSRF | Nenhum token visível no HTML estático (form sem `action`, submissão via AJAX do plugin) | D — depende da abordagem escolhida para o novo backend |
| CAPTCHA | reCAPTCHA v3 invisível, chave pública exposta no HTML (`data-sitekey`) — comportamento normal/esperado do reCAPTCHA (chave pública é para ser pública) | A — não é uma falha, é o funcionamento padrão do serviço |
| Validação | Só client-side/nativa (apenas e-mail obrigatório); sem indicação visual de campo obrigatório (sem asterisco) | C — melhoria de acessibilidade/UX possível |
| Consentimento LGPD | **Nenhum checkbox de consentimento, nenhum texto de LGPD, nenhum link para política de privacidade** em nenhum lugar da página (confirmado por busca textual) | D — decisão do cliente sobre se/como adicionar, não deve ser inventado |
| Exposição de e-mails | `contato@ctpricems.com.br` e `protecaodedados@ctpricems.com.br` expostos em texto puro (`mailto:`) no topbar/footer, sem ofuscação | B — mesmo padrão já usado/aceito em todas as páginas já implementadas do projeto, não é específico desta página |
| Scripts externos | Google reCAPTCHA (`google.com/recaptcha`, `gstatic.com/recaptcha`), Google Maps (footer, já auditado globalmente) | A — necessários para as funcionalidades correspondentes (reCAPTCHA, mapa) |

---

## 7. Dados de contato encontrados × `config/company.php`

| Dado | Nesta página | `config/company.php` | Divergência? |
|---|---|---|---|
| Endereço (logradouro/cidade/UF) | "R. José Antônio, 2.777 ... Campo Grande - MS" | Idêntico | Não |
| Bairro | "Monte Castelo" (topbar e footer) | `null` (pendente — divergência já registrada) | Confirma o TODO já existente, não é novidade |
| CEP | "79.010-190" (footer) | `null` (pendente) | Confirma o TODO já existente |
| Telefone fixo | `(67) 3313-7300` | Idêntico | Não |
| WhatsApp da topbar/footer desta página | `(67) 99232-4097` | Canônico definido é `(67) 99261-6117` | Já documentado (não é uma nova divergência — `global-data-conflicts.md`) |
| E-mails | `contato@ctpricems.com.br`, `protecaodedados@ctpricems.com.br` | Idênticos | Não |
| Google Maps embed | `Vila Rosa Pires, ..., 79002-400` | Idêntico (`google_maps_embed_url`) | Não |
| **Departamentos** — Comercial | `(67) 99232-4097` | `(67) 99232-4097` | **Idêntico, sem divergência** |
| **Departamentos** — Pessoal | `(67) 3313-7301` | `(67) 3313-7301` | Idêntico |
| **Departamentos** — Fiscal | `(67) 3313-7302` | `(67) 3313-7302` | Idêntico |
| **Departamentos** — Contábil | `(67) 3313-7304` | `(67) 3313-7304` | Idêntico |
| **Departamentos** — Central/Empresarial | `(67) 3313-7300` | `(67) 3313-7300` | Idêntico |

**Achado relevante**: os 5 números de departamento já centralizados em `config/company.php`
(`departamentos`) foram confirmados **exatos** contra a única página do site onde esse diretório
de fato aparece — nenhuma correção necessária nesse array. Nenhum dado novo foi encontrado que
ajude a resolver o TODO de bairro/CEP (a página reproduz a mesma divergência "Monte Castelo/
79.010-190" já registrada em `global-data-conflicts.md`, não uma terceira variante).

---

## 8. Mapa

Mesmo iframe global do footer, já auditado/implementado (`google_maps_embed_url` idêntico,
340×200px na versão desktop, responsivo). Nenhuma divergência específica desta página. Não
reauditado em detalhe aqui (instrução explícita de não duplicar auditoria do footer).

---

## 9. Tipografia e layout — conteúdo exclusivo desta página

| Elemento | Fonte | Tamanho | Peso | `line-height` | Cor |
|---|---|---|---|---|---|
| Hero eyebrow | Roboto | 20px | 700 | 20px | `rgb(0,34,44)` |
| Hero título | Roboto | 30px | 700 | 30px | `rgb(5,112,56)` |
| Texto introdutório do formulário | Roboto | 16px | 400 | 21px | `rgb(254,254,254)` |
| Label de campo | Roboto | 16px | 400 | — | `rgb(16,227,107)` |
| Texto de input | Roboto | 15px | 400 | — | `rgb(122,122,122)` (placeholder)/escuro (digitado) |
| Botão "Enviar" | Roboto | 15px | 500 | — | branco sobre `rgb(97,206,112)` |
| Label de departamento | Roboto | 14px | 700 | — | `rgb(16,227,107)` |
| Telefone de departamento | Roboto | 14px | 400 | — | branco |

**Containers**: Hero e formulário usam `max-width:1140px`/`1200px` respectivamente (mesmos
valores já vistos em outras páginas — 1140px é o padrão predominante do site, 1200px já visto no
carrossel de clientes e nas faixas de `/parcerias/`). Faixa de departamentos: `max-width:1200px`,
5 colunas de 240px cada (1440px), sem gap entre colunas (`gap:0px`), `padding:0 10px`.

**Cores de fundo**: `rgb(0,34,44)` (`--color-dark-teal`) na seção de formulário e na faixa de
departamentos — mesma cor já usada no header/footer/faixas de título do projeto, não uma cor
nova.

---

## 10. Responsividade

- **Breakpoint real confirmado: 767/768px** — idêntico ao já usado em todo o projeto (Clientes,
  Parcerias, Sobre Nós). Em 768px as colunas do formulário e da faixa de departamentos ainda
  ficam lado a lado; em 767px o formulário empilha (foto acima, formulário abaixo) e a faixa de
  departamentos empilha em 5 linhas.
- **Hero**: sem breakpoint próprio, 400px e tipografia idênticos em todos os 5 viewports —
  confirmado igual ao já esperado de `boxed-hero.php`.
- **Formulário (900×1200 e 768×1024)**: colunas ainda lado a lado (linha), largura ~442px/376px
  cada. Nenhum overflow.
- **Formulário (767×1024 e 390×844)**: empilha verticalmente — a foto de fundo
  (`background-size:contain`) passa a ocupar a largura toda da seção (antes só ocupava a metade
  esquerda), ficando atrás de todo o formulário, inclusive dos campos "Nome"/"E-mail" — isso
  reduz o contraste dos labels verdes sobre a foto em alguns pontos (ver §13).
- **Faixa de departamentos**: 5 colunas lado a lado até 768px; em ≤767px empilha em 5 linhas de
  ~76px cada (altura total da faixa cresce de ~96px para ~402px).
- **Nenhum overflow horizontal** (`scrollWidth === clientWidth`) em nenhum dos 5 viewports —
  diferente do defeito já conhecido do `internal-hero.php` no site original (que não afeta esta
  página, pois ela usa o padrão `boxed-hero`, sem esse problema).

---

## 11. Interações e animações

| Interação | Resultado |
|---|---|
| Foco por teclado (`Tab`) no campo "Nome" | `:focus-visible` real ativa `box-shadow` sutil + escurecimento da borda — indicador fraco (ver §13) |
| Hover no botão "Enviar" | Muda de `rgb(97,206,112)` para `rgb(63,194,22)`, `transition:0.3s`, sem `transform`/escala |
| Validação nativa | Bloqueia submissão sem e-mail preenchido (balão nativo do navegador) — não testado further para não enviar dados |
| Links de WhatsApp por departamento | `href="https://api.whatsapp.com/send?phone=55..."`, mesmo padrão do resto do site — não clicados (evitar abrir WhatsApp real) |
| Nenhuma animação de entrada por scroll | Confirmado: nenhum elemento desta página tem `elementor-invisible` ou configuração de animação |
| Nenhum accordion/modal | Não encontrado nesta página |
| Mapa (iframe) | Mesmo comportamento já validado globalmente |

---

## 12. Assets exclusivos

| Asset | URL | Formato | Dimensões nativas | Uso | Status |
|---|---|---|---|---|---|
| `pgcontato.jpg` | `.../wp-content/uploads/2024/09/pgcontato.jpg` | JPG | 1200×600 | Background do Hero | Novo (exclusivo desta página) |
| `maosdadas.jpg` | `.../wp-content/uploads/2024/09/maosdadas.jpg` | JPG | 816×1080 | Background da seção de formulário | Novo (exclusivo) |
| `Isotipolinear.png` (+ variantes `-300x300`, `-150x150`, `-768x768`, `-1024x1024`) | `.../wp-content/uploads/2024/09/Isotipolinear*.png` | PNG (transparente) | 1080×1080 (original) | Imagem decorativa sobreposta na seção de formulário | Novo (exclusivo) — mas é só um contorno/logomarca genérica, potencialmente reutilizável em outras páginas se necessário no futuro |
| `LogoPreferencialColorida-*.png` | já usado em todo o site | PNG | — | Logo do header/footer | Já existente/compartilhado |
| Bandeiras de idioma (`pt-br.png`, `en-us.png`, `es.png`) | já usadas em todo o site | PNG | — | Topbar (GTranslate) | Já existente/compartilhado |

Nenhum asset quebrado (404) encontrado nesta página — 94 requisições de rede verificadas, todas
`200`/`301` (redirecionamento esperado do Google Maps) ou `pending` (long-polling de extensão do
antivírus local, não faz parte do site). Nenhum download foi realizado nesta etapa, conforme
instruído.

---

## 13. Melhorias possíveis (somente registro, não implementadas)

- **Indicador de foco fraco**: `outline:none` + `box-shadow` sutil (`0 0 0 1px rgba(0,0,0,0.1)`)
  é pouco perceptível, especialmente para usuários com baixa visão. Um `outline`/`box-shadow` mais
  contrastante seria uma melhoria de acessibilidade genuína.
- **Nenhuma indicação visual de campo obrigatório**: o único campo obrigatório (E-mail) não tem
  asterisco nem `aria-required`; o usuário só descobre ao tentar enviar.
- **Contraste dos labels sobre a foto em mobile**: em ≤767px a foto de fundo cobre toda a largura
  atrás do formulário, incluindo atrás dos labels verdes ("Nome", "Empresa") — contraste reduzido
  em certos pontos da foto (pele/tecido claro).
- **Ausência de `autocomplete`**: nenhum campo (`name`, `email`, etc.) declara `autocomplete`,
  perdendo preenchimento automático do navegador — mesma observação já registrada para o
  formulário de contato da Home.
- **Ausência de honeypot**: única proteção anti-spam é o reCAPTCHA; um honeypot simples é barato
  de adicionar e reduz dependência do serviço do Google.
- **Sem consentimento LGPD explícito no formulário**: apenas o banner de cookies genérico do
  site; não há checkbox/texto específico sobre uso dos dados enviados pelo formulário de contato.
  Decisão do cliente sobre se deseja adicionar.
- **`autocomplete`/semântica**: labels já usam `for`/`id` corretamente (bom), mas nenhum campo
  usa `aria-describedby` para associar o placeholder da Mensagem como dica adicional.
- **Placeholder ausente em Nome/E-mail/Empresa**: só a Mensagem tem uma dica de preenchimento;
  os outros três campos não orientam o usuário sobre o formato esperado.

Nada disso foi corrigido nesta etapa — apenas catalogado para decisão futura.

---

## 14. Arquitetura futura do formulário

Ver recomendação conceitual detalhada na §5 (endpoint PHP próprio, validação server-side,
honeypot, CSRF via token de sessão, rate limit simples, envio via `mail()`/SMTP, feedback inline
de sucesso/erro). Nenhuma escolha de serviço externo desnecessário é proposta — a única decisão
pendente do cliente é se o reCAPTCHA (ou equivalente) deve ser mantido.

---

## 15. REFERENCE DRIFT

Nenhum novo drift identificado nesta página. Reconfirmado (não é uma novidade, apenas consistente
com o já registrado): o botão/widget "Área Restrita" continua ausente do header desta página ao
vivo, mesmo padrão do **DRIFT-001** já documentado em `reference-baseline.md` — não é um drift
adicional específico de `/fale-conosco/`, é o mesmo drift global já registrado se repetindo aqui
como esperado.

Erros de console observados (`CSP report-only` e `CORB` relacionados ao iframe do reCAPTCHA em
alguns reloads) são comportamento normal/externo do Google reCAPTCHA, não um problema do site nem
um drift.

---

## Screenshots capturados

- `docs/reference/screenshots/fale-conosco-desktop-1440-full.png`
- `docs/reference/screenshots/fale-conosco-tablet-900-full.png`
- `docs/reference/screenshots/fale-conosco-mobile-390-full.png`
