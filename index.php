<?php
/**
 * index.php — Home
 *
 * Nesta etapa: topbar, header, footer e todas as seções de conteúdo da Home foram
 * implementados visualmente (ver docs/reference/home-desktop-audit.md,
 * docs/reference/home-tablet-audit.md e docs/reference/home-mobile-audit.md). Cookie banner e
 * botão flutuante de WhatsApp permanecem como placeholder — ver docs/architecture-proposal.md.
 *
 * O Hero é renderizado por components/hero-slider.php, a seção "Bem-vindo à CT Price" por
 * components/welcome-section.php, "Ética, agilidade..." (vídeo institucional) por
 * components/video-section.php, "Nossos Serviços" por components/services-section.php,
 * "O que dizem nossos clientes" por components/testimonials-section.php, o carrossel de
 * clientes/parceiros por components/clients-carousel-section.php, "Por que nos escolher?" por
 * components/why-choose-us-section.php, "Últimas notícias" por components/blog-section.php e
 * "Quer receber um contato?" por components/contact-section.php — esta página só define os
 * dados de cada um (conteúdo real e confirmado do site original) e inclui os componentes. O
 * footer (includes/footer.php) é global e reutilizado por todas as páginas do site, não
 * específico da Home.
 *
 * Com esta seção, todas as 13 seções de nível superior documentadas no baseline
 * (docs/reference/home-desktop-audit.md, seção 1) estão implementadas.
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

$videoSectionHeading = 'Ética, agilidade, segurança nos processos e respeito ao cliente';
$videoSectionHtml = 'A <strong class="video-section__highlight">CT Price</strong> nasceu determinada a conquistar o mercado com eficiência e dedicação, valorizando sempre o cliente e preocupando-se em encontrar soluções adequadas para cada situação.';
$videoSectionSrc = BASE_URL . '/assets/videos/institucional-ct-price.mp4';
$videoSectionCover = BASE_URL . '/assets/images/content/institucional-video-cover.jpg';

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

$testimonials = [
    [
        'text' => "\"Nossa história com a CT Price começa com a necessidade de mudanças e essa oportunidade de trilhar novos caminhos. Há quase 30 anos no ramo de alimentação, a Roasted Potato – Campo Grande precisava de uma Empresa Contábil capaz de visualizar, planejar, organizar, orientar e conduzir as mudanças necessárias com segurança e profissionalismo.\nA CT Price, sob a coordenação do Marcelo procura entender o negócio da empresa com suas características particulares e estrategicamente projeta caminhos e possibilidades seguras de crescimento.\nEstamos certos e confiantes de que, juntos, continuaremos a avançar com ainda mais segurança e inovação.\nSomos gratos a todos da Equipe CT Price pelo empenho e profissionalismo.\"",
        'avatar' => BASE_URL . '/assets/images/testimonials/roasted-potato.jpg',
        'name' => 'Edvaldo Cezar Germiniani',
        'company' => 'ROASTED POTATO',
    ],
    [
        'text' => '"Quero agradecer à família CT Price pela parceria há mais de 10 anos. Sempre tivemos um atendimento especial de todos os setores, RH, Fiscal, dentre outros. Podemos contar com uma consultoria de alto nível."',
        'avatar' => BASE_URL . '/assets/images/testimonials/agrotouro.jpg',
        'name' => 'Mário Jorge',
        'company' => 'AgroTouro',
    ],
    [
        'text' => "\"A CT PRICE ORGANIZAÇÃO CONTÁBIL é uma empresa que respira e vive na qualidade.\nCom seus princípios fundamentados na defesa da empresa e do empresário frente as adversidades todas, com uma equipe coesa e participativa que atua de maneira firme e ágil, instrumentalizada na participação de cada um como membro de uma equipe que se propõe e alcança os resultados finais.\nCapitaneada pelo contabilista Marcelo Barbosa da Silva, você pode acreditar, compromisso e confiança são seus ideais e se você tem um problema, CT PRICE é a sua solução.\"",
        'avatar' => BASE_URL . '/assets/images/testimonials/mauro-cesar-senna.png',
        'name' => 'Mauro César Senna',
        'company' => 'INTELECTA SOLUÇÕES EMPRESARIAIS',
    ],
    [
        'text' => '"A CT PRICE se destaca pelo seu profissionalismo na prestação de serviços contábeis e de planejamento tributário. Com uma equipe dedicada e competente, a empresa tem proporcionado ganhos significativos para os empresários, comprovados pelo sucesso dos clientes atendidos ao longo dos anos. A precisão, ética e agilidade da CT PRICE garantem resultados positivos e a confiança de todos que trabalham com eles."',
        'avatar' => BASE_URL . '/assets/images/testimonials/dieter-augusto-dreyer.png',
        'name' => 'Dieter Augusto Dreyer',
        'company' => 'PLANER SOLUÇÕES EMPRESARIAIS',
    ],
];

// 82 logos válidos do carrossel de clientes/parceiros, na ordem original (index 0-84 do widget
// "Image Carousel" original), com 3 arquivos 404 já removidos (defeito conhecido categoria C,
// docs/reference/home-desktop-audit.md seção 16: mv.jpg, modelo.jpg, logo_0020_Camada16.jpg —
// não baixados nem reproduzidos). Servidos de assets/images/clients/home-carousel/.
$clientLogos = [
    ['file' => 'vitrine.jpg', 'alt' => 'vitrine'],
    ['file' => 'uniao.jpeg', 'alt' => 'uniao'],
    ['file' => 'termo-truck.jpg', 'alt' => 'termo-truck'],
    ['file' => 'techagro.jpg', 'alt' => 'techagro'],
    ['file' => 'tcm.jpg', 'alt' => 'tcm'],
    ['file' => 'suprimed.jpeg', 'alt' => 'suprimed'],
    ['file' => 'studio-vip.jpg', 'alt' => 'studio-vip'],
    ['file' => 'so-sal.jpg', 'alt' => 'so-sal'],
    ['file' => 'soman.jpg', 'alt' => 'soman'],
    ['file' => 'smartfit.jpg', 'alt' => 'smartfit'],
    ['file' => 'sermix.jpg', 'alt' => 'sermix'],
    ['file' => 'santana-haddad.jpg', 'alt' => 'santana-haddad'],
    ['file' => 'saborzitos-removebg-preview_c63b3438.png', 'alt' => 'saborzitos-removebg-preview_c63b3438'],
    ['file' => 'pro-nutri.jpg', 'alt' => 'pro-nutri'],
    ['file' => 'paoetal.jpg', 'alt' => 'paoetal'],
    ['file' => 'omegamed_bacc5628.png', 'alt' => 'omegamed_bacc5628'],
    ['file' => 'natus_f072d16c.png', 'alt' => 'natus_f072d16c'],
    ['file' => 'multi-coisas.jpg', 'alt' => 'multi-coisas'],
    ['file' => 'mix.jpg', 'alt' => 'mix'],
    ['file' => 'meta.jpg', 'alt' => 'meta'],
    ['file' => 'macal.jpg', 'alt' => 'macal'],
    ['file' => 'lopes.jpg', 'alt' => 'lopes'],
    ['file' => 'logo-zornimat_2ae545f4.png', 'alt' => 'logo-zornimat_2ae545f4'],
    ['file' => 'logo_0034_Camada2.jpg', 'alt' => 'logo_0034_Camada2'],
    ['file' => 'logo_0024_Camada12.jpg', 'alt' => 'logo_0024_Camada12'],
    ['file' => 'logo_0022_Camada14.jpg', 'alt' => 'logo_0022_Camada14'],
    ['file' => 'logo_0018_Camada18.jpg', 'alt' => 'logo_0018_Camada18'],
    ['file' => 'logo_0017_Camada19.jpg', 'alt' => 'logo_0017_Camada19'],
    ['file' => 'logo_0014_Camada22.jpg', 'alt' => 'logo_0014_Camada22'],
    ['file' => 'logo_0003_Camada33.jpg', 'alt' => 'logo_0003_Camada33'],
    ['file' => 'logo_0002_Camada34.jpg', 'alt' => 'logo_0002_Camada34'],
    ['file' => 'liquida-1.png', 'alt' => 'líquida'],
    ['file' => 'lider-aco.jpg', 'alt' => 'lider-aco'],
    ['file' => 'ki-karnes.jpeg', 'alt' => 'ki-karnes'],
    ['file' => 'kardol.png', 'alt' => 'kardol'],
    ['file' => 'js-distribuidora_6a23f49f.jpeg', 'alt' => 'js-distribuidora_6a23f49f'],
    ['file' => 'infomacal-1.jpeg', 'alt' => 'infomacal'],
    ['file' => 'infogala-1.jpeg', 'alt' => 'infogala'],
    ['file' => 'infofruteli-1.jpeg', 'alt' => 'infofruteli'],
    ['file' => 'infoendosurgical-1.png', 'alt' => 'infoendosurgical'],
    ['file' => 'infoarkad-1.jpeg', 'alt' => 'infoarkad'],
    ['file' => 'imbra.jpg', 'alt' => 'imbra'],
    ['file' => 'image-14.png', 'alt' => 'image-14'],
    ['file' => 'image-13_785c1623.png', 'alt' => 'image-13_785c1623'],
    ['file' => 'image-12_2a5c8312.png', 'alt' => 'image-12_2a5c8312'],
    ['file' => 'image-11_e554fb56.png', 'alt' => 'image-11_e554fb56'],
    ['file' => 'image-10_1bc2a900.png', 'alt' => 'image-10_1bc2a900'],
    ['file' => 'image-9.png', 'alt' => 'image-9'],
    ['file' => 'image-4_c1b84f9b.png', 'alt' => 'image-4_c1b84f9b'],
    ['file' => 'image-3_8b445a2e.png', 'alt' => 'image-3_8b445a2e'],
    ['file' => 'ifa-1.png', 'alt' => 'ifa'],
    ['file' => 'hvm_9ecc1df3.png', 'alt' => 'hvm_9ecc1df3'],
    ['file' => 'hr-rodan.jpg', 'alt' => 'hr-rodan'],
    ['file' => 'homeo.jpeg', 'alt' => 'homeo'],
    ['file' => 'hm_80f2d712.png', 'alt' => 'hm_80f2d712'],
    ['file' => 'health-brasil_d0fe5f29.jpeg', 'alt' => 'health-brasil_d0fe5f29'],
    ['file' => 'groupacj.jpg', 'alt' => 'groupacj'],
    ['file' => 'gmad_4c26933a.png', 'alt' => 'gmad_4c26933a'],
    ['file' => 'giocondo.jpg', 'alt' => 'giocondo'],
    ['file' => 'genos.jpg', 'alt' => 'genos'],
    ['file' => 'g.png', 'alt' => 'g'],
    ['file' => 'figueira.png', 'alt' => 'figueira'],
    ['file' => 'farmacias-associadas.jpeg', 'alt' => 'farmacias-associadas'],
    ['file' => 'eco-park_b21bb592.jpg', 'alt' => 'eco-park_b21bb592'],
    ['file' => 'estoque_627cc681.jpg', 'alt' => 'estoque_627cc681'],
    ['file' => 'domine_b50ea254.jpg', 'alt' => 'domine_b50ea254'],
    ['file' => 'dimaq.jpg', 'alt' => 'dimaq'],
    ['file' => 'dib.png', 'alt' => 'dib'],
    ['file' => 'dale.jpg', 'alt' => 'dale'],
    ['file' => 'corujao_9251c69f.jpeg', 'alt' => 'corujao_9251c69f'],
    ['file' => 'comak.jpg', 'alt' => 'comak'],
    ['file' => 'close-up-person-working-call-center-scaled.jpg', 'alt' => 'close-up-person-working-call-center'],
    ['file' => 'casa-da-sementes.jpg', 'alt' => 'casa-da-sementes'],
    ['file' => 'carol-nathan_724ef560.jpg', 'alt' => 'carol-nathan_724ef560'],
    ['file' => 'capital.jpg', 'alt' => 'capital'],
    ['file' => 'artpan.jpg', 'alt' => 'artpan'],
    ['file' => 'arkad.jpeg', 'alt' => 'arkad'],
    ['file' => 'alumix.jpg', 'alt' => 'alumix'],
    ['file' => 'alfa.jpg', 'alt' => 'alfa'],
    ['file' => 'agrotouro.jpg', 'alt' => 'agrotouro'],
    ['file' => 'agro-buso_fc9ec0bc.jpeg', 'alt' => 'agro-buso_fc9ec0bc'],
    ['file' => 'agriseiva_057e47b7.png', 'alt' => 'agriseiva_057e47b7'],
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

$blogHeading = 'Últimas notícias';
$blogPosts = [
    [
        'image' => BASE_URL . '/assets/images/blog/blog03-300x155.webp',
        'category' => 'FOLHA DE PAGAMENTO',
        'title' => 'Reforma trabalhista volta à pauta do STF; julgamento acontece neste mês',
        'excerpt' => 'Julgamento será retomado sobre a validade de contrato de trabalho intermitente.',
        // Original: https://ctprice.com.br/wp/reforma-trabalhista-volta-a-pauta-do-stf-julgamento-acontece-neste-mes/
        // Sem defeito conhecido documentado (ao contrário do CTA de services-section) e a página
        // de post da nova arquitetura ainda não existe — link reproduzido como no original.
        'url' => 'https://ctprice.com.br/wp/reforma-trabalhista-volta-a-pauta-do-stf-julgamento-acontece-neste-mes/',
        'date' => 'agosto 2, 2024',
        'time' => '17:01',
    ],
    [
        'image' => BASE_URL . '/assets/images/blog/blog02-300x155.webp',
        'category' => 'INFORMATIVO',
        'title' => 'Receita Federal e Correios lançam portal de compras internacionais',
        'excerpt' => 'Ferramenta tem como objetivo auxiliar consumidores em questões de importação, desde o rastreamento até a prevenção de fraudes.',
        'url' => 'https://ctprice.com.br/wp/receita-federal-e-correios-lancam-portal-de-compras-internacionais/',
        'date' => 'agosto 2, 2024',
        'time' => '16:59',
    ],
    [
        'image' => BASE_URL . '/assets/images/blog/blog01-300x155.webp',
        'category' => 'INFORMATIVO',
        'title' => 'Novo golpe mira em empreendedores e cria sites falsos que simulam a geração de documentos',
        'excerpt' => 'Receita Federal alerta empresários sobre os sites falsos e diz que já está tomando as medidas cabíveis para tirá-los do ar.',
        // Slug "hello-world" é o post de exemplo padrão do WordPress, nunca excluído no
        // original (site-inventory.md, seção 2) — reproduzido como está, não é um link quebrado.
        'url' => 'https://ctprice.com.br/wp/hello-world/',
        'date' => 'julho 29, 2024',
        'time' => '13:53',
    ],
];

$contactHeading = 'Quer receber um contato?';
$contactText = 'Gostaria de falar com um de nossos especialistas? Basta enviar seus dados e entraremos em contato em breve. <br>Você também pode nos enviar um e-mail se preferir. <br> Ou envie uma mensagem em nosso WhatsApp.<br><br>';
$contactWhatsapp = $company['whatsapp_principal']['url'];
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
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/video-section.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/services-section.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/testimonials-section.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/clients-carousel-section.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/why-choose-us-section.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/blog-section.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/contact-section.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/footer.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/whatsapp-button.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/cookie-banner.css">
</head>
<body>

<?php require __DIR__ . '/includes/topbar.php'; ?>
<?php require __DIR__ . '/includes/header.php'; ?>

<main>
    <?php require __DIR__ . '/components/hero-slider.php'; ?>
    <?php require __DIR__ . '/components/welcome-section.php'; ?>
    <?php require __DIR__ . '/components/video-section.php'; ?>
    <?php require __DIR__ . '/components/services-section.php'; ?>
    <?php require __DIR__ . '/components/testimonials-section.php'; ?>
    <?php require __DIR__ . '/components/clients-carousel-section.php'; ?>
    <?php require __DIR__ . '/components/why-choose-us-section.php'; ?>
    <?php require __DIR__ . '/components/blog-section.php'; ?>
    <?php require __DIR__ . '/components/contact-section.php'; ?>

    <!-- TODO: demais seções da Home (ver docs/reference/home-desktop-audit.md) -->
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
<?php require __DIR__ . '/includes/cookie-banner.php'; ?>
<?php require __DIR__ . '/includes/whatsapp-button.php'; ?>

<script src="<?= BASE_URL ?>/assets/vendor/swiper/swiper-bundle.min.js" defer></script>
<script src="<?= BASE_URL ?>/assets/js/header.js" defer></script>
<script src="<?= BASE_URL ?>/assets/js/cookie-banner.js" defer></script>
<script src="<?= BASE_URL ?>/assets/js/video-section.js" defer></script>
<script src="<?= BASE_URL ?>/assets/js/testimonials-init.js" defer></script>
<script src="<?= BASE_URL ?>/assets/js/clients-carousel-init.js" defer></script>
<script src="<?= BASE_URL ?>/assets/js/hero-init.js" defer></script>
<script src="<?= BASE_URL ?>/assets/js/scroll-reveal.js" defer></script>
</body>
</html>
