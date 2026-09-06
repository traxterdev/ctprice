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
 * - Rate limit por sessão: backoff crescente (2s, 4s, 8s, ... até 60s) após cada falha
 *   consecutiva — suficiente para desestimular força bruta manual sem exigir tabela/IP tracking
 *   nesta primeira versão (ver docs/cms.md, pendências).
 */

declare(strict_types=1);

const ADMIN_LOGIN_MAX_BACKOFF_SECONDS = 60;

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

/**
 * Login efetivo: valida credenciais, aplica rate limit, regenera o ID de sessão em caso de
 * sucesso e atualiza `ultimo_login_em`. Mensagem de erro SEMPRE genérica (nunca revela se foi o
 * e-mail ou a senha que estava errada, nem se a conta existe).
 *
 * @return array{success:bool, message:string}
 */
function admin_attempt_login(string $email, string $password): array
{
    $genericError = 'E-mail ou senha inválidos.';

    if (admin_login_is_blocked()) {
        return ['success' => false, 'message' => 'Muitas tentativas. Aguarde alguns segundos e tente novamente.'];
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
        return ['success' => false, 'message' => $genericError];
    }

    admin_record_login_attempt(true);
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
