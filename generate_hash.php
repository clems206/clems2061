<?php
// generate_hash.php
$motDePasseAdmin = "27112015.Chouchoune"; // Remplacez par le mot de passe que vous voulez
$hashMotDePasse = password_hash($motDePasseAdmin, PASSWORD_DEFAULT);
echo "Le hash de votre mot de passe est : " . $hashMotDePasse;
?>