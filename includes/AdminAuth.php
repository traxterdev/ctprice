<?php
/**
 * includes/AdminAuth.php
 *
 * Sessão, autenticação e CSRF do `/admin/` — mesmo espírito dos formulários públicos já
 * aprovados (Fale Conosco/Ouvidoria: token de sessão + `hash_equals()`, rate limit simples sem
 * tabela própria), adaptado para login administrativo. Nenhuma biblioteca externa.
 *
 * Chaves de sessão PRÓPRIAS do admin (nunca reaproveita `fale_conosco_csrf`/`home_contact_csrf`/
 * `ouvidoria_csrf` — cada área do site já segue essa convenção de isolamento):
 *   admin_user_id            — id do administrador autenticado.
 *   admin_csrf               — token CSRF das telas administrativas.
 *   admin_login_fail_count   — tentativas de login malsucedidas consecutivas (rate limit).
 *   admin_login_blocked_until — timestamp até quando novas tentativas são recusadas.
 *
 * Segurança:
 * - `session_regenerate_id(true)` após login bem-sucedido (mitiga session fixation).
 * - Sessão inteira destruída no logout (não só a variável de usuário).
 * - Usuário buscado no banco A CADA requisição (não só no login) — se for desativado
 *   (`ativo = 0`) enquanto a sessão ainda existe, a próxima requisição já bloqueia (ver §8 da
 *   tarefa: "usuário inativo não entra").
 * - Rate limit em DUAS camadas (sprint 03 — a primeira sozinha não resiste a uma aba anônima):
 *   1) por SESSÃO: backoff exponencial (2s, 4s, 8s... até 60s) após cada falha consecutiva —
 *      desestimula clique repetido na mesma aba, resposta imediata, sem tocar no banco.
 *   2) PERSISTENTE (`admin_login_attempts`, banco): conta tentativas malsucedidas por e-mail
 *      normalizado E por IP numa janela de tempo — sobrevive a uma sessão/aba nova. Ver
 *      `admin_login_is_blocked_persistent()`.
 *   Qualquer uma das duas camadas bloqueando já é suficiente para recusar a tentativa — a
 *   mensagem ao usuário é sempre a mesma genérica, nunca revela qual camada bloqueou nem se o
 *   e-mail existe (evita enumeração de contas).
 */

declare(strict_types=1);

const ADMIN_LOGIN_MAX_BACKOFF_SECONDS = 60;

// Camada persistente (banco) — limiares deliberadamente mais folgados que o backoff de sessão
// (que já pega o caso comum): existe para resistir a uma sessão nova, não para ser o primeiro
// filtro. 8 tentativas malsucedidas em 15 minutos (por e-mail OU por IP) bloqueiam novas
// tentativas por essa mesma janela — sem bloqueio "até" fixo separado: a janela desliza sozinha
// conforme tentativas antigas saem dela.
const ADMIN_LOGIN_DB_MAX_ATTEMPTS = 8;
const ADMIN_LOGIN_DB_WINDOW_MINUTES = 15;
// Idade máxima de uma linha antes de ser elegível para limpeza (bem maior que a janela de
// bloqueio — mantém histórico curto o suficiente para não crescer indefinidamente, sem exigir
// cron: a limpeza roda inline a cada tentativa nova, ver admin_login_prune_attempts()).
const ADMIN_LOGIN_DB_RETENTION_HOURS = 24;

function admin_start_session(): void
{
    ctprice_configure_session_cookie();
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function admin_csrf_token(): string
{
    admin_start_session();
    if (empty($_SESSION['admin_csrf'])) {
        $_SESSION['admin_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['admin_csrf'];
}

function admin_verify_csrf(?string $submittedToken): bool
{
    admin_start_session();
    $sessionToken = (string) ($_SESSION['admin_csrf'] ?? '');
    $submittedToken = (string) ($submittedToken ?? '');
    return $sessionToken !== '' && $submittedToken !== '' && hash_equals($sessionToken, $submittedToken);
}

/**
 * Administrador autenticado da requisição atual, ou null. Reconsulta o banco a cada chamada
 * (cacheado em variável estática só dentro da mesma requisição) para refletir imediatamente uma
 * desativação (`ativo = 0`) feita por outro administrador.
 *
 * @return array{id:int, nome:string, email:string}|null
 */
function admin_current_user(): ?array
{
    static $cached = false; // false = ainda não resolvido nesta requisição; null = resolvido, sem usuário
    if ($cached !== false) {
        return $cached;
    }

    admin_start_session();
    $id = $_SESSION['admin_user_id'] ?? null;
    if (!is_int($id)) {
        $cached = null;
        return null;
    }

    try {
        $stmt = Database::connection()->prepare(
            'SELECT id, nome, email FROM admin_users WHERE id = :id AND ativo = 1'
        );
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
    } catch (Throwable $e) {
        error_log('CT Price CMS [AdminAuth]: falha ao verificar sessão — ' . $e->getMessage());
        $user = false;
    }

    if (!$user) {
        // Sessão aponta para um usuário que não existe mais, foi desativado, ou o banco falhou —
        // trata como "não autenticado" (falha controlada, nunca expõe o motivo ao navegador).
        unset($_SESSION['admin_user_id']);
        $cached = null;
        return null;
    }

    $cached = ['id' => (int) $user['id'], 'nome' => $user['nome'], 'email' => $user['email']];
    return $cached;
}

/**
 * Chamar no topo de TODA página administrativa privada. Encerra a execução com um redirect para
 * o login se não houver sessão administrativa válida — nenhum endpoint privado roda sem isso.
 */
function admin_require_login(): array
{
    $user = admin_current_user();
    if ($user === null) {
        header('Location: ' . BASE_URL . '/admin/login.php', true, 302);
        exit;
    }
    return $user;
}

/**
 * true = ainda dentro do período de bloqueio (não processar a tentativa de login agora).
 */
function admin_login_is_blocked(): bool
{
    admin_start_session();
    $blockedUntil = (int) ($_SESSION['admin_login_blocked_until'] ?? 0);
    return $blockedUntil > time();
}

function admin_login_seconds_remaining(): int
{
    admin_start_session();
    $blockedUntil = (int) ($_SESSION['admin_login_blocked_until'] ?? 0);
    return max(0, $blockedUntil - time());
}

/**
 * Registra o resultado de uma tentativa de login e recalcula o backoff. Sucesso zera o contador;
 * falha dobra o tempo de bloqueio (2, 4, 8, 16, 32, 60... segundos, com teto).
 */
function admin_record_login_attempt(bool $success): void
{
    admin_start_session();

    if ($success) {
        unset($_SESSION['admin_login_fail_count'], $_SESSION['admin_login_blocked_until']);
        return;
    }

    $failCount = (int) ($_SESSION['admin_login_fail_count'] ?? 0) + 1;
    $_SESSION['admin_login_fail_count'] = $failCount;
    $backoff = min(ADMIN_LOGIN_MAX_BACKOFF_SECONDS, (int) (2 ** $failCount));
    $_SESSION['admin_login_blocked_until'] = time() + $backoff;
}

function admin_normalize_email(string $email): string
{
    return strtolower(trim($email));
}

function admin_client_ip(): string
{
    // Sem suporte a X-Forwarded-For nesta sprint (ver comentário da migration) — REMOTE_ADDR é o
    // único valor que o próprio Apache/PHP determina, nunca vindo direto de um cabeçalho que o
    // cliente controla.
    return (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
}

/**
 * true = a camada PERSISTENTE (banco) está bloqueando novas tentativas para este e-mail OU este
 * IP — sobrevive a uma sessão/aba nova (ver comentário de topo do arquivo).
 */
function admin_login_is_blocked_persistent(string $emailNormalized, string $ip): bool
{
    try {
        $stmt = Database::connection()->prepare(
            'SELECT COUNT(*) FROM admin_login_attempts
             WHERE sucesso = 0
               AND created_at >= (NOW() - INTERVAL :window MINUTE)
               AND (email_normalizado = :email OR ip = :ip)'
        );
        $stmt->execute([
            'window' => ADMIN_LOGIN_DB_WINDOW_MINUTES,
            'email' => $emailNormalized,
            'ip' => $ip,
        ]);
        return ((int) $stmt->fetchColumn()) >= ADMIN_LOGIN_DB_MAX_ATTEMPTS;
    } catch (Throwable $e) {
        error_log('CT Price CMS [AdminAuth]: falha ao consultar admin_login_attempts — ' . $e->getMessage());
        // Banco indisponível: não bloqueia por causa disto (admin_attempt_login já trata a falha
        // de conexão separadamente, antes mesmo de chegar aqui, ao consultar admin_users) — aqui
        // só evita que uma falha nesta tabela auxiliar impeça login legítimo.
        return false;
    }
}

/** Registra a tentativa na tabela persistente e aproveita para limpar linhas antigas. */
function admin_login_record_db_attempt(string $emailNormalized, string $ip, bool $success): void
{
    try {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'INSERT INTO admin_login_attempts (email_normalizado, ip, sucesso) VALUES (:email, :ip, :sucesso)'
        );
        $stmt->execute(['email' => $emailNormalized, 'ip' => $ip, 'sucesso' => $success ? 1 : 0]);
        admin_login_prune_attempts();
    } catch (Throwable $e) {
        error_log('CT Price CMS [AdminAuth]: falha ao registrar tentativa de login — ' . $e->getMessage());
    }
}

/** Remove linhas mais velhas que ADMIN_LOGIN_DB_RETENTION_HOURS — sem exigir cron. */
function admin_login_prune_attempts(): void
{
    try {
        $stmt = Database::connection()->prepare(
            'DELETE FROM admin_login_attempts WHERE created_at < (NOW() - INTERVAL :hours HOUR)'
        );
        $stmt->execute(['hours' => ADMIN_LOGIN_DB_RETENTION_HOURS]);
    } catch (Throwable $e) {
        error_log('CT Price CMS [AdminAuth]: falha ao limpar admin_login_attempts — ' . $e->getMessage());
    }
}

/**
 * Login efetivo: valida credenciais, aplica rate limit (sessão + persistente), regenera o ID de
 * sessão em caso de sucesso e atualiza `ultimo_login_em`. Mensagem de erro SEMPRE genérica (nunca
 * revela se foi o e-mail ou a senha que estava errada, nem se a conta existe).
 *
 * @return array{success:bool, message:string}
 */
function admin_attempt_login(string $email, string $password): array
{
    $genericError = 'E-mail ou senha inválidos.';
    $genericRateLimit = 'Muitas tentativas. Aguarde alguns minutos e tente novamente.';
    $emailNormalized = admin_normalize_email($email);
    $ip = admin_client_ip();

    if (admin_login_is_blocked()) {
        return ['success' => false, 'message' => $genericRateLimit];
    }

    if (admin_login_is_blocked_persistent($emailNormalized, $ip)) {
        // Não incrementa o backoff de sessão aqui — a camada persistente já está segurando
        // sozinha; registrar mais uma tentativa "falha" na sessão não mudaria o resultado.
        return ['success' => false, 'message' => $genericRateLimit];
    }

    try {
        $stmt = Database::connection()->prepare(
            'SELECT id, password_hash, ativo FROM admin_users WHERE email = :email'
        );
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
    } catch (Throwable $e) {
        error_log('CT Price CMS [AdminAuth]: falha ao consultar admin_users — ' . $e->getMessage());
        return ['success' => false, 'message' => 'Não foi possível processar o login no momento. Tente novamente em instantes.'];
    }

    // Mesma resposta genérica tanto para "e-mail não existe" quanto para "senha errada" quanto
    // para "usuário inativo" — nunca revela qual dos três é o caso real.
    if (!$user || (int) $user['ativo'] !== 1 || !password_verify($password, $user['password_hash'])) {
        admin_record_login_attempt(false);
        admin_login_record_db_attempt($emailNormalized, $ip, false);
        return ['success' => false, 'message' => $genericError];
    }

    admin_record_login_attempt(true);
    admin_login_record_db_attempt($emailNormalized, $ip, true);
    admin_start_session();

    // Mitiga session fixation — um novo ID de sessão é emitido só depois de autenticar de fato.
    session_regenerate_id(true);
    $_SESSION['admin_user_id'] = (int) $user['id'];

    try {
        $update = Database::connection()->prepare('UPDATE admin_users SET ultimo_login_em = NOW() WHERE id = :id');
        $update->execute(['id' => $user['id']]);
    } catch (Throwable $e) {
        // Falha ao registrar o último login não deve impedir o acesso — só é logada.
        error_log('CT Price CMS [AdminAuth]: falha ao atualizar ultimo_login_em — ' . $e->getMessage());
    }

    return ['success' => true, 'message' => 'Login realizado com sucesso.'];
}

/**
 * Mensagem "flash" de uma tela para a próxima (ex.: "Cliente criado com sucesso.") — evita
 * reenviar formulário ao atualizar a página (padrão POST/Redirect/GET). Lida uma única vez.
 */
function admin_flash_set(string $type, string $message): void
{
    admin_start_session();
    $_SESSION['admin_flash'] = ['type' => $type, 'message' => $message];
}

/** @return array{type:string, message:string}|null */
function admin_flash_get(): ?array
{
    admin_start_session();
    $flash = $_SESSION['admin_flash'] ?? null;
    unset($_SESSION['admin_flash']);
    return $flash;
}

/**
 * Força mínima aceitável de senha (criação/reset de administrador — nunca usado no login em si,
 * que só verifica o hash) — comprimento mínimo + mistura básica de letra e número. Não é uma
 * régua de complexidade elaborada (sem exigência de símbolo/maiúscula) — suficiente para esta
 * fase sem irritar o administrador com regras excessivas; mesmo limite de comprimento já usado
 * por database/create_admin.php.
 */
function admin_is_strong_password(string $password): bool
{
    return mb_strlen($password) >= 10
        && preg_match('/[a-zA-Z]/', $password) === 1
        && preg_match('/[0-9]/', $password) === 1;
}

const ADMIN_PASSWORD_HINT = 'Mínimo de 10 caracteres, com pelo menos uma letra e um número.';

function admin_logout(): void
{
    admin_start_session();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}
