<?php
/**
 * admin/hero/form.php
 *
 * Formulário de criação/edição de um Banner da Home — mesmo padrão de admin/partners/form.php.
 * `titulo` é um <textarea> simples (não um editor visual): aceita só `<br>` (quebra de linha) e
 * `<span class="hero-slide__highlight">texto</span>` (destaque, mesmo estilo do banner atual) —
 * tudo o mais é removido no servidor por ctprice_sanitize_hero_title_html() (admin/hero/save.php),
 * nunca confiado como veio do formulário.
 *
 * AJUSTE (2026-09-22, ajuste pontual): o usuário administrativo não deve precisar ler/escrever
 * essas tags à mão — a toolbar `[data-hero-toolbar="titulo"]` acima do textarea (botões
 * "Destaque"/"Quebra de linha") insere/remove exatamente esse mesmo markup na seleção atual via
 * JS puro (ver assets/js/admin.js, sem biblioteca nova) — é só uma AJUDA de edição por cima do
 * mesmo textarea/mesmo valor enviado; a validação real continua 100% no servidor
 * (ctprice_sanitize_hero_title_html()), nunca confiada ao que o JS produziu no navegador.
 */

declare(strict_types=1);

require __DIR__ . '/../../config/bootstrap.php';
require __DIR__ . '/../../includes/AdminAuth.php';
require __DIR__ . '/../../repositories/HeroSlideRepository.php';

$adminCurrentUser = admin_require_login();
$csrfToken = admin_csrf_token();

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$slide = null;
$dbError = false;

if ($id !== null) {
    try {
        $slide = (new HeroSlideRepository())->find($id);
    } catch (Throwable $e) {
        error_log('CT Price CMS [admin/hero/form]: falha ao buscar — ' . $e->getMessage());
        $dbError = true;
    }
    if (!$dbError && !$slide) {
        admin_flash_set('error', 'Banner não encontrado.');
        header('Location: ' . BASE_URL . '/admin/hero/', true, 302);
        exit;
    }
}

$isEdit = $slide !== null;
$adminPageTitle = $isEdit ? 'Editar banner' : 'Novo banner';
$adminActiveMenu = 'hero';
require __DIR__ . '/../includes/layout-header.php';
?>
<div class="admin-header">
    <div><h1><?= $isEdit ? 'Editar banner' : 'Novo banner' ?></h1></div>
    <a class="admin-btn admin-btn--outline" href="<?= BASE_URL ?>/admin/hero/">&larr; Voltar</a>
</div>

<?php if ($dbError): ?>
<div class="admin-alert admin-alert--error">Não foi possível carregar o formulário agora. Tente novamente em instantes.</div>
<?php else: ?>
<div class="admin-panel">
    <form class="admin-form" method="post" action="<?= BASE_URL ?>/admin/hero/save.php" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <?php if ($isEdit): ?><input type="hidden" name="id" value="<?= (int) $slide['id'] ?>"><?php endif; ?>

        <div class="admin-form__group">
            <label for="titulo">Título</label>
            <div class="admin-toolbar" data-hero-toolbar="titulo">
                <button type="button" class="admin-btn admin-btn--outline admin-btn--sm" data-hero-action="highlight">★ Destaque</button>
                <button type="button" class="admin-btn admin-btn--outline admin-btn--sm" data-hero-action="linebreak">↵ Quebra de linha</button>
            </div>
            <textarea id="titulo" name="titulo" rows="3" required style="width:100%; padding:10px 12px; border:1px solid #D5DCDA; border-radius:6px; font-family:inherit; font-size:14px;"><?= htmlspecialchars($slide['titulo'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            <p class="admin-form__hint">
                Selecione um trecho e clique em "Destaque" para deixá-lo em evidência, ou posicione o
                cursor e clique em "Quebra de linha" para separar as linhas. Use os botões acima —
                não é preciso escrever nenhum código.
            </p>
        </div>

        <div class="admin-form__group">
            <label for="texto">Texto (subtítulo/descrição — opcional)</label>
            <textarea id="texto" name="texto" rows="2" style="width:100%; padding:10px 12px; border:1px solid #D5DCDA; border-radius:6px; font-family:inherit; font-size:14px;"><?= htmlspecialchars($slide['texto'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div class="admin-form__group">
            <label for="botao_texto">Texto do botão (opcional)</label>
            <input type="text" id="botao_texto" name="botao_texto" maxlength="190" value="<?= htmlspecialchars($slide['botao_texto'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="admin-form__group">
            <label for="botao_url">URL do botão (obrigatória se houver texto do botão)</label>
            <input type="text" id="botao_url" name="botao_url" maxlength="500" placeholder="https:// ou /caminho-interno/" value="<?= htmlspecialchars($slide['botao_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <p class="admin-form__hint">Pode ser um link interno (ex.: /clientes/) ou externo (https://...). Sem texto do botão, nenhum botão aparece.</p>
        </div>

        <div class="admin-form__group">
            <label for="imagem">Imagem <?= $isEdit ? '(opcional — envie só para substituir)' : '' ?></label>
            <?php if ($isEdit && !empty($slide['imagem_path'])): ?>
            <div class="admin-form__current-logo">
                <img src="<?= BASE_URL ?>/<?= htmlspecialchars($slide['imagem_path'], ENT_QUOTES, 'UTF-8') ?>" alt="">
                <span class="admin-form__hint">Imagem atual</span>
            </div>
            <?php endif; ?>
            <input type="file" id="imagem" name="imagem" accept="image/png,image/jpeg,image/webp" <?= $isEdit ? '' : 'required' ?>>
            <p class="admin-form__hint">PNG, JPEG ou WEBP — máximo 5MB. Recomendado: imagem horizontal em alta resolução, proporção semelhante ao banner atual (1200×600px).</p>
        </div>

        <div class="admin-form__actions">
            <button type="submit" class="admin-btn admin-btn--primary">Salvar</button>
            <a class="admin-btn admin-btn--outline" href="<?= BASE_URL ?>/admin/hero/">Cancelar</a>
        </div>
    </form>
</div>
<?php endif; ?>

<?php require __DIR__ . '/../includes/layout-footer.php'; ?>
