<?php
/**
 * index.php — Home
 *
 * Nesta etapa: topbar, header e o Hero foram implementados visualmente (ver
 * docs/reference/home-desktop-audit.md, docs/reference/home-tablet-audit.md e
 * docs/reference/home-mobile-audit.md). Demais seções, footer, cookie banner e botão de
 * WhatsApp permanecem como placeholder — ver docs/architecture-proposal.md.
 *
 * O Hero é renderizado por components/hero-slider.php, a seção "Bem-vindo à CT Price" por
 * components/welcome-section.php, "Nossos Serviços" por components/services-section.php e "Por
 * que nos escolher?" por components/why-choose-us-section.php — esta página só define os dados
 * de cada um (conteúdo real e confirmado do site original) e inclui os componentes.
 */
require __DIR__ . '/config/bootstrap.php';

$heroSlides = [
    [
        'image' => BASE_URL . '/assets/images/hero/caroussel01.jpg',
        'html' => '<span class="hero-slide__highlight">Cuide da sua empresa,</span> <br>e deixe a contabilidade nas <br>mãos de quem entende',
    ],
    [
        'image' => BASE_URL . '/assets/images/hero/csinicial02.jpg',
        'html' => 'Trabalhamos <span class="hero-slide__highlight">integrados </span>aos<br> colaboradores de sua empresa, <br>para que juntos possamos obter <br><span class="hero-slide__highlight">os melhores resultados</span>',
    ],
    [
        'image' => BASE_URL . '/assets/images/hero/caroussel02.jpg',
        'html' => 'Atuamos nos ramos de<br> contabilidade e planejamento<br> tributário em formato digital <br><span class="hero-slide__highlight">sem papel e sem burocracia</span>.',
    ],
    [
        'image' => BASE_URL . '/assets/images/hero/caroussel03a.jpg',
        'html' => 'Fornecemos informações <br> <span class="hero-slide__highlight">precisas e seguras</span> para <br>que você possa tomar <br>as <span class="hero-slide__highlight">melhores decisões </span><br>para seu negócio',
    ],
];

$welcomeHeading = 'Bem-vindo à CT Price';
$welcomeLead = 'Obtenha consultoria e suporte especializado da CT price, uma empresa de consultoria contábil que está sempre ao seu lado.';
$welcomeItems = [
    [
        'icon' => 'chart-bar',
        'title' => 'Consultoria de negócios',
        'text' => 'Mais do que simples contabilidade, nossa consultoria de negócios oferece estratégias personalizadas e soluções inteligentes para impulsionar o crescimento e o sucesso da sua empresa.',
    ],
    [
        'icon' => 'bell',
        'title' => 'Gerenciamento de riscos',
        'text' => 'Com uma abordagem estratégica de gerenciamento de riscos, ajudamos a sua empresa a identificar, reduzir e prevenir riscos, garantindo uma gestão financeira e tributária, segura e sustentável.',
    ],
    [
        'icon' => 'lightbulb',
        'title' => 'Pesquisa de<br> mercado',
        'text' => 'Combinando uma contabilidade segura e de análises de mercado detalhadas, ajudamos a sua empresa a tomar decisões embasadas em informações estratégicas valiosas, garantindo a maximização e o sucesso do seu negócio.',
    ],
    [
        'icon' => 'headset',
        'title' => 'Serviços de Qualidade',
        'text' => 'Nosso compromisso é oferecer uma prestação de serviço de excelência, agilidade e comprometimento, garantindo que sua empresa tenha um suporte contábil seguro, confiável e eficiente.',
    ],
];

$servicesEyebrow = 'Nossos Serviços';
$servicesHeading = 'Deixe a contabilidade nas mãos de quem entende!';
$servicesItems = [
    [
        'icon' => 'swatchbook',
        'title' => 'Contabilidade de Empresas',
        'text' => 'Na CT Price, tratamos cada detalhe com máxima seriedade e precisão. <br>Confie sua empresa a nós para uma gestão impecável, que assegura conformidade e oferece insights estratégicos valiosos. <br>Seu sucesso é nossa prioridade!"',
    ],
    [
        'icon' => 'building',
        'title' => 'Abertura, Alteração e Baixa de Empresas',
        'text' => 'Nosso serviço de abertura, alteração e baixa de empresas garante uma transição tranquila e eficiente em cada etapa. Com expertise e atenção aos detalhes, facilitamos todos os processos legais e burocráticos, permitindo que você foque no crescimento do seu negócio.',
    ],
    [
        'icon' => 'money-check-alt',
        'title' => 'Planejamento Tributário',
        'text' => 'Nosso planejamento tributário é projetado para otimizar sua carga fiscal e maximizar a eficiência financeira. Com uma abordagem estratégica e personalizada, garantimos conformidade e identificamos oportunidades para economizar impostos, ajudando sua empresa a prosperar com segurança.',
    ],
    [
        'icon' => 'hat-cowboy',
        'title' => 'Assessoria ao Produtor Rural',
        'text' => 'A CT Price oferece ao produtor rural suporte especializado para gestão financeira e tributária no campo. Com profundo conhecimento do setor, ajudamos você a maximizar resultados e focar no que realmente importa: O crescimento sustentável da sua produção.',
    ],
    [
        'icon' => 'users',
        'title' => 'Terceirização da Folha de Pagamento',
        'text' => 'Com nossa terceirização da folha de pagamento, sua empresa ganha em segurança e agilidade. Cuidamos de todo o processo com precisão, garantindo cumprimento das normas e tranquilidade para que você se concentre no core do seu negócio.',
    ],
    [
        'icon' => 'handshake',
        'title' => 'Vamos Além da Consultoria',
        'text' => 'Nossas parcerias em projetos de incentivos fiscais e consultoria empresarial oferecem soluções estratégicas para otimizar sua carga tributária e impulsionar o crescimento do seu negócio. Com expertise e visão de futuro, ajudamos sua empresa a alcançar novos patamares de sucesso.',
    ],
];
$servicesCta = [
    'label' => 'Fale Conosco',
    // Original aponta para https://ctprice.com.br/contato (404 — defeito conhecido categoria C,
    // docs/architecture-proposal.md seção 2). Destino corrigido para a URL funcional da nova
    // arquitetura, já usada em config/menu.php.
    'url' => '/fale-conosco/',
];

$whyChooseUsHeading = 'Por que nos escolher?';
$whyChooseUsImage = BASE_URL . '/assets/images/content/why-choose-us.jpg';
$whyChooseUsItems = [
    [
        'title' => 'Profissionalismo e Excelência',
        'text' => 'Nossa equipe de analistas é altamente qualificada e experiente em proporcionar um atendimento estratégico e personalizado para sua empresa.',
    ],
    [
        'title' => 'Foco em Resultados',
        'text' => 'Nosso suporte é sinônimo de excelência, proporcionando atendimento ágil e soluções precisas para que sua empresa prospere com mais segurança e eficiência.',
    ],
    [
        'title' => 'Equipe Altamente Qualificada',
        'text' => 'Nos destacamos pela alta performance de nosso time de profissionais, entregando resultados excepcionais com qualidade, presteza e eficiência, para o impulsionamento da sua empresa',
    ],
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CT Price</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/vendor/swiper/swiper-bundle.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/reset.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/fonts.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/variables.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/header.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/hero.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/welcome-section.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/services-section.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/why-choose-us-section.css">
</head>
<body>

<?php require __DIR__ . '/includes/topbar.php'; ?>
<?php require __DIR__ . '/includes/header.php'; ?>

<main>
    <?php require __DIR__ . '/components/hero-slider.php'; ?>
    <?php require __DIR__ . '/components/welcome-section.php'; ?>
    <?php require __DIR__ . '/components/services-section.php'; ?>
    <?php require __DIR__ . '/components/why-choose-us-section.php'; ?>

    <!-- TODO: demais seções da Home (ver docs/reference/home-desktop-audit.md) -->
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
<?php require __DIR__ . '/includes/cookie-banner.php'; ?>
<?php require __DIR__ . '/includes/whatsapp-button.php'; ?>

<script src="<?= BASE_URL ?>/assets/vendor/swiper/swiper-bundle.min.js" defer></script>
<script src="<?= BASE_URL ?>/assets/js/header.js" defer></script>
<script src="<?= BASE_URL ?>/assets/js/hero-init.js" defer></script>
<script src="<?= BASE_URL ?>/assets/js/scroll-reveal.js" defer></script>
</body>
</html>
