<?php
/**
 * includes/Uploads.php
 *
 * Upload seguro de imagens do `/admin/` — logos (Clientes/Parceiros, sprint 01), imagens de
 * Benefícios, fotos/miniaturas de Depoimentos e thumbnails de posts do Blog (sprint 02). Nenhuma
 * biblioteca externa.
 *
 * Requisitos de segurança implementados (ver CLAUDE.md/tarefa da sprint, §15):
 * - MIME real via `finfo` lendo o CONTEÚDO do arquivo — nunca `$_FILES[...]['type']` (esse valor
 *   vem do navegador do visitante, não é confiável).
 * - Extensão da SAÍDA decidida pelo MIME detectado (whitelist fixa), nunca pela extensão do nome
 *   original enviado.
 * - Nome físico sempre ALEATÓRIO (`random_bytes`) — o nome original do arquivo nunca chega ao
 *   disco nem é usado para decidir nada.
 * - Tamanho máximo (5MB — mesmo limite por arquivo já aprovado em `ouvidoria/ouvidoria-action.php`
 *   para uploads de visitantes, reaproveitado aqui por consistência).
 * - SVG NÃO é aceito nesta primeira versão: nenhum logo atual (82 clientes + 62 parceiros,
 *   conferido nesta sprint) usa SVG, e um SVG pode conter `<script>`/JS embutido — sem
 *   necessidade real comprovada, o risco não se justifica (ver docs/cms.md).
 * - Diretório de destino próprio (`assets/uploads/clients/` ou `assets/uploads/partners/`),
 *   protegido contra execução de script por `assets/uploads/.htaccess` (nega `.php` e afins,
 *   defesa em profundidade mesmo já não sendo possível gerar um `.php` por este fluxo).
 *
 * NÃO é um componente de mídia genérico — só entende logos de Clientes/Parceiros (ver §24 da
 * tarefa: "não implementar mídia genérica ainda").
 */

declare(strict_types=1);

const ADMIN_UPLOAD_MAX_BYTES = 5 * 1024 * 1024; // 5MB — mesmo limite já aprovado na Ouvidoria.

/** MIME real (detectado) => extensão de saída. Nenhuma outra combinação é aceita. */
const ADMIN_UPLOAD_ALLOWED_MIMES = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp',
];

/**
 * Processa um upload de logo vindo de $_FILES[$fieldName] e devolve o caminho relativo (a partir
 * da raiz do site, sem BASE_URL) do arquivo salvo — ex.: "assets/uploads/clients/ab12....jpg".
 *
 * @param array<string, mixed> $file um elemento de $_FILES (ex.: $_FILES['logo'])
 * @param 'clients'|'partners'|'benefits'|'testimonials'|'posts' $entity subpasta de destino
 * @throws RuntimeException mensagem já segura para exibir ao administrador
 */
function ctprice_admin_handle_logo_upload(array $file, string $entity): string
{
    if (!in_array($entity, ['clients', 'partners', 'benefits', 'testimonials', 'posts'], true)) {
        throw new RuntimeException('Destino de upload inválido.');
    }

    $error = $file['error'] ?? UPLOAD_ERR_NO_FILE;
    if ($error === UPLOAD_ERR_NO_FILE) {
        throw new RuntimeException('Nenhum arquivo enviado.');
    }
    if ($error !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Falha ao enviar o arquivo (código ' . (int) $error . ').');
    }

    $tmpPath = (string) ($file['tmp_name'] ?? '');
    if ($tmpPath === '' || !is_uploaded_file($tmpPath)) {
        // Nunca confia em um caminho que não foi genuinamente criado pelo mecanismo de upload do
        // PHP nesta mesma requisição — bloqueia qualquer tentativa de apontar para outro arquivo
        // do servidor.
        throw new RuntimeException('Upload inválido.');
    }

    $size = (int) ($file['size'] ?? 0);
    if ($size <= 0 || $size > ADMIN_UPLOAD_MAX_BYTES) {
        throw new RuntimeException('O arquivo deve ter no máximo 5MB.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($tmpPath) ?: '';

    if (!isset(ADMIN_UPLOAD_ALLOWED_MIMES[$mime])) {
        throw new RuntimeException('Formato não suportado. Envie um arquivo PNG, JPEG ou WEBP.');
    }

    $extension = ADMIN_UPLOAD_ALLOWED_MIMES[$mime];
    $filename = bin2hex(random_bytes(16)) . '.' . $extension;

    $targetDir = __DIR__ . '/../assets/uploads/' . $entity;
    if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
        error_log("CT Price CMS [Uploads]: não foi possível criar $targetDir");
        throw new RuntimeException('Não foi possível salvar o arquivo no momento.');
    }

    $targetPath = $targetDir . '/' . $filename;
    if (!move_uploaded_file($tmpPath, $targetPath)) {
        error_log("CT Price CMS [Uploads]: move_uploaded_file falhou para $targetPath");
        throw new RuntimeException('Não foi possível salvar o arquivo no momento.');
    }

    // Impede execução mesmo que o servidor real esteja mal configurado — nenhuma permissão de
    // execução é necessária para um arquivo servido como imagem estática.
    @chmod($targetPath, 0644);

    return 'assets/uploads/' . $entity . '/' . $filename;
}

/**
 * Remove um logo antigo do disco SOMENTE se ele estiver dentro de assets/uploads/ (arquivos
 * enviados pelo próprio admin). Nunca apaga nada em assets/images/... — esses são os assets
 * originais do site (migrados de config/clients.php/config/partners.php), e apagá-los por engano
 * ao "substituir" um logo destruiria conteúdo do site que não pertence ao CMS.
 */
function ctprice_admin_delete_old_logo_if_owned(string $logoPath): void
{
    $normalized = ltrim($logoPath, '/');
    if (!str_starts_with($normalized, 'assets/uploads/')) {
        return;
    }

    $fullPath = __DIR__ . '/../' . $normalized;
    $uploadsRoot = realpath(__DIR__ . '/../assets/uploads');
    $realTarget = realpath($fullPath);

    // Confirma que o caminho resolvido continua DENTRO de assets/uploads/ antes de apagar —
    // proteção contra um logo_path manipulado com "../" chegando até aqui.
    if ($realTarget !== false && $uploadsRoot !== false && str_starts_with($realTarget, $uploadsRoot)) {
        @unlink($realTarget);
    }
}
