# CMS administrativo — CT Price (Sprints 01, 02 e 03)

Fundação administrativa (Sprint 01): MariaDB próprio, autenticação, painel `/admin/`, gestão de
Clientes e Parceiros. Conteúdo editorial (Sprint 02): Vagas, Benefícios, Depoimentos e Blog.
Administração/segurança operacional/preparação para produção (Sprint 03): gestão de
administradores, rate limit persistente, limite de relacionados, procedimento de deploy. PHP puro
+ PDO — sem ORM, sem framework, sem Supabase.

**Status: operacionalmente pronto para produção** — falta apenas receber as credenciais do
servidor real, configurar o MariaDB de produção e executar o procedimento da seção
"Deploy em produção" abaixo.

## Arquitetura

- **Banco**: MariaDB/MySQL local (Laragon), banco `ctprice_site` — totalmente separado do banco
  do sistema de RH. Usuário dedicado `ctprice_site`, com `GRANT ALL PRIVILEGES` restrito a este
  banco (nunca o `root`, nem acesso a outro banco).
- **Conexão**: `includes/Database.php` — classe estática, uma única conexão PDO por requisição,
  `utf8mb4`, `ERRMODE_EXCEPTION`, prepared statements reais (`EMULATE_PREPARES => false`), fuso
  `America/Campo_Grande` (-04:00, também definido via `date_default_timezone_set()` em
  `config/bootstrap.php`).
- **Migrations**: `database/migrations/*.sql`, numeradas por data — aplicadas por
  `database/migrate.php` (CLI), que registra o que já rodou em `schema_migrations` (idempotente).
- **Camada de acesso**: um repositório por entidade (`ClientRepository`, `PartnerRepository`,
  `JobRepository`, `BenefitRepository`, `TestimonialRepository`, `BlogPostRepository`,
  `AdminUserRepository`) — únicas classes que escrevem SQL para suas tabelas. Nenhum componente/
  página monta query própria. Sem Service Layer (não há regra de negócio que justifique).
- **Autenticação**: `includes/AdminAuth.php` — sessão (reaproveita
  `ctprice_configure_session_cookie()` do bootstrap), CSRF por token de sessão,
  `session_regenerate_id()` no login, usuário revalidado no banco a cada requisição (`ativo = 1`).
  Rate limit em duas camadas — ver seção própria abaixo.
- **Upload**: `includes/Uploads.php` — MIME real via `finfo`, extensão decidida pelo MIME
  (whitelist PNG/JPEG/WEBP — SVG avaliado e descartado, nenhum uso real encontrado), nome físico
  aleatório, diretório próprio por entidade (`assets/uploads/{clients,partners,benefits,
  testimonials,posts}/`), `assets/uploads/.htaccess` nega execução de script.
- **Sanitização de HTML**: `includes/HtmlSanitizer.php` — só usado pelo corpo dos posts do blog.
  Allowlist fixa (`p, strong, em, ul, ol, li, a, h2, h3, br`), via `DOMDocument` (nunca regex).
  Tags perigosas (`script, style, iframe, object, form, ...`) removidas COM conteúdo; qualquer
  outra tag é desembrulhada (perde a tag, mantém o texto). Links: só `href` `http`/`https`
  sobrevivem: `rel="noopener noreferrer"` em links externos, `target` não é adicionado (preserva
  comportamento dos 3 artigos originais).

## Configuração local

1. Copie `config/database.example.php` para `config/database.local.php` (nunca commitado — ver
   `.gitignore`; já protegido por HTTP pelo `.htaccess`, que bloqueia todo o diretório `config/`).
2. Preencha host/porta/banco/usuário/senha do seu MariaDB local.
3. Rode as migrations: `php database/migrate.php`
4. Importe os dados estáticos atuais:
   - `php database/seed_clients_and_partners.php` (Clientes/Parceiros)
   - `php database/seed_editorial_content.php` (Vagas/Benefícios/Depoimentos/Blog)
5. Crie o primeiro administrador: `php database/create_admin.php "Nome" email@dominio.com "senha-forte"`
6. Acesse `/admin/login.php`.

Todos os comandos acima são reexecutáveis com segurança (idempotentes) e são **CLI-only** —
recusam-se a rodar via navegador (`PHP_SAPI !== 'cli'`) e, além disso, todo o diretório
`database/` já é bloqueado por HTTP pelo `.htaccess` (defesa em profundidade — duas camadas
independentes, ver "Migrations e seeds em produção" abaixo).

## Módulos disponíveis

- **Dashboard** (`/admin/`): totais ativos de Clientes, Parceiros, Depoimentos, posts publicados e
  Administradores. Nenhum analytics. (Vagas/Benefícios saíram do dashboard — ver "Vagas e
  Benefícios — retirados do CMS" abaixo.)
- **Clientes** (`/admin/clients/`): CRUD + ativar/desativar + reordenar (setas). Campos: nome,
  logo, site (opcional).
- **Parceiros** (`/admin/partners/`): idem, com categoria (`tools`/`companies`, mesma semântica de
  "Ferramentas"/"Parceiros" de `/parcerias/`) e URL opcional (preserva o caso real "Auditto", sem
  link).
- **Depoimentos** (`/admin/testimonials/`): CRUD + ativar/desativar + reordenar. Um único upload
  ("foto") alimenta `foto_path` e `thumbnail_path` — ver "Decisão: upload único" abaixo.
  `video_id`/`video_list`/URLs validados server-side.
- **Notícias/Blog** (`/admin/posts/`): criar, editar, publicar/despublicar (reaproveita a coluna
  `ativo`), definir data de publicação. **Slug só é definido na criação e nunca muda depois**
  (campo bloqueado na edição, e o servidor ignora um slug forjado no POST de edição). Sem setas de
  reordenação: a ordem pública é sempre cronológica (`published_at DESC`). Relacionados de cada
  post: no máximo 2 (ver "Correção: posts relacionados" abaixo).
- **Administradores** (`/admin/users/` — sprint 03): listar, criar (nome/e-mail/senha inicial +
  confirmação), editar nome/e-mail, redefinir senha (fluxo próprio, separado da edição de perfil),
  ativar/desativar, excluir com confirmação. Um único perfil ("Administrador") — sem RBAC.

Não implementado (ver tarefa da sprint 03, §14): RBAC, MFA, recuperação automática de senha,
editor WYSIWYG, biblioteca de mídia genérica, analytics, API, páginas institucionais editáveis,
configurações gerais da empresa.

## Vagas e Benefícios — retirados do CMS (decisão de arquitetura)

**Vagas e Benefícios não são mais módulos administráveis por este CMS.** Decisão de arquitetura:
esse conteúdo passará a vir de outro sistema já existente, que se tornará a fonte canônica dessas
duas entidades no futuro.

O que mudou nesta correção:
- Os CRUDs administrativos foram removidos: os diretórios `admin/jobs/` e `admin/benefits/` (cada
  um com `index/form/save/toggle/delete/move.php`) foram excluídos — eram totalmente isolados, sem
  nenhuma outra parte do projeto dependendo deles.
- Os itens "Vagas" e "Benefícios" saíram do menu administrativo (`admin/includes/
  layout-header.php`) e do dashboard (`admin/index.php` — cards e consultas removidos).

O que **não** mudou (de propósito, enquanto a integração com o outro sistema não existe):
- As tabelas `jobs` e `benefits` continuam no banco, com os mesmos dados.
- `repositories/JobRepository.php` e `repositories/BenefitRepository.php` continuam existindo e
  sendo usados.
- A página pública `/trabalhe-conosco/` continua lendo Vagas e Benefícios **do banco** através
  desses repositórios, exatamente como antes — não foi alterada e não ficou vazia.
- `database/seed_editorial_content.php` continua importando Vagas/Benefícios (entre outras
  entidades) — ver "Fonte canônica dos dados" abaixo para o que isso significa hoje.

**A integração com o outro sistema (fonte canônica futura) ainda NÃO foi implementada.** Enquanto
ela não existir, o banco `jobs`/`benefits` desta aplicação continua sendo a única fonte que a
página pública lê — só deixou de ser **editável por este CMS**. Quando os dados técnicos dessa
integração estiverem disponíveis, essa seção deste documento precisará ser revisada.

## Gestão de administradores (sprint 03)

`admin/users/{index,form,save,toggle,delete,reset-password}.php` + `repositories/
AdminUserRepository.php`. Sem migration nova — reaproveita a tabela `admin_users` já criada na
sprint 01 (`nome`, `email`, `password_hash`, `ativo`, `ultimo_login_em`).

Proteções obrigatórias, reforçadas NO SERVIDOR (a UI só desabilita os botões — nunca é a única
defesa):
- **Um administrador não pode desativar nem excluir a própria conta** (`toggle.php`/`delete.php`
  comparam o `id` alvo com `admin_current_user()['id']`).
- **O sistema nunca pode ficar sem nenhum administrador ativo** — vale para qualquer conta, não só
  a própria: desativar/excluir o último administrador ativo restante é bloqueado
  (`AdminUserRepository::countActive() <= 1` antes de aplicar a ação). "Ativar" nunca é bloqueado.
- E-mail único (`isEmailAvailable()`, checado antes de salvar — nunca depende só da UNIQUE KEY do
  banco).
- Senha: `password_hash()`/`password_verify()` (nunca texto puro); `admin_is_strong_password()`
  exige mínimo de 10 caracteres com pelo menos uma letra e um número (mesmo limite já usado por
  `database/create_admin.php`); confirmação de senha na criação e na redefinição; a senha **nunca**
  é exibida de volta na tela, nunca enviada por e-mail, nunca aparece em `error_log()`.
- CSRF + POST + prepared statements + validação server-side em toda operação mutável (mesmo
  padrão dos demais módulos).

Testado nesta sprint: auto-desativação bloqueada, auto-exclusão bloqueada, senha fraca rejeitada,
confirmação divergente rejeitada, e-mail duplicado rejeitado, criação/edição/redefinição de senha/
ativação/desativação/exclusão funcionais, sessão de uma conta desativada perde acesso
imediatamente (mesma revalidação a cada requisição já usada desde a sprint 01).

## Rate limit do login (reforçado na sprint 03)

Duas camadas independentes — a primeira sozinha não resiste a uma aba anônima/sessão nova:

1. **Por sessão** (desde a sprint 01): backoff exponencial (2s, 4s, 8s... até 60s) após cada falha
   consecutiva — resposta imediata, sem tocar no banco, resolve o caso comum (clique repetido).
2. **Persistente** (`admin_login_attempts`, banco — sprint 03): registra CADA tentativa (sucesso
   ou falha) com e-mail normalizado + IP (`$_SERVER['REMOTE_ADDR']`). 8 tentativas malsucedidas em
   15 minutos (por e-mail OU por IP) bloqueiam novas tentativas por essa mesma janela deslizante —
   sobrevive a uma sessão/aba nova, que zeraria a camada 1 sozinha. Linhas mais velhas que 24h são
   removidas a cada tentativa nova (sem exigir cron).

Qualquer uma das duas camadas bloqueando já é suficiente para recusar a tentativa. A mensagem ao
usuário é **sempre genérica** ("E-mail ou senha inválidos." / "Muitas tentativas. Aguarde alguns
minutos e tente novamente.") em qualquer cenário — e-mail não existe, senha errada, conta
inativa, ou bloqueio por rate limit — nunca revela qual é o caso real (evita enumeração de
contas).

**Sem suporte a `X-Forwarded-For`/proxy reverso nesta sprint** — o cabeçalho é forjável pelo
cliente; tratá-lo corretamente exige saber em que proxy confiar (decisão de infraestrutura do
servidor real de produção, não deste código). Se o servidor definitivo ficar atrás de um proxy/
load balancer, `admin_client_ip()` (`includes/AdminAuth.php`) precisará ser ajustada para ler o IP
real do cliente a partir do cabeçalho que aquele proxy específico populam de forma confiável.

Testado nesta sprint: 8 tentativas malsucedidas em sessões (abas) diferentes, todas a mesma
mensagem genérica; a 9ª tentativa, em uma sessão nova adicional, foi bloqueada mesmo usando a
senha CORRETA — confirmando que a camada persistente resiste à sessão nova.

## Correção: posts relacionados (sprint 03)

Pendência registrada nas sprints anteriores: `blog/_post-template.php` passava **todos** os posts
publicados para `related-posts.php`, que só filtrava o post atual — sem limite, a lista cresceria
indefinidamente conforme o blog crescesse.

Corrigido com `BlogPostRepository::relatedTo($slugAtual, 2)`: filtro por slug E `LIMIT 2` já na
consulta SQL (não mais só no componente) — o post atual é excluído ANTES do limite ser aplicado,
então o resultado é sempre até 2 relacionados de verdade (nunca 2 antes de excluir o atual, que
poderiam virar 1 ou 0 se o post atual estivesse entre os mais recentes). Preserva exatamente o
comportamento visual original ("os outros 2 posts"). Testado com 4 posts publicados: a página de
um post histórico mostrou exatamente 2 relacionados, os mais recentes entre os 3 restantes.

A grade "Últimas notícias" (Home/Informações) já usa `allPublished(3)` desde a sprint 02 — sem
mudança nesta sprint.

## Decisão: upload único em Depoimentos (documentada, não alterada)

O formulário de Depoimentos usa UM upload ("foto") para alimentar os dois campos do schema
(`foto_path` e `thumbnail_path`). Isso foi intencional desde a sprint 02: os 7 depoimentos reais
migrados usam o MESMO arquivo nos dois pontos (foto da pessoa + miniatura do vídeo), e o
comportamento atual funciona corretamente em produção. **Não foi refatorado nesta sprint** — os
dois campos continuam existindo separadamente no banco (para o dia em que uma miniatura de vídeo
diferente da foto da pessoa for realmente necessária), mas o formulário só ganha um segundo campo
de upload quando essa necessidade for real, não antes.

## Slugs e roteamento de posts novos

Os 3 posts históricos continuam com diretório físico próprio na raiz (ex.: `/hello-world/`) —
preservados exatamente, sem nenhuma mudança de URL. Um post **novo**, criado só em `/admin/posts/`
(sem diretório físico), é servido por uma rota nova:

- `.htaccess` (bloco "Roteamento de posts do blog"): só entra em ação quando **nenhum**
  arquivo/diretório físico responde pela URL (`RewriteCond ... !-f`/`!-d`) — nunca interfere nas
  10 institucionais, nos 3 posts históricos, em `admin/`, `assets/` ou qualquer regra já existente
  (redirects `/wp/`, bloqueios de `.git`/`docs`/etc.). Padrão de um único segmento sem "."
  (`^([a-z0-9-]+)/?$`), nunca intercepta um asset com extensão faltando.
- `blog-post.php` (raiz): recebe o slug, revalida o formato (defesa em profundidade, nunca confia
  só na regra do servidor) e delega a `blog/_post-template.php`, que busca o post no banco. Não
  encontrado/não publicado → delega para o **mesmo** `404.php` de sempre (nunca inventa conteúdo,
  nunca transforma uma URL desconhecida em artigo).

Slug: normalizado (`BlogPostRepository::slugify()` — minúsculas, só `[a-z0-9-]`, sem `/` nem
`..`), único (checado antes de salvar), e rejeitado se coincidir com um nome de diretório físico
reservado (as 10 institucionais + `admin/assets/blog/config/components/includes/content/database/
repositories` — lista fixa em `admin/posts/save.php`) — evita criar um post cuja URL nunca seria
alcançada.

## Fonte canônica dos dados

**O banco de dados é a fonte canônica** para Clientes, Parceiros, Vagas, Benefícios, Depoimentos e
Blog. As páginas públicas (`/`, `/sobre-nos/`, `/informacoes/`, `/clientes/`, `/parcerias/`,
`/trabalhe-conosco/`, `/depoimentos/`, os posts) e os componentes que as compõem não leem mais
`config/clients.php`, `config/partners.php`, `config/jobs.php`, `config/benefits.php`,
`config/video-testimonials.php`, `config/blog-posts.php` nem `content/blog/*.php`.

Esses arquivos **não foram apagados** — existem só como fonte histórica dos importadores
(`database/seed_clients_and_partners.php`, `database/seed_editorial_content.php`). Não há duas
fontes públicas simultâneas.

**Exceção temporária — Vagas e Benefícios**: o banco continua sendo a fonte que a página pública
lê hoje, mas deixou de ser **editável** por este CMS (ver "Vagas e Benefícios — retirados do CMS"
acima). Esses dois, especificamente, têm uma fonte canônica **futura** já decidida (o outro
sistema já existente) que ainda não foi integrada — quando for, o banco `jobs`/`benefits` desta
aplicação deixa de ser usado pela página pública, e esta seção precisará ser atualizada.

## Disponibilidade e erros em produção

- **Banco indisponível**: toda página pública que lê do banco envolve a chamada em `try/catch` —
  o erro vai só para `error_log()` (nunca para o navegador); a seção correspondente renderiza
  vazia, ou o post cai em 404 controlado. `includes/Database.php` nunca deixa uma `PDOException`
  (com host/usuário/senha no texto) escapar — sempre relança como `RuntimeException` com mensagem
  genérica.
- **Erro administrativo**: nenhuma tela do admin exibe stack trace, caminho de servidor ou
  credencial — toda exceção capturada vira uma mensagem genérica (`admin_flash_set('error', ...)`)
  e o detalhe real vai só para `error_log()`.
- **Upload inválido**: `includes/Uploads.php` nunca inclui o caminho físico do servidor nem o
  nome original do arquivo nas mensagens ao usuário (ex.: "Formato não suportado. Envie um
  arquivo PNG, JPEG ou WEBP." — nunca "falha ao mover /var/www/.../tmp/xyz").
- **Login inválido**: mensagem sempre genérica em qualquer cenário (ver seção de rate limit
  acima).

Nenhum sistema de logging novo foi criado — `error_log()` (já usado desde a sprint 01) continua
suficiente nesta fase.

## `.htaccess`

`database/` e `repositories/` continuam bloqueados por HTTP direto (só usados via
`require`/`include`). `admin/` continua deliberadamente fora de qualquer bloqueio. O bloco de
roteamento de posts (sprint 02) não usa `[F]` — é um `RewriteRule` condicional que só redireciona
internamente quando nada físico responde pela URL.

## Deploy em produção

### Quando as credenciais do servidor chegarem

1. No servidor real, criar um banco MariaDB próprio (`ctprice_site` ou nome equivalente) —
   **nunca o banco do sistema de RH**.
2. Criar um usuário MariaDB dedicado a este banco (nunca o usuário/root administrativo do
   servidor).
3. `GRANT ALL PRIVILEGES ON ctprice_site.* TO 'usuario'@'host'` — acesso restrito SOMENTE a este
   banco (mesmo padrão já usado em desenvolvimento local, ver seção "Arquitetura").
4. Copiar `config/database.example.php` para `config/database.local.php` **diretamente no
   servidor** (nunca pelo Git — o arquivo já é gitignored e bloqueado por `.htaccess`) e preencher
   com as credenciais reais.
5. Publicar o código (ver "Deploy via GitHub" abaixo).
6. Rodar `php database/migrate.php` (cria as tabelas — idempotente, seguro mesmo se já houver
   sido rodado antes).
7. Rodar os seeds **apenas na primeira publicação**: `php database/seed_clients_and_partners.php`
   e `php database/seed_editorial_content.php` (idempotentes, mas depois da primeira importação o
   conteúdo passa a ser editado pelo `/admin/`, não reimportado — reexecutar um seed depois disso
   só reafirmaria os valores originais dos arquivos `config/*.php`, sobrescrevendo edições feitas
   no admin para os registros migrados).
8. Criar o primeiro administrador real: `php database/create_admin.php "Nome" email@ctpricems.com.br "senha-forte"`.
9. Confirmar que `assets/uploads/{clients,partners,benefits,testimonials,posts}/` existem e são
   graváveis pelo usuário do processo PHP (`www-data`/equivalente) no servidor.
10. Confirmar `.htaccess`: `mod_rewrite` habilitado, `AllowOverride` cobrindo as diretivas usadas
    (`RewriteEngine`/`RewriteRule`/`RewriteCond`/`Options -Indexes`/`ErrorDocument`) — mesmo
    requisito já documentado em `docs/deploy-production.md` para os redirects/bloqueios
    existentes, agora incluindo também o bloco de roteamento de posts.

**Nenhuma credencial real foi ou deve ser colocada no Git** — `config/database.local.php`
permanece só um exemplo local nesta sprint (a senha ali é do MariaDB de desenvolvimento, nunca de
produção).

### Backup recomendado antes do go-live

Antes de rodar migrations/seeds pela primeira vez no servidor real (ou antes de qualquer deploy
subsequente que altere schema): `mysqldump ctprice_site > backup-antes-do-deploy.sql` (ou
equivalente do painel de hospedagem). Como o banco de produção é novo (criado no passo 1 acima),
o primeiro backup relevante é o do **WordPress atual**, já coberto pelo procedimento de go-live
do site público (`docs/deploy-production.md`) — não duplicado aqui.

### Migrations e seeds em produção

- Migrations são idempotentes (`schema_migrations` registra o que já rodou — `php
  database/migrate.php` pode ser executado quantas vezes for preciso).
- Seeds não duplicam registros (`ON DUPLICATE KEY UPDATE` para Clientes/Parceiros/Benefícios/
  Posts; verificação em PHP por título/nome+empresa para Vagas/Depoimentos — ver comentários dos
  próprios scripts).
- `database/migrate.php`, `database/create_admin.php`, `database/seed_*.php` são **CLI-only**:
  cada um recusa a própria execução se `PHP_SAPI !== 'cli'` (roda como script de linha de comando,
  nunca como página) — e, independentemente disso, todo o diretório `database/` já é bloqueado
  por HTTP direto pelo `.htaccess`. Duas camadas independentes, nenhuma delas removida.

### Deploy via GitHub

O código é publicado via `git archive`/checkout no servidor (ver `docs/deploy-production.md` para
o procedimento completo já existente). Confirmado nesta sprint que isso é seguro para os uploads:

- Uploads reais (`assets/uploads/{clients,partners,benefits,testimonials,posts}/*`) **nunca são
  rastreados pelo Git** (`.gitignore`) — um `git pull`/nova extração de `git archive` nunca os
  toca, porque o Git nunca soube que eles existiam.
- Os DIRETÓRIOS continuam existindo mesmo num checkout limpo, via `.gitkeep` (um arquivo vazio,
  rastreado, em cada subpasta) — sem isso, uma pasta totalmente vazia não sobreviveria a um clone
  novo do zero.
- `assets/uploads/.htaccess` (nega execução de `.php` e afins nesses diretórios) **é** rastreado
  pelo Git — continua chegando em todo deploy novo, junto com o resto do `.htaccess` da raiz.
- Nenhum armazenamento externo foi criado — os uploads continuam em disco local, fora do controle
  de versão, exatamente como pedido.

## Sessão administrativa — smoke test (sprint 03)

Confirmado nesta sprint: login (sucesso), logout (destrói a sessão inteira), regeneração de
`session_id()` após login, usuário desativado perde acesso imediatamente (mesma sessão já aberta,
sem precisar de novo login), rota privada sem sessão redireciona para `/admin/login.php`, CSRF
inválido rejeitado com mensagem genérica.

## Pendências reais restantes

- Integração de Vagas/Benefícios com o outro sistema (fonte canônica futura) — ainda não
  implementada; sem dados técnicos dessa integração até o momento (ver "Vagas e Benefícios —
  retirados do CMS" acima).
- Rate limit persistente não trata `X-Forwarded-For`/proxy reverso (ver seção própria acima) —
  ajustar `admin_client_ip()` quando a topologia real de produção (proxy/load balancer, se
  houver) for conhecida.
- Sem recuperação automática de senha, sem RBAC, sem MFA (fora de escopo, adiado por decisão
  explícita).
- Depoimentos com upload único para dois campos de imagem — documentado acima como decisão
  consciente, não bug.
- Informações institucionais pendentes (bairro/CEP, URLs da Área Restrita, redes sociais, links
  externos de parceiros, site de Walter Ferreira, decisões de LGPD/anonimato da Ouvidoria)
  continuam aguardando confirmação da CT Price — nada disso bloqueia o CMS em si.
