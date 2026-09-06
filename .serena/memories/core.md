# CT Price — Project Map

Reconstruction of ctprice.com.br (currently WordPress+Elementor) as plain PHP 8+/HTML5/CSS/JS.
Live reference: https://ctprice.com.br/wp/. **Not a redesign** — see project root `CLAUDE.md` for
the fidelity rule (measure, never estimate; preserve visuals/content; no unrequested modernization).

## Directory map
- `/<slug>/index.php` — one directory per public page at site root (institutional pages AND blog
  posts alike, e.g. `sobre-nos/`, `hello-world/`). No router, no framework — Apache/PHP-builtin
  directory+index.php resolution only.
- `config/` — `bootstrap.php` (loaded first by every page: defines `BASE_URL`, loads `$company`/
  `$menu`, session-cookie helper, `ctprice_absolute_url()`) + data files (`company.php`,
  `menu.php`, `blog-posts.php`, `clients.php`, `jobs.php`, `benefits.php`,
  `video-testimonials.php`, `restricted-area.php`, `dedication-section.php`, ...).
- `components/` — presentational includes; each expects a data array assigned by the caller right
  before `require`.
- `includes/` — global chrome: `topbar.php`, `header.php`, `footer.php`, `cookie-banner.php`,
  `whatsapp-button.php`. Same on all 13 pages.
- `blog/_post-template.php` — shared full-page template for all 3 blog posts (topbar→header→
  article-header→article-content-section→footer). Each `/<slug>/index.php` for a post just sets
  `$postSlug` and requires this. NOT itself a public route.
- `content/blog/<slug>.php` — trusted HTML body per article (`return <<<'HTML' ... HTML;`).
- `assets/` — `css/`, `js/`, `images/`, `fonts/` (self-hosted woff2, no Google Fonts at runtime),
  `vendor/swiper/` (only 3rd-party lib in the project).
- `docs/reference/` — audit trail: `<page>-audit.md` + `<page>-final-validation.md` per page,
  `site-inventory.md` (13 canonical URLs), `reference-baseline.md` (frozen baseline + REFERENCE
  DRIFT policy), `global-data-conflicts.md`, `architecture-proposal.md`, `global-final-audit.md`.
  **Never deploy this directory to production** — see `mem:known_issues`.

## Invariants
- No WordPress, no PHP framework, no JS framework, no build step, no Composer (`vendor/` in
  .gitignore is for the unused case; the only real 3rd-party code is `assets/vendor/swiper/`).
- 13 real public URLs total: 10 institutional + 3 blog posts (`hello-world` slug is a real,
  legitimate article — historical WordPress default-post slug, deliberately not renamed).
- CMS/Supabase/admin panel/DB are explicitly out of scope until the whole public site is done and
  approved — never start them unprompted.

## Further memories
- `mem:tech_stack` — stack specifics, fonts, Swiper usage.
- `mem:conventions` — required code patterns (data-before-include, scope-collision hazard,
  escaping, security pattern for forms, container/breakpoint values).
- `mem:suggested_commands` — how to run/stop the local server and lint, Windows/Git-Bash gotchas.
- `mem:task_completion` — what "done" means for a page/feature here.
- `mem:testing_environment_quirks` — sandbox/OS-specific false signals to not misdiagnose as bugs.
- `mem:known_issues` — pending CT Price decisions + confirmed audit findings (P0/P1) not yet fixed.
