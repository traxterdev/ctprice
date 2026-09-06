<?php
/**
 * includes/Database.php
 *
 * Ponto único de conexão PDO com o MariaDB do CMS (banco `ctprice_site`, totalmente separado do
 * banco do sistema de RH — ver config/database.local.php e docs/cms.md).
 *
 * Responsabilidades desta classe, e só estas (nenhum ORM, nenhuma query builder):
 * - montar o DSN e abrir UMA conexão PDO por requisição (padrão singleton simples);
 * - `utf8mb4` (charset da conexão E da própria conexão MySQL via DSN, não só do PHP);
 * - `PDO::ERRMODE_EXCEPTION` — todo erro de banco vira exceção, nunca um retorno `false` silencioso;
 * - `PDO::ATTR_EMULATE_PREPARES => false` — prepared statements REAIS do driver MySQL, não
 *   emulados pelo PHP (proteção real contra injeção de SQL, não apenas escaping de string);
 * - fuso horário da sessão MySQL coerente com `date_default_timezone_set()` do projeto
 *   (config/bootstrap.php) — evita que `NOW()`/`CURRENT_TIMESTAMP` do banco divirjam do horário
 *   que o PHP calcula para "agora".
 *
 * Falha de conexão: nunca expõe host/usuário/senha/stack trace ao visitante — só registra via
 * `error_log()` e lança uma `RuntimeException` com mensagem genérica, para o chamador decidir como
 * degradar (ver docs/cms.md, "disponibilidade do banco").
 */

final class Database
{
    private static ?PDO $connection = null;

    private function __construct()
    {
        // Classe estática — nunca instanciada.
    }

    public static function connection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $configFile = __DIR__ . '/../config/database.local.php';
        if (!is_file($configFile)) {
            error_log('CT Price CMS: config/database.local.php não encontrado — copie config/database.example.php e preencha as credenciais locais.');
            throw new RuntimeException('Banco de dados indisponível.');
        }

        /** @var array<string, mixed> $config */
        $config = require $configFile;

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $config['host'] ?? '127.0.0.1',
            $config['port'] ?? 3306,
            $config['database'] ?? '',
            $config['charset'] ?? 'utf8mb4'
        );

        try {
            self::$connection = new PDO($dsn, $config['username'] ?? '', $config['password'] ?? '', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                // -04:00 = America/Campo_Grande (Brasil não observa horário de verão desde 2019 —
                // deslocamento fixo o ano todo). Mesmo fuso definido em
                // config/bootstrap.php via date_default_timezone_set().
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '-04:00'",
            ]);
        } catch (PDOException $e) {
            error_log('CT Price CMS: falha ao conectar ao MariaDB — ' . $e->getMessage());
            throw new RuntimeException('Banco de dados indisponível.');
        }

        return self::$connection;
    }
}
