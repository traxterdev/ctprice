<?php
/**
 * repositories/BlogPostRepository.php
 *
 * Mesmo padrão de repositories/ClientRepository.php, para `blog_posts`. Duas diferenças reais
 * em relação aos demais repositórios desta sprint:
 *
 *   - Sem `ordem`/setas: a listagem pública é sempre cronológica (`published_at DESC`), como já
 *     era com config/blog-posts.php — não existe "reordenar manualmente" um blog.
 *   - `slug`: chave pública da URL (ver .htaccess + blog/show.php) — precisa ser única, normalizada
 *     e nunca conter "/" nem "..". `slugify()`/`isSlugAvailable()` cobrem isso; quem decide
 *     REJEITAR um slug reservado (nomes de diretórios institucionais) é admin/posts/save.php, não
 *     este repositório (regra de roteamento, não de dados).
 *
 * "Publicado"/"despublicado" reaproveita a mesma coluna `ativo` dos demais módulos (mesmo
 * `setActive()`) — só o rótulo muda no admin ("Publicar"/"Despublicar"). Público exige também
 * `published_at <= NOW()` (permite agendar uma data futura sem nenhuma lógica de agendamento
 * adicional).
 */

declare(strict_types=1);

final class BlogPostRepository
{
    /**
     * Posts publicados (ativo=1 E published_at já passou), mais recentes primeiro — usado pelos
     * cards de "Últimas notícias" (Home/Informações) e pela coluna de relacionados.
     *
     * $limit: a grade de "Últimas notícias" foi desenhada para 3 posts (grid 3/2/1 colunas, ver
     * components/blog-section.php) — passado pelo chamador (index.php/informacoes/index.php) para
     * a seção continuar com a mesma aparência mesmo com o blog crescendo. `null` (padrão) retorna
     * todos os publicados — usado pela coluna de relacionados, que já é uma lista simples, não uma
     * grade de colunas fixas.
     *
     * @return list<array<string, mixed>> já no formato esperado por components/blog-section.php
     *         e components/related-posts.php (title/excerpt/image/url/date/time/slug)
     */
    public function allPublished(?int $limit = null): array
    {
        $sql = 'SELECT slug, titulo, categoria, excerpt, imagem_path, published_at
                FROM blog_posts
                WHERE ativo = 1 AND published_at <= NOW()
                ORDER BY published_at DESC, id DESC';

        if ($limit !== null) {
            $sql .= ' LIMIT ' . max(1, $limit);
        }

        $stmt = Database::connection()->query($sql);

        return array_map([self::class, 'mapPublicRow'], $stmt->fetchAll());
    }

    /**
     * Posts relacionados a um artigo — publicados, ativos, DIFERENTES do slug atual, mais
     * recentes primeiro, limitados a `$limit` (padrão 2 — mesmo comportamento visual original,
     * "os outros 2 posts"; ver components/related-posts.php). Correção da pendência registrada em
     * docs/cms.md: antes desta sprint, `blog/_post-template.php` passava TODOS os publicados para
     * `related-posts.php`, que só filtrava o post atual — sem limite, a lista cresceria
     * indefinidamente conforme novos posts fossem publicados. O filtro por slug agora acontece
     * aqui (SQL), não mais só no componente — o `LIMIT` é aplicado DEPOIS de excluir o post
     * atual, então sempre entrega até 2 relacionados de verdade (não 2 antes de filtrar, que
     * poderiam virar 1 ou 0 caso o post atual estivesse entre eles).
     *
     * @return list<array<string, mixed>> mesmo formato de allPublished()
     */
    public function relatedTo(string $excludeSlug, int $limit = 2): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT slug, titulo, categoria, excerpt, imagem_path, published_at
             FROM blog_posts
             WHERE ativo = 1 AND published_at <= NOW() AND slug != :slug
             ORDER BY published_at DESC, id DESC
             LIMIT ' . max(1, $limit)
        );
        $stmt->execute(['slug' => $excludeSlug]);

        return array_map([self::class, 'mapPublicRow'], $stmt->fetchAll());
    }

    /** @param array<string, mixed> $row */
    private static function mapPublicRow(array $row): array
    {
        return [
            'slug' => $row['slug'],
            'title' => $row['titulo'],
            'category' => $row['categoria'],
            'excerpt' => $row['excerpt'],
            'image' => BASE_URL . '/' . ltrim($row['imagem_path'], '/'),
            'url' => BASE_URL . '/' . $row['slug'] . '/',
            'date' => self::dateText($row['published_at']),
            'time' => self::timeText($row['published_at']),
        ];
    }

    /** Post publicado por slug — usado pela página pública do artigo (blog/_post-template.php). */
    public function findPublishedBySlug(string $slug): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM blog_posts WHERE slug = :slug AND ativo = 1 AND published_at <= NOW()'
        );
        $stmt->execute(['slug' => $slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** @return list<array<string, mixed>> */
    public function allForAdmin(): array
    {
        $stmt = Database::connection()->query('SELECT * FROM blog_posts ORDER BY published_at DESC, id DESC');
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM blog_posts WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * true se o slug estiver livre (ou pertencer ao próprio registro sendo editado, via
     * $excludeId) — usado pelo admin ANTES de tentar salvar, para dar um erro amigável em vez de
     * estourar a UNIQUE KEY do banco.
     */
    public function isSlugAvailable(string $slug, ?int $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $stmt = Database::connection()->prepare('SELECT COUNT(*) FROM blog_posts WHERE slug = :slug AND id != :id');
            $stmt->execute(['slug' => $slug, 'id' => $excludeId]);
        } else {
            $stmt = Database::connection()->prepare('SELECT COUNT(*) FROM blog_posts WHERE slug = :slug');
            $stmt->execute(['slug' => $slug]);
        }
        return ((int) $stmt->fetchColumn()) === 0;
    }

    public function create(string $titulo, string $slug, string $categoria, string $excerpt, string $imagemPath, string $bodyHtml, string $publishedAt): int
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO blog_posts (titulo, slug, categoria, excerpt, imagem_path, body_html, published_at, ativo)
             VALUES (:titulo, :slug, :categoria, :excerpt, :imagem_path, :body_html, :published_at, 1)'
        );
        $stmt->execute([
            'titulo' => $titulo,
            'slug' => $slug,
            'categoria' => $categoria,
            'excerpt' => $excerpt,
            'imagem_path' => $imagemPath,
            'body_html' => $bodyHtml,
            'published_at' => $publishedAt,
        ]);
        return (int) Database::connection()->lastInsertId();
    }

    public function update(int $id, string $titulo, string $slug, string $categoria, string $excerpt, ?string $imagemPath, string $bodyHtml, string $publishedAt): void
    {
        $sets = [
            'titulo = :titulo', 'slug = :slug', 'categoria = :categoria', 'excerpt = :excerpt',
            'body_html = :body_html', 'published_at = :published_at',
        ];
        $params = [
            'titulo' => $titulo,
            'slug' => $slug,
            'categoria' => $categoria,
            'excerpt' => $excerpt,
            'body_html' => $bodyHtml,
            'published_at' => $publishedAt,
            'id' => $id,
        ];

        if ($imagemPath !== null) {
            $sets[] = 'imagem_path = :imagem_path';
            $params['imagem_path'] = $imagemPath;
        }

        $stmt = Database::connection()->prepare('UPDATE blog_posts SET ' . implode(', ', $sets) . ' WHERE id = :id');
        $stmt->execute($params);
    }

    public function setActive(int $id, bool $active): void
    {
        $stmt = Database::connection()->prepare('UPDATE blog_posts SET ativo = :ativo WHERE id = :id');
        $stmt->execute(['ativo' => $active ? 1 : 0, 'id' => $id]);
    }

    /** @return string caminho da imagem removida */
    public function delete(int $id): string
    {
        $pdo = Database::connection();
        $find = $pdo->prepare('SELECT imagem_path FROM blog_posts WHERE id = :id');
        $find->execute(['id' => $id]);
        $path = (string) $find->fetchColumn();

        $stmt = $pdo->prepare('DELETE FROM blog_posts WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return $path;
    }

    /**
     * Normaliza um texto em slug seguro: minúsculas, só [a-z0-9-], sem "/" nem "..", sem hífens
     * duplicados/nas pontas. Usado tanto para sugerir um slug a partir do título quanto para
     * validar/normalizar um slug digitado manualmente no admin.
     */
    public static function slugify(string $text): string
    {
        $text = strtolower($text);
        $map = ['á'=>'a','à'=>'a','ã'=>'a','â'=>'a','ä'=>'a','é'=>'e','è'=>'e','ê'=>'e','ë'=>'e',
                'í'=>'i','ì'=>'i','î'=>'i','ï'=>'i','ó'=>'o','ò'=>'o','õ'=>'o','ô'=>'o','ö'=>'o',
                'ú'=>'u','ù'=>'u','û'=>'u','ü'=>'u','ç'=>'c','ñ'=>'n'];
        $text = strtr($text, $map);
        $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
        return trim($text, '-');
    }

    public static function dateText(string $publishedAt): string
    {
        static $meses = [
            1 => 'janeiro', 2 => 'fevereiro', 3 => 'março', 4 => 'abril',
            5 => 'maio', 6 => 'junho', 7 => 'julho', 8 => 'agosto',
            9 => 'setembro', 10 => 'outubro', 11 => 'novembro', 12 => 'dezembro',
        ];
        $dt = new DateTimeImmutable($publishedAt);
        return $meses[(int) $dt->format('n')] . ' ' . $dt->format('j') . ', ' . $dt->format('Y');
    }

    public static function timeText(string $publishedAt): string
    {
        return (new DateTimeImmutable($publishedAt))->format('H:i');
    }
}
