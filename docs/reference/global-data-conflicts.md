# Divergências nos dados globais da CT Price

## Metodologia

- **Ferramenta:** Chrome DevTools MCP — navegação real em todas as 10 páginas institucionais, nos 3 posts de blog e nos sistemas externos relevantes, extraindo topbar, footer, links de WhatsApp (topbar e botão flutuante separadamente), e-mails, redes sociais e links de mapa via DOM (`querySelectorAll`), não apenas leitura de HTML estático.
- **Data:** 2026-08-17
- **Objetivo:** registrar, sem escolher arbitrariamente, todo dado global que aparece com valores diferentes em páginas diferentes do site atual — para uso na etapa de consolidação de `config/company.php` (ver `docs/architecture-proposal.md`, seções 13 e 14.1).
- Nenhuma divergência foi resolvida aqui — cada uma permanece como `TODO` em `config/company.php` até decisão do cliente.

---

## 1. WhatsApp — divergência em 3 valores, dependendo da página E da posição (topbar vs. botão flutuante)

Levantamento completo, página por página, distinguindo o link do **topbar** (ícone de WhatsApp na barra superior) do **botão flutuante** (ícone fixo no canto inferior esquerdo):

| Página | Topbar | Botão flutuante |
|---|---|---|
| Home (`/wp/`) | `(67) 99261-6117` | `(67) 99261-6117` |
| A CT Price (`/wp/sobre-nos/`) | `(67) 99232-4097` | `(67) 99232-4097` (mesmo valor do topbar) |
| Clientes (`/wp/clientes/`) | `(67) 99232-4097` | `(67) 99232-4097` |
| Parceiros (`/wp/parcerias/`) | `(67) 99232-4097` | `(67) 99232-4097` |
| Fale Conosco (`/wp/fale-conosco/`) | `(67) 99232-4097` | `(67) 99232-4097` (mesmo valor também usado como contato "Comercial" no diretório da página) |
| Informações (`/wp/informacoes/`) | `(67) 99232-4097` | `(67) 99232-4097` |
| Trabalhe Conosco (`/wp/trabalhe-conosco/`) | `(67) 99232-4097` | `(67) 99232-4097` |
| Ouvidoria (`/wp/ouvidoria/`) | `(67) 99232-4097` | `(67) 99232-4097` |
| **Depoimentos (`/wp/depoimentos/`)** | **`(67) 99261-6117`** (igual à Home) | **`(67) 99232-4097`** (igual às demais páginas) — **os dois valores diferentes aparecem na mesma página** |
| Área Restrita (`/wp/arearestrita/`) | `(67) 99232-4097` | `(67) 99232-4097` |
| **Os 3 posts de blog** (mesmo template compartilhado) | `(67) 99232-4097` | **`(67) 99204-1134`** — um **terceiro** número, exclusivo do botão flutuante nos posts, não visto em nenhuma outra página |

**Resumo — 3 números distintos encontrados no total:**
1. `(67) 99261-6117` — `5567992616117` — usado só na Home (topbar+flutuante) e no topbar de Depoimentos.
2. `(67) 99232-4097` — `5567992324097` — o mais frequente; usado no topbar de 8 das 9 páginas institucionais restantes, no botão flutuante de 8 delas, no botão flutuante de Depoimentos, no topbar dos 3 posts de blog, e como contato "Comercial" na página Fale Conosco.
3. `(67) 99204-1134` — `5567992041134` — exclusivo do botão flutuante dos 3 posts de blog.

Nota técnica: no link do topbar de Depoimentos, o parâmetro da URL tem um espaço codificado antes do número (`phone=%205567992616117`) — o número funcional é o mesmo (`5567992616117`), só o link está malformado.

**Campo afetado em `config/company.php`:** `whatsapp_principal` — mantido como `TODO`, nenhum dos 3 valores foi escolhido.

**Não é conflito** — contatos por departamento da página Fale Conosco, todos confirmados sem divergência entre si (aparecem uma única vez cada, em um só lugar):
- Comercial: `(67) 99232-4097` (mesmo nº do topbar/geral)
- Pessoal: `(67) 3313-7301`
- Fiscal: `(67) 3313-7302`
- Contábil: `(67) 3313-7304`
- Central/Empresarial: `(67) 3313-7300` (mesmo nº do telefone fixo geral)

---

## 2. Telefone fixo — sem divergência

`(67) 3313-7300` aparece de forma idêntica no topbar de todas as 10 páginas institucionais e dos 3 posts de blog verificados. **Nenhuma divergência encontrada** — preenchido em `config/company.php`.

---

## 3. Endereço — divergência de bairro e CEP entre o texto exibido e o link do mapa incorporado

Texto exibido (idêntico em topbar/footer de todas as páginas verificadas):
> "R. José Antônio, 2.777 / Monte Castelo – CEP: 79.010-190 / Campo Grande – MS"

URL do `<iframe>` do Google Maps incorporado no footer (idêntica em todas as páginas verificadas):
```
https://maps.google.com/maps?q=R.%20Jos%C3%A9%20Ant%C3%B4nio%2C%202777%20-%20Vila%20Rosa%20Pires%2C%20Campo%20Grande%20-%20MS%2C%2079002-400&t=m&z=15&output=embed&iwloc=near
```
— que corresponde ao endereço "R. José Antônio, 2777 - **Vila Rosa Pires**, Campo Grande - MS, **79002-400**".

| Dado | Texto do site (topbar/footer) | Query do mapa incorporado |
|---|---|---|
| Logradouro | R. José Antônio, 2.777 | R. José Antônio, 2777 — **igual** |
| Bairro | Monte Castelo | Vila Rosa Pires — **diferente** |
| CEP | 79.010-190 | 79002-400 — **diferente** |
| Cidade/UF | Campo Grande – MS | Campo Grande - MS — **igual** |

O link curto do endereço no footer (`https://goo.gl/maps/eYes1Vqbyzw6hBYy8`) foi aberto e confirmado **funcional**: resolve para uma ficha do Google Maps intitulada "CT Price Organização Contábil" (corroborando o nome fantasia/razão social, sem conflito).

**Campos afetados em `config/company.php`:** `endereco.bairro` e `endereco.cep` — mantidos como `TODO`. `logradouro`, `cidade` e `uf` foram preenchidos por não terem divergência. A URL de embed em si (`google_maps_embed_url`) foi registrada como está no site — é um dado funcional, não o texto humano em conflito.

---

## 4. Redes sociais — nenhuma rede social oficial da CT Price encontrada

Busca por links de Facebook, Instagram, LinkedIn, YouTube, X/Twitter e TikTok em todas as páginas institucionais e nos 3 posts de blog não encontrou nenhum perfil da própria CT Price.

A única ocorrência de links de redes sociais no site é na página **Depoimentos**, dentro dos cartões de depoimento — são links para os perfis/sites de **clientes individuais** citados nos depoimentos (ex.: Instagram/site de "Agro Só Sal", "Solda Maq", "Cotto Figueira", "AgroTouro", "Grupo Figueira", "Campo Doce Distribuidora", "Saborzitos"), não da CT Price. Confirmado via inspeção da hierarquia do DOM (`elementor-social-icons-wrapper` dentro do widget de depoimento/parceiro, não no header/footer globais).

**Campo afetado em `config/company.php`:** `redes_sociais` — mantido vazio, sem `TODO` de divergência (não há dado da empresa a registrar, e os links de clientes não pertencem a este campo).

---

## 5. Sistemas externos — apenas o de recrutamento pôde ser confirmado como válido

| Sistema | URL | Status confirmado nesta revisão |
|---|---|---|
| Recrutamento/Vagas | `https://recrutamento.ctprice.com.br/vagas` | **Funcional** — carregou normalmente, título "CT Price - Gestão de Currículos" |
| Área Restrita → Clientes | `https://ctprice.com.br/documentos` | **Quebrado** (404 puro de servidor) — não registrado como válido |
| Área Restrita → Colaboradores | `https://ctprice.com.br/sh-admin` | **Quebrado/exposto** (listagem crua de diretório, sem aplicação funcional) — não registrado como válido |
| Agência de desenvolvimento | `https://agencialester.com.br/` | **Não confirmado** — tentativa de acesso resultou em timeout de navegação (15s) nesta revisão, mesmo resultado da auditoria anterior. Não registrado como válido por não ter sido possível confirmar que o destino existe/responde. |

**Campos afetados em `config/company.php`:** `sistemas_externos.recrutamento` preenchido (confirmado). Os demais três permanecem `TODO`.

---

## 6. Achado adicional (fora do escopo de `config/company.php`) — menu do rodapé difere nos posts de blog

Não é um dado global da empresa, mas foi observado durante esta investigação e vale registrar para uma futura revisão de `config/menu.php`: o footer dos **3 posts de blog** (template compartilhado) lista um menu secundário com rótulos diferentes dos usados no footer das páginas institucionais:

- Páginas institucionais: "Início · A CT Price · Nossos Clientes · Nossos Parceiros · Fale Conosco · Informações · Trabalhe Conosco · Benefícios"
- Posts de blog: "Início · Sobre nós · Nossos Serviços · Clientes · Parceiros · Fale Conosco · Informações · Vagas"

Endereço, e-mails, responsável técnico e copyright do footer são idênticos entre os dois grupos — só os rótulos/itens do menu secundário diferem. Não alterado em `config/menu.php` nesta etapa (fora do escopo desta tarefa).
