<?php
/**
 * admin/partners/index.php
 *
 * Listagem administrativa de Parceiros — DUAS grades separadas (Ferramentas/Parceiros, mesma
 * semântica de config/partners.php e de /parcerias/) porque a reordenação é sempre relativa à
 * própria categoria (ver repositories/PartnerRepository::moveUp/moveDown).
 */

declare(strict_types=1);

require __DIR__ . '/../../config/bootstrap.php';
require __DIR__ . '/../../includes/AdminAuth.php';
require __DIR__ . '/../../repositories/PartnerRepository.php';

$adminCurrentUser = admin_require_login();
$csrfToken = admin_csrf_token();

$categoryLabels = ['tools' => 'Ferramentas', 'companies' => 'Parceiros'];
$partnersByCategory = ['tools' => [], 'companies' => []];
$dbError = false;

try {
    $repository = new PartnerRepository();
    foreach (array_keys($categoryLabels) as $categoria) {
        $partnersByCategory[$categoria] = $repository->allForAdmin($categoria);
    }
} catch (Throwable $e) {
    error_log('CT Price CMS [admin/partners]: falha ao listar — ' . $e->getMessage());
    $dbError = true;
}

$adminPageTitle = 'Parceiros';
$adminActiveMenu = 'partners';
require __DIR__ . '/../includes/layout-header.php';
?>
<div class="admin-header">
    <div>
        <h1>Parceiros</h1>
        <p>Exibidos em /parcerias/, em duas grades: Ferramentas e Parceiros.</p>
    </div>
    <a class="admin-btn admin-btn--primary" href="<?= BASE_URL ?>/admin/partners/form.php">+ Novo parceiro</a>
</div>

<?php if ($dbError): ?>
<div class="admin-alert admin-alert--error">Não foi possível carregar a lista agora. Tente novamente em instantes.</div>
<?php else: ?>
    <?php foreach ($categoryLabels as $categoria => $label): ?>
    <?php $items = $partnersByCategory[$categoria]; ?>
    <div class="admin-panel" style="margin-bottom:24px;">
        <div class="admin-panel__toolbar">
            <h2><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?> (<?= count($items) ?>)</h2>
        </div>
        <?php if (!$items): ?>
        <p class="admin-empty-state">Nenhum item nesta categoria ainda.</p>
        <?php else: ?>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Logo</th>
                        <th>Nome</th>
                        <th>URL</th>
                        <th>Ordem</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $index => $item): ?>
                    <tr>
                        <td>
                            <img class="admin-table__logo" src="<?= BASE_URL ?>/<?= htmlspecialchars($item['logo_path'], ENT_QUOTES, 'UTF-8') ?>" alt="">
                        </td>
                        <td class="admin-table__name"><?= htmlspecialchars($item['nome'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <?php if ($item['url']): ?>
                            <span class="admin-table__truncate" style="--truncate-width:340px" title="<?= htmlspecialchars($item['url'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($item['url'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php else: ?>
                            <span style="color:#9AA6A2">— sem link —</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="admin-table__actions">
                                <form method="post" action="<?= BASE_URL ?>/admin/partners/move.php">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                    <input type="hidden" name="direction" value="up">
                                    <button type="submit" class="admin-btn admin-btn--outline admin-btn--sm" <?= $index === 0 ? 'disabled' : '' ?> aria-label="Subir">&uarr;</button>
                                </form>
                                <form method="post" action="<?= BASE_URL ?>/admin/partners/move.php">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                    <input type="hidden" name="direction" value="down">
                                    <button type="submit" class="admin-btn admin-btn--outline admin-btn--sm" <?= $index === count($items) - 1 ? 'disabled' : '' ?> aria-label="Descer">&darr;</button>
                                </form>
                            </div>
                        </td>
                        <td>
                            <span class="admin-badge <?= ((int) $item['ativo'] === 1) ? 'admin-badge--active' : 'admin-badge--inactive' ?>">
                                <?= ((int) $item['ativo'] === 1) ? 'Ativo' : 'Inativo' ?>
                            </span>
                        </td>
                        <td>
                            <div class="admin-table__actions">
                                <a class="admin-btn admin-btn--outline admin-btn--sm" href="<?= BASE_URL ?>/admin/partners/form.php?id=<?= (int) $item['id'] ?>">Editar</a>
                                <form method="post" action="<?= BASE_URL ?>/admin/partners/toggle.php">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                    <button type="submit" class="admin-btn admin-btn--outline admin-btn--sm">
                                        <?= ((int) $item['ativo'] === 1) ? 'Desativar' : 'Ativar' ?>
                                    </button>
                                </form>
                                <form method="post" action="<?= BASE_URL ?>/admin/partners/delete.php" data-confirm="Excluir definitivamente este parceiro? Esta ação não pode ser desfeita.">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                    <button type="submit" class="admin-btn admin-btn--danger admin-btn--sm">Excluir</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php require __DIR__ . '/../includes/layout-footer.php'; ?>
