<?php
/**
 * admin/hero/index.php
 *
 * Listagem administrativa dos Banners da Home (Hero) — mesmo padrão de admin/partners/index.php
 * (uma grade só, sem categorias). Reordenação por setas subir/descer (sem drag-and-drop nesta
 * primeira versão, conforme pedido).
 */

declare(strict_types=1);

require __DIR__ . '/../../config/bootstrap.php';
require __DIR__ . '/../../includes/AdminAuth.php';
require __DIR__ . '/../../repositories/HeroSlideRepository.php';

$adminCurrentUser = admin_require_login();
$csrfToken = admin_csrf_token();

$slides = [];
$dbError = false;

try {
    $slides = (new HeroSlideRepository())->allForAdmin();
} catch (Throwable $e) {
    error_log('CT Price CMS [admin/hero]: falha ao listar — ' . $e->getMessage());
    $dbError = true;
}

$adminPageTitle = 'Banners da Home';
$adminActiveMenu = 'hero';
require __DIR__ . '/../includes/layout-header.php';
?>
<div class="admin-header">
    <div>
        <h1>Banners da Home</h1>
        <p>Slides exibidos no topo da Home (Hero), em ordem. Desativados não aparecem no site.</p>
    </div>
    <a class="admin-btn admin-btn--primary" href="<?= BASE_URL ?>/admin/hero/form.php">+ Novo banner</a>
</div>

<?php if ($dbError): ?>
<div class="admin-alert admin-alert--error">Não foi possível carregar a lista agora. Tente novamente em instantes.</div>
<?php else: ?>
    <?php if (!$slides): ?>
    <p class="admin-empty-state">Nenhum banner cadastrado ainda.</p>
    <?php else: ?>
    <div class="admin-panel">
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Imagem</th>
                        <th>Título</th>
                        <th>Ordem</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($slides as $index => $slide): ?>
                    <tr>
                        <td>
                            <img class="admin-table__logo" src="<?= BASE_URL ?>/<?= htmlspecialchars($slide['imagem_path'], ENT_QUOTES, 'UTF-8') ?>" alt="">
                        </td>
                        <td class="admin-table__name">
                            <span class="admin-table__truncate" style="--truncate-width:340px" title="<?= htmlspecialchars(strip_tags($slide['titulo']), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(strip_tags($slide['titulo']), ENT_QUOTES, 'UTF-8') ?></span>
                        </td>
                        <td>
                            <div class="admin-table__actions">
                                <form method="post" action="<?= BASE_URL ?>/admin/hero/move.php">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="id" value="<?= (int) $slide['id'] ?>">
                                    <input type="hidden" name="direction" value="up">
                                    <button type="submit" class="admin-btn admin-btn--outline admin-btn--sm" <?= $index === 0 ? 'disabled' : '' ?> aria-label="Subir">&uarr;</button>
                                </form>
                                <form method="post" action="<?= BASE_URL ?>/admin/hero/move.php">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="id" value="<?= (int) $slide['id'] ?>">
                                    <input type="hidden" name="direction" value="down">
                                    <button type="submit" class="admin-btn admin-btn--outline admin-btn--sm" <?= $index === count($slides) - 1 ? 'disabled' : '' ?> aria-label="Descer">&darr;</button>
                                </form>
                            </div>
                        </td>
                        <td>
                            <span class="admin-badge <?= ((int) $slide['ativo'] === 1) ? 'admin-badge--active' : 'admin-badge--inactive' ?>">
                                <?= ((int) $slide['ativo'] === 1) ? 'Ativo' : 'Inativo' ?>
                            </span>
                        </td>
                        <td>
                            <div class="admin-table__actions">
                                <a class="admin-btn admin-btn--outline admin-btn--sm" href="<?= BASE_URL ?>/admin/hero/form.php?id=<?= (int) $slide['id'] ?>">Editar</a>
                                <form method="post" action="<?= BASE_URL ?>/admin/hero/toggle.php">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="id" value="<?= (int) $slide['id'] ?>">
                                    <button type="submit" class="admin-btn admin-btn--outline admin-btn--sm">
                                        <?= ((int) $slide['ativo'] === 1) ? 'Desativar' : 'Ativar' ?>
                                    </button>
                                </form>
                                <form method="post" action="<?= BASE_URL ?>/admin/hero/delete.php" data-confirm="Excluir definitivamente este banner? Esta ação não pode ser desfeita.">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="id" value="<?= (int) $slide['id'] ?>">
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
<?php endif; ?>

<?php require __DIR__ . '/../includes/layout-footer.php'; ?>
