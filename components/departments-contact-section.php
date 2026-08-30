<?php
/**
 * components/departments-contact-section.php
 *
 * Faixa de contatos por departamento — 5 itens (Comercial, Pessoal, Fiscal, Contábil,
 * Central/Empresarial), fundo `--color-dark-teal` (#00222C), destaque `--color-accent-green`
 * (#10E36B) no nome do departamento. Padrão identificado em
 * docs/reference/fale-conosco-audit.md, seção 1 (seção `ed4ab37` da referência) — única página
 * do site onde este diretório aparece.
 *
 * Dados vêm de `config/company.php['departamentos']` — NÃO duplicados aqui. O array já traz
 * `label` e `telefone`; o link de WhatsApp é montado localmente a partir do telefone (mesmo
 * formato `55` + DDD + número já usado em todo o site), já que `config/company.php` não
 * pré-monta uma URL para cada departamento (só para o WhatsApp principal).
 *
 * DIFERENÇA CONSCIENTE em relação à referência: grade responsiva com estágio intermediário
 * (5 → 3 → 1 colunas, breakpoints 1024/767px já usados em todo o projeto) em vez do salto direto
 * "5 colunas → 1 coluna" do original — decisão de qualidade visual pré-aprovada (não é
 * obrigatório preservar um breakpoint ruim só por fidelidade).
 *
 * Espera, definida pelo chamador antes do include:
 *
 *   $departmentsContactSection = [
 *       'departments' => $company['departamentos'], // array associativo, ver config/company.php
 *   ];
 */

/**
 * Converte um telefone no formato "(67) 99232-4097" para o link `https://api.whatsapp.com/send?phone=...`
 * já usado em todo o site — mesma lógica aplicada a esses mesmos 5 números na referência
 * (confirmada item a item em docs/reference/fale-conosco-audit.md, seção 7).
 */
if (!function_exists('ctprice_department_whatsapp_url')) {
    function ctprice_department_whatsapp_url(string $telefone): string
    {
        $digits = preg_replace('/\D/', '', $telefone) ?? '';
        return $digits === '' ? '' : 'https://api.whatsapp.com/send?phone=55' . $digits;
    }
}

$departments = $departmentsContactSection['departments'] ?? [];
?>
<section class="departments-contact-section">
    <div class="departments-contact-section__inner">
        <ul class="departments-contact-section__grid">
            <?php foreach ($departments as $department):
                $label = $department['label'] ?? '';
                $telefone = $department['telefone'] ?? '';
                $whatsappUrl = $telefone !== '' ? ctprice_department_whatsapp_url($telefone) : '';
            ?>
            <li class="departments-contact-section__item">
                <span class="departments-contact-section__label"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></span>
                <?php if ($whatsappUrl): ?>
                <a class="departments-contact-section__phone" href="<?= htmlspecialchars($whatsappUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" aria-label="Falar com <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?> pelo WhatsApp: <?= htmlspecialchars($telefone, ENT_QUOTES, 'UTF-8') ?>">
                    <?= htmlspecialchars($telefone, ENT_QUOTES, 'UTF-8') ?>
                </a>
                <?php else: ?>
                <span class="departments-contact-section__phone"><?= htmlspecialchars($telefone, ENT_QUOTES, 'UTF-8') ?></span>
                <?php endif; ?>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
