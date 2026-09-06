# Plano de redirects 301 — URLs antigas WordPress

Data: 2026-09-06
Escopo: mapeamentos comprovados por `docs/reference/site-inventory.md` e
`docs/reference/global-final-audit.md` — nenhum redirect foi criado por semelhança textual ou
suposição. Implementação: `.htaccess` (raiz), seção "Redirects 301 de URLs antigas WordPress".

## A — Obrigatório (implementado)

URLs públicas reais antigas com equivalente direto confirmado no inventário.

| URL antiga | URL nova | Status | Implementado |
|---|---|---|---|
| `/wp/` | `/` | 301 | ✅ |
| `/wp/sobre-nos/` | `/sobre-nos/` | 301 | ✅ |
| `/wp/clientes/` | `/clientes/` | 301 | ✅ |
| `/wp/parcerias/` | `/parcerias/` | 301 | ✅ |
| `/wp/fale-conosco/` | `/fale-conosco/` | 301 | ✅ |
| `/wp/informacoes/` | `/informacoes/` | 301 | ✅ |
| `/wp/trabalhe-conosco/` | `/trabalhe-conosco/` | 301 | ✅ |
| `/wp/ouvidoria/` | `/ouvidoria/` | 301 | ✅ |
| `/wp/depoimentos/` | `/depoimentos/` | 301 | ✅ |
| `/wp/arearestrita/` | `/arearestrita/` | 301 | ✅ |
| `/wp/reforma-trabalhista-volta-a-pauta-do-stf-julgamento-acontece-neste-mes/` | `/reforma-trabalhista-volta-a-pauta-do-stf-julgamento-acontece-neste-mes/` | 301 | ✅ |
| `/wp/receita-federal-e-correios-lancam-portal-de-compras-internacionais/` | `/receita-federal-e-correios-lancam-portal-de-compras-internacionais/` | 301 | ✅ |
| `/wp/hello-world/` | `/hello-world/` | 301 | ✅ |

Slugs conferidos contra `config/blog-posts.php` (fonte real dos slugs em produção) antes de
escrever as regras — nenhuma divergência encontrada em relação a `site-inventory.md`.

## B — Recomendado (implementado)

Aliases comprovados no inventário, sem página própria no WordPress original.

| URL antiga | URL nova | Status | Implementado |
|---|---|---|---|
| `/wp/home/` | `/` | 301 (direto, sem passar por `/wp/`) | ✅ |

`site-inventory.md` confirma: `/wp/home/` sempre foi um alias/redirect da própria Home no
WordPress (mesmo `data-elementor-id`), nunca uma página com conteúdo próprio.

## C — NÃO redirecionar sem decisão do cliente/CT Price

| URL antiga | Motivo de não implementar | Fonte |
|---|---|---|
| `/contato` | Achado como 404 no original, usado pelo CTA "Fale Conosco" da seção "Nossos Serviços" da Home. `global-final-audit.md` (pendências consolidadas, item 4) registra **decisão pendente** sobre se o destino correto é `/fale-conosco/` ou se o comportamento quebrado deve ser preservado — não é um mapeamento inequívoco, portanto não implementado (regra explícita desta sprint: ambiguidade documental → não implementar). | `site-inventory.md` §3.1, `global-final-audit.md` §49.4 |
| `/contato/` | Mesmo caso acima. | idem |
| `/documentos` | Destino quebrado da antiga Área Restrita (404 puro no original); `config/company.php['sistemas_externos']['area_restrita_clientes']` continua `null` — não há destino real para apontar. | `site-inventory.md` §3.2, `global-final-audit.md` §11/12 |
| `/sh-admin` | Destino quebrado/exposto da antiga Área Restrita (listagem de diretório crua no original); `area_restrita_colaboradores` continua `null`. | `site-inventory.md` §3.3 |
| `/sh-admin/` | Mesmo caso acima. | idem |

Nenhuma dessas 5 URLs ganhou destino inventado — todas continuam 404 (confirmado na seção de
testes de `docs/reference/404-redirects-server-validation.md`).

## D — Residual / sem conteúdo público genuíno (não redirecionado)

| URL antiga | Motivo | Fonte |
|---|---|---|
| `/wp/blog/` | Nunca existiu como página no WordPress original — já retornava a 404 estilizada do próprio tema. Não é um destino "perdido", é a confirmação de que nunca houve página de arquivo/listagem do blog. | `site-inventory.md` §3.6 |
| `/wp/AAAA/MM/DD/...` (arquivos por data) | A auditoria dos posts confirmou que esses arquivos de data não têm conteúdo público genuíno — não fazem sentido nem como redirect para a Home nem para o blog. | `blog-posts-audit.md`, `global-final-audit.md` §K (validação final dos posts) |

Nenhuma regra genérica `^wp/(.*)$` foi criada — de propósito. Uma regra assim esconderia
qualquer URL antiga sem mapeamento real atrás de um destino novo também inexistente,
mascarando o problema em vez de expô-lo como 404.

## Comportamento observado (empírico, Apache real — `ctprice.traxter`)

- **Sem encadeamento**: todos os 14 redirects (A + B) vão direto ao destino final —
  confirmado com `curl -L`, `num_redirects=1` em todos os casos testados, incluindo
  `/wp/home/` (vai direto a `/`, nunca passa por `/wp/`) e `/wp/clientes`/`/wp/clientes/`
  (ambos vão direto a `/clientes/`, mesma regra via `/?` opcional no regex).
- **Query string**: preservada automaticamente pelo comportamento padrão do Apache
  (`/wp/clientes/?utm_source=teste` → `Location: /clientes/?utm_source=teste`) — nenhuma
  lógica de limpeza foi escrita.
- **Fragmentos** (`#...`): nunca chegam ao servidor (padrão HTTP) — não se aplica regra
  server-side; o navegador preserva o fragmento ao seguir o redirect normalmente.
- **Host Header**: ver `docs/reference/404-redirects-server-validation.md`, seção "Host
  Header nos redirects" — o Host da requisição só é refletido no `Location` se estiver na
  mesma allowlist já aprovada em `ctprice_absolute_url()`; qualquer outro cai no domínio
  canônico de produção.

Todos os 14 redirects usam `[R=301,L,NC]` — nunca 302/307/meta refresh/JavaScript.
