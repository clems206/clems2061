<?php
// Lignes pour l'affichage des erreurs (gardez-les pour l'instant)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Démarrage de session sécurisé
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Le reste de votre code PHP pour login.php...
// Par exemple, la vérification si l'utilisateur est déjà connecté :
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit;
}
// ... etc.
?>
<!DOCTYPE html>
...
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Connexion</title>
    <link rel="stylesheet" href="../assets/css/style.css"> <style>
        /* Styles additionnels ou spécifiques pour la page de connexion admin */
        body {
            background-color: #f0f2f5; /* Un fond neutre pour la zone admin */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: 'Open Sans', sans-serif; /* */
            color: #343a40; /* */
        }
        .login-container {
            background-color: #ffffff;
            padding: 30px 40px;
            border-radius: 8px; /* */
            box-shadow: 0 5px 15px rgba(0,0,0,0.1); /* */
            width: 100%;
            max-width: 420px;
            text-align: center;
        }
        .login-container h2 {
            font-family: 'Montserrat', sans-serif; /* */
            color: #343a40; /* */
            margin-bottom: 25px; /* */
            font-size: 1.8em;
        }
        /* Utilisation des classes de style.css pour la cohérence */
        /* .form-group, .form-group label, .form-group input sont déjà stylés dans style.css */
        /* .btn, .primary-btn sont déjà stylés dans style.css */

        .form-group {
            margin-bottom: 20px; /* */
            text-align: left; /* Pour que les labels soient alignés à gauche */
        }

        .form-group label { /* S'assure que le style du label est bien appliqué comme dans style.css */
            display: block; /* */
            font-weight: 600; /* */
            margin-bottom: 8px; /* */
        }

        /* S'assurer que les inputs prennent toute la largeur disponible dans le conteneur de connexion */
        .form-group input[type="text"],
        .form-group input[type="