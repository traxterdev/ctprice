# Auditoria visual — `/ouvidoria/`

Data: 2026-08-30
Referência: `https://ctprice.com.br/wp/ouvidoria/` (`data-elementor-id="1345"`, 8 seções, confirmado
em `docs/reference/site-inventory.md`)
Documentação-base: `CLAUDE.md`, `docs/reference/reference-baseline.md`,
`docs/reference/site-inventory.md`, `docs/reference/fale-conosco-audit.md`,
`docs/reference/fale-conosco-final-validation.md`, `docs/reference/trabalhe-conosco-final-validation.md`

**Escopo desta etapa: SOMENTE auditoria.** Nenhum arquivo de implementação foi criado ou
alterado. Nenhum asset foi baixado. **Nenhuma manifestação real foi enviada** — a única
interação de teste com o formulário foi um clique no botão "Enviar" com todos os campos vazios,
que a validação nativa do navegador (`required`) bloqueou antes de qualquer requisição de rede
(confirmado: nenhum `POST` para `admin-ajax.php` ocorreu; as únicas chamadas de rede disparadas
foram do ciclo de vida interno do reCAPTCHA invisível do Google, sem dado de formulário).

Viewports inspecionados: 1440×900, 900×1200, 768×1024, 767×1024, 390×844.

---

## Achado principal

Ao contrário do que se poderia presumir ("Hero + formulário"), a página tem **4 seções de
conteúdo distintas** entre Hero e footer: um bloco "texto + foto" institucional (fundo escuro),
um bloco "texto + texto" (confidencialidade + "quando utilizar"), e só então o bloco que reúne
heading + texto + botão de WhatsApp exclusivo + o formulário propriamente dito. **O formulário
exige identificação completa (Nome, Contato, E-mail, Empresa todos obrigatórios) e não oferece
nenhuma opção de manifestação anônima** — um achado relevante para um canal de "denúncias/
reclamações". Também não há nenhum texto ou link de política de privacidade/LGPD em nenhum
lugar da página, apesar de coletar dados pessoais e permitir upload de arquivo.

---

## 1. Estrutura completa (8 seções de topo, ordem confirmada)

| # | `data-id` | Função | Altura (1440×900) | Container |
|---|---|---|---|---|
| 0 | `1e664b61` | Topbar | 66px | — |
| 1 | `2fd7238d` | Header (logo + menu, "Ouvidoria" com sublinhado ativo) | 132px | — |
| 2 | `468aaa77` | Hero ("ouvidoria" / "Na CT Price, sua voz é nossa prioridade.") | 400px | 1140px |
| 3 | `5eba72d` | Texto institucional + foto (fundo `#00222C`, full-bleed) | 485px | **1240px** |
| 4 | `838af24` | Texto (confidencialidade) + texto ("Quando utilizar a Ouvidoria?" + lista) | 480px | **1240px** |
| 5 | `821402c` | Heading + texto + botão WhatsApp exclusivo + **formulário** (fundo `#00222C`) | 531px | 1140px |
| 6 | `7c52a34` | Footer (logo, endereço, menu, mapa) | 400px | — |
| 7 | `39d7e2e0` | Bottom bar (copyright) | 78,39px | — |

Espaçamento entre seções: **0px** entre todas as seções adjacentes, exceto **25px** entre a
seção 4 ("Quando utilizar") e a seção 5 (formulário) — único gap não-zero da página, medido, não
presumido.

**Nenhuma seção é "apenas Hero + formulário"** — o formulário é precedido por dois blocos de
texto institucional substanciais (juntos, 965px de altura em 1440×900, mais que o dobro da
altura do próprio bloco do formulário).

---

## 2. Hero

**Classificação: A — reutilização direta de `components/boxed-hero.php`** (mesma estrutura já
usada em `/clientes/`, `/parcerias/`, `/fale-conosco/`, `/informacoes/`, `/trabalhe-conosco/` —
não o padrão `internal-hero.php`, que usa 640px/coluna assimétrica).

| Propriedade | Medido | Igual a `boxed-hero.php`? |
|---|---|---|
| Altura | 400px em todos os 5 viewports (sem breakpoint próprio) | Sim |
| Container | `.e-con-inner`, `max-width: min(100%, 1140px)` → 1140px | Sim |
| Estrutura textual | Dois `<h2>`: eyebrow "ouvidoria" + título "Na CT Price, sua voz é nossa prioridade.", sem `<strong>` de destaque parcial | Sim |
| Background | `url(.../ouvidoria.png)` — **asset novo, exclusivo desta página** | Mecanismo idêntico |
| `background-size` | `cover` | Sim |
| `background-position` | **`50% 50%`** (valor padrão do componente — igual a `/clientes/`, diferente de `/parcerias/`/`/informacoes/`/`/trabalhe-conosco/`, que usam `0% 0%` com `informacoes.jpg`) | Sim, sem necessidade de prop customizada |
| Tipografia eyebrow | Roboto 700, 20px, `rgb(0,34,44)`, uppercase | Idêntico |
| Tipografia título | Roboto 700, 30px, `rgb(5,112,56)` | Idêntico |
| Alinhamento | Esquerda | Idêntico |
| Responsividade | Sem mudança de altura/tipografia em nenhum dos 5 viewports | Idêntico |

**Nenhum modificador novo é necessário** — `background_position` pode simplesmente não ser
passado (usa o padrão `50% 50%` já existente no componente).

---

## 3. Conteúdo institucional (transcrição literal)

### 3.1 Hero
- Eyebrow: "ouvidoria"
- Título: "Na CT Price, sua voz é nossa prioridade."

### 3.2 Bloco "texto + foto" (fundo escuro, seção 3)
> Na **CT Price**, acreditamos que a comunicação transparente e o respeito ao cliente são
> pilares fundamentais de uma gestão eficiente e ética.
>
> Por essa razão, foi disponibilizado um novo canal de atendimento para que nossos clientes e
> parceiros possam expressar sua insatisfação por problemas ligados ao seu atendimento, ou
> mesmo, manifestar sua satisfação sobre a qualidade do atendimento recebido.
>
> Esse novo canal, trata-se da **OUVIDORIA**, o qual, é um espaço exclusivo aos nossos clientes
> que já utilizam outros canais de atendimento e não se sentem plenamente atendidos.

(Destaques em `#10E36B` nas palavras "CT Price" e "OUVIDORIA", exatamente como no HTML original.)

### 3.3 Bloco "texto + texto" (seção 4)

**Coluna esquerda** (confidencialidade):
> As informações registradas neste canal, são sigilosas e analisadas somente pela diretoria da
> empresa e pelo time de ouvidoria interna, onde, o objetivo é a promoção da melhoria contínua
> dos nossos serviços, processos e condutas.
>
> Reclamações, sugestões, elogios, denúncias ou solicitações especiais são tratadas com
> seriedade, dentro de prazos previamente estabelecidos.
>
> Todas as manifestações são registradas, analisadas e respondidas pelo nosso **Canal de
> Ouvidoria**, garantindo que cada voz seja ouvida e respeitada.
>
> Ao recorrer à Ouvidoria, você está contribuindo diretamente para a evolução da **CT Price**,
> fortalecendo nosso propósito de entregar excelência contábil com ética, qualidade e foco no
> cliente.
>
> Na **CT Price**, sua voz é a nossa prioridade!!

**Coluna direita** — heading "Quando utilizar a Ouvidoria?" + lista:
- Quando os demais canais não resolveram sua demanda.
- Para registrar denúncias, reclamações, sugestões ou elogios.
- Para contribuir com a melhoria contínua dos nossos serviços.

### 3.4 Bloco do formulário (seção 5)
- Heading: "CANAIS EXCLUSIVOS PARA RECLAMAÇÕES OU ELOGIOS"
- Texto: "**Envie sua manifestação** pelo formulário ao lado ou pelo nosso **Canal Exclusivo**
  de atendimento."

Nenhum outro texto institucional, aviso legal ou explicação adicional foi encontrado em nenhuma
parte da página.

---

## 4. Formulário — auditoria campo a campo

Widget `elementor-widget-form` (Elementor Pro Forms), `name="Contato Ouvidoria"`, `method="post"`.
Campos ocultos: `post_id=1345`, `form_id=e759403`, `referer_title=Ouvidoria`, `queried_id=1345`
(nenhum token de CSRF visível no HTML estático — mesmo padrão já observado em
`fale-conosco-audit.md`).

| # | Campo | `label` | Tipo | Obrigatório | `placeholder` | `maxlength` | Validação | `autocomplete` | Largura |
|---|---|---|---|---|---|---|---|---|---|
| 1 | Nome | "Nome" | `text` | Sim | — | — | HTML5 `required` | ausente | 100% |
| 2 | Contato | "Contato" | `tel` | Sim | — | — | `required` + `pattern="[0-9()#&+*-=.]+"` (título: "Apenas números e caracteres de telefone") | ausente | 33% |
| 3 | E-mail | "E-mail" | `email` | Sim | — | — | `required` + validação nativa de e-mail do navegador | ausente | 66% |
| 4 | Empresa | "Empresa" | `text` | Sim | — | — | `required` | ausente | 100% |
| 5 | Mensagem | "Mensagem" | `textarea` (4 linhas) | Sim | "Informe como podemos te ajudar" | — | `required` | ausente | 100% |
| 6 | reCAPTCHA v3 | — | invisível (`data-size="invisible"`) | — | — | — | server-side (não verificável) | — | 100% |
| 7 | Upload | — (sem `<label>`) | `file`, `multiple` | **Não** | — | — | nenhum `accept` declarado (qualquer tipo aceito no cliente) | — | 100% |
| 8 | Enviar | — | `submit` | — | — | — | — | — | 100% |

**Ordem exata confirmada**: Nome → Contato → E-mail → Empresa → Mensagem → reCAPTCHA (invisível)
→ Upload → Enviar.

**Nenhum `select`, `radio` ou `checkbox` existe no formulário** — nenhum campo de escolha de
categoria de manifestação (ex.: "reclamação"/"elogio"/"denúncia"), apesar de o heading da seção
mencionar "reclamações ou elogios" e o texto institucional citar "reclamações, sugestões,
elogios, denúncias".

**Teste de validação client-side realizado** (sem enviar dados): clique em "Enviar" com todos os
campos vazios → `nameInput.checkValidity() === false`, `validationMessage: "Preencha este
campo."` (mensagem genérica nativa do navegador, não uma mensagem customizada do Elementor) —
nenhuma requisição de rede com dados de formulário foi disparada.

---

## 5. Anonimato

**Não existe nenhuma opção de manifestação anônima.** Confirmado por:
- Ausência de qualquer `radio`/`checkbox`/toggle relacionado a "identificado"/"anônimo" no HTML
  do formulário (busca exaustiva no DOM).
- Ausência de qualquer palavra como "anônim..." em todo o texto visível da página (busca de
  texto completo no `body.innerText`, sem resultado).
- **Todos os 4 campos de identificação (Nome, Contato, E-mail, Empresa) são obrigatórios** —
  não há como submeter uma manifestação sem se identificar completamente, incluindo dados de
  contato direto (telefone e e-mail).

Isso é um ponto notável para um canal cujo próprio texto institucional menciona "denúncias" —
convencionalmente, canais de ouvidoria/denúncia costumam oferecer uma via anônima. Registrado
aqui como achado, não corrigido nesta etapa (decisão de negócio, ver §17).

---

## 6. Backend atual (identificação conceitual, sem acessar credenciais)

- **Elementor Pro Forms** (`data-widget_type="form.default"`), o mesmo mecanismo já identificado
  em `/fale-conosco/` e `/trabalhe-conosco/` (popup de candidatura).
- Submissão real (não testada) seguiria o padrão conhecido do Elementor Pro: `POST` assíncrono
  para `wp-admin/admin-ajax.php` (`action=elementor_pro_forms_send_form`), com nonce injetado
  via JavaScript no momento do envio (não presente como campo estático no HTML — por isso não
  aparece na lista de campos ocultos).
- **reCAPTCHA v3 invisível** confirmado ativo: `data-sitekey="6Lfqs68qAAAAAFH0odzLFC02EJlV8rISafPwKKWD"`,
  `data-action="Form"`, badge `bottomright`. Script `google.com/recaptcha/api2/...` carregado e
  executado normalmente (confirmado via requisições de rede reais durante o teste de validação).
- **Destino do e-mail da manifestação**: não verificável do lado cliente (configuração
  server-side do Elementor Pro Forms, não exposta no HTML) — nenhuma tentativa de acessar o
  painel administrativo foi feita, conforme instruído.
- **Upload**: campo presente (`multiple`, opcional) — o destino de armazenamento do arquivo
  (mídia da biblioteca do WordPress, anexo de e-mail, ou ambos) também não é verificável do lado
  cliente.
- Nenhum redirecionamento pós-envio nem webhook externo foi identificável a partir do HTML/JS
  estático.

---

## 7. Uploads

| Propriedade | Valor |
|---|---|
| Nome do campo | `form_fields[field_a628506][]` (array — confirma suporte a múltiplos arquivos) |
| Múltiplos arquivos | Sim (`multiple` presente) |
| Tipos permitidos | **Nenhuma restrição declarada** (`accept` ausente) — qualquer tipo de arquivo pode ser selecionado no seletor do sistema operacional |
| Tamanho máximo | Não declarado no HTML (validação de tamanho, se existir, é apenas server-side/php.ini — não verificável do lado cliente) |
| Obrigatoriedade | Opcional (sem `required`) |
| Validação aparente | Nenhuma validação client-side além do seletor nativo do navegador |
| Destino público aparente | Não observável sem submissão real (não testada, conforme instruído) |

**Nenhum arquivo foi enviado durante esta auditoria.**

---

## 8. Segurança e privacidade

| Item | Situação | Classificação |
|---|---|---|
| CSRF (token visível) | Nenhum token estático no HTML (nonce, se existir, é injetado via JS no submit — mesmo padrão já visto em `/fale-conosco/` original) | C — defeito conhecido a corrigir (a implementação já estabelecida em `/fale-conosco/`, com token de sessão + `hash_equals`, é o padrão a seguir) |
| CAPTCHA | reCAPTCHA v3 invisível, ativo e configurado | A — fidelidade obrigatória de intenção (usar alguma proteção anti-bot), mas reCAPTCHA em si é uma dependência de terceiro (Google) — decisão de implementação equivalente já tomada em `/fale-conosco/` foi NÃO usar reCAPTCHA e usar honeypot + rate limit próprios; mesma linha recomendada aqui (ver §17) |
| Honeypot | Nenhum campo oculto do tipo honeypot encontrado | D — decisão dependente da nova implementação (nenhuma alteração possível na referência) |
| Rate limit | Não verificável do lado cliente (nenhuma indicação de limite de tentativas na página) | D |
| Validação server-side | Não verificável sem submissão real; a validação visível é só client-side (`required`, `pattern`, tipos HTML5) | D |
| Proteção de upload (tipo/tamanho) | Nenhuma restrição de tipo (`accept`) declarada no cliente; tamanho não verificável | C — ausência de `accept` é uma lacuna de UX/segurança básica a corrigir numa reconstrução |
| Consentimento/LGPD | **Nenhum texto, checkbox ou link de política de privacidade/LGPD em toda a página** (busca textual completa, sem resultado) — mesma lacuna já registrada para `/fale-conosco/` original, mas mais sensível aqui por envolver "denúncias" e upload de arquivo | C — defeito conhecido a corrigir, dependente de texto legal aprovado (mesma ressalva já registrada para Fale Conosco: não inventar texto jurídico sem aprovação) |
| Mensagens de erro | Apenas a mensagem nativa genérica do navegador foi observada (`"Preencha este campo."`) — nenhuma mensagem customizada do Elementor foi disparada nesta auditoria (exigiria passar da validação HTML5 nativa, não testado para evitar aproximar-se de um envio real) | B — comportamento não totalmente verificável sem risco de envio parcial |
| Exposição de dados | Nenhum dado sensível exposto no HTML/JS estático (sem nonce, sem chave privada, sem endpoint administrativo) | A |
| Scripts externos | Google reCAPTCHA (`google.com/recaptcha/...`), GTranslate (topbar, já auditado globalmente), Google Maps (footer, já auditado globalmente) | A/B — mesmos scripts já classificados em auditorias anteriores |

**Nenhuma correção foi implementada nesta etapa** — apenas classificação, conforme instruído.

---

## 9. Dados de contato

| Dado | Nesta página | `config/company.php` | Divergência? |
|---|---|---|---|
| WhatsApp da topbar | `(67) 99232-4097` | Canônico `(67) 99261-6117` | Já documentado (mesmo padrão de todas as páginas internas, não novo) |
| Telefone fixo (topbar) | `(67) 3313-7300` | Idêntico | Não |
| E-mails (topbar/footer) | `contato@ctpricems.com.br` / `protecaodedados@ctpricems.com.br` | Idênticos | Não |
| Endereço/bairro/CEP (footer) | "Monte Castelo", "79.010-190" | `null`/`null` (pendente, já registrado) | Confirma o TODO já existente, não novo |
| **WhatsApp exclusivo da Ouvidoria** | **`(67) 99110-3140`** (`https://wa.me/5567991103140`) | **Não existe nenhum campo equivalente em `config/company.php`** | **Novo dado, não centralizado** — nenhuma página auditada até agora tinha um contato específico de departamento fora de `departamentos` (Fale Conosco). Recomenda-se, na implementação futura, adicionar este número a `config/company.php` (ex.: uma chave `ouvidoria` ao lado de `departamentos`, ou um campo dedicado) em vez de hardcoded na página — decisão de estrutura para a etapa de implementação, não tomada aqui |
| E-mail dedicado da Ouvidoria | Não encontrado em nenhum lugar da página | — | Não aplicável — não existe tal e-mail visível |

**Nenhuma alteração foi feita em `config/company.php`.**

---

## 10. Links exclusivos da página

| Texto | URL | `target` | `rel` | HTTPS? | Funciona? |
|---|---|---|---|---|---|
| Botão WhatsApp "(67) 99110-3140" | `https://wa.me/5567991103140` | `_blank` | **ausente** | Sim | Sim (padrão `wa.me`, mesmo mecanismo já usado em outros números do site) |

Nenhum outro link exclusivo (política de privacidade, termos, canal externo além do WhatsApp,
formulário externo) foi encontrado nesta página — todos os demais links (menu, footer, mapa,
e-mails) são os globais já auditados em páginas anteriores, sem novidade:

| Link global reconfirmado | Observação |
|---|---|
| Footer "Benefícios" → `/wp/informacoes/#beneficios` | Mesmo link quebrado já conhecido (categoria C, não específico desta página) |
| Item de menu "Ouvidoria" (`href="http://ctprice.com.br/wp/ouvidoria"`) | Mesma inconsistência HTTP/sem barra final já registrada em `site-inventory.md` (categoria C, global, não nova) |
| Endereço (Google Maps, footer) | `target="_blank" rel="noopener"` (sem `noreferrer`) — mesmo padrão B já aceito em todas as páginas |

**Achado de segurança específico desta página**: o botão de WhatsApp exclusivo da Ouvidoria usa
`target="_blank"` **sem nenhum `rel`** (nem `noopener` nem `noreferrer`) — diferente do padrão já
visto no link do endereço/Google Maps (que ao menos tem `noopener`). **Classificação: C — defeito
conhecido a corrigir** (risco de reverse tabnabbing, mesma categoria já aplicada a outros botões
`target="_blank"` sem `rel` em auditorias anteriores do projeto).

---

## 11. Layout e tipografia (medições diretas, 1440×900)

| Seção | Container | Padding lateral | Background | Cor de texto |
|---|---|---|---|---|
| Hero | 1140px | — | `url(ouvidoria.png)` `cover` `50% 50%` | eyebrow `#00222C`, título `#057038` |
| Texto+foto (seção 3) | **1240px** | 0px (full-bleed) | sólido `#00222C` | `#FEFEFE` (branco) |
| Texto+texto (seção 4) | **1240px** | 10px | transparente | esquerda `#7A7A7A` (cinza), heading direita `#057038` |
| Formulário (seção 5) | 1140px | 10px | sólido `#00222C` | heading/texto `#FEFEFE`; labels do formulário `#10E36B` |

- Colunas: seção 3 → 2 colunas de 620px cada (50/50); seção 4 → 2 colunas de 620px (50/50);
  seção 5 → 1 linha full-width (heading) + 2 colunas de 570px (texto+WhatsApp / formulário).
- Parágrafos: Roboto 16px, `line-height:24px` — cinza `#7A7A7A` (fundo claro) ou branco `#FEFEFE`
  (fundo escuro).
- Heading "Quando utilizar a Ouvidoria?": Roboto 20px/700/`#057038`.
- Heading "CANAIS EXCLUSIVOS...": Roboto 20px/700/`#FEFEFE`, centralizado.
- Lista "Quando utilizar": 3 itens, mesma tipografia 16px/`#7A7A7A` dos parágrafos.
- Campo de formulário: borda `1px solid rgb(105,114,125)`, `border-radius:3px`, padding `8px
  16px`, fundo branco, fonte 15px. Label: 16px/400/`#10E36B`.
- Botão "Enviar": fundo `#61CE70`, texto branco, padding `0 24px`, `border-radius:3px`.
- Botão WhatsApp exclusivo: estilo "pill outline" — borda `3px solid #10E36B`, fundo transparente,
  texto `#10E36B`, `border-radius:40px`, padding `12px 24px` — mesma família visual do botão
  "Área Restrita" do header (outline 3px + pill), mas com a cor de destaque (`--color-accent-green`)
  em vez da cor de marca (`--color-brand-green`).
- Decoração: um `::before` no próprio elemento da seção 5 (fundo `#00222C`) aplica
  `background-image: url(Isotipolinear.png)` — **mesmo asset já existente no projeto**
  (`assets/images/pages/fale-conosco/Isotipolinear.png`), `background-size:contain`,
  `no-repeat`, `opacity:0.5`, cobrindo a seção inteira como marca d'água — puramente decorativo.
- Foto "atendente" (seção 3): `object-fit:fill` implícito (dimensão fixa via `width`/`height` do
  atributo HTML, sem CSS de contenção) — em telas largas ocupa 450×450px; note-se que a roupa
  escura da modelo tem baixo contraste contra o fundo `#00222C`, tornando a foto visualmente
  discreta em capturas reduzidas (observação de qualidade visual, não um defeito técnico — a
  imagem carrega e renderiza corretamente em todos os viewports testados).

---

## 12. Responsividade

Breakpoints reais medidos (nenhum presumido):

| Elemento | Desktop (≥1024px) | 900×1200 | 768×1024 | **767×1024** | 390×844 |
|---|---|---|---|---|---|
| Hero | 400px, sem mudança | idêntico | idêntico | idêntico | idêntico |
| Seção 3 (texto+foto) | 2 colunas (620px) | 2 colunas (443px) | 2 colunas (377px) | **1 coluna** (empilha, texto acima da foto) | 1 coluna |
| Seção 4 (texto+texto) | 2 colunas (620px) | 2 colunas (433px) | 2 colunas | **1 coluna** | 1 coluna |
| Formulário | 2 colunas (570/570) | 2 colunas (443/443) | 2 colunas | **1 coluna** (empilha, texto+WhatsApp acima do formulário) | 1 coluna |
| Campos "Contato"/"E-mail" (33%/66%) | lado a lado | lado a lado | lado a lado | **empilhados, 100% cada** | 100% cada |

**Breakpoint de conteúdo confirmado em exatamente 767px** (testado 767 vs 768 diretamente) — o
mesmo valor já usado em todo o projeto, não uma configuração nova desta página. **Nenhum
overflow horizontal** (`scrollWidth === clientWidth`) em nenhum dos 5 viewports (1425/1425,
885/885, 753/753, 752/752, 375/375).

Nenhum comportamento mobile "ruim" do WordPress foi identificado além do já esperado (nenhum
colapso de altura, nenhum corte de conteúdo, nenhuma imagem quebrada em nenhum viewport).

---

## 13. Interações

| Interação | Resultado |
|---|---|
| Validação nativa dos campos obrigatórios | Confirmada (`checkValidity()===false`, mensagem nativa "Preencha este campo.") ao tentar enviar vazio |
| reCAPTCHA v3 invisível | Script carrega e inicializa (`grecaptcha-badge` presente, chamadas `reload`/`clr` observadas na tentativa de envio) — nenhuma interação visível exigida do usuário |
| Upload — seletor de arquivo | Botão nativo "Escolher arquivos" (`<input type="file">` sem estilização customizada) |
| Hover no botão WhatsApp exclusivo | `transition` presente (câmbio de leve, não testado pixel a pixel) |
| Hover no botão "Enviar" | Padrão do widget `button.default` do Elementor, mesmo mecanismo já visto em outros formulários do site |
| Campos condicionais / show-hide | **Nenhum** — todos os campos são sempre visíveis, nenhuma lógica condicional |
| Popup/modal | **Nenhum** |
| Animações de entrada por scroll | **Nenhuma** (`elementor-invisible`/`data-settings` de animação: 0 elementos) |
| Menu — item "Ouvidoria" | Sublinhado verde de estado ativo (mesmo padrão de indicador de página ativa já visto em outras páginas — pendência global já registrada, não específica desta) |

**Nenhuma manifestação real foi enviada, nenhum arquivo foi anexado.**

---

## 14. Acessibilidade

- **Labels corretamente associados via `for`/`id`**: confirmado para todos os 5 campos de texto
  (`Nome`, `Contato`, `E-mail`, `Empresa`, `Mensagem`) — a árvore de acessibilidade lê o nome
  acessível como `"<Label> *"` para cada um (o `*` de obrigatório é lido junto, sem um texto
  alternativo tipo "(obrigatório)" como já implementado em `/fale-conosco/`).
- **Upload sem `<label>`**: o campo de arquivo não tem rótulo associado nem `aria-label` — mesmo
  padrão de lacuna já visto no popup removido de `/trabalhe-conosco/`.
- **Indicação de obrigatoriedade**: apenas visual (`*` vermelho ao lado do label) — não há texto
  tipo `sr-only` "(obrigatório)" para leitores de tela; o atributo `required` nativo é o que
  efetivamente comunica isso a tecnologias assistivas (suficiente, mas redundância visual/textual
  ajudaria).
- **Hierarquia de headings**: 4 `<h2>` no total (eyebrow, título, "Quando utilizar", "Canais
  exclusivos"), nenhum `<h1>` — mesmo padrão consistente já visto em todo o site, não específico
  desta página.
- **Contraste**: texto branco sobre fundo `#00222C` (seções 3 e 5) tem contraste alto; texto
  cinza `#7A7A7A` sobre fundo branco (seção 4) seria o único ponto a testar com uma ferramenta de
  contraste formal — não calculado nesta etapa (mesmo padrão de texto cinza já usado em todo o
  site, não específico desta página).
- **Ordem de tabulação**: sequencial e lógica (Nome → Contato → E-mail → Empresa → Mensagem →
  upload → Enviar), confirmado pela ordem do DOM (sem `tabindex` customizado).
- **Mensagens de erro**: só a mensagem nativa genérica do navegador foi observada nesta auditoria
  (não foi possível, sem se aproximar de um envio real, verificar se o Elementor also exibe
  mensagens de erro customizadas — por exemplo, apos passar da validação `required` mas falhar
  em outro critério).
- **Textos de ajuda**: o único (`title` do campo "Contato", "Apenas números e caracteres de
  telefone (#, -, *, etc.) são aceitos.") só é exposto via atributo `title` (tooltip nativo do
  navegador) — não é lido automaticamente por todos os leitores de tela sem interação extra.
- **Opção anônima**: não se aplica (não existe, ver §5).

---

## 15. Assets exclusivos

| Asset | URL original | Formato | Dimensões | Uso | Classificação |
|---|---|---|---|---|---|
| `ouvidoria.png` | `.../wp-content/uploads/2025/04/ouvidoria.png` | PNG | 1200×600 | Background do Hero | **Novo, necessário** |
| `atendente-1024x1024.png` (ou variante) | `.../wp-content/uploads/2025/04/atendente-1024x1024.png` | PNG | 1024×1024 (arquivo "full" — variantes 300×300/150×150/768×768 também existem via `srcset`) | Foto institucional (seção 3) | **Novo, necessário** |
| `Isotipolinear.png` | `.../wp-content/uploads/2024/09/Isotipolinear.png` | PNG | 1080×1080 (já confirmado em `fale-conosco-audit.md`) | Marca d'água decorativa de fundo (seção 5, via `::before`) | **Já existente no projeto** (`assets/images/pages/fale-conosco/Isotipolinear.png`) — reaproveitável diretamente, **não precisa ser baixado novamente** |
| Ícone WhatsApp (SVG inline, Font Awesome `fa-whatsapp`) | embutido no HTML (`<svg>`, sem arquivo externo) | SVG inline | — | Ícone do botão WhatsApp exclusivo | **Compartilhável** — mesmo ícone conceitual já reproduzido sem dependência de Font Awesome em outros botões/links de WhatsApp do projeto |

**Nenhum asset quebrado ou duplicado** foi encontrado nesta página. **Nenhum download foi
realizado nesta etapa** — dimensões/formato confirmados via `getimagesize()` direto na URL
remota, sem gravar arquivo local.

---

## 16. Possível reaproveitamento arquitetural

- **`boxed-hero.php` serve, sem modificação** (Categoria A) — mesmo padrão já usado em 5 páginas
  internas; usa o valor padrão de `background_position` (`50% 50%`), nem precisa da prop
  customizada.
- **Componentes de `/fale-conosco/`**:
  - O padrão geral de segurança do endpoint (`fale-conosco-action.php`: CSRF por sessão,
    honeypot, rate limit, validação server-side, `ctprice_clean_line()` contra header injection)
    é diretamente aplicável como base conceitual para um futuro endpoint de Ouvidoria — mesma
    filosofia, não o mesmo arquivo (campos diferem: Contato/telefone obrigatório aqui, Empresa
    obrigatória aqui vs. opcional em Fale Conosco, mais o campo de upload que Fale Conosco não
    tem).
  - `components/contact-form-section.php` **não deve ser reutilizado como está** — layout
    (foto + formulário) e campos são diferentes o suficiente (upload, campo "Contato" com
    padrão de telefone, ausência de decorative image posicionada como no Fale Conosco) para
    justificar um componente de formulário próprio, seguindo a mesma convenção de dados-como-array
    já usada no projeto.
  - `assets/js/contact-form.js` (envio assíncrono com feedback inline, fallback sem JS via
    303/`?status=`) é um padrão de **CSS/JS conceitualmente reaproveitável** — a mesma estratégia
    de submissão (fetch + JSON, com fallback de formulário nativo) se aplica à Ouvidoria, mas
    precisaria de ajuste para lidar com `multipart/form-data` (upload de arquivo), que o Fale
    Conosco atual não tem.
- **Componente novo necessário**: um formulário próprio da Ouvidoria (`ouvidoria-form-section.php`
  ou nome equivalente) — os campos, a presença de upload, e a decisão sobre anonimato (pendente
  de definição do cliente) tornam pouco produtivo forçar reuso do componente de Fale Conosco.
- **Marca d'água decorativa** (`Isotipolinear.png` como `background-image` de seção): já existe
  o asset; o mecanismo CSS (`::before` full-bleed, `opacity`, `background-size:contain`) é simples
  o suficiente para reproduzir diretamente em CSS próprio do novo componente, sem precisar de
  componente/abstração dedicados.
- **Bloco "texto + foto"/"texto + texto"** (seções 3 e 4): estruturalmente parecidos com
  `components/image-text-section.php` (usado em "História", `/sobre-nos/`) e poderiam, em
  princípio, ser candidatos a reaproveitamento — mas a auditoria não aprofundou essa comparação
  pixel a pixel (fora do escopo desta etapa, que é sobre o formulário/segurança principalmente);
  registrado como possível investigação futura na etapa de implementação, não decidido aqui.

---

## 17. Arquitetura futura do formulário (recomendação conceitual, sem programar)

- **Endpoint PHP próprio** (`ouvidoria/ouvidoria-action.php` ou equivalente), seguindo a mesma
  filosofia já validada em `fale-conosco/fale-conosco-action.php`:
  - **CSRF**: token de sessão opaco (`random_bytes(32)` + `hash_equals()`), gerado em
    `ouvidoria/index.php` antes de qualquer saída HTML.
  - **Honeypot**: campo oculto adicional (rejeição silenciosa, sem revelar ao bot que foi detectado).
  - **Rate limit**: por sessão, sem exigir banco de dados (mesmo padrão de 30s já usado em Fale
    Conosco) — registrar explicitamente como limitação consciente (não substitui proteção por
    IP/WAF).
  - **Validação server-side completa**: nunca confiar em `required`/`pattern` do HTML५ (o
    `pattern` do campo "Contato", por exemplo, precisa ser revalidado no servidor).
  - **Upload seguro, se mantido**: validar extensão/MIME real do arquivo (não confiar no nome/
    extensão declarados pelo navegador), limitar tamanho e quantidade, armazenar fora da raiz
    pública ou com nomes gerados (não o nome original do arquivo), nunca executar o conteúdo
    enviado.
  - **Envio por e-mail**: `From` sempre institucional (nunca o e-mail do denunciante, mesmo
    princípio já aplicado em Fale Conosco), `Reply-To` = e-mail informado (após validação),
    anexando o(s) arquivo(s) enviado(s) ou informando um link de download seguro — decisão de
    implementação.
  - **Anonimato**: **decisão pendente do cliente** (ver §5) — se aprovado, a arquitetura
    precisaria tornar Nome/Contato/E-mail/Empresa condicionalmente opcionais (não removê-los,
    apenas não exigi-los quando "manifestação anônima" for selecionada), com um campo de escolha
    explícito (radio/checkbox) controlando isso via JS + validação server-side correspondente.
  - **Logs mínimos**: registrar (server-side, não exposto ao usuário) tentativas de envio malsucedidas
    para diagnóstico, sem armazenar dados sensíveis em log de acesso padrão do servidor.
  - **Feedback seguro**: mensagens genéricas ao usuário (sem stack trace/detalhes técnicos),
    mesmo padrão já usado em Fale Conosco — especialmente importante aqui por lidar com conteúdo
    potencialmente sensível (denúncias).
- **CAPTCHA**: recomenda-se a mesma decisão já tomada em Fale Conosco — **não usar reCAPTCHA do
  Google** (dependência de terceiro, banner de cookies/privacidade adicional) e confiar em
  honeypot + rate limit próprios, a menos que o volume de spam real justifique reconsiderar.
- **Nenhum banco de dados, CMS ou painel administrativo** deve ser criado nesta fase — consistente
  com a decisão global já registrada (concluir todo o site público antes de iniciar essas etapas).

---

## 18. REFERENCE DRIFT

Nenhuma divergência entre o site ao vivo e nenhum baseline previamente registrado foi encontrada
nesta auditoria (esta é a primeira auditoria de `/ouvidoria/`, não há baseline anterior desta
página especificamente). DRIFT-001 (botão "Área Restrita" ausente do header ao vivo) foi
reconfirmado como **presente** nesta página (mesmo padrão inconsistente entre páginas já
registrado durante a auditoria de `/trabalhe-conosco/` — nem toda página ao vivo remove o botão).
Nenhum novo drift registrado.

---

## Screenshots capturados

- `docs/reference/screenshots/ouvidoria-desktop-1440-full.png`
- `docs/reference/screenshots/ouvidoria-tablet-900-full.png`
- `docs/reference/screenshots/ouvidoria-mobile-390-full.png`

Todos capturados após rolagem completa da página em incrementos de ~400px (metodologia já usada
em auditorias anteriores) — nenhuma lacuna visual encontrada nas 3 capturas finais. Nota: no
screenshot tablet, o badge flutuante do reCAPTCHA aparece parcialmente expandido no canto
superior direito — comportamento padrão do próprio widget do Google (desliza para fora ao
hover/interação), não um defeito do layout da página.
