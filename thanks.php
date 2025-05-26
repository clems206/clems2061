<?php 
// Suppose que vous avez un header commun inclus
include 'includes/header.php'; 

$titre_page = "Confirmation"; // Titre par défaut
$message_utilisateur = ""; // Message à afficher

// Vérifier si la redirection vient du formulaire de devis
if (isset($_GET['from']) && $_GET['from'] === 'devis') {
    $titre_page = "Demande de devis envoyée !";
    $message_utilisateur .= "<p class='lead'>Merci, votre demande de devis a bien été enregistrée.</p>";

    // Afficher la référence de la demande si l'ID est présent
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $devis_id = htmlspecialchars($_GET['id']);
        $message_utilisateur .= "<p>Votre numéro de référence est : <strong>DEVIS-{$devis_id}</strong>. Veuillez le conserver pour toute communication future.</p>";
    }

    $message_utilisateur .= "<p>Nous examinerons votre demande dans les plus brefs délais et vous contacterons si nous avons besoin de plus d'informations.</p>";

    // Message optionnel concernant la notification email à l'admin
    if (isset($_GET['email_status'])) {
        if ($_GET['email_status'] === 'failed_mail_function' || $_GET['email_status'] === 'failed_admin_notification') {
            $message_utilisateur .= "<p class='text-warning mt-3'>Note : Un souci technique a empêché l'envoi de la notification automatique à notre équipe, mais soyez assuré(e) que votre demande est bien enregistrée et sera traitée.</p>";
        }
        // Pas besoin de message spécifique si 'sent_with_mail', c'est un processus interne.
    }

} else {
    // Message générique si on arrive sur thanks.php sans les paramètres attendus
    // C'est peut-être le message que vous voyez actuellement
    $titre_page = "Confirmation";
    $message_utilisateur = "<p>Bienvenue sur notre page de confirmation.</p><p>Utilisez les formulaires pour soumettre vos demandes.</p>";
}
?>

<section class="py-3 py-md-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-7 col-xl-6">
                <div class="card border border-light-subtle rounded-3 shadow-sm">
                    <div class="card-body p-3 p-md-4 p-xl-5 text-center">
                        
                        <div class="mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-check-circle-fill text-success" viewBox="0 0 16 16">
                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                            </svg>
                        </div>

                        <h2 class="fs-4 fw-bold mb-3"><?php echo $titre_page; ?></h2>
                        
                        <?php echo $message_utilisateur; ?>

                        <div class="mt-4">
                            <a href="index.php" class="btn btn-primary me-2">Retour à l'accueil</a>
                            <a href="devis.php" class="btn btn-outline-secondary">Faire une autre demande</a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php 
// Suppose que vous avez un footer commun inclus
include 'includes/footer.php'; 
?>