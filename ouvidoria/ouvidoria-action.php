<?php
/**
 * ouvidoria/ouvidoria-action.php
 *
 * Endpoint de processamento do formulário de manifestação de `/ouvidoria/`
 * (components/ombudsman-form-section.php). PHP puro — sem WordPress, sem Elementor, sem
 * `admin-ajax.php`, sem framework, sem biblioteca externa. Endpoint PRÓPRIO, não compartilhado
 * com `fale-conosco/fale-conosco-action.php` — campos, regras de validação, upload e chaves de
 * sessão (CSRF/rate limit) são todos independentes (ver docs/reference/ouvidoria-audit.md, seção
 * 16, e o comentário de components/ombudsman-form-section.php).
 *
 * Transporte: `mail()` nativo do PHP, com corpo MIME multipart/mixed montado manualmente quando
 * há anexos válidos (função `ctprice_ouvidoria_build_email()` abaixo, isolada deste arquivo —
 * NÃO uma refatoração de fale-conosco-action.php, por instrução explícita da tarefa). Mesma
 * ressalva já registrada em Fale Conosco: isto não garante entrega — depende de o servidor de
 * produção ter um MTA configurado.
 *
 * Segurança implementada:
 * - CSRF: token opaco por sessão (chave `ouvidoria_csrf`, própria desta página — nunca a mesma
 *   chave `fale_conosco_csrf`), comparado com `hash_equals()`.
 * - Honeypot: campo oculto (`website`) — se preenchido, resposta é uma rejeição SILENCIOSA e o
 *   upload NEM É LIDO (os arquivos temporários do PHP são descartados automaticamente pelo
 *   próprio PHP ao fim da requisição, sem processamento adicional nosso).
 * - Rate limit por sessão (chave `ouvidoria_last_submit`, própria desta página — 30s), só
 *   atualizado após um envio de fato aceito e processado (nunca em erro de validação nem
 *   honeypot).
 * - Validação server-side completa de todos os campos de texto.
 * - Upload: MIME real verificado via `finfo` (nunca a extensão/`type` informados pelo navegador),
 *   máximo 3 arquivos, 5MB por arquivo, 10MB no total, apenas PDF/JPEG/PNG. Cada arquivo é lido
 *   diretamente do upload temporário do próprio PHP (`is_uploaded_file()` + `file_get_contents()`)
 *   e IMEDIATAMENTE removido (`unlink()`) após a leitura — nenhuma cópia adicional é criada em
 *   nenhum diretório do projeto, público ou não; nada é persistido em disco além do que o PHP já
 *   gerencia sozinho durante o upload.
 * - Nome do anexo no e-mail é SEMPRE gerado internamente (`anexo-1.pdf`, `anexo-2.jpg`, ...) a
 *   partir da extensão correspondente ao MIME real detectado — nunca o nome de arquivo enviado
 *   pelo usuário. O nome original só aparece, sanitizado (sem CR/LF), como metadado no corpo do
 *   e-mail.
 * - Header injection: valores usados em cabeçalhos (From/Reply-To) passam por
 *   `ctprice_clean_line()`; o e-mail do denunciante é validado com
 *   `filter_var(FILTER_VALIDATE_EMAIL)` antes de virar `Reply-To`. `From` é sempre o e-mail
 *   institucional de `config/company.php` — nunca o e-mail informado pelo usuário.
 *
 * PENDÊNCIA DE DESTINATÁRIO: não existe, até o momento, um e-mail exclusivo confirmado para a
 * Ouvidoria em `config/company.php` (ver docs/reference/ouvidoria-audit.md, seção 9/16) — usa-se
 * o e-mail institucional geral (`emails.contato`) enquanto um endereço dedicado não for definido
 * e confirmado pelo cliente. Não inventado aqui.
 *
 * IDENTIFICAÇÃO OBRIGATÓRIA: Nome, Contato, E-mail e Empresa continuam obrigatórios nesta etapa —
 * não existe opção de manifestação anônima (decisão de negócio pendente do cliente, não alterada
 * por iniciativa própria — ver docs/reference/ouvidoria-audit.md, seção 5/17).
 *
 * DETECÇÃO DE `post_max_size` EXCEDIDO: se o corpo da requisição (ex.: anexos grandes) ultrapassa
 * `post_max_size` do PHP, `$_POST`/`$_FILES` chegam vazios ao nosso código — sem essa detecção,
 * isso cairia no passo de CSRF e responderia com uma mensagem enganosa ("sessão expirou"). Uma
 * checagem pequena comparando `Content-Length` a `post_max_size` (ver
 * `ctprice_ouvidoria_ini_bytes()`) intercepta esse caso antes do CSRF e responde com uma mensagem
 * honesta (413), sem gerenciar upload progress nem qualquer infraestrutura adicional.
 *
 * Não expõe stack trace, caminho de servidor nem detalhes de exceção em nenhuma resposta.
 */

declare(strict_types=1);

require __DIR__ . '/../config/bootstrap.php';

// Mesma configuração de cookie de sessão central usada em todas as páginas com formulário — ver
// config/bootstrap.php. Precisa ser chamada aqui e em ouvidoria/index.php da mesma forma.
ctprice_configure_session_cookie();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

const CTPRICE_OUVIDORIA_MAX_FILES = 3;
const CTPRICE_OUVIDORIA_MAX_FILE_SIZE = 5 * 1024 * 1024; // 5 MB
const CTPRICE_OUVIDORIA_MAX_TOTAL_SIZE = 10 * 1024 * 1024; // 10 MB
const CTPRICE_OUVIDORIA_ALLOWED_MIMES = [
    'application/pdf' => 'pdf',
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
];
const CTPRICE_OUVIDORIA_RATE_LIMIT_SECONDS = 30;

/**
 * Responde e encerra a execução — mesmo padrão de fale-conosco-action.php: JSON para requisições
 * assíncronas (`X-Requested-With`/`Accept: application/json`, enviadas por
 * assets/js/ouvidoria-form.js), 303 (Post/Redirect/Get) de volta para a página com `?status=`
 * para a submissão HTML "crua" (JavaScript desabilitado/falho) — ver
 * components/ombudsman-form-section.php.
 */
function ctprice_ouvidoria_respond(bool $isAjax, int $httpStatus, array $payload, string $redirectStatus): void
{
    if ($isAjax) {
        http_response_code($httpStatus);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($payload);
        exit;
    }

    header('Location: ' . BASE_URL . '/ouvidoria/?status=' . urlencode($redirectStatus), true, 303);
    exit;
}

/** Remove quebras de linha e colapsa espaços — para qualquer valor usado em cabeçalho de e-mail. */
function ctprice_clean_line(string $value): string
{
    $value = str_replace(["\r", "\n"], ' ', $value);
    return trim((string) preg_replace('/\s+/', ' ', $value));
}

/** Converte um valor de `php.ini` com sufixo de unidade ("2G", "8M", "512K") para bytes. */
function ctprice_ouvidoria_ini_bytes(string $value): int
{
    $value = trim($value);
    if ($value === '') {
        return 0;
    }
    $unit = strtolower(substr($value, -1));
    $number = (int) $value;
    return match ($unit) {
        'g' => $number * 1024 * 1024 * 1024,
        'm' => $number * 1024 * 1024,
        'k' => $number * 1024,
        default => (int) $value,
    };
}

/**
 * Monta o e-mail (headers + corpo) da manifestação. Se `$attachments` estiver vazio, produz um
 * e-mail simples `text/plain` (mesmo formato de fale-conosco-action.php). Se houver anexos,
 * produz um `multipart/mixed` manual (boundary próprio) com a mensagem em texto como primeira
 * parte e cada anexo em base64 nas partes seguintes. Função isolada deste endpoint — não
 * compartilhada com fale-conosco-action.php, por não haver necessidade de anexos lá hoje.
 *
 * @param array<int, array{filename: string, mime: string, content: string}> $attachments
 * @return array{headers: string, body: string}
 */
function ctprice_ouvidoria_build_email(string $fromName, string $fromEmail, string $replyTo, string $bodyText, array $attachments): array
{
    $baseHeaders = [
        'From: ' . $fromName . ' <' . $fromEmail . '>',
        'Reply-To: ' . $replyTo,
    ];

    if (!$attachments) {
        $headers = implode("\r\n", array_merge(
            ['MIME-Version: 1.0', 'Content-Type: text/plain; charset=UTF-8'],
            $baseHeaders
        ));
        return ['headers' => $headers, 'body' => $bodyText];
    }

    $boundary = 'ctp-ouvidoria-' . bin2hex(random_bytes(12));

    $headers = implode("\r\n", array_merge(
        ['MIME-Version: 1.0', 'Content-Type: multipart/mixed; boundary="' . $boundary . '"'],
        $baseHeaders
    ));

    $parts = [];
    $parts[] = "--{$boundary}\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: 8bit\r\n\r\n{$bodyText}\r\n";

    foreach ($attachments as $attachment) {
        $encoded = chunk_split(base64_encode($attachment['content']));
        $parts[] = "--{$boundary}\r\n"
            . 'Content-Type: ' . $attachment['mime'] . '; name="' . $attachment['filename'] . "\"\r\n"
            . "Content-Transfer-Encoding: base64\r\n"
            . 'Content-Disposition: attachment; filename="' . $attachment['filename'] . "\"\r\n\r\n"
            . $encoded;
    }

    $body = implode("\r\n", $parts) . "--{$boundary}--";

    return ['headers' => $headers, 'body' => $body];
}

$isAjax = (
    (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    || (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'))
);

// 1) Método — só POST é aceito.
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    ctprice_ouvidoria_respond($isAjax, 405, [
        'success' => false,
        'message' => 'Método não permitido.',
    ], 'error');
}

// 1.5) Corpo da requisição maior que `post_max_size` — quando isso acontece, o PHP zera $_POST e
// $_FILES ANTES do nosso código rodar (e emite um aviso próprio para o log), o que faria o passo
// de CSRF logo abaixo responder com uma mensagem enganosa ("sua sessão expirou") num caso que na
// verdade é "o anexo é grande demais". Detecção pequena e segura via `Content-Length` do próprio
// cliente comparado a `post_max_size` (convertido para bytes) — sem gerenciar upload progress nem
// infraestrutura adicional. Só dispara quando $_POST/$_FILES vieram vazios E o Content-Length
// declarado excede o limite — uma requisição legitimamente pequena (sem esse cabeçalho ou com
// valor baixo) nunca cai aqui.
if (empty($_POST) && empty($_FILES)) {
    $contentLength = (int) ($_SERVER['CONTENT_LENGTH'] ?? 0);
    $postMaxBytes = ctprice_ouvidoria_ini_bytes(ini_get('post_max_size') ?: '0');
    if ($contentLength > 0 && $postMaxBytes > 0 && $contentLength > $postMaxBytes) {
        ctprice_ouvidoria_respond($isAjax, 413, [
            'success' => false,
            'message' => 'O envio excede o limite permitido pelo servidor. Reduza o tamanho ou a quantidade de anexos e tente novamente.',
        ], 'invalid');
    }
}

// 2) CSRF — token de sessão gerado em ouvidoria/index.php (chave própria, não compartilhada).
$sessionToken = (string) ($_SESSION['ouvidoria_csrf'] ?? '');
$submittedToken = (string) ($_POST['csrf_token'] ?? '');
if ($sessionToken === '' || $submittedToken === '' || !hash_equals($sessionToken, $submittedToken)) {
    ctprice_ouvidoria_respond($isAjax, 403, [
        'success' => false,
        'message' => 'Sua sessão expirou. Atualize a página e tente novamente.',
    ], 'error');
}

// 3) Honeypot — bots preenchem todo campo; um humano nunca vê nem preenche este. Rejeição
// silenciosa: NENHUM arquivo enviado é lido ou processado (o PHP descarta os temporários de
// upload sozinho ao fim da requisição) e nenhum e-mail é enviado.
$honeypot = trim((string) ($_POST['website'] ?? ''));
if ($honeypot !== '') {
    ctprice_ouvidoria_respond($isAjax, 200, [
        'success' => true,
        'message' => 'Manifestação enviada com sucesso! Em breve entraremos em contato.',
    ], 'success');
}

// 4) Rate limit simples por sessão — chave própria desta página (nunca a mesma de Fale Conosco).
// LIMITAÇÃO CONSCIENTE: reduz reenvio trivial pela mesma sessão, mas não substitui proteção por
// IP/proxy/WAF — não implementado nesta fase.
$now = time();
$lastSubmit = (int) ($_SESSION['ouvidoria_last_submit'] ?? 0);
if ($lastSubmit > 0 && ($now - $lastSubmit) < CTPRICE_OUVIDORIA_RATE_LIMIT_SECONDS) {
    ctprice_ouvidoria_respond($isAjax, 429, [
        'success' => false,
        'message' => 'Aguarde alguns segundos antes de enviar novamente.',
    ], 'rate_limited');
}

// 5) Validação server-side dos campos de texto — nunca confia apenas em HTML/JavaScript.
$name = ctprice_clean_line((string) ($_POST['name'] ?? ''));
$contactRaw = trim((string) ($_POST['contact'] ?? ''));
$email = ctprice_clean_line((string) ($_POST['email'] ?? ''));
$companyField = ctprice_clean_line((string) ($_POST['company'] ?? ''));
$message = trim((string) ($_POST['message'] ?? '')); // corpo do e-mail, pode ter quebras de linha

$errors = [];

if ($name === '') {
    $errors['name'] = 'Informe seu nome.';
} elseif (mb_strlen($name) > 150) {
    $errors['name'] = 'Nome muito longo (máximo 150 caracteres).';
}

$contactDigits = preg_replace('/\D/', '', $contactRaw) ?? '';
if ($contactRaw === '') {
    $errors['contact'] = 'Informe um telefone de contato.';
} elseif (mb_strlen($contactRaw) > 30 || strlen($contactDigits) < 8 || strlen($contactDigits) > 13) {
    $errors['contact'] = 'Informe um telefone válido (com DDD).';
}

if ($email === '') {
    $errors['email'] = 'Informe um e-mail para que possamos responder.';
} elseif (mb_strlen($email) > 190 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Informe um e-mail válido.';
}

if ($companyField === '') {
    $errors['company'] = 'Informe o nome da empresa.';
} elseif (mb_strlen($companyField) > 150) {
    $errors['company'] = 'Nome da empresa muito longo (máximo 150 caracteres).';
}

if ($message === '') {
    $errors['message'] = 'Descreva sua manifestação.';
} elseif (mb_strlen($message) > 5000) {
    $errors['message'] = 'Mensagem muito longa (máximo 5000 caracteres).';
}

// 6) Validação dos anexos (opcionais) — quantidade, tamanho, e MIME REAL via finfo (nunca o
// nome/extensão/`type` informados pelo navegador). Path traversal não se aplica: o nome de
// arquivo do usuário nunca é usado como caminho físico (ver ctprice_ouvidoria_build_email()) —
// só como metadado sanitizado no corpo do e-mail.
$attachmentsToSend = [];
$uploadedFiles = $_FILES['anexos'] ?? null;
if ($uploadedFiles && is_array($uploadedFiles['name'] ?? null)) {
    $fileCount = count(array_filter($uploadedFiles['error'], fn ($e) => $e !== UPLOAD_ERR_NO_FILE));

    if ($fileCount > CTPRICE_OUVIDORIA_MAX_FILES) {
        $errors['anexos'] = 'Envie no máximo ' . CTPRICE_OUVIDORIA_MAX_FILES . ' arquivos.';
    } else {
        $totalSize = 0;
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $originalNames = [];

        for ($i = 0; $i < count($uploadedFiles['name']); $i++) {
            $error = $uploadedFiles['error'][$i] ?? UPLOAD_ERR_NO_FILE;
            if ($error === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE) {
                $errors['anexos'] = 'Um dos arquivos excede o limite permitido pelo servidor.';
                break;
            }
            if ($error !== UPLOAD_ERR_OK) {
                $errors['anexos'] = 'Falha ao receber um dos arquivos enviados. Tente novamente.';
                break;
            }

            $tmpName = $uploadedFiles['tmp_name'][$i];
            $size = (int) ($uploadedFiles['size'][$i] ?? 0);

            if (!is_uploaded_file($tmpName)) {
                $errors['anexos'] = 'Falha ao validar um dos arquivos enviados.';
                break;
            }

            if ($size > CTPRICE_OUVIDORIA_MAX_FILE_SIZE) {
                $errors['anexos'] = 'Cada arquivo deve ter no máximo 5MB.';
                @unlink($tmpName);
                break;
            }

            $totalSize += $size;
            if ($totalSize > CTPRICE_OUVIDORIA_MAX_TOTAL_SIZE) {
                $errors['anexos'] = 'O total dos anexos deve ter no máximo 10MB.';
                @unlink($tmpName);
                break;
            }

            $detectedMime = (string) finfo_file($finfo, $tmpName);
            $extension = CTPRICE_OUVIDORIA_ALLOWED_MIMES[$detectedMime] ?? null;
            if ($extension === null) {
                $errors['anexos'] = 'Formato de arquivo não permitido. Envie apenas PDF, JPG ou PNG.';
                @unlink($tmpName);
                break;
            }

            $content = file_get_contents($tmpName);
            @unlink($tmpName);
            if ($content === false) {
                $errors['anexos'] = 'Falha ao ler um dos arquivos enviados.';
                break;
            }

            $originalName = ctprice_clean_line((string) ($uploadedFiles['name'][$i] ?? ''));
            $originalNames[] = mb_substr($originalName, 0, 150) !== '' ? mb_substr($originalName, 0, 150) : ('arquivo-' . ($i + 1));

            $attachmentsToSend[] = [
                'filename' => 'anexo-' . (count($attachmentsToSend) + 1) . '.' . $extension,
                'mime' => $detectedMime,
                'content' => $content,
            ];
        }

        finfo_close($finfo);
    }
}

if ($errors) {
    // Qualquer arquivo temporário ainda não lido/removido (ex.: erro ocorreu num campo de texto,
    // não no upload) é descartado pelo próprio PHP ao fim da requisição — não precisamos
    // gerenciar isso manualmente além do que já fizemos no loop acima.
    ctprice_ouvidoria_respond($isAjax, 422, [
        'success' => false,
        'message' => 'Verifique os campos destacados.',
        'errors' => $errors,
    ], 'invalid');
}

// 7) Destinatário — sempre a partir de config/company.php, nunca hardcoded aqui. Não existe
// e-mail exclusivo de Ouvidoria confirmado ainda (ver comentário no topo do arquivo).
$to = $company['emails']['contato'] ?? '';
if ($to === '') {
    error_log('[ouvidoria] Envio abortado: config/company.php não define emails.contato.');
    ctprice_ouvidoria_respond($isAjax, 500, [
        'success' => false,
        'message' => 'Não foi possível enviar sua manifestação no momento. Tente novamente mais tarde.',
    ], 'error');
}

// 8) Monta e envia o e-mail. From fixo institucional — NUNCA o e-mail digitado pelo denunciante.
$fromName = ctprice_clean_line((string) ($company['razao_social'] ?? 'CT Price'));
$fromEmail = $to;

$subject = 'Nova manifestação - Ouvidoria';
$encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';

$bodyLines = [
    'Nova manifestação recebida pelo formulário de /ouvidoria/:',
    '',
    'Nome: ' . $name,
    'Contato: ' . $contactRaw,
    'E-mail: ' . $email,
    'Empresa: ' . $companyField,
    '',
    'Mensagem:',
    $message,
];
if (!empty($originalNames)) {
    $bodyLines[] = '';
    $bodyLines[] = 'Anexos (nome original informado pelo remetente, não verificado): ' . implode(', ', $originalNames);
}
$bodyText = implode("\n", $bodyLines);

$builtEmail = ctprice_ouvidoria_build_email($fromName, $fromEmail, $email, $bodyText, $attachmentsToSend);

$sent = @mail($to, $encodedSubject, $builtEmail['body'], $builtEmail['headers']);

if (!$sent) {
    // mail() pode falhar silenciosamente quando o servidor não tem um MTA configurado — não é
    // necessariamente um defeito de código (mesma ressalva já registrada em fale-conosco-action.php).
    error_log('[ouvidoria] Falha ao enviar e-mail via mail() — verifique a configuração de MTA do servidor.');
    ctprice_ouvidoria_respond($isAjax, 500, [
        'success' => false,
        'message' => 'Não foi possível enviar sua manifestação no momento. Tente novamente mais tarde ou use o WhatsApp exclusivo.',
    ], 'error');
}

// Rate limit só é atualizado após um envio de fato aceito e processado.
$_SESSION['ouvidoria_last_submit'] = $now;

ctprice_ouvidoria_respond($isAjax, 200, [
    'success' => true,
    'message' => 'Manifestação enviada com sucesso! Em breve entraremos em contato.',
], 'success');
