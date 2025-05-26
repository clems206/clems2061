<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// La suite de votre code login.php commence ici (session_start(), etc.)

session_start(); // Indispensable pour manipuler les sessions

// --- 1. Vérifier la méthode de la requête ---
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    // Si la requête n'est pas POST, rediriger vers login.php avec une erreur
    header("Location: login.php?status=invalid_request");
    exit;
}

// --- 2. Définir les identifiants administrateur attendus ---
// Remplacez par le nom d'utilisateur que vous souhaitez
$expected_username = "admin";
// Collez ici le HASH de mot de passe que vous avez généré avec generate_hash.php
// EXEMPLE de HASH (NE PAS UTILISER CELUI-CI TEL QUEL, GÉNÉREZ LE VÔTRE !) :
$expected_password_hash = '$2y$10$nKyhREFx6TyzHh68.7cAHOM6Wh.JUv1ckRw5fi3kRaU8zu3wD95Fe'; // REMPLACEZ CECI PAR VOTRE PROPRE HASH

// --- 3. Récupérer les données soumises ---
// Utilisation de l'opérateur null coalescent (??) pour éviter les erreurs si les clés n'existent pas
$submitted_username = $_POST['admin_username'] ?? '';
$submitted_password = $_POST['admin_password'] ?? '';

// --- 4. Validation simple (vérifier que les champs ne sont pas vides) ---
if (empty($submitted_username) || empty($submitted_password)) {
    // Rediriger vers login.php avec une erreur si des champs sont manquants
    header("Location: login.php?status=error_auth"); // Ou un message d'erreur plus spécifique
    exit;
}

// --- 5. Vérifier les identifiants ---
if ($submitted_username === $expected_username && password_verify($submitted_password, $expected_password_hash)) {
    // Les identifiants sont corrects

    // Régénérer l'ID de session pour des raisons de sécurité (prévention de fixation de session)
    session_regenerate_id(true);

    // Enregistrer l'état de connexion dans la session
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_username'] = $submitted_username; // Optionnel : stocker le nom d'utilisateur

    // Rediriger vers la page principale de l'administration (par exemple, admin/index.php)
    header("Location: index.php");
    exit;
} else {
    // Les identifiants sont incorrects
    // Rediriger vers login.php avec un message d'erreur
    header("Location: login.php?status=error_auth");
    exit;
}
?>