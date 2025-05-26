<?php
// contact.php

// Définir le titre de la page
$page_title = "Contact - CréaMod3D : Contactez-nous pour vos projets 3D";
// Définir la méta description spécifique à la page de contact
$meta_description = "Contactez CréaMod3D par email, téléphone ou visitez nos locaux à Blaye. Posez vos questions sur la modélisation, l'impression et la conception 3D.";
// Définir les mots-clés spécifiques
$meta_keywords = "contact 3D, CréaMod3D contact, téléphone 3D, adresse 3D, devis 3D";
// Définir la page courante pour la navigation active
$current_page = 'contact';

// Informations de contact de l'entreprise
$company_name = "CréaMod3D";
$company_email = "contact@creamod3d.fr";
$company_phone = "0677509503";
$company_address = "64 rue saint romain";
$company_zip_city = "33390 Blaye";
$company_country = "France";

// URL pour Google Maps (vous pouvez générer cette URL depuis Google Maps)
// Exemple: recherchez votre adresse sur Google Maps, cliquez sur 'Partager', puis 'Intégrer une carte'
// Copiez l'URL qui se trouve dans le 'src' de l'iframe.
// J'ai mis une URL d'exemple, REMPLACEZ-LA PAR VOTRE URL D'INTÉGRATION RÉELLE DE GOOGLE MAPS !
$Maps_embed_url = "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2797.5504780516637!2d-0.669641723223019!3d45.13280035515284!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4802c6c0b3f5e56d%3A0xc3c5b5f5e5f5e5f5!2s64%20Rue%20Saint%20Romain%2C%2033390%20Blaye%2C%20France!5e0!3m2!1sfr!2sfr!4v1700000000000!5m2!1sfr!2sfr";


// Inclure le header
require_once 'includes/header.php';
?>

        <section class="contact-section">
            <div class="container">
                <h2>Contactez-nous</h2>
                <p class="section-subtitle">Nous sommes à votre écoute pour toutes questions et projets.</p>

                <div class="contact-grid">
                    <div class="contact-info">
                        <h3>Nos coordonnées</h3>
                        <p><strong>Nom de l'entreprise :</strong> <?php echo $company_name; ?></p>
                        <p><strong>Email :</strong> <a href="mailto:<?php echo $company_email; ?>"><?php echo $company_email; ?></a></p>
                        <p><strong>Téléphone :</strong> <a href="tel:<?php echo str_replace(' ', '', $company_phone); ?>"><?php echo $company_phone; ?></a></p>
                        <p><strong>Adresse :</strong><br>
                            <?php echo $company_address; ?><br>
                            <?php echo $company_zip_city; ?><br>
                            <?php echo $company_country; ?>
                        </p>
                        
                        <div class="map-container-inline">
                            <h3>Où nous trouver</h3>
                            <iframe
                                src="<?php echo $Maps_embed_url; ?>"
                                width="100%"
                                height="300" 
                                style="border:0;"
                                allowfullscreen=""
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>

                    </div>

                    <div class="contact-form-container">
                        <h3>Envoyez-nous un message</h3>
                        <form action="submit_contact.php" method="POST" class="contact-form">
                            <div class="form-group-inline">
                                <div class="form-group">
                                    <label for="contact_name">Nom *</label>
                                    <input type="text" id="contact_name" name="contact_name" required placeholder="Votre nom">
                                </div>
                                <div class="form-group">
                                    <label for="contact_prenom">Prénom *</label>
                                    <input type="text" id="contact_prenom" name="contact_prenom" required placeholder="Votre prénom">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="contact_email">Email *</label>
                                <input type="email" id="contact_email" name="contact_email" required placeholder="votre.email@exemple.com">
                            </div>
                            <div class="form-group">
                                <label for="contact_subject">Sujet (facultatif)</label>
                                <input type="text" id="contact_subject" name="contact_subject" placeholder="Sujet de votre message">
                            </div>
                            <div class="form-group">
                                <label for="contact_message">Votre message *</label>
                                <textarea id="contact_message" name="contact_message" rows="8" required placeholder="Écrivez votre message ici..."></textarea>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn primary-btn">Envoyer le message</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                </div>
        </section>

<?php
// Inclure le footer
require_once 'includes/footer.php';
?>