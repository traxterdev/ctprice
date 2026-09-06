<?php
/**
 * repositories/JobRepository.php
 *
 * Mesmo padrão de repositories/ClientRepository.php. `requisitos`/`diferenciais` são guardados
 * como TEXT (um item por linha) e convertidos para array só na leitura pública (`toListArray()`) —
 * o components/jobs-section.php espera `requirements`/`differentials` como array de strings,
 * exatamente como config/jobs.php já entregava.
 */

declare(strict_types=1);

final class JobRepository
{
    /**
     * Vagas ativas, na ordem cadastrada, já no formato esperado por components/jobs-section.php.
     *
     * @return list<array{title:string, requirements:list<string>, differentials:list<string>}>
     */
    public function allActive(): array
    {
        $stmt = Database::connection()->query(
            'SELECT titulo, requisitos, diferenciais FROM jobs WHERE ativo = 1 ORDER BY ordem ASC, id ASC'
        );

        return array_map(static function (array $row): array {
            return [
                'title' => $row['titulo'],
                'requirements' => self::toListArray($row['requisitos']),
                'differentials' => self::toListArray($row['diferenciais'] ?? ''),
            ];
        }, $stmt->fetchAll());
    }

    /** @return list<array<string, mixed>> */
    public function allForAdmin(): array
    {
        $stmt = Database::connection()->query(
            'SELECT * FROM jobs ORDER BY ordem ASC, id ASC'
        );
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM jobs WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function nextOrder(): int
    {
        $max = Database::connection()->query('SELECT MAX(ordem) FROM jobs')->fetchColumn();
        return $max !== null ? ((int) $max) + 1 : 0;
    }

    public function create(string $titulo, string $requisitos, ?string $diferenciais, int $ordem): int
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO jobs (titulo, requisitos, diferenciais, ordem, ativo)
             VALUES (:titulo, :requisitos, :diferenciais, :ordem, 1)'
        );
        $stmt->execute([
            'titulo' => $titulo,
            'requisitos' => $requisitos,
            'diferenciais' => $diferenciais,
            'ordem' => $ordem,
        ]);
        return (int) Database::connection()->lastInsertId();
    }

    public function update(int $id, string $titulo, string $requisitos, ?string $diferenciais): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE jobs SET titulo = :titulo, requisitos = :requisitos, diferenciais = :diferenciais WHERE id = :id'
        );
        $stmt->execute([
            'titulo' => $titulo,
            'requisitos' => $requisitos,
            'diferenciais' => $diferenciais,
            'id' => $id,
        ]);
    }

    public function setActive(int $id, bool $active): void
    {
        $stmt = Database::connection()->prepare('UPDATE jobs SET ativo = :ativo WHERE id = :id');
        $stmt->execute(['ativo' => $active ? 1 : 0, 'id' => $id]);
    }

    public function delete(int $id): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM jobs WHERE id = :id');
        $stmt->execute(['id' => $id]);
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

        $current = $pdo->prepare('SELECT id, ordem FROM jobs WHERE id = :id');
        $current->execute(['id' => $id]);
        $currentRow = $current->fetch();
        if (!$currentRow) {
            return;
        }

        $comparator = $direction === 'DESC' ? '<' : '>';
        $neighborStmt = $pdo->prepare(
            "SELECT id, ordem FROM jobs WHERE ordem $comparator :ordem ORDER BY ordem $direction LIMIT 1"
        );
        $neighborStmt->execute(['ordem' => $currentRow['ordem']]);
        $neighbor = $neighborStmt->fetch();
        if (!$neighbor) {
            return;
        }

        $pdo->beginTransaction();
        try {
            $update = $pdo->prepare('UPDATE jobs SET ordem = :ordem WHERE id = :id');
            $update->execute(['ordem' => $neighbor['ordem'], 'id' => $currentRow['id']]);
            $update->execute(['ordem' => $currentRow['ordem'], 'id' => $neighbor['id']]);
            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /** "um item\npor linha" -> ['um item', 'por linha'] — linhas vazias descartadas. */
    public static function toListArray(?string $text): array
    {
        if ($text === null || trim($text) === '') {
            return [];
        }
        $lines = preg_split('/\r\n|\r|\n/', $text) ?: [];
        return array_values(array_filter(array_map('trim', $lines), static fn ($line) => $line !== ''));
    }

    /** ['um item', 'por linha'] -> "um item\npor linha" — mesma convenção usada no textarea do admin. */
    public static function fromListArray(array $items): string
    {
        return implode("\n", array_values(array_filter(array_map('trim', $items), static fn ($line) => $line !== '')));
    }
}
