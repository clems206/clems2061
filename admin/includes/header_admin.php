<?php
// admin/includes/header_admin.php

// Si la session n'est pas déjà démarrée (par la page appelante comme index.php), la démarrer.
// Utile si on oublie de le faire sur une nouvelle page admin.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Double vérification : s'assurer que l'administrateur est connecté.
// Normalement, la page appelante (ex: admin/index.php) fait déjà cette vérification.
// Ceci est une sécurité supplémentaire.
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas authentifié
    // Le message 'session_expired' peut être géré par login.php pour informer l'utilisateur.
    header('Location: login.php?status=session_expired');
    exit;
}

// Récupérer le nom d'utilisateur stocké dans la session pour l'affichage (optionnel)
$admin_username_display = isset($_SESSION['admin_username']) ? htmlspecialchars($_SESSION['admin_username']) : 'Admin';

// Titre de la page (doit être défini dans la page qui inclut ce header, sinon titre par défaut)
if (!isset($page_admin_title)) {
    $page_admin_title = "Administration";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_admin_title); ?> - CréaMod3D Admin</title>
    <link rel="stylesheet" href="assets/css/admin_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="admin-wrapper">
        <header class="admin-header">
            <div class="admin-header-container">
                <div class="admin-logo">
                    <a href="index.php">CréaMod3D - Administration</a>
                </div>
                <nav class="admin-navigation">
                    <ul>
                        <li><a href="index.php" class="<?php echo ($page_admin_title == 'Tableau de Bord') ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt"></i> Tableau de Bord</a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle"><i class="fas fa-edit"></i> Gestion Contenu <i class="fas fa-caret-down"></i></a>
                            <ul class="dropdown-menu">
                                <li><a href="manage_home_content.php">Page d'Accueil</a></li>
                                <li><a href="manage_services.php">Services</a></li>
                                <li><a href="manage_contact_details.php">Coordonnées</a></li>
                                </ul>
                        </li>
                        <li class="dropdown">
                             <a href="#" class="dropdown-toggle"><i class="fas fa-envelope"></i> Soumissions <i class="fas fa-caret-down"></i></a>
                             <ul class="dropdown-menu">
                                <li><a href="view_contact_messages.php">Messages Contact</a></li>
                                <li><a href="view_devis_requests.php">Demandes de Devis</a></li>
                             </ul>
                        </li>
                        <li><a href="settings.php" class="<?php echo ($page_admin_title == 'Paramètres') ? 'active' : ''; ?>"><i class="fas fa-cog"></i> Paramètres</a></li>
                        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion (<?php echo $admin_username_display; ?>)</a></li>
                    </ul>
                </nav>
            </div>
        </header>

        <main class="admin-main-content">
            <div class="admin-container">
                <h1><?php echo htmlspecialchars($page_admin_title); ?></h1>