<?php
/**
 * components/welcome-section.php
 *
 * Primeira seção de conteúdo da Home, imediatamente após o Hero: "Bem-vindo à CT Price" —
 * título + texto introdutório + grade de 4 destaques (ícone + título + texto).
 *
 * Espera, definidas pelo chamador antes do include:
 *
 *   $welcomeHeading (string)                — título (H2)
 *   $welcomeLead    (string)                — parágrafo de introdução
 *   $welcomeItems   (array) — cada item: ['icon' => chave de $welcomeIcons, 'title' => ..., 'text' => ...]
 *                             'title' é impresso sem escapar (conteúdo estático confiável, definido em
 *                             index.php) porque um item reproduz uma quebra de linha manual (<br>)
 *                             presente no HTML original — mesmo padrão já usado no Hero para 'html'.
 *
 * Ícones: SVGs inline equivalentes aos usados no site original (Font Awesome — chart-bar,
 * bell/far, lightbulb/far, headset), com o mesmo desenho, proporção e cor — sem carregar a
 * biblioteca inteira. Path data extraído por medição direta do DOM original.
 *
 * Medições e conteúdo: docs/reference/home-desktop-audit.md (seção 3),
 * docs/reference/home-tablet-audit.md e docs/reference/home-mobile-audit.md (seção "Bem-vindo").
 * Animação de entrada (fadeInLeft) confirmada em data-settings do widget original — reproduzida
 * via CSS + IntersectionObserver (assets/js/scroll-reveal.js), sem biblioteca de animação.
 */

if (!isset($welcomeItems) || !is_array($welcomeItems)) {
    $welcomeItems = [];
}

$welcomeIcons = [
    'chart-bar' => [
        'viewBox' => '0 0 512 512',
        'path' => 'M332.8 320h38.4c6.4 0 12.8-6.4 12.8-12.8V172.8c0-6.4-6.4-12.8-12.8-12.8h-38.4c-6.4 0-12.8 6.4-12.8 12.8v134.4c0 6.4 6.4 12.8 12.8 12.8zm96 0h38.4c6.4 0 12.8-6.4 12.8-12.8V76.8c0-6.4-6.4-12.8-12.8-12.8h-38.4c-6.4 0-12.8 6.4-12.8 12.8v230.4c0 6.4 6.4 12.8 12.8 12.8zm-288 0h38.4c6.4 0 12.8-6.4 12.8-12.8v-70.4c0-6.4-6.4-12.8-12.8-12.8h-38.4c-6.4 0-12.8 6.4-12.8 12.8v70.4c0 6.4 6.4 12.8 12.8 12.8zm96 0h38.4c6.4 0 12.8-6.4 12.8-12.8V108.8c0-6.4-6.4-12.8-12.8-12.8h-38.4c-6.4 0-12.8 6.4-12.8 12.8v198.4c0 6.4 6.4 12.8 12.8 12.8zM496 384H64V80c0-8.84-7.16-16-16-16H16C7.16 64 0 71.16 0 80v336c0 17.67 14.33 32 32 32h464c8.84 0 16-7.16 16-16v-32c0-8.84-7.16-16-16-16z',
    ],
    'bell' => [
        'viewBox' => '0 0 448 512',
        'path' => 'M439.39 362.29c-19.32-20.76-55.47-51.99-55.47-154.29 0-77.7-54.48-139.9-127.94-155.16V32c0-17.67-14.32-32-31.98-32s-31.98 14.33-31.98 32v20.84C118.56 68.1 64.08 130.3 64.08 208c0 102.3-36.15 133.53-55.47 154.29-6 6.45-8.66 14.16-8.61 21.71.11 16.4 12.98 32 32.1 32h383.8c19.12 0 32-15.6 32.1-32 .05-7.55-2.61-15.27-8.61-21.71zM67.53 368c21.22-27.97 44.42-74.33 44.53-159.42 0-.2-.06-.38-.06-.58 0-61.86 50.14-112 112-112s112 50.14 112 112c0 .2-.06.38-.06.58.11 85.1 23.31 131.46 44.53 159.42H67.53zM224 512c35.32 0 63.97-28.65 63.97-64H160.03c0 35.35 28.65 64 63.97 64z',
    ],
    'lightbulb' => [
        'viewBox' => '0 0 352 512',
        'path' => 'M176 80c-52.94 0-96 43.06-96 96 0 8.84 7.16 16 16 16s16-7.16 16-16c0-35.3 28.72-64 64-64 8.84 0 16-7.16 16-16s-7.16-16-16-16zM96.06 459.17c0 3.15.93 6.22 2.68 8.84l24.51 36.84c2.97 4.46 7.97 7.14 13.32 7.14h78.85c5.36 0 10.36-2.68 13.32-7.14l24.51-36.84c1.74-2.62 2.67-5.7 2.68-8.84l.05-43.18H96.02l.04 43.18zM176 0C73.72 0 0 82.97 0 176c0 44.37 16.45 84.85 43.56 115.78 16.64 18.99 42.74 58.8 52.42 92.16v.06h48v-.12c-.01-4.77-.72-9.51-2.15-14.07-5.59-17.81-22.82-64.77-62.17-109.67-20.54-23.43-31.52-53.15-31.61-84.14-.2-73.64 59.67-128 127.95-128 70.58 0 128 57.42 128 128 0 30.97-11.24 60.85-31.65 84.14-39.11 44.61-56.42 91.47-62.1 109.46a47.507 47.507 0 0 0-2.22 14.3v.1h48v-.05c9.68-33.37 35.78-73.18 52.42-92.16C335.55 260.85 352 220.37 352 176 352 78.8 273.2 0 176 0z',
    ],
    'headset' => [
        'viewBox' => '0 0 512 512',
        'path' => 'M192 208c0-17.67-14.33-32-32-32h-16c-35.35 0-64 28.65-64 64v48c0 35.35 28.65 64 64 64h16c17.67 0 32-14.33 32-32V208zm176 144c35.35 0 64-28.65 64-64v-48c0-35.35-28.65-64-64-64h-16c-17.67 0-32 14.33-32 32v112c0 17.67 14.33 32 32 32h16zM256 0C113.18 0 4.58 118.83 0 256v16c0 8.84 7.16 16 16 16h16c8.84 0 16-7.16 16-16v-16c0-114.69 93.31-208 208-208s208 93.31 208 208h-.12c.08 2.43.12 165.72.12 165.72 0 23.35-18.93 42.28-42.28 42.28H320c0-26.51-21.49-48-48-48h-32c-26.51 0-48 21.49-48 48s21.49 48 48 48h181.72c49.86 0 90.28-40.42 90.28-90.28V256C507.42 118.83 398.82 0 256 0z',
    ],
];
?>
<section class="welcome-section">
    <div class="welcome-section__container">
        <h2 class="welcome-section__heading"><?= htmlspecialchars($welcomeHeading ?? '', ENT_QUOTES, 'UTF-8') ?></h2>
        <?php if (!empty($welcomeLead)): ?>
        <p class="welcome-section__lead"><?= htmlspecialchars($welcomeLead, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <div class="welcome-section__grid">
            <?php foreach ($welcomeItems as $item): ?>
                <?php $icon = $welcomeIcons[$item['icon']] ?? null; ?>
                <div class="welcome-card" data-animate-item>
                    <?php if ($icon): ?>
                    <span class="welcome-card__icon">
                        <svg viewBox="<?= htmlspecialchars($icon['viewBox'], ENT_QUOTES, 'UTF-8') ?>" aria-hidden="true">
                            <path d="<?= $icon['path'] ?>"/>
                        </svg>
                    </span>
                    <?php endif; ?>
                    <div class="welcome-card__content">
                        <h3 class="welcome-card__title"><?= $item['title'] ?></h3>
                        <p class="welcome-card__text"><?= htmlspecialchars($item['text'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
