<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// admin/index.php (Page de connexion de l'administration)

// Inclure le fichier de configuration (connexion BDD et session)
require_once '../config/config.php'; // Le chemin est relatif à admin/

// Vérifier si l'utilisateur est déjà connecté
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php'); // Rediriger vers le tableau de bord si déjà connecté
    exit;
}

$message = ''; // Message d'erreur ou de succès

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $message = "Veuillez remplir tous les champs.";
    } else {
        // Requête SQL pour trouver l'utilisateur par email
        $stmt = $pdo->prepare("SELECT id, name, email, password FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        // Vérifier si l'utilisateur existe et si le mot de passe est correct
        // UTILISEZ password_verify() car le mot de passe en BDD est hashé avec password_hash()
        if ($user && password_verify($password, $user['password'])) {
            // Mot de passe correct, démarrer la session
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_name'] = $user['name'];
            $_SESSION['admin_email'] = $user['email'];

            header('Location: dashboard.php'); // Rediriger vers le tableau de bord
            exit;
        } else {
            $message = "Email ou mot de passe incorrect.";
        }
    }
}

// Titre pour la page HTML
$page_title = "Administration - Connexion";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { background-color: #f0f2f5; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; font-family: 'Open Sans', sans-serif;}
        .login-container { background-color: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 400px; text-align: center; }
        .login-container h2 { color: #343a40; margin-bottom: 30px; font-size: 2em; font-family: 'Montserrat', sans-serif;}
        .login-container .form-group { margin-bottom: 20px; text-align: left; }
        .login-container label { display: block; font-weight: 600; margin-bottom: 8px; color: #343a40; }
        .login-container input[type="email"],
        .login-container input[type="password"] { width: 100%; padding: 12px 15px; border: 1px solid #dee2e6; border-radius: 5px; font-size: 1em; }
        .login-container input[type="email"]:focus,
        .login-container input[type="password"]:focus { border-color: #A0B26E; box-shadow: 0 0 0 3px rgba(160, 178, 110, 0.25); outline: none; }
        .login-container button { width: 100%; padding: 12px; margin-top: 20px; font-size: 1.1em; cursor: pointer; }
        .message { margin-top: 20px; padding: 10px; border-radius: 5px; }
        .message.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .message.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Connexion Administration CréaMod3D</h2>
        <?php if (!empty($message)): ?>
            <div class="message <?php echo strpos($message, 'incorrect') !== false || strpos($message, 'remplir') !== false ? 'error' : 'success'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        <form action="index.php" method="POST">
            <div class="form-group">
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn primary-btn">Se connecter</button>
        </form>
    </div>
</body>
</html>