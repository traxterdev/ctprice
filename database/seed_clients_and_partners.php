<?php
/**
 * database/seed_clients_and_partners.php
 *
 * Importa os dados ESTÁTICOS atuais (config/clients.php e config/partners.php) para as tabelas
 * `clients`/`partners` — fonte inicial real do CMS, não dados inventados (ver docs/cms.md, "fonte
 * canônica").
 *
 * Idempotente: usa `INSERT ... ON DUPLICATE KEY UPDATE`, apoiado nas UNIQUE KEYs das migrations
 * (`clients.logo_path`; `partners.(categoria, logo_path)`) — reexecutar este script não duplica
 * registros; apenas atualiza nome/url/ordem/ativo=1 caso o conteúdo do config tenha mudado desde
 * a última importação.
 *
 * `logo_path` migrado aponta para os arquivos JÁ existentes em assets/images/... (nenhuma imagem
 * é copiada/movida por este script) — os uploads feitos pelo admin depois desta sprint é que
 * passam a viver em assets/uploads/... (ver includes/Uploads.php).
 *
 * Uso (CLI, a partir da raiz do projeto):
 *   php database/seed_clients_and_partners.php
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Este script só pode ser executado via linha de comando.');
}

require __DIR__ . '/../config/bootstrap.php';

$pdo = Database::connection();

// --- Clientes -----------------------------------------------------------------------------
$clients = require __DIR__ . '/../config/clients.php';

$stmtClient = $pdo->prepare(
    'INSERT INTO clients (nome, logo_path, ordem, ativo)
     VALUES (:nome, :logo_path, :ordem, 1)
     ON DUPLICATE KEY UPDATE nome = VALUES(nome), ordem = VALUES(ordem), ativo = 1'
);

$clientCount = 0;
foreach ($clients as $index => $client) {
    $logoPath = 'assets/images/clients/home-carousel/' . $client['file'];
    $stmtClient->execute([
        'nome' => $client['alt'] ?? $client['file'],
        'logo_path' => $logoPath,
        'ordem' => $index,
    ]);
    $clientCount++;
}

echo "Clientes importados/atualizados: $clientCount\n";

// --- Parceiros ------------------------------------------------------------------------------
$partnersData = require __DIR__ . '/../config/partners.php';

$stmtPartner = $pdo->prepare(
    'INSERT INTO partners (nome, categoria, logo_path, url, ordem, ativo)
     VALUES (:nome, :categoria, :logo_path, :url, :ordem, 1)
     ON DUPLICATE KEY UPDATE nome = VALUES(nome), url = VALUES(url), ordem = VALUES(ordem), ativo = 1'
);

$partnerCount = 0;
foreach (['tools', 'companies'] as $categoria) {
    $items = $partnersData[$categoria] ?? [];
    foreach ($items as $index => $item) {
        $logoPath = 'assets/images/partners/' . $categoria . '/' . $item['image'];
        $stmtPartner->execute([
            'nome' => $item['name'],
            'categoria' => $categoria,
            'logo_path' => $logoPath,
            'url' => $item['url'] ?? null,
            'ordem' => $index,
        ]);
        $partnerCount++;
    }
}

echo "Parceiros importados/atualizados: $partnerCount\n";
