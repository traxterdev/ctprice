<?php
/**
 * database/migrate.php
 *
 * Runner de migrations minimalista — NÃO é um framework de migrations (sem down/rollback, sem
 * geração de arquivo, sem dependências entre migrations além da ordem alfabética do nome do
 * arquivo). Cada migration é um `.sql` puro em database/migrations/, numerado por data
 * (AAAA_MM_DD_NNNNNN_descricao.sql) para ordem determinística.
 *
 * Idempotente: uma tabela própria `schema_migrations` registra o que já foi aplicado — rodar este
 * script várias vezes só aplica o que ainda não rodou.
 *
 * Uso (CLI, a partir da raiz do projeto):
 *   php database/migrate.php
 *
 * Não deve ser exposto por HTTP — bloqueado por .htaccess (regra do diretório `database`, ver
 * docs/cms.md) e, por segurança adicional, recusa-se a rodar se chamado via SAPI web.
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Este script só pode ser executado via linha de comando.');
}

require __DIR__ . '/../config/bootstrap.php';

$pdo = Database::connection();

$pdo->exec(
    'CREATE TABLE IF NOT EXISTS schema_migrations (
        migration VARCHAR(255) NOT NULL,
        applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (migration)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
);

$applied = $pdo->query('SELECT migration FROM schema_migrations')->fetchAll(PDO::FETCH_COLUMN);
$appliedSet = array_flip($applied);

$migrationsDir = __DIR__ . '/migrations';
$files = glob($migrationsDir . '/*.sql');
sort($files, SORT_STRING);

if (!$files) {
    echo "Nenhum arquivo de migration encontrado em database/migrations/.\n";
    exit(0);
}

$ranCount = 0;

foreach ($files as $file) {
    $name = basename($file);

    if (isset($appliedSet[$name])) {
        echo "  já aplicada: $name\n";
        continue;
    }

    $sql = file_get_contents($file);
    if ($sql === false || trim($sql) === '') {
        fwrite(STDERR, "  ERRO: não foi possível ler $name\n");
        exit(1);
    }

    echo "  aplicando: $name ... ";

    try {
        // Sem transação: DDL (CREATE TABLE) do MySQL/MariaDB provoca commit implícito por si só
        // (não é transacional) — envolver isto num BEGIN/COMMIT não protegeria nada e só
        // mascararia o erro real num eventual ROLLBACK sem transação ativa.
        $pdo->exec($sql);
        $stmt = $pdo->prepare('INSERT INTO schema_migrations (migration) VALUES (:migration)');
        $stmt->execute(['migration' => $name]);
        echo "OK\n";
        $ranCount++;
    } catch (Throwable $e) {
        fwrite(STDERR, "FALHOU\n" . $e->getMessage() . "\n");
        exit(1);
    }
}

echo "\n$ranCount migration(s) aplicada(s) agora. " . count($files) . " no total.\n";
