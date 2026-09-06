<?php
/**
 * admin/index.php — Dashboard
 *
 * Só o que a tarefa da sprint pede (§9): total de clientes ativos + total de parceiros ativos.
 * Nenhum analytics inventado.
 */

declare(strict_types=1);

require __DIR__ . '/../config/bootstrap.php';
require __DIR__ . '/../includes/AdminAuth.php';

$adminCurrentUser = admin_require_login();

$activeClients = null;
$activePartners = null;
$activeJobs = null;
$activeBenefits = null;
$activeTestimonials = null;
$publishedPosts = null;
$activeAdmins = null;
$dbError = false;

try {
    $pdo = Database::connection();
    $activeClients = (int) $pdo->query('SELECT COUNT(*) FROM clients WHERE ativo = 1')->fetchColumn();
    $activePartners = (int) $pdo->query('SELECT COUNT(*) FROM partners WHERE ativo = 1')->fetchColumn();
    $activeJobs = (int) $pdo->query('SELECT COUNT(*) FROM jobs WHERE ativo = 1')->fetchColumn();
    $activeBenefits = (int) $pdo->query('SELECT COUNT(*) FROM benefits WHERE ativo = 1')->fetchColumn();
    $activeTestimonials = (int) $pdo->query('SELECT COUNT(*) FROM video_testimonials WHERE ativo = 1')->fetchColumn();
    $publishedPosts = (int) $pdo->query('SELECT COUNT(*) FROM blog_posts WHERE ativo = 1 AND published_at <= NOW()')->fetchColumn();
    $activeAdmins = (int) $pdo->query('SELECT COUNT(*) FROM admin_users WHERE ativo = 1')->fetchColumn();
} catch (Throwable $e) {
    error_log('CT Price CMS [admin/index]: falha ao consultar totais — ' . $e->getMessage());
    $dbError = true;
}

$adminPageTitle = 'Dashboard';
$adminActiveMenu = 'dashboard';
require __DIR__ . '/includes/layout-header.php';
?>
<div class="admin-header">
    <div>
        <h1>Dashboard</h1>
        <p>Visão geral do conteúdo administrável do site.</p>
    </div>
</div>

<?php if ($dbError): ?>
<div class="admin-alert admin-alert--error">Não foi possível carregar os totais agora. Tente novamente em instantes.</div>
<?php else: ?>
<div class="admin-stats">
    <div class="admin-stat-card">
        <p class="admin-stat-card__label">Clientes ativos</p>
        <p class="admin-stat-card__value"><?= $activeClients ?></p>
    </div>
    <div class="admin-stat-card">
        <p class="admin-stat-card__label">Parceiros ativos</p>
        <p class="admin-stat-card__value"><?= $activePartners ?></p>
    </div>
    <div class="admin-stat-card">
        <p class="admin-stat-card__label">Vagas ativas</p>
        <p class="admin-stat-card__value"><?= $activeJobs ?></p>
    </div>
    <div class="admin-stat-card">
        <p class="admin-stat-card__label">Benefícios ativos</p>
        <p class="admin-stat-card__value"><?= $activeBenefits ?></p>
    </div>
    <div class="admin-stat-card">
        <p class="admin-stat-card__label">Depoimentos ativos</p>
        <p class="admin-stat-card__value"><?= $activeTestimonials ?></p>
    </div>
    <div class="admin-stat-card">
        <p class="admin-stat-card__label">Posts publicados</p>
        <p class="admin-stat-card__value"><?= $publishedPosts ?></p>
    </div>
    <div class="admin-stat-card">
        <p class="admin-stat-card__label">Administradores ativos</p>
        <p class="admin-stat-card__value"><?= $activeAdmins ?></p>
    </div>
</div>
<?php endif; ?>

<?php require __DIR__ . '/includes/layout-footer.php'; ?>
