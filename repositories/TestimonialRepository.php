<?php
/**
 * repositories/TestimonialRepository.php
 *
 * Mesmo padrão de repositories/ClientRepository.php, para `video_testimonials`.
 */

declare(strict_types=1);

final class TestimonialRepository
{
    /** @return list<array<string, mixed>> já no formato esperado por components/video-testimonials-section.php */
    public function allActive(): array
    {
        $stmt = Database::connection()->query(
            'SELECT id, nome, empresa, depoimento, video_id, video_list, foto_path, thumbnail_path,
                    site_url, instagram_url, ordem
             FROM video_testimonials WHERE ativo = 1 ORDER BY ordem ASC, id ASC'
        );

        return array_map(static function (array $row): array {
            return [
                'name' => $row['nome'],
                'company' => $row['empresa'],
                'quote' => $row['depoimento'],
                'photo' => $row['foto_path'],
                'thumbnail' => $row['thumbnail_path'],
                'video_id' => $row['video_id'],
                'video_list' => $row['video_list'],
                'website_url' => $row['site_url'],
                'instagram_url' => $row['instagram_url'],
            ];
        }, $stmt->fetchAll());
    }

    /** @return list<array<string, mixed>> */
    public function allForAdmin(): array
    {
        $stmt = Database::connection()->query('SELECT * FROM video_testimonials ORDER BY ordem ASC, id ASC');
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM video_testimonials WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function nextOrder(): int
    {
        $max = Database::connection()->query('SELECT MAX(ordem) FROM video_testimonials')->fetchColumn();
        return $max !== null ? ((int) $max) + 1 : 0;
    }

    public function create(array $data, string $fotoPath, string $thumbnailPath, int $ordem): int
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO video_testimonials
                (nome, empresa, depoimento, video_id, video_list, foto_path, thumbnail_path, site_url, instagram_url, ordem, ativo)
             VALUES
                (:nome, :empresa, :depoimento, :video_id, :video_list, :foto_path, :thumbnail_path, :site_url, :instagram_url, :ordem, 1)'
        );
        $stmt->execute([
            'nome' => $data['nome'],
            'empresa' => $data['empresa'],
            'depoimento' => $data['depoimento'],
            'video_id' => $data['video_id'],
            'video_list' => $data['video_list'],
            'foto_path' => $fotoPath,
            'thumbnail_path' => $thumbnailPath,
            'site_url' => $data['site_url'],
            'instagram_url' => $data['instagram_url'],
            'ordem' => $ordem,
        ]);
        return (int) Database::connection()->lastInsertId();
    }

    public function update(int $id, array $data, ?string $fotoPath, ?string $thumbnailPath): void
    {
        $sets = [
            'nome = :nome', 'empresa = :empresa', 'depoimento = :depoimento',
            'video_id = :video_id', 'video_list = :video_list',
            'site_url = :site_url', 'instagram_url = :instagram_url',
        ];
        $params = [
            'nome' => $data['nome'],
            'empresa' => $data['empresa'],
            'depoimento' => $data['depoimento'],
            'video_id' => $data['video_id'],
            'video_list' => $data['video_list'],
            'site_url' => $data['site_url'],
            'instagram_url' => $data['instagram_url'],
            'id' => $id,
        ];

        if ($fotoPath !== null) {
            $sets[] = 'foto_path = :foto_path';
            $params['foto_path'] = $fotoPath;
        }
        if ($thumbnailPath !== null) {
            $sets[] = 'thumbnail_path = :thumbnail_path';
            $params['thumbnail_path'] = $thumbnailPath;
        }

        $stmt = Database::connection()->prepare('UPDATE video_testimonials SET ' . implode(', ', $sets) . ' WHERE id = :id');
        $stmt->execute($params);
    }

    public function setActive(int $id, bool $active): void
    {
        $stmt = Database::connection()->prepare('UPDATE video_testimonials SET ativo = :ativo WHERE id = :id');
        $stmt->execute(['ativo' => $active ? 1 : 0, 'id' => $id]);
    }

    /** @return array{foto_path:string, thumbnail_path:string} caminhos removidos */
    public function delete(int $id): array
    {
        $pdo = Database::connection();
        $find = $pdo->prepare('SELECT foto_path, thumbnail_path FROM video_testimonials WHERE id = :id');
        $find->execute(['id' => $id]);
        $row = $find->fetch() ?: ['foto_path' => '', 'thumbnail_path' => ''];

        $stmt = $pdo->prepare('DELETE FROM video_testimonials WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return $row;
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

        $current = $pdo->prepare('SELECT id, ordem FROM video_testimonials WHERE id = :id');
        $current->execute(['id' => $id]);
        $currentRow = $current->fetch();
        if (!$currentRow) {
            return;
        }

        $comparator = $direction === 'DESC' ? '<' : '>';
        $neighborStmt = $pdo->prepare(
            "SELECT id, ordem FROM video_testimonials WHERE ordem $comparator :ordem ORDER BY ordem $direction LIMIT 1"
        );
        $neighborStmt->execute(['ordem' => $currentRow['ordem']]);
        $neighbor = $neighborStmt->fetch();
        if (!$neighbor) {
            return;
        }

        $pdo->beginTransaction();
        try {
            $update = $pdo->prepare('UPDATE video_testimonials SET ordem = :ordem WHERE id = :id');
            $update->execute(['ordem' => $neighbor['ordem'], 'id' => $currentRow['id']]);
            $update->execute(['ordem' => $currentRow['ordem'], 'id' => $neighbor['id']]);
            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
