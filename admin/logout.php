<?php
// admin/logout.php

// Inclure le fichier de configuration (pour s'assurer que la session est démarrée avant de la détruire)
require_once '../config/config.php';

// Détruire toutes les variables de session
$_SESSION = array();

// Si vous voulez détruire complètement la session, supprimez aussi le cookie de session.
// Note : Cela détruira la session, et non seulement les données de session !
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalement, détruire la session.
session_destroy();

// Rediriger vers la page de connexion
header('Location: index.php');
exit;

// IMPORTANT : Assurez-vous qu'il n'y a AUCUN ESPACE, RETOUR À LA LIGNE OU CARACTÈRE APRÈS CETTE LIGNE PHP.
// NE PAS METTRE DE BALISE FERMANTE PHP ?> 