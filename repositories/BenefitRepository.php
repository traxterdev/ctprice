<?php
/**
 * repositories/BenefitRepository.php
 *
 * Mesmo padrão de repositories/ClientRepository.php — estrutura idêntica (nome + imagem +
 * ordem + ativo), só o nome da tabela/campo de imagem muda.
 */

declare(strict_types=1);

final class BenefitRepository
{
    /** @return list<array{id:int, nome:string, imagem_path:string, ordem:int}> */
    public function allActive(): array
    {
        $stmt = Database::connection()->query(
            'SELECT id, nome, imagem_path, ordem FROM benefits WHERE ativo = 1 ORDER BY ordem ASC, id ASC'
        );
        return $stmt->fetchAll();
    }

    /** @return list<array<string, mixed>> */
    public function allForAdmin(): array
    {
        $stmt = Database::connection()->query('SELECT * FROM benefits ORDER BY ordem ASC, id ASC');
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM benefits WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function nextOrder(): int
    {
        $max = Database::connection()->query('SELECT MAX(ordem) FROM benefits')->fetchColumn();
        return $max !== null ? ((int) $max) + 1 : 0;
    }

    public function create(string $nome, string $imagemPath, int $ordem): int
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO benefits (nome, imagem_path, ordem, ativo) VALUES (:nome, :imagem_path, :ordem, 1)'
        );
        $stmt->execute(['nome' => $nome, 'imagem_path' => $imagemPath, 'ordem' => $ordem]);
        return (int) Database::connection()->lastInsertId();
    }

    public function update(int $id, string $nome, ?string $imagemPath): void
    {
        if ($imagemPath !== null) {
            $stmt = Database::connection()->prepare('UPDATE benefits SET nome = :nome, imagem_path = :imagem_path WHERE id = :id');
            $stmt->execute(['nome' => $nome, 'imagem_path' => $imagemPath, 'id' => $id]);
            return;
        }

        $stmt = Database::connection()->prepare('UPDATE benefits SET nome = :nome WHERE id = :id');
        $stmt->execute(['nome' => $nome, 'id' => $id]);
    }

    public function setActive(int $id, bool $active): void
    {
        $stmt = Database::connection()->prepare('UPDATE benefits SET ativo = :ativo WHERE id = :id');
        $stmt->execute(['ativo' => $active ? 1 : 0, 'id' => $id]);
    }

    /** @return string caminho da imagem removida */
    public function delete(int $id): string
    {
        $pdo = Database::connection();
        $find = $pdo->prepare('SELECT imagem_path FROM benefits WHERE id = :id');
        $find->execute(['id' => $id]);
        $path = (string) $find->fetchColumn();

        $stmt = $pdo->prepare('DELETE FROM benefits WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return $path;
    }

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

        $current = $pdo->prepare('SELECT id, ordem FROM benefits WHERE id = :id');
        $current->execute(['id' => $id]);
        $currentRow = $current->fetch();
        if (!$currentRow) {
            return;
        }

        $comparator = $direction === 'DESC' ? '<' : '>';
        $neighborStmt = $pdo->prepare(
            "SELECT id, ordem FROM benefits WHERE ordem $comparator :ordem ORDER BY ordem $direction LIMIT 1"
        );
        $neighborStmt->execute(['ordem' => $currentRow['ordem']]);
        $neighbor = $neighborStmt->fetch();
        if (!$neighbor) {
            return;
        }

        $pdo->beginTransaction();
        try {
            $update = $pdo->prepare('UPDATE benefits SET ordem = :ordem WHERE id = :id');
            $update->execute(['ordem' => $neighbor['ordem'], 'id' => $currentRow['id']]);
            $update->execute(['ordem' => $currentRow['ordem'], 'id' => $neighbor['id']]);
            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
