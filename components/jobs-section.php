<?php
/**
 * components/jobs-section.php
 *
 * Grade de vagas em aberto de `/trabalhe-conosco/` — substitui, com melhorias deliberadas de
 * arquitetura e UX, o padrão do site original (docs/reference/trabalhe-conosco-audit.md, seções
 * 4 e 6):
 *
 *   - CTA "Clique aqui" → "Candidatar-se" (texto compreensível fora do contexto visual, ver
 *     auditoria seção 12).
 *   - Sem popup de cadastro de currículo (Elementor Pro, 10 campos + upload, backend próprio):
 *     candidatura direcionada para o sistema oficial de recrutamento já existente
 *     (`config/company.php['sistemas_externos']['recrutamento']`), evitando duplicar uma
 *     funcionalidade que a empresa já mantém em outro lugar.
 *   - Pré-requisitos/diferenciais como listas HTML reais (`<ul><li>`), não texto solto com "->"
 *     ASCII — mesmo conteúdo, formato adequado.
 *   - Cards com identidade visual própria (fundo branco, borda sutil, radius, sombra leve) — não
 *     os containers sem nenhum estilo do Elementor.
 *
 * Não força reutilização de components/flat-icon-box-section.php: aquele componente foi feito
 * para um ícone + título + UM parágrafo curto centralizado; aqui o conteúdo é rico (múltiplos
 * parágrafos/listas por vaga), alinhado à esquerda, sem ícone, e com um CTA por card — mais
 * modificador do que reaproveitamento real (mesmo raciocínio já registrado para
 * image-content-cta-section.php vs image-text-section.php).
 *
 * Nenhuma altura é forçada igual entre os cards (`align-items: start` no grid) — o card da vaga
 * com menos texto (Analista Fiscal) fica mais baixo em vez de esticado com espaço vazio, por
 * instrução explícita da tarefa de implementação.
 *
 * Espera, definidas pelo chamador antes do include:
 *
 *   $jobsSection = [
 *       'jobs' => [ // config/jobs.php
 *           ['title' => ..., 'requirements' => [...], 'differentials' => [...]],
 *           ...
 *       ],
 *       'apply_url' => 'URL do sistema de recrutamento — SEMPRE vinda de
 *                       config/company.php[\'sistemas_externos\'][\'recrutamento\'], nunca
 *                       hardcoded aqui nem duplicada em config/jobs.php',
 *   ];
 */

$jobs = $jobsSection['jobs'] ?? [];
$applyUrl = $jobsSection['apply_url'] ?? '';
?>
<section class="jobs-section">
    <div class="jobs-section__container">
        <div class="jobs-section__grid">
            <?php foreach ($jobs as $job): ?>
            <?php
                $title = $job['title'] ?? '';
                $requirements = $job['requirements'] ?? [];
                $differentials = $job['differentials'] ?? [];
            ?>
            <article class="job-card">
                <h3 class="job-card__title"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h3>

                <?php if ($requirements): ?>
                <div class="job-card__block">
                    <h4 class="job-card__label">Pré-requisitos</h4>
                    <ul class="job-card__list">
                        <?php foreach ($requirements as $item): ?>
                        <li><?= htmlspecialchars($item, ENT_QUOTES, 'UTF-8') ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <?php if ($differentials): ?>
                <div class="job-card__block">
                    <h4 class="job-card__label">Diferencial</h4>
                    <ul class="job-card__list">
                        <?php foreach ($differentials as $item): ?>
                        <li><?= htmlspecialchars($item, ENT_QUOTES, 'UTF-8') ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <?php if ($applyUrl !== ''): ?>
                <a class="job-card__cta" href="<?= htmlspecialchars($applyUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer">
                    Candidatar-se
                    <span class="sr-only"> para a vaga de <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?> — abre o sistema de recrutamento em uma nova guia</span>
                </a>
                <?php endif; ?>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
