<?php
// Configuration pour afficher les erreurs pendant le développement
// À commenter ou retirer en production !
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// NOTE: Si PHPMailer était la SEULE raison d'utiliser Composer et 'vendor/autoload.php',
// la ligne 'require 'vendor/autoload.php';' pourrait être supprimée.
// Si vous avez d'autres bibliothèques gérées par Composer, laissez-la.
// Pour cet exemple, je la retire en supposant que PHPMailer était la seule.
// require 'vendor/autoload.php'; 

// Inclusion du fichier de configuration de la base de données
require_once 'config/config.php'; // Assurez-vous que ce chemin est correct et que $pdo est défini

// Vérification si le formulaire a été soumis via la méthode POST et si le bouton 'submit_devis' a été cliqué
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_devis'])) {

    // Récupération et nettoyage des données du formulaire
    $nom = isset($_POST['nom']) ? htmlspecialchars(trim($_POST['nom'])) : '';
    $prenom = isset($_POST['prenom']) ? htmlspecialchars(trim($_POST['prenom'])) : '';
    $email_client = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
    $telephone = isset($_POST['telephone']) && !empty($_POST['telephone']) ? htmlspecialchars(trim($_POST['telephone'])) : null; 
    $project_type = isset($_POST['project_type']) ? htmlspecialchars(trim($_POST['project_type'])) : '';
    $project_text = isset($_POST['project_text']) && !empty($_POST['project_text']) ? htmlspecialchars(trim($_POST['project_text'])) : null;
    $project_description = isset($_POST['project_description']) ? htmlspecialchars(trim($_POST['project_description'])) : '';

    // Validation simple des champs obligatoires
    if (empty($nom) || empty($prenom) || !filter_var($email_client, FILTER_VALIDATE_EMAIL) || empty($project_type) || empty($project_description)) {
        header('Location: devis.php?status=error&message=' . rawurlencode('Veuillez remplir tous les champs obligatoires correctement.'));
        exit;
    }

    // 1. Insertion des données dans la base de données
    try {
        $sql = "INSERT INTO devis_requests (nom, prenom, email, telephone, project_type, project_text, project_description, created_at, updated_at) 
                VALUES (:nom, :prenom, :email, :telephone, :project_type, :project_text, :project_description, NOW(), NOW())";
        
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':email', $email_client);
        $stmt->bindParam(':telephone', $telephone, $telephone === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(':project_type', $project_type);
        $stmt->bindParam(':project_text', $project_text, $project_text === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(':project_description', $project_description);

        $stmt->execute();
        $lastInsertId = $pdo->lastInsertId();

    } catch (PDOException $e) {
        error_log("Erreur d'insertion DB pour submit_devis.php : " . $e->getMessage()); 
        header('Location: devis.php?status=error&message=' . rawurlencode('Une erreur serveur est survenue lors de l\'enregistrement de votre demande. Veuillez réessayer.'));
        exit;
    }

    // 2. Envoi de l'email de notification à l'administrateur avec la fonction mail()
    $to_admin = 'contact@creamod3d.fr';
    $subject_admin = 'Nouvelle demande de devis (#'. $lastInsertId .') : ' . $project_type . (!empty($project_text) ? ' - ' . htmlspecialchars($project_text) : '');
    
    // Construction du corps de l'email en HTML (identique à avant)
    $email_body_html = "<h1>Nouvelle demande de devis (Réf: #{$lastInsertId})</h1>";
    $email_body_html .= "<p>Une nouvelle demande de devis a été soumise via le site web creamod3d.fr.</p>";
    $email_body_html .= "<ul>";
    $email_body_html .= "<li><strong>Nom :</strong> " . $nom . "</li>";
    $email_body_html .= "<li><strong>Prénom :</strong> " . $prenom . "</li>";
    $email_body_html .= "<li><strong>Email :</strong> <a href='mailto:" . $email_client . "'>" . $email_client . "</a></li>";
    if ($telephone) {
        $email_body_html .= "<li><strong>Téléphone :</strong> " . $telephone . "</li>";
    }
    $email_body_html .= "<li><strong>Type de projet :</strong> " . $project_type . "</li>";
    if ($project_text) {
        $email_body_html .= "<li><strong>Sujet/Titre :</strong> " . $project_text . "</li>";
    }
    $email_body_html .= "</ul>";
    $email_body_html .= "<h2>Description détaillée du projet :</h2>";
    $email_body_html .= "<p style='white-space: pre-wrap; font-family: sans-serif; border: 1px solid #eee; padding: 10px; background-color: #f9f9f9;'>" . $project_description . "</p>"; 
    $email_body_html .= "<hr>";
    $email_body_html .= "<p><em>Demande soumise le : " . date('d/m/Y à H:i:s') . " (heure du serveur)</em></p>";
    $email_body_html .= "<p><em>ID de la demande dans la base de données : " . $lastInsertId . "</em></p>";

    // En-têtes pour l'email HTML
    // Il est crucial de bien les formater pour une bonne délivrabilité et un affichage correct.
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    // Utilisez une adresse d'expéditeur valide liée à votre domaine pour 'From'
    // Mettre l'email du client directement dans 'From' avec mail() peut causer des problèmes de délivrabilité (spam).
    $headers .= 'From: <noreply@creamod3d.fr>' . "\r\n"; // Adaptez cette adresse si nécessaire
    $headers .= 'Reply-To: <' . $email_client . '>' . "\r\n";
    // Vous pourriez ajouter d'autres en-têtes si besoin, comme Cc ou Bcc:
    // $headers .= 'Cc: autre_adresse@example.com' . "\r\n";
    // $headers .= 'Bcc: adresse_cachee@example.com' . "\r\n";

    // Envoi de l'email
    // La fonction mail() retourne true si l'email a été accepté pour envoi par le serveur, false sinon.
    // Cela ne garantit pas que l'email sera effectivement délivré ou ne sera pas dans les spams.
    if (mail($to_admin, $subject_admin, $email_body_html, $headers)) {
        // L'email a été accepté pour envoi
        header('Location: thanks.php?from=devis&id=' . $lastInsertId . '&email_status=sent_with_mail');
        exit;
    } else {
        // L'email n'a pas pu être accepté pour envoi par le serveur mail local
        error_log("Erreur lors de l'utilisation de la fonction mail() pour submit_devis.php (ID Demande: {$lastInsertId})");
        header('Location: thanks.php?from=devis&id=' . $lastInsertId . '&email_status=failed_mail_function');
        exit;
    }

} else {
    header('Location: devis.php');
    exit;
}
?>