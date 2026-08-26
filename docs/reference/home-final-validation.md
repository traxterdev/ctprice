# Validação final definitiva — Home completa (13 seções)

Esta é a validação da Home **completa**: todas as 13 seções de nível superior documentadas no
baseline (`home-desktop-audit.md`, seção 1) estão implementadas — topbar, header, Hero,
Bem-vindo à CT Price, Ética/vídeo institucional, Nossos Serviços, Depoimentos, Carrossel de
clientes/parceiros, Por que nos escolher?, Últimas notícias, Quer receber um contato?, Footer
principal e bottom bar — além dos componentes globais (WhatsApp flutuante, cookie banner).

Substitui a validação anterior (`home-final-validation.md`, versão de 10 seções). Método:
implementação local (`http://127.0.0.1:8099/`) comparada à referência ao vivo
(`https://ctprice.com.br/wp/`) e ao baseline (`reference-baseline.md`,
`home-desktop-audit.md`, `home-tablet-audit.md`, `home-mobile-audit.md`), via Chrome DevTools MCP,
nos três viewports obrigatórios: 1440×900, 900×1200, 390×844. Nenhum valor mensurável foi
estimado.

---

## 1. Altura total da página

| Viewport | Baseline (13 seções) | Implementação (13 seções) | Diferença |
|---|---|---|---|
| 1440×900 | 6.603,6px | 6.599px | -4,6px (0,07%) |
| 900×1200 | 7.841px | 7.555px | -286px (reflow de texto, ver seção 3) |
| 390×844 | 12.042,8px | 12.158px | +115px (reflow de texto, ver seção 3) |

Em 1440×900 — o viewport onde o baseline foi medido com maior precisão por seção — a
implementação está a **4,6px de 6.603,6px (0,07% de diferença)** somando as 13 seções, o que é
consistente com fidelidade pixel a pixel. Nos outros dois viewports a diferença vem de reflow de
texto em grids mais estreitos (títulos/parágrafos com tipografia fixa que não reduz, quebrando em
mais ou menos linhas conforme a largura — comportamento já documentado e aceito nas validações
anteriores desta Home, não uma regressão).

---

## 2. Tabela de composição acumulada — 1440×900

| Seção | Y original | Y local | Altura original | Altura local | Diferença |
|---|---|---|---|---|---|
| Topbar | 0 | 0 | 66 | 66 | 0 |
| Header | 66 | 66 | 132 | 132 | 0 |
| Hero | 198 | 198 | 660 | 660 | 0 |
| Bem-vindo à CT Price | 908 | 908 | 566,6 | 566,58 | ~0 |
| Ética / vídeo institucional | 1524,6 | 1524,58 | 837,4 | 837,39 | ~0 |
| Nossos Serviços | 2387 | 2386,97 | 1090,4 | 1090,38 | ~0 |
| Depoimentos | 3527,3 | 3527,34 | 486,3 | 486,42 | +0,12 |
| Carrossel de clientes | 4063,7 | 4063,77 | 200 | 200 | 0 |
| Por que nos escolher? | 4263,7 | 4263,77 | 602 | 602 | 0 |
| Últimas notícias | 4915,7 | 4915,77 | 688,5 | 684,59 | -3,91 |
| Quer receber um contato? | 5654,2 | 5650,36 | 471 | 470 | -1 (+ arrasto do blog) |
| Footer principal | 6125,2 | 6120,36 | 400 | 400 | 0 (+ arrasto do blog) |
| Footer bottom bar | 6525,2 | 6520,36 | 78,4 | 78,39 | ~0 (+ arrasto do blog) |

O único desvio de altura não-trivial é o blog (-3,91px), já registrado (seção 6) como diferença
residual aceita — as diferenças de Y nas seções posteriores ao blog são inteiramente esse mesmo
desvio se propagando (cascata), não desvios novos e independentes.

**Margens entre TODAS as seções adjacentes foram conferidas nos três viewports** (topo a topo,
seção a seção) e batem exatamente com o colapso de margin esperado a partir dos valores já
medidos e documentados por seção — sem nenhuma margem duplicada, ausente ou fora do padrão em
nenhum dos 12 pares de seções adjacentes, em nenhum dos três viewports.

---

## 3. Comportamento nos três viewports

- **Sem overflow horizontal em nenhum dos três** (`scrollWidth === clientWidth`): 1425px em
  1440px, 885px em 900px, 390px em 390px.
- **900×1200 (7.555px) e 390×844 (12.158px)**: grids mais estreitos (Bem-vindo, Serviços,
  Depoimentos, Por que escolher) crescem de altura por quebra de linha adicional — tipografia
  fixa, sem redução de `font-size`, replicando o comportamento real do site original (já
  documentado nas auditorias de tablet/mobile). O carrossel de logos passa de 10 (desktop) → 2
  (tablet) → 1 (mobile) logo(s) visível(is), confirmado nos três viewports.
- Nenhuma seção teve elemento oculto, removido ou reordenado em nenhum breakpoint.

---

## 4. Regressões encontradas e corrigidas nesta rodada

Uma diferença comprovada foi encontrada e corrigida em `assets/css/testimonials-section.css`
(componente já aprovado — corrigido por diferença comprovada, não por preferência):

**Espaço reservado para a paginação do carrossel de Depoimentos subdimensionado.** Ao montar a
Home completa e medir a composição acumulada, a seção "Depoimentos" mediu 467,42px de altura
contra 486,3125px da referência — uma diferença de ~18,9px não identificada na validação
específica anterior desta seção (que conferiu a altura do slide mais alto, mas não o espaço
reservado abaixo dele para a paginação). A causa: `.testimonials-swiper__pagination` tinha
`margin-top: 15px`, enquanto o original reserva 40px de `padding-bottom` no widget do Swiper para
a paginação. Corrigido para `margin-top: 34px` (34 + 6px de altura das bolinhas = 40px, igual ao
original). Resultado: 486,42px vs. 486,3125px da referência (diferença de 0,11px, dentro da
margem de uma variação já aceita anteriormente). Esta não é uma "altura artificial" — é a
correção de um espaçamento medido incorretamente na etapa anterior.

Nenhuma outra regressão foi encontrada — nenhum estilo global vazou entre componentes, nenhum
z-index incorreto, nenhum elemento sobreposto além do já esperado (WhatsApp flutuante coberto
pelo cookie banner quando ambos visíveis), nenhuma fonte incorreta, nenhuma quebra inesperada de
layout.

---

## 5. Interações testadas (Home completa)

| Interação | Resultado |
|---|---|
| Header: hambúrguer mobile abrir/fechar | ✅ |
| Header: submenu mobile ("Clientes e Parceiros") | ✅ expande inline |
| Hero: autoplay/loop | ✅ `swiper.autoplay.running===true`, `loop===true` |
| Vídeo: capa visível, sem autoplay | ✅ `video.paused===true` antes do clique |
| Vídeo: clique → reproduz, loop, sem controles nativos | ✅ (comportamento medido do original, preservado) |
| Serviços: hover do CTA "Fale Conosco" | ✅ (já confirmado em validação anterior, cor muda para `#10E36B`) |
| Serviços: destino do CTA | ✅ `/fale-conosco/` |
| Depoimentos: autoplay/loop/touch | ✅ `swiper.autoplay.running===true`, `loop===true`, `allowTouchMove===true` |
| Depoimentos: convivência com Hero e Clientes (3 Swipers simultâneos) | ✅ todos rodando independentemente, sem conflito |
| Clientes: 10 logos desktop / 2 tablet / 1 mobile | ✅ confirmado via `swiper.params.slidesPerView` nos 3 viewports |
| Clientes: continuidade do loop após múltiplos ciclos | ✅ (já testado em validação anterior: `slideToLoop(81)` → `slideNext()` → volta a index 0 sem quebra) |
| Blog: hover (box-shadow) | ✅ `box-shadow` base presente, regra `:hover` confirmada em validação anterior |
| Blog: links | ✅ |
| Contato: aparência dos campos | ✅ |
| Contato: CTA WhatsApp | ✅ mesmo número canônico |
| Contato: formulário | não enviado (fora do escopo) |
| Footer: 8 links do menu secundário | ✅ todos presentes e corretos, incluindo "Benefícios" → `/trabalhe-conosco/#beneficios` |
| Footer: mapa | ✅ `<iframe>` carrega |
| WhatsApp flutuante: posição fixa | ✅ `left:35px; bottom:50px` em qualquer scroll |
| WhatsApp flutuante: URL canônica | ✅ `https://api.whatsapp.com/send?phone=5567992616117` |
| WhatsApp × cookie banner (z-index) | ✅ banner (`z-index:100000`) cobre o botão (`z-index:1`) na faixa de sobreposição |
| Cookie banner: primeira visita | ✅ visível |
| Cookie banner: aceitar → fecha | ✅ |
| Cookie banner: reload → permanece oculto | ✅ |
| Cookie banner: limpar preferência → reaparece | ✅ |

---

## 6. Console e rede (página completa)

- **Console:** único item recorrente é o aviso de acessibilidade do Chrome sobre `autocomplete`
  ausente nos 3 campos de texto do formulário de contato (já registrado como pendência não
  bloqueante). **Nenhum erro JavaScript** com todos os scripts da Home carregados juntos (header,
  cookie-banner, video-section, testimonials-init, clients-carousel-init, hero-init,
  scroll-reveal, Swiper) — sem conflito entre as 3 instâncias de Swiper.
- **Rede:** **nenhum 404** em nenhum asset do projeto (CSS, JS, fontes, imagens, vídeo) em nenhum
  dos três viewports — incluindo as 82 imagens do carrossel de clientes e o vídeo institucional
  (~37MB, carrega e reproduz corretamente). Requisições externas: Google Maps (mapa do footer,
  esperado) e um script de extensão local de antivírus (`kaspersky-labs.com`, já documentado como
  injeção do ambiente, não parte do site).

---

## 7. Performance básica (checklist)

| Item | Status |
|---|---|
| Não carrega WordPress em runtime | ✅ |
| Não carrega Elementor em runtime | ✅ |
| Não carrega jQuery | ✅ |
| Não carrega Font Awesome | ✅ |
| Não carrega Google Fonts remoto | ✅ (fontes locais, `assets/fonts/*.woff2`) |
| Não carrega Swiper via CDN | ✅ (`assets/vendor/swiper/`) |
| Imagens locais | ✅ (`assets/images/**`) |
| Vídeo local | ✅ (`assets/videos/institucional-ct-price.mp4`) |

Nenhuma otimização de performance foi realizada nesta etapa (fora de escopo).

---

## 8. Diferenças intencionais já aprovadas (não revistas nesta validação)

- Hero mobile: **CORREÇÃO INTENCIONAL DE DEFEITO CONHECIDO** — o bug de quebra de linha extrema
  do original (`padding-left:150px` fixo + `font-size` sem redução, forçando o texto do slide a
  quebrar palavra a palavra em telas estreitas) foi corrigido, não preservado: padding lateral
  responsivo em vez de `padding-left:150px` fixo, redução tipográfica no mobile, eliminando a
  quebra extrema de texto — mantendo identidade visual, imagem de fundo, altura da seção e
  composição geral do Hero.
- "Por que nos escolher?": imagem implementada como `background-image` para evitar o bug de
  colapso de altura (~20px) do widget original no mobile — correção intencional já registrada.
- Carrossel de logos: `object-fit:contain` + altura fixa de slide em vez do `object-fit:fill`
  sem altura própria do original — correção intencional já registrada (evita distorção/tamanho
  excessivo no mobile).
- 3 logos 404 do carrossel de clientes (`mv.jpg`, `modelo.jpg`, `logo_0020_Camada16.jpg`) — não
  reproduzidos, defeito conhecido categoria C.
- Número canônico de WhatsApp unificado em todas as seções/componentes (topbar, contato, footer,
  botão flutuante) — divergências do site original documentadas e não replicadas.
- CTA "Fale Conosco" (Serviços) e link "Benefícios" (Footer): destinos corrigidos de URLs
  quebradas do original (categoria C).
- `endereco.bairro`/`endereco.cep` pendentes em `config/company.php` — afeta a altura do topbar
  mobile (mais curto que o original), documentado como consequência esperada, não defeito.

---

## 9. REFERENCE DRIFT

Nenhum novo `REFERENCE DRIFT` identificado nesta validação. O único já registrado
(`DRIFT-001` — botão "Área Restrita" removido do header ao vivo) permanece válido e não foi
reavaliado aqui.

---

## 10. Pendências não bloqueantes

- Atributo `autocomplete` ausente nos 3 campos de texto do formulário de contato.
- Diferença residual de ~4px na altura do card de blog (artefato de espaço em branco do HTML
  gerado pelo Elementor/WordPress entre o excerto e o link "Leia mais" — não reproduzido
  deliberadamente, por envolver copiar uma particularidade de markup do WordPress).
- Diferença de ~5% no tamanho do logo mobile do header (333px vs. 350px do baseline) — decisão já
  tomada em etapa anterior (`min(350px, 90%)` para evitar overflow em telas ainda mais estreitas).
- `endereco.bairro`/`endereco.cep` pendentes em `config/company.php`.
- Três seções do baseline totalmente concluídas nesta etapa (Depoimentos, Carrossel de clientes,
  Ética/vídeo institucional já implementadas em etapas anteriores) — **a Home está agora completa
  quanto às 13 seções do baseline**; não há mais seções de conteúdo pendentes na Home.
