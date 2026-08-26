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
