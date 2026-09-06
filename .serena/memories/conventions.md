# Code Conventions

## Page/component wiring
Every page: `require __DIR__ . '/../config/bootstrap.php';` first, then build a plain PHP array
(e.g. `$boxedHero = [...]`) immediately before `require __DIR__ . '/../components/X.php';`. The
component reads its expected array key(s) with `??` defaults and echoes `htmlspecialchars($v,
ENT_QUOTES, 'UTF-8')` for every dynamic value (HTML content that's deliberately trusted, e.g.
`intro_html`/article bodies, is documented as such in a comment and echoed raw).

## `require` runs in caller scope — real bug class, has recurred
`require`/`include` do NOT get their own scope. A component's loop/local variable can silently
clobber a global set earlier by `bootstrap.php` (`$company`, `$menu`) if named the same and the
component is required before `includes/footer.php`/`whatsapp-button.php` (which read `$company`).
Confirmed real incident: a `foreach` using `$company` as the loop var inside
`video-testimonials-section.php` wiped the global `$company` array, breaking the footer/WhatsApp
button; fixed by renaming to `$clientCompany`. Convention since then: any file that's `require`d
into a shared page scope and needs a working variable that shadows a common name must use a
prefixed/unique name (`$ctpriceBlogPosts`, `$ctpricePost`, `$clientCompany`, ...), and `unset()`
scratch variables at the end of config files that build derived arrays.

## Container widths / breakpoints (deliberately unified, not per-original-page)
- Content container: 1140px everywhere in the rebuild, even where the original WordPress page
  measured something else (1200px, ~1240px, etc.) — documented as a conscious consistency choice
  each time it applies, not a fidelity miss.
- Real breakpoints used throughout: 1024px (header nav collapses to hamburger) and 767px (content
  grids/two-column layouts collapse to one column). Any grid that claims a different threshold
  needs a citation to actual measurement, not assumption.
- Multi-item grids that must center a partial last row use flexbox `flex-wrap` +
  `justify-content:center`, never CSS Grid + `nth-child` math.

## Forms security pattern (Fale Conosco, Ouvidoria — copy this shape for any new form)
- CSRF: `bin2hex(random_bytes(32))` stored in `$_SESSION['<page>_csrf']` (page-specific key,
  never shared), compared with `hash_equals()`.
- Honeypot: hidden field named `website`; if non-empty, respond with the SAME success redirect/
  payload as a real submission (never reveal the trap) but never send mail.
- Rate limit: `$_SESSION['<page>_last_submit']` timestamp, page-specific key, ~30s window.
- File uploads (Ouvidoria): validate real MIME via `finfo_file()` against an explicit allow-list
  (never trust client `type`/extension); generated attachment filenames, never the user's;
  temp files always `@unlink()`ed after reading.
- Header injection defense: any user value placed in a mail header goes through a
  `ctprice_clean_line()`-style CRLF-stripper first; visitor email is `Reply-To`, never `From`.
- Responses: POST-only (GET/other methods rejected), 303 redirect with `?status=` for non-JS
  fallback, JSON for `X-Requested-With: XMLHttpRequest`/`Accept: application/json`. Never leak a
  stack trace or server path in any response — log via `error_log()` instead.

## `ctprice_absolute_url()` (config/bootstrap.php)
The only place an absolute (scheme+host) URL is built (needed for `rel=canonical` and social
share links). Never trust `$_SERVER['HTTP_HOST']` directly — it's attacker-controlled (Host
header injection into canonical/share URLs). Validates the request host against
`CTPRICE_CANONICAL_HOST` + `www.` + `localhost`/`127.0.0.1`, else falls back to the canonical
host. Internal links should keep using `BASE_URL . '/path'` (relative-to-root) — this helper is
only for the rare case a value must work outside the browser context.

## Content fidelity (CLAUDE.md)
Never invent/modernize/redesign without an explicit ask. Any measurable property must be
measured against the live reference site, not estimated. Known original defects are preserved
unless the task explicitly asks to fix them (and then it's documented as a conscious deviation,
categorized in `docs/architecture-proposal.md`'s A/B/C/D defect scheme).
