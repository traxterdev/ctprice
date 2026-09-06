<?php
/**
 * repositories/PartnerRepository.php
 *
 * Única camada que sabe escrever SQL para `partners` — mesmo espírito de
 * repositories/ClientRepository.php. `categoria` ('tools'|'companies') é tratada como dimensão
 * de primeira classe (não uma tag genérica) porque `/parcerias/` sempre exibe as duas grades
 * separadamente (ver components/logo-grid-section.php, chamado duas vezes).
 */

declare(strict_types=1);

final class PartnerRepository
{
    private const CATEGORIES = ['tools', 'companies'];

    private function assertCategory(string $categoria): void
    {
        if (!in_array($categoria, self::CATEGORIES, true)) {
            throw new InvalidArgumentException("Categoria inválida: $categoria");
        }
    }

    /**
     * @return list<array{id:int, nome:string, logo_path:string, url:?string, ordem:int}>
     */
    public function allActiveByCategory(string $categoria): array
    {
        $this->assertCategory($categoria);
        $stmt = Database::connection()->prepare(
            'SELECT id, nome, logo_path, url, ordem
             FROM partners
             WHERE categoria = :categoria AND ativo = 1
             ORDER BY ordem ASC, id ASC'
        );
        $stmt->execute(['categoria' => $categoria]);
        return $stmt->fetchAll();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function allForAdmin(?string $categoria = null): array
    {
        if ($categoria !== null) {
            $this->assertCategory($categoria);
            $stmt = Database::connection()->prepare(
                'SELECT id, nome, categoria, logo_path, url, ordem, ativo, created_at, updated_at
                 FROM partners WHERE categoria = :categoria
                 ORDER BY categoria ASC, ordem ASC, id ASC'
            );
            $stmt->execute(['categoria' => $categoria]);
            return $stmt->fetchAll();
        }

        $stmt = Database::connection()->query(
            'SELECT id, nome, categoria, logo_path, url, ordem, ativo, created_at, updated_at
             FROM partners
             ORDER BY categoria ASC, ordem ASC, id ASC'
        );
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM partners WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function nextOrder(string $categoria): int
    {
        $this->assertCategory($categoria);
        $stmt = Database::connection()->prepare('SELECT MAX(ordem) FROM partners WHERE categoria = :categoria');
        $stmt->execute(['categoria' => $categoria]);
        $max = $stmt->fetchColumn();
        return $max !== null ? ((int) $max) + 1 : 0;
    }

    public function create(string $nome, string $categoria, string $logoPath, ?string $url, int $ordem): int
    {
        $this->assertCategory($categoria);
        $stmt = Database::connection()->prepare(
            'INSERT INTO partners (nome, categoria, logo_path, url, ordem, ativo)
             VALUES (:nome, :categoria, :logo_path, :url, :ordem, 1)'
        );
        $stmt->execute([
            'nome' => $nome,
            'categoria' => $categoria,
            'logo_path' => $logoPath,
            'url' => $url,
            'ordem' => $ordem,
        ]);
        return (int) Database::connection()->lastInsertId();
    }

    public function update(int $id, string $nome, string $categoria, ?string $logoPath, ?string $url): void
    {
        $this->assertCategory($categoria);

        if ($logoPath !== null) {
            $stmt = Database::connection()->prepare(
                'UPDATE partners SET nome = :nome, categoria = :categoria, logo_path = :logo_path, url = :url WHERE id = :id'
            );
            $stmt->execute(['nome' => $nome, 'categoria' => $categoria, 'logo_path' => $logoPath, 'url' => $url, 'id' => $id]);
            return;
        }

        $stmt = Database::connection()->prepare(
            'UPDATE partners SET nome = :nome, categoria = :categoria, url = :url WHERE id = :id'
        );
        $stmt->execute(['nome' => $nome, 'categoria' => $categoria, 'url' => $url, 'id' => $id]);
    }

    /**
     * Move o item para o fim da ordem de uma categoria — usado só quando a edição TROCA a
     * categoria do item (a `ordem` antiga pertencia à categoria anterior e perderia sentido).
     */
    public function moveToEndOfCategory(int $id, string $categoria): void
    {
        $this->assertCategory($categoria);
        $stmt = Database::connection()->prepare('UPDATE partners SET ordem = :ordem WHERE id = :id');
        $stmt->execute(['ordem' => $this->nextOrder($categoria), 'id' => $id]);
    }

    public function setActive(int $id, bool $active): void
    {
        $stmt = Database::connection()->prepare('UPDATE partners SET ativo = :ativo WHERE id = :id');
        $stmt->execute(['ativo' => $active ? 1 : 0, 'id' => $id]);
    }

    /** @return string caminho do logo removido (para o chamador decidir se apaga o arquivo) */
    public function delete(int $id): string
    {
        $pdo = Database::connection();
        $find = $pdo->prepare('SELECT logo_path FROM partners WHERE id = :id');
        $find->execute(['id' => $id]);
        $logoPath = (string) $find->fetchColumn();

        $stmt = $pdo->prepare('DELETE FROM partners WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return $logoPath;
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

        $current = $pdo->prepare('SELECT id, categoria, ordem FROM partners WHERE id = :id');
        $current->execute(['id' => $id]);
        $currentRow = $current->fetch();
        if (!$currentRow) {
            return;
        }

        // A troca só faz sentido DENTRO da mesma categoria — "subir" um parceiro nunca deve
        // trocar de posição com uma ferramenta (grades exibidas separadamente).
        $comparator = $direction === 'DESC' ? '<' : '>';
        $neighborStmt = $pdo->prepare(
            "SELECT id, ordem FROM partners
             WHERE categoria = :categoria AND ordem $comparator :ordem
             ORDER BY ordem $direction
             LIMIT 1"
        );
        $neighborStmt->execute(['categoria' => $currentRow['categoria'], 'ordem' => $currentRow['ordem']]);
        $neighbor = $neighborStmt->fetch();
        if (!$neighbor) {
            return;
        }

        $pdo->beginTransaction();
        try {
            $update = $pdo->prepare('UPDATE partners SET ordem = :ordem WHERE id = :id');
            $update->execute(['ordem' => $neighbor['ordem'], 'id' => $currentRow['id']]);
            $update->execute(['ordem' => $currentRow['ordem'], 'id' => $neighbor['id']]);
            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
