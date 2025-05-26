<?php
// admin/devis.php (Gestion des demandes de devis)

// Inclure le fichier de configuration (connexion BDD et session)
require_once '../config/config.php'; // Le chemin est relatif à admin/

// Vérifier si l'utilisateur est connecté, sinon rediriger vers la page de connexion
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php'); // Rediriger vers la page de connexion
    exit;
}

$message = ''; // Message de succès ou d'erreur

// --- Traitement des actions (marquer comme traité) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'mark_processed') {
    $devis_id = (int)($_POST['devis_id'] ?? 0);

    if ($devis_id > 0) {
        try {
            $stmt = $pdo->prepare("UPDATE devis_requests SET is_processed = TRUE, updated_at = NOW() WHERE id = :id");
            $stmt->execute(['id' => $devis_id]);
            $message = "Demande de devis #{$devis_id} marquée comme traitée avec succès.";
        } catch (PDOException $e) {
            $message = "Erreur lors du traitement de la demande : " . $e->getMessage();
        }
    } else {
        $message = "ID de devis invalide.";
    }
}


// --- Récupération des demandes de devis ---
$filter_processed = $_GET['filter'] ?? 'pending'; // 'pending', 'processed', 'all'
$sql = "SELECT * FROM devis_requests";
$params = [];

if ($filter_processed === 'pending') {
    $sql .= " WHERE is_processed = FALSE";
} elseif ($filter_processed === 'processed') {
    $sql .= " WHERE is_processed = TRUE";
}
$sql .= " ORDER BY created_at DESC"; // Les plus récents en premier

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $devis_requests = $stmt->fetchAll();
} catch (PDOException $e) {
    $devis_requests = [];
    $message = "Erreur lors de la récupération des devis : " . $e->getMessage();
    // En production, vous devriez logger cette erreur plutôt que de l'afficher directement
    // error_log("Erreur PDO admin/devis.php : " . $e->getMessage());
}


// Titre pour la page HTML
$page_title = "Administration - Demandes de Devis";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link rel="stylesheet" href="../assets/css/style.css"> <style>
        /* Styles d'administration (reprise de dashboard.php) */
        body { background-color: #f0f2f5; font-family: 'Open Sans', sans-serif; }
        .admin-header { background-color: #343a40; color: #f9f9f9; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.2); }
        .admin-header h1 { margin: 0; font-size: 1.5em; color: #f9f9f9; font-family: 'Montserrat', sans-serif;}
        .admin-header .user-info { font-size: 0.9em; }
        .admin-header .user-info a { color: #A0B26E; text-decoration: none; margin-left: 15px; }
        .admin-header .user-info a:hover { text-decoration: underline; }

        .admin-nav { background-color: #212529; padding: 10px 0; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .admin-nav ul { list-style: none; display: flex; justify-content: center; padding: 0; margin: 0; }
        .admin-nav ul li a { display: block; color: #f9f9f9; text-decoration: none; padding: 10px 20px; transition: background-color 0.3s ease; }
        .admin-nav ul li a:hover { background-color: #A0B26E; }
        .admin-nav ul li a.active { background-color: #A0B26E; } /* Pour marquer la page active */


        /* Styles spécifiques à la page Devis */
        .admin-content { padding: 40px 20px; max-width: 1400px; margin: 20px auto; background-color: #fff; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .admin-content h2 { text-align: center; color: #343a40; margin-bottom: 30px; font-size: 2.5em; }
        .filter-buttons { text-align: center; margin-bottom: 30px; }
        .filter-buttons a {
            display: inline-block;
            padding: 8px 15px;
            margin: 0 5px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            text-decoration: none;
            color: #343a40;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
        }
        .filter-buttons a.active, .filter-buttons a:hover {
            background-color: #A0B26E;
            color: #fff;
            border-color: #A0B26E;
        }

        .devis-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .devis-table th, .devis-table td { border: 1px solid #dee2e6; padding: 12px; text-align: left; }
        .devis-table th { background-color: #e9ecef; color: #343a40; font-weight: 600; }
        .devis-table tr:nth-child(even) { background-color: #f8f9fa; }
        .devis-table tr:hover { background-color: #e2e6ea; }

        .devis-table td.actions { text-align: center; white-space: nowrap; }
        .devis-table .btn-action { padding: 5px 10px; font-size: 0.85em; }
        .devis-table .btn-view { background-color: #A0B26E; color: #fff; border: 1px solid #A0B26E; }
        .devis-table .btn-view:hover { background-color: #8E9F5A; border-color: #8E9F5A; }
        .devis-table .btn-process { background-color: #28a745; color: #fff; border: 1px solid #28a745; }
        .devis-table .btn-process:hover { background-color: #218838; border-color: #218838; }
        .devis-table .status-processed { color: #28a745; font-weight: bold; }
        .devis-table .status-pending { color: #dc3545; font-weight: bold; }

        .devis-detail-modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1001; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgba(0,0,0,0.6); /* Black w/ opacity */
        }
        .devis-detail-modal-content {
            background-color: #fefefe;
            margin: 5% auto; /* 5% from the top and centered */
            padding: 30px;
            border-radius: 10px;
            width: 90%; /* Could be more or less, depending on screen size */
            max-width: 800px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            position: relative;
        }
        .devis-detail-modal-content h3 { color: #A0B26E; margin-bottom: 20px; font-size: 2em; text-align: center; }
        .devis-detail-modal-content p { margin-bottom: 10px; font-size: 1em; }
        .devis-detail-modal-content strong { color: #343a40; }
        .devis-detail-modal-content .close-button {
            color: #aaa;
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .devis-detail-modal-content .close-button:hover,
        .devis-detail-modal-content .close-button:focus {
            color: #333;
            text-decoration: none;
            cursor: pointer;
        }
        .devis-detail-modal-content .file-link { word-break: break-all; }
        .message { margin-bottom: 20px; padding: 10px; border-radius: 5px; }
        .message.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        /* Responsive */
        @media (max-width: 768px) {
            .admin-header { flex-direction: column; text-align: center; }
            .admin-header h1 { margin-bottom: 10px; }
            .admin-header .user-info { margin-top: 10px; }
            .admin-nav ul { flex-direction: column; }
            .admin-content { padding: 20px; margin: 10px; }
            .devis-table, .devis-table tbody, .devis-table th, .devis-table td, .devis-table tr { display: block; }
            .devis-table thead tr { position: absolute; top: -9999px; left: -9999px; } /* Hide headers */
            .devis-table tr { border: 1px solid #dee2e6; margin-bottom: 15px; border-radius: 8px; overflow: hidden;}
            .devis-table td { border: none; border-bottom: 1px solid #eee; position: relative; padding-left: 50%; text-align: right; }
            .devis-table td:before { /* Simulate header for mobile */
                position: absolute;
                top: 6px;
                left: 6px;
                width: 45%;
                padding-right: 10px;
                white-space: nowrap;
                content: attr(data-label); /* Use data-label attribute for content */
                font-weight: bold;
                text-align: left;
                color: #555;
            }
            .devis-table td.actions { text-align: right; padding-left: 10px; }
            .devis-table td.actions:before { content: ""; display: none;}
            .devis-detail-modal-content { margin: 10% auto; padding: 20px; width: 95%; }
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <h1><?php echo htmlspecialchars(SITE_NAME); ?></h1>
        <div class="user-info">
            Bienvenue, <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Administrateur'); ?> !
            <a href="logout.php">Déconnexion</a>
        </div>
    </header>

    <nav class="admin-nav">
        <ul>
            <li><a href="dashboard.php">Tableau de Bord</a></li>
            <li><a href="devis.php" class="active">Demandes de Devis</a></li>
            <li><a href="messages.php">Messages de Contact</a></li>
            <li><a href="products.php">Gestion des Produits</a></li>
        </ul>
    </nav>

    <main class="admin-content">
        <h2>Demandes de Devis</h2>

        <?php if (!empty($message)): ?>
            <div class="message <?php echo strpos($message, 'succès') !== false ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="filter-buttons">
            <a href="?filter=pending" class="<?php echo ($filter_processed === 'pending') ? 'active' : ''; ?>">En Attente</a>
            <a href="?filter=processed" class="<?php echo ($filter_processed === 'processed') ? 'active' : ''; ?>">Traitées</a>
            <a href="?filter=all" class="<?php echo ($filter_processed === 'all') ? 'active' : ''; ?>">Toutes</a>
        </div>

        <?php if (!empty($devis_requests)): ?>
        <table class="devis-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Nom Client</th>
                    <th>Email</th>
                    <th>Type Projet</th>
                    <th>Prix Approx.</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($devis_requests as $devis): ?>
                <tr>
                    <td data-label="ID"><?php echo htmlspecialchars($devis['id']); ?></td>
                    <td data-label="Date"><?php echo date('d/m/Y H:i', strtotime($devis['created_at'])); ?></td>
                    <td data-label="Nom Client"><?php echo htmlspecialchars($devis['prenom'] . ' ' . $devis['nom']); ?></td>
                    <td data-label="Email"><a href="mailto:<?php echo htmlspecialchars($devis['email']); ?>"><?php echo htmlspecialchars($devis['email']); ?></a></td>
                    <td data-label="Type Projet"><?php echo htmlspecialchars($devis['project_type']); ?></td>
                    <td data-label="Prix Approx."><?php echo number_format($devis['approx_price'], 2, ',', '.') . ' €'; ?></td>
                    <td data-label="Statut" class="<?php echo $devis['is_processed'] ? 'status-processed' : 'status-pending'; ?>">
                        <?php echo $devis['is_processed'] ? 'Traitée' : 'En Attente'; ?>
                    </td>
                    <td data-label="Actions" class="actions">
                        <button class="btn-action btn-view" onclick="openDevisModal(<?php echo htmlspecialchars(json_encode($devis)); ?>)">Voir</button>
                        <?php if (!$devis['is_processed']): ?>
                            <form method="POST" style="display:inline-block;" onsubmit="return confirm('Êtes-vous sûr de vouloir marquer ce devis comme traité ?');">
                                <input type="hidden" name="action" value="mark_processed">
                                <input type="hidden" name="devis_id" value="<?php echo htmlspecialchars($devis['id']); ?>">
                                <button type="submit" class="btn-action btn-process">Traiter</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p style="text-align: center; margin-top: 50px;">Aucune demande de devis trouvée pour le filtre sélectionné.</p>
        <?php endif; ?>
    </main>

    <div id="devisDetailModal" class="devis-detail-modal">
        <div class="devis-detail-modal-content">
            <span class="close-button" onclick="closeDevisModal()">&times;</span>
            <h3>Détails de la Demande de Devis #<span id="modalDevisId"></span></h3>
            <p><strong>Date :</strong> <span id="modalCreatedAt"></span></p>
            <p><strong>Client :</strong> <span id="modalClientName"></span></p>
            <p><strong>Email :</strong> <span id="modalClientEmail"></span></p>
            <p><strong>Téléphone :</strong> <span id="modalClientPhone"></span></p>
            <p><strong>Type de Projet :</strong> <span id="modalProjectType"></span></p>
            <p><strong>Texte/Nom du Projet :</strong> <span id="modalProjectText"></span></p>
            <p><strong>Couleur du Tour :</strong> <span id="modalColorTour"></span></p>
            <p><strong>Éclairage :</strong> <span id="modalLighting"></span></p>
            <p><strong>Option Supplémentaire :</strong> <span id="modalOptionSup"></span></p>
            <p><strong>Description du Projet :</strong><br><span id="modalProjectDescription"></span></p>
            <p><strong>Prix Approximatif :</strong> <span id="modalApproxPrice"></span></p>
            <p><strong>Statut :</strong> <span id="modalIsProcessed"></span></p>
            <p><strong>Fichiers joints :</strong> <span id="modalUploadedFiles"></span></p>
        </div>
    </div>

    <script>
        // JavaScript pour la modale de détails
        var modal = document.getElementById("devisDetailModal");
        var span = document.getElementsByClassName("close-button")[0];

        function openDevisModal(devis) {
            document.getElementById("modalDevisId").textContent = devis.id;
            document.getElementById("modalCreatedAt").textContent = new Date(devis.created_at).toLocaleString('fr-FR', {
                year: 'numeric', month: 'numeric', day: 'numeric', hour: '2-digit', minute: '2-digit'
            });
            document.getElementById("modalClientName").textContent = devis.prenom + ' ' + devis.nom;
            document.getElementById("modalClientEmail").innerHTML = '<a href="mailto:' + devis.email + '">' + devis.email + '</a>';
            document.getElementById("modalClientPhone").textContent = devis.telephone || 'Non fourni';
            document.getElementById("modalProjectType").textContent = devis.project_type;
            document.getElementById("modalProjectText").textContent = devis.project_text || 'N/A';
            document.getElementById("modalColorTour").textContent = devis.color_tour || 'N/A';
            document.getElementById("modalLighting").textContent = devis.lighting || 'N/A';
            document.getElementById("modalOptionSup").textContent = devis.option_sup || 'N/A';
            document.getElementById("modalProjectDescription").textContent = devis.project_description;
            document.getElementById("modalApproxPrice").textContent = new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(devis.approx_price);
            document.getElementById("modalIsProcessed").textContent = devis.is_processed ? 'Traitée' : 'En Attente';

            var filesHtml = '';
            if (devis.uploaded_files_paths) {
                // Les chemins sont stockés en JSON, il faut le parser
                try {
                    var filePaths = JSON.parse(devis.uploaded_files_paths);
                    if (filePaths.length > 0) {
                        filePaths.forEach(function(path) {
                            // Laravel stocke dans storage/app/public/... qui devient /storage/... en public
                            // En PHP pur, vous devez vous assurer que le dossier /storage/ existe à la racine du site
                            // Et que les fichiers uploadés y sont.
                            var publicPath = path.replace('storage/', '/storage/');
                            filesHtml += '<li><a href="' + publicPath + '" target="_blank" class="file-link">' + publicPath.substring(publicPath.lastIndexOf('/') + 1) + '</a></li>';
                        });
                        document.getElementById("modalUploadedFiles").innerHTML = '<ul>' + filesHtml + '</ul>';
                    } else {
                        document.getElementById("modalUploadedFiles").textContent = 'Aucun fichier joint.';
                    }
                } catch (e) {
                    document.getElementById("modalUploadedFiles").textContent = 'Erreur de lecture des chemins de fichiers.';
                    console.error("Erreur de parsing JSON pour les chemins de fichiers:", e);
                }
            } else {
                 document.getElementById("modalUploadedFiles").textContent = 'Aucun fichier joint.';
            }

            modal.style.display = "block";
        }

        function closeDevisModal() {
            modal.style.display = "none";
        }

        // Close the modal when clicking outside of it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>