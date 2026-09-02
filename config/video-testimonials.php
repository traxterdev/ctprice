<?php
/**
 * config/video-testimonials.php
 *
 * Os 7 depoimentos em vídeo de `/depoimentos/` — conteúdo transcrito literalmente de
 * docs/reference/depoimentos-audit.md, seção 3 (inventário auditado no site original).
 *
 * INDEPENDENTE de `$testimonials` (Home, hoje definido em index.php, consumido por
 * components/testimonials-section.php): a auditoria confirmou que os dois conjuntos praticamente
 * não se sobrepõem — apenas "Mario Jorge/AgroTouro" aparece nas duas páginas, com citação e
 * assets completamente diferentes em cada uma. Não há duplicação real a eliminar unificando as
 * duas fontes, então elas permanecem separadas (ver docs/reference/depoimentos-audit.md, seção 4).
 *
 * Array estático simples (mesmo espírito de config/clients.php/config/jobs.php/config/benefits.php)
 * — não é um schema de CMS. Nenhum campo de cargo/rating/estrelas foi inventado: o original não
 * tem nenhum desses dados.
 *
 * `photo`/`thumbnail` são nomes de arquivo relativos a
 * assets/images/pages/depoimentos/people/ e .../thumbnails/ respectivamente (BASE_URL e os dois
 * diretórios são montados pelo componente, não aqui — mesma convenção de
 * components/clients-carousel-section.php).
 *
 * `video_id`/`video_list`: extraídos das URLs auditadas (`youtube.com/watch?v=ID[&list=LIST]`
 * ou `youtu.be/ID`) — nenhum vídeo foi trocado. Só o depoimento de Aline Zacarini tem `video_list`
 * (playlist do YouTube presente na URL original).
 *
 * `website_url`: preservada exatamente como auditada, incluindo os casos com `http://` (não
 * `https://`) — não é uma correção nossa a fazer aqui. Para Walter Ferreira Cruz, `website_url`
 * é `null` DE PROPÓSITO: a auditoria confirmou que os links de "site" e "Instagram" do card
 * original apontavam para a MESMA URL do Instagram (nenhum site próprio foi de fato linkado) —
 * em vez de duplicar o mesmo botão duas vezes (o que sugeriria, de forma enganosa, dois destinos
 * diferentes), esta implementação renderiza só o botão do Instagram para esse card. Nenhuma URL
 * nova foi inventada. Pendência: confirmar com a CT Price se existe um site próprio para
 * divulgar nesse card.
 *
 * Consumido por: depoimentos/index.php + components/video-testimonials-section.php.
 */

return [
    [
        'name' => 'Aline Zacarini',
        'company' => 'Agro Só Sal',
        'quote' => 'Contar com a CT price é a certeza de estar sempre com a melhor parceira.',
        'photo' => 'aline-zacarini.jpeg',
        'thumbnail' => 'aline-zacarini.jpeg',
        'video_id' => 'Vr9EFGVx0T8',
        'video_list' => 'PLQhq9pdnKsr86BVYW6xYn51NHHyvGLSZr',
        'website_url' => 'http://www.agrososal.com.br/',
        'instagram_url' => 'https://www.instagram.com/agrososal',
    ],
    [
        'name' => 'Bruno Alessio',
        'company' => 'Soldamaq',
        'quote' => 'CT Price: nossa parceira há 3 anos, trazendo visão estratégica e gerencial para melhorar nossa performance.',
        'photo' => 'bruno-alessio.png',
        'thumbnail' => 'bruno-alessio.png',
        'video_id' => 'Cq4w62rSpyE',
        'video_list' => null,
        'website_url' => 'https://www.soldamaq.com.br/',
        'instagram_url' => 'https://www.instagram.com/soldamaq/',
    ],
    [
        'name' => 'Réus Fornari',
        'company' => 'Cotto Figueira',
        'quote' => 'Há mais de 15 anos com a CT Price: comunicação próxima e o conforto de crescer com segurança.',
        'photo' => 'reus-fornari.png',
        'thumbnail' => 'reus-fornari.png',
        'video_id' => 'eyPTwRBjzU0',
        'video_list' => null,
        'website_url' => 'https://cottofigueira.com/',
        'instagram_url' => 'https://www.instagram.com/cottofigueira/',
    ],
    [
        'name' => 'Mario Jorge',
        'company' => 'AgroTouro',
        'quote' => 'Há 13 anos com a CT Price, tenho tranquilidade para crescer e desenvolver o meu trabalho.',
        'photo' => 'mario-jorge.png',
        'thumbnail' => 'mario-jorge.png',
        'video_id' => 'yNKcg8QHjws',
        'video_list' => null,
        'website_url' => 'https://www.agrotouro.com/',
        'instagram_url' => 'https://www.instagram.com/agrotouroms/',
    ],
    [
        'name' => 'João Francisco',
        'company' => 'Grupo Figueira',
        'quote' => 'Há quase 15 anos, contamos com o trabalho impecável da CT Price, que acompanha o nosso crescimento em cada etapa.',
        'photo' => 'joao-francisco.jpg',
        'thumbnail' => 'joao-francisco.jpg',
        'video_id' => 'hVOh8mo_sm0',
        'video_list' => null,
        'website_url' => 'https://materiais.postofigueira.com.br/postos-figueira',
        'instagram_url' => 'http://instagram.com/grupofigueirabr',
    ],
    [
        'name' => 'Mário Sérgio Miguel',
        'company' => 'Campo Doce',
        'quote' => 'A CT Price inovou e mudou a nossa visão sobre a contabilidade da nossa empresa.',
        'photo' => 'mario-sergio-miguel.png',
        'thumbnail' => 'mario-sergio-miguel.png',
        'video_id' => '3cOzYXDAP7A',
        'video_list' => null,
        'website_url' => 'https://campodoce.com.br/',
        'instagram_url' => 'https://www.instagram.com/campodocedistribuidora',
    ],
    [
        'name' => 'Walter Ferreira Cruz',
        'company' => 'Saborzitos',
        'quote' => 'O trabalho da CT Price é excelente. Estamos com eles há 5 anos e estamos muito satisfeitos.',
        'photo' => 'walter-ferreira-cruz.jpeg',
        'thumbnail' => 'walter-ferreira-cruz.jpeg',
        'video_id' => 'yuv_kedv72I',
        'video_list' => null,
        'website_url' => null, // ver comentário do arquivo — mesma URL do Instagram no original
        'instagram_url' => 'https://www.instagram.com/saborzitosoficial',
    ],
];
