<?php
/**
 * repositories/AdminUserRepository.php
 *
 * Mesmo padrão dos demais repositórios (ver ClientRepository.php). Sem `ordem`/setas (lista
 * ordenada por nome — não há conceito de "posição de exibição pública" para administradores).
 *
 * Regras de proteção (o "não permitir que o administrador logado desative/exclua a própria conta,
 * nem deixe o sistema sem nenhum administrador ativo") são decididas pelo CHAMADOR
 * (admin/users/toggle.php, admin/users/delete.php) — este repositório só oferece os dados
 * necessários para essas decisões (`countActive()`), sem embutir regra de UI/sessão aqui.
 */

declare(strict_types=1);

final class AdminUserRepository
{
    /** @return list<array<string, mixed>> nunca inclui password_hash */
    public function allForAdmin(): array
    {
        $stmt = Database::connection()->query(
            'SELECT id, nome, email, ativo, ultimo_login_em, created_at, updated_at
             FROM admin_users ORDER BY nome ASC'
        );
        return $stmt->fetchAll();
    }

    /** @return array<string, mixed>|null nunca inclui password_hash */
    public function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT id, nome, email, ativo, ultimo_login_em, created_at, updated_at
             FROM admin_users WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function isEmailAvailable(string $email, ?int $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $stmt = Database::connection()->prepare('SELECT COUNT(*) FROM admin_users WHERE email = :email AND id != :id');
            $stmt->execute(['email' => $email, 'id' => $excludeId]);
        } else {
            $stmt = Database::connection()->prepare('SELECT COUNT(*) FROM admin_users WHERE email = :email');
            $stmt->execute(['email' => $email]);
        }
        return ((int) $stmt->fetchColumn()) === 0;
    }

    public function countActive(): int
    {
        return (int) Database::connection()->query('SELECT COUNT(*) FROM admin_users WHERE ativo = 1')->fetchColumn();
    }

    public function create(string $nome, string $email, string $passwordHash): int
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO admin_users (nome, email, password_hash, ativo) VALUES (:nome, :email, :hash, 1)'
        );
        $stmt->execute(['nome' => $nome, 'email' => $email, 'hash' => $passwordHash]);
        return (int) Database::connection()->lastInsertId();
    }

    public function updateProfile(int $id, string $nome, string $email): void
    {
        $stmt = Database::connection()->prepare('UPDATE admin_users SET nome = :nome, email = :email WHERE id = :id');
        $stmt->execute(['nome' => $nome, 'email' => $email, 'id' => $id]);
    }

    public function resetPassword(int $id, string $passwordHash): void
    {
        $stmt = Database::connection()->prepare('UPDATE admin_users SET password_hash = :hash WHERE id = :id');
        $stmt->execute(['hash' => $passwordHash, 'id' => $id]);
    }

    public function setActive(int $id, bool $active): void
    {
        $stmt = Database::connection()->prepare('UPDATE admin_users SET ativo = :ativo WHERE id = :id');
        $stmt->execute(['ativo' => $active ? 1 : 0, 'id' => $id]);
    }

    public function delete(int $id): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM admin_users WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
