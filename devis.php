<?php include 'includes/header.php'; ?>

<div class="form-container devis-form-container">
  <h2>Demandez votre devis personnalisé</h2>
  <p class="form-description">Remplissez les informations ci-dessous.</p>

  <form action="submit_devis.php" method="POST">
    <div class="form-row">
      <div class="form-group-half">
        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom" required>
      </div>
      <div class="form-group-half">
        <label for="prenom">Prénom :</label>
        <input type="text" id="prenom" name="prenom" required>
      </div>
    </div>
    
    <div class="form-row">
      <div class="form-group-half">
        <label for="email">Adresse Mail :</label>
        <input type="email" id="email" name="email" required>
      </div>
      <div class="form-group-half">
        <label for="telephone">Numéro de Téléphone <span class="optional-text">(Optionnel)</span> :</label>
        <input type="tel" id="telephone" name="telephone">
      </div>
    </div>
    
    <div class="form-group">
      <label for="project_type">Type de projet souhaité :</label>
      <select id="project_type" name="project_type" required>
        <option value="" selected disabled>Choisissez une option...</option>
        <option value="Prénom lumineux">Prénom lumineux</option>
        <option value="Logo lumineux">Logo lumineux</option>
        <option value="Figurine">Figurine</option>
        <option value="Autre">Autre (à préciser dans la description)</option>
      </select>
    </div>

    <div class="form-group">
      <label for="project_text">Sujet / Titre court <span class="optional-text">(Optionnel)</span> :</label>
      <input type="text" id="project_text" name="project_text" maxlength="250">
    </div>

    <div class="form-group">
      <label for="project_description">Description détaillée de votre projet :</label>
      <textarea id="project_description" name="project_description" rows="6" required></textarea>
    </div>

    <div class="form-group">
      <button type="submit" name="submit_devis" class="submit-button">Envoyer la demande</button>
    </div>
  </form>
</div>

<?php include 'includes/footer.php'; ?>