<?php
/**
 * admin/logout.php
 *
 * Encerra a sessão administrativa (destrói a sessão inteira, não só a variável de usuário — ver
 * includes/AdminAuth.php::admin_logout()) e volta para o login.
 */

declare(strict_types=1);

require __DIR__ . '/../config/bootstrap.php';
require __DIR__ . '/../includes/AdminAuth.php';

admin_logout();

header('Location: ' . BASE_URL . '/admin/login.php', true, 302);
exit;
