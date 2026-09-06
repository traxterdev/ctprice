# Task Completion Criteria

There is no CI, no test suite, no linter config beyond `php -l`. A page/feature task in this
project is considered done when:

1. `php -l` passes on every changed/new PHP file.
2. Manual `curl`/browser check confirms the page renders 200 with no PHP
   Warning/Notice/Deprecated/Fatal in the output.
3. Live validation via Chrome DevTools MCP at the 5 standard viewports (see
   `mem:suggested_commands`) — structure, no horizontal overflow (`scrollWidth===clientWidth`),
   the specific breakpoint behavior claimed, console/network clean (no own JS errors, no 404, no
   WordPress/Elementor/jQuery leftover).
4. For a new/changed page, a matching `docs/reference/<page>-audit.md` (measurement-first,
   pre-implementation) and `docs/reference/<page>-final-validation.md` (post-implementation,
   16-point-style report) exist, following the same structure as the existing pairs in
   `docs/reference/` — this project's established documentation pattern, not optional polish.
5. Any newly-discovered real bug fixed mid-task is called out explicitly in the final-validation
   doc's "correções realizadas" section, not silently folded in.
6. No commit is made unless the user explicitly asks — historically every task in this project
   ends with "NÃO faça commit" and work is left staged/unstaged for review.
