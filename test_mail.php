<?php
$to = "contact@creamod3d.com"; // REMPLACEZ PAR VOTRE VRAIE ADRESSE EMAIL
$subject = "Test mail depuis CreMod3D";
$message = "Ceci est un mail de test envoyé depuis le site CreMod3D.";
$headers = "From: clems206@gmail.com"; // Utilisez une adresse liée à votre domaine si possible

if (mail($to, $subject, $message, $headers)) {
    echo "Email de test envoyé avec succès à " . $to;
} else {
    echo "Échec de l'envoi de l'email de test.";
    // Si possible, vérifiez les logs d'erreurs du serveur pour plus de détails
}
?>