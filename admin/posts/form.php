<?php
/**
 * admin/posts/form.php — criação/edição de um post do blog.
 *
 * `slug` só é editável na CRIAÇÃO (preserva os 3 slugs históricos existentes — nunca deve ser
 * possível quebrar a URL de um post publicado editando o título depois). Ver admin/posts/save.php
 * para a validação completa (unicidade, formato, lista de reservados).
 */

declare(strict_types=1);

require __DIR__ . '/../../config/bootstrap.php';
require __DIR__ . '/../../includes/AdminAuth.php';
require __DIR__ . '/../../repositories/BlogPostRepository.php';

$adminCurrentUser = admin_require_login();
$csrfToken = admin_csrf_token();

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$post = null;
$dbError = false;

if ($id !== null) {
    try {
        $post = (new BlogPostRepository())->find($id);
    } catch (Throwable $e) {
        error_log('CT Price CMS [admin/posts/form]: ' . $e->getMessage());
        $dbError = true;
    }
    if (!$dbError && !$post) {
        admin_flash_set('error', 'Post não encontrado.');
        header('Location: ' . BASE_URL . '/admin/posts/', true, 302);
        exit;
    }
}

$isEdit = $post !== null;
$publishedAtValue = $isEdit
    ? (new DateTimeImmutable($post['published_at']))->format('Y-m-d\TH:i')
    : (new DateTimeImmutable('now'))->format('Y-m-d\TH:i');

$adminPageTitle = $isEdit ? 'Editar post' : 'Novo post';
$adminActiveMenu = 'posts';
require __DIR__ . '/../includes/layout-header.php';
?>
<div class="admin-header">
    <div><h1><?= $isEdit ? 'Editar post' : 'Novo post' ?></h1></div>
    <a class="admin-btn admin-btn--outline" href="<?= BASE_URL ?>/admin/posts/">&larr; Voltar</a>
</div>

<?php if ($dbError): ?>
<div class="admin-alert admin-alert--error">Não foi possível carregar o formulário agora.</div>
<?php else: ?>
<div class="admin-panel">
    <form class="admin-form" method="post" action="<?= BASE_URL ?>/admin/posts/save.php" enctype="multipart/form-data" style="max-width:760px;">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <?php if ($isEdit): ?><input type="hidden" name="id" value="<?= (int) $post['id'] ?>"><?php endif; ?>

        <div class="admin-form__group">
            <label for="titulo">Título</label>
            <input type="text" id="titulo" name="titulo" required maxlength="255" value="<?= htmlspecialchars($post['titulo'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="admin-form__group">
            <label for="slug">Slug (URL) <?= $isEdit ? '— não pode ser alterado depois de criado' : '' ?></label>
            <input type="text" id="slug" name="slug" required maxlength="255" pattern="[a-z0-9]+(-[a-z0-9]+)*"
                value="<?= htmlspecialchars($post['slug'] ?? '', ENT_QUOTES, 'UTF-8') ?>" <?= $isEdit ? 'readonly style="background:#F4F6F5;"' : '' ?>>
            <p class="admin-form__hint"><?= $isEdit ? 'A URL pública é ' . htmlspecialchars(BASE_URL . '/' . $post['slug'] . '/', ENT_QUOTES, 'UTF-8') . '.' : 'Só letras minúsculas, números e hífen (ex.: meu-novo-artigo). Deixe em branco para gerar a partir do título.' ?></p>
        </div>

        <div class="admin-form__group">
            <label for="categoria">Categoria (selo exibido no card)</label>
            <input type="text" id="categoria" name="categoria" required maxlength="60" placeholder="ex.: INFORMATIVO" value="<?= htmlspecialchars($post['categoria'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="admin-form__group">
            <label for="excerpt">Resumo (excerpt — usado no card e na meta description)</label>
            <textarea id="excerpt" name="excerpt" rows="2" required maxlength="500" style="width:100%; padding:10px 12px; border:1px solid #D5DCDA; border-radius:6px; font-family:inherit; font-size:14px;"><?= htmlspecialchars($post['excerpt'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div class="admin-form__group">
            <label for="published_at">Data de publicação</label>
            <input type="datetime-local" id="published_at" name="published_at" required value="<?= htmlspecialchars($publishedAtValue, ENT_QUOTES, 'UTF-8') ?>">
            <p class="admin-form__hint">Uma data futura mantém o post oculto do público até a hora chegar (mesmo que "Publicado" esteja marcado).</p>
        </div>

        <div class="admin-form__group">
            <label for="imagem">Imagem do card <?= $isEdit ? '(opcional — envie só para substituir)' : '' ?></label>
            <?php if ($isEdit && !empty($post['imagem_path'])): ?>
            <div class="admin-form__current-logo">
                <img src="<?= BASE_URL ?>/<?= htmlspecialchars($post['imagem_path'], ENT_QUOTES, 'UTF-8') ?>" alt="">
                <span class="admin-form__hint">Imagem atual</span>
            </div>
            <?php endif; ?>
            <input type="file" id="imagem" name="imagem" accept="image/png,image/jpeg,image/webp" <?= $isEdit ? '' : 'required' ?>>
            <p class="admin-form__hint">PNG, JPEG ou WEBP — máximo 5MB.</p>
        </div>

        <div class="admin-form__group">
            <label for="body_html">Conteúdo</label>
            <textarea id="body_html" name="body_html" rows="14" required style="width:100%; padding:10px 12px; border:1px solid #D5DCDA; border-radius:6px; font-family:inherit; font-size:14px;"><?= htmlspecialchars($post['body_html'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            <p class="admin-form__hint">HTML simples — apenas &lt;p&gt;, &lt;strong&gt;, &lt;em&gt;, &lt;ul&gt;, &lt;ol&gt;, &lt;li&gt;, &lt;a href&gt;, &lt;h2&gt;, &lt;h3&gt;, &lt;br&gt; são mantidos; qualquer outra tag (script, iframe, estilos, etc.) é removida automaticamente ao salvar.</p>
        </div>

        <div class="admin-form__actions">
            <button type="submit" class="admin-btn admin-btn--primary">Salvar</button>
            <a class="admin-btn admin-btn--outline" href="<?= BASE_URL ?>/admin/posts/">Cancelar</a>
        </div>
    </form>
</div>
<?php endif; ?>

<?php require __DIR__ . '/../includes/layout-footer.php'; ?>
