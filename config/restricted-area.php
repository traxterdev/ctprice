<?php
/**
 * config/restricted-area.php
 *
 * Os 2 acessos exibidos em `/arearestrita/` — conteúdo transcrito literalmente de
 * docs/reference/arearestrita-audit.md (seções 3 e 6). Array estático simples (mesmo espírito de
 * config/jobs.php/config/benefits.php/config/video-testimonials.php) — não é um schema de CMS.
 *
 * A auditoria confirmou que os textos de descrição dos 2 acessos são REALMENTE diferentes um do
 * outro ("fale com a CT Price" vs. "fale com alguém responsável") — preservado como está, sem
 * normalizar os dois para o mesmo texto.
 *
 * `url_key` aponta para a CHAVE em config/company.php['sistemas_externos'] que contém a URL real
 * de cada acesso — a URL em si NUNCA fica duplicada aqui. Enquanto essa chave for `null` (destino
 * ainda não confirmado pela CT Price — ver docs/reference/arearestrita-audit.md, seção 18), o
 * componente renderiza automaticamente o estado indisponível, sem precisar de nenhuma alteração
 * neste arquivo quando a URL for preenchida no futuro.
 *
 * Consumido por: arearestrita/index.php + components/restricted-access-section.php.
 */

return [
    [
        'key' => 'clientes',
        'title' => 'Clientes',
        'description' => 'Acesse aqui sua área restrita. Caso não tenha ou esqueceu os seus dados de acesso, fale com a CT Price.',
        'image' => 'clientes.jpg',
        'url_key' => 'area_restrita_clientes',
    ],
    [
        'key' => 'colaboradores',
        'title' => 'Colaboradores',
        'description' => 'Acesse aqui sua área restrita. Caso não tenha ou esqueceu os seus dados de acesso, fale com alguém responsável.',
        'image' => 'colaboradores.jpg',
        'url_key' => 'area_restrita_colaboradores',
    ],
];
