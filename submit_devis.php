<?php
// submit_devis.php (Traitement du formulaire de demande de devis)

// Inclure le fichier de configuration (connexion BDD et session)
require_once '../config/config.php'; // Le chemin est relatif à la racine du site si ce fichier est dans public_html/submit_devis.php

// Ne pas afficher les erreurs en production. Pour le débogage, décommentez temporairement.
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- 1. Récupération et NETTOYAGE des données du formulaire ---
    // Utilisation de htmlspecialchars et trim pour la sécurité et la propreté des données
    $nom = htmlspecialchars(trim($_POST['nom'] ?? ''));
    $prenom = htmlspecialchars(trim($_POST['prenom'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $telephone = htmlspecialchars(trim($_POST['telephone'] ?? '')); // Le champ téléphone est facultatif
    $project_type = htmlspecialchars(trim($_POST['project_type'] ?? ''));
    $project_text = htmlspecialchars(trim($_POST['project_text'] ?? ''));
    $color_tour = htmlspecialchars(trim($_POST['color_tour'] ?? 'N/A'));
    $lighting = htmlspecialchars(trim($_POST['lighting'] ?? 'N/A'));
    $option_sup = htmlspecialchars(trim($_POST['option_sup'] ?? 'Aucune'));
    $project_description = htmlspecialchars(trim($_POST['project_description'] ?? ''));

    // --- 2. Validation simple (vous devriez ajouter des validations plus robustes en production) ---
    // Vérifie si les champs obligatoires sont remplis
    if (empty($nom) || empty($prenom) || empty($email) || empty($project_type) || empty($project_description)) {
        header("Location: thanks.php?status=error&msg=missing_fields");
        exit();
    }

    // Vérifie le format de l'email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: thanks.php?status=error&msg=invalid_email");
        exit();
    }

    // --- 3. Traitement des fichiers uploadés (si présents) ---
    $uploaded_files_info = []; // Pour stocker les informations des fichiers uploadés pour l'email admin
    $uploaded_files_paths_db = []; // Pour stocker les chemins relatifs pour la BDD (JSON)

    $upload_dir_base = __DIR__ . '/../storage/uploads/devis/'; // Chemin absolu du dossier de stockage

    // Assurez-vous que le répertoire de téléchargement existe et est inscriptible
    if (!is_dir($upload_dir_base)) {
        mkdir($upload_dir_base, 0755, true); // Crée le répertoire récursivement si non existant
    }

    if (isset($_FILES['project_files']) && is_array($_FILES['project_files']['name'])) {
        $total_files = count($_FILES['project_files']['name']);
        for ($i = 0; $i < $total_files; $i++) {
            $file_name = $_FILES['project_files']['name'][$i];
            $file_tmp_name = $_FILES['project_files']['tmp_name'][$i];
            $file_error = $_FILES['project_files']['error'][$i];
            $file_size = $_FILES['project_files']['size'][$i];

            $max_file_size = 10 * 1024 * 1024; // 10 MB par fichier

            if ($file_error === UPLOAD_ERR_OK) {
                if ($file_size > $max_file_size) {
                    $uploaded_files_info[] = "Fichier '$file_name' trop volumineux (max 10MB).";
                    continue; // Passe au fichier suivant
                }

                // Génère un nom de fichier unique et sécurisé pour éviter les collisions et les injections de chemin
                $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
                $unique_file_name = uniqid('devis_', true) . '.' . $file_extension; // devis_uniqueID.ext
                $target_file_path = $upload_dir_base . $unique_file_name;

                if (move_uploaded_file($file_tmp_name, $target_file_path)) {
                    $uploaded_files_info[] = "Fichier '$file_name' téléversé avec succès.";
                    // Stocke le chemin relatif pour la BDD
                    $uploaded_files_paths_db[] = 'storage/uploads/devis/' . $unique_file_name; // Chemin depuis le dossier public
                } else {
                    $uploaded_files_info[] = "Erreur lors du téléversement du fichier '$file_name'.";
                }
            } elseif ($file_error !== UPLOAD_ERR_NO_FILE) {
                // Gérer les erreurs de téléchargement PHP
                $error_msg = 'Erreur inconnue';
                switch ($file_error) {
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE: $error_msg = 'Fichier trop grand.'; break;
                    case UPLOAD_ERR_PARTIAL: $error_msg = 'Téléchargement partiel.'; break;
                    case UPLOAD_ERR_NO_TMP_DIR: $error_msg = 'Dossier temporaire manquant.'; break;
                    case UPLOAD_ERR_CANT_WRITE: $error_msg = 'Échec de l\'écriture sur le disque.'; break;
                    case UPLOAD_ERR_EXTENSION: $error_msg = 'Extension PHP a arrêté le téléchargement.'; break;
                }
                $uploaded_files_info[] = "Erreur de téléversement pour '$file_name': $error_msg (Code $file_error).";
            }
        }
    }
    // Convertir les chemins des fichiers téléchargés en JSON pour la BDD
    $uploaded_files_json = json_encode($uploaded_files_paths_db);


    // --- 4. Calcul du prix approximatif ---
    $approx_price = null; // Par défaut, non applicable ou 0
    if ($project_type === 'Prénom Lumineux' || $project_type === 'Logo') {
        $text_length = strlen($project_text);
        $price_calculated = 0;
        if ($text_length > 0) {
            if ($text_length <= 3) {
                $price_calculated = 39.90;
            } else {
                $price_calculated = 39.90 + ($text_length - 3) * 5.00;
            }
            if ($option_sup === 'Wifi') {
                $price_calculated += 7.00;
            }
        }
        $approx_price = $price_calculated;
    }


    // --- 5. Enregistrement dans la base de données ---
    try {
        $stmt = $pdo->prepare("INSERT INTO devis_requests (nom, prenom, email, telephone, project_type, project_text, color_tour, lighting, option_sup, project_description, approx_price, uploaded_files_paths, created_at, updated_at) 
                               VALUES (:nom, :prenom, :email, :telephone, :project_type, :project_text, :color_tour, :lighting, :option_sup, :project_description, :approx_price, :uploaded_files_paths, NOW(), NOW())");

        $stmt->execute([
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'telephone' => $telephone,
            'project_type' => $project_type,
            'project_text' => ($project_type === 'Prénom Lumineux' || $project_type === 'Logo') ? $project_text : null, // Stocke seulement si pertinent
            'color_tour' => ($project_type === 'Prénom Lumineux' || $project_type === 'Logo') ? $color_tour : null,
            'lighting' => ($project_type === 'Prénom Lumineux' || $project_type === 'Logo') ? $lighting : null,
            'option_sup' => ($project_type === 'Prénom Lumineux' || $project_type === 'Logo') ? $option_sup : null,
            'project_description' => $project_description,
            'approx_price' => $approx_price,
            'uploaded_files_paths' => $uploaded_files_json,
        ]);
        
        $devis_id = $pdo->lastInsertId(); // Récupère l'ID du devis inséré

    } catch (PDOException $e) {
        // En cas d'erreur de base de données, rediriger vers une page d'erreur
        error_log("Erreur BDD soumission devis : " . $e->getMessage()); // Enregistrer l'erreur dans les logs du serveur
        header("Location: thanks.php?status=error&msg=db_error");
        exit();
    }


    // --- 6. Envoi des emails ---
    // Pour l'envoi d'emails, vous aurez besoin d'une configuration SMTP dans votre php.ini
    // ou d'une bibliothèque comme PHPMailer. Ici, on utilise la fonction mail() de PHP.

    $admin_email = ADMIN_EMAIL; // Récupéré de config.php

    // Email à l'administrateur (vous)
    $subject_admin = "Nouvelle demande de devis de " . $prenom . " " . $nom;
    $headers_admin = "MIME-Version: 1.0\r\n";
    $headers_admin .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers_admin .= "From: Devis CréaMod3D <" . ADMIN_EMAIL . ">\r\n";
    $headers_admin .= "Reply-To: " . $email . "\r\n";

    ob_start(); // Démarre la mise en tampon de sortie
    ?>
    <!DOCTYPE html>
    <html lang='fr'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Nouvelle demande de devis CréaMod3D</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { width: 90%; max-width: 600px; margin: 20px auto; border: 1px solid #ddd; padding: 20px; border-radius: 8px; background-color: #f9f9f9; }
            h2 { color: #A0B26E; text-align: center;}
            h3 { color: #343a40; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-top: 25px; }
            p { margin-bottom: 10px; }
            strong { color: #212529; }
            .info-box { background-color: #e9ecef; border-left: 5px solid #A0B26E; padding: 15px; margin-top: 20px; border-radius: 5px; }
            .price-info { color: #dc3545; font-weight: bold; font-size: 1.2em; margin-top: 15px; border-top: 1px dashed #ddd; padding-top: 15px; text-align: center; }
            .files-info { margin-top: 20px; padding: 10px; background-color: #e9ecef; border-radius: 5px; }
            .files-info ul { list-style-type: none; padding: 0; }
            .files-info li { margin-bottom: 5px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <h2>Nouvelle Demande de Devis Détaillé</h2>
            <p>Vous avez reçu une nouvelle demande de devis via votre site CréaMod3D.</p>

            <h3>Coordonnées du client :</h3>
            <div class="info-box">
                <p><strong>Nom :</strong> <?php echo htmlspecialchars($nom); ?></p>
                <p><strong>Prénom :</strong> <?php echo htmlspecialchars($prenom); ?></p>
                <p><strong>Email :</strong> <a href="mailto:<?php echo htmlspecialchars($email); ?>"><?php echo htmlspecialchars($email); ?></a></p>
                <p><strong>Téléphone :</strong> <?php echo htmlspecialchars($telephone); ?></p>
            </div>

            <h3>Détails du projet :</h3>
            <div class="info-box">
                <p><strong>Type de Projet :</strong> <?php echo htmlspecialchars($project_type); ?></p>
                <?php if (in_array($project_type, ['Prénom Lumineux', 'Logo'])): ?>
                    <p><strong>Texte / Nom du Projet :</strong> <?php echo htmlspecialchars($project_text); ?></p>
                    <p><strong>Couleur du Tour :</strong> <?php echo htmlspecialchars($color_tour); ?></p>
                    <p><strong>Éclairage :</strong> <?php echo htmlspecialchars($lighting); ?></p>
                    <p><strong>Option Supplémentaire :</strong> <?php echo htmlspecialchars($option_sup); ?></p>
                <?php endif; ?>
                <p><strong>Description du Projet :</strong><br><?php echo nl2br(htmlspecialchars($project_description)); ?></p>
            </div>

            <div class="price-info">
                Prix approximatif calculé : <?php echo number_format($approx_price, 2, ',', '.') . ' €'; ?>
            </div>

            <?php if (!empty($uploaded_files_paths_db)): ?>
                <h3>Fichiers joints :</h3>
                <div class="files-info">
                    <ul>
                        <?php foreach($uploaded_files_paths_db as $file_path): ?>
                            <li><a href="<?php echo htmlspecialchars(str_replace('storage/', '/storage/', $file_path)); ?>" target="_blank" class="file-link"><?php echo htmlspecialchars(basename($file_path)); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php else: ?>
                <p class="files-info">Aucun fichier n'a été joint à cette demande.</p>
            <?php endif; ?>

            <p style="font-size: 0.8em; color: #666; margin-top: 30px; text-align: center;">Ceci est un email automatique, veuillez ne pas y répondre directement.</p>
        </div>
    </body>
    </html>
    <?php
    $email_body_admin = ob_get_clean(); // Récupère le contenu et nettoie le tampon


    // Email de confirmation au client
    $subject_client = "Confirmation de votre demande de devis CréaMod3D";
    $headers_client = "MIME-Version: 1.0\r\n";
    $headers_client .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers_client .= "From: CréaMod3D <" . ADMIN_EMAIL . ">\r\n";
    $headers_client .= "Reply-To: " . ADMIN_EMAIL . "\r\n";

    ob_start(); // Démarre la mise en tampon de sortie pour le client
    ?>
    <!DOCTYPE html>
    <html lang='fr'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Confirmation de votre demande de devis CréaMod3D</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { width: 90%; max-width: 600px; margin: 20px auto; border: 1px solid #ddd; padding: 20px; border-radius: 8px; background-color: #f9f9f9; }
            h2 { color: #A0B26E; text-align: center; }
            p { margin-bottom: 10px; }
            strong { color: #212529; }
            .info-box { background-color: #e9ecef; border-left: 5px solid #A0B26E; padding: 15px; margin-top: 20px; border-radius: 5px; }
            .signature { margin-top: 30px; font-style: italic; color: #555; text-align: right;}
        </style>
    </head>
    <body>
        <div class='container'>
            <h2>Confirmation de votre demande de devis</h2>
            <p>Bonjour <?php echo htmlspecialchars($prenom); ?>,</p>
            <p>Nous avons bien reçu votre demande de devis pour votre projet chez CréaMod3D. Nous vous remercions de votre intérêt !</p>
            <p>Notre équipe va examiner attentivement votre demande et vous recontactera dans les plus brefs délais avec une proposition.</p>

            <h3>Récapitulatif de votre demande :</h3>
            <div class="info-box">
                <p><strong>Type de Projet :</strong> <?php echo htmlspecialchars($project_type); ?></p>
                <?php if (in_array($project_type, ['Prénom Lumineux', 'Logo'])): ?>
                    <p><strong>Texte / Nom du Projet :</strong> <?php echo htmlspecialchars($project_text); ?></p>
                    <p><strong>Couleur du Tour :</strong> <?php echo htmlspecialchars($color_tour); ?></p>
                    <p><strong>Éclairage :</strong> <?php echo htmlspecialchars($lighting); ?></p>
                    <p><strong>Option Supplémentaire :</strong> <?php echo htmlspecialchars($option_sup); ?></p>
                <?php endif; ?>
                <p><strong>Description du Projet :</strong><br><?php echo nl2br(htmlspecialchars($project_description)); ?></p>
            </div>

            <p class='signature'>Cordialement,</p>
            <p class='signature'>L'équipe CréaMod3D</p>
        </div>
    </body>
    </html>
    <?php
    $email_body_client = ob_get_clean(); // Récupère le contenu et nettoie le tampon


    // --- 7. Envoi final des emails ---
    $mail_sent_admin = mail($admin_email, $subject_admin, $email_body_admin, $headers_admin);
    $mail_sent_client = mail($email, $subject_client, $email_body_client, $headers_client);

    if ($mail_sent_admin && $mail_sent_client) {
        header("Location: thanks.php?status=success");
        exit();
    } else {
        error_log("Erreur envoi mail devis (admin: " . ($mail_sent_admin ? 'OK' : 'FAIL') . ", client: " . ($mail_sent_client ? 'OK' : 'FAIL') . ")");
        header("Location: thanks.php?status=error&msg=mail_send_failed");
        exit();
    }

} else {
    // Si quelqu'un essaie d'accéder directement à submit_devis.php
    header("Location: devis.php");
    exit();
}

// IMPORTANT : Assurez-vous qu'il n'y a AUCUN ESPACE, RETOUR À LA LIGNE OU CARACTÈRE APRÈS CETTE LIGNE PHP.
// NE PAS METTRE DE BALISE FERMANTE PHP ?> À LA FIN DE CE FICHIER.