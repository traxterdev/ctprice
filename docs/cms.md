# CMS administrativo — CT Price (Sprints 01 e 02)

Fundação administrativa (Sprint 01): MariaDB próprio, autenticação, painel `/admin/`, gestão de
Clientes e Parceiros. Conteúdo editorial (Sprint 02): Vagas, Benefícios, Depoimentos e Blog. PHP
puro + PDO — sem ORM, sem framework, sem Supabase.

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
  `JobRepository`, `BenefitRepository`, `TestimonialRepository`, `BlogPostRepository`) — únicas
  classes que escrevem SQL para suas tabelas. Nenhum componente/página monta query própria. Sem
  Service Layer (não há regra de negócio que justifique).
- **Autenticação**: `includes/AdminAuth.php` — sessão (reaproveita
  `ctprice_configure_session_cookie()` do bootstrap), CSRF por token de sessão, rate limit com
  backoff exponencial (2s, 4s, 8s... até 60s) por sessão, `session_regenerate_id()` no login,
  usuário revalidado no banco a cada requisição (`ativo = 1`).
- **Upload**: `includes/Uploads.php` — MIME real via `finfo`, extensão decidida pelo MIME
  (whitelist PNG/JPEG/WEBP — SVG avaliado e descartado, nenhum uso real encontrado em 133+30
  imagens), nome físico aleatório, diretório próprio por entidade (`assets/uploads/{clients,
  partners,benefits,testimonials,posts}/`), `assets/uploads/.htaccess` nega execução de script.
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

Todos os comandos acima são reexecutáveis com segurança (idempotentes).

## Módulos disponíveis

- **Dashboard** (`/admin/`): totais ativos de Clientes, Parceiros, Vagas, Benefícios, Depoimentos
  e posts publicados. Nenhum analytics.
- **Clientes** (`/admin/clients/`): CRUD + ativar/desativar + reordenar (setas). Campos: nome,
  logo, site (opcional).
- **Parceiros** (`/admin/partners/`): idem, com categoria (`tools`/`companies`, mesma semântica de
  "Ferramentas"/"Parceiros" de `/parcerias/`) e URL opcional (preserva o caso real "Auditto", sem
  link).
- **Vagas** (`/admin/jobs/`): CRUD + ativar/desativar + reordenar. Pré-requisitos/diferenciais são
  textareas "um item por linha" (convertidos para `<ul><li>` na leitura pública — mesma
  apresentação de sempre). O link de candidatura continua vindo de
  `config/company.php['sistemas_externos']['recrutamento']`, nunca duplicado aqui.
- **Benefícios** (`/admin/benefits/`): CRUD + upload + ativar/desativar + reordenar.
- **Depoimentos** (`/admin/testimonials/`): CRUD + ativar/desativar + reordenar. Um único upload
  ("foto") alimenta `foto_path` e `thumbnail_path` (mesma convenção real dos 7 depoimentos atuais
  — os dois campos existem no schema para o dia em que uma miniatura de vídeo diferente da foto
  da pessoa for realmente necessária). `video_id`/`video_list`/URLs validados server-side.
- **Notícias/Blog** (`/admin/posts/`): criar, editar, publicar/despublicar (reaproveita a coluna
  `ativo`), definir data de publicação. **Slug só é definido na criação e nunca muda depois**
  (campo bloqueado na edição, e o servidor ignora um slug forjado no POST de edição). Sem setas de
  reordenação: a ordem pública é sempre cronológica (`published_at DESC`).

Não implementado (ver tarefa da sprint, §15/§24): RBAC, recuperação de senha, MFA, gerenciamento
de administradores, editor WYSIWYG, mídia genérica, páginas institucionais editáveis, SEO
avançado, API, analytics.

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

## Disponibilidade do banco

Toda página pública que lê do banco envolve a chamada em `try/catch`: se o banco estiver
indisponível, o erro é registrado via `error_log()` (nunca exposto ao visitante) e a seção
correspondente é renderizada vazia/o post cai em 404 controlado — nunca stack trace, nunca página
quebrada.

## `.htaccess`

`database/` e `repositories/` continuam bloqueados por HTTP direto (só usados via
`require`/`include`). `admin/` continua deliberadamente fora de qualquer bloqueio. Novo nesta
sprint: o bloco de roteamento de posts (ver seção acima) — não usa `[F]`, é um `RewriteRule`
condicional que só redireciona internamente quando nada físico responde pela URL.

## Preparação para produção (fora do escopo desta sprint)

- Banco/usuário/senha próprios no servidor real (nunca o do RH); preencher
  `config/database.local.php` manualmente lá (nunca versionado).
- Rodar `php database/migrate.php`, depois os dois seeds — só na primeira publicação.
- Criar o primeiro administrador real com `php database/create_admin.php`.
- `assets/uploads/` precisa existir e ser gravável pelo PHP no servidor.
- Confirmar que `mod_rewrite`/`AllowOverride` no servidor real cobrem também a nova regra de
  roteamento de posts (mesmo requisito já documentado para os redirects/bloqueios existentes).

## Pendências reais para a próxima sprint

- Rate limit de login é só por sessão (sem tabela/IP).
- Sem recuperação de senha, sem RBAC, sem UI de gestão de administradores (só CLI).
- Grade "Últimas notícias" (Home/Informações) limitada às 3 mais recentes (`LIMIT`, layout
  desenhado para 3 colunas); a coluna de relacionados de cada post continua sem limite — pode
  crescer bastante conforme o blog cresce, sem paginação (não implementada nesta sprint).
- Depoimentos: schema tem `foto_path`/`thumbnail_path` separados, mas o admin usa um único upload
  para os dois — separar os dois uploads no formulário fica para quando houver necessidade real.
