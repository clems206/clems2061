<?php
// admin/dashboard.php (Tableau de bord de l'administration)

// Inclure le fichier de configuration (connexion BDD et session)
require_once '../config/config.php'; // Le chemin est relatif à admin/

// Vérifier si l'utilisateur est connecté, sinon rediriger vers la page de connexion
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php'); // Rediriger vers la page de connexion
    exit;
}

// Récupérer les informations de l'administrateur connecté
$admin_name = $_SESSION['admin_name'] ?? 'Administrateur';

// --- Pour l'exemple, récupération du nombre de devis et messages ---
// Vous pouvez étendre ceci pour afficher des statistiques réelles

// Compter les demandes de devis non traitées
try {
    $stmt_devis = $pdo->query("SELECT COUNT(*) FROM devis_requests WHERE is_processed = FALSE");
    $pending_devis_count = $stmt_devis->fetchColumn();
} catch (PDOException $e) {
    $pending_devis_count = 'Erreur';
    // Log l'erreur : error_log("Erreur PDO : " . $e->getMessage());
}

// Compter les messages de contact non lus
try {
    $stmt_messages = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = FALSE");
    $unread_messages_count = $stmt_messages->fetchColumn();
} catch (PDOException $e) {
    $unread_messages_count = 'Erreur';
    // Log l'erreur : error_log("Erreur PDO : " . $e->getMessage());
}


// Titre pour la page HTML
$page_title = "Administration - Tableau de Bord";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link rel="stylesheet" href="../assets/css/style.css"> <style>
        /* Styles spécifiques à l'administration */
        body { background-color: #f0f2f5; font-family: 'Open Sans', sans-serif; }
        .admin-header { background-color: #343a40; color: #f9f9f9; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.2); }
        .admin-header h1 { margin: 0; font-size: 1.5em; color: #f9f9f9; font-family: 'Montserrat', sans-serif;}
        .admin-header .user-info { font-size: 0.9em; }
        .admin-header .user-info a { color: #A0B26E; text-decoration: none; margin-left: 15px; }
        .admin-header .user-info a:hover { text-decoration: underline; }

        .admin-nav { background-color: #212529; padding: 10px 0; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .admin-nav ul { list-style: none; display: flex; justify-content: center; padding: 0; margin: 0; }
        .admin-nav ul li a { display: block; color: #f9f9f9; text-decoration: none; padding: 10px 20px; transition: background-color 0.3s ease; }
        .admin-nav ul li a:hover, .admin-nav ul li a.active { background-color: #A0B26E; } /* Vert-olive pour actif/hover */

        .dashboard-content { padding: 40px 20px; max-width: 1200px; margin: 20px auto; background-color: #fff; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .dashboard-content h2 { text-align: center; color: #343a40; margin-bottom: 30px; font-size: 2.5em; }
        .dashboard-stats { display: flex; justify-content: space-around; flex-wrap: wrap; gap: 30px; margin-top: 40px; }
        .stat-card { background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 25px; text-align: center; flex: 1; min-width: 280px; max-width: 32%; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .stat-card h3 { color: #A0B26E; font-size: 1.8em; margin-bottom: 10px; }
        .stat-card p { font-size: 2.5em; font-weight: bold; color: #343a40; }

        .dashboard-actions { text-align: center; margin-top: 50px; }
        .dashboard-actions h3 { color: #343a40; margin-bottom: 25px; font-size: 1.8em; }
        .action-links { display: flex; flex-direction: column; align-items: center; gap: 15px; }
        .action-links .btn { min-width: 250px; } /* Ajuster la largeur des boutons */

        /* Responsive */
        @media (max-width: 768px) {
            .admin-header { flex-direction: column; text-align: center; }
            .admin-header h1 { margin-bottom: 10px; }
            .admin-header .user-info { margin-top: 10px; }
            .admin-nav ul { flex-direction: column; }
            .stat-card { max-width: 100%; }
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <h1><?php echo htmlspecialchars(SITE_NAME); ?></h1>
        <div class="user-info">
            Bienvenue, <?php echo htmlspecialchars($admin_name); ?> !
            <a href="logout.php">Déconnexion</a>
        </div>
    </header>

    <nav class="admin-nav">
        <ul>
            <li><a href="dashboard.php" class="active">Tableau de Bord</a></li>
            <li><a href="devis.php">Demandes de Devis</a></li>
            <li><a href="messages.php">Messages de Contact</a></li>
            <li><a href="products.php">Gestion des Produits</a></li>
            </ul>
    </nav>

    <main class="dashboard-content">
        <h2>Tableau de Bord</h2>

        <div class="dashboard-stats">
            <div class="stat-card">
                <h3>Demandes de Devis en Attente</h3>
                <p><?php echo htmlspecialchars($pending_devis_count); ?></p>
            </div>
            <div class="stat-card">
                <h3>Messages Non Lus</h3>
                <p><?php echo htmlspecialchars($unread_messages_count); ?></p>
            </div>
            <div class="stat-card">
                <h3>Produits en Ligne</h3>
                <p>N/A</p> </div>
        </div>

        <div class="dashboard-actions">
            <h3>Actions Rapides</h3>
            <div class="action-links">
                <a href="devis.php" class="btn primary-btn">Voir les Demandes de Devis</a>
                <a href="messages.php" class="btn primary-btn">Voir les Messages de Contact</a>
                <a href="products.php" class="btn primary-btn">Gérer les Produits</a>
                </div>
        </div>
    </main>

    </body>
</html>