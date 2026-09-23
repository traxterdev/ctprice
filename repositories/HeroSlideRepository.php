<?php
/**
 * repositories/HeroSlideRepository.php
 *
 * Única camada que sabe escrever SQL para `hero_slides` — mesmo espírito de
 * repositories/PartnerRepository.php (CRUD + reordenação por troca de `ordem` com o vizinho).
 * `titulo` já chega SANITIZADO (ver includes/HtmlSanitizer.php::ctprice_sanitize_hero_title_html)
 * — este repositório nunca sanitiza nem escapa, só persiste.
 */

declare(strict_types=1);

final class HeroSlideRepository
{
    /**
     * Slides ativos, na ordem de exibição — consumido pela Home (index.php).
     *
     * @return list<array{id:int, titulo:string, texto:?string, imagem_path:string, botao_texto:?string, botao_url:?string}>
     */
    public function activeOrdered(): array
    {
        $stmt = Database::connection()->query(
            'SELECT id, titulo, texto, imagem_path, botao_texto, botao_url
             FROM hero_slides
             WHERE ativo = 1
             ORDER BY ordem ASC, id ASC'
        );
        return $stmt->fetchAll();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function allForAdmin(): array
    {
        $stmt = Database::connection()->query(
            'SELECT id, titulo, texto, imagem_path, botao_texto, botao_url, ordem, ativo, created_at, updated_at
             FROM hero_slides
             ORDER BY ordem ASC, id ASC'
        );
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM hero_slides WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function nextOrder(): int
    {
        $max = Database::connection()->query('SELECT MAX(ordem) FROM hero_slides')->fetchColumn();
        return $max !== null ? ((int) $max) + 1 : 0;
    }

    public function create(
        string $titulo,
        ?string $texto,
        string $imagemPath,
        ?string $botaoTexto,
        ?string $botaoUrl,
        int $ordem
    ): int {
        $stmt = Database::connection()->prepare(
            'INSERT INTO hero_slides (titulo, texto, imagem_path, botao_texto, botao_url, ordem, ativo)
             VALUES (:titulo, :texto, :imagem_path, :botao_texto, :botao_url, :ordem, 1)'
        );
        $stmt->execute([
            'titulo' => $titulo,
            'texto' => $texto,
            'imagem_path' => $imagemPath,
            'botao_texto' => $botaoTexto,
            'botao_url' => $botaoUrl,
            'ordem' => $ordem,
        ]);
        return (int) Database::connection()->lastInsertId();
    }

    public function update(
        int $id,
        string $titulo,
        ?string $texto,
        ?string $imagemPath,
        ?string $botaoTexto,
        ?string $botaoUrl
    ): void {
        if ($imagemPath !== null) {
            $stmt = Database::connection()->prepare(
                'UPDATE hero_slides
                 SET titulo = :titulo, texto = :texto, imagem_path = :imagem_path,
                     botao_texto = :botao_texto, botao_url = :botao_url
                 WHERE id = :id'
            );
            $stmt->execute([
                'titulo' => $titulo,
                'texto' => $texto,
                'imagem_path' => $imagemPath,
                'botao_texto' => $botaoTexto,
                'botao_url' => $botaoUrl,
                'id' => $id,
            ]);
            return;
        }

        $stmt = Database::connection()->prepare(
            'UPDATE hero_slides
             SET titulo = :titulo, texto = :texto, botao_texto = :botao_texto, botao_url = :botao_url
             WHERE id = :id'
        );
        $stmt->execute([
            'titulo' => $titulo,
            'texto' => $texto,
            'botao_texto' => $botaoTexto,
            'botao_url' => $botaoUrl,
            'id' => $id,
        ]);
    }

    public function setActive(int $id, bool $active): void
    {
        $stmt = Database::connection()->prepare('UPDATE hero_slides SET ativo = :ativo WHERE id = :id');
        $stmt->execute(['ativo' => $active ? 1 : 0, 'id' => $id]);
    }

    /** @return string caminho da imagem removida (para o chamador decidir se apaga o arquivo) */
    public function delete(int $id): string
    {
        $pdo = Database::connection();
        $find = $pdo->prepare('SELECT imagem_path FROM hero_slides WHERE id = :id');
        $find->execute(['id' => $id]);
        $imagemPath = (string) $find->fetchColumn();

        $stmt = $pdo->prepare('DELETE FROM hero_slides WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return $imagemPath;
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

        $current = $pdo->prepare('SELECT id, ordem FROM hero_slides WHERE id = :id');
        $current->execute(['id' => $id]);
        $currentRow = $current->fetch();
        if (!$currentRow) {
            return;
        }

        $comparator = $direction === 'DESC' ? '<' : '>';
        $neighborStmt = $pdo->prepare(
            "SELECT id, ordem FROM hero_slides
             WHERE ordem $comparator :ordem
             ORDER BY ordem $direction
             LIMIT 1"
        );
        $neighborStmt->execute(['ordem' => $currentRow['ordem']]);
        $neighbor = $neighborStmt->fetch();
        if (!$neighbor) {
            return;
        }

        $pdo->beginTransaction();
        try {
            $update = $pdo->prepare('UPDATE hero_slides SET ordem = :ordem WHERE id = :id');
            $update->execute(['ordem' => $neighbor['ordem'], 'id' => $currentRow['id']]);
            $update->execute(['ordem' => $currentRow['ordem'], 'id' => $neighbor['id']]);
            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
