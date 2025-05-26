<?php
// index.php

// Définir le titre de la page
$page_title = "Accueil - CréaMod3D : Modélisation, Impression & Conception 3D sur Mesure";
// Définir la méta description spécifique à la page d'accueil
$meta_description = "CréaMod3D, votre expert en modélisation, impression et conception 3D. Donnez vie à vos projets, du prototype à la série, avec notre savoir-faire unique.";
// Définir les mots-clés spécifiques (optionnel, mais peut aider certaines plateformes)
$meta_keywords = "accueil 3D, services 3D, entreprise 3D, CréaMod3D, modélisation 3D, impression 3D, conception produit, prototypage";
// Définir la page courante pour la navigation active
$current_page = 'accueil';

// Pour Open Graph (facultatif si vous utilisez les valeurs par défaut du header)
// Il est recommandé de spécifier des URL absolues pour les images OG
$og_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/index.php";
$og_image = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/assets/img/og-image-accueil.jpg"; // Créez cette image !


// --- Simulation de données dynamiques (ces données viendraient de votre BDD/admin) ---
// Ces données seraient gérées par votre administration.
$hero_title = "Transformez vos idées en réalité 3D";
$hero_subtitle = "Modélisation, Impression & Conception 3D sur mesure pour professionnels et particuliers.";
$hero_button_text = "Découvrir nos services";
$hero_button_link = "boutique.php"; // Ou vers une ancre sur la page

$about_title = "Qui sommes-nous ?";
$about_text = "Chez CréaMod3D, nous sommes passionnés par l'innovation et la création. Nous mettons notre expertise en modélisation et impression 3D au service de vos projets les plus audacieux, de la conception à la réalisation. Notre équipe allie savoir-faire technique et créativité pour donner vie à vos concepts les plus complexes, avec une précision et une qualité inégalées.";

$services = [
    [
        "icon" => "fa-solid fa-cube",
        "title" => "Modélisation 3D",
        "description" => "Création de modèles 3D détaillés à partir de croquis, photos ou idées, prêts pour l'impression ou le rendu."
    ],
    [
        "icon" => "fa-solid fa-print",
        "title" => "Impression 3D",
        "description" => "Production de pièces physiques et prototypes avec diverses technologies (FDM, SLA, SLS) et matériaux de pointe."
    ],
    [
        "icon" => "fa-solid fa-lightbulb",
        "title" => "Conception Produit",
        "description" => "De l'idée au prototype, nous vous accompagnons dans le développement complet de vos produits, optimisés pour la fabrication."
    ]
];

$cta_title = "Prêt à concrétiser votre projet ?";
$cta_button_text = "Demander un devis détaillé";
$cta_button_link = "devis.php";

// Inclure le header (qui utilise les variables définies ci-dessus)
require_once 'includes/header.php';
?>

        <section class="hero-section">
            <div class="container">
                <h2><?php echo $hero_title; ?></h2>
                <p><?php echo $hero_subtitle; ?></p>
                <a href="<?php echo $hero_button_link; ?>" class="btn primary-btn">
                    <?php echo $hero_button_text; ?>
                </a>
            </div>
        </section>

        <section class="about-section">
            <div class="container">
                <h3><?php echo $about_title; ?></h3>
                <p><?php echo $about_text; ?></p>
            </div>
        </section>

        <section class="services-section">
            <div class="container">
                <h3>Nos Services</h3>
                <div class="services-grid">
                    <?php foreach ($services as $service): ?>
                        <div class="service-item">
                            <i class="<?php echo $service['icon']; ?> service-icon"></i>
                            <h4><?php echo $service['title']; ?></h4>
                            <p><?php echo $service['description']; ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="cta-section">
            <div class="container">
                <h2><?php echo $cta_title; ?></h2>
                <a href="<?php echo $cta_button_link; ?>" class="btn primary-btn">
                    <?php echo $cta_button_text; ?>
                </a>
            </div>
        </section>

        <?php
// Inclure le footer
require_once 'includes/footer.php';
?>