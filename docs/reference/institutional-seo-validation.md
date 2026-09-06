# SEO institucional — validação (sprint de fechamento)

Data: 2026-09-06
Escopo: as 10 páginas institucionais (canonical/description ausentes, conforme
`global-final-audit.md`, seções 25–27). Os 3 artigos já tinham SEO básico validado e não foram
alterados.

## Arquitetura

`includes/seo-head.php` — partial de `<head>` (mesmo padrão de `includes/header.php`/
`includes/footer.php`, sem classe/serviço/sistema genérico). Cada página institucional define
`$pageMeta = ['title' => ..., 'description' => ..., 'canonical_path' => ...]` antes do próprio
`<head>` e inclui o partial no lugar da antiga tag `<title>` solta. O canonical é sempre construído
por `ctprice_absolute_url()` (`config/bootstrap.php`), já protegida contra Host Header forjado —
nenhuma lógica nova de segurança foi criada.

`404.php` e `blog/_post-template.php` não usam este partial (o primeiro não deve ter canonical/
description institucional; o segundo já implementa o mesmo padrão inline, com description vinda do
excerpt do post).

## Tabela — 10 institucionais

| Página | Title | Description | Canonical | Alteração |
|---|---|---|---|---|
| `/` | CT Price | Criada (nenhuma existia) | `https://ctprice.com.br/` | `$pageMeta` + canonical/description novos |
| `/sobre-nos/` | Sobre nós — CT Price | Criada | `https://ctprice.com.br/sobre-nos/` | idem |
| `/clientes/` | Clientes — CT Price | Criada | `https://ctprice.com.br/clientes/` | idem |
| `/parcerias/` | Parcerias — CT Price | Criada | `https://ctprice.com.br/parcerias/` | idem |
| `/fale-conosco/` | Fale Conosco — CT Price | Criada | `https://ctprice.com.br/fale-conosco/` | idem |
| `/informacoes/` | Informações — CT Price | Criada | `https://ctprice.com.br/informacoes/` | idem |
| `/trabalhe-conosco/` | Trabalhe Conosco — CT Price | Criada | `https://ctprice.com.br/trabalhe-conosco/` | idem |
| `/ouvidoria/` | Ouvidoria — CT Price | Criada | `https://ctprice.com.br/ouvidoria/` | idem |
| `/depoimentos/` | Depoimentos — CT Price | Criada | `https://ctprice.com.br/depoimentos/` | idem |
| `/arearestrita/` | Área restrita — CT Price | Criada | `https://ctprice.com.br/arearestrita/` | idem |

Todos os 10 `<title>` foram preservados exatamente como estavam (já únicos e coerentes) — nenhum
foi reescrito.

## Testes executados (servidor local `php -S`)

- **HTTP**: 10 institucionais + 3 artigos → 200; `/404.php` → 404; asset estático inexistente → 404.
- **Metadata**: 1 `<title>` + 1 `<meta name="description">` + 1 `<link rel="canonical">` em cada
  institucional, confirmado via extração do HTML renderizado.
- **Host Header forjado** (`Host: evil.com`) em `/`, `/clientes/`, `/ouvidoria/` e `/hello-world/`:
  canonical sempre retorna a `ctprice.com.br`, nunca reflete o host forjado.
- **Query string / barra final**: `/clientes/?utm_source=teste`, `/clientes`, `/clientes/` e
  `/hello-world/?utm_source=teste&foo=bar` → canonical sempre limpo, sem query string.
- **404**: `noindex,follow` presente, sem `<link rel="canonical">`, sem meta description
  institucional.
- **Artigos (smoke test)**: os 3 posts mantiveram title/description(excerpt)/canonical únicos,
  sem regressão.
- **13 titles**: confirmados únicos (0 duplicados).
- **`/wp/` residual**: zero ocorrências nas 13 páginas.
- **PHP**: `php -l` sem erro nos 12 arquivos tocados; zero
  Warning/Notice/Fatal/Deprecated/Parse error no HTML das 13 páginas.

## Arquivos modificados/criados

- Criado: `includes/seo-head.php`
- Modificados: `index.php`, `sobre-nos/index.php`, `clientes/index.php`, `parcerias/index.php`,
  `fale-conosco/index.php`, `informacoes/index.php`, `trabalhe-conosco/index.php`,
  `ouvidoria/index.php`, `depoimentos/index.php`, `arearestrita/index.php`

Nenhum arquivo de conteúdo, layout, CSS ou dado institucional foi alterado.
