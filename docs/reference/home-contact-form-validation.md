# Validação — Formulário de Contato da Home (Sprint P1)

**Referência do achado corrigido:** `docs/reference/global-final-audit.md` — "O formulário existente
na Home é apenas visual e não possui backend funcional." Este documento registra a correção; o
achado histórico permanece intacto no relatório original (não foi apagado nem reescrito).

**Data:** 2026-09-06
**Escopo:** exclusivamente o formulário "Quer receber um contato?" da Home
(`components/contact-section.php`). Nenhuma outra pendência do audit global foi tocada nesta
sprint (404, redirects, canonical, meta descriptions, links externos, CMS, Supabase).

## 1. Formulário original (antes da correção)

Estrutura visual e campos já existiam (implementados numa sprint anterior, fiel ao original):
Nome, E-mail, Telefone, Mensagem (todos marcados `required` na marcação), botão "Enviar". O
`<form>` não tinha `action` funcional nem qualquer processamento de submissão — clicar em
"Enviar" não fazia nada. Não havia CSRF, honeypot, rate limit, nem qualquer validação
server-side.

## 2. Estratégia de reutilização

**Endpoint próprio, não reuso do endpoint de Fale Conosco.** Campos da Home
(Nome/E-mail/Telefone/Mensagem) são diferentes dos de Fale Conosco
(Nome/E-mail/Empresa/Mensagem) — nenhum dos dois é subconjunto do outro (Telefone não existe em
Fale Conosco; Empresa não existe na Home). Forçar um único endpoint a aceitar os dois contratos
exigiria condicionais artificiais e colocaria em risco o fluxo de Fale Conosco já validado.
Duplicação considerada pequena e estável — não abstraída em um serviço genérico de formulários
(decisão consciente, ver seção 20 da spec desta sprint). Mesmo raciocínio aplicado ao JavaScript:
`assets/js/home-contact-form.js` é um arquivo próprio, não uma generalização de
`assets/js/contact-form.js` (prefixos de ID de campo diferentes: `contact-*` aqui, `cf-*` lá).

## 3. Endpoint utilizado/criado

**Novo:** `home-contato-action.php`, na raiz do projeto (ao lado de `index.php`, mesmo padrão de
`fale-conosco/fale-conosco-action.php` viver ao lado de `fale-conosco/index.php`). PHP puro, sem
WordPress/Elementor/framework/biblioteca externa.

## 4. Validações server-side

Nunca confia em `required`/`type=email`/`type=tel` do HTML — tudo revalidado no PHP:

| Campo    | Regra |
|----------|-------|
| name     | obrigatório, ≤150 caracteres |
| email    | obrigatório, `FILTER_VALIDATE_EMAIL`, ≤190 caracteres |
| phone    | obrigatório, ≤30 caracteres brutos, 8–13 dígitos após `preg_replace('/\D/','',...)` (mesmo critério já usado em `ouvidoria/ouvidoria-action.php`) |
| message  | obrigatório, ≤5000 caracteres |

Erro de validação → HTTP 422, JSON `{success:false, message, errors:{campo: mensagem}}` (modo
AJAX) ou redirect 303 `?status=invalid` (modo sem JS).

## 5. CSRF

Token opaco de 32 bytes (`random_bytes(32)` → hex), gerado em `index.php` e guardado em
`$_SESSION['home_contact_csrf']` — chave própria, nunca compartilhada com
`fale_conosco_csrf`/`ouvidoria_csrf`. Comparação com `hash_equals()`. POST sem token ou com token
incorreto → HTTP 403, resposta genérica ("Sua sessão expirou..."), nunca revela o valor esperado.

## 6. Honeypot

Campo `website`, oculto via CSS (`position:absolute; width:1px; height:1px; overflow:hidden;
left:-9999px` — não `display:none`), com `tabindex="-1"` e wrapper `aria-hidden="true"`, fora da
navegação por teclado e da experiência de leitores de tela. Se preenchido: resposta de sucesso
**silenciosa** (HTTP 200, mesma mensagem de sucesso real), nenhum e-mail é enviado, o rate limit
**não** é consumido (`home_contact_last_submit` não é atualizado). Testado em produção real via
curl: honeypot preenchido → `{"success":true,...}` sem envio de e-mail.

## 7. Rate limit

30 segundos entre envios válidos da mesma sessão, chave própria `home_contact_last_submit` (nunca
compartilhada com Fale Conosco/Ouvidoria). Só é atualizado após um envio **de fato aceito e
processado** — erros de CSRF/honeypot/validação nunca o consomem. Envio duplo imediato → HTTP
429, `?status=rate_limited`.

## 8. Envio de e-mail

`mail()` nativo do PHP (mesma abordagem de Fale Conosco/Ouvidoria — sem biblioteca externa).
- **Destinatário:** `$company['emails']['contato']` de `config/company.php` — nunca hardcoded; se
  ausente, aborta com HTTP 500 antes de tentar enviar.
- **From:** sempre institucional (nome/e-mail de `config/company.php`) — **nunca** o e-mail
  digitado pelo visitante (evita spoofing/SPF-DKIM).
- **Reply-To:** e-mail do visitante, só depois de `filter_var(FILTER_VALIDATE_EMAIL)`.
- **Subject:** fixo ("Novo contato pelo site - Home"), codificado em `=?UTF-8?B?...?=`, não
  contém input do usuário.
- **Header injection:** todo valor usado em cabeçalho passa por `ctprice_clean_line()` (remove
  CR/LF, colapsa espaços) antes de compor `From`/`Reply-To`.
- Se `mail()` retornar `false`: nenhuma resposta de sucesso é enviada ao cliente (HTTP 500,
  mensagem genérica), o rate limit não é atualizado, detalhe técnico só vai para `error_log()`.

## 9. Comportamento com JavaScript

`assets/js/home-contact-form.js` (novo, JS puro, sem biblioteca): `fetch()` assíncrono com
`Accept: application/json` + `X-Requested-With: XMLHttpRequest`; guarda `isSubmitting` contra
duplo submit (além de desabilitar o botão); estado "Enviando…" no botão durante a requisição;
erros por campo (`showFieldError`) associados ao `<span role="alert">` correspondente e à classe
`.contact-form__field--invalid`; feedback geral em `role="status" aria-live="polite"`; reset do
formulário **só** após sucesso confirmado; nunca usa `innerHTML` com dado vindo do usuário/servidor
(`textContent` em todos os pontos).

## 10. Comportamento sem JavaScript

POST HTML nativo (`method="post"`, `action="home-contato-action.php"`). O endpoint detecta a
ausência de `X-Requested-With`/`Accept: application/json` e responde com redirect 303 para
`/?status=<valor>` (PRG — Post/Redirect/Get, refresh não reenvia). `components/contact-section.php`
lê `?status=` em `index.php` e exibe um banner estático equivalente
(`.contact-form__static-banner--success|error`) com as mesmas cores/paleta do feedback dinâmico.

## 11. Resultado no Mailpit — LIMITAÇÃO DE AMBIENTE (não inventado)

Mailpit (`127.0.0.1:8025`) foi usado como MTA local (`sendmail_path` do PHP apontando para
`mailpit.exe sendmail`) e ficou em `0` mensagens durante **toda** a bateria de testes, mesmo em
envios que o endpoint confirmou como bem-sucedidos (`mail()` retornando `true`,
`error_get_last()` retornando `NULL`).

**Diagnóstico realizado (não presumido):**
- `php_ini_loaded_file()` e `ini_get('sendmail_path')` confirmados corretos, apontando para o
  mesmo comando que funciona manualmente.
- Rodar o **mesmo comando exato** (`mailpit.exe sendmail`) manualmente via PowerShell, fora do
  processo do Apache, **entrega a mensagem normalmente** no Mailpit.
- Conclusão: isto é uma restrição de spawn de processo do worker Apache/mod_fcgid **neste
  ambiente de sandbox**, não um defeito do código da aplicação — mesma categoria de limitação já
  documentada para `php -S` em sprints anteriores deste projeto.

**O que isso significa para esta validação:** o conteúdo exato do e-mail (destinatário, From,
Reply-To, subject, corpo) foi revisado por leitura direta do código-fonte de
`home-contato-action.php` (seção 8 acima) e não pôde ser confirmado por inspeção visual de uma
mensagem realmente recebida no Mailpit nesta sessão. Isto é registrado como uma limitação
conhecida do ambiente, não como um resultado positivo fabricado.

**Itens da bateria de 12 pontos efetivamente executados** (via curl contra o Apache real +
Chrome DevTools MCP contra o navegador real):
1. GET no endpoint → 405 ✅
2. POST sem CSRF → 403 ✅
3. CSRF incorreto → 403 ✅
4. Campos obrigatórios vazios → 422 com `errors` por campo ✅
5. E-mail inválido → 422, `errors.email` ✅ (confirmado também visualmente no navegador, DOM + screenshot)
6. Limite de tamanho (mensagem >5000 caracteres) → 422 ✅
7. Honeypot preenchido → sucesso silencioso, sem e-mail, sem consumir rate limit ✅
8. Envio válido → sucesso (`mail()` retorna `true`; entrega real ao Mailpit não confirmável — ver acima) ✅ (parcial)
9. Rate limit (segundo envio <30s) → 429 ✅
10. Duplo submit (duplo clique real no navegador) → apenas 1 requisição de rede disparada (`isSubmitting`) ✅
11. Fluxo com JavaScript → confirmado no navegador real (feedback, reset, botão restaurado) ✅
12. Fluxo sem JavaScript → confirmado via POST cru + navegação para `?status=success`, banner estático renderizado ✅

## 12. Resultado visual — regressão da Home

Seção testada em todos os viewports pedidos, comparando com o layout aprovado em
`home-final-validation.md`. Nenhuma alteração de largura/altura das colunas, inputs, labels,
botão ou footer; nenhum overflow horizontal.

| Viewport | Resultado |
|---|---|
| 1440×900 | ✅ Duas colunas (30/50), idêntico ao baseline. Banner de sucesso testado — some harmoniosamente acima do botão, sem deslocar layout. |
| 900×1200 | ✅ Duas colunas, idêntico. |
| 768×1024 | ✅ Duas colunas (ainda acima do breakpoint de 767px). |
| 767×1024 | ✅ Empilhado (WhatsApp em cima, formulário embaixo) — breakpoint `max-width:767px` confirmado disparando corretamente. |
| 390×844 | ⚠️ **Limitação de ferramenta:** o `resize_page` do Chrome DevTools MCP nesta sessão não aceitou largura abaixo de 500px (piso confirmado testando 390/375/320 — todos resultaram em 500px reais). Testado na largura mínima alcançável (500×844): layout empilhado, sem overflow, consistente com o comportamento em 767px. **Não foi possível confirmar o pixel exato 390px nesta sessão** — registrado como limitação, não inventado como testado.

## 13. Regressão — Fale Conosco

Nenhum arquivo de Fale Conosco foi modificado nesta sprint (`fale-conosco/*`,
`assets/css/contact-form-section.css`, `assets/js/contact-form.js` — todos intocados). Regressão
rápida executada mesmo assim:
- UI: snapshot do DOM confirma formulário intacto (campos `name/email/company/message` + `csrf_token`/`website`, `action="/fale-conosco/fale-conosco-action.php"`).
- CSRF: POST sem token → 403 ✅
- Honeypot: preenchido → sucesso silencioso ✅
- Envio válido: sucesso ✅
- Rate limit: segundo envio imediato → 429 ✅

Nenhuma regressão encontrada.

## 14. Regressão — Ouvidoria

`config/bootstrap.php` **não** foi modificado nesta sprint (só em sprint anterior, já
revalidada). Pela própria condicional da spec ("se alterar bootstrap.php..."), uma bateria completa
não era estritamente exigida — smoke test leve executado por precaução: página carrega, formulário
intacto (campos `name/contact/email/company/message/anexos[]` + `csrf_token`/`website`,
`action="/ouvidoria/ouvidoria-action.php"`), console sem erros próprios. Nenhuma regressão
encontrada.

## 15. Console e rede (Home)

Console: nenhuma mensagem de erro própria (as únicas mensagens vistas em outras páginas durante a
sessão eram de uma extensão de antivírus do navegador — `kaspersky-labs.com` — não relacionadas ao
código do site). Rede: todos os assets da Home responderam 200/304, incluindo
`assets/css/contact-section.css` e `assets/js/home-contact-form.js` (cache HTTP funcionando
normalmente). Nenhum 404. Nenhuma dependência de WordPress/Elementor/jQuery. Nenhuma biblioteca
nova adicionada.

## 16. Diferenças conscientes / pendências mantidas

- **Sem reCAPTCHA** — decisão explícita desta sprint (proteção via honeypot + rate limit).
- **Sem checkbox/texto de LGPD** — não inventado; pendência que pode continuar registrada.
- **Endpoint próprio** em vez de reuso do endpoint de Fale Conosco — ver seção 2.
- **Entrega real ao Mailpit não confirmável** neste ambiente de sandbox — ver seção 11.
- **Viewport 390px exato não alcançável** pela ferramenta de teste nesta sessão — ver seção 12.

## 17. Arquivos criados/modificados

**Novos:**
- `home-contato-action.php`
- `assets/js/home-contact-form.js`
- `docs/reference/home-contact-form-validation.md` (este documento)

**Modificados:**
- `index.php` — geração/verificação de CSRF, leitura de `?status=`, `$contactFormAction`, script `home-contact-form.js`.
- `components/contact-section.php` — CSRF hidden field, honeypot, `data-field`, `aria-describedby`, `autocomplete`, spans de erro, feedback dinâmico, banner estático, docblock atualizado.
- `assets/css/contact-section.css` — estilos de honeypot/erro/feedback/banner (só adições, nenhuma regra pré-existente alterada).

## 18. Status deste P1

**Resolvido.** O formulário visual existente na Home agora envia mensagens de forma funcional e
segura (CSRF, honeypot, rate limit, validação server-side completa, proteção contra header
injection), sem depender de WordPress e sem alterar a composição visual aprovada da página. A
única ressalva registrada é a impossibilidade de confirmar a entrega final ao MTA local
(Mailpit) dentro desta sandbox — limitação de ambiente já teorizada e agora reconfirmada,
documentada honestamente na seção 11, não uma falha do código do endpoint.

O achado original permanece registrado, sem alteração, em `docs/reference/global-final-audit.md`.
