<?php
// config/config.php

// Informations de connexion à la base de données
define('DB_HOST', '127.0.0.1'); // Souvent 'localhost' ou '127.0.0.1' chez Hostinger
define('DB_NAME', 'u923439069_creamod3d'); // VOTRE NOM DE BDD
define('DB_USER', 'u923439069_clems206'); // VOTRE UTILISATEUR BDD
define('DB_PASS', 'Z2&ceAm5'); // VOTRE MOT DE PASSE BDD

// Connexion à la base de données avec PDO
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // En production, ne pas afficher l'erreur détaillée à l'utilisateur
    // Mais enregistrez-la dans un fichier log ou redirigez vers une page d'erreur générique.
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Démarrer la session PHP (important pour l'authentification)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Autres constantes ou configurations générales
define('SITE_NAME', 'CréaMod3D Administration');
define('ADMIN_EMAIL', 'contact@creamod3d.fr'); // Votre email pour les notifications admin

// IMPORTANT : Assurez-vous qu'il n'y a AUCUN ESPACE, RETOUR À LA LIGNE OU CARACTÈRE APRÈS CETTE LIGNE PHP.
// NE PAS METTRE DE BALISE FERMANTE PHP 
?>