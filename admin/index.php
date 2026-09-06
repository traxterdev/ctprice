<?php
/**
 * admin/index.php — Dashboard
 *
 * Totais dos módulos administráveis por este CMS. Nenhum analytics inventado.
 *
 * Vagas/Benefícios NÃO aparecem aqui — decisão de arquitetura: passarão a vir de outro sistema já
 * desenvolvido, não são mais administrados por este CMS (ver comentário de admin/includes/
 * layout-header.php e das tabelas `jobs`/`benefits`, que continuam existindo — a página pública
 * /trabalhe-conosco/ ainda depende delas — só o CRUD administrativo foi retirado).
 */

declare(strict_types=1);

require __DIR__ . '/../config/bootstrap.php';
require __DIR__ . '/../includes/AdminAuth.php';

$adminCurrentUser = admin_require_login();

$activeClients = null;
$activePartners = null;
$activeTestimonials = null;
$publishedPosts = null;
$activeAdmins = null;
$totalClients = null;
$totalPartners = null;
$totalTestimonials = null;
$totalPosts = null;
$dbError = false;

try {
    $pdo = Database::connection();
    $activeClients = (int) $pdo->query('SELECT COUNT(*) FROM clients WHERE ativo = 1')->fetchColumn();
    $activePartners = (int) $pdo->query('SELECT COUNT(*) FROM partners WHERE ativo = 1')->fetchColumn();
    $activeTestimonials = (int) $pdo->query('SELECT COUNT(*) FROM video_testimonials WHERE ativo = 1')->fetchColumn();
    $publishedPosts = (int) $pdo->query('SELECT COUNT(*) FROM blog_posts WHERE ativo = 1 AND published_at <= NOW()')->fetchColumn();
    $activeAdmins = (int) $pdo->query('SELECT COUNT(*) FROM admin_users WHERE ativo = 1')->fetchColumn();
    $totalClients = (int) $pdo->query('SELECT COUNT(*) FROM clients')->fetchColumn();
    $totalPartners = (int) $pdo->query('SELECT COUNT(*) FROM partners')->fetchColumn();
    $totalTestimonials = (int) $pdo->query('SELECT COUNT(*) FROM video_testimonials')->fetchColumn();
    $totalPosts = (int) $pdo->query('SELECT COUNT(*) FROM blog_posts')->fetchColumn();
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

<h2 class="admin-section-title">Conteúdo do site</h2>
<div class="admin-module-list">
    <div class="admin-module-row">
        <span class="admin-module-row__name">Clientes</span>
        <span class="admin-module-row__count"><strong><?= $totalClients ?></strong> cadastrados</span>
        <a href="<?= BASE_URL ?>/admin/clients/" class="admin-btn admin-btn--outline admin-btn--sm">Gerenciar</a>
    </div>
    <div class="admin-module-row">
        <span class="admin-module-row__name">Parceiros</span>
        <span class="admin-module-row__count"><strong><?= $totalPartners ?></strong> cadastrados</span>
        <a href="<?= BASE_URL ?>/admin/partners/" class="admin-btn admin-btn--outline admin-btn--sm">Gerenciar</a>
    </div>
    <div class="admin-module-row">
        <span class="admin-module-row__name">Notícias</span>
        <span class="admin-module-row__count"><strong><?= $totalPosts ?></strong> cadastradas</span>
        <a href="<?= BASE_URL ?>/admin/posts/" class="admin-btn admin-btn--outline admin-btn--sm">Gerenciar</a>
    </div>
    <div class="admin-module-row">
        <span class="admin-module-row__name">Depoimentos</span>
        <span class="admin-module-row__count"><strong><?= $totalTestimonials ?></strong> cadastrados</span>
        <a href="<?= BASE_URL ?>/admin/testimonials/" class="admin-btn admin-btn--outline admin-btn--sm">Gerenciar</a>
    </div>
</div>
<?php endif; ?>

<?php require __DIR__ . '/includes/layout-footer.php'; ?>
