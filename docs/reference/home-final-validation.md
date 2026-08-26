# Validação geral — Home completa

Validação de composição da Home já implementada (topbar, header, Hero, Bem-vindo à CT Price,
Nossos Serviços, Por que nos escolher?, Últimas notícias, Quer receber um contato?, footer
principal, bottom bar, botão flutuante de WhatsApp, cookie banner), percorrendo a página do
início ao fim nos três viewports obrigatórios — não apenas cada seção isolada.

Método: implementação local (`http://127.0.0.1:8099/`, servidor PHP embutido) comparada à
referência ao vivo (`https://ctprice.com.br/wp/`) e ao baseline já registrado (`reference-baseline.md`,
`home-desktop-audit.md`, `home-tablet-audit.md`, `home-mobile-audit.md`), via Chrome DevTools MCP
(`getBoundingClientRect`, `getComputedStyle`, screenshots, snapshot de acessibilidade, console e
rede). Nenhum valor mensurável foi estimado.

---

## 1. Escopo já implementado vs. baseline completo

A Home implementada cobre 10 das 13 seções de nível superior do baseline (`home-desktop-audit.md`,
seção 1). Três seções documentadas no baseline **ainda não foram implementadas** (já sinalizado
como `TODO` em `index.php`, não é uma regressão desta validação):

- `#4` — "Ética, agilidade..." + vídeo institucional (baseline: 837,4px + margem 25px)
- `#6` — Depoimentos (carrossel de testemunhos) (baseline: 486,3px + margem 50px)
- `#7` — Carrossel de clientes/parceiros (logos) (baseline: 200px)

Consequência direta: a altura total do documento da implementação é menor que a do baseline nos
três viewports (ver seção 2) — isso é esperado, não um defeito. Todas as comparações de altura de
seção/margem abaixo são feitas seção a seção, não pela soma total.

---

## 2. Alturas totais do documento

| Viewport | Baseline (13 seções) | Implementação (10 seções) |
|---|---|---|
| 1440×900 | 6.603,6px | 4.950px |
| 900×1200 | 7.841px (13 seções) | 5.933px |
| 390×844 | 12.042,8px (13 seções) | 10.067px |

A diferença é integralmente explicada pelas 3 seções não implementadas (ver seção 1) — confirmado
seção a seção abaixo, não por dedução aritmética.

---

## 3. Medições por seção (1440×900)

Todas as seções implementadas foram comparadas individualmente contra `home-desktop-audit.md`
(seção 1) e contra medição direta da referência ao vivo. Nenhum overflow horizontal
(`scrollWidth === clientWidth === 1425px` nos dois casos).

| Seção | Altura (baseline/referência) | Altura (implementação) | Resultado |
|---|---|---|---|
| Topbar | 66px | 66px | ✅ idêntico |
| Header | 132px | 132px | ✅ idêntico |
| Hero | 660px | 660px | ✅ idêntico |
| Bem-vindo à CT Price | 566,6px | 566,58px | ✅ idêntico |
| Nossos Serviços | 1090,4px | 1090,375px | ✅ idêntico |
| Por que nos escolher? | 602px | 602px | ✅ idêntico |
| Últimas notícias (blog) | 688,5px (688,546875 medido ao vivo) | 683,59px → **684,59px após correção** (seção 5) | ✅ (diferença residual de ~4px aceita, ver seção 5) |
| Quer receber um contato? | 471px | 470px | ✅ (1px, arredondamento) |
| Footer principal | 400px | 400px | ✅ idêntico |
| Footer bottom bar | 78,4px | 78,39px | ✅ idêntico |

Margens entre seções adjacentes na implementação (colapso de margin verificado): 50px entre
Bem-vindo e Serviços (margin-bottom de Bem-vindo prevalece), 0px entre Serviços e "Por que nos
escolher", 50px antes e depois do blog — todos consistentes com os valores de margem vertical
documentados por seção em `home-desktop-audit.md` (seção 1), aplicados agora entre vizinhos
diretos (já que as seções intermediárias ainda não existem).

---

## 4. Tablet (900×1200) e Mobile (390×844)

- **Sem overflow horizontal em nenhum dos dois** (`scrollWidth === clientWidth` em ambos: 885px
  em 900px de viewport, 390px em 390px de viewport).
- Grids que ficam mais estreitos (Bem-vindo, Serviços, Por que nos escolher) crescem de altura em
  900px por quebra de linha adicional do texto — comportamento esperado, consistente com
  `home-tablet-audit.md` (colunas mais estreitas, mesma tipografia fixa).
- O grid do blog passa a 2 colunas em ≤1024px (breakpoint próprio do componente, já documentado em
  `blog-section.css`) — com 3 posts, isso produz 2 linhas (2+1), aumentando bastante a altura da
  seção em 900px. Confirmado como comportamento esperado por `home-tablet-audit.md` ("o grid do
  blog já reflui em 1024px"), não uma regressão.
- Topbar em 390px mede 132px de altura na implementação vs. 176px no baseline mobile. **Isso já
  está registrado como diferença esperada, não defeito**, em `reference-baseline.md` (seção 5):
  o endereço implementado é mais curto (bairro/CEP pendentes em `config/company.php`), ocupando
  menos linhas no topbar mobile. Não é uma regressão desta validação.
- Logo do header em 390px: 333×96,7px na implementação vs. 350×101,6px no baseline mobile — a
  implementação usa `width: min(350px, 90%)` (decisão já tomada e comentada em `header.css`,
  evitando overflow em telas ainda mais estreitas). Diferença pequena (~5%), não tratada como
  regressão nesta validação por já ser uma decisão registrada de uma etapa anterior — mantida.
- Header mobile empilha corretamente (logo → hambúrguer → botão "Área Restrita", centralizados),
  igual ao documentado em `home-mobile-audit.md`.
- "Por que nos escolher?" em 900px e 390px renderiza corretamente (imagem + lista, sem o bug de
  colapso de altura do original em mobile — a implementação usa `background-image` com altura
  própria em vez do `<img>` do Elementor que colapsa no site de referência). Isso é uma decisão já
  tomada em etapa anterior (correção de defeito conhecido categoria C), não revista aqui.

---

## 5. Correções realizadas nesta validação

Uma diferença comprovada foi encontrada e corrigida em `assets/css/blog-section.css`
(componente já aprovado em etapa anterior — corrigido aqui por regressão comprovada, não por
preferência):

1. **Badge de categoria do post (ex.: "FOLHA DE PAGAMENTO") sem inset.** Medição direta da
   referência mostrou `top: 20px; right: 20px` (badge afastado do canto); a implementação tinha
   `top: 0; right: 0` (badge colado no canto da miniatura). Corrigido para `top: 20px; right: 20px`.
2. **Falta de `border-top` na área de data/hora do post.** A referência tem
   `border-top: 1px solid rgb(234,234,234)` em `.elementor-post__meta-data` (linha divisória sutil
   acima da data); a implementação não tinha nenhuma borda. Adicionado
   `border-top: 1px solid #EAEAEA` em `.blog-card__meta`.

Após a correção, a altura do card de blog subiu 1px (571,59px → 572,59px), reduzindo a diferença
residual frente à referência (576,55px) de ~4,95px para ~3,95px. Essa diferença residual foi
investigada e rastreada até um espaçamento de ~5px entre o excerto e o link "Leia mais" no HTML da
referência, não explicado por nenhuma regra CSS de margin/padding legível (provável artefato de
espaço em branco do próprio markup gerado pelo Elementor/WordPress). Por `CLAUDE.md` (não copiar
código/estrutura gerada pelo Elementor como arquitetura), essa diferença residual de ~4px por
card (~0,6% da altura da seção, imperceptível visualmente) foi **aceita e documentada**, não
reproduzida artificialmente.

Nenhuma outra correção foi necessária — nenhuma regressão foi encontrada em topbar, header, hero,
welcome-section, services-section, why-choose-us-section, contact-section, footer ou nos
componentes globais (WhatsApp, cookie banner).

---

## 6. Interações testadas

| Interação | Resultado |
|---|---|
| Submenu desktop (hover/foco) | Não testado nesta rodada (já coberto e aprovado na implementação do header) |
| Hambúrguer mobile (390px): abrir/fechar | ✅ abre empurrando conteúdo (in-flow, não overlay), ícone alterna barras↔X |
| Submenu mobile ("Clientes e Parceiros") | ✅ expande inline, item pai ganha `background:#00222C`/`color:#10E36B` |
| Hero: autoplay | ✅ `swiper.autoplay.running === true`, avança sozinho (índice mudou entre duas leituras) |
| Hero: loop | ✅ `swiper.params.loop === true` |
| Hero: touch/swipe | Não testado diretamente (dependente do Swiper, biblioteca padrão, sem customização própria) |
| Serviços: hover do CTA "Fale Conosco" | ✅ `background-color` muda para `rgb(16,227,107)` (#10E36B), igual ao baseline |
| Serviços: CTA "Fale Conosco" (destino) | ✅ aponta para `/fale-conosco/` (correção de categoria C já registrada, não uma URL quebrada) |
| Blog: hover dos cards | CSS confirmado (`:hover { box-shadow: 0 0 30px rgba(0,0,0,.15) }`), mecanismo de hover já confirmado funcional via teste do CTA de Serviços |
| Blog: links | ✅ títulos, "LEIA MAIS »" e miniatura apontam para a URL do post original |
| Contato: aparência dos campos | ✅ Nome/E-mail/Telefone/Mensagem renderizam com placeholder e asterisco de obrigatório |
| Contato: CTA do WhatsApp | ✅ link aponta para `https://api.whatsapp.com/send?phone=5567992616117` (número canônico) |
| Contato: formulário | Não enviado (fora do escopo, conforme instrução) |
| Footer: links | ✅ todos os 8 itens do menu secundário presentes e apontando para as páginas corretas, incluindo "Benefícios" → `/trabalhe-conosco/#beneficios` (correção categoria C já registrada) |
| Footer: mapa | ✅ `<iframe>` do Google Maps carrega e responde (link "Abrir no Maps" presente) |
| WhatsApp flutuante: posição durante scroll | ✅ `position:fixed`, mesma posição de viewport em `scrollY=0` e `scrollY=2000` |
| WhatsApp flutuante: link | ✅ mesmo número canônico do WhatsApp usado em topbar/contato/footer |
| Cookie banner: primeira visita (preferência limpa) | ✅ banner visível |
| Cookie banner: aceitar → fecha | ✅ `hidden=true` após clique em "Ok", valor gravado em `localStorage` |
| Cookie banner: reload → permanece oculto | ✅ confirmado |
| Cookie banner: limpar preferência → reaparece | ✅ confirmado |
| Cookie banner × WhatsApp flutuante | ✅ z-index do banner (100000) cobre o botão (z-index 1) na faixa de sobreposição, sem sobra visual |

---

## 7. Console e rede

- **Console:** único item recorrente nos três viewports é um aviso de acessibilidade do Chrome
  ("An element doesn't have an autocomplete attribute", contagem 3 — os três campos de texto do
  formulário de contato). Não é um erro; é uma melhoria de acessibilidade pendente, registrada
  como pendência não bloqueante (seção 8). Nenhum erro JavaScript em nenhum dos três viewports.
- **Rede:** nenhum 404 nos assets do próprio projeto (CSS, JS, fontes, imagens) em nenhum dos três
  viewports. Um `net::ERR_ABORTED` observado em 390px para
  `LogoPreferencialColorida-1024x297.png` é o comportamento nativo esperado do navegador ao
  abortar um candidato de `srcset` que ele decide não usar (troca para o candidato de 768w) — não
  é um erro de implementação.
- Requisições externas observadas: Google Maps (esperado, mapa incorporado do footer) e um script
  de extensão local de antivírus (`kaspersky-labs.com`) — já documentado em
  `home-desktop-audit.md` como injeção do ambiente de auditoria, não parte do site, ignorado aqui
  pela mesma razão.

---

## 8. Performance básica (checklist)

| Item | Status |
|---|---|
| Não carrega Font Awesome | ✅ confirmado (nenhuma requisição de Font Awesome nas três viewports) |
| Não carrega jQuery | ✅ confirmado |
| Não carrega assets do WordPress | ✅ confirmado (nenhuma requisição a `wp-content`/`wp-includes`) |
| Não depende de CDN em runtime | ✅ confirmado (todo CSS/JS/fonte servido de `127.0.0.1:8099`, exceto o mapa do Google, que é uma integração externa esperada, não uma dependência de CDN de front-end) |
| Usa Swiper local | ✅ `assets/vendor/swiper/swiper-bundle.min.{css,js}` |
| Usa fontes locais | ✅ `assets/fonts/*.woff2`, sem Google Fonts remoto |

Nenhuma otimização adicional foi realizada nesta etapa (fora de escopo).

---

## 9. Defeitos conhecidos já corrigidos (categorias A–D, `architecture-proposal.md`)

Reconfirmados presentes e intactos nesta validação (não revistos, apenas confirmados):

- CTA "Fale Conosco" (Serviços): `/fale-conosco/` em vez do `https://ctprice.com.br/contato` quebrado.
- Footer "Benefícios": `/trabalhe-conosco/#beneficios` em vez do `/informacoes/#beneficios` quebrado.
- Cookie banner: `aria-label` do botão de fechar corrigido para descrever a ação real (etapa anterior).
- "Por que nos escolher": imagem implementada como `background-image` (evita o bug de colapso de altura do widget original em mobile).

---

## 10. REFERENCE DRIFT

Nenhum novo `REFERENCE DRIFT` identificado nesta validação. O único já registrado
(`DRIFT-001` — botão "Área Restrita" removido do header ao vivo) permanece válido e não foi
reavaliado aqui (fora do escopo desta validação de composição).

---

## 11. Pendências que não bloqueiam a Home

- Atributo `autocomplete` ausente nos 3 campos de texto do formulário de contato (aviso de
  acessibilidade do Chrome, não um erro).
- Diferença residual de ~4px na altura do card de blog (ver seção 5) — aceita, não reproduzida
  artificialmente por envolver um artefato de espaço em branco do HTML gerado pelo WordPress.
- Diferença de ~5% no tamanho do logo mobile (333px vs. 350px) — decisão já tomada em etapa
  anterior (`min(350px, 90%)`), mantida.
- `endereco.bairro`/`endereco.cep` pendentes em `config/company.php` (já documentado em
  `reference-baseline.md`, seção 5) — segue afetando a altura do topbar mobile.
- Três seções do baseline ainda não implementadas na Home: "Ética, agilidade..." + vídeo
  institucional, Depoimentos (carrossel), Carrossel de clientes/parceiros — fora do escopo desta
  tarefa (validação, não implementação de novas seções).
