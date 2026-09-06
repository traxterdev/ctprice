<?php
/**
 * repositories/ClientRepository.php
 *
 * Única camada que sabe escrever SQL para `clients` — nenhum componente/página monta query
 * própria (ver CLAUDE.md/tarefa da sprint, §19). Sem Service Layer: os métodos abaixo já são a
 * lógica inteira necessária (buscar ativos/ordenados, CRUD administrativo, reordenar) — não há
 * regra de negócio adicional que justifique uma camada extra.
 *
 * Todas as queries usam prepared statements reais (ver includes/Database.php,
 * `PDO::ATTR_EMULATE_PREPARES => false`) — nenhuma interpolação de valor em SQL.
 */

declare(strict_types=1);

final class ClientRepository
{
    /**
     * Clientes ativos, na ordem cadastrada — usado pelo carrossel da Home/Sobre Nós/Informações
     * e pela grade de /clientes/ (que aplica seu próprio embaralhamento determinístico por cima
     * deste resultado, ver components/clients-grid-section.php — inalterado por esta sprint).
     *
     * @return list<array{id:int, nome:string, logo_path:string, site_url:?string, ordem:int}>
     */
    public function allActive(): array
    {
        $stmt = Database::connection()->query(
            'SELECT id, nome, logo_path, site_url, ordem
             FROM clients
             WHERE ativo = 1
             ORDER BY ordem ASC, id ASC'
        );
        return $stmt->fetchAll();
    }

    /**
     * Todos os clientes (ativos e inativos) para a listagem administrativa.
     *
     * @return list<array<string, mixed>>
     */
    public function allForAdmin(): array
    {
        $stmt = Database::connection()->query(
            'SELECT id, nome, logo_path, site_url, ordem, ativo, created_at, updated_at
             FROM clients
             ORDER BY ordem ASC, id ASC'
        );
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM clients WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function nextOrder(): int
    {
        $max = Database::connection()->query('SELECT MAX(ordem) FROM clients')->fetchColumn();
        return $max !== null ? ((int) $max) + 1 : 0;
    }

    public function create(string $nome, string $logoPath, ?string $siteUrl, int $ordem): int
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO clients (nome, logo_path, site_url, ordem, ativo)
             VALUES (:nome, :logo_path, :site_url, :ordem, 1)'
        );
        $stmt->execute([
            'nome' => $nome,
            'logo_path' => $logoPath,
            'site_url' => $siteUrl,
            'ordem' => $ordem,
        ]);
        return (int) Database::connection()->lastInsertId();
    }

    public function update(int $id, string $nome, ?string $logoPath, ?string $siteUrl): void
    {
        if ($logoPath !== null) {
            $stmt = Database::connection()->prepare(
                'UPDATE clients SET nome = :nome, logo_path = :logo_path, site_url = :site_url WHERE id = :id'
            );
            $stmt->execute(['nome' => $nome, 'logo_path' => $logoPath, 'site_url' => $siteUrl, 'id' => $id]);
            return;
        }

        $stmt = Database::connection()->prepare(
            'UPDATE clients SET nome = :nome, site_url = :site_url WHERE id = :id'
        );
        $stmt->execute(['nome' => $nome, 'site_url' => $siteUrl, 'id' => $id]);
    }

    public function setActive(int $id, bool $active): void
    {
        $stmt = Database::connection()->prepare('UPDATE clients SET ativo = :ativo WHERE id = :id');
        $stmt->execute(['ativo' => $active ? 1 : 0, 'id' => $id]);
    }

    /** @return string caminho do logo removido (para o chamador decidir se apaga o arquivo) */
    public function delete(int $id): string
    {
        $pdo = Database::connection();
        $find = $pdo->prepare('SELECT logo_path FROM clients WHERE id = :id');
        $find->execute(['id' => $id]);
        $logoPath = (string) $find->fetchColumn();

        $stmt = $pdo->prepare('DELETE FROM clients WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return $logoPath;
    }

    /**
     * Troca a posição (`ordem`) do registro com o vizinho imediatamente anterior/seguinte —
     * "setas subir/descer" simples (ver §14 da tarefa: sem drag-and-drop).
     */
    public function moveUp(int $id): void
    {
        $this->swapWithNeighbor($id, 'DESC');
    }

    public function moveDown(int $id): void
    {
        $this->swapWithNeighbor($id, 'ASC');
    }

    private function swapWithNeighbor(int $id, string $direction): void
    {
        $pdo = Database::connection();

        $current = $pdo->prepare('SELECT id, ordem FROM clients WHERE id = :id');
        $current->execute(['id' => $id]);
        $currentRow = $current->fetch();
        if (!$currentRow) {
            return;
        }

        $comparator = $direction === 'DESC' ? '<' : '>';
        $neighborStmt = $pdo->prepare(
            "SELECT id, ordem FROM clients
             WHERE ordem $comparator :ordem
             ORDER BY ordem $direction
             LIMIT 1"
        );
        $neighborStmt->execute(['ordem' => $currentRow['ordem']]);
        $neighbor = $neighborStmt->fetch();
        if (!$neighbor) {
            return; // já é o primeiro/último — nada a fazer
        }

        $pdo->beginTransaction();
        try {
            $update = $pdo->prepare('UPDATE clients SET ordem = :ordem WHERE id = :id');
            $update->execute(['ordem' => $neighbor['ordem'], 'id' => $currentRow['id']]);
            $update->execute(['ordem' => $currentRow['ordem'], 'id' => $neighbor['id']]);
            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
