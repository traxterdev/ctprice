# Suggested Commands (Windows + Git Bash tool)

## Local server
Start: `php -S 127.0.0.1:8099` from the repo root (background). No router script — requests to a
real directory serve `directory/index.php`; requests to a real static file serve it as-is;
anything else falls back to serving the ROOT `index.php` (Home) with 200 — this is a dev-server
routing artifact, not the app's behavior (see `mem:testing_environment_quirks` before concluding
"404 is broken" from this).

Stop (Bash's `pkill`/`kill` are unreliable against Windows php.exe from this Git-Bash tool — a
stray duplicate process silently keeps the old port bound): use PowerShell instead —
`Get-NetTCPConnection -LocalPort 8099 -State Listen | Stop-Process -Force` (or, to be thorough,
also `Get-Process php | Stop-Process -Force`).

## Lint
`php -l path/to/file.php` per changed file — the only automated check available (no test suite).

## Git Bash gotchas in this environment
- Every Bash call ends with a spurious `.../claude-XXXX-cwd: No such file or directory` line —
  ignore it, it doesn't indicate the real command failed.
- A bare argument/format-string starting with `/` (e.g. a `curl -w` format string like
  `"/foo: %{http_code}\n"`) gets silently mangled into a Windows path by Git Bash's POSIX-path
  auto-conversion. Avoid leading `/` in such strings, or the "result" is garbage, not a real
  finding.
- Writing scratch files: prefer a project-local throwaway dir (clean it up before finishing) over
  `/tmp` (often permission-denied here) or the nominal scratchpad path (its `/c/...` form can also
  fail to resolve from this shell).
- Mixing `curl -d`/`--data-urlencode` with `-F` in the same call is a hard error (exit 2) —
  multipart uploads need every field passed via `-F`.

## Manual verification workflow (no automated tests exist)
1. `php -l` every changed file.
2. Start the dev server, `curl` the affected route(s) for a quick sanity pass.
3. Chrome DevTools MCP for real visual/interactive validation, at the 5 standard viewports:
   1440x900, 900x1200, 768x1024, 767x1024, 390x844. Use `emulate` (not `resize_page`, which has
   been flaky/stuck in this environment) to set viewport size reliably; opening a fresh page via
   `new_page` also resets a stuck viewport.
4. Stop the dev server (PowerShell, see above) when done.
