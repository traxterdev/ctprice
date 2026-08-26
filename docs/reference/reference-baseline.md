# Baseline Visual — Projeto CT Price

## Status

A partir deste documento, as auditorias e capturas já produzidas passam a ser o **baseline oficial** da reconstrução do site da CT Price. Toda medição, decisão de layout, tipografia, cor, espaçamento e comportamento responsivo já implementada ou a implementar deve ser confrontada contra este baseline — não contra uma nova leitura ad-hoc do site ao vivo feita sem registro.

Este documento não substitui as auditorias — ele as declara como referência congelada e define o processo para quando o site ao vivo divergir delas.

---

## 1. Fontes do baseline

O baseline oficial é composto pelos seguintes documentos e artefatos, todos já produzidos e versionados em `docs/reference/`:

| Fonte | Conteúdo |
|---|---|
| `docs/reference/home-desktop-audit.md` | Medição completa da Home em 1440×900 — estrutura, header/topbar, tipografia, cores, componentes, carrosséis, footer |
| `docs/reference/home-tablet-audit.md` | Medição da Home em 900×1200 — comportamento híbrido, breakpoints reais (1024px e 767px), diferenças de tablet |
| `docs/reference/home-mobile-audit.md` | Medição da Home em 390×844 — comportamento mobile, bugs conhecidos (hero, "Por que nos escolher", logos), breakpoints |
| `docs/reference/site-inventory.md` | Inventário de todas as 13 páginas reais do site, links, sistemas externos, componentes globais |
| `docs/reference/global-data-conflicts.md` | Divergências de dados globais (telefone, WhatsApp, endereço) encontradas entre páginas do site atual |
| `docs/reference/screenshots/home-desktop-1440-full.png` | Captura de referência — Home, 1440px |
| `docs/reference/screenshots/home-tablet-900-full.png` | Captura de referência — Home, 900px |
| `docs/reference/screenshots/home-mobile-390-full.png` | Captura de referência — Home, 390px |

Toda decisão de implementação (CSS, HTML, JS) deve poder ser rastreada até um valor medido em uma dessas fontes. Onde uma medição não existir ainda (ex.: páginas internas além da Home), ela deve ser feita e documentada **antes** da implementação, seguindo o mesmo processo já usado nestes documentos — não deve ser inferida a partir do site ao vivo sem registro.

---

## 2. Papel do site ao vivo a partir de agora

O site ao vivo, `https://ctprice.com.br/wp/`, **continua sendo usado** para:

- **Medições** de propriedades ainda não auditadas (ex.: páginas internas, componentes ainda não cobertos).
- **Confirmação de estilos** durante rodadas de validação visual (comparação implementação × referência).
- **Obtenção de assets** (imagens, logos, ícones) legitimamente hospedados no site atual.
- **Investigação técnica** de comportamento, breakpoints, configurações de widgets/carrosséis.

O que muda: o site ao vivo **deixa de ser a fonte de verdade automática**. Ele é dinâmico — pode ser editado pelo cliente, por terceiros, ou sofrer alterações de conteúdo a qualquer momento (como já aconteceu — ver seção 4). O baseline (seção 1) é uma fotografia medida em uma data específica e é isso que a reconstrução reproduz, salvo decisão explícita em contrário.

---

## 3. Política de REFERENCE DRIFT

**Definição:** REFERENCE DRIFT é qualquer diferença encontrada entre o site ao vivo e o baseline já auditado — ou seja, uma mudança que o site de referência sofreu **depois** de ter sido medido e documentado.

**Regra:** um REFERENCE DRIFT nunca é aplicado automaticamente à implementação. Ele deve ser:

1. **Confirmado** — reproduzido de forma consistente no site ao vivo (não um glitch de carregamento, cache ou estado transitório).
2. **Classificado** explicitamente como `REFERENCE DRIFT` (não como "defeito a corrigir" das categorias A/B/C/D já definidas em `docs/architecture-proposal.md`, seção 2 — um drift é uma mudança de conteúdo/estrutura do site de referência ao longo do tempo, uma categoria à parte).
3. **Registrado** neste documento (seção 4), com: o que era, o que passou a ser, quando/como foi percebido, e o estado atual da implementação em relação a ele.
4. **Deixado como está** na implementação (mantendo o baseline original) até uma decisão explícita do projeto — nunca uma correção silenciosa feita só porque "é o que o site mostra agora".

Isso protege a reconstrução de perseguir um alvo em movimento e preserva a rastreabilidade: qualquer divergência futura entre a implementação e o site ao vivo tem uma explicação registrada, não é um "esquecimento".

---

## 4. Drifts confirmados

### DRIFT-001 — Botão/widget "Área Restrita" removido do header

- **Baseline original:** documentado em `docs/reference/home-desktop-audit.md` (seção 2.2) e `docs/reference/site-inventory.md** — o header contém um botão "Área Restrita" (pill outline, `border: 3px solid #057038`, `border-radius: 40px`), widget Elementor `elementor-element-7b41d49c`, dentro do container `elementor-element-5dd6cbe2` (terceira coluna do header, ao lado de logo e menu).
- **O que mudou:** durante a rodada de validação visual do header (comparação ao vivo × implementação), o container `elementor-element-5dd6cbe2` ainda existe no DOM do site ao vivo, na mesma posição, mas está **vazio** — o widget do botão foi removido. Confirmado por inspeção direta do DOM e reconfirmado após hard-reload (não é cache).
- **Estado atual da implementação:** o botão "Área Restrita" **foi mantido** em `includes/header.php`, reproduzindo o baseline original medido (não o estado atual do site ao vivo).
- **Ação:** nenhuma. Não remover o botão da implementação até decisão explícita do projeto/cliente sobre se essa remoção no site atual é intencional e deve ser replicada, ou se é um acidente/estado transitório do site de referência.

---

## 5. Diferenças que NÃO são drift nem defeito de implementação

Nem toda diferença observada entre a implementação e o site ao vivo é um `REFERENCE DRIFT` ou um bug de CSS/HTML. Um caso já identificado e que deve ser tratado como esperado, não como erro:

### Campos pendentes em `config/company.php` (bairro, CEP)

- `config/company.php` mantém `endereco.bairro` e `endereco.cep` como `null`, propositalmente, porque a auditoria encontrou divergência entre o texto exibido no site ("Monte Castelo", CEP "79.010-190") e o endereço usado na query do mapa incorporado ("Vila Rosa Pires", CEP "79002-400") — ver `docs/reference/global-data-conflicts.md`, seção 3.
- Consequência visual direta: o endereço renderizado pela implementação é mais curto que o do site ao vivo (falta o bairro), o que também faz o texto ocupar menos linhas em telas estreitas (ex.: topbar mobile com altura menor que a medida no baseline).
- **Isso não deve ser registrado como defeito visual da implementação nem como REFERENCE DRIFT.** É uma consequência esperada e documentada de um dado ainda não confirmado — resolve-se preenchendo o campo em `config/company.php` quando o valor correto for definido, não ajustando CSS/espaçamento para "forçar" a altura a bater.

---

## 6. Processo daqui para frente

1. Antes de implementar qualquer página ou componente novo: consultar o baseline (seção 1) primeiro.
2. Se o baseline não cobre o que está sendo implementado: medir no site ao vivo, documentar a medição (nova auditoria ou adendo a uma existente), e só então implementar — o mesmo processo já usado para Home/header.
3. Se, durante uma validação visual, o site ao vivo divergir do que o baseline documentou: **não ajustar a implementação para seguir o site ao vivo automaticamente**. Classificar como `REFERENCE DRIFT`, registrar nesta seção 4, e manter a implementação fiel ao baseline até decisão explícita.
4. Diferenças explicáveis por dados pendentes (seção 5) ou por classificações já existentes de defeito conhecido (`docs/architecture-proposal.md`, seção 2, categorias A/B/C/D) seguem as regras já estabelecidas nesses documentos — não precisam virar um novo registro de drift.
