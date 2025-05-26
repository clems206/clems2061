<?php
// submit_contact.php

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- 1. Récupération et NETTOYAGE des données du formulaire ---
    // Utilisation de htmlspecialchars et trim pour la sécurité et la propreté des données
    $name = htmlspecialchars(trim($_POST['contact_name'] ?? ''));
    $prenom = htmlspecialchars(trim($_POST['contact_prenom'] ?? '')); // Récupération du champ Prénom
    $email = htmlspecialchars(trim($_POST['contact_email'] ?? ''));
    $subject = htmlspecialchars(trim($_POST['contact_subject'] ?? ''));
    $message = htmlspecialchars(trim($_POST['contact_message'] ?? ''));

    // --- 2. Validation simple (vous devriez ajouter des validations plus robustes en production) ---
    // Vérifie si les champs obligatoires sont remplis
    if (empty($name) || empty($prenom) || empty($email) || empty($message)) {
        header("Location: thanks.php?status=contact_error&msg=missing_fields");
        exit();
    }

    // Vérifie le format de l'email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: thanks.php?status=contact_error&msg=invalid_email");
        exit();
    }

    // --- 3. Envoi de l'email à l'administrateur (vous) ---
    $to_admin = "contact@creamod3d.fr"; // REMPLACEZ PAR VOTRE ADRESSE EMAIL DE RÉCEPTION !
    $subject_admin = "Nouveau message de contact de " . $prenom . " " . $name . " (CréaMod3D)";

    $email_body_admin = "
    <!DOCTYPE html>
    <html lang='fr'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Nouveau message de contact CréaMod3D</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { width: 80%; margin: 20px auto; border: 1px solid #ddd; padding: 20px; border-radius: 8px; background-color: #f9f9f9; }
            h2 { color: #A0B26E; } /* Utilisation d'une couleur du logo pour le titre */
            p { margin-bottom: 10px; }
            strong { color: #212529; } /* Gris foncé pour les labels */
            .info-box { background-color: #e9ecef; border-left: 5px solid #A0B26E; padding: 15px; margin-top: 20px; border-radius: 5px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <h2>Nouveau Message de Contact</h2>
            <div class='info-box'>
                <p><strong>Nom :</strong> " . $name . "</p>
                <p><strong>Prénom :</strong> " . $prenom . "</p>
                <p><strong>Email :</strong> " . $email . "</p>
                <p><strong>Sujet :</strong> " . ($subject ? $subject : "Non spécifié") . "</p>
            </div>
            <p><strong>Message :</strong><br>" . nl2br($message) . "</p>
            <p style='font-size: 0.8em; color: #666; margin-top: 20px;'>Ceci est un email automatique, veuillez ne pas y répondre directement.</p>
        </div>
    </body>
    </html>";

    $headers_admin = "MIME-Version: 1.0" . "\r\n";
    $headers_admin .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers_admin .= "From: Contact CréaMod3D <contact@creamod3d.fr>" . "\r\n"; // Remplacez par une adresse valide du même domaine
    $headers_admin .= "Reply-To: " . $email . "\r\n"; // Permet de répondre directement au client


    // --- 4. Envoi de l'email de confirmation au client ---
    $to_client = $email;
    $subject_client = "Confirmation de la réception de votre message - CréaMod3D";

    $email_body_client = "
    <!DOCTYPE html>
    <html lang='fr'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Confirmation message CréaMod3D</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { width: 80%; margin: 20px auto; border: 1px solid #ddd; padding: 20px; border-radius: 8px; background-color: #f9f9f9; }
            h2 { color: #A0B26E; } /* Utilisation d'une couleur du logo pour le titre */
            p { margin-bottom: 10px; }
            strong { color: #212529; } /* Gris foncé pour les labels */
            .signature { margin-top: 30px; font-style: italic; color: #555; }
        </style>
    </head>
    <body>
        <div class='container'>
            <h2>Confirmation de la réception de votre message</h2>
            <p>Bonjour " . $prenom . ",</p>
            <p>Nous avons bien reçu votre message et nous vous remercions de nous avoir contactés.</p>
            <p>Notre équipe va examiner votre demande et nous vous répondrons dans les plus brefs délais.</p>
            <p>Voici un récapitulatif de votre message :</p>
            <p><strong>Sujet :</strong> " . ($subject ? $subject : "Non spécifié") . "</p>
            <p><strong>Votre message :</strong><br>" . nl2br($message) . "</p>
            <p class='signature'>Cordialement,</p>
            <p class='signature'>L'équipe CréaMod3D</p>
        </div>
    </body>
    </html>";

    $headers_client = "MIME-Version: 1.0" . "\r\n";
    $headers_client .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers_client .= "From: CréaMod3D <contact@creamod3d.fr>" . "\r\n"; // REMPLACEZ PAR VOTRE ADRESSE EMAIL DE VOTRE DOMAINE !
    $headers_client .= "Reply-To: contact@creamod3d.fr" . "\r\n"; // Permet au client de répondre à votre adresse générale

    // --- 5. Envoi des emails ---
    $mail_sent_admin = mail($to_admin, $subject_admin, $email_body_admin, $headers_admin);
    $mail_sent_client = mail($to_client, $subject_client, $email_body_client, $headers_client);

    if ($mail_sent_admin && $mail_sent_client) {
        // Redirection vers une page de succès
        header("Location: thanks.php?status=contact_success");
        exit();
    } else {
        // Redirection vers une page d'erreur
        // Vous pouvez ajouter une logique de log d'erreurs ici pour le débogage
        header("Location: thanks.php?status=contact_error");
        exit();
    }

} else {
    // Si quelqu'un essaie d'accéder directement à submit_contact.php sans passer par le formulaire
    header("Location: contact.php");
    exit();
}
?>