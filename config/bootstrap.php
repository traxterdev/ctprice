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
