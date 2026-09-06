# Validação — 404, roteamento Apache e redirects 301 (Sprint P1)

Data: 2026-09-06
Referência do achado corrigido: `docs/reference/global-final-audit.md`, seção 39 ("nenhuma
página 404 customizada existe") e seções 41/42 ("plano de redirecionamentos 301, recomendação,
não implementado"). O achado histórico permanece intacto naquele relatório — este documento
registra a correção.

**Escopo desta sprint**: página 404 própria, integração com Apache, redirects 301 comprovados,
validação de slash/no-slash, documentação do plano de redirects. NÃO tratado (propositalmente):
canonical institucional, meta descriptions, `expose_php`, links externos quebrados de
Parceiros/Depoimentos, pendências CT Price, redes sociais, CMS, Supabase (regra permanente do
projeto — não utilizado, não iniciado).

---

## 1. Estado anterior

- Nenhum `404.php` (ou equivalente) existia no projeto.
- Nenhum `ErrorDocument` configurado em `.htaccess`.
- Nenhum redirect 301 implementado — só um plano/recomendação registrado em
  `global-final-audit.md` §41/42, nunca aplicado.
- `.htaccess` (raiz) já existia da sprint P0 anterior (bloqueio de `.git`/`docs`/diretórios
  internos), lido integralmente antes de qualquer alteração e **não substituído** — só
  evoluído, com as proteções P0 preservadas nas mesmas posições relativas.

---

## 2. Ambiente de teste

Confirmado novamente antes de qualquer teste — mesmo Apache real já validado na sprint P0, não
`php -S` (que ignora `.htaccess` por completo):

- Apache/2.4.66 (Win64), `mod_fcgid` (PHP 8.4.12).
- Vhost `C:\laragon\etc\apache2\sites-enabled\auto.ctprice.traxter.conf`:
  `DocumentRoot "C:/laragon/www/ctprice"`, `ServerName ctprice.traxter`,
  `ServerAlias *.ctprice.traxter`, `AllowOverride All`, `Require all granted`.
- `httpd.exe -t` confirmou sintaxe válida antes de iniciar o servidor, em cada revisão do
  `.htaccess` desta sprint.
- `mod_rewrite` confirmado ativo (todos os redirects/bloqueios dependem dele e funcionaram).
- `mod_dir` confirmado ativo (resolve `/sobre-nos` → `/sobre-nos/` automaticamente, sem regra
  própria — seção 11 abaixo).

---

## 3. Estratégia adotada para a 404

Handler PHP próprio (`404.php`, raiz do projeto — mesmo nível de `index.php`), **não** um front
controller/router: não reescreve nenhuma URL desconhecida para si mesmo, não decide o que
exibir a partir do caminho pedido. O Apache é quem decide chamá-lo, via `ErrorDocument 404
/404.php` — uma URL física real (página existente, asset existente) continua sendo servida
normalmente pelo próprio Apache; só o que não existe fisicamente cai no handler.

`404.php` chama `http_response_code(404)` incondicionalmente, logo após `require
config/bootstrap.php` e antes de qualquer saída HTML — garante HTTP 404 real tanto via
`ErrorDocument` quanto em acesso direto (`/404.php`).

Reaproveita os mesmos globais de qualquer página pública: `includes/topbar.php`,
`includes/header.php`, `includes/footer.php`, `includes/cookie-banner.php`,
`includes/whatsapp-button.php` — nenhum header/footer/menu/WhatsApp/banner de cookie próprio foi
criado. CSS próprio mínimo em `assets/css/error-page.css` (só usado por esta página — não virou
componente genérico de erros).

---

## 4. Arquivo/handler criado

- `404.php` (novo, raiz).
- `assets/css/error-page.css` (novo).

Conteúdo: `<h1>Página não encontrada</h1>`, texto "O endereço informado não foi encontrado ou
pode ter sido alterado.", CTA principal "Voltar para o início" → `/`, link secundário discreto
"Precisa de ajuda? Fale Conosco" → `/fale-conosco/`. Número "404" decorativo, `aria-hidden="true"`
(não duplicado para leitor de tela, que já ouve o H1). `<meta name="robots" content="noindex,follow">`,
sem `canonical`. Não expõe `REQUEST_URI`/caminho físico/stack trace em nenhum momento — o arquivo
não lê `$_SERVER['REQUEST_URI']`.

Visual: gradiente `linear-gradient(180deg, #00222C 0%, #057038 100%)` — o mesmo já usado em
`.article-header` (cabeçalho editorial dos posts), não uma cor nova. Botão reaproveita `.btn`
(global, `assets/css/header.css`) + uma cópia escopada de `.btn--filled` (mesmo padrão de
duplicação já usado em `services-section.css`/`contact-section.css` — `.btn--filled` nunca foi
uma classe global no projeto).

---

## 5. Resultado de URL inexistente

```
GET /rota-que-nao-existe-ctprice/  → HTTP 404, corpo = 404.php renderizado (título, H1, CTA)
```

Confirmado via `curl` (status + corpo) e via navegador real (Chrome DevTools MCP): título da aba
"Página não encontrada — CT Price", `<h1>` real, topbar/header/menu/footer/WhatsApp/cookie banner
presentes e funcionais. Não 200, não redirect para a Home, não a página padrão do Apache, não
stack trace.

---

## 6. Resultado do acesso direto à 404

```
GET /404.php  → HTTP 404
```

Confirmado: acessar o arquivo diretamente (não via `ErrorDocument`) também responde 404 — a
própria página nunca é um endpoint 200 indexável, mesmo isolada do mecanismo do Apache.

---

## 7. Visual/responsividade da 404

Chrome DevTools MCP disponível — validado nos 5 viewports pedidos, mais um bug real encontrado e
corrigido durante a própria validação (seção 21).

| Viewport | Resultado |
|---|---|
| 1440×900 | ✅ Centralizado, gradiente institucional, CTA com fundo visível (após correção), sem overflow (`scrollWidth: 1425` ≤ 1440) |
| 900×1200 | ✅ Menu em hambúrguer (breakpoint próprio do header), conteúdo central intacto, sem overflow |
| 768×1024 | ✅ Sem overflow (`scrollWidth: 753` ≤ 768) |
| 767×1024 | ✅ Header empilha (breakpoint próprio de 767px), conteúdo da 404 continua centralizado, sem overflow |
| 390×844 | ⚠️ **Mesma limitação de ferramenta já registrada em `docs/reference/home-contact-form-validation.md`**: o `resize_page` desta sessão não aceitou largura abaixo de 500px (testado 390/375/320, todos resultaram em 500px reais). Testado no mínimo alcançável (500×844): sem overflow (`scrollWidth: 485`), layout coerente. Pixel exato 390px não confirmado nesta sessão — limitação registrada, não inventada. |

Acessibilidade (item 33 da sprint): H1 real (`role="heading" level="1"`); texto compreensível,
sem jargão; CTA e link secundário são `<a>` reais (confirmado via accessibility snapshot, não
`<div role="button">`); foco visível herdado do comportamento nativo do navegador (nenhum
`outline: none` em `reset.css` nem em `error-page.css`); nenhuma dependência de imagem (o "404"
é texto puro, não uma imagem); número "404" marcado `aria-hidden="true"` (puramente decorativo,
não duplica informação do H1 para leitor de tela); navegável por teclado (mesmos componentes
globais já aprovados do header/footer).

---

## 8. Quantidade de redirects 301 implementados

**14 redirects**, todos em `.htaccess` (raiz): 10 institucionais + 3 posts + 1 alias
(`/wp/home/`). Nenhuma regra genérica `^wp/(.*)$`.

## 9. Tabela resumida dos redirects implementados

Ver tabela completa (com classificação A/B/C/D e status) em
[`docs/reference/redirect-plan.md`](redirect-plan.md). Resumo:

| # | De | Para | Confirmado (curl, Apache real) |
|---|---|---|---|
| 1 | `/wp/` | `/` | 301, 1 hop |
| 2 | `/wp/home/` | `/` | 301, direto (não passa por `/wp/`) |
| 3 | `/wp/sobre-nos/` | `/sobre-nos/` | 301 |
| 4 | `/wp/clientes/` | `/clientes/` | 301 |
| 5 | `/wp/parcerias/` | `/parcerias/` | 301 |
| 6 | `/wp/fale-conosco/` | `/fale-conosco/` | 301 |
| 7 | `/wp/informacoes/` | `/informacoes/` | 301 |
| 8 | `/wp/trabalhe-conosco/` | `/trabalhe-conosco/` | 301 |
| 9 | `/wp/ouvidoria/` | `/ouvidoria/` | 301 |
| 10 | `/wp/depoimentos/` | `/depoimentos/` | 301 |
| 11 | `/wp/arearestrita/` | `/arearestrita/` | 301 |
| 12 | `/wp/reforma-trabalhista-.../` | `/reforma-trabalhista-.../` | 301 |
| 13 | `/wp/receita-federal-.../` | `/receita-federal-.../` | 301 |
| 14 | `/wp/hello-world/` | `/hello-world/` | 301 |

## 10. URLs antigas deliberadamente não redirecionadas

| URL | Categoria | Motivo |
|---|---|---|
| `/contato`, `/contato/` | C | `global-final-audit.md` registra decisão **pendente** (cliente) sobre o destino — ambiguidade documental explícita, não implementado por instrução desta sprint |
| `/documentos` | C | Destino da Área Restrita continua `null`/quebrado — sem destino real |
| `/sh-admin`, `/sh-admin/` | C | Idem |
| `/wp/blog/` | D | Nunca existiu como página real no original (já era 404 do próprio tema) |
| `/wp/AAAA/MM/DD/...` | D | Sem conteúdo público genuíno, confirmado na auditoria dos posts |

Todas as 7 confirmadas 404 (não inventado destino algum). Detalhe/fontes em
`docs/reference/redirect-plan.md`, categorias C e D.

---

## 11. Comportamento slash/no-slash — URLs novas

| URL | Resultado |
|---|---|
| `/sobre-nos` | 301 → `/sobre-nos/` (automático, `mod_dir`) |
| `/sobre-nos/` | 200 |
| `/clientes` | 301 → `/clientes/` |
| `/clientes/` | 200 |
| `/hello-world` | 301 → `/hello-world/` |
| `/hello-world/` | 200 |

Comportamento nativo do `mod_dir` (`AllowOverride`/config padrão do Apache) — **nenhuma regra
customizada de trailing slash foi adicionada**, conforme instruído (o Apache já resolve
corretamente).

## 12. Comportamento slash/no-slash — URLs antigas

| URL | Resultado |
|---|---|
| `/wp/clientes` | 301 → `/clientes/` (direto, 1 hop) |
| `/wp/clientes/` | 301 → `/clientes/` (direto, 1 hop) |
| `/wp/hello-world` | 301 → `/hello-world/` (direto, 1 hop) |
| `/wp/hello-world/` | 301 → `/hello-world/` (direto, 1 hop) |

As duas formas (com/sem barra) são cobertas pela MESMA regra, via `/?` opcional no regex de cada
`RewriteRule` — não há regras duplicadas por variante de barra.

---

## 13. Redirect chains

**Nenhuma.** Confirmado com `curl -L -w "%{num_redirects}"` em todos os 14 redirects e nas 4
combinações de slash/no-slash da seção 12: `num_redirects=1` em 100% dos casos — sempre 1 hop
direto ao destino final. Nenhum caso de `/wp/home/ → /wp/ → /` ou `/wp/clientes → /wp/clientes/
→ /clientes/` — cada `RewriteRule` já resolve diretamente ao destino final com `[R=301,L]`.

---

## 14. Resultado do teste Host Header nos redirects

Ver `.htaccess`, comentário na seção de redirects, e `config/bootstrap.php`
(`ctprice_absolute_url()`) para o precedente já aprovado do mesmo padrão de defesa.

**Achado**: por padrão, quando a substituição de uma `RewriteRule [R=301]` é relativa ao
servidor (começa com `/`), o Apache monta o cabeçalho `Location` absoluto usando o `Host` da
própria requisição (comportamento padrão de `UseCanonicalName Off`) — um `Host` forjado seria
refletido no redirect. **Mitigado** com o mesmo modelo já aprovado em `ctprice_absolute_url()`:
uma allowlist (`ctprice.com.br`, `www.ctprice.com.br`, `ctprice.traxter`, `localhost`,
`127.0.0.1`) decide, via duas `RewriteCond`/`RewriteRule` no topo do bloco de redirects, o valor
de uma variável de ambiente (`CTPRICE_REDIRECT_HOST`) usada nas 14 regras — o domínio canônico
literal (`ctprice.com.br`) aparece **uma única vez** (regra de fallback), nunca repetido nas 14
regras individuais.

**Testes reais executados**:

| Teste | `Host` enviado | `Location` resultante |
|---|---|---|
| Host legítimo local | `ctprice.traxter` | `http://ctprice.traxter/clientes/` (refletido, correto — allowlisted) |
| Host forjado, direto | `evil.com` (`Header` manual em `/wp/clientes/`) | **404** — o próprio Apache já rejeita esse `Host` antes de chegar ao `.htaccess` (não bate com `ServerName`/`ServerAlias` do vhost local, cai num vhost padrão diferente sem este `DocumentRoot`) |
| Host forjado, com porta | `evil.com:8080` | **404** — mesmo motivo acima |
| **Host que bate no vhost mas fora da allowlist da regra** | `attacker.ctprice.traxter` (casa com `ServerAlias *.ctprice.traxter` do vhost, portanto chega ao `.htaccess`) | `http://ctprice.com.br/clientes/` — **fallback correto ao domínio canônico, `attacker.ctprice.traxter` NÃO aparece no `Location`** |

O terceiro teste é a prova real de que a mitigação funciona: é o único cenário deste ambiente
onde um `Host` não confiável efetivamente chega às regras de redirect (os dois primeiros são
barrados antes disso, por uma camada de proteção adicional específica deste vhost local — não
necessariamente presente em todo ambiente de produção, ex.: hospedagem com vhost único/catch-all
aceitando qualquer `Host`). Como a camada de vhost não pode ser assumida como garantida em
produção, a mitigação a nível de `.htaccess` foi mantida como defesa em profundidade — pequena,
centralizada, no mesmo espírito da correção já aprovada em `ctprice_absolute_url()`. Esta sprint
não se tornou um novo projeto de segurança: nenhuma regra adicional de bloqueio de `Host` foi
criada além deste ajuste pontual nos redirects.

---

## 15. Preservação das proteções P0

| Recurso | Resultado |
|---|---|
| `/.git/config` | 403 |
| `/CLAUDE.md` | 403 |
| `/docs/reference/global-final-audit.md` | 403 |
| `/config/company.php` | 403 |

Todos continuam 403 — **não** viraram a nova página 404 (a ordem das regras no `.htaccess`
garante isso: as regras de bloqueio `[F,L]` continuam terminando a avaliação antes de qualquer
coisa relacionada a `ErrorDocument`, que só entra em ação quando o próprio Apache decide que a
resposta é 404, nunca 403). Nenhuma proteção da sprint P0 foi removida, reordenada de forma a
mudar seu resultado, ou enfraquecida.

---

## 16. Smoke test das 13 URLs

Todas **200** depois da alteração do `.htaccess`: `/`, `/sobre-nos/`, `/clientes/`,
`/parcerias/`, `/fale-conosco/`, `/informacoes/`, `/trabalhe-conosco/`, `/ouvidoria/`,
`/depoimentos/`, `/arearestrita/`, `/reforma-trabalhista-.../`, `/receita-federal-.../`,
`/hello-world/`. 13/13 — nenhuma interceptada pelos novos redirects/pela 404, nenhum loop,
nenhum 403/404 indevido.

## 17. Smoke test dos formulários/endpoints

| Página | Status | Endpoint POST |
|---|---|---|
| `/` | 200 | `POST /home-contato-action.php` → 303 (rejeição lógica da aplicação — sem payload — não bloqueio de servidor) |
| `/fale-conosco/` | 200 | `POST /fale-conosco/fale-conosco-action.php` → 303 |
| `/ouvidoria/` | 200 | `POST /ouvidoria/ouvidoria-action.php` → 303 |

Os 3 endpoints continuam alcançáveis por POST através do Apache real — o `.htaccess` não os
bloqueia. Bateria completa de segurança não foi repetida (já validada nas sprints anteriores),
conforme instruído.

## 18. Smoke test dos artigos

| Slug | Status | Canonical | Aponta de volta a `/wp/`? |
|---|---|---|---|
| `reforma-trabalhista-...` | 200 | `http://ctprice.com.br/reforma-trabalhista-.../` | Não |
| `receita-federal-...` | 200 | `http://ctprice.com.br/receita-federal-.../` | Não |
| `hello-world` | 200 | `http://ctprice.com.br/hello-world/` | Não |

Relacionados continuam locais (nenhuma mudança nos componentes de artigo nesta sprint).

## 19. Smoke test de assets

`assets/css/header.css`, `assets/js/header.js`,
`assets/images/logo/LogoPreferencialColorida-1024x297.png`,
`assets/fonts/roboto-variable.woff2`, `assets/images/blog/blog01-300x155.webp` — todos **200**.

## 20. Console/rede (navegador disponível)

Console da 404: só o mesmo ruído de extensão de navegador já documentado em todas as validações
anteriores deste projeto (`kaspersky-labs.com`, `[debug] Search endpoint requested!`) e uma
mensagem esperada (`Failed to load resource: 404`) — que é a própria navegação de nível superior
para a URL inexistente reportando seu próprio status, não um recurso quebrado (confirmado via
`list_network_requests`: **todos** os CSS/JS/imagens/fontes da 404 responderam 200; o único 404
da lista é a requisição de documento principal, exatamente o comportamento esperado). Zero erro
JavaScript próprio, zero dependência WordPress/Elementor/jQuery, zero biblioteca nova.

---

## 21. Correções realizadas durante a validação

1. **Bug real, encontrado e corrigido nesta própria validação**: o CTA "Voltar para o início"
   renderizava sem plano de fundo (texto solto, sem aparência de botão) porque `.btn--filled`
   nunca foi carregado — `assets/css/error-page.css` só importava `.btn` (base, de
   `header.css`); `.btn--filled` nunca foi uma classe global no projeto (cada seção que a usa
   define sua própria cópia escopada). Corrigido adicionando `.error-page__actions .btn--filled`
   com os mesmos valores já aprovados em `services-section.css` (`padding: 12px 24px`,
   `background: #61CE70`, hover `var(--color-accent-green)`) — mesma convenção já usada no
   projeto, não uma nova regra inventada. Confirmado corrigido via screenshot antes/depois.

Nenhuma outra correção foi necessária.

---

## 22. Status deste P1

**Resolvido.** URLs inexistentes retornam uma página 404 institucional com status HTTP 404 real
(inclusive em acesso direto a `/404.php`); as 14 URLs WordPress antigas comprovadamente
equivalentes redirecionam diretamente (1 hop, sem cadeia) com HTTP 301 para suas URLs novas; as
proteções de deploy da sprint P0 continuam ativas e inalteradas em seu comportamento; as 13
páginas públicas permanecem 200/funcionais. Um Host Header não confiável não é refletido nos
redirects (mitigado com o mesmo padrão já aprovado em `ctprice_absolute_url()`). Nenhuma URL sem
mapeamento comprovado ganhou destino inventado — as 7 URLs de categoria C/D permanecem 404,
documentadas em `docs/reference/redirect-plan.md`.

**Limitações registradas honestamente** (nenhum resultado inventado):
- Viewport 390px exato não alcançável pela ferramenta de teste nesta sessão (mesma limitação já
  registrada na sprint anterior) — testado no mínimo alcançável (500px), sem overflow.
- O teste de Host Header forjado direto (`evil.com`) foi barrado por uma camada de vhost
  específica deste ambiente local antes de chegar ao `.htaccess` — a mitigação a nível de
  `RewriteRule` foi validada de forma real através de um Host que efetivamente chega às regras
  (`attacker.ctprice.traxter`, via `ServerAlias` deste vhost), não apenas por leitura de código.

---

## 23. Arquivos criados/modificados

**Criados:**
- `404.php`
- `assets/css/error-page.css`
- `docs/reference/redirect-plan.md`
- `docs/reference/404-redirects-server-validation.md` (este documento)

**Modificados:**
- `.htaccess` — adicionado bloco de redirects 301 (com a mitigação de Host Header) e
  `ErrorDocument 404 /404.php`; nenhuma regra de proteção P0 removida ou alterada.
- `docs/deploy-production.md` — adicionadas notas sobre `mod_rewrite`/`AllowOverride`/
  `ErrorDocument` e 2 itens novos no checklist de deploy.

**Não alterado**: `docs/reference/global-final-audit.md` (achado histórico preservado, conforme
instruído em todas as sprints deste projeto).
