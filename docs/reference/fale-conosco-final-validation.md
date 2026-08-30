# Validação final — `/fale-conosco/`

Data: 2026-08-30
Documentação-base: `docs/reference/fale-conosco-audit.md`, `docs/reference/reference-baseline.md`,
`docs/reference/home-final-validation.md`, `docs/reference/sobre-nos-final-validation.md`,
`docs/reference/clientes-final-validation.md`, `docs/reference/parcerias-final-validation.md`

Escopo: validação visual, funcional, de segurança e de integração de `/fale-conosco/` antes do
checkpoint Git. Duas pequenas correções foram aplicadas nesta validação (§5 e §9); nenhuma outra
alteração de código foi feita. Nenhum commit foi feito nesta tarefa.

---

## 1. Estrutura final confirmada

Ordem no DOM, idêntica nos 5 viewports (1440×900, 900×1200, 768×1024, 767×1024, 390×844):
topbar → header → Hero → bloco imagem+formulário → faixa de departamentos → footer → bottom bar →
WhatsApp flutuante → cookie banner.

### 1.1 Métricas por viewport (1440×900)

| Seção | Top | Altura |
|---|---|---|
| Hero (`boxed-hero`) | 198 | 400 |
| Bloco formulário (`contact-form-section`) | 598 | 680 |
| Faixa de departamentos | 1278 | 126 |
| Footer | 1404 | — |

Transições: 0px de gap entre Hero→formulário, formulário→departamentos e departamentos→footer —
mesmo padrão de encaixe justo já usado/aprovado nas demais páginas internas.
`scrollWidth === clientWidth` (1425=1425) — sem overflow. Conforme instruído, **não foi buscada
equivalência de altura total com o WordPress** (melhorias conscientes já aprovadas mudam a
altura esperada).

### 1.2 Demais viewports

| Viewport | `scrollWidth===clientWidth` | Colunas do formulário | Colunas de departamentos |
|---|---|---|---|
| 900×1200 | 885=885 ✓ | lado a lado (row) | 3 |
| 768×1024 | 753=753 ✓ | lado a lado (row) | 3 |
| 767×1024 | 752=752 ✓ | empilhado (column), sem sobreposição (`photoBottom === textTop` exatos) | 1 |
| 390×844 | 375=375 ✓ | empilhado | 1 |

Nenhum gap inesperado, nenhuma sobreposição, nenhum overflow horizontal em nenhum viewport.

---

## 2. Resultado do Hero

Confirmada reutilização direta de `components/boxed-hero.php`, sem nenhuma alteração no
componente (nenhuma regressão encontrada que a justificasse):

| Propriedade | Medido |
|---|---|
| Altura | 400px em todos os 5 viewports |
| `background-size` | `cover` |
| `background-position` | `50% 50%` (valor padrão do componente) |
| Container | `.boxed-hero__inner`, 1140px |
| Eyebrow | "fale conosco" |
| Título | "Tire suas dúvidas ou envie sugestões" |
| Imagem | `assets/images/pages/fale-conosco/pgcontato.jpg` |

---

## 3. Resultado do bloco visual do formulário

- **Imagem `maosdadas.jpg`**: `background-size:cover`, preenche toda a coluna esquerda (680px de
  altura em 1440×900) sem distorção — crop de qualidade, mostra o aperto de mãos por completo.
- **Isotipo `Isotipolinear.png`**: sobreposto, 360px de largura, visível em desktop/tablet
  (≥768px), oculto em `≤767px` (confirmado `display:none`) — decisão consciente já aprovada.
- **Background `#00222C`**: confirmado como cor de fundo de toda a seção.
- **Proporção das colunas**: 50/50 (600px/600px de um container de 1200px) em desktop/tablet;
  100% empilhado em `≤767px`.
- **Integração foto↔formulário em `≤767px`**: `photoBottom` e `textTop` batem exatamente (985px
  em 767×1024) — a foto termina exatamente onde o formulário começa, **sem nenhuma
  sobreposição**, ao contrário do comportamento da referência (labels sobre a foto). Contraste
  confirmado adequado (formulário sobre fundo sólido `#00222C`, não sobre a foto).

---

## 4. Resultado dos campos

| Campo | `label` associado | `autocomplete` | Placeholder | Obrigatório |
|---|---|---|---|---|
| Nome | `for="cf-name"` ✓ | `name` | "Seu nome" | Não |
| E-mail | `for="cf-email"` ✓ | `email` | "seuemail@exemplo.com" | **Sim** (único) |
| Empresa | `for="cf-company"` ✓ | `organization` | "Nome da sua empresa" | Não |
| Mensagem | `for="cf-message"` ✓ | — (não aplicável a textarea) | "Informe como podemos te ajudar" | Não |

- Indicação de obrigatoriedade do E-mail: asterisco visual **+** texto oculto "(obrigatório)"
  via `.sr-only` — não depende só de cor.
- Tabulação real confirmada (`Tab` físico, não `.focus()` programático): Nome → E-mail → Empresa
  → Mensagem → Enviar → primeiro link de departamento — nesta ordem exata, sem o honeypot
  interceptar o foco em nenhum momento.
- Foco visível real: outline `3px solid rgb(16,227,107)` + borda destacada em todos os campos,
  no botão e nos links de departamento — confirmado via `:focus-visible` real.
- Estado de erro (E-mail inválido) testado ao vivo no navegador: mensagem de erro associada via
  `aria-describedby`, classe `--invalid` aplicada, botão retorna ao estado normal.
- Feedback de sucesso testado ao vivo: mensagem inline, `form.reset()` confirmado (campo Nome
  volta a `""`).
- **Confirmado: apenas o E-mail permanece obrigatório** — nenhum novo campo obrigatório foi
  introduzido.

---

## 5. Resultado da validação server-side (`fale-conosco-action.php`)

Revisão + testes automatizados (via `curl`, sessão real) confirmam:

- Aceita somente `POST` (`GET` → `405` via JSON quando a requisição se identifica como AJAX, ou
  redirecionamento 303 no fallback sem JS).
- Valida e-mail com `filter_var(FILTER_VALIDATE_EMAIL)` + limite de tamanho.
- Valida tamanho máximo de todos os campos de texto (Nome/Empresa 150, Mensagem 5000).
- Nunca confia em `required`/`type=email` do HTML — todos os campos revalidados no servidor.
- CR/LF removidos de Nome/E-mail/Empresa via `ctprice_clean_line()` antes de qualquer uso em
  cabeçalho de e-mail — impede header injection.
- `From` é sempre o e-mail institucional (`config/company.php`); o e-mail do visitante nunca é
  usado como `From`.
- `Reply-To` só é montado **depois** de `$email` passar por `filter_var()` + limpeza de CR/LF.
- Nenhuma resposta expõe stack trace, caminho de servidor ou detalhe de exceção — mensagens
  sempre genéricas e pré-definidas; detalhes técnicos vão só para `error_log()`.

**Correção aplicada nesta validação**: nenhuma no próprio endpoint de validação (já estava
correto) — ver §9 para a correção real feita (guarda de duplo envio no JS) e §6 (sessão/cookie).

---

## 6. Resultado de CSRF / Honeypot / Rate limit

### CSRF

- Token gerado com `random_bytes(32)` (fonte criptograficamente segura), armazenado em
  `$_SESSION['fale_conosco_csrf']`.
- Comparação com `hash_equals()` (tempo constante) — confirmado no código.
- Token ausente → `403`; token incorreto → `403` (ambos testados via `curl`).
- Estratégia de token estável por sessão (não rotacionado por requisição) mantida como está —
  decisão já aprovada; múltiplas abas continuam funcionando porque compartilham a mesma sessão
  e, portanto, o mesmo token (não há invalidação entre abas).
- Token não aparece em nenhum log do servidor (`error_log()` só é chamado nos ramos de falha de
  `mail()`/destinatário ausente, nunca com o token como argumento).

### Honeypot

- Campo `website`: fora da tela (`position:absolute; left:-9999px`), `aria-hidden="true"` no
  wrapper, `tabindex="-1"` no input — confirmado fora da ordem de tabulação real (teclado físico).
- Preenchido → resposta `200` com a **mesma mensagem de sucesso** do envio real — nenhuma
  mensagem denuncia ao bot que foi bloqueado.
- Confirmado (Mailpit): nenhum e-mail é gerado quando o honeypot está preenchido.

### Rate limit

- Intervalo de 30 segundos confirmado via teste real (envio válido seguido de reenvio imediato →
  `429`).
- Confirmado que um erro de validação (e-mail inválido) **não** consome o rate limit — testado
  enviando um e-mail inválido logo após um honeypot "bem-sucedido" e recebendo `422` (não `429`)
  normalmente.
- Confirmado que o honeypot também não interfere no rate limit (retorna antes de qualquer
  checagem de tempo).
- Resposta `429` tratada corretamente pelo JavaScript: cai no ramo de erro genérico do
  `contact-form.js`, exibindo a mensagem do servidor ("Aguarde alguns segundos...") na área de
  feedback.
- **Limitação consciente registrada**: rate limit por sessão reduz abuso simples (um mesmo
  visitante enviando repetidamente), mas **não substitui proteção por IP/proxy/WAF** — um
  atacante trocando de sessão/cookie a cada requisição não é limitado por este mecanismo. Nenhuma
  infraestrutura adicional foi implementada agora, conforme instruído.

---

## 7. Resultado da revisão de sessão/cookie (correção aplicada)

**Situação encontrada**: `session_start()` era chamado em `fale-conosco/index.php` e
`fale-conosco/fale-conosco-action.php` sem nenhuma configuração explícita de `HttpOnly`/
`SameSite`/`Secure` — o cookie de sessão saía apenas com os padrões do `php.ini` do servidor
(que podem variar entre ambientes).

**Correção aplicada**: adicionada a função `ctprice_configure_session_cookie()` em
`config/bootstrap.php` — único arquivo já incluído por toda página, evitando duplicar a
configuração nos dois arquivos que usam sessão. Chamada antes de `session_start()` em ambos.
Define:

- `HttpOnly`: sempre ativo.
- `SameSite=Lax`.
- `Secure`: ativado automaticamente somente quando a requisição chega via HTTPS
  (`$_SERVER['HTTPS']` ou porta 443) — em HTTP puro (ambiente local) permanece **desativado** de
  propósito, porque um cookie `Secure` seria descartado pelo navegador em conexão não
  criptografada, quebrando a sessão localmente.

**Confirmado ao vivo** (`curl -I`): `Set-Cookie: PHPSESSID=...; path=/; HttpOnly; SameSite=Lax`
— sem `Secure` em HTTP local, exatamente como projetado. Ambiente local HTTP não foi quebrado.

**Pendência de produção registrada**: se o servidor real ficar atrás de um proxy reverso/load
balancer que termina o HTTPS antes do PHP, é necessário confirmar que esse proxy popula
`$_SERVER['HTTPS']` (ou ajustar a função para checar `X-Forwarded-Proto`) — depende da
infraestrutura de produção, não verificável a partir deste código.

---

## 8. Resultado de segurança de saída / XSS

Revisão de código + teste ao vivo confirmam:

- Nenhum dado enviado pelo visitante (nome/e-mail/empresa/mensagem) é refletido em nenhuma página
  HTML — o endpoint responde só com JSON (para AJAX) ou um redirecionamento 303 para uma URL com
  `?status=` restrito a 4 valores fixos (`success`/`rate_limited`/`invalid`/`error`), nunca ecoado
  livremente: `fale-conosco/index.php` compara `$_GET['status']` com `===` contra literais fixos
  e usa apenas mensagens hardcoded correspondentes — o valor bruto do parâmetro nunca é impresso.
- `assets/js/contact-form.js`: confirmado por busca no código — **toda** escrita dinâmica usa
  `.textContent` (nunca `.innerHTML`), incluindo mensagens de sucesso/erro vindas do servidor e
  erros de campo — impossível injetar HTML mesmo que o servidor um dia devolvesse uma string com
  marcação.
- Mensagens JSON do endpoint são sempre tratadas como texto puro pelo `.textContent` no cliente.
- Nenhuma correção foi necessária nesta parte — já estava implementada corretamente.

---

## 9. Resultado com e sem JavaScript

### Com JavaScript (PART M)

- Botão entra em estado "Enviando…" e fica `disabled` durante a requisição — confirmado.
- **Correção aplicada**: adicionada uma guarda explícita (`isSubmitting`) no início do handler de
  `submit`, além do `disabled` do botão — testado disparando dois `submit` sincronamente
  (simulando duplo clique muito rápido): confirmado apenas **1** chamada `fetch` real, a segunda
  é ignorada.
- Resposta JSON interpretada corretamente nos 3 casos: sucesso, erro de validação (422 com
  `errors`), erro genérico (403/429/500).
- Sucesso limpa o formulário (`form.reset()` confirmado).
- Erro de campo permanece associado ao campo certo via `aria-describedby`/classe `--invalid`.
- Erro técnico (403/429/500) mostra sempre a mensagem genérica pré-definida pelo servidor, nunca
  detalhe interno.
- Botão volta ao estado normal (`disabled=false`, texto "Enviar") em todos os casos, inclusive
  erro — confirmado no `.finally()`.
- Console: nenhuma exceção JavaScript não tratada. O único log observado
  (`Failed to load resource: ... 422`) é o registro padrão do Chrome para qualquer resposta HTTP
  não-2xx de um `fetch` — não é uma exceção JS, é o mesmo comportamento que ocorreria com
  qualquer requisição de rede que retorne um código de erro, tratado corretamente pelo código.

### Sem JavaScript (PART L)

Testado via submissão HTML "crua" (POST direto ao endpoint, sem os cabeçalhos
`X-Requested-With`/`Accept: application/json` que só o `contact-form.js` envia — exatamente o que
o navegador faz quando o script não roda):

- Formulário continua enviável (POST aceito e processado normalmente pelo mesmo endpoint).
- Sucesso/erro volta para a página via **redirecionamento 303** (`Location: /fale-conosco/
  ?status=success`) — confirmado com `curl -D -`.
- Seguindo o redirecionamento, a página exibe o banner estático correspondente (testado
  navegando para `?status=success`): mensagem "Mensagem enviada com sucesso!..." renderizada por
  `components/contact-form-section.php`.
- **Refresh não reenvia o POST**: o padrão **Post-Redirect-Get** (código `303`, não `200`) garante
  que atualizar a página depois do redirecionamento só repete o `GET` da URL com `?status=`, nunca
  o `POST` original — comportamento padrão de navegadores para 303, não depende de JavaScript.

---

## 10. Resultado da acessibilidade

- Ordem de foco por teclado real confirmada: Nome → E-mail → Empresa → Mensagem → Enviar →
  Comercial (primeiro link de departamento) — sequência exata, honeypot nunca alcançado.
- Foco visível forte em todos os elementos interativos (`outline: 3px solid #10E36B`).
- Labels reais (`<label for>`) em todos os 4 campos.
- `aria-describedby` liga cada campo ao seu `<span>` de erro correspondente.
- `aria-live="polite"` na região de feedback geral do formulário.
- Indicação textual (não só cor) de campo obrigatório no E-mail (`.sr-only`).
- Links de departamento com `aria-label` completo ("Falar com Comercial pelo WhatsApp: ...").
- Nenhuma animação significativa foi adicionada nesta página (só transições simples de cor/borda
  em hover/focus) — `prefers-reduced-motion` não é necessário aqui, consistente com a
  recomendação de não criar complexidade sem necessidade real.

---

## 11. Resultado dos departamentos

- **Exatamente 5 departamentos** renderizados (Comercial, Pessoal, Fiscal, Contábil,
  Central/Empresarial).
- Dados vêm **somente** de `config/company.php['departamentos']` — nenhum telefone duplicado ou
  hardcoded em `components/departments-contact-section.php`.
- Telefones confirmados idênticos aos auditados; os 5 links de WhatsApp montados localmente
  batem exatamente com os hrefs da referência (`5567992324097`, `556733137301`, `556733137302`,
  `556733137304`, `556733137300`).
- Nenhum dos 5 foi substituído pelo WhatsApp global/canônico — cada departamento mantém seu
  próprio número, como confirmado na auditoria.
- Grade: 5 colunas (desktop) → 3 (tablet, `≤1023px`) → 1 (mobile, `≤767px`) — sem overflow em
  nenhum viewport.
- Área de toque: ~34px de altura por link em mobile, com padding lateral generoso (largura da
  coluna inteira) — atende ao mínimo de 24×24px da WCAG 2.2 AA (2.5.8); não atinge o alvo de
  44×44px da diretriz AAA, mas é uma área confortável dado o contexto (link de texto isolado, sem
  elementos vizinhos disputando o toque).

---

## 12. Correções realizadas nesta validação

1. **Sessão/cookie** (`config/bootstrap.php`, `fale-conosco/index.php`,
   `fale-conosco/fale-conosco-action.php`): adicionada configuração explícita de
   `HttpOnly`/`SameSite=Lax`/`Secure` condicional para o cookie de sessão, centralizada em uma
   função reutilizável — ver §7.
2. **Guarda de envio concorrente** (`assets/js/contact-form.js`): adicionada variável
   `isSubmitting` para impedir duas submissões simultâneas, como camada extra além do
   `disabled` do botão — ver §9.

Nenhuma outra alteração de código foi necessária — todo o restante (validação server-side, CSRF,
honeypot, rate limit, XSS, Hero, responsividade, departamentos) já estava correto na
implementação original.

---

## 13. Console / rede

- Nenhum erro JavaScript próprio (não tratado) em nenhum dos 5 viewports.
- Nenhum `404` — todas as 23 requisições de origem própria (`127.0.0.1:8099/...`) retornaram
  `200`.
- `Content-Type: application/json; charset=UTF-8` confirmado em todas as respostas do endpoint
  (via `curl -I`).
- Nenhuma dependência WordPress, Elementor ou jQuery.
- Nenhuma biblioteca nova (nenhum `<script src>` externo além do já existente Google Maps do
  footer global).
- Ruído externo já conhecido (extensão Kaspersky do navegador de teste, script/iframe do Google
  Maps) separado dos erros do projeto — não é um problema desta página.

---

## 14. Testes mínimos obrigatórios (PART Q) — todos re-executados nesta validação

| # | Teste | Resultado |
|---|---|---|
| 1 | `GET` no endpoint (headers AJAX) | `405` ✓ |
| 2 | `POST` sem CSRF | `403` ✓ |
| 3 | CSRF incorreto | `403` ✓ |
| 4 | E-mail inválido | `422` com `errors.email` ✓ |
| 5 | Campo acima do limite (mensagem 6000 car.) | `422` com `errors.message` ✓ |
| 6 | Honeypot preenchido | `200` sucesso simulado, **Mailpit confirmou 0 e-mails gerados** ✓ |
| 7 | Envio válido | `200`, capturado pelo **Mailpit local** (From/To/Reply-To/assunto/corpo corretos) ✓ |
| 8 | Reenvio imediato | `429` ✓ |
| 9 | Falha simulada de transporte | ver nota abaixo — **não foi possível reproduzir neste ambiente** |
| 10 | Fluxo com JavaScript | confirmado end-to-end no navegador (sucesso + erro de campo + duplo envio bloqueado) ✓ |
| 11 | Fluxo sem JavaScript | confirmado via POST cru + redirecionamento 303 + banner estático ✓ |

### Nota sobre o teste #9 (falha simulada do `mail()`)

Tentativas de forçar `mail()` a retornar `false` foram feitas sobrescrevendo `sendmail_path`
(caminho inexistente), depois `SMTP`/`smtp_port` (host/porta inalcançáveis), e por fim um
executável de `sendmail_path` que sempre retorna código de saída de falha. **Em nenhum dos três
casos `mail()` retornou `false`** neste ambiente (Windows + PHP 8.4 + Laragon) — isso é uma
limitação já documentada da implementação `mail()` do PHP no Windows, que historicamente nem
sempre propaga falhas de transporte como retorno `false` (diferente do comportamento mais
confiável em Linux/Unix com `sendmail_path`). **Isto não é um defeito do código do projeto**: o
código (`if (!$sent) { ... retorna 500 genérico ... }`) foi revisado linha a linha e está correto
— ele responderia com erro genérico e nunca com sucesso, caso `$sent` algum dia seja `false`.
**Pendência registrada para produção**: reconfirmar este caminho específico (falha de `mail()` →
resposta 500, sem sucesso falso) em um ambiente Linux ou com um transporte SMTP real cuja falha
possa ser reproduzida de forma confiável, antes de depender 100% dele em produção.

Nenhuma mensagem foi enviada ao destinatário real da CT Price em nenhum teste — todas as
tentativas usaram e-mails fictícios (`@example.com`) e, quando efetivamente processadas, foram
capturadas pelo Mailpit local (purgado ao final de cada rodada) ou pelo ambiente de teste com
SMTP quebrado (sem qualquer entrega).

---

## 15. Pendências não bloqueantes (produção)

- **Confirmar funcionamento real do MTA/`mail()` em produção** — este código depende do servidor
  de produção ter um transporte de e-mail configurado; isso não foi e não pode ser confirmado a
  partir do ambiente de desenvolvimento local.
- **Reconfirmar especificamente o caminho de falha do `mail()`** (`mail()` retornando `false` →
  resposta 500 genérica, nunca sucesso) em ambiente Linux/produção — não reproduzível no Windows
  local nesta tarefa (ver §14, teste #9).
- **reCAPTCHA (ou equivalente)**: decisão futura, condicionada a spam real aparecer; não
  implementado nesta fase por decisão já aprovada.
- **Texto/checkbox de consentimento LGPD**: depende de conteúdo jurídico aprovado pelo cliente;
  não inventado nesta tarefa.
- **Bairro e CEP do endereço**: continuam pendentes em `config/company.php` (divergência já
  registrada em `global-data-conflicts.md`), não relacionados a esta página especificamente.
- **Rate limit por sessão é proteção básica**, não um mecanismo antiabuso distribuído — não
  substitui uma solução por IP/proxy/WAF caso spam real apareça no futuro.
- **Confirmação com o proxy/infraestrutura de produção** sobre como `$_SERVER['HTTPS']` (ou um
  cabeçalho `X-Forwarded-Proto`) é populado, para que a flag `Secure` do cookie de sessão ative
  corretamente atrás de um eventual proxy reverso (ver §7).

---

## 16. REFERENCE DRIFT

Nenhum novo. Nenhuma divergência entre esta validação e `fale-conosco-audit.md` foi encontrada
além das diferenças já conscientemente aprovadas (listadas no início da tarefa de implementação).
