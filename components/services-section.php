<?php
/**
 * components/services-section.php
 *
 * Seção "Nossos Serviços" da Home, imediatamente após welcome-section: rótulo (eyebrow) +
 * divisor + título + 6 cards de serviço (ícone + título + texto), em duas grades de 3 (não uma
 * única de 6 — ver comentário em assets/css/services-section.css sobre o motivo) + CTA
 * "Fale Conosco".
 *
 * Espera, definidas pelo chamador antes do include:
 *
 *   $servicesEyebrow (string) — rótulo pequeno acima do título (H2, texto real em title-case;
 *                     o caixa-alta visual vem de text-transform:uppercase no CSS, não do dado)
 *   $servicesHeading (string) — título principal (H2)
 *   $servicesItems   (array)  — cada item: ['icon' => chave de $servicesIcons, 'title' => ...,
 *                     'text' => ...]
 *   $servicesCta     (array)  — ['label' => ..., 'url' => ...]
 *
 * 'text' é impresso sem escapar (conteúdo estático confiável, definido em index.php) porque o
 * primeiro item reproduz duas quebras de linha manuais (<br>) presentes no HTML original —
 * mesmo padrão já usado no Hero ('html') e em welcome-section ('title').
 *
 * Ícones: SVGs inline equivalentes aos usados no site original (Font Awesome — swatchbook,
 * building, money-check-alt, hat-cowboy, users, handshake/far), mesmo desenho/proporção/cor,
 * sem carregar a biblioteca inteira. Path data extraído por medição direta do DOM original.
 *
 * CTA: no original, o link "Fale Conosco" aponta para https://ctprice.com.br/contato, que
 * retorna 404 (defeito conhecido, categoria C — docs/architecture-proposal.md, seção 2). Aqui
 * $servicesCta['url'] já deve vir com o destino funcional da nova arquitetura (/fale-conosco/,
 * mesma URL já usada em config/menu.php), não com a URL quebrada do original.
 *
 * Sem animação de entrada: ao contrário de welcome-section, nenhum widget desta seção tem
 * data-settings de animação no original (confirmado por inspeção direta) — não usa
 * assets/js/scroll-reveal.js.
 *
 * Medições: docs/reference/home-desktop-audit.md (seção 1, tabela de larguras/gaps) e
 * reinspeção direta via Chrome DevTools MCP em 1440x900/900x1200/390x844 (ver relatório final).
 *
 * O elemento com id="nossosservicos" reproduz o menu-anchor real do original (mesmo id) — sem
 * altura visual, mas ocupa a posição do primeiro item da grade flex, restaurando o mesmo gap
 * antes do rótulo que existe no original.
 */

if (!isset($servicesItems) || !is_array($servicesItems)) {
    $servicesItems = [];
}

$servicesIcons = [
    'swatchbook' => [
        'viewBox' => '0 0 512 512',
        'path' => 'M434.66,167.71h0L344.5,77.36a31.83,31.83,0,0,0-45-.07h0l-.07.07L224,152.88V424L434.66,212.9A32,32,0,0,0,434.66,167.71ZM480,320H373.09L186.68,506.51c-2.06,2.07-4.5,3.58-6.68,5.49H480a32,32,0,0,0,32-32V352A32,32,0,0,0,480,320ZM192,32A32,32,0,0,0,160,0H32A32,32,0,0,0,0,32V416a96,96,0,0,0,192,0ZM96,440a24,24,0,1,1,24-24A24,24,0,0,1,96,440Zm32-184H64V192h64Zm0-128H64V64h64Z',
    ],
    'building' => [
        'viewBox' => '0 0 448 512',
        'path' => 'M436 480h-20V24c0-13.255-10.745-24-24-24H56C42.745 0 32 10.745 32 24v456H12c-6.627 0-12 5.373-12 12v20h448v-20c0-6.627-5.373-12-12-12zM128 76c0-6.627 5.373-12 12-12h40c6.627 0 12 5.373 12 12v40c0 6.627-5.373 12-12 12h-40c-6.627 0-12-5.373-12-12V76zm0 96c0-6.627 5.373-12 12-12h40c6.627 0 12 5.373 12 12v40c0 6.627-5.373 12-12 12h-40c-6.627 0-12-5.373-12-12v-40zm52 148h-40c-6.627 0-12-5.373-12-12v-40c0-6.627 5.373-12 12-12h40c6.627 0 12 5.373 12 12v40c0 6.627-5.373 12-12 12zm76 160h-64v-84c0-6.627 5.373-12 12-12h40c6.627 0 12 5.373 12 12v84zm64-172c0 6.627-5.373 12-12 12h-40c-6.627 0-12-5.373-12-12v-40c0-6.627 5.373-12 12-12h40c6.627 0 12 5.373 12 12v40zm0-96c0 6.627-5.373 12-12 12h-40c-6.627 0-12-5.373-12-12v-40c0-6.627 5.373-12 12-12h40c6.627 0 12 5.373 12 12v40zm0-96c0 6.627-5.373 12-12 12h-40c-6.627 0-12-5.373-12-12V76c0-6.627 5.373-12 12-12h40c6.627 0 12 5.373 12 12v40z',
    ],
    'money-check-alt' => [
        'viewBox' => '0 0 640 512',
        'path' => 'M608 32H32C14.33 32 0 46.33 0 64v384c0 17.67 14.33 32 32 32h576c17.67 0 32-14.33 32-32V64c0-17.67-14.33-32-32-32zM176 327.88V344c0 4.42-3.58 8-8 8h-16c-4.42 0-8-3.58-8-8v-16.29c-11.29-.58-22.27-4.52-31.37-11.35-3.9-2.93-4.1-8.77-.57-12.14l11.75-11.21c2.77-2.64 6.89-2.76 10.13-.73 3.87 2.42 8.26 3.72 12.82 3.72h28.11c6.5 0 11.8-5.92 11.8-13.19 0-5.95-3.61-11.19-8.77-12.73l-45-13.5c-18.59-5.58-31.58-23.42-31.58-43.39 0-24.52 19.05-44.44 42.67-45.07V152c0-4.42 3.58-8 8-8h16c4.42 0 8 3.58 8 8v16.29c11.29.58 22.27 4.51 31.37 11.35 3.9 2.93 4.1 8.77.57 12.14l-11.75 11.21c-2.77 2.64-6.89 2.76-10.13.73-3.87-2.43-8.26-3.72-12.82-3.72h-28.11c-6.5 0-11.8 5.92-11.8 13.19 0 5.95 3.61 11.19 8.77 12.73l45 13.5c18.59 5.58 31.58 23.42 31.58 43.39 0 24.53-19.05 44.44-42.67 45.07zM416 312c0 4.42-3.58 8-8 8H296c-4.42 0-8-3.58-8-8v-16c0-4.42 3.58-8 8-8h112c4.42 0 8 3.58 8 8v16zm160 0c0 4.42-3.58 8-8 8h-80c-4.42 0-8-3.58-8-8v-16c0-4.42 3.58-8 8-8h80c4.42 0 8 3.58 8 8v16zm0-96c0 4.42-3.58 8-8 8H296c-4.42 0-8-3.58-8-8v-16c0-4.42 3.58-8 8-8h272c4.42 0 8 3.58 8 8v16z',
    ],
    'hat-cowboy' => [
        'viewBox' => '0 0 640 512',
        'path' => 'M490 296.9C480.51 239.51 450.51 64 392.3 64c-14 0-26.49 5.93-37 14a58.21 58.21 0 0 1-70.58 0c-10.51-8-23-14-37-14-58.2 0-88.2 175.47-97.71 232.88C188.81 309.47 243.73 320 320 320s131.23-10.51 170-23.1zm142.9-37.18a16 16 0 0 0-19.75 1.5c-1 .9-101.27 90.78-293.16 90.78-190.82 0-292.22-89.94-293.24-90.84A16 16 0 0 0 1 278.53C1.73 280.55 78.32 480 320 480s318.27-199.45 319-201.47a16 16 0 0 0-6.09-18.81z',
    ],
    'users' => [
        'viewBox' => '0 0 640 512',
        'path' => 'M96 224c35.3 0 64-28.7 64-64s-28.7-64-64-64-64 28.7-64 64 28.7 64 64 64zm448 0c35.3 0 64-28.7 64-64s-28.7-64-64-64-64 28.7-64 64 28.7 64 64 64zm32 32h-64c-17.6 0-33.5 7.1-45.1 18.6 40.3 22.1 68.9 62 75.1 109.4h66c17.7 0 32-14.3 32-32v-32c0-35.3-28.7-64-64-64zm-256 0c61.9 0 112-50.1 112-112S381.9 32 320 32 208 82.1 208 144s50.1 112 112 112zm76.8 32h-8.3c-20.8 10-43.9 16-68.5 16s-47.6-6-68.5-16h-8.3C179.6 288 128 339.6 128 403.2V432c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48v-28.8c0-63.6-51.6-115.2-115.2-115.2zm-223.7-13.4C161.5 263.1 145.6 256 128 256H64c-35.3 0-64 28.7-64 64v32c0 17.7 14.3 32 32 32h65.9c6.3-47.4 34.9-87.3 75.2-109.4z',
    ],
    'handshake' => [
        'viewBox' => '0 0 640 512',
        'path' => 'M519.2 127.9l-47.6-47.6A56.252 56.252 0 0 0 432 64H205.2c-14.8 0-29.1 5.9-39.6 16.3L118 127.9H0v255.7h64c17.6 0 31.8-14.2 31.9-31.7h9.1l84.6 76.4c30.9 25.1 73.8 25.7 105.6 3.8 12.5 10.8 26 15.9 41.1 15.9 18.2 0 35.3-7.4 48.8-24 22.1 8.7 48.2 2.6 64-16.8l26.2-32.3c5.6-6.9 9.1-14.8 10.9-23h57.9c.1 17.5 14.4 31.7 31.9 31.7h64V127.9H519.2zM48 351.6c-8.8 0-16-7.2-16-16s7.2-16 16-16 16 7.2 16 16c0 8.9-7.2 16-16 16zm390-6.9l-26.1 32.2c-2.8 3.4-7.8 4-11.3 1.2l-23.9-19.4-30 36.5c-6 7.3-15 4.8-18 2.4l-36.8-31.5-15.6 19.2c-13.9 17.1-39.2 19.7-55.3 6.6l-97.3-88H96V175.8h41.9l61.7-61.6c2-.8 3.7-1.5 5.7-2.3H262l-38.7 35.5c-29.4 26.9-31.1 72.3-4.4 101.3 14.8 16.2 61.2 41.2 101.5 4.4l8.2-7.5 108.2 87.8c3.4 2.8 3.9 7.9 1.2 11.3zm106-40.8h-69.2c-2.3-2.8-4.9-5.4-7.7-7.7l-102.7-83.4 12.5-11.4c6.5-6 7-16.1 1-22.6L367 167.1c-6-6.5-16.1-6.9-22.6-1l-55.2 50.6c-9.5 8.7-25.7 9.4-34.6 0-9.3-9.9-8.5-25.1 1.2-33.9l65.6-60.1c7.4-6.8 17-10.5 27-10.5l83.7-.2c2.1 0 4.1.8 5.5 2.3l61.7 61.6H544v128zm48 47.7c-8.8 0-16-7.2-16-16s7.2-16 16-16 16 7.2 16 16c0 8.9-7.2 16-16 16z',
    ],
];
?>
<section class="services-section">
    <div class="services-section__container">
        <span id="nossosservicos" class="services-section__anchor" aria-hidden="true"></span>
        <h2 class="services-section__eyebrow"><?= htmlspecialchars($servicesEyebrow ?? '', ENT_QUOTES, 'UTF-8') ?></h2>
        <div class="services-section__divider" role="presentation"></div>
        <h2 class="services-section__heading"><?= htmlspecialchars($servicesHeading ?? '', ENT_QUOTES, 'UTF-8') ?></h2>

        <?php foreach (array_chunk($servicesItems, 3) as $row): ?>
        <div class="services-section__grid">
            <?php foreach ($row as $item): ?>
                <?php $icon = $servicesIcons[$item['icon']] ?? null; ?>
                <div class="service-card">
                    <?php if ($icon): ?>
                    <span class="service-card__icon">
                        <svg viewBox="<?= htmlspecialchars($icon['viewBox'], ENT_QUOTES, 'UTF-8') ?>" aria-hidden="true">
                            <path d="<?= $icon['path'] ?>"/>
                        </svg>
                    </span>
                    <?php endif; ?>
                    <div class="service-card__content">
                        <h3 class="service-card__title"><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <p class="service-card__text"><?= $item['text'] ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>

        <?php if (!empty($servicesCta['url'])): ?>
        <div class="services-section__cta">
            <a class="btn btn--filled" href="<?= htmlspecialchars($servicesCta['url'], ENT_QUOTES, 'UTF-8') ?>">
                <?= htmlspecialchars($servicesCta['label'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            </a>
        </div>
        <?php endif; ?>
    </div>
</section>
