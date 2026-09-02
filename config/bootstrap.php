<?php
/**
 * config/bootstrap.php
 *
 * Ponto único de configuração de ambiente do projeto. Toda página pública deve incluir este
 * arquivo uma única vez, no topo (antes de qualquer include de includes/ ou components/).
 *
 * Responsabilidades:
 *
 * 1) Define BASE_URL — a raiz pública do site, usada para montar URLs de assets (CSS, JS,
 *    imagens) de forma absoluta a partir da raiz do domínio. Como todas as páginas vivem em
 *    "/" ou em uma subpasta de primeiro nível (ex.: "/sobre-nos/"), usar caminhos absolutos
 *    como "{BASE_URL}/assets/css/header.css" funciona identicamente em qualquer página, sem
 *    precisar calcular "../" manualmente em cada template. Se o site for publicado em um
 *    subdiretório em vez da raiz do domínio, basta ajustar esta única constante.
 *
 * 2) Carrega, uma única vez, os dados globais (config/company.php) e a estrutura de menu
 *    (config/menu.php), disponibilizando-os como $company e $menu para qualquer arquivo
 *    incluído depois deste no mesmo escopo (includes/topbar.php, includes/header.php etc.).
 *
 * Ver docs/architecture-proposal.md, seções 3, 9 e 13.
 */

if (!defined('BASE_URL')) {
    define('BASE_URL', '');
}

$company = require __DIR__ . '/company.php';
$menu = require __DIR__ . '/menu.php';

/**
 * Configura os parâmetros do cookie de sessão ANTES de session_start() ser chamado. Centralizado
 * aqui porque é o único arquivo já incluído por toda página — hoje só `/fale-conosco/` usa sessão
 * (token CSRF + rate limit do formulário, ver fale-conosco/fale-conosco-action.php), mas qualquer
 * página futura que precise de sessão deve reutilizar esta função em vez de duplicar a
 * configuração. Chamar esta função não inicia uma sessão nem define um cookie por si só — ela só
 * tem efeito quando a página que a chama também chama `session_start()` em seguida.
 *
 * - `httponly`: sempre ativo — o cookie de sessão não precisa (e não deve) ser lido por
 *   JavaScript no navegador.
 * - `samesite=Lax`: permite navegação normal (ex.: alguém chegando a `/fale-conosco/` a partir de
 *   um link em outro site) sem expor o cookie a requisições cross-site de terceiros — adequado
 *   para um formulário que não precisa funcionar embutido em iframe de outro domínio.
 * - `secure`: ativado automaticamente SOMENTE quando a requisição já chegou via HTTPS
 *   (`$_SERVER['HTTPS']` ou a porta 443) — em HTTP puro (ambiente local de desenvolvimento) fica
 *   desativado de propósito, porque um cookie `Secure` é descartado pelo navegador em conexões
 *   não criptografadas, o que quebraria a sessão localmente. PENDÊNCIA DE PRODUÇÃO: se o servidor
 *   real ficar atrás de um proxy reverso/load balancer que termina o HTTPS antes do PHP, é preciso
 *   confirmar que esse proxy popula `$_SERVER['HTTPS']` (ou ajustar esta função para também checar
 *   um cabeçalho como `X-Forwarded-Proto`) — isso depende da infraestrutura de produção, não pode
 *   ser confirmado a partir deste código.
 */
/**
 * Domínio canônico de produção — ÚNICA definição deste valor no projeto (usado só pela validação
 * de Host em `ctprice_absolute_url()` abaixo, nunca repetido em nenhum outro arquivo).
 */
if (!defined('CTPRICE_CANONICAL_HOST')) {
    define('CTPRICE_CANONICAL_HOST', 'ctprice.com.br');
}

/**
 * Monta uma URL ABSOLUTA (com esquema + host) a partir de um caminho relativo à raiz do site —
 * necessário só quando um valor precisa funcionar fora do contexto do próprio navegador (ex.:
 * `rel="canonical"`, links de compartilhamento em redes sociais que abrem em outro domínio). A
 * maioria das páginas nunca precisa disso — para links internos, `BASE_URL . '/caminho'` (relativo
 * à raiz) já basta e é o padrão usado em todo o resto do projeto.
 *
 * SEGURANÇA — Host Header: `$_SERVER['HTTP_HOST']` vem da requisição, controlado pelo cliente, e
 * NUNCA é usado diretamente aqui (um `Host` forjado seria refletido no `canonical` e nas URLs de
 * compartilhamento, ex.: envenenar o `canonical` para apontar a outro domínio, ou compartilhar um
 * link com título/conteúdo da CT Price mas domínio de terceiro). `htmlspecialchars` na saída
 * impede XSS, mas não impede esse tipo de "canonical/share poisoning" — por isso o host da
 * requisição só é aceito se bater (ignorando porta) com `CTPRICE_CANONICAL_HOST` ou com o próprio
 * ambiente local de desenvolvimento; qualquer outro valor cai no fallback canônico conhecido.
 */
if (!function_exists('ctprice_absolute_url')) {
    function ctprice_absolute_url(string $path): string
    {
        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['SERVER_PORT'] ?? null) === '443');
        $scheme = $isHttps ? 'https' : 'http';

        $requestHost = (string) ($_SERVER['HTTP_HOST'] ?? '');
        $requestHostname = strtolower((string) strtok($requestHost, ':'));

        $allowedHostnames = [
            CTPRICE_CANONICAL_HOST,
            'www.' . CTPRICE_CANONICAL_HOST,
            'localhost',
            '127.0.0.1',
        ];

        $host = in_array($requestHostname, $allowedHostnames, true) ? $requestHost : CTPRICE_CANONICAL_HOST;

        // Aceita $path com ou sem "/" inicial de forma previsível (nunca gera host+caminho colados).
        $normalizedPath = '/' . ltrim($path, '/');

        return $scheme . '://' . $host . BASE_URL . $normalizedPath;
    }
}

if (!function_exists('ctprice_configure_session_cookie')) {
    function ctprice_configure_session_cookie(): void
    {
        if (session_status() !== PHP_SESSION_NONE) {
            return;
        }

        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['SERVER_PORT'] ?? null) === '443');

        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax',
            'secure' => $isHttps,
        ]);
    }
}
