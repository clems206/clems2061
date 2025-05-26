<?php
// includes/main-nav.php

// Variable pour gérer l'état actif du lien de navigation
// Sera définie dans chaque page appelante (ex: $current_page = 'devis';)
if (!isset($current_page)) {
    $current_page = ''; // Aucune page active par défaut
}
?>
<nav class="main-nav">
    <ul>
        <li><a href="index.php" class="<?php echo ($current_page == 'accueil') ? 'active' : ''; ?>">Accueil</a></li>
        <li><a href="boutique.php" class="<?php echo ($current_page == 'boutique') ? 'active' : ''; ?>">Boutique</a></li>
        <li><a href="devis.php" class="<?php echo ($current_page == 'devis') ? 'active' : ''; ?>">Devis</a></li>
        <li><a href="contact.php" class="<?php echo ($current_page == 'contact') ? 'active' : ''; ?>">Contact</a></li>
    </ul>
</nav>