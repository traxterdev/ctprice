<?php
/**
 * database/MigrationRunner.php
 *
 * Lógica reutilizável de aplicação de migrations — extraída de database/migrate.php (que
 * continua CLI-only e agora só chama este runner) para ser reaproveitada também por
 * admin/aplicar-atualizacao.php (ferramenta TEMPORÁRIA de implantação — ver o cabeçalho desse
 * arquivo). Mesmo comportamento de sempre: idempotente via `schema_migrations`, sem transação
 * envolvendo o DDL (CREATE TABLE do MySQL/MariaDB provoca commit implícito por si só — envolver
 * isto num BEGIN/COMMIT não protegeria nada).
 *
 * NÃO é exposto por HTTP diretamente — coberto pela mesma regra do .htaccess raiz que já
 * bloqueia todo o diretório `database/` (`RewriteRule ^(...|database|...)(/|$) - [F,L,NC]`). Só é
 * alcançável via `require` a partir de outro script PHP (CLI ou admin autenticado).
 */

declare(strict_types=1);

final class MigrationRunner
{
    /**
     * Nomes (basename) das migrations em `$migrationsDir` ainda não registradas em
     * `schema_migrations`, na ordem em que seriam aplicadas — só LEITURA, não aplica nada.
     *
     * @return list<string>
     */
    public static function pending(PDO $pdo, string $migrationsDir): array
    {
        self::ensureTable($pdo);
        $appliedSet = array_flip(
            $pdo->query('SELECT migration FROM schema_migrations')->fetchAll(PDO::FETCH_COLUMN)
        );

        $pending = [];
        foreach (self::files($migrationsDir) as $file) {
            $name = basename($file);
            if (!isset($appliedSet[$name])) {
                $pending[] = $name;
            }
        }
        return $pending;
    }

    /**
     * Aplica todas as migrations de `$migrationsDir` ainda não registradas — idempotente
     * (reexecutar não reaplica as já registradas). Lança em caso de falha (o chamador decide como
     * reportar; nenhuma migration após a que falhou é tentada).
     *
     * @return array{applied: list<string>, already: list<string>, total: int}
     */
    public static function run(PDO $pdo, string $migrationsDir): array
    {
        self::ensureTable($pdo);
        $appliedSet = array_flip(
            $pdo->query('SELECT migration FROM schema_migrations')->fetchAll(PDO::FETCH_COLUMN)
        );

        $files = self::files($migrationsDir);
        $applied = [];
        $already = [];

        foreach ($files as $file) {
            $name = basename($file);

            if (isset($appliedSet[$name])) {
                $already[] = $name;
                continue;
            }

            $sql = file_get_contents($file);
            if ($sql === false || trim($sql) === '') {
                throw new RuntimeException("Não foi possível ler a migration: $name");
            }

            $pdo->exec($sql);
            $stmt = $pdo->prepare('INSERT INTO schema_migrations (migration) VALUES (:migration)');
            $stmt->execute(['migration' => $name]);
            $applied[] = $name;
        }

        return ['applied' => $applied, 'already' => $already, 'total' => count($files)];
    }

    /** @return list<string> */
    private static function files(string $migrationsDir): array
    {
        $files = glob($migrationsDir . '/*.sql') ?: [];
        sort($files, SORT_STRING);
        return $files;
    }

    private static function ensureTable(PDO $pdo): void
    {
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS schema_migrations (
                migration VARCHAR(255) NOT NULL,
                applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (migration)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
        );
    }
}
