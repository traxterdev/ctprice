<?php
/**
 * /hello-world/ — Post do blog
 *
 * Slug histórico do WordPress preservado exatamente (ver docs/reference/blog-posts-audit.md,
 * seção 19: conteúdo real e legítimo, não renomeado, não redirecionado). Só define o slug e
 * delega todo o layout/dados a blog/_post-template.php.
 */
require __DIR__ . '/../config/bootstrap.php';

$postSlug = 'hello-world';
require __DIR__ . '/../blog/_post-template.php';
