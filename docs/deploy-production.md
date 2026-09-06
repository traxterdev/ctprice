# Publicação em produção — CT Price

Este documento define o que vai para produção, o que não vai, e como gerar um pacote de deploy
limpo. Correção originada do achado P0 da auditoria global
(`docs/reference/global-final-audit.md`) — detalhes da correção em
`docs/reference/deploy-security-validation.md`.

Duas camadas de proteção, propositalmente redundantes (defesa em profundidade — nenhuma das duas
substitui a outra):

1. **Estratégia de publicação** (esta seção) — o pacote enviado ao servidor já não contém os
   arquivos internos, na origem.
2. **`.htaccess`** (raiz do projeto) — mesmo que um arquivo interno chegue ao servidor por
   engano (cópia manual, engano de deploy, arquivo novo esquecido fora do `.gitattributes`), o
   Apache nega acesso HTTP a ele.

---

## Por que não há `/public/` ainda

A arquitetura atual usa a raiz do projeto como `DocumentRoot` (sem uma pasta `public/` separada
contendo só o que é servível). Migrar para essa arquitetura é uma mudança estrutural grande,
fora do escopo desta correção — ela é compatível com o projeto como está hoje. Se uma
reestruturação com `DocumentRoot` apontando para uma subpasta pública separada for desejada no
futuro, deve ser tratada como uma melhoria própria, documentada e planejada à parte (não uma
consequência automática desta correção).

---

## Estratégia de publicação: `git archive`

Sem CI/CD, sem build, sem npm, sem Docker — usa só um recurso nativo do próprio Git.
`.gitattributes` (raiz do projeto) marca os caminhos internos com `export-ignore`, que o Git
respeita automaticamente ao gerar um pacote com `git archive`. O pacote resultante já nasce sem
`docs/`, `CLAUDE.md`, `README.md`, `.serena/` — nenhum script de exclusão próprio foi criado.

```bash
# a partir de um commit já com .htaccess/.gitattributes commitados:
git archive --format=zip -o ctprice-deploy.zip HEAD
# (ou --format=tar.gz -o ctprice-deploy.tar.gz HEAD)
```

Extrair `ctprice-deploy.zip` diretamente no `DocumentRoot` do servidor de produção.

### Pré-requisito importante
`git archive` só inclui/exclui o que já está **commitado** — `.htaccess` e `.gitattributes` (os
2 arquivos desta correção) precisam ser adicionados a um commit antes que este fluxo funcione de
ponta a ponta. Enquanto não commitados, gerar o pacote com `git archive HEAD` ainda vai **omitir
corretamente** `docs/`/`CLAUDE.md`/`README.md`/`.serena/` (o `.gitattributes` já commitado ativa
a regra), mas o `.htaccess` em si ficaria de fora do pacote — verifique que o pacote final contém
`.htaccess` antes de publicar.

### Teste de validação já realizado (não regenerar rotineiramente)
`git archive --worktree-attributes --format=zip -o teste.zip HEAD` foi executado durante esta
correção (arquivo de teste apagado logo em seguida, nunca ficou no repositório) — confirmado: 0
ocorrências de `docs/`, `CLAUDE.md`, `README.md`, `.serena/` no pacote resultante; arquivos reais
da aplicação (`config/bootstrap.php`, `*/index.php` etc.) presentes normalmente.

---

## Enviar (faz parte do pacote de produção)

- Diretórios de página (`/<slug>/index.php` na raiz — institucionais e os 3 posts do blog).
- `components/`, `includes/`, `config/`, `content/`, `blog/` — **continuam existindo no
  servidor**, pois o PHP precisa deles via `require`/`include` (leitura direta do filesystem,
  nunca via HTTP). "Não publicar diretamente" nesta lista significa "não expor por HTTP", não
  "não enviar ao servidor" — ver a seção do `.htaccess` abaixo.
- `assets/` (CSS/JS/imagens/fontes/vendor do Swiper).
- `.htaccess` (raiz) — obrigatório, é o que aplica o bloqueio de HTTP direto no próprio servidor.

## NÃO enviar/publicar diretamente

- `.git/` (histórico completo do repositório).
- `docs/` (auditorias, validações finais, screenshots de referência — documentação interna do
  projeto, não conteúdo do site).
- `CLAUDE.md`, `README.md` — documentação de desenvolvimento.
- `.serena/`, `.claude/` — configuração/cache de ferramentas de desenvolvimento.
- Logs, temporários, dumps, backups, segredos — nenhum existe no repositório hoje (inventariado
  em `docs/reference/deploy-security-validation.md`), mas a regra vale para qualquer um que
  venha a aparecer no futuro.

**Importante**: mesmo que um desses arquivos acabe indo ao servidor por engano (ex.: alguém copia
a pasta de trabalho inteira em vez de usar `git archive`), o `.htaccess` da raiz nega acesso HTTP
a todos eles — ver `docs/reference/deploy-security-validation.md` para as regras exatas e os
testes realizados.

---

## Checklist rápido antes de publicar

1. `.htaccess` e `.gitattributes` estão commitados?
2. `git archive --format=zip -o ctprice-deploy.zip HEAD` gerado a partir do commit correto?
3. Pacote extraído contém `.htaccess` na raiz? (confirme antes de subir — ver pré-requisito acima)
4. Nenhum arquivo `docs/`, `CLAUDE.md`, `README.md`, `.serena/` no pacote?
5. Depois de publicar: `/​.git/config`, `/CLAUDE.md`, `/docs/` retornam algo diferente de 200 no
   servidor real (403/404, nunca o conteúdo)?
