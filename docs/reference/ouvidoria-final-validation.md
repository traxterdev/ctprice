# Validação final full-page, funcional e de segurança — `/ouvidoria/`

Data: 2026-08-30
Documentação-base: `docs/reference/ouvidoria-audit.md`, `docs/reference/reference-baseline.md`,
`docs/reference/fale-conosco-final-validation.md`

Escopo: validação definitiva de `/ouvidoria/` — a página com o fluxo público mais sensível do
projeto até aqui (manifestação textual + anexos). Nenhum arquivo de teste foi enviado ao
destinatário real da CT Price — todos os envios de e-mail desta validação foram capturados
localmente pelo Mailpit e purgados ao final de cada rodada. Nenhum commit foi feito.

Durante esta validação, **uma correção real foi encontrada e aplicada** (ver §"Correções
realizadas") — um caso de `post_max_size` excedido que produzia uma mensagem enganosa ao usuário.

---

## PARTE A — Full-page (5 viewports)

Ordem confirmada no DOM em 1440×900, 900×1200, 768×1024, 767×1024 e 390×844: topbar → header →
Hero → bloco institucional texto+foto → bloco Confidencialidade+"Quando utilizar" → WhatsApp
exclusivo+formulário → footer → bottom bar → WhatsApp global (fixo) → cookie banner (fixo).

- **Sem gaps/sobreposições**: em todos os 5 viewports, o `top` de cada seção é exatamente igual
  ao `bottom` da anterior.
- **`scrollWidth === clientWidth`** confirmado nos 5 viewports: 1425/1425, 885/885, 753/753,
  752/752, 375/375 — nenhum overflow horizontal.
- Nenhuma tentativa foi feita de igualar a altura total ao WordPress.

**Resultado: aprovada sem ressalvas.**

---

## PARTE B — Hero e conteúdo institucional

| Item | Confirmado |
|---|---|
| `boxed-hero.php` | Sem alteração (`git diff` vazio) |
| Altura | 400px nos 5 viewports |
| Imagem | `ouvidoria.png` |
| `background-position` | `50% 50%` |
| Eyebrow/título | "ouvidoria" / "Na CT Price, sua voz é nossa prioridade." |

Conteúdo renderizado comparado literalmente, frase por frase, contra
`docs/reference/ouvidoria-audit.md` §3: texto de finalidade da Ouvidoria, texto de
confidencialidade, heading "Quando utilizar a Ouvidoria?", os 3 itens da lista, e a introdução do
bloco do formulário — **idênticos em todos os pontos**, nenhuma palavra reescrita.

**Resultado: aprovada sem ressalvas.**

---

## PARTE C — Assets

| Asset | Dimensões/formato | Status |
|---|---|---|
| `assets/images/pages/ouvidoria/ouvidoria.png` | 1200×600, PNG, 843.910 bytes | OK |
| `assets/images/pages/ouvidoria/atendente.png` | 1024×1024, PNG, 453.329 bytes | OK |
| `assets/images/logo/Isotipolinear.png` | 1080×1080, PNG, 16.237 bytes | OK, compartilhado |

- Confirmado: **nenhuma cópia antiga** de `Isotipolinear.png` restante em
  `assets/images/pages/fale-conosco/` (listagem do diretório mostra apenas `maosdadas.jpg` e
  `pgcontato.jpg`) — única cópia existe em `assets/images/logo/`.
- `/fale-conosco/` carrega a imagem do novo caminho corretamente (200, 1080×1080).
- Nenhum 404 em nenhum asset em nenhuma das duas páginas.

**Resultado: aprovada sem ressalvas.**

---

## PARTE D — WhatsApp exclusivo

- **Única fonte confirmada**: `grep` no projeto mostra o número/URL literais apenas em
  `config/company.php` (mais uma menção em comentário de exemplo no componente, não um valor
  usado) — nenhuma duplicação real.
- Renderizado: `href="https://wa.me/5567991103140"`, `target="_blank"`,
  `rel="noopener noreferrer"`, `aria-label="Canal exclusivo da Ouvidoria pelo WhatsApp: (67)
  99110-3140"`.
- Confirmado como **distinto** do WhatsApp global (`(67) 99261-6117`) e do WhatsApp da topbar
  (`(67) 99232-4097`) — não foi substituído por nenhum dos dois.

**Resultado: aprovada sem ressalvas.**

---

## PARTE E — Formulário

Ordem e estrutura confirmadas: honeypot (oculto) → Nome → Contato → E-mail → Empresa → Mensagem
→ Anexar evidências (opcional) → Enviar. Obrigatórios: Nome/Contato/E-mail/Empresa/Mensagem;
upload opcional — exatamente como especificado.

- `autocomplete`: `name`/`tel`/`email`/`organization` nos 4 campos aplicáveis (Mensagem, um
  texto livre, não tem token padrão de autocomplete — consistente com o mesmo padrão já usado em
  Fale Conosco).
- Indicação de obrigatoriedade: visual (`*`) **e** textual/acessível (`sr-only "(obrigatório)"`)
  em todos os 5 campos obrigatórios.
- Hint do upload (`aria-describedby`) visível e associado corretamente.
- Erros inline testados ao vivo (campos Nome/Contato/E-mail inválidos): banner
  "Verifique os campos destacados." + 3 mensagens específicas por campo renderizadas
  corretamente no DOM.
- Foco: `:focus-visible` confirmado ativo via `Tab` real; honeypot corretamente pulado na
  tabulação (`tabindex="-1"`).

**Resultado: aprovada sem ressalvas.**

---

## PARTE F — CSRF / Honeypot / Rate limit

| Verificação | Resultado |
|---|---|
| Token CSRF | 64 caracteres hex (32 bytes de `random_bytes`), chave de sessão `ouvidoria_csrf` (própria, distinta de `fale_conosco_csrf`) |
| POST sem CSRF | 403 |
| POST com CSRF incorreto | 403 |
| Honeypot preenchido | 200 silencioso, **0 e-mails** enviados (confirmado no Mailpit), upload não processado |
| Honeypot fora do teclado/leitor de tela | Confirmado (`tabindex="-1"`, `aria-hidden` no wrapper) |
| Rate limit — chave | `ouvidoria_last_submit` (própria, distinta de Fale Conosco) |
| Rate limit — 3 erros de validação seguidos | **Não consumiram o limite** (envio válido em seguida retornou 200, não 429) |
| Rate limit — reenvio imediato após sucesso | 429, mensagem tratada corretamente |

**Resultado: aprovada sem ressalvas.**

---

## PARTE G — Upload e limites do PHP

Valores efetivos confirmados neste ambiente (`php --ini` + `ini_get`):

| Diretiva | Valor efetivo | Limite da aplicação | Compatível? |
|---|---|---|---|
| `upload_max_filesize` | 2G | 5MB por arquivo | Sim — limite do PHP é muito maior, nunca é o fator restritivo aqui |
| `post_max_size` | 2G | 10MB no total | Sim, pelo mesmo motivo |
| `max_file_uploads` | 20 | 3 arquivos | Sim |

**Nenhuma incompatibilidade neste ambiente** — o `php.ini` do Laragon usado aqui é generoso o
suficiente para nunca ser o fator limitante; os limites da aplicação (3/5MB/10MB) são sempre os
primeiros a se aplicar. `php.ini` do projeto não foi alterado.

### Detecção de `post_max_size` excedido (CORREÇÃO APLICADA nesta validação)

**Achado**: em um cenário onde o corpo da requisição excede `post_max_size` (não reproduzível
com os valores generosos deste ambiente sem uma configuração dedicada — testado via uma segunda
instância temporária do servidor de desenvolvimento, `-d post_max_size=1M`, exclusivamente para
esta verificação, sem tocar o `php.ini` do projeto), o PHP zera `$_POST` e `$_FILES` **antes** do
nosso código rodar. Sem tratamento específico, isso cairia direto no passo de CSRF (que também
depende de `$_POST`) e responderia **"Sua sessão expirou"** — uma mensagem enganosa para uma
situação que na verdade é "o anexo é grande demais".

**Correção aplicada**: adicionado um passo (1.5) em `ouvidoria/ouvidoria-action.php`, entre a
checagem de método e a checagem de CSRF, que compara `$_SERVER['CONTENT_LENGTH']` (informado
pelo próprio cliente) contra `post_max_size` convertido para bytes (`ctprice_ouvidoria_ini_bytes()`,
nova função pequena, isolada, sem gerenciar upload progress nem infraestrutura adicional). Só
dispara quando `$_POST`/`$_FILES` chegam vazios **e** o `Content-Length` declarado excede o
limite — uma requisição legitimamente pequena nunca cai nesse caminho.

**Teste de confirmação**: instância temporária em outra porta com `post_max_size=1M`, corpo de
~3MB enviado → **413**, mensagem: *"O envio excede o limite permitido pelo servidor. Reduza o
tamanho ou a quantidade de anexos e tente novamente."* — não mais a mensagem de CSRF.

**Observação sobre `display_errors`**: neste ambiente de desenvolvimento, `display_errors` está
ativo, e o próprio PHP imprime um aviso (`PHP Request Startup: POST Content-Length ... exceeds
the limit`) **antes** do corpo da resposta, contaminando o JSON com HTML solto. Isso é
comportamento do PHP ligado à diretiva `display_errors` (deveria estar `Off` em produção — prática
padrão de qualquer ambiente PHP, não específica deste projeto), não um defeito da nossa lógica de
detecção, que responde corretamente com 413 e a mensagem certa por trás desse ruído. O JS do
cliente (`ouvidoria-form.js`) já trata esse caso com segurança: se o `response.json()` falhar por
causa do HTML solto, cai no `.catch(() => null)` e exibe a mensagem genérica seguraem vez de
travar ou expor o aviso ao usuário. **Recomendação registrada**: confirmar `display_errors=Off`
na configuração de produção (item de infraestrutura, não deste código).

**Resultado: aprovada — 1 correção real aplicada e verificada.**

---

## PARTE H — Segurança real dos anexos

10 cenários testados via `curl`/PHP (arquivos gerados localmente, nunca reais):

| # | Cenário | Resultado |
|---|---|---|
| 1 | PDF válido | 200, aceito |
| 2 | JPG válido | 200, aceito |
| 3 | PNG válido | 200, aceito |
| 4 | Arquivo PHP renomeado para `.pdf` | **422**, "Formato de arquivo não permitido" (MIME real detectado como `text/x-php`) |
| 5 | `.jpg` com conteúdo texto simples (não-imagem) | **422**, mesma mensagem (MIME real `text/plain`) |
| 6 | Arquivo de 6MB (> 5MB) | **422**, "Cada arquivo deve ter no máximo 5MB" |
| 7 | 4 arquivos (> 3) | **422**, "Envie no máximo 3 arquivos" |
| 8 | Arquivo vazio (0 bytes) | **422**, rejeitado (MIME real `application/x-empty`, fora da lista permitida) |
| 9 | SVG com `<script>` embutido | **422**, rejeitado (`image/svg+xml` fora da lista) |
| 10 | ZIP | **422**, rejeitado (`application/octet-stream` fora da lista) |

Todos usam o MIME **real** detectado via `finfo_file()` — nunca a extensão do nome do arquivo nem
o `type` informado pelo `<input>`/navegador. `is_uploaded_file()` confirmado usado antes de
qualquer leitura. Nenhum SVG, PHP, HTML, ZIP ou executável passa pela lista de permitidos
(`application/pdf`, `image/jpeg`, `image/png`, só essas três).

**Resultado: aprovada sem ressalvas.**

---

## PARTE I — Nomes de arquivo e construção do e-mail multipart

Revisão de `ctprice_ouvidoria_build_email()`: boundary gerado internamente
(`bin2hex(random_bytes(12))`, nunca a partir de input do usuário), `Content-Type:
multipart/mixed; boundary="..."` correto, cada anexo com `Content-Transfer-Encoding: base64`
(via `chunk_split(base64_encode(...))`) e `Content-Disposition: attachment; filename="anexo-N.ext"`
— **o nome físico do anexo nunca é derivado do nome original**, sempre gerado
(`anexo-1.pdf`, `anexo-2.jpg`, ...). CRLF (`\r\n`) usado de forma consistente entre as partes MIME.

Testes com nomes de arquivo hostis (via `fetch()`/`FormData` real no navegador, para eliminar
qualquer artefato de escaping do shell de teste):

| Nome de arquivo enviado | Resultado |
|---|---|
| `comprovante simples.pdf` (espaço) | Aceito; metadado no corpo: `comprovante simples.pdf` (preservado) |
| `foto "teste".jpg` (aspas) | Aceito; metadado no corpo: `foto %22teste%22.jpg` (aspas neutralizadas pelo próprio navegador antes de chegar ao PHP) |
| `relatório açãoç.pdf` (acentos) | Aceito; metadado no corpo: `relatório açãoç.pdf` (acentos preservados corretamente) |
| `evil.pdf\r\nBcc: attacker@evil.com\r\nX-Injected: yes` (tentativa de injeção CRLF) | Aceito como nome de arquivo; **nenhum cabeçalho injetado** — `Bcc` continua vazio, `To` continua só o destinatário institucional, nenhum `X-Injected`; o conteúdo malicioso aparece apenas como texto inerte no corpo (`evil.pdf%0D%0ABcc: ...`, com `%0D%0A` = CRLF já neutralizado pelo próprio navegador) |

Em todos os 4 casos, o anexo físico no e-mail permaneceu `anexo-1.pdf`/`anexo-2.pdf` (nome
gerado) — o nome original, mesmo hostil, nunca influencia o `Content-Disposition` real nem
qualquer outro cabeçalho, apenas aparece como texto simples (já neutralizado) no corpo. **Nenhuma
quebra de header, nenhuma injeção de MIME, nenhuma quebra de `Content-Disposition` possível.**

(Uma tentativa equivalente via `curl -F` com nome contendo aspas falhou por uma limitação da
própria sintaxe `-F` do curl no Windows — não relacionada ao servidor; substituída pelo teste via
`fetch()` real acima, mais autoritativo por não passar por nenhuma camada de escaping de shell.)

**Resultado: aprovada sem ressalvas.**

---

## PARTE J — Temporários

Confirmado em todos os caminhos de saída testados (sucesso sem anexo, sucesso com 1/3 anexos,
rejeição por MIME, rejeição por tamanho, rejeição por quantidade, honeypot, CSRF, rate limit,
`post_max_size` excedido): o diretório de upload temporário usado nos testes
(`.php-upload-tmp/`, criado só para viabilizar os testes locais — ver PARTE K) **permaneceu vazio
após cada rodada**, incluindo a bateria completa de ~25 requisições desta validação.

Revisão de código confirma por quê: cada arquivo é lido (`file_get_contents`) e **imediatamente**
`unlink()`ado dentro do próprio laço de validação (passo 6), **antes** de qualquer tentativa de
envio (passo 8) — ou seja, no momento em que `mail()` é chamado, nenhum arquivo temporário da
aplicação já existe mais, independente do resultado do envio. No caso específico de "mais de 3
arquivos" (rejeitado antes de entrar no laço), a limpeza é feita pelo próprio mecanismo automático
do PHP ao fim da requisição (nenhum arquivo chega a ser tocado pelo nosso código nesse caminho) —
confirmado vazio por observação direta após o teste correspondente.

**Resultado: aprovada sem ressalvas.**

---

## PARTE K — Ambiente Laragon / `upload_tmp_dir`

- **Nenhum caminho de `upload_tmp_dir` hardcoded** em nenhum arquivo PHP da aplicação (`grep`
  confirma — a única menção a `upload_tmp_dir` no código é um comentário explicando a peculiaridade
  do ambiente, não um valor usado).
- **Nenhum `.ini` de teste entrou no projeto** — o diretório `.php-upload-tmp/` foi criado e
  removido manualmente fora do controle de versão, nunca commitado, e não aparece em nenhum
  `git status` desta sessão (confirmado após limpeza).
- **Achado durante a limpeza**: uma rodada de testes desta validação deixou temporariamente
  arquivos de teste soltos na raiz do projeto (cookies de sessão de teste, páginas HTML baixadas,
  PDFs/imagens de teste com nomes especiais) — **todos removidos** antes de finalizar; `git
  status` final confirma apenas os arquivos de implementação legítimos.
- A peculiaridade em si (`upload_tmp_dir` padrão do Windows não gravável pelo servidor de
  desenvolvimento embutido do PHP nesta máquina, mesmo com o caminho longo resolvido) está
  registrada apenas em documentação (aqui e no relatório de implementação) — não influencia
  nenhum código da aplicação, que usa exclusivamente o mecanismo padrão `$_FILES` do PHP.

**Resultado: aprovada sem ressalvas — nenhuma configuração de ambiente vaza para o repositório.**

---

## PARTE L — Transporte de e-mail

- Destinatário: `$company['emails']['contato']` (nenhum endereço hardcoded no endpoint).
- `From`: sempre institucional (`razao_social` + `emails.contato`).
- `Reply-To`: só o e-mail do denunciante, só após `FILTER_VALIDATE_EMAIL`.
- Headers protegidos via `ctprice_clean_line()` (remove CR/LF).
- `mail() === false` nunca gera resposta de sucesso (revisado no código, ver PARTE M).

### Testes via Mailpit (rodada final limpa)
| Cenário | Resultado |
|---|---|
| Sem anexo | Corpo `text/plain` correto, todos os campos presentes |
| Com 1 anexo | `multipart/mixed` correto, 1 anexo (`anexo-1.pdf`), **byte-idêntico ao original** (`diff` sem diferença) |
| Com 3 anexos | 3 anexos (`anexo-1.pdf`/`anexo-2.jpg`/`anexo-3.png`), **todos byte-idênticos aos originais** |

Todas as mensagens de teste foram purgadas do Mailpit ao final (`DELETE /api/v1/messages`,
`"total":0` confirmado). **Nenhuma manifestação real foi enviada à CT Price.**

**Resultado: aprovada sem ressalvas.**

---

## PARTE M — Falha do transporte

Não foi feita nova tentativa de forçar `mail()` a retornar `false` neste ambiente Windows — a
impossibilidade já foi estabelecida e documentada em `fale-conosco-final-validation.md` (mesmo
PHP/ambiente) e se aplica igualmente aqui. Em vez disso, revisão de código:

- O branch `if (!$sent)` existe, registra em `error_log()` (sem detalhes técnicos na resposta) e
  responde 500 com mensagem genérica segura, mencionando o WhatsApp exclusivo como alternativa —
  nunca uma resposta de sucesso.
- Confirmado por leitura do código (PARTE J): a limpeza de temporários já está 100% completa
  **antes** de `mail()` ser chamado — o branch de falha não precisa (e não tem) lógica de limpeza
  própria, porque não há mais nada para limpar nesse ponto.
- **Pendência registrada**: reconfirmar em ambiente Linux/produção que `mail()` retorna `false`
  de forma confiável quando o transporte falha (mesma pendência já aberta para Fale Conosco, não
  específica desta página).

**Resultado: aprovada — nenhum resultado inventado, pendência de produção reconfirmada.**

---

## PARTE N — Segurança de saída / XSS

- `assets/js/ouvidoria-form.js`: **nenhum uso de `innerHTML`** — só `.textContent` em todos os 4
  pontos de escrita dinâmica (`grep` confirma).
- `ouvidoria/index.php`: `$_GET['status']` comparado por `===` contra exatamente 4 strings
  literais (`success`/`rate_limited`/`invalid`/`error`) — o valor bruto do parâmetro **nunca** é
  ecoado; só a mensagem pré-definida pela nossa própria aplicação é exibida.
- `components/ombudsman-form-section.php`: mesmo essa mensagem já confiável passa por
  `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')` (defesa em profundidade).
- **Teste de confirmação**: `curl "http://.../ouvidoria/?status=<script>alert(1)</script>"` →
  **nenhum banner é renderizado** (0 ocorrências de `.ombudsman-form__banner` na resposta) — o
  valor não-whitelisted é simplesmente ignorado, não refletido de nenhuma forma.
- Nomes de arquivo (PARTE I) nunca são refletidos sem `htmlspecialchars` em nenhum contexto HTML
  (o único lugar onde aparecem é no corpo `text/plain` do e-mail, que não é HTML).

**Resultado: aprovada sem ressalvas.**

---

## PARTE O — Fluxo sem JavaScript

Reproduzido via `curl` com POST `multipart/form-data` nativo, sem os cabeçalhos que o JS envia
(`X-Requested-With`/`Accept: application/json`) — exatamente o que um navegador sem JavaScript
enviaria:

| Cenário | Resultado |
|---|---|
| Envio válido com anexo | **303** → `Location: /ouvidoria/?status=success` |
| Envio com anexo de MIME inválido | **303** → `Location: /ouvidoria/?status=invalid`; página final exibe banner: *"Verifique os dados informados (e os anexos, se houver) e tente novamente."* — mensagem compreensível, menciona explicitamente os anexos |
| Requisição subsequente (GET na URL de redirecionamento) | Uma nova aba/refresh dessa URL é sempre um **GET** — o padrão Post/Redirect/Get elimina estruturalmente qualquer reenvio acidental do formulário |

**Resultado: aprovada sem ressalvas.**

---

## PARTE P — Fluxo com JavaScript

- `new FormData(form)` usado diretamente — `Content-Type`/boundary **nunca** definidos
  manualmente (`grep` confirma: só `Accept`/`X-Requested-With` no objeto `headers`), deixando o
  navegador gerar o multipart e o boundary automaticamente.
- Guarda de duplo envio: `form.requestSubmit()` disparado duas vezes sincronamente → confirmado
  **exatamente 1** requisição `POST` para `ouvidoria-action.php` na aba de rede.
- Erros por campo renderizados corretamente (Nome/Contato/E-mail testados ao vivo).
- Sucesso: feedback verde exibido, `form.reset()` confirmado (campo Nome e `<input type="file">`
  ambos vazios após).
- Nenhum erro de console em nenhum dos testes.

**Resultado: aprovada sem ressalvas.**

---

## PARTE Q — Regressão de Fale Conosco

| Item | Resultado |
|---|---|
| Imagem decorativa carrega | Sim, `/assets/images/logo/Isotipolinear.png`, 200, 1080×1080 |
| 404 | Nenhum |
| Formulário | Testado com envio válido — 200, sucesso |
| CSRF | Funcional (mesmo padrão, chave própria `fale_conosco_csrf`, não afetada pela mudança) |
| UI (desktop 1440×900 e mobile 390×844) | Sem mudança visual, sem overflow (375/375 em mobile) |
| Console | Sem erros novos |
| Leitura de `config/company.php` após adicionar a chave `ouvidoria` | `whatsapp_principal`, `departamentos` (5 itens), `emails.contato` — todos intactos e lidos corretamente, confirmado via script PHP direto |

**Resultado: aprovada sem ressalvas.**

---

## PARTE R — Sessão

- `Set-Cookie` confirmado idêntico nas duas páginas: `HttpOnly; SameSite=Lax` (sem `Secure` em
  HTTP puro local, como projetado — ativaria automaticamente em HTTPS pela lógica já existente em
  `ctprice_configure_session_cookie()`).
- **Uma única função** de configuração de sessão (`config/bootstrap.php`), chamada pelos 4
  arquivos com formulário (`ouvidoria/index.php`, `ouvidoria/ouvidoria-action.php`,
  `fale-conosco/index.php`, `fale-conosco/fale-conosco-action.php`) — nenhuma segunda
  inicialização de sessão criada.
- Chaves de CSRF (`ouvidoria_csrf` × `fale_conosco_csrf`) e de rate limit (`ouvidoria_last_submit`
  × `fale_conosco_last_submit`) confirmadas distintas por `grep` — os dois formulários operam de
  forma independente dentro da mesma sessão PHP.

**Resultado: aprovada sem ressalvas.**

---

## Correções realizadas nesta validação

1. **Detecção de `post_max_size` excedido** (`ouvidoria/ouvidoria-action.php`): adicionado um
   passo de verificação (novo, isolado, ~12 linhas) entre a checagem de método e a checagem de
   CSRF, mais uma função auxiliar pequena (`ctprice_ouvidoria_ini_bytes()`) — evita que esse
   cenário específico produza a mensagem enganosa "sua sessão expirou". Testado e confirmado (ver
   PARTE G).

Nenhuma outra correção foi necessária — todos os demais itens auditados (PARTES A–R) já estavam
corretos como entregues na implementação.

---

## Console e rede

Nenhum erro JavaScript próprio em nenhuma das páginas/viewports testados (a única mensagem
observada em qualquer teste é o mesmo ruído de extensão de navegador já documentado em validações
anteriores — `[debug] Search endpoint requested!`). Nenhum 404 em nenhum asset próprio. Nenhuma
dependência WordPress/Elementor/jQuery/reCAPTCHA. Nenhuma biblioteca nova.

---

## Pendências de cliente / LGPD / anonimato (não bloqueantes para esta etapa)

Registradas explicitamente, sem inventar respostas:

1. **Definir se a Ouvidoria aceitará manifestações anônimas** — hoje Nome/Contato/E-mail/Empresa
   continuam obrigatórios, igual ao original, por não haver autorização para essa mudança de
   política de negócio.
2. **Aprovar texto de privacidade/LGPD específico** — nenhum texto jurídico foi inventado; a
   página não exibe nenhum aviso de privacidade/consentimento até que um texto aprovado exista.
3. **Definir política de retenção de manifestações/anexos** — hoje não há retenção nenhuma (nada
   é armazenado; tudo segue por e-mail e os temporários são removidos imediatamente) — uma
   política formal de retenção é uma decisão de negócio, não técnica.
4. **Confirmar destinatário/e-mail exclusivo da Ouvidoria** — usa-se `emails.contato` (geral)
   provisoriamente; nenhum endereço dedicado foi inventado.
5. **Confirmar se anexos devem efetivamente seguir por e-mail** (vs. um mecanismo diferente,
   como armazenamento externo com link seguro) — implementado via e-mail por ser a única via já
   validada e disponível nesta fase (sem banco/CMS).
6. **Confirmar se os limites de 3 arquivos/5MB/10MB atendem à operação real** — valores
   conservadores escolhidos como ponto de partida seguro, não uma medição de necessidade real do
   negócio.

---

## Diferenças conscientes reconfirmadas (não regressões)

Endpoint PHP próprio; remoção do Elementor Forms; ausência temporária de reCAPTCHA (honeypot +
rate limit no lugar); CSRF/honeypot/rate limit com chaves próprias; upload restrito e validado
por MIME real; identificação obrigatória preservada; anonimato e texto LGPD não inventados sem
decisão do cliente; e-mail institucional geral usado provisoriamente como destinatário; WhatsApp
exclusivo centralizado em `config/company.php`; `Isotipolinear.png` movido para localização
compartilhada (`assets/images/logo/`).
