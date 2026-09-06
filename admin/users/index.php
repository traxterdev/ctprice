<?php
/**
 * admin/users/index.php — listagem administrativa de Administradores.
 *
 * Sem setas de ordenação (lista por nome — ver AdminUserRepository). Botões de "Desativar"/
 * "Excluir" da PRÓPRIA conta e do ÚLTIMO administrador ativo já vêm desabilitados aqui (mesma
 * regra reforçada de novo, server-side, em toggle.php/delete.php — nunca confia só na UI).
 */

declare(strict_types=1);

require __DIR__ . '/../../config/bootstrap.php';
require __DIR__ . '/../../includes/AdminAuth.php';
require __DIR__ . '/../../repositories/AdminUserRepository.php';

$adminCurrentUser = admin_require_login();
$csrfToken = admin_csrf_token();

$users = [];
$dbError = false;
try {
    $repository = new AdminUserRepository();
    $users = $repository->allForAdmin();
    $activeCount = $repository->countActive();
} catch (Throwable $e) {
    error_log('CT Price CMS [admin/users]: falha ao listar — ' . $e->getMessage());
    $dbError = true;
    $activeCount = 0;
}

$adminPageTitle = 'Administradores';
$adminActiveMenu = 'users';
require __DIR__ . '/../includes/layout-header.php';
?>
<div class="admin-header">
    <div>
        <h1>Administradores</h1>
        <p>Acesso ao painel administrativo. Um único perfil ("Administrador") nesta fase.</p>
    </div>
    <a class="admin-btn admin-btn--primary" href="<?= BASE_URL ?>/admin/users/form.php">+ Novo administrador</a>
</div>

<?php if ($dbError): ?>
<div class="admin-alert admin-alert--error">Não foi possível carregar a lista agora.</div>
<?php else: ?>
<div class="admin-panel">
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th>Nome</th><th>E-mail</th><th>Último login</th><th>Status</th><th>Ações</th></tr></thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <?php
                    $isSelf = (int) $user['id'] === (int) $adminCurrentUser['id'];
                    $isActive = (int) $user['ativo'] === 1;
                    // Última conta ativa: não pode ser desativada/excluída por NINGUÉM (não é
                    // só uma restrição sobre a própria conta) — ver §1 da tarefa.
                    $isLastActive = $isActive && $activeCount <= 1;
                    $blockToggleOff = $isActive && ($isSelf || $isLastActive);
                    $blockDelete = $isSelf || $isLastActive;
                ?>
                <tr>
                    <td class="admin-table__name"><?= htmlspecialchars($user['nome'], ENT_QUOTES, 'UTF-8') ?><?= $isSelf ? ' <span style="color:#7A8A85;">(você)</span>' : '' ?></td>
                    <td class="admin-table__name"><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= $user['ultimo_login_em'] ? htmlspecialchars((new DateTimeImmutable($user['ultimo_login_em']))->format('d/m/Y H:i'), ENT_QUOTES, 'UTF-8') : '—' ?></td>
                    <td>
                        <span class="admin-badge <?= $isActive ? 'admin-badge--active' : 'admin-badge--inactive' ?>">
                            <?= $isActive ? 'Ativo' : 'Inativo' ?>
                        </span>
                    </td>
                    <td>
                        <div class="admin-table__actions">
                            <a class="admin-btn admin-btn--outline admin-btn--sm" href="<?= BASE_URL ?>/admin/users/form.php?id=<?= (int) $user['id'] ?>">Editar</a>
                            <a class="admin-btn admin-btn--outline admin-btn--sm" href="<?= BASE_URL ?>/admin/users/reset-password.php?id=<?= (int) $user['id'] ?>">Redefinir senha</a>
                            <form method="post" action="<?= BASE_URL ?>/admin/users/toggle.php">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
                                <button type="submit" class="admin-btn admin-btn--outline admin-btn--sm" <?= $blockToggleOff ? 'disabled title="' . ($isSelf ? 'Você não pode desativar sua própria conta.' : 'Precisa haver ao menos um administrador ativo.') . '"' : '' ?>>
                                    <?= $isActive ? 'Desativar' : 'Ativar' ?>
                                </button>
                            </form>
                            <form method="post" action="<?= BASE_URL ?>/admin/users/delete.php" data-confirm="Excluir definitivamente este administrador? Esta ação não pode ser desfeita.">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
                                <button type="submit" class="admin-btn admin-btn--danger admin-btn--sm" <?= $blockDelete ? 'disabled title="' . ($isSelf ? 'Você não pode excluir sua própria conta.' : 'Precisa haver ao menos um administrador ativo.') . '"' : '' ?>>Excluir</button>
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
