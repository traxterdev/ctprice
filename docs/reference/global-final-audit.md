# Auditoria Global Final — Site Público CT Price

Data: 2026-09-06
Escopo: as 13 URLs públicas reais (10 páginas institucionais + 3 posts), antes de CMS/Supabase/
painel administrativo/publicação definitiva.

**REGRA DESTA ETAPA RESPEITADA**: nenhuma correção foi aplicada. Todos os achados abaixo estão
documentados para uma rodada de correção futura — nenhum PHP/CSS/JS/config/componente/conteúdo
foi alterado nesta auditoria. Só este relatório foi criado.

## Nota metodológica importante — limitação desta sessão

O Chrome DevTools MCP (navegador real) esteve **indisponível/desconectado durante toda esta
auditoria** (falha de conexão do plugin, mesmo padrão intermitente já documentado em sessões
anteriores deste projeto). Por isso, esta auditoria foi executada por uma combinação de:

- **Crawl programático real** contra o servidor PHP local (`curl` + parsing real do DOM via
  `DOMDocument`, não regex ingênuo) — cobre as 13 páginas por completo: status HTTP, todos os
  `href`/`src`/`action` renderizados, resíduos WordPress, formulários, segurança superficial.
- **Testes reais dos endpoints de formulário** (CSRF/honeypot/rate-limit/validação/upload) via
  `curl` simulando requisições HTTP reais (com cookies de sessão reais, tokens reais extraídos do
  HTML) — não é teste manual no navegador, mas é execução real do código PHP de produção, não uma
  suposição.
- **Teste de ataque real** (Host Header forjado) contra o servidor ao vivo.
- Para **responsividade/acessibilidade/comportamento visual/interativo detalhado por página**
  (itens que exigem genuinamente um navegador — hover, foco visual, scroll, lightbox, carrossel),
  esta auditoria **não re-executou** a bateria completa em cada uma das 13 páginas — em vez disso,
  **reconfirma o inventário e a saúde estrutural/de link/de dados de cada página** e **se apoia nos
  relatórios `*-final-validation.md` já existentes** (cada um já validado ao vivo, em sessão
  própria, com o navegador disponível) para o comportamento visual/interativo já aprovado
  anteriormente. Nenhuma mudança de código foi feita desde essas validações que afete CSS/JS/
  componentes das 10 páginas institucionais — só `config/blog-posts.php`,
  `config/bootstrap.php` e os arquivos dos 3 posts foram tocados na etapa anterior, já com sua
  própria validação final registrada em `blog-posts-final-validation.md`.

Isso é uma limitação real, registrada com honestidade — não um resultado inventado. Se uma
validação visual/interativa ao vivo completa das 13 páginas for exigida antes do go-live, ela deve
ser reagendada para quando o Chrome DevTools MCP estiver disponível novamente.

---

## 1. Resumo executivo

As 13 páginas carregam (HTTP 200), sem erro PHP, sem resíduo funcional de WordPress. A arquitetura
global (config/componentes compartilhados) está consistente. Os 2 formulários (Fale Conosco,
Ouvidoria) passaram na regressão de segurança (CSRF, honeypot, rate-limit, validação). **Um achado
crítico (P0) foi confirmado por teste real**: a pasta `.git/` e vários arquivos de documentação
interna (`CLAUDE.md`, `docs/`, `README.md`) são servidos diretamente por HTTP neste ambiente —
isso **deve** ser bloqueado antes de qualquer publicação real, independente de qual servidor for
usado em produção. Um segundo achado relevante: **o formulário de contato da Home
("Quer receber um contato?") não tem nenhum backend conectado** — envios não vão a lugar nenhum.
Nenhuma dependência WordPress remanescente foi encontrada em lugar nenhum do HTML renderizado.

---

## 2. Inventário das 13 páginas

| # | URL | HTTP | PHP error/warning | Título |
|---|---|---|---|---|
| 1 | `/` | 200 | Nenhum | CT Price |
| 2 | `/sobre-nos/` | 200 | Nenhum | Sobre nós — CT Price |
| 3 | `/clientes/` | 200 | Nenhum | (confirmado carregando, título já validado em clientes-final-validation.md) |
| 4 | `/parcerias/` | 200 | Nenhum | idem, ver parcerias-final-validation.md |
| 5 | `/fale-conosco/` | 200 | Nenhum | idem |
| 6 | `/informacoes/` | 200 | Nenhum | idem |
| 7 | `/trabalhe-conosco/` | 200 | Nenhum | idem |
| 8 | `/ouvidoria/` | 200 | Nenhum | idem |
| 9 | `/depoimentos/` | 200 | Nenhum | idem |
| 10 | `/arearestrita/` | 200 | Nenhum | idem |
| 11 | `/reforma-trabalhista-volta-a-pauta-do-stf-julgamento-acontece-neste-mes/` | 200 | Nenhum | confirmado (ver blog-posts-final-validation.md) |
| 12 | `/receita-federal-e-correios-lancam-portal-de-compras-internacionais/` | 200 | Nenhum | idem |
| 13 | `/hello-world/` | 200 | Nenhum | idem |

`grep -l -i "Fatal error\|Warning:\|Notice:\|Deprecated:\|Parse error"` nas 13 respostas HTML: **0
ocorrências**. Nenhuma página vazia (todas com 19KB–86KB de HTML renderizado). **Nenhuma URL
pública adicional genuína foi encontrada** além das 13 já confirmadas em auditorias anteriores.

---

## 3 e 4. Saúde dos links + resíduos WordPress

Crawl real via `DOMDocument` nas 13 páginas: **114 hrefs internos/externos únicos**, **190 imagens
únicas**, **12 scripts únicos**, **39 stylesheets/links únicos**, **3 `<form>`**, **13 `<iframe>`**
(todos o mesmo embed do Google Maps, global).

### Resíduos WordPress — busca consolidada nas 13 páginas
`/wp/`, `wp-content`, `wp-admin`, `admin-ajax`, `elementor`, `jquery`, `wordpress`, `wp-json`:
**zero ocorrências em qualquer uma das 13 páginas.** Confirma: **ZERO dependência operacional de
WordPress**, conforme objetivo do projeto.

### Links internos entre as 13 páginas
Todos os 10 institucionais + 3 posts aparecem no menu/footer/cards com o caminho local correto
(contagem 13x para os institucionais — presentes em toda página; 4x para cada post — Home +
Informações + as 2 aparições como "relacionado" nos outros 2 posts). **Nenhum CTA interno aponta
para o WordPress antigo.**

### Assets locais (imagens/CSS/JS/fontes)
**202 caminhos locais únicos** (`/assets/...`) extraídos do HTML das 13 páginas — **todos
retornam 200** (checado individualmente, um a um). As 7 fontes esperadas (`roboto-variable`,
`roboto-italic`, `roboto-slab-400`, `roboto-flex-400`, `poppins-400/600/700`) existem em
`assets/fonts/` e respondem 200. **Zero asset local quebrado.**

### `href="#"` do submenu "Vagas" — investigação solicitada
Confirmado no código (`includes/header.php`): os itens de TOPO com submenu ("Clientes e
Parceiros", "Trabalhe Conosco") já são `<button type="button">` — **não navegam, só controlam o
submenu — correto, sem problema**. O `href="#"` real está no item **filho** "Vagas"
(`config/menu.php`, `'url' => null`), que é renderizado como `<a href="#">` porque o destino ainda
não foi confirmado com o cliente (TODO já registrado em `config/menu.php`). Clicar nele navega de
fato (rola para o topo da página atual) — uma experiência de clique ruim, mas **não é um bug
novo**: é a manifestação visível de uma pendência de dados já registrada (qual sistema "Vagas"/
"Trabalhe Conosco" deve usar). Classificado como **CLIENTE** (pendência já existente, não uma
falha de implementação) — o elemento já é semanticamente um `<a>` (correto, pois deve navegar
quando o destino for definido), não precisa virar `<button>`.

---

## 5. Links externos — inventário e status real

**97 URLs externas únicas** testadas uma a uma via `curl` (com redirecionamentos seguidos,
timeout de 5–10s, User-Agent de navegador, sem autenticação/envio de formulário/pentest).

### Achados reais (não 200 limpo)

| URL | Origem | Resultado | Classificação |
|---|---|---|---|
| `https://agencialester.com.br/` | `/parcerias/` (um dos parceiros/ferramentas listadas) | **404** | Quebrado — CLIENTE |
| `https://imidiatv.com.br/` | `/parcerias/` | **401 Unauthorized** | Suspeito/obsoleto — CLIENTE |
| `https://portalenfse.com.br/Login/Login` | `/parcerias/` | **404** | Quebrado — CLIENTE |
| `https://app.meuatendimento.chat/` | `/parcerias/` | **Falha de DNS** (domínio não resolve) | Quebrado — CLIENTE |
| `https://servicos.crcms.org.br/spwms/STRT/login.aspx` | `/parcerias/` | **Timeout de conexão** (5s) | Suspeito — CLIENTE (servidor pode estar apenas lento/instável; não conclusivo) |
| `https://www.soldamaq.com.br/` | `/depoimentos/` (site do cliente Soldamaq) | **403 Forbidden** | Suspeito — provavelmente bloqueio de bot/WAF ao `curl`, não necessariamente quebrado para um navegador real; recomenda-se checagem manual antes de reclassificar como quebrado |
| `https://www.nfe.fazenda.gov.br/portal/consultaRecaptcha.aspx?...` | `/parcerias/` | **Excesso de redirecionamentos** (>5) | Suspeito — provável fluxo de redirecionamento dependente de sessão/cookies que o `curl` sem navegador não completa; portal do governo, não indicativo de link morto |

**Todos os outros ~90 links externos (WhatsApp, e-mail, Google Maps, redes sociais dos
depoimentos, portais de login de parceiros/ferramentas, recrutamento, fontes jornalísticas dos
posts, compartilhamento social) responderam 200/302 normalmente.** Nenhuma autenticação foi
tentada, nenhum formulário foi enviado, nenhum teste de invasão foi realizado — só a resposta HTTP
inicial de cada destino foi observada, exatamente como exigido.

### Números de telefone/WhatsApp encontrados no HTML final (classificação completa)

| Número | Onde aparece | Classificação |
|---|---|---|
| `5567992616117` | Topbar + botão flutuante, 13 páginas | Global canônico (`config/company.php['whatsapp_principal']`) |
| `5567992324097` | Diretório de departamentos, Fale Conosco (Comercial) | Departamento válido |
| `556733137300/301/302/304` | Diretório de departamentos, Fale Conosco (Central/Pessoal/Fiscal/Contábil) | Departamento válido |
| `5567991103140` | Ouvidoria (canal próprio) | Ouvidoria válido |
| `5567992041134` (o número divergente do template WordPress dos posts) | **Não encontrado em nenhuma das 13 páginas reconstruídas** | Confirma que a unificação de globais nos posts (já registrada em `blog-posts-final-validation.md`) continua correta — nenhuma regressão |

Nenhum número inesperado/divergente novo foi encontrado.

---

## 6 e 7. Header/Topbar/Footer globais

Os 39 stylesheets, 12 scripts e os componentes de header/footer são os MESMOS arquivos physical
em todas as 13 páginas (confirmado via a lista de scripts/estilos por página do crawl — nenhuma
página carrega uma cópia própria de `header.css`/`footer.css`/`variables.css`; todas usam
exatamente `/assets/css/header.css`, `/assets/css/footer.css`, `/assets/css/variables.css`,
`/assets/css/cookie-banner.css`, `/assets/css/whatsapp-button.css`, `/assets/css/fonts.css`,
`/assets/css/reset.css` — 13/13 cada). Isso é estruturalmente impossível de divergir por página,
diferente do site original (onde cada página tinha uma cópia independente do Elementor) — a
própria arquitetura elimina a classe de defeito que gerou `global-data-conflicts.md`.

**Confirmado ausente no HTML final**: `(67) 99204-1134` e `w.app/AgenciaLester` (valores
divergentes do template WordPress antigo dos posts) — zero ocorrências nas 13 páginas.

---

## 8, 9, 10 — Bottom bar / WhatsApp flutuante / Cookie banner

- **Bottom bar**: mesmo componente (`includes/footer.php`) nas 13 páginas — copyright calculado
  dinamicamente (`© Copyright 2026`, confirmado no HTML — nunca hardcoded "2024" como o original).
- **WhatsApp flutuante**: presente nas 13 páginas, usa o número canônico global
  (`5567992616117`) em todas, exceto a Ouvidoria, que **adicionalmente** expõe seu canal próprio
  (`5567991103140`) dentro do conteúdo da página — os dois coexistem sem conflito (são elementos
  visualmente e funcionalmente distintos: botão flutuante global vs. link de conteúdo específico
  da Ouvidoria) — mesma conclusão já registrada em `ouvidoria-final-validation.md`, reconfirmada
  aqui pela ausência de qualquer novo dado divergente.
- **Cookie banner**: mesmo componente/CSS/JS (`cookie-banner.css`/`cookie-banner.js`) carregado
  nas 13 páginas, incluindo os 3 posts (confirmado: nenhum banner diferente nos artigos). O
  comportamento interativo (aceite, persistência via `localStorage`, reaparecimento após limpeza)
  já foi validado ao vivo em `home-final-validation.md` e não foi re-testado interativamente nesta
  sessão (exigiria navegador) — sem motivo para suspeitar de regressão, já que nenhum arquivo do
  cookie banner foi tocado nas 2 últimas etapas de implementação.

---

## 11, 12 — `config/company.php` e pendências globais

### Mapa CHAVE → consumidor

| Chave | Consumido por |
|---|---|
| `razao_social` | topbar, footer, `<title>` (via padrão "— CT Price") |
| `whatsapp_principal` | topbar, botão flutuante (13 páginas) |
| `telefone_fixo` | topbar (13 páginas) |
| `endereco.*` | topbar, footer, mapa embutido (13 páginas) |
| `emails.*` | topbar, footer (13 páginas) |
| `responsavel_tecnico` | footer (13 páginas) |
| `departamentos.*` | Fale Conosco (diretório por departamento) |
| `ouvidoria.whatsapp` | Ouvidoria |
| `redes_sociais` | vazio — não consumido em lugar nenhum (nenhuma rede social institucional existe) |
| `sistemas_externos.recrutamento` | header (submenu Trabalhe Conosco), Trabalhe Conosco (CTA de candidatura) |
| `sistemas_externos.area_restrita_*` | Área Restrita (2 cards) |
| `sistemas_externos.agencia_desenvolvimento` | `null`, não consumido (crédito do footer é texto simples, sem link — ver seção 3) |
| `copyright_ano` | footer (13 páginas) |

**Nenhum dado empresarial hardcoded fora do config foi encontrado** nas 13 páginas — busca
consolidada pelos números de telefone/e-mail confirma que todos vêm de `config/company.php` ou de
`config/blog-posts.php`/conteúdo editorial legítimo (categoria A, não B/D).

### Pendências de dados globais — revalidadas

- **Bairro/CEP**: `config/company.php['endereco']['bairro']` e `['cep']` continuam `null` —
  divergência Monte Castelo/79.010-190 vs. Vila Rosa Pires/79002-400 **não resolvida**, conforme
  esperado. **CLIENTE.**
- **Área Restrita**: `area_restrita_clientes`/`area_restrita_colaboradores` continuam `null` —
  confirmado no arquivo atual, sem teste temporário repetido (já documentado suficientemente em
  `arearestrita-final-validation.md`, nenhuma mudança global desde então afeta esse fluxo).
- **Redes sociais**: `config['redes_sociais'] = []` — nenhuma rede social institucional
  encontrada, estado inalterado.
- **`agencia_desenvolvimento`**: continua `null` — o teste desta auditoria confirmou
  `agencialester.com.br` retornando **404** (não mais timeout, como em auditorias anteriores) —
  atualização do estado, ainda **CLIENTE** (aguardando destino correto ou confirmação de que não
  há mais parceria).

---

## 13 e 14. Matriz de links internos (simplificada)

| Origem | Destino | Status |
|---|---|---|
| Menu principal (13 páginas) | 8 destinos institucionais + 2 submenus | Todos válidos, exceto "Vagas" (`#`, pendente — seção 4) |
| Footer (13 páginas) | 8 destinos (institucionais + `#beneficios`) | Todos válidos |
| Home → cards "Últimas notícias" | 3 posts locais | Válido (confirmado clique real em sessão anterior) |
| Informações → cards "Últimas notícias" | 3 posts locais | Válido |
| Cada post → 2 relacionados | Os outros 2 posts | Válido, sem redundância |
| Área Restrita → 2 cards | `sistemas_externos.area_restrita_*` (`null`) | Estado indisponível correto, sem link falso |
| Trabalhe Conosco → CTA candidatura | `sistemas_externos.recrutamento` | Válido (200, ver seção 5) |

**Nenhum CTA interno aponta para o WordPress antigo** — confirmado via ausência total de `/wp/`
no crawl.

---

## 15. Âncoras

`/trabalhe-conosco/#beneficios` é a única âncora real do site (usada no submenu "Benefícios" e no
footer, apontando para o mesmo destino em ambos — corrigido em relação ao original, que tinha 2
versões diferentes e ambas quebradas). O ID `beneficios` existe fisicamente em
`components/benefits-grid-section.php`, incluído por `trabalhe-conosco/index.php` — confirmado por
inspeção de código (`<section class="benefits-grid-section" id="beneficios">`). Nenhuma âncora
vazia ou inexistente foi encontrada no crawl das 13 páginas.

---

## 16. Fale Conosco — regressão funcional

Testado diretamente contra o endpoint (`fale-conosco/fale-conosco-action.php`), com sessão e
tokens reais extraídos do HTML (não simulados):

| Teste | Resultado |
|---|---|
| GET no endpoint | 405 lógico → redireciona `?status=error` (sem stack trace, sem dump) |
| POST sem CSRF | Rejeitado → `?status=error` |
| POST com honeypot preenchido | Rejeição SILENCIOSA confirmada → `?status=success` (mesmo comportamento visível de sucesso, mas sem processar o envio — confirmado no código que a rejeição ocorre ANTES de qualquer tentativa de e-mail) |
| POST legítimo completo | Aceito → `?status=success` |
| Reenvio imediato (mesma sessão) | Rate limit confirmado → `?status=rate_limited` |
| `mail()` real | Tentou conectar a `[::1]:1025` (Mailpit) e falhou — **Mailpit não estava em execução nesta sessão**, então a entrega real não pôde ser confirmada; mesmo assim o endpoint reportou `status=success` ao usuário — **isso é o comportamento já conhecido e documentado do `mail()` nativo no Windows** (retorna sucesso mesmo quando o MTA local falha) — não é uma regressão nova, é a limitação de ambiente já registrada em auditorias anteriores deste projeto |

**Não foi possível confirmar a entrega real via Mailpit nesta sessão** (serviço não estava
acessível em `127.0.0.1:8025`) — a bateria de segurança/validação do formulário, que é o que esta
etapa pede como regressão, está confirmada; a verificação de entrega de e-mail (corpo, Reply-To,
destinatário) já está registrada com evidência em `fale-conosco-final-validation.md` e não foi
reexecutada aqui.

---

## 17 e 18. Ouvidoria — regressão funcional e limites

| Teste | Resultado |
|---|---|
| CSRF | Confirmado — rejeição sem token válido |
| Honeypot | Confirmado — rejeição silenciosa (`?status=success` sem processar) |
| Rate limit (30s) | Confirmado — reenvio imediato → `?status=rate_limited` |
| Validação server-side (nome/contato/e-mail/empresa/mensagem) | Confirmado — submissão incompleta → `?status=invalid` com os campos certos listados |
| Envio sem anexo | Confirmado — aceito (`?status=success`) |
| 1 PDF válido | **Não confirmado nesta sessão** — ver nota abaixo |
| MIME falso rejeitado | **Não confirmado nesta sessão** — ver nota abaixo |
| Limpeza de temporário | Não observável sem upload bem-sucedido nesta sessão |

### Nota — limitação de ambiente para teste de upload (compatível com o já registrado)

Ao tentar simular upload de arquivo via `curl` (multipart), o PHP retornou consistentemente
`"File upload error - unable to create a temporary file"` — investigado e isolado como uma
particularidade do processo de servidor PHP em segundo plano desta sessão de auditoria (mesmo
apontando explicitamente `upload_tmp_dir` para um diretório local confirmadamente gravável, e
mesmo após reiniciar o processo do zero) — **não é um defeito do código do endpoint**: a lógica de
validação de MIME real via `finfo` (não a extensão/`type` do navegador) foi revisada diretamente
no código-fonte (`ouvidoria/ouvidoria-action.php`, função de validação de anexos) e está
implementada corretamente (compara contra `CTPRICE_OUVIDORIA_ALLOWED_MIMES`, rejeita qualquer MIME
fora de `application/pdf`/`image/jpeg`/`image/png`). Esta exata classe de peculiaridade
(`upload_tmp_dir` no Windows) já havia sido antecipada e documentada em
`ouvidoria-final-validation.md`, que testou isso com sucesso via navegador real (upload de fato
funcionando, PDF válido aceito, MIME falso rejeitado, temporários limpos) — esta auditoria não
encontrou nenhuma mudança de código desde então que pudesse ter quebrado essa lógica (o arquivo
`ouvidoria/ouvidoria-action.php` não foi tocado por nenhuma das duas últimas implementações). Os
limites já aprovados (3 arquivos, 5MB por arquivo, 10MB total, PDF/JPEG/PNG, detecção de
`post_max_size`) permanecem no código, confirmados por leitura direta — não redesenhados.

**Mailpit foi verificado como indisponível nesta sessão** (mesma situação do item 16) — nenhuma
purga foi necessária porque nenhum e-mail de teste chegou a ser enfileirado com sucesso.

---

## 19. Área Restrita

Confirmado no estado atual do arquivo: `config/company.php['sistemas_externos']
['area_restrita_clientes']` e `['area_restrita_colaboradores']` continuam `null`. Crawl do HTML
renderizado da página: **zero** ocorrência de `/documentos` ou `/sh-admin`, **zero** `<a>` dentro
da grade de acessos (confirmado: os 2 cards mostram o estado "Acesso temporariamente
indisponível"). Nenhum novo teste temporário de config foi feito (já documentado suficientemente
em `arearestrita-final-validation.md`; nenhuma mudança global desde então afeta esse componente).

---

## 20–24. Componentes interativos e páginas específicas

**Não re-executados interativamente nesta sessão** (exigem navegador — Depoimentos/lightbox,
carrossel de clientes, Parceiros/Trabalhe Conosco não têm interações que dependam de mudanças
recentes). Reconfirmado apenas o que é verificável sem navegador:

- **Depoimentos**: 7 cards confirmados presentes no HTML (crawl de imagens
  `assets/images/pages/depoimentos/` — 7 fotos + 7 thumbnails, igual ao já validado); nenhum
  iframe do YouTube presente no HTML inicial (confirmado: os 13 iframes encontrados no crawl são
  todos o mesmo Google Maps, nenhum `youtube`) — confirma que o carregamento sob demanda continua
  intacto. Link do site de Walter Ferreira Cruz continua ausente (só Instagram) — confirmado via
  contagem de links de `instagram.com`/`soldamaq.com.br` etc. já listada na seção 5.
- **Clientes/Sobre Nós**: `assets/js/clients-carousel-init.js` carregado nas 3 páginas esperadas
  (Home, Informações, Sobre Nós) — confirma que o componente `.logo-card` não foi afetado por
  nenhuma mudança recente (nenhum arquivo do carrossel foi tocado nas 2 últimas implementações).
  `assets/js/clients-grid-lightbox.js` carregado só em `/clientes/`, como esperado.
- **Parceiros**: `logo-card.css` carregado (compartilhado), grande volume de imagens em
  `assets/images/partners/` confirmado sem 404 (parte do check de 202 assets).
- **Trabalhe Conosco**: `jobs-section.css`/`benefits-grid-section.css` carregados corretamente
  só nessa página; 14 imagens de benefícios confirmadas sem 404.
- **Blog**: ver seção 5/6 — Home/Informações/relacionados confirmados apontando só para caminhos
  locais, zero `/wp/`, canonical e compartilhamento já revalidados na etapa anterior.

Nenhuma mudança de código nessas áreas desde as respectivas validações finais — risco de
regressão avaliado como baixo, mas **não pode ser chamado de "reconfirmado visualmente" nesta
sessão** por honestidade metodológica.

---

## 25, 26, 27 — SEO técnico básico

### Títulos (`<title>`)
13/13 páginas com `<title>` presente e único (nenhum duplicado, nenhum título padrão/genérico
"Untitled"/"Document"). Sufixo "— CT Price" confirmado nas páginas verificadas diretamente nesta
sessão (posts) e já registrado nas validações finais das institucionais.

### Meta description
Os 3 posts têm meta description = excerpt (confirmado). As 10 páginas institucionais: **não
verificado nesta sessão se todas têm meta description própria** — recomenda-se checagem explícita
numa próxima etapa (registrado como item a confirmar, não como defeito confirmado).

### Canonical
Os 3 posts têm exatamente 1 `<link rel="canonical">` cada, absoluto, imune a Host Header forjado
(ver seção 28). **As 10 páginas institucionais não possuem `rel="canonical"`** (confirmado:
nenhuma delas usa `ctprice_absolute_url()` nem define a tag) — registrado como ausência, não
implementado nesta etapa, conforme instruído.

---

## 28. Host Header — regressão da correção recente

Reconfirmado nesta sessão: com `Host: evil.com` forjado contra `/hello-world/` ao vivo, o
`canonical` e as 4 URLs de compartilhamento continuam corretamente revertendo para
`ctprice.com.br` (não refletem o host forjado). `grep` por `ctprice_absolute_url(` no projeto
confirma que **somente** `blog/_post-template.php` e `components/article-content-section.php`
(via o array `share`) usam essa função — nenhuma outra página/componente a chama, então **não há
nenhuma outra superfície afetada** a testar.

---

## 29 e 30. Headings e landmarks

| Página | `<main>` | Observação de heading |
|---|---|---|
| 3 posts | Presente (confirmado no código do template compartilhado) | H1 único + H2 "Mais notícias", sem salto — já corrigido e revalidado |
| 10 institucionais | Presente em todas (padrão já estabelecido desde a implementação da Home) | Hierarquia já auditada individualmente em cada `*-final-validation.md`; não re-auditada heading a heading nesta sessão |

Nenhuma inconsistência de landmark (`header`/`nav`/`main`/`footer`) foi identificada na revisão de
código dos templates compartilhados (`includes/*.php`, `blog/_post-template.php`) — todas as 13
páginas usam os mesmos includes globais.

---

## 31. Acessibilidade global

Não foi possível fazer uma varredura interativa (foco visível, contraste renderizado, navegação
por teclado) sem navegador nesta sessão. Os aspectos verificáveis por código/HTML estático foram
confirmados: `alt` presente e não vazio nos assets tocados pela última implementação (posts,
Área Restrita — já confirmados em suas validações finais); formulários com `<label for>`
associado (confirmado por inspeção de `components/contact-form-section.php`/
`ombudsman-form-section.php`); nenhum `<div role="button">` novo foi introduzido pela
implementação atual (o único caso já documentado — os antigos botões de compartilhamento do
WordPress — foi propositalmente substituído por `<a>` reais nos posts). Recomenda-se uma
varredura interativa completa (WCAG prática, não certificação) quando o navegador estiver
disponível novamente, antes do go-live definitivo.

---

## 32–35. Assets, CSS, JS, fontes

- **Imagens**: 190 únicas no HTML renderizado, **0 quebradas** (todas 200). Nenhuma duplicação
  física óbvia identificada além do reuso intencional já documentado (thumbnails do blog
  reaproveitadas entre Home/Informações/relacionados).
- **CSS**: 39 stylesheets únicos, **0 404**. Nenhum CSS carregado por uma página que
  claramente não precisa dele foi identificado na revisão (cada página carrega só os arquivos
  correspondentes aos seus próprios componentes, confirmado pela distribuição por página do
  crawl).
- **JS**: 12 scripts únicos, **0 404**, **0 jQuery**, **0 Elementor**, cada um carregado só nas
  páginas que o usam (`contact-form.js` só em Fale Conosco, `ouvidoria-form.js` só em Ouvidoria,
  `video-testimonials-lightbox.js` só em Depoimentos, `clients-grid-lightbox.js` só em Clientes
  etc.) — nenhum script global desnecessário encontrado.
- **Fontes**: 7 arquivos `.woff2` esperados, todos presentes e servidos com 200, hospedagem local
  confirmada (nenhuma chamada a `fonts.googleapis.com`/`fonts.gstatic.com` no HTML das 13
  páginas).

---

## 36. Performance superficial (observação, não otimização)

Nenhum iframe/vídeo carregado antecipadamente fora do já documentado e aprovado (o único iframe
em todas as páginas é o mapa do footer, sempre presente — comportamento já aceito globalmente;
nenhum vídeo do YouTube nos Depoimentos carrega antes do clique, confirmado). Nenhuma
duplicação de asset identificada além do reuso intencional. Lighthouse/ferramentas de performance
não foram executadas nesta sessão (fora do escopo desta etapa e dependente de navegador).

---

## 37 e 38. Responsividade global e breakpoint 767/768

**Não re-executado com smoke test visual nesta sessão** (exige navegador). Todas as 13 páginas já
têm responsividade validada e aprovada em suas respectivas validações finais, incluindo o
breakpoint 767/768 em cada componente que depende dele (grids, Depoimentos, Área Restrita,
Trabalhe Conosco/Benefícios, artigos). Nenhuma mudança de CSS foi feita em nenhum componente
institucional desde essas validações — o único CSS novo desde então é `assets/css/article.css`
(dos posts), já validado nos 5 viewports em `blog-posts-final-validation.md`. Recomenda-se
reconfirmação visual quando o navegador estiver disponível, mas não há indício de regressão.

---

## 39. 404 / páginas inexistentes

**Achado real e importante**: com o servidor de desenvolvimento embutido do PHP (`php -S`, sem
script de roteamento), uma URL claramente inexistente (`/completely-bogus-path-abc-999/`) retorna
**HTTP 200 e renderiza a Home** — isso é o comportamento PADRÃO do `php -S` sem um router.php (ele
tenta servir `index.php` do docroot para qualquer caminho que não seja um arquivo real), **não**
uma característica do código do projeto. Um arquivo estático inexistente
(`/assets/images/nao-existe.jpg`) corretamente retorna 404 puro do próprio servidor embutido — a
diferença confirma que é um comportamento de roteamento do servidor de teste, não do PHP da
aplicação.

**O que É um achado real, independente do servidor**: **o projeto não tem nenhuma página 404
customizada** (nenhum arquivo `404.php`, nenhuma regra de erro configurada) — o site original
(`site-inventory.md`) confirma que o WordPress tinha um 404 estilizado do próprio tema
("Página não encontrada"). A reconstrução **ainda não tem um equivalente**. Isso só pode ser
verificado de fato no servidor de produção real (Apache/LiteSpeed), que decidirá o comportamento
para uma URL/diretório verdadeiramente inexistente — mas o projeto precisa, de qualquer forma, de
uma página 404 própria antes do go-live. **P1.**

---

## 40. URLs com e sem barra final

Testado em páginas institucionais e nos 3 posts: `/sobre-nos` e `/sobre-nos/`, `/hello-world` e
`/hello-world/` — **ambas retornam 200 com o conteúdo correto**, sem redirecionamento, no servidor
de desenvolvimento (PHP resolve `diretorio/index.php` para as duas formas). **Atenção para
produção**: um Apache real com `mod_dir` (comportamento padrão) tipicamente insere um
redirecionamento 301 automático de `/sobre-nos` → `/sobre-nos/` — isso é esperado e desejável para
SEO (evita conteúdo duplicado), mas precisa ser confirmado no ambiente de produção real antes do
go-live, já que o servidor de desenvolvimento não reproduz esse comportamento.

---

## 41 e 42. Plano de redirecionamentos 301 (recomendação, não implementado)

Baseado em `docs/architecture-proposal.md` (seção 10/11, já detalhado) e `site-inventory.md`:

### A. Obrigatórios (mapeamento direto, mesma slug sem `/wp/`)
Todas as 13 URLs: `/wp/` → `/`, `/wp/sobre-nos/` → `/sobre-nos/`, ... até
`/wp/hello-world/` → `/hello-world/` (tabela completa já existe em
`docs/architecture-proposal.md`, seção 11 — não duplicada aqui).

### B. Recomendados
- `/wp/home/` → `/` (alias do WordPress, nunca foi uma página própria).
- Normalizar `http://` → `https://` (o item de menu "Ouvidoria" do site antigo usava `http://`
  sem "s").

### C. URLs quebradas antigas que NÃO devem ser redirecionadas sem decisão do cliente
- `ctprice.com.br/contato` (404 no original, CTA da Home) — não criar um redirect "inventando"
  um destino; decisão pendente se deve virar `/fale-conosco/` ou permanecer quebrado até definição.
- `ctprice.com.br/documentos`, `ctprice.com.br/sh-admin` — destinos da Área Restrita, ambos
  quebrados no original; não redirecionar até a CT Price fornecer os destinos reais.

### D. Aliases irrelevantes
- `/wp/blog/` (nunca existiu como página, 404 no original) — não precisa de redirect.

Nenhuma alteração de `.htaccess` foi feita nesta etapa — apenas o plano.

---

## 43. Arquivos de servidor

**Nenhum `.htaccess` existe na raiz do projeto.** Nenhuma configuração Apache/LiteSpeed própria
foi encontrada. Isso é consistente com o estágio atual (pré-deploy) — mas significa que **nenhuma
das proteções mencionadas nas seções 41/44/46 (redirects, bloqueio de `.git`/dotfiles, cabeçalhos
de segurança) existe ainda em lugar nenhum** — tudo depende de configuração a ser criada no
momento do deploy real.

---

## 44 e 45. Segurança superficial — **ACHADO P0 CONFIRMADO**

### 🔴 P0 — `.git/` e documentação interna servidos diretamente por HTTP

Testado diretamente contra o servidor local (que serve a raiz do repositório como document root,
o mesmo modelo que uma cópia ingênua da raiz do repo para produção teria):

| Caminho testado | Resultado |
|---|---|
| `/.git/config` | **200 — conteúdo real do arquivo servido**, incluindo a URL do repositório remoto (`github.com/traxterdev/ctprice.git`) |
| `/CLAUDE.md` | **200 — conteúdo real servido** (instruções internas do projeto) |
| `/README.md` | **200 — servido** |
| `/docs/reference/blog-posts-audit.md` | **200 — servido** (e, por extensão, toda a documentação de auditoria/decisões internas em `docs/`) |
| `/docs/architecture-proposal.md` | **200 — servido** |
| `/docs/reference/screenshots/*.png` | **200 — servido** (screenshots de referência/auditoria) |
| `/.gitignore` | **200 — servido** |
| `/.env` | 200, mas cai no fallback da Home — **o arquivo não existe fisicamente** (nunca foi criado neste projeto), então não há segredo a vazar aqui — mas confirma que, SE existisse, seria igualmente exposto |
| `/composer.json` (arquivo que não existe) | 200, cai no fallback da Home — confirma que a exposição é real só para arquivos que de fato existem em disco, não uma falha de todo caminho |

**Causa raiz**: não há nenhuma regra de servidor (`.htaccess`/config Apache/LiteSpeed) bloqueando
acesso a `.git/`, arquivos de documentação (`docs/`, `*.md`) ou qualquer arquivo fora de
`assets/`/páginas públicas. Qualquer arquivo que exista fisicamente sob a raiz do docroot é
servido como está.

**Impacto real se a raiz do repositório for copiada como está para o document root de
produção**: exposição completa do histórico do Git (via ferramentas como `git-dumper`, que só
precisam de `.git/config` acessível para reconstruir o repositório inteiro), de toda a
documentação interna de auditoria/decisões de negócio, e de qualquer arquivo futuro que venha a
ser adicionado à raiz sem proteção explícita.

**Classificação: P0 — bloqueador.** Antes de qualquer publicação real, é obrigatório:
1. Nunca fazer deploy copiando a raiz do repositório Git diretamente para o document root — ou
2. Adicionar bloqueio explícito no servidor de produção (`.htaccess`/config equivalente) negando
   acesso a `.git/`, `.claude/`, `docs/`, `*.md`, `.gitignore`, e qualquer diretório/arquivo que
   não seja intencionalmente público (`assets/`, as páginas de conteúdo e seus `*-action.php`).

Ambas as abordagens são recomendadas em conjunto (defesa em profundidade), não uma ou outra.

### Endpoints de formulário — headers e comportamento
`X-Powered-By: PHP/8.4.12` está presente nas respostas (exposição da versão exata do PHP) —
comportamento padrão do PHP quando `expose_php` não é desativado no `php.ini`; **P2**,
recomendação: `expose_php = Off` no `php.ini` de produção. Nenhuma outra informação sensível nos
headers (sem stack trace, sem caminho de servidor, sem detalhe de exceção) — confirmado nas
respostas de erro de ambos os formulários.

### Métodos HTTP
GET nos 2 endpoints de ação (`fale-conosco-action.php`, `ouvidoria-action.php`) é corretamente
rejeitado (redireciona para `?status=error`, nunca processa como se fosse POST). Nenhuma listagem
de diretório foi encontrada em `assets/` (404 puro do servidor ao tentar listar).

---

## 46. Risco de arquivos internos no deploy

Diretamente ligado ao achado P0 da seção 44: **`docs/reference/` (todas as auditorias,
screenshots, relatórios de validação) e a documentação de arquitetura NÃO devem fazer parte do
document root de produção.** Confirmado nesta sessão que, se copiados como estão, ficam
publicamente acessíveis (seção 44). Recomendação: o processo de deploy deve copiar/publicar
SOMENTE os diretórios/arquivos realmente públicos (`assets/`, `components/`, `config/`,
`includes/`, `blog/`, `content/`, e os diretórios de página com seus `index.php`), nunca
`docs/`, `.claude/`, `CLAUDE.md`, `README.md`, `.git/`, ou qualquer arquivo de trabalho interno —
seja por exclusão explícita no script de deploy, seja por bloqueio de servidor (seção 44), ou
(preferencialmente) ambos.

---

## 47. Git / arquivos indesejados

`git status --short` no estado final: **limpo** (nenhuma alteração pendente desta auditoria).
Um arquivo órfão foi encontrado no disco (não rastreado pelo Git, corretamente coberto pela regra
`*.log` do `.gitignore`, portanto nunca seria commitado): `assets/fonts/php_server.log` (resíduo
de uma sessão de trabalho anterior, datado de 25/08). Não representa risco de vazamento via Git,
mas **representa exatamente o risco da seção 46** se o deploy copiar a pasta de trabalho inteira
em vez de fazer um `git archive`/checkout limpo — outra evidência concreta a favor da recomendação
daquela seção. Nenhum arquivo foi removido nesta etapa (fora do escopo de auditoria).

---

## 48. Dados sensíveis

Busca por padrões de senha/token/API key/secret/credencial/chave privada em todos os arquivos
`.php` do projeto: **nenhum segredo real encontrado** — as únicas ocorrências de "token" são o
mecanismo legítimo de CSRF (`random_bytes(32)`, gerado em runtime, nunca hardcoded) já usado em
Fale Conosco e Ouvidoria. Busca por padrões de chave de API conhecidos (Google, AWS, GitHub,
Slack, OpenAI): **zero ocorrências** em qualquer arquivo `.php` do projeto (a chave do Google Maps
vista em requisições de rede durante auditorias anteriores pertence ao site de referência ao vivo,
não ao código deste projeto).

---

## 49. Pendências consolidadas da CT Price

1. **Endereço**: bairro ("Monte Castelo" vs. "Vila Rosa Pires") e CEP ("79.010-190" vs.
   "79002-400") — qual é o correto?
2. **Área Restrita**: URL correta do portal de Clientes; URL correta do portal de Colaboradores;
   manter ou remover os 2 cards caso os sistemas tenham sido descontinuados; futuro do botão
   "Área Restrita" no header após a modernização.
3. **Menu "Trabalhe Conosco"/"Vagas"**: destino único (sistema externo de recrutamento vs. página
   institucional) — hoje divergente entre header/footer no original, e "Vagas" está com `url`
   nula na reconstrução aguardando essa decisão.
4. **Link "Fale Conosco" da Home** (seção "Nossos Serviços"): no original aponta para
   `ctprice.com.br/contato` (404) — decisão pendente sobre corrigir para `/fale-conosco/` ou
   manter fiel ao original quebrado.
5. **Ouvidoria**: confirmação de que a política de anonimato/retenção/LGPD atual (nenhuma
   funcionalidade de anonimato foi inventada, nenhum texto legal foi criado) está de acordo com o
   que a CT Price deseja publicar.
6. **Depoimentos**: site pessoal/comercial de Walter Ferreira Cruz, se existir (hoje só o
   Instagram é exibido, por decisão consciente de auditoria anterior).
7. **Parceiros/ferramentas com link quebrado ou suspeito** (seção 5 desta auditoria):
   `agencialester.com.br` (404), `imidiatv.com.br` (401), `portalenfse.com.br/Login/Login` (404),
   `app.meuatendimento.chat` (DNS não resolve), `servicos.crcms.org.br/spwms/...` (timeout) —
   confirmar se essas parcerias/ferramentas continuam ativas e, se sim, qual é a URL correta.
8. **Redes sociais institucionais**: confirmado, nesta e em auditorias anteriores, que a CT Price
   não possui nenhuma rede social oficial própria — apenas confirmar que isso continua correto.
9. **`agencia_desenvolvimento`**: confirmar se a Agência Lester continua sendo a
   desenvolvedora/manutenedora de referência e, se sim, qual é a URL correta (o domínio atual
   retorna 404).

**Nenhuma pendência já resolvida foi repetida aqui** — a lista acima reflete só o que continua
genuinamente em aberto.

---

## 50. Itens reservados para futuro CMS (não pertencem à reconstrução pública atual)

- Painel administrativo para editar posts do blog, vagas, benefícios, depoimentos, logos de
  clientes/parceiros.
- Gestão de mídia (upload de imagens fora do fluxo de código).
- Qualquer funcionalidade de autenticação real para a Área Restrita.
- Automação de redirects/URLs quando novos posts forem publicados.
- SEO avançado (Open Graph, JSON-LD) — propositalmente adiado, conforme já decidido nas etapas de
  implementação dos posts.

---

## Classificação consolidada dos achados

### 🔴 P0 — bloqueador
1. **`.git/` e documentação interna (`docs/`, `CLAUDE.md`, `README.md`) servidos diretamente por
   HTTP** quando a raiz do projeto é usada como document root — risco real de exposição completa
   do histórico do repositório e de documentação interna de negócio. (Seções 44/46/47)

### 🟠 P1 — importante antes do go-live
2. **Formulário de contato da Home ("Quer receber um contato?") não tem backend** — `<form
   class="contact-form">` sem `action`/`method`, sem JS de interceptação (diferente de Fale
   Conosco, que tem `contact-form.js` e um endpoint real) — qualquer envio por esse formulário é
   silenciosamente perdido (recarrega a Home com os dados na query string, sem processar nada).
   (Achado novo desta auditoria, seção 3/16 — nenhuma auditoria anterior havia testado
   especificamente este ponto; `home-final-validation.md` já registrava o envio do formulário como
   "fora do escopo" daquela validação.)
3. **Nenhuma página 404 customizada existe no projeto** — o site original tinha uma; a
   reconstrução ainda não. (Seção 39)
4. **Ausência de `.htaccess`/qualquer configuração de servidor de produção** — nenhum dos
   redirecionamentos 301 planejados, bloqueios de segurança ou normalização HTTP→HTTPS existe
   ainda em lugar nenhum. (Seções 41–43)
5. **Canonical ausente nas 10 páginas institucionais** (só os 3 posts têm). (Seção 27)

### 🟡 P2 — melhoria recomendada
6. `X-Powered-By: PHP/8.4.12` exposto nos headers — recomenda-se `expose_php = Off` em produção.
7. Confirmar meta description em todas as 10 páginas institucionais (não verificado
   individualmente nesta sessão).
8. Link "Vagas" do submenu usa `href="#"` (navegação sem efeito) enquanto o destino não é
   definido — comportamento aceitável temporariamente, mas o ideal é resolver a pendência de
   dados (item CLIENTE #3) o quanto antes.
9. Resíduo `assets/fonts/php_server.log` (não rastreado, mas presente em disco) — limpar antes de
   qualquer cópia de diretório para produção.

### 🔵 CLIENTE
Ver seção 49 (lista consolidada e completa).

### ⚪ FUTURO CMS
Ver seção 50.

---

## 51. Recomendação de prontidão para go-live

**Não está pronto para publicação em produção ainda**, especificamente por causa do **P0** (seção
44) — que é resolvido com uma mudança de processo de deploy/configuração de servidor, não de
código da aplicação, e portanto não deveria atrasar significativamente o cronograma. Os demais
P1 são pontuais e bem delimitados (1 formulário sem backend, 1 página 404 a criar, 1 arquivo de
configuração de servidor a escrever, canonical a adicionar em 10 páginas). Nenhum P0/P1 encontrado
está relacionado a conteúdo, fidelidade visual ou dados institucionais — toda a reconstrução de
conteúdo/layout já validada anteriormente permanece íntegra, sem nenhuma regressão detectada.

**Recomendação**: tratar o P0 e os 4 P1 numa rodada de correção focada (escopo pequeno e bem
definido) antes de considerar o go-live; nesse momento, também vale reservar uma janela com o
Chrome DevTools MCP disponível para uma validação visual/interativa final completa das 13 páginas
em conjunto, já que esta auditoria não pôde reexecutá-la por indisponibilidade de ferramenta.
Depois disso, a abertura da fase de CMS pode prosseguir normalmente.

---

## Arquivos criados nesta etapa

- `docs/reference/global-final-audit.md` (este documento)

Nenhum outro arquivo foi criado ou alterado (todos os arquivos de diagnóstico temporários usados
durante a auditoria foram removidos ao final da sessão).
