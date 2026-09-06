# Correção do P0 — Proteção da superfície de deploy

Data: 2026-09-06
Escopo: correção exclusiva do único achado P0 de `docs/reference/global-final-audit.md` (esse
relatório permanece histórico e não foi alterado — este documento registra a resolução).

> **P0 original**: quando a raiz completa do repositório é usada como `DocumentRoot`, arquivos
> internos como `.git/config`, `CLAUDE.md`, `README.md`, `docs/` e screenshots ficam acessíveis
> via HTTP.

**Status ao final desta sprint: P0 RESOLVIDO e validado em Apache real (não só `php -S`).**

---

## 1. Causa raiz

Nenhuma regra de servidor (`.htaccess`/config de vhost) existia para negar acesso HTTP a arquivos
que não são páginas públicas. Como a arquitetura do projeto usa a raiz do repositório como
`DocumentRoot` (sem `/public/` separado), qualquer arquivo físico ali é servido pelo Apache se
nada o proibir explicitamente — confirmado por teste direto nesta sprint (seção 4).

---

## 2. Ambiente de teste usado

A auditoria original só pôde usar o servidor embutido do PHP (`php -S`), que **ignora
`.htaccess` por completo** — qualquer teste de bloqueio feito só com `php -S` seria inconclusivo.
Para esta correção, foi confirmado e usado um Apache real já disponível localmente via Laragon:

- Apache/2.4.66 (Win64) com `mod_fcgid` (PHP 8.4.12), `mod_rewrite`, `mod_alias`,
  `mod_authz_core` carregados.
- Vhost já existente (não criado nesta sprint): `C:\laragon\etc\apache2\sites-enabled\
  auto.ctprice.traxter.conf` — `DocumentRoot "C:/laragon/www/ctprice"`,
  `ServerName ctprice.traxter`, **`AllowOverride All`** (`.htaccess` habilitado), `Require all
  granted`. `ctprice.traxter` já resolve para `127.0.0.1` (entrada de hosts própria do Laragon).
- Nenhuma regra de bloqueio pré-existente foi encontrada nesse vhost ou em `httpd.conf` além da
  proteção padrão do próprio Apache a arquivos `.ht*` (`<Files ".ht*"> Require all denied
  </Files>`, já presente por padrão — não duplicada no `.htaccess` novo).
- Apache foi iniciado manualmente para esta sprint (não estava em execução) e todos os testes
  desta sprint foram feitos contra ele — não contra `php -S`.

---

## 3. Solução — duas camadas

### Camada 1 — `.htaccess` (raiz do projeto, novo)
Nega acesso HTTP direto (`403 Forbidden`, via `mod_rewrite` + flag `[F,L,NC]`) a:

- `.git/` e todo o conteúdo.
- `docs/` e todo o conteúdo.
- `CLAUDE.md`, `README.md`.
- `.env`/`.env.*` (preventivo — nenhum existe hoje, ver seção 5).
- Qualquer `*.log`.
- `.gitignore`, `.gitattributes`.
- `.serena/`, `.claude/` (config/cache de ferramentas de desenvolvimento).
- `config/`, `components/`, `includes/`, `content/`, `blog/` (diretórios internos, usados só via
  `require`/`include` do PHP — nunca por URL pública).

Mais `Options -Indexes` (desabilita listagem de diretório em qualquer pasta sem regra própria).

Todas as regras usam `[NC]` (No Case) — o filesystem alvo pode não diferenciar maiúsculas/
minúsculas; sem isso, `/CLAUDE.MD` ou `/.GIT/config` contornariam a regra (testado, seção 8).

**Deliberadamente NÃO bloqueado**: `/.well-known/` (não existe uma regra genérica "bloquear
qualquer coisa que comece com ponto" — cada caminho sensível é citado explicitamente), `assets/`
(exceto a extensão `.log` dentro dela — só o arquivo de log removido, não a pasta), páginas
públicas, e os 2 endpoints de formulário.

### Camada 2 — `.gitattributes` (raiz do projeto, novo) + `git archive`
`export-ignore` em `docs`, `CLAUDE.md`, `README.md`, `.serena`, `.gitattributes`, `.gitignore` —
um pacote de produção gerado com `git archive` já nasce sem esses caminhos, sem depender só do
`.htaccess` no servidor. Detalhe completo e checklist: `docs/deploy-production.md`.

### Consequência direta: remoção do log órfão
`assets/fonts/php_server.log` — inspecionado antes de remover: log de acesso do próprio `php -S`
(3 linhas, nenhum dado sensível, gerado por engano em 25/08 quando um servidor de desenvolvimento
foi iniciado com o diretório de trabalho errado). Não rastreado pelo Git (já coberto por
`*.log` no `.gitignore`). Removido nesta sprint porque é uma consequência direta da correção do
P0 (estava na árvore pública, dentro de `assets/`) — não uma limpeza geral de P2. A regra `*.log`
do novo `.htaccess` permanece como defesa em profundidade contra qualquer log futuro no mesmo
lugar.

---

## 4. Resultado ANTES da correção (Apache real, não `php -S`)

| Recurso | Resultado |
|---|---|
| `/.git/config` | 200 — conteúdo real (URL do remoto) |
| `/.git/HEAD` | 200 |
| `/CLAUDE.md` | 200 — conteúdo real |
| `/README.md` | 200 |
| `/docs/reference/global-final-audit.md` | 200 |
| `/docs/` | **200 — listagem real de diretório do Apache** (`Index of /docs`) |
| `/config/company.php` | 200 |
| `/config/bootstrap.php` | 200 |
| `/includes/header.php` | **200 — `Fatal error` do PHP com caminho absoluto do servidor exposto** (`Undefined constant "BASE_URL" in C:\laragon\www\ctprice\includes\header.php:33`) |
| `/blog/_post-template.php` | **200 — `Fatal error` com caminho absoluto exposto** (`RuntimeException ... in C:\laragon\www\ctprice\blog\_post-template.php:29`) |
| `/content/blog/hello-world.php` | 200 |
| `/assets/fonts/php_server.log` | 200 |
| `/.gitignore` | 200 |
| `/.env` | 404 (arquivo não existe) |

Achado adicional confirmado nesta sprint, mais grave do que a auditoria original havia
caracterizado: acessar diretamente um `include`/template interno **não só expõe o código-fonte**
— em vários casos ele **executa parcialmente como PHP** e retorna um `Fatal error` com o caminho
absoluto real do servidor no corpo da resposta (`C:\laragon\www\ctprice\...`), porque essas peças
esperam variáveis que só existem quando incluídas por uma página real.

---

## 5. Inventário de conteúdo sensível — só o que realmente existe

Verificado por busca direta no filesystem (nada presumido):

| Item | Existe? |
|---|---|
| `.git/` | Sim |
| `.gitignore` | Sim |
| `.gitattributes` | Não existia antes desta sprint (criado agora) |
| `.env` / `.env.*` | Não existe |
| `CLAUDE.md` | Sim |
| `README.md` | Sim |
| `docs/` | Sim (83 arquivos rastreados) |
| Logs | 1 — `assets/fonts/php_server.log` (removido, seção 3) |
| Temporários/dumps/backups/`.sql`/`.bak`/`.old`/`.orig` | Nenhum encontrado |
| Chaves privadas/certificados | Nenhum encontrado |
| Caches | `.serena/cache/` (do próprio Serena — protegido junto com `.serena/` no `.htaccess`) |
| Config local de ferramenta | `.claude/settings.local.json` (existe; não rastreado pelo Git; protegido só via `.htaccess`, ver seção 3) |

---

## 6. Resultado DEPOIS da correção (mesmo Apache real)

| Recurso | Resultado |
|---|---|
| `/.git/config` | **403** |
| `/.git/HEAD` | **403** |
| `/CLAUDE.md` | **403** |
| `/README.md` | **403** |
| `/docs/reference/global-final-audit.md` | **403** |
| `/docs/` | **403** (sem listagem) |
| `/docs/reference/screenshots/blog-hello-world-implementation-desktop-1440-full.png` | **403** |
| `/config/company.php` | **403** |
| `/config/bootstrap.php` | **403** |
| `/components/article-header.php` | **403** |
| `/includes/header.php` | **403** (sem `Fatal error`, sem caminho de servidor — bloqueado antes do PHP rodar) |
| `/content/blog/hello-world.php` | **403** |
| `/blog/_post-template.php` | **403** (idem) |
| `/assets/fonts/php_server.log` | **403** (arquivo também removido, seção 3) |
| `/.gitignore` | **403** |
| `/.env` | **403** (preventivo — arquivo continua não existindo) |
| `/.serena/project.yml` | **403** |
| `/.claude/settings.local.json` | **403** |

Corpo da resposta 403 confirmado limpo nos 2 casos que antes vazavam caminho de servidor —
página genérica padrão do Apache (`<title>403 Forbidden</title>`), sem nenhum dado da aplicação.

---

## 7. Diretórios internos da aplicação — HTTP bloqueado, PHP intacto

Confirmado nas 13 páginas públicas (seção 9): todas continuam 200, com conteúdo e título
corretos, **zero** `Warning`/`Notice`/`Fatal error` no HTML — ou seja, `require`/`include` do PHP
para `config/`, `components/`, `includes/`, `content/`, `blog/` continua funcionando
normalmente (leitura direta do filesystem pelo processo PHP, nunca via requisição HTTP — o
`.htaccess` só intercepta requisições HTTP reais).

---

## 8. Segurança das próprias regras (tentativas de contorno testadas)

| Tentativa | Resultado |
|---|---|
| `/CLAUDE.MD`, `/claude.md` (maiúsculas/minúsculas) | 403 |
| `/.GIT/config`, `/.Git/HEAD` | 403 |
| `/DOCS/`, `/Docs/reference/...` | 403 |
| `/CONFIG/company.php`, `/Includes/header.php` | 403 |
| `/.git` (sem barra final), `/.git//config` (barra dupla) | 403 |
| `//config/company.php` (barra dupla inicial) | 403 |
| `/docs` (sem barra final) | 403 |
| `/config/company.php/` (barra final extra) | 403 |
| `/%2e%67it/config` (`.git` URL-encoded) | 403 |

Nenhum contorno trivial encontrado. Nenhum redirecionamento em loop observado (todas as respostas
403/200 voltaram diretamente, sem cadeia de redirecionamento). Nenhum erro 500 — `httpd -t`
confirmou sintaxe válida antes de qualquer teste, e todas as 13 páginas + todos os recursos
bloqueados responderam com o código esperado, nunca 500.

---

## 9. Smoke test das 13 URLs públicas (Apache real, depois da correção)

Todas **200**, confirmadas com o mesmo Apache/`.htaccess` desta sprint: `/`, `/sobre-nos/`,
`/clientes/`, `/parcerias/`, `/fale-conosco/`, `/informacoes/`, `/trabalhe-conosco/`,
`/ouvidoria/`, `/depoimentos/`, `/arearestrita/`,
`/reforma-trabalhista-volta-a-pauta-do-stf-julgamento-acontece-neste-mes/`,
`/receita-federal-e-correios-lancam-portal-de-compras-internacionais/`, `/hello-world/`.
Conteúdo/título verificado (não só o código HTTP) em `/hello-world/` e `/ouvidoria/` — corretos,
sem PHP warning no corpo.

---

## 10. Assets — amostra representativa

Todos **200** depois da correção: `assets/css/header.css`, `assets/js/header.js`,
`assets/images/logo/LogoPreferencialColorida-1024x297.png`,
`assets/fonts/roboto-variable.woff2`, `assets/vendor/swiper/swiper-bundle.min.css`. `assets/`
não foi bloqueado genericamente — só a extensão `.log` dentro da árvore inteira do site.

---

## 11. Endpoints de formulário

| Teste | Resultado |
|---|---|
| GET `/fale-conosco/fale-conosco-action.php` | 303 (rejeição da própria aplicação, como já implementado — `.htaccess` não interferiu) |
| GET `/ouvidoria/ouvidoria-action.php` | 303 (idem) |
| POST sem CSRF `/fale-conosco/...` | 303 (aplicação, não bloqueado pelo servidor) |
| POST completo e válido (token real extraído do formulário, sessão real) | **303 → `?status=success`** — fluxo completo confirmado funcionando de ponta a ponta através do Apache real + `mod_fcgid`, não só do `php -S` |

Nenhuma bateria completa de segurança foi refeita (já validada em sprints anteriores) — só a
confirmação de que o hardening de diretórios não interferiu no funcionamento dos endpoints.

---

## 12. Estratégia/documentação de deploy

Documentada em `docs/deploy-production.md`: publicação via `git archive` (usa `.gitattributes`
com `export-ignore`, recurso nativo do Git — sem CI/CD, sem build, sem npm, sem Docker) +
`.htaccess` como segunda camada. Testado nesta sprint (`git archive --worktree-attributes`,
arquivo de teste descartado): pacote gerado corretamente sem `docs/`/`CLAUDE.md`/`README.md`/
`.serena/`, com os arquivos reais da aplicação presentes.

---

## 13. Diferenças Apache/LiteSpeed vs. `php -S`

| Comportamento | `php -S` (servidor embutido) | Apache real (`ctprice.traxter`, testado nesta sprint) |
|---|---|---|
| Lê `.htaccess`? | **Nunca** — ignora completamente, qualquer arquivo existente é servido | Sim, com `AllowOverride All` já configurado no vhost |
| Rota inexistente | Cai no fallback do `index.php` da raiz (achado já registrado na auditoria global, não confundir com 404 real) | Comportamento normal do Apache (404 real para caminho inexistente, 403 para caminho bloqueado) |
| Diretório sem index, sem `Options -Indexes` | Não aplicável (não implementa listagem de diretório do Apache) | Listaria o conteúdo (`Options -Indexes` corrige) |
| Uso recomendado | Desenvolvimento local rápido, sem validar regras de servidor | Única forma confiável de validar `.htaccess`/comportamento de produção antes do go-live |

**Nenhum teste desta sprint usou `php -S` para validar o `.htaccess`** — todos os resultados das
seções 4, 6, 7, 8, 9, 10, 11 vêm do Apache real listado na seção 2. Se o servidor de produção for
LiteSpeed em vez de Apache, a sintaxe de `.htaccess` usada aqui (`RewriteRule`/`Options`) é
compatível (LiteSpeed lê `.htaccess` no formato Apache nativamente), mas uma revalidação rápida
no ambiente real de produção antes do go-live continua recomendada.

---

## 14. Limitações desta sprint

- Validado num Apache local (Laragon), não no LiteSpeed real de produção — recomenda-se repetir
  ao menos a matriz da seção 6/8 em staging/produção antes do go-live definitivo.
- `.htaccess`/`.gitattributes` ainda não foram commitados (instrução explícita desta sprint) — o
  fluxo de `git archive` só funciona de ponta a ponta depois de um commit incluindo os dois (ver
  `docs/deploy-production.md`).
- Nenhum P1/P2/pendência de cliente foi tratado nesta sprint, propositalmente.

---

## 15. Arquivos criados/modificados

- **Criados**: `.htaccess` (raiz), `.gitattributes` (raiz), `docs/deploy-production.md`,
  `docs/reference/deploy-security-validation.md` (este documento).
- **Removido**: `assets/fonts/php_server.log` (log órfão, não rastreado — consequência direta da
  correção, seção 3).
- **Não alterado**: `docs/reference/global-final-audit.md` (permanece histórico, conforme
  instruído).
