<?php
/**
 * admin/posts/index.php — listagem administrativa de posts do blog. Sem setas de ordenação (a
 * ordem pública é sempre cronológica por `published_at` — ver repositories/BlogPostRepository.php).
 */

declare(strict_types=1);

require __DIR__ . '/../../config/bootstrap.php';
require __DIR__ . '/../../includes/AdminAuth.php';
require __DIR__ . '/../../repositories/BlogPostRepository.php';

$adminCurrentUser = admin_require_login();
$csrfToken = admin_csrf_token();

$posts = [];
$dbError = false;
try {
    $posts = (new BlogPostRepository())->allForAdmin();
} catch (Throwable $e) {
    error_log('CT Price CMS [admin/posts]: falha ao listar — ' . $e->getMessage());
    $dbError = true;
}

$adminPageTitle = 'Notícias';
$adminActiveMenu = 'posts';
require __DIR__ . '/../includes/layout-header.php';
?>
<div class="admin-header">
    <div>
        <h1>Notícias</h1>
        <p>Exibidas na Home/Informações ("Últimas notícias") e em suas próprias URLs (ex.: /um-slug/).</p>
    </div>
    <a class="admin-btn admin-btn--primary" href="<?= BASE_URL ?>/admin/posts/form.php">+ Novo post</a>
</div>

<?php if ($dbError): ?>
<div class="admin-alert admin-alert--error">Não foi possível carregar a lista agora.</div>
<?php elseif (!$posts): ?>
<div class="admin-panel"><p class="admin-empty-state">Nenhum post cadastrado ainda.</p></div>
<?php else: ?>
<div class="admin-panel">
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th>Título</th><th>Slug</th><th>Publicado em</th><th>Status</th><th>Ações</th></tr></thead>
            <tbody>
                <?php foreach ($posts as $post): ?>
                <?php $isPublished = (int) $post['ativo'] === 1; ?>
                <tr>
                    <td class="admin-table__name"><?= htmlspecialchars($post['titulo'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>/<?= htmlspecialchars($post['slug'], ENT_QUOTES, 'UTF-8') ?>/</td>
                    <td><?= htmlspecialchars((new DateTimeImmutable($post['published_at']))->format('d/m/Y H:i'), ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <span class="admin-badge <?= $isPublished ? 'admin-badge--active' : 'admin-badge--inactive' ?>">
                            <?= $isPublished ? 'Publicado' : 'Rascunho' ?>
                        </span>
                    </td>
                    <td>
                        <div class="admin-table__actions">
                            <a class="admin-btn admin-btn--outline admin-btn--sm" href="<?= BASE_URL ?>/<?= htmlspecialchars($post['slug'], ENT_QUOTES, 'UTF-8') ?>/" target="_blank" rel="noopener noreferrer">Ver</a>
                            <a class="admin-btn admin-btn--outline admin-btn--sm" href="<?= BASE_URL ?>/admin/posts/form.php?id=<?= (int) $post['id'] ?>">Editar</a>
                            <form method="post" action="<?= BASE_URL ?>/admin/posts/toggle.php">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="id" value="<?= (int) $post['id'] ?>">
                                <button type="submit" class="admin-btn admin-btn--outline admin-btn--sm"><?= $isPublished ? 'Despublicar' : 'Publicar' ?></button>
                            </form>
                            <form method="post" action="<?= BASE_URL ?>/admin/posts/delete.php" data-confirm="Excluir definitivamente este post? Esta ação não pode ser desfeita.">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="id" value="<?= (int) $post['id'] ?>">
                                <button type="submit" class="admin-btn admin-btn--danger admin-btn--sm">Excluir</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php require __DIR__ . '/../includes/layout-footer.php'; ?>
