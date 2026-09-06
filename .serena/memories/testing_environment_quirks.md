# Testing/Environment Quirks — do not misdiagnose as app bugs

- **`php -S` fallback routing**: any path that isn't a real file/dir falls back to serving the
  root `index.php` (Home) with 200. This makes 404 behavior untestable via the dev server — real
  404 handling can only be judged against the eventual production webserver config (which doesn't
  exist yet — no `.htaccess` in the repo).
- **Windows `mail()`**: returns success (`true`) from the app's PHP even when the local MTA/relay
  (e.g. Mailpit on port 1025) is unreachable — a known, previously-documented limitation, not a
  code defect. Don't trust `status=success` alone as proof of email delivery on this OS/setup;
  check for a Mailpit inbox entry (when Mailpit is actually running) or server error_log.
- **Background/nohup-launched `php -S` + file uploads**: in this sandboxed Bash tool, a `php -S`
  process started via `(nohup ... &)` can fail to create upload temp files ("unable to create a
  temporary file") even when `upload_tmp_dir` is explicitly pointed at a confirmed-writable local
  directory and a synchronous foreground PHP process can write there fine. Isolated as an
  execution-context quirk of backgrounded processes in this specific sandbox, not reproducible via
  a real browser upload — don't conclude upload/MIME-validation code is broken from this alone;
  verify by reading the validation code directly (`finfo_file()` against an allow-list) instead.
- **Chrome DevTools MCP plugin disconnects mid-session** periodically (`CONNECT_TIMEOUT`) and does
  not reliably self-reconnect via in-session retry (`ToolSearch`) or the troubleshooting skill
  (that skill targets project `.mcp.json` configs, not this plugin-managed server) — requires the
  user to restart/reconnect it. When down, fall back to `curl`+`DOMDocument`-based static crawling
  for anything not genuinely requiring a live browser (link/asset health, form security, HTML
  structure), and say plainly which checks needed a browser and weren't re-run.
- **`take_screenshot` compositing artifact**: has repeatedly failed to visually render certain
  CSS layers correctly in the captured PNG (background-image/gradient right after a scroll/timing
  change; a semi-transparent `background-color` backdrop under a `fixed`-positioned modal) even
  though the real DOM/CSS is correct. Before treating a screenshot as ground truth for "X isn't
  rendering", cross-check with `document.elementFromPoint()` / computed-style inspection.
- **`resize_page` MCP tool**: has gotten stuck reporting a stale/wrong viewport size in this
  session before; `emulate` with an explicit `viewport` string is the reliable way to set size,
  and opening a fresh page (`new_page`) also clears a stuck state.
- **`document.scrollingElement` is `<html>`, not `<body>`**, on this site's pages — any
  scroll-lock CSS (`overflow:hidden` while a modal is open) must target `html`, not `body`, or it
  silently does nothing. Confirmed real bug once (video testimonials lightbox), same fix pattern
  needed for any future modal/lightbox.
