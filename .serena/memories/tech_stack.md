# Tech Stack

- PHP 8+ (dev-tested against 8.4.12), procedural style, `declare(strict_types=1)` in
  form-processing action scripts.
- HTML5 + hand-written CSS (no preprocessor, no Tailwind/Bootstrap) + vanilla JS (no jQuery, no
  React/Vue).
- No package manager. No Composer (`/vendor/` ignored but unused). Only vendored 3rd-party lib:
  Swiper (`assets/vendor/swiper/swiper-bundle.min.{js,css}`), used only on pages with a carousel
  (Home, Sobre Nós, Informações).
- Fonts: Roboto (variable, 400-900 + italic), Roboto Slab (variable, 400), Roboto Flex (fixed
  400 despite variable source — matches original's fixed declaration), Poppins (static 400/600/
  700) — all self-hosted `.woff2` in `assets/fonts/`, no Google Fonts CDN call at runtime.
- No test framework/runner exists in this project — verification is `php -l` + manual
  `php -S` + Chrome DevTools MCP live browser checks (see `mem:task_completion`).
- No `.htaccess`/webserver config exists yet anywhere in the repo (pre-deploy stage).
