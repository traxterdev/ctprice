<?php
/**
 * home-contato-action.php
 *
 * Endpoint de processamento do formulário "Quer receber um contato?" da Home
 * (components/contact-section.php). PHP puro — sem WordPress, sem Elementor, sem
 * `admin-ajax.php`, sem framework, sem biblioteca externa.
 *
 * Vive na RAIZ do projeto, ao lado de `index.php` (a página que o usa) — mesmo padrão de
 * `fale-conosco/fale-conosco-action.php` viver ao lado de `fale-conosco/index.php`.
 *
 * ENDPOINT PRÓPRIO, NÃO REUTILIZA fale-conosco-action.php: os campos são diferentes (Home tem
 * Nome/E-mail/Telefone/Mensagem; Fale Conosco tem Nome/E-mail/Empresa/Mensagem — nenhum dos dois
 * é subconjunto do outro) — ver docs/reference/home-contact-form-validation.md, seção 2. Forçar
 * um único endpoint a aceitar os dois contratos exigiria condicionais artificiais e colocaria em
 * risco o fluxo de Fale Conosco já validado. Duplicação pequena e estável, não abstraída em um
 * serviço genérico de formulários (decisão consciente desta sprint).
 *
 * Segurança implementada (mesma filosofia já aprovada em Fale Conosco/Ouvidoria, com chaves de
 * sessão PRÓPRIAS — nunca compartilhadas, para que um envio na Home nunca consuma o rate limit
 * de outro formulário nem vice-versa):
 * - CSRF: token opaco por sessão (`home_contact_csrf`, gerado em index.php), comparado com
 *   `hash_equals()`.
 * - Honeypot: campo oculto (`website`) — se preenchido, resposta é uma rejeição SILENCIOSA (session
 *   nunca vê o rate limit consumido, nenhum e-mail é enviado).
 * - Rate limit simples por sessão (`home_contact_last_submit`, 30s) — só atualizado após um envio
 *   de fato aceito e processado (erros de validação/CSRF/honeypot nunca o consomem).
 * - Validação server-side completa (nunca confia em HTML5 `required`/`type=email`/`type=tel`).
 * - Header injection: valores usados em cabeçalhos de e-mail passam por `ctprice_clean_line()`
 *   (remove CR/LF); o e-mail do visitante é validado com `filter_var(FILTER_VALIDATE_EMAIL)`
 *   antes de virar `Reply-To`. O e-mail do visitante NUNCA é usado como `From` — o remetente é
 *   sempre o e-mail institucional de `config/company.php`.
 *
 * Não expõe stack trace, caminho de servidor nem detalhes de exceção em nenhuma resposta — apenas
 * mensagens genéricas seguras (erros técnicos só vão para `error_log()`).
 */

declare(strict_types=1);

require __DIR__ . '/config/bootstrap.php';

// Mesma configuração de cookie de sessão usada em index.php — precisa ser idêntica nos dois
// arquivos para operar sobre a mesma sessão (ver config/bootstrap.php).
ctprice_configure_session_cookie();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Responde e encerra a execução. Requisições assíncronas (identificadas por
 * `X-Requested-With: XMLHttpRequest` ou `Accept: application/json`, enviadas por
 * assets/js/home-contact-form.js) recebem JSON. Uma submissão HTML "crua" (JavaScript desabilitado
 * ou falho) recebe um redirecionamento 303 de volta para a Home, com `?status=` para que
 * components/contact-section.php exiba um banner estático equivalente.
 */
function ctprice_home_contact_respond(bool $isAjax, int $httpStatus, array $payload, string $redirectStatus): void
{
    if ($isAjax) {
        http_response_code($httpStatus);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($payload);
        exit;
    }

    header('Location: ' . BASE_URL . '/?status=' . urlencode($redirectStatus), true, 303);
    exit;
}

/**
 * Remove quebras de linha (CR/LF) e colapsa espaços — usado em qualquer valor que possa acabar em
 * um cabeçalho de e-mail (From/Reply-To), prevenindo header injection.
 */
function ctprice_clean_line(string $value): string
{
    $value = str_replace(["\r", "\n"], ' ', $value);
    return trim((string) preg_replace('/\s+/', ' ', $value));
}

$isAjax = (
    (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    || (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'))
);

// 1) Método — só POST é aceito.
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    ctprice_home_contact_respond($isAjax, 405, [
        'success' => false,
        'message' => 'Método não permitido.',
    ], 'error');
}

// 2) CSRF — token de sessão gerado em index.php. Chave própria (home_contact_csrf), nunca
// compartilhada com fale_conosco_csrf/ouvidoria_csrf.
$sessionToken = (string) ($_SESSION['home_contact_csrf'] ?? '');
$submittedToken = (string) ($_POST['csrf_token'] ?? '');
if ($sessionToken === '' || $submittedToken === '' || !hash_equals($sessionToken, $submittedToken)) {
    ctprice_home_contact_respond($isAjax, 403, [
        'success' => false,
        'message' => 'Sua sessão expirou. Atualize a página e tente novamente.',
    ], 'error');
}

// 3) Honeypot — bots preenchem todo campo; um humano nunca vê nem preenche este.
$honeypot = trim((string) ($_POST['website'] ?? ''));
if ($honeypot !== '') {
    ctprice_home_contact_respond($isAjax, 200, [
        'success' => true,
        'message' => 'Mensagem enviada com sucesso! Em breve entraremos em contato.',
    ], 'success');
}

// 4) Rate limit simples por sessão — chave própria (home_contact_last_submit), nunca compartilhada.
$now = time();
$minIntervalSeconds = 30;
$lastSubmit = (int) ($_SESSION['home_contact_last_submit'] ?? 0);
if ($lastSubmit > 0 && ($now - $lastSubmit) < $minIntervalSeconds) {
    ctprice_home_contact_respond($isAjax, 429, [
        'success' => false,
        'message' => 'Aguarde alguns segundos antes de enviar novamente.',
    ], 'rate_limited');
}

// 5) Validação server-side — nunca confia apenas em HTML/JavaScript. Os 4 campos abaixo são
// exatamente os já existentes visualmente na Home (Nome/E-mail/Telefone/Mensagem), todos já
// marcados como obrigatórios na marcação original — a validação server-side reflete o mesmo.
$name = ctprice_clean_line((string) ($_POST['name'] ?? ''));
$email = ctprice_clean_line((string) ($_POST['email'] ?? ''));
$phoneRaw = trim((string) ($_POST['phone'] ?? ''));
$message = trim((string) ($_POST['message'] ?? '')); // corpo do e-mail, não cabeçalho — pode ter quebras de linha

$errors = [];

if ($name === '') {
    $errors['name'] = 'Informe seu nome.';
} elseif (mb_strlen($name) > 150) {
    $errors['name'] = 'Nome muito longo (máximo 150 caracteres).';
}

if ($email === '') {
    $errors['email'] = 'Informe um e-mail para que possamos responder.';
} elseif (mb_strlen($email) > 190 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Informe um e-mail válido.';
}

// Mesmo critério já usado em ouvidoria/ouvidoria-action.php para telefone (8 a 13 dígitos, com
// DDD) — reaproveitado aqui por ser o padrão já estabelecido no projeto, não uma regra nova.
$phoneDigits = preg_replace('/\D/', '', $phoneRaw) ?? '';
if ($phoneRaw === '') {
    $errors['phone'] = 'Informe um telefone de contato.';
} elseif (mb_strlen($phoneRaw) > 30 || strlen($phoneDigits) < 8 || strlen($phoneDigits) > 13) {
    $errors['phone'] = 'Informe um telefone válido (com DDD).';
}

if ($message === '') {
    $errors['message'] = 'Escreva sua mensagem.';
} elseif (mb_strlen($message) > 5000) {
    $errors['message'] = 'Mensagem muito longa (máximo 5000 caracteres).';
}

if ($errors) {
    ctprice_home_contact_respond($isAjax, 422, [
        'success' => false,
        'message' => 'Verifique os campos destacados.',
        'errors' => $errors,
    ], 'invalid');
}

// 6) Destinatário — sempre a partir de config/company.php, nunca hardcoded aqui.
$to = $company['emails']['contato'] ?? '';
if ($to === '') {
    error_log('[home-contato] Envio abortado: config/company.php não define emails.contato.');
    ctprice_home_contact_respond($isAjax, 500, [
        'success' => false,
        'message' => 'Não foi possível enviar sua mensagem no momento. Tente novamente mais tarde.',
    ], 'error');
}

// 7) Monta e envia o e-mail. From fixo institucional — NUNCA o e-mail digitado pelo visitante
// (evita spoofing e problemas de SPF/DKIM na entrega). O e-mail do visitante, já validado acima,
// só é usado como Reply-To.
$fromName = ctprice_clean_line((string) ($company['razao_social'] ?? 'CT Price'));
$fromEmail = $to;

$subject = 'Novo contato pelo site - Home';
$encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';

$bodyLines = [
    'Novo contato recebido pelo formulário da Home ("Quer receber um contato?"):',
    '',
    'Nome: ' . $name,
    'E-mail: ' . $email,
    'Telefone: ' . $phoneRaw,
    '',
    'Mensagem:',
    $message,
];
$body = implode("\n", $bodyLines);

$headers = implode("\r\n", [
    'MIME-Version: 1.0',
    'Content-Type: text/plain; charset=UTF-8',
    'From: ' . $fromName . ' <' . $fromEmail . '>',
    'Reply-To: ' . $email,
]);

$sent = @mail($to, $encodedSubject, $body, $headers);

if (!$sent) {
    // mail() pode falhar silenciosamente quando o servidor não tem um MTA configurado (comum em
    // ambiente de desenvolvimento local) — não é necessariamente um defeito de código. Detalhes
    // ficam só no log do servidor, nunca na resposta ao usuário.
    error_log('[home-contato] Falha ao enviar e-mail via mail() — verifique a configuração de MTA do servidor.');
    ctprice_home_contact_respond($isAjax, 500, [
        'success' => false,
        'message' => 'Não foi possível enviar sua mensagem no momento. Tente novamente mais tarde ou use o WhatsApp.',
    ], 'error');
}

// Rate limit só é atualizado após um envio de fato aceito e processado.
$_SESSION['home_contact_last_submit'] = $now;

ctprice_home_contact_respond($isAjax, 200, [
    'success' => true,
    'message' => 'Mensagem enviada com sucesso! Em breve entraremos em contato.',
], 'success');
