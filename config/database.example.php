<?php
/**
 * config/database.example.php
 *
 * Modelo seguro e VERSIONÁVEL de `config/database.local.php` — que nunca é commitado (ver
 * .gitignore) e nunca deve conter uma credencial real neste arquivo de exemplo.
 *
 * Como usar (desenvolvimento local, MariaDB do Laragon):
 *
 *   1. Copie este arquivo para config/database.local.php.
 *   2. Preencha 'username'/'password' com um usuário dedicado ao banco `ctprice_site`
 *      (nunca o `root` do MariaDB em produção; localmente é aceitável só para agilizar o
 *      desenvolvimento, mas prefira também um usuário próprio — ver docs/cms.md).
 *
 * Em produção: banco, usuário e senha PRÓPRIOS do site (nunca o banco do sistema de RH),
 * fornecidos pelo servidor definitivo da CT Price — nunca inventados aqui.
 */

return [
    'host' => '127.0.0.1',
    'port' => 3306,
    'database' => 'ctprice_site',
    'username' => 'ctprice_site',
    'password' => '',
    'charset' => 'utf8mb4',
];
