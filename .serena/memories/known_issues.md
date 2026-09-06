# Known Issues / Pending Decisions (as of the last global audit)

Full detail: `docs/reference/global-final-audit.md` (2026-09-06) and
`docs/reference/global-data-conflicts.md`. Summary of what's still open — check these files for
current status before assuming still-unresolved; update this memory if a fix lands.

## P0 — must fix before any real deploy
`.git/`, `docs/`, `CLAUDE.md`, `README.md` are directly HTTP-servable when the repo root is used
as the document root (confirmed by direct request in a local test) — `.git/config` alone enables
full repo history reconstruction via tools like git-dumper. No `.htaccess` exists yet anywhere.
**Never deploy by copying the repo root as-is** — deploy only the public subset, and/or add
server-level deny rules for `.git/`, `.claude/`, `docs/`, `*.md` before anything goes live.

## P1 — should fix before go-live
- Home page's "Quer receber um contato?" form (`components/contact-section.php`) has **no
  backend at all** — `<form class="contact-form">` with no `action`, no `method`, no JS
  interceptor (unlike Fale Conosco, which has a real endpoint + `contact-form.js`). Submissions
  are silently lost (reload with data in the query string). Real gap discovered in the 2026-09-06
  global audit — not covered by `home-final-validation.md` (that doc explicitly scoped submission
  testing out).
- No custom 404 page exists (the original WordPress site has a themed one).
- No `.htaccess`/production webserver config exists yet (redirect plan for `/wp/*` → `/*` is
  drafted in `docs/architecture-proposal.md` §10-11 and `global-final-audit.md` §41-42, not
  implemented).
- `rel=canonical` exists only on the 3 blog posts, not on the 10 institutional pages.

## Data still pending an explicit CT Price decision
- Address `bairro`/`cep`: "Monte Castelo"/"79.010-190" (visible text) vs. "Vila Rosa Pires"/
  "79002-400" (embedded Google Maps query) — `config/company.php['endereco']` keeps both `null`
  on purpose until resolved; don't guess a value.
- `config/company.php['sistemas_externos']['area_restrita_clientes'|'area_restrita_colaboradores']`
  — kept `null` on purpose (original destinations `ctprice.com.br/documentos` and `/sh-admin` are
  confirmed broken/exposed) — the Área Restrita page already renders a correct "temporarily
  unavailable" state driven by these being null; don't restore the old URLs.
- Menu "Trabalhe Conosco"/"Vagas": header historically points at the external recruitment system,
  footer at the institutional page — `config/menu.php` keeps these `url => null` with a TODO
  rather than picking one.
- `sistemas_externos.agencia_desenvolvimento` (Agência Lester) — `null`; the live domain
  `agencialester.com.br` now returns 404 (confirmed 2026-09-06; earlier audits saw a timeout).
- Several external partner-tool links on `/parcerias/` and one client link on `/depoimentos/`
  confirmed broken/suspicious in the 2026-09-06 audit (404/401/DNS-fail/timeout/403) — listed in
  `global-final-audit.md` §5 and §49; needs CT Price confirmation, not a code fix.
