<?php
// includes/header.php

// Valeurs par défaut si non définies dans la page appelante
if (!isset($page_title)) {
    $page_title = "CréaMod3D - Votre Partenaire en Création 3D : Modélisation, Impression & Conception";
}
if (!isset($meta_description)) {
    $meta_description = "CréaMod3D transforme vos idées en réalité 3D. Experts en modélisation, impression et conception 3D sur mesure pour professionnels et particuliers en France.";
}
if (!isset($meta_keywords)) {
    $meta_keywords = "modélisation 3D, impression 3D, conception produit, service 3D, prototypage, fabrication additive, devis 3D, CréaMod3D, France";
}
if (!isset($og_title)) {
    $og_title = $page_title;
}
if (!isset($og_description)) {
    $og_description = $meta_description;
}
if (!isset($og_url)) {
    // Tente de déterminer l'URL actuelle
    $og_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
}
if (!isset($og_image)) {
    // Image par défaut pour le partage social, idéalement un logo ou une image représentative
    $og_image = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/assets/img/og-image-default.jpg"; // Créez cette image !
}
if (!isset($canonical_url)) {
    $canonical_url = $og_url; // Par défaut, l'URL actuelle
}

// Pour le Schema Markup
$company_name = "CréaMod3D";
$company_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
$company_logo = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/assets/img/logo.png"; // Créez votre logo ici !
$company_phone = "+33650779503"; // Votre numéro de téléphone
$company_email = "contact@creamod3d.fr"; // Votre email

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo $page_title; ?></title>

    <meta name="description" content="<?php echo htmlspecialchars($meta_description); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($meta_keywords); ?>">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?php echo htmlspecialchars($canonical_url); ?>">

    <meta property="og:title" content="<?php echo htmlspecialchars($og_title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($og_description); ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($og_url); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($og_image); ?>">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="fr_FR">
    <meta property="og:site_name" content="<?php echo htmlspecialchars($company_name); ?>">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@VotreCompteTwitter" /> <meta name="twitter:creator" content="@VotreCompteTwitter" /> <meta name="twitter:title" content="<?php echo htmlspecialchars($og_title); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($og_description); ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($og_image); ?>">

    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">


    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">

    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Organization",
      "name": "<?php echo htmlspecialchars($company_name); ?>",
      "url": "<?php echo htmlspecialchars($company_url); ?>",
      "logo": "<?php echo htmlspecialchars($company_logo); ?>",
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "<?php echo htmlspecialchars($company_phone); ?>",
        "contactType": "customer service",
        "email": "<?php echo htmlspecialchars($company_email); ?>"
      },
      "sameAs": [
        "https://www.facebook.com/VotrePageFacebook",  // Remplacez par vos vrais liens !
        "https://www.instagram.com/VotreCompteInstagram",
        "https://www.linkedin.com/company/VotrePageLinkedIn"
      ]
    }
    </script>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "WebSite",
      "name": "<?php echo htmlspecialchars($company_name); ?>",
      "url": "<?php echo htmlspecialchars($company_url); ?>",
      "potentialAction": {
        "@type": "SearchAction",
        "target": "<?php echo htmlspecialchars($company_url); ?>/search.php?q={search_term_string}", // Adaptez si vous avez une fonction de recherche
        "query-input": "required name=search_term_string"
      }
    }
    </script>
</head>
<body>
    <header class="header">
        <div class="container">
            <h1 class="logo"><a href="index.php">CréaMod3D</a></h1>
            <?php require_once 'includes/main-nav.php'; // Inclure le menu de navigation ?>
            <button class="nav-toggle" aria-label="Toggle navigation">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>
        </div>
    </header>

    <main>