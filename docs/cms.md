# CMS administrativo — CT Price (Sprint 01)

Fundação administrativa: MariaDB próprio, autenticação, painel `/admin/`, gestão de Clientes e
Parceiros, integração com o site público. PHP puro + PDO — sem ORM, sem framework, sem Supabase.

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
- **Camada de acesso**: `repositories/ClientRepository.php` e `repositories/PartnerRepository.php`
  — únicas classes que escrevem SQL para `clients`/`partners`. Nenhum componente/página monta
  query própria. Sem Service Layer (não há regra de negócio que justifique).
- **Autenticação**: `includes/AdminAuth.php` — sessão (reaproveita
  `ctprice_configure_session_cookie()` do bootstrap), CSRF por token de sessão, rate limit com
  backoff exponencial (2s, 4s, 8s... até 60s) por sessão, `session_regenerate_id()` no login,
  usuário revalidado no banco a cada requisição (`ativo = 1`).
- **Upload**: `includes/Uploads.php` — MIME real via `finfo`, extensão decidida pelo MIME
  (whitelist PNG/JPEG/WEBP), nome físico aleatório, diretório próprio
  (`assets/uploads/clients/`, `assets/uploads/partners/`), `assets/uploads/.htaccess` nega
  execução de script nesses diretórios (defesa em profundidade).

## Configuração local

1. Copie `config/database.example.php` para `config/database.local.php` (nunca commitado — ver
   `.gitignore`; já protegido por HTTP pelo `.htaccess`, que bloqueia todo o diretório `config/`).
2. Preencha host/porta/banco/usuário/senha do seu MariaDB local.
3. Rode as migrations: `php database/migrate.php`
4. Importe os dados estáticos atuais: `php database/seed_clients_and_partners.php`
5. Crie o primeiro administrador: `php database/create_admin.php "Nome" email@dominio.com "senha-forte"`
6. Acesse `/admin/login.php`.

Todos os 3 comandos acima são reexecutáveis com segurança (idempotentes).

## Módulos disponíveis nesta sprint

- **Dashboard** (`/admin/`): total de clientes ativos + total de parceiros ativos.
- **Clientes** (`/admin/clients/`): listar, criar, editar, ativar/desativar, excluir (com
  confirmação), reordenar (setas). Campos: nome, logo, site (opcional).
- **Parceiros** (`/admin/partners/`): mesmo CRUD, com categoria (`tools`/`companies` — mesma
  semântica de "Ferramentas" e "Parceiros" já existente em `/parcerias/`) e URL opcional
  (preserva o caso real "Auditto", sem link).

Não implementado nesta sprint (ver tarefa, §24): páginas institucionais, blog, depoimentos,
vagas/benefícios, múltiplos níveis de usuário, recuperação de senha, mídia genérica, API.

## Fonte canônica dos dados

**O banco de dados (`clients`/`partners`) é a fonte canônica a partir desta sprint.** As páginas
públicas (`/`, `/sobre-nos/`, `/informacoes/`, `/clientes/`, `/parcerias/`) e os componentes
(`clients-carousel-section.php`, `clients-grid-section.php`, `logo-grid-section.php`) não leem
mais `config/clients.php`/`config/partners.php`.

Esses dois arquivos **não foram apagados** — passam a existir só como a fonte histórica da
importação (consumidos unicamente por `database/seed_clients_and_partners.php`). Não há mais duas
fontes oficiais simultâneas.

## Disponibilidade do banco

Toda página pública que lê `clients`/`partners` envolve a chamada em `try/catch`: se o banco
estiver indisponível, o erro é registrado via `error_log()` (nunca exposto ao visitante) e a
seção correspondente é renderizada vazia (os componentes já toleram `$clientLogos`/`$items`
vazio) — falha controlada, sem stack trace, sem página quebrada.

## `.htaccess`

Dois diretórios novos entraram no bloqueio de acesso HTTP direto já existente (mesma regra que já
protegia `config/`, `includes/`, etc.): `database/` (migrations SQL + scripts CLI) e
`repositories/` (classes de acesso ao banco). `admin/` foi deliberadamente mantido FORA de
qualquer bloqueio — precisa ser acessível por HTTP; quem entra é decidido pela sessão
(`includes/AdminAuth.php`), não pelo servidor.

## Preparação para produção (fora do escopo desta sprint)

- Criar, no servidor real, um banco `ctprice_site` (ou nome equivalente) próprio, com usuário e
  senha próprios — nunca o banco do sistema de RH.
- Preencher `config/database.local.php` no servidor com essas credenciais (o arquivo nunca é
  versionado; precisa ser criado manualmente em cada ambiente).
- Rodar `php database/migrate.php`, depois `php database/seed_clients_and_partners.php` (só na
  primeira publicação — depois disso o banco passa a ser editado pelo `/admin/`, não reimportado).
- Criar o primeiro administrador real com `php database/create_admin.php`.
- `assets/uploads/` precisa existir e ser gravável pelo PHP no servidor (fora do controle do
  Git — ver `.gitignore`).

## Pendências reais para a próxima sprint

- Rate limit de login é só por sessão (sem tabela/IP) — suficiente para esta primeira versão, mas
  vale reforçar (ex.: por IP) antes de expor a um público mais amplo.
- Sem recuperação de senha — reset hoje depende de rodar `database/create_admin.php` de novo
  (documentado acima como comportamento esperado, não bug).
- Sem RBAC — um único perfil "Administrador".
- `admin_users` não tem UI própria nesta sprint (só o script CLI) — gestão de administradores
  fica para uma sprint futura, se necessário.
