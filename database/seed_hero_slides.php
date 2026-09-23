<?php
/**
 * database/seed_hero_slides.php
 *
 * Importa os 4 slides ESTÁTICOS atuais do Hero (antes desta sprint, definidos diretamente em
 * index.php, `$heroSlides`) para a tabela `hero_slides` — fonte inicial real do CMS, não dados
 * inventados (mesmo espírito de database/seed_clients_and_partners.php).
 *
 * Idempotente: usa `INSERT ... ON DUPLICATE KEY UPDATE`, apoiado na UNIQUE KEY de
 * `hero_slides.imagem_path` (ver migration) — reexecutar este script não duplica slides, só
 * atualiza título/ordem/ativo=1 caso o conteúdo mude.
 *
 * `imagem_path` migrado aponta para os arquivos JÁ existentes em assets/images/hero/ (nenhuma
 * imagem é copiada/movida por este script) — uploads feitos pelo admin depois desta sprint vivem
 * em assets/uploads/hero/ (ver includes/Uploads.php).
 *
 * AJUSTE (2026-09-23): a lista de slides e a lógica de importação foram extraídas para as
 * funções ctprice_hero_seed_slides()/ctprice_seed_hero_slides() abaixo — reaproveitadas por
 * admin/aplicar-atualizacao.php (ferramenta TEMPORÁRIA de implantação, ver seu cabeçalho) sem
 * duplicar o conteúdo do seed. `require`'d por outro script, este arquivo não executa nada
 * sozinho — só declara as funções; o bloco de execução no fim só roda quando ESTE arquivo é o
 * script de entrada da CLI.
 *
 * Uso (CLI, a partir da raiz do projeto):
 *   php database/seed_hero_slides.php
 */

declare(strict_types=1);

if (!function_exists('ctprice_hero_seed_slides')) {
    /**
     * Conteúdo real dos 4 slides originais do Hero — fonte única, também usada por
     * admin/aplicar-atualizacao.php para checar se o seed já foi aplicado (compara os
     * `imagem_path` esperados com o que já existe em `hero_slides`, sem reimplementar a lista).
     *
     * @return list<array{imagem_path:string, titulo:string}>
     */
    function ctprice_hero_seed_slides(): array
    {
        return [
            [
                'imagem_path' => 'assets/images/hero/caroussel01.jpg',
                'titulo' => '<span class="hero-slide__highlight">Cuide da sua empresa,</span> <br>e deixe a contabilidade nas <br>mãos de quem entende',
            ],
            [
                'imagem_path' => 'assets/images/hero/csinicial02.jpg',
                'titulo' => 'Trabalhamos <span class="hero-slide__highlight">integrados </span>aos<br> colaboradores de sua empresa, <br>para que juntos possamos obter <br><span class="hero-slide__highlight">os melhores resultados</span>',
            ],
            [
                'imagem_path' => 'assets/images/hero/caroussel02.jpg',
                'titulo' => 'Atuamos nos ramos de<br> contabilidade e planejamento<br> tributário em formato digital <br><span class="hero-slide__highlight">sem papel e sem burocracia</span>.',
            ],
            [
                'imagem_path' => 'assets/images/hero/caroussel03a.jpg',
                'titulo' => 'Fornecemos informações <br> <span class="hero-slide__highlight">precisas e seguras</span> para <br>que você possa tomar <br>as <span class="hero-slide__highlight">melhores decisões </span><br>para seu negócio',
            ],
        ];
    }
}

if (!function_exists('ctprice_seed_hero_slides')) {
    /**
     * Importa/atualiza os slides de ctprice_hero_seed_slides() em `hero_slides` — idempotente via
     * a UNIQUE KEY de `imagem_path` (`ON DUPLICATE KEY UPDATE`). `titulo` passa por
     * ctprice_sanitize_hero_title_html() antes de chegar ao banco (mesmo sanitizador usado pelo
     * admin — ver includes/HtmlSanitizer.php) por consistência de fonte única de verdade, não
     * porque este conteúdo específico seja não confiável (é o mesmo texto que já estava em
     * produção).
     *
     * @return int quantidade de slides importados/atualizados
     */
    function ctprice_seed_hero_slides(PDO $pdo): int
    {
        require_once __DIR__ . '/../includes/HtmlSanitizer.php';

        $stmt = $pdo->prepare(
            'INSERT INTO hero_slides (titulo, texto, imagem_path, botao_texto, botao_url, ordem, ativo)
             VALUES (:titulo, NULL, :imagem_path, NULL, NULL, :ordem, 1)
             ON DUPLICATE KEY UPDATE titulo = VALUES(titulo), ordem = VALUES(ordem), ativo = 1'
        );

        $count = 0;
        foreach (ctprice_hero_seed_slides() as $index => $slide) {
            $stmt->execute([
                'titulo' => ctprice_sanitize_hero_title_html($slide['titulo']),
                'imagem_path' => $slide['imagem_path'],
                'ordem' => $index,
            ]);
            $count++;
        }

        return $count;
    }
}

if (PHP_SAPI === 'cli' && isset($argv[0]) && realpath($argv[0]) === __FILE__) {
    require __DIR__ . '/../config/bootstrap.php';
    $count = ctprice_seed_hero_slides(Database::connection());
    echo "Slides do Hero importados/atualizados: $count\n";
}
