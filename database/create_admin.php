<?php
/**
 * database/create_admin.php
 *
 * Cria (ou atualiza a senha de) um administrador do CMS — a única forma prevista de ter o
 * primeiro usuário, propositalmente FORA de qualquer migration versionada (uma senha nunca deve
 * existir em texto no Git, nem com hash — ver docs/cms.md).
 *
 * Uso (CLI, a partir da raiz do projeto):
 *   php database/create_admin.php "Nome Completo" email@ctpricems.com.br "senha-forte-aqui"
 *
 * Se o e-mail já existir, atualiza nome/senha/ativo=1 do registro existente em vez de duplicar
 * (reexecutável com segurança — útil para resetar a senha local sem acessar o banco na mão).
 *
 * Não deve ser exposto por HTTP — bloqueado por .htaccess (diretório `database`) e recusa-se a
 * rodar fora da CLI.
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Este script só pode ser executado via linha de comando.');
}

require __DIR__ . '/../config/bootstrap.php';

$nome = trim((string) ($argv[1] ?? ''));
$email = trim((string) ($argv[2] ?? ''));
$senha = (string) ($argv[3] ?? '');

if ($nome === '' || $email === '' || $senha === '') {
    fwrite(STDERR, "Uso: php database/create_admin.php \"Nome Completo\" email@dominio.com \"senha-forte\"\n");
    exit(1);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "E-mail inválido: $email\n");
    exit(1);
}

if (mb_strlen($senha) < 10) {
    fwrite(STDERR, "Senha muito curta — use pelo menos 10 caracteres.\n");
    exit(1);
}

$pdo = Database::connection();
$hash = password_hash($senha, PASSWORD_DEFAULT);

$existing = $pdo->prepare('SELECT id FROM admin_users WHERE email = :email');
$existing->execute(['email' => $email]);
$existingId = $existing->fetchColumn();

if ($existingId !== false) {
    $stmt = $pdo->prepare(
        'UPDATE admin_users SET nome = :nome, password_hash = :hash, ativo = 1 WHERE id = :id'
    );
    $stmt->execute(['nome' => $nome, 'hash' => $hash, 'id' => $existingId]);
    echo "Administrador existente atualizado (id $existingId): $email\n";
    exit(0);
}

$stmt = $pdo->prepare(
    'INSERT INTO admin_users (nome, email, password_hash, ativo) VALUES (:nome, :email, :hash, 1)'
);
$stmt->execute(['nome' => $nome, 'email' => $email, 'hash' => $hash]);

echo 'Administrador criado (id ' . $pdo->lastInsertId() . "): $email\n";
