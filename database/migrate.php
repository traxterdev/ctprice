<?php
/**
 * database/migrate.php
 *
 * Wrapper CLI — a lógica de aplicar migrations foi extraída para database/MigrationRunner.php
 * (idêntica à anterior, só reorganizada) para ser reaproveitada também por
 * admin/aplicar-atualizacao.php (ferramenta TEMPORÁRIA de implantação — ver seu cabeçalho).
 * Este script continua CLI-only e é a única forma de rodar migrations via terminal, exatamente
 * como antes.
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
require __DIR__ . '/MigrationRunner.php';

$migrationsDir = __DIR__ . '/migrations';

try {
    $result = MigrationRunner::run(Database::connection(), $migrationsDir);
} catch (Throwable $e) {
    fwrite(STDERR, "FALHOU\n" . $e->getMessage() . "\n");
    exit(1);
}

if ($result['total'] === 0) {
    echo "Nenhum arquivo de migration encontrado em database/migrations/.\n";
    exit(0);
}

foreach ($result['already'] as $name) {
    echo "  já aplicada: $name\n";
}
foreach ($result['applied'] as $name) {
    echo "  aplicando: $name ... OK\n";
}

echo "\n" . count($result['applied']) . ' migration(s) aplicada(s) agora. ' . $result['total'] . " no total.\n";
