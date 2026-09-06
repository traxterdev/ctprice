<?php
/**
 * admin/partners/form.php
 *
 * Formulário de criação/edição de um Parceiro (ver admin/clients/form.php para o mesmo padrão) —
 * inclui o seletor de categoria (tools/companies), ausente em Clientes.
 */

declare(strict_types=1);

require __DIR__ . '/../../config/bootstrap.php';
require __DIR__ . '/../../includes/AdminAuth.php';
require __DIR__ . '/../../repositories/PartnerRepository.php';

$adminCurrentUser = admin_require_login();
$csrfToken = admin_csrf_token();

$categoryLabels = ['tools' => 'Ferramentas', 'companies' => 'Parceiros'];

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$partner = null;
$dbError = false;

if ($id !== null) {
    try {
        $partner = (new PartnerRepository())->find($id);
    } catch (Throwable $e) {
        error_log('CT Price CMS [admin/partners/form]: falha ao buscar — ' . $e->getMessage());
        $dbError = true;
    }
    if (!$dbError && !$partner) {
        admin_flash_set('error', 'Parceiro não encontrado.');
        header('Location: ' . BASE_URL . '/admin/partners/', true, 302);
        exit;
    }
}

$isEdit = $partner !== null;
$selectedCategory = $partner['categoria'] ?? ($_GET['categoria'] ?? 'companies');
if (!isset($categoryLabels[$selectedCategory])) {
    $selectedCategory = 'companies';
}

$adminPageTitle = $isEdit ? 'Editar parceiro' : 'Novo parceiro';
$adminActiveMenu = 'partners';
require __DIR__ . '/../includes/layout-header.php';
?>
<div class="admin-header">
    <div>
        <h1><?= $isEdit ? 'Editar parceiro' : 'Novo parceiro' ?></h1>
    </div>
    <a class="admin-btn admin-btn--outline" href="<?= BASE_URL ?>/admin/partners/">&larr; Voltar</a>
</div>

<?php if ($dbError): ?>
<div class="admin-alert admin-alert--error">Não foi possível carregar o formulário agora. Tente novamente em instantes.</div>
<?php else: ?>
<div class="admin-panel">
    <form class="admin-form" method="post" action="<?= BASE_URL ?>/admin/partners/save.php" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= (int) $partner['id'] ?>">
        <?php endif; ?>

        <div class="admin-form__group">
            <label for="nome">Nome</label>
            <input type="text" id="nome" name="nome" required maxlength="190" value="<?= htmlspecialchars($partner['nome'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="admin-form__group">
            <label for="categoria">Categoria</label>
            <select id="categoria" name="categoria">
                <?php foreach ($categoryLabels as $value => $label): ?>
                <option value="<?= $value ?>" <?= $selectedCategory === $value ? 'selected' : '' ?>><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="admin-form__group">
            <label for="url">URL (opcional — deixe em branco se não houver link, como "Auditto")</label>
            <input type="url" id="url" name="url" maxlength="500" placeholder="https://" value="<?= htmlspecialchars($partner['url'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="admin-form__group">
            <label for="logo">Logo <?= $isEdit ? '(opcional — envie só para substituir)' : '' ?></label>
            <?php if ($isEdit && !empty($partner['logo_path'])): ?>
            <div class="admin-form__current-logo">
                <img src="<?= BASE_URL ?>/<?= htmlspecialchars($partner['logo_path'], ENT_QUOTES, 'UTF-8') ?>" alt="">
                <span class="admin-form__hint">Logo atual</span>
            </div>
            <?php endif; ?>
            <input type="file" id="logo" name="logo" accept="image/png,image/jpeg,image/webp" <?= $isEdit ? '' : 'required' ?>>
            <p class="admin-form__hint">PNG, JPEG ou WEBP — máximo 5MB.</p>
        </div>

        <div class="admin-form__actions">
            <button type="submit" class="admin-btn admin-btn--primary">Salvar</button>
            <a class="admin-btn admin-btn--outline" href="<?= BASE_URL ?>/admin/partners/">Cancelar</a>
        </div>
    </form>
</div>
<?php endif; ?>

<?php require __DIR__ . '/../includes/layout-footer.php'; ?>
