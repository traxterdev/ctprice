<?php
/**
 * fale-conosco/fale-conosco-action.php
 *
 * Endpoint de processamento do formulário de contato de `/fale-conosco/`
 * (components/contact-form-section.php). PHP puro — sem WordPress, sem Elementor, sem
 * `admin-ajax.php`, sem framework, sem biblioteca externa (nem PHPMailer/Composer).
 *
 * Transporte: `mail()` nativo do PHP (ver §7 da tarefa de implementação). IMPORTANTE — isto NÃO
 * garante entrega: depende inteiramente de o servidor de produção ter um MTA (sendmail/Postfix/
 * relay) configurado. Este código não afirma e não pode confirmar que SMTP foi configurado. A
 * estrutura abaixo (montagem de destinatário/assunto/corpo/headers isolada da chamada de envio)
 * foi organizada para permitir trocar `mail()` por um transporte SMTP mais tarde sem refazer o
 * formulário nem o endpoint — só a função de envio precisaria mudar.
 *
 * Segurança implementada:
 * - CSRF: token opaco gerado por sessão (ver fale-conosco/index.php), comparado com
 *   `hash_equals()` (comparação em tempo constante).
 * - Honeypot: campo oculto (`website`) — se preenchido, resposta é uma rejeição SILENCIOSA
 *   (parece sucesso para não ensinar o bot a se adaptar), mas nenhum e-mail é enviado.
 * - Rate limit simples por sessão: um envio bem-sucedido por 30 segundos, sem exigir banco de
 *   dados.
 * - Validação server-side completa (nunca confia em HTML5 `required`/`type=email` do cliente).
 * - Header injection: todos os valores usados em cabeçalhos de e-mail (From/Reply-To) passam por
 *   `ctprice_clean_line()` (remove CR/LF) e o e-mail do visitante é adicionalmente validado com
 *   `filter_var(FILTER_VALIDATE_EMAIL)` antes de virar `Reply-To`. O e-mail do visitante NUNCA é
 *   usado como `From` — o remetente é sempre o e-mail institucional de `config/company.php`.
 *
 * Não expõe stack trace, caminho de servidor nem detalhes de exceção em nenhuma resposta —
 * apenas mensagens genéricas seguras (erros técnicos são só registrados via `error_log()`).
 */

declare(strict_types=1);

require __DIR__ . '/../config/bootstrap.php';

// Mesma configuração de cookie de sessão usada em fale-conosco/index.php (ver
// config/bootstrap.php) — precisa ser idêntica nos dois arquivos para operar sobre a mesma sessão.
ctprice_configure_session_cookie();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Responde e encerra a execução. Requisições assíncronas (identificadas por
 * `X-Requested-With: XMLHttpRequest` ou `Accept: application/json`, enviadas pelo
 * assets/js/contact-form.js) recebem JSON. Uma submissão HTML "crua" (JavaScript desabilitado ou
 * falho) recebe um redirecionamento 303 de volta para a página, com `?status=` para que
 * components/contact-form-section.php exiba um banner estático equivalente — ver
 * docs/reference/fale-conosco-audit.md, seção 8 ("continuar funcional... caso JavaScript falhe").
 */
function ctprice_fale_conosco_respond(bool $isAjax, int $httpStatus, array $payload, string $redirectStatus): void
{
    if ($isAjax) {
        http_response_code($httpStatus);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($payload);
        exit;
    }

    header('Location: ' . BASE_URL . '/fale-conosco/?status=' . urlencode($redirectStatus), true, 303);
    exit;
}

/**
 * Remove quebras de linha (CR/LF) e colapsa espaços — usado em qualquer valor que possa acabar
 * em um cabeçalho de e-mail (From/Reply-To), prevenindo header injection.
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
    ctprice_fale_conosco_respond($isAjax, 405, [
        'success' => false,
        'message' => 'Método não permitido.',
    ], 'error');
}

// 2) CSRF — token de sessão gerado em fale-conosco/index.php.
$sessionToken = (string) ($_SESSION['fale_conosco_csrf'] ?? '');
$submittedToken = (string) ($_POST['csrf_token'] ?? '');
if ($sessionToken === '' || $submittedToken === '' || !hash_equals($sessionToken, $submittedToken)) {
    ctprice_fale_conosco_respond($isAjax, 403, [
        'success' => false,
        'message' => 'Sua sessão expirou. Atualize a página e tente novamente.',
    ], 'error');
}

// 3) Honeypot — bots preenchem todo campo; um humano nunca vê nem preenche este.
$honeypot = trim((string) ($_POST['website'] ?? ''));
if ($honeypot !== '') {
    ctprice_fale_conosco_respond($isAjax, 200, [
        'success' => true,
        'message' => 'Mensagem enviada com sucesso! Em breve entraremos em contato.',
    ], 'success');
}

// 4) Rate limit simples por sessão — evita reenvio imediato repetido sem exigir banco de dados.
$now = time();
$minIntervalSeconds = 30;
$lastSubmit = (int) ($_SESSION['fale_conosco_last_submit'] ?? 0);
if ($lastSubmit > 0 && ($now - $lastSubmit) < $minIntervalSeconds) {
    ctprice_fale_conosco_respond($isAjax, 429, [
        'success' => false,
        'message' => 'Aguarde alguns segundos antes de enviar novamente.',
    ], 'rate_limited');
}

// 5) Validação server-side — nunca confia apenas em HTML/JavaScript.
$name = ctprice_clean_line((string) ($_POST['name'] ?? ''));
$email = ctprice_clean_line((string) ($_POST['email'] ?? ''));
$visitorCompany = ctprice_clean_line((string) ($_POST['company'] ?? ''));
$message = trim((string) ($_POST['message'] ?? '')); // corpo do e-mail, não cabeçalho — pode ter quebras de linha

$errors = [];

if (mb_strlen($name) > 150) {
    $errors['name'] = 'Nome muito longo (máximo 150 caracteres).';
}

if ($email === '') {
    $errors['email'] = 'Informe um e-mail para que possamos responder.';
} elseif (mb_strlen($email) > 190 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Informe um e-mail válido.';
}

if (mb_strlen($visitorCompany) > 150) {
    $errors['company'] = 'Nome da empresa muito longo (máximo 150 caracteres).';
}

if (mb_strlen($message) > 5000) {
    $errors['message'] = 'Mensagem muito longa (máximo 5000 caracteres).';
}

if ($errors) {
    ctprice_fale_conosco_respond($isAjax, 422, [
        'success' => false,
        'message' => 'Verifique os campos destacados.',
        'errors' => $errors,
    ], 'invalid');
}

// 6) Destinatário — sempre a partir de config/company.php, nunca hardcoded aqui.
$to = $company['emails']['contato'] ?? '';
if ($to === '') {
    error_log('[fale-conosco] Envio abortado: config/company.php não define emails.contato.');
    ctprice_fale_conosco_respond($isAjax, 500, [
        'success' => false,
        'message' => 'Não foi possível enviar sua mensagem no momento. Tente novamente mais tarde.',
    ], 'error');
}

// 7) Monta e envia o e-mail. From fixo institucional — NUNCA o e-mail digitado pelo visitante
// (evita spoofing e problemas de SPF/DKIM na entrega). O e-mail do visitante, já validado acima,
// só é usado como Reply-To.
$fromName = ctprice_clean_line((string) ($company['razao_social'] ?? 'CT Price'));
$fromEmail = $to;

$subject = 'Novo contato pelo site - Fale Conosco';
$encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';

$bodyLines = [
    'Novo contato recebido pelo formulário de /fale-conosco/:',
    '',
    'Nome: ' . ($name !== '' ? $name : '(não informado)'),
    'E-mail: ' . $email,
    'Empresa: ' . ($visitorCompany !== '' ? $visitorCompany : '(não informado)'),
    '',
    'Mensagem:',
    $message !== '' ? $message : '(não informado)',
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
    error_log('[fale-conosco] Falha ao enviar e-mail via mail() — verifique a configuração de MTA do servidor.');
    ctprice_fale_conosco_respond($isAjax, 500, [
        'success' => false,
        'message' => 'Não foi possível enviar sua mensagem no momento. Tente novamente mais tarde ou use o WhatsApp.',
    ], 'error');
}

// Rate limit só é atualizado após um envio de fato aceito e processado.
$_SESSION['fale_conosco_last_submit'] = $now;

ctprice_fale_conosco_respond($isAjax, 200, [
    'success' => true,
    'message' => 'Mensagem enviada com sucesso! Em breve entraremos em contato.',
], 'success');
