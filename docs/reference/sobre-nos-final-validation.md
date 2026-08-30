# Validação final full-page — `/sobre-nos/` ("A CT Price")

Data: 2026-08-30
Referência: https://ctprice.com.br/wp/sobre-nos/
Implementação: `/sobre-nos/` local (PHP 8+, sem WordPress/Elementor/jQuery/Font Awesome)
Documentação-base: `docs/reference/sobre-nos-audit.md`, `docs/reference/reference-baseline.md`

Escopo desta validação: comparação **full-page** (não por componente isolado) nos três
viewports obrigatórios do projeto (1440×900, 900×1200, 390×844), incluindo interações,
console/rede e ausência de dependências legadas. Nenhuma seção nova foi implementada aqui —
apenas medição, uma correção de defeito comprovado (contorno dos cards de Missão/Visão/Valores)
e registro de diferenças já aprovadas/pendentes.

---

## 1. Estrutura confirmada (ordem no DOM)

topbar → header → Hero interno → História → Missão/Visão/Valores → Dedicação → Carrossel de
clientes → footer → bottom bar → WhatsApp flutuante → cookie banner. Ordem idêntica à
referência nos três viewports.

---

## 2. Medições — 1440×900

| Seção | Y original | Y local | Altura original | Altura local | Diferença |
|---|---|---|---|---|---|
| Topbar | 0 | 0 | 66 | 66 | 0 |
| Header | 66 | 66 | 132 | 132 | 0 |
| Hero interno | 198 | 198 | 640 | 640 | 0 |
| História | 873 | 873 | 524,78125 | 524,78125 | 0 |
| Missão/Visão/Valores | 1447,78125 | 1447,78125 | 445,59375 | 445,59375 | 0 (após correção — ver §6) |
| Dedicação | 1943,375 | 1943,375 | 457,375 | 457,375 | 0 |
| Carrossel de clientes | 2400,75 | 2400,75 | 200 | 200 | 0 |
| Footer + bottom bar | 2600,75 | 2600,75 | 478,390625 (400+78,390625) | 478,390625 | 0 |
| **Altura total da página** | **3079,140625** | **3079,140625** | — | — | **0** |

Container: 1140px em História/MVV/Dedicação, 1200px no carrossel — confirmado idêntico.
`scrollWidth === clientWidth` (1425=1425) nos dois. Sem overflow.

## 3. Medições — 900×1200

| Seção | Y original | Y local | Altura original | Altura local | Diferença |
|---|---|---|---|---|---|
| Topbar | 0 | 0 | 85 | 110 | +25 (ver §7.1 — global, fora de escopo) |
| Header | 85 | 110 | 83 | 73 | -10 (mesmo caso — compensa parcialmente) |
| Hero interno | 168 | 183 | 640 | 640 | 0 |
| História | 843 | 858 | 668,78125 | 668,78125 | 0 |
| Missão/Visão/Valores | 1561,78125 | 1576,78125 | 469,59375 | 469,59375 | 0 |
| Dedicação | 2081,375 | 2096,375 | 429,78125 | 429,78125 | 0 |
| Carrossel de clientes | 2511,15625 | 2526,15625 | 475,0625 | 200 | -275,0625 (correção já aprovada — ver §7.2) |
| Footer | 2986,21875 | — | 400 | — | — |
| Bottom bar | 3386,21875 | — | 78,390625 | — | — |

`scrollWidth === clientWidth` = 885/885 nos dois. Sem overflow. Todas as alturas de conteúdo
(hero, história, MVV, dedicação) batem exatamente, sub-pixel incluído — a única fonte real de
diferença de posição Y é o offset acumulado do bloco topbar+header (ver §7.1), e a única
diferença de altura de seção é o carrossel (correção intencional já aprovada, §7.2).

## 4. Medições — 390×844

| Seção | Y original | Y local | Altura original | Altura local | Diferença |
|---|---|---|---|---|---|
| Topbar | 0 | 0 | 176 | 132 | -44 (global, fora de escopo) |
| Header | 176 | 132 | 224,625 | 249,6875 | +25,0625 (idem) |
| Hero interno | 400,625 | 381,6875 | 640 | 640 | 0 |
| História | 1075,625 | 1056,6875 | 994,109375 | 994,109375 | 0 |
| Missão/Visão/Valores | 2119,734375 | 2100,796875 | 1154,78125 | 1154,78125 | 0 (após correção) |
| Dedicação | 3324,515625 | 3305,578125 | 744,921875 | 744,921875 | 0 |
| Carrossel de clientes | 4069,4375 | 4050,5 | 410,0625 | 200 | -210,0625 (correção já aprovada) |
| Footer (main) | — | — | 711,875 | 684,578125 | -27,296875 (bairro/CEP pendente, §7.3) |
| Bottom bar | 5191,375 | — | 160,78125 | 160,78125 | 0 |

`scrollWidth === clientWidth` local = 390/390 (sem overflow). Na referência, `scrollWidth`
(407) > `clientWidth` (390) — 17px de overflow **do próprio site original**, causado pelo
defeito conhecido do Hero interno mobile (`margin-left` fixo de 200px, sem breakpoint) — a
implementação local já corrige esse defeito (ver §7.4) e por isso não reproduz o overflow.

**Nota metodológica**: a primeira leitura da altura da Dedicação em 390px indicou um valor
incorreto (497,78125 em vez de 744,921875) porque a imagem `01-1024x684.jpg` ainda não tinha
terminado de carregar (`loading="lazy"`, `img.complete === false` no momento da medição). Após
rolar a página (ou aguardar o carregamento) o valor real bate exatamente com a referência — não
é um defeito de código, é um artefato de medição já identificado e corrigido nesta mesma sessão.

---

## 5. Regressões encontradas e corrigidas

### 5.1 Cards de Missão/Visão/Valores sem contorno (REGRESSÃO CONFIRMADA E CORRIGIDA)

A auditoria original (`sobre-nos-audit.md`) registrou os cards da seção Missão/Visão/Valores
como "planos, sem borda/cartão". A reinspeção direta desta validação full-page (DevTools →
`getComputedStyle` no `.elementor-widget-container` de cada icon-box) mostrou que os cards **têm
sim** um contorno fino: `border: 1px solid rgb(5, 112, 56)` (`#057038`, `--color-brand-green`)
com `border-radius: 15px`. Isso não estava implementado (`components/flat-icon-box-section.php`
/ `assets/css/flat-icon-box-section.css`), causando uma diferença medida de exatamente 2px na
altura da seção e, mais importante, uma diferença VISUAL real (sem o contorno o card fica
"solto" sobre o fundo branco).

**Correção aplicada**: adicionado `border: 1px solid var(--color-brand-green); border-radius:
15px;` à classe `.flat-icon-box` em `assets/css/flat-icon-box-section.css`, e atualizado o
comentário de documentação em ambos os arquivos (CSS e componente) para refletir o padrão
correto ("com contorno" em vez de "sem borda/cartão"). Após a correção, a altura da seção MVV
bate exatamente com a referência nos três viewports (ver tabelas acima).

Isso é registrado como correção de erro de documentação/implementação anterior, não como
`REFERENCE DRIFT` — o contorno sempre existiu no site original; a auditoria inicial é que não o
capturou.

### 5.2 CTA "Fale Conosco" da Dedicação com sublinhado indevido (REGRESSÃO CONFIRMADA E CORRIGIDA em tarefa anterior)

Já corrigido na tarefa de implementação da seção Dedicação (`text-decoration: underline` →
`none`, `text-align: center`); reconfirmado aqui como correto na validação full-page.

---

## 6. Diferenças intencionais já aprovadas (reconfirmadas, não tratadas como regressão)

- **Hero interno mobile**: correção responsiva já aplicada (`margin-left:0` em `max-width:767px`
  em vez do `margin-left:200px` fixo do original, que causa 17px de overflow horizontal na
  referência — não reproduzido aqui).
- **CTA da Dedicação**: `https://ctprice.com.br/contato` (404 no original) → `/fale-conosco/`
  (Categoria C).
- **3 logos 404** do carrossel (`mv.jpg`, `modelo.jpg`, `logo_0020_Camada16.jpg`) — não baixados
  nem reproduzidos, mesmos 82 logos válidos usados na Home via `config/clients.php`.
- **Logos sem distorção**: carrossel usa altura fixa (200px de seção, 70px de slide) com
  `object-fit:contain`, em vez do `object-fit:fill` do original (que produz logos gigantes e
  esticados em larguras menores — confirmado diretamente nesta validação: a seção do carrossel
  no original chega a 475px de altura em 900px de largura e ~410px em 390px, com imagens
  individuais de até 334px de altura, por escalarem só pela largura). Diferença de altura da
  seção nos viewports estreitos é o resultado esperado e aprovado dessa correção.
- **WhatsApp canônico unificado**: número `(67) 99261-6117` usado de forma consistente na
  topbar, botão flutuante e footer, em vez de variar por página como no original.
- **Bairro/CEP do endereço**: pendente (endereço local não inclui "Monte Castelo" / "CEP:
  79.010-190"), causando o footer ser ~27px mais baixo que a referência em 390px (o restante do
  footer — logo, menu, mapa — bate exatamente). Aguardando confirmação do dado definitivo pelo
  cliente.

Nenhuma dessas diferenças foi alterada nesta validação.

---

## 7. Itens fora de escopo (pendências não bloqueantes, não corrigidos)

Os itens abaixo pertencem a **componentes globais** (`includes/topbar.php`,
`includes/header.php`) já implementados e aprovados durante a reconstrução da Home, e afetam
igualmente a Home e o Sobre Nós (confirmado por comparação direta entre as duas páginas
locais). Corrigi-los está fora do escopo desta tarefa (validação apenas da página "A CT Price")
e arriscaria alterar um componente global sem essa ser a tarefa solicitada — registrados aqui
para decisão futura, não corrigidos:

1. **Altura combinada topbar+header** difere da referência em 900×1200 (183 vs 168, -15px) e em
   390×844 (381,6875 vs 400,625/576,625 — o texto da topbar/menu quebra em número de linhas
   diferente do original nesses larguras). Não é uma regressão desta tarefa: idêntico na Home.
2. **Botão "Área Restrita"** aparece no header local em todas as páginas; não existe na
   referência. Documentado no próprio `includes/header.php` como decisão deliberada de escopo
   (`* Logo, navegação principal (com submenus) e botão "Área Restrita"`), portanto tratado como
   decisão já tomada em tarefa anterior, não como defeito desta página.
3. **Indicador de página ativa no menu** (sublinhado em "A CT Price" na referência, quando nessa
   página) não está implementado no header local. Mesma observação: componente global, fora do
   escopo desta validação.
4. **`ReferenceError: google is not defined`**: observado de forma intermitente/não
   reprodutível no console ao carregar `/sobre-nos/` (não ocorreu em 2 de 3 reloads seguidos, e
   não ocorre na Home apesar do mesmo iframe do Google Maps) — origem no script externo do
   Google Maps Embed (`maps.googleapis.com/maps/api/js?...callback=onApiLoad`), carregado pelo
   próprio iframe, não pelo nosso código. Sem ação — não é determinístico nem específico desta
   página.

---

## 8. Interações testadas

| Interação | Resultado |
|---|---|
| Menu desktop (links diretos) | OK — todos os links resolvem para as rotas esperadas |
| Submenu "Clientes e Parceiros" (hover, desktop) | OK — abre mostrando "Clientes" e "Parceiros" |
| Hambúrguer (900×1200) | OK — abre menu com os 10 itens |
| Hambúrguer (390×844) | OK — abre menu com os 10 itens |
| CTA "Fale Conosco" (seção Dedicação) | OK — navega para `/fale-conosco/` (confirmado via clique real) |
| Hover dos ícones MVV | Regra CSS `:hover` presente e correta (`scale(1.2)`, 0.3s) — mesmo padrão já validado no componente |
| Carrossel — autoplay | OK — `swiper.autoplay.running === true`, delay 5000ms |
| Carrossel — loop | OK — `swiper.params.loop === true` |
| Carrossel — swipe | OK — `allowTouchMove === true` |
| Mapa do footer (iframe) | OK — carrega, exibe endereço, link "Abrir no Maps" funcional |
| Links do footer | OK — todos resolvem para rotas da nova arquitetura |
| WhatsApp canônico (topbar + botão flutuante) | OK — mesmo número (`5567992616117`) nos dois pontos |
| Cookie banner | OK — aparece em carga limpa (sem `localStorage`), botão "Ok" fecha e persiste (`ctprice_cookie_consent`) |

---

## 9. Console / rede

- **Erros JavaScript do próprio código**: nenhum.
- **404 de assets próprios**: nenhum (127 requisições a `127.0.0.1:8099/...`, todas HTTP 200).
- **Fontes**: carregadas localmente (`assets/fonts/*.woff2`), sem Google Fonts remoto.
- **Swiper**: carregado localmente (`assets/vendor/swiper/`).
- **Dependências legadas**: varredura do HTML renderizado confirma ausência de WordPress
  (`wp-content`/`wp-includes`/`wp-json`), Elementor, jQuery e Font Awesome.
- Requisições a `gc.kis.v2.scr.kaspersky-labs.com` observadas no log de rede são injetadas pelo
  antivírus da máquina local (extensão de navegador), não fazem parte do site.
- Ver §7.4 para o único erro de console observado (intermitente, de script externo do Google
  Maps, não determinístico).

---

## 10. REFERENCE DRIFT

Nenhum. Todo o conteúdo, gradiente, links e textos reconfirmados nesta validação batem
exatamente com `sobre-nos-audit.md`. A única correção feita (contorno dos cards MVV, §5.1) é uma
correção de uma lacuna da auditoria original, não uma mudança do site de referência ao vivo.
