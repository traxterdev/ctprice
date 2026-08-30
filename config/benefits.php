<?php
/**
 * config/benefits.php
 *
 * Os 14 benefícios exibidos em `/trabalhe-conosco/#beneficios` — mesmas 14 imagens do original
 * (`ben01.png`…`ben14.png`, docs/reference/trabalhe-conosco-audit.md, seção 4), na mesma ordem.
 *
 * O nome de cada benefício está desenhado dentro da própria imagem (nenhum título em HTML no
 * original) — por isso todo `alt` abaixo foi escrito a partir da inspeção visual direta de cada
 * arquivo (ver auditoria), descrevendo objetivamente o que a imagem mostra (marca/benefício), sem
 * inventar nenhum texto comercial que não estivesse na imagem original.
 *
 * Arquivos em assets/images/pages/trabalhe-conosco/beneficios/ (baixados e verificados
 * byte-a-byte e por dimensão contra o original nesta implementação).
 *
 * Consumido por: trabalhe-conosco/index.php + components/benefits-grid-section.php.
 */

return [
    ['image' => 'ben01.png', 'alt' => 'Benefício de parceria com a loja Lillium'],
    ['image' => 'ben02.png', 'alt' => 'Benefício de parceria com o Amiste Café'],
    ['image' => 'ben03.png', 'alt' => 'Plano de saúde Hapvida'],
    ['image' => 'ben04.png', 'alt' => 'Cartão de benefícios Caju'],
    ['image' => 'ben05.png', 'alt' => 'Comemoração de aniversário dos colaboradores'],
    ['image' => 'ben06.png', 'alt' => 'Portal do Empregado pelo aplicativo Onvio'],
    ['image' => 'ben07.png', 'alt' => 'Programa de indicação de empresas'],
    ['image' => 'ben08.png', 'alt' => 'Premiação por desempenho'],
    ['image' => 'ben09.png', 'alt' => 'Programa de desenvolvimento ao colaborador'],
    ['image' => 'ben10.png', 'alt' => 'Programa de indicação de talentos'],
    ['image' => 'ben11.png', 'alt' => 'Plano de saúde Unimed'],
    ['image' => 'ben12.png', 'alt' => 'Day off de aniversário (B Day)'],
    ['image' => 'ben13.png', 'alt' => 'Código de vestimenta (dress code)'],
    ['image' => 'ben14.png', 'alt' => 'Programa de ginástica laboral'],
];
