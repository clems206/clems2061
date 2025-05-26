<?php
// thanks.php

// Définir le titre de la page pour le header
$page_title = "Confirmation de votre demande - CréaMod3D";
// Définir la page courante pour la navigation (aucune active pour thanks.php, ou une par défaut)
$current_page = ''; // Ou 'accueil' si vous voulez que le lien Accueil soit actif sur cette page

// Les balises méta SEO peuvent être définies ici si vous voulez des descriptions spécifiques
// $meta_description = "Confirmation de l'envoi de votre formulaire chez CréaMod3D.";
// $meta_keywords = "confirmation, formulaire envoyé, CréaMod3D";

// Inclure le header (qui inclura aussi le menu de navigation)
require_once 'includes/header.php';
?>

        <section class="devis-section" style="padding-top: 150px; text-align: center;">
            <div class="container">
                <?php
                // Logique pour déterminer le message à afficher en fonction du statut dans l'URL
                if (isset($_GET['status'])) {
                    $status = $_GET['status']; // Récupère le statut de l'URL

                    if ($status == 'success') {
                        // Statut pour le formulaire de devis (si tout va bien)
                        echo '<h2>Votre demande de devis a bien été envoyée !</h2>';
                        echo '<p>Nous vous remercions pour votre demande de devis. Une confirmation a été envoyée à votre adresse email.</p>';
                        echo '<p>Nous reviendrons vers vous dans les plus brefs délais.</p>';
                    } elseif ($status == 'error') {
                        // Statut pour le formulaire de devis (si une erreur générique survient)
                        echo '<h2>Une erreur est survenue lors de l\'envoi de votre demande de devis.</h2>';
                        echo '<p>Nous sommes désolés, un problème est survenu. Veuillez réessayer ultérieurement ou nous contacter directement.</p>';
                        // Vous pouvez ajouter un message plus spécifique si vous avez utilisé &msg=...
                        // Par exemple: if (isset($_GET['msg'])) echo '<p>Détail: ' . htmlspecialchars($_GET['msg']) . '</p>';
                    } elseif ($status == 'contact_success') {
                        // Statut pour le formulaire de contact (si tout va bien)
                        echo '<h2>Votre message a bien été envoyé !</h2>';
                        echo '<p>Nous vous remercions de nous avoir contactés. Une confirmation a été envoyée à votre adresse email.</p>';
                        echo '<p>Nous reviendrons vers vous dans les plus brefs délais.</p>';
                    } elseif ($status == 'contact_error') {
                        // Statut pour le formulaire de contact (si une erreur survient)
                        echo '<h2>Une erreur est survenue lors de l\'envoi de votre message.</h2>';
                        echo '<p>Nous sommes désolés, un problème est survenu. Veuillez réessayer ultérieurement ou nous contacter directement.</p>';
                        // Vous pouvez aussi ajouter des messages spécifiques si vous passez des &msg= dans l'URL
                        // Par exemple: if (isset($_GET['msg']) && $_GET['msg'] == 'missing_fields') echo '<p>Tous les champs obligatoires n\'ont pas été remplis.</p>';
                    } else {
                        // Si un statut inconnu est passé, ou si status n'est pas "success"
                        echo '<h2>Statut de confirmation inconnu.</h2>';
                        echo '<p>Votre action a été traitée. Veuillez vérifier votre email ou nous contacter si vous avez des questions.</p>';
                    }
                } else {
                    // Message par défaut si aucun statut n'est défini dans l'URL (accès direct à thanks.php)
                    echo '<h2>Bienvenue sur notre page de confirmation.</h2>';
                    echo '<p>Utilisez les formulaires pour soumettre vos demandes.</p>';
                }
                ?>
                <br>
                <a href="index.php" class="btn primary-btn">Retour à l'accueil</a>
            </div>
        </section>

<?php
// Inclure le footer
require_once 'includes/footer.php';
?>