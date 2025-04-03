<?php
include_once '../../config.php';
include_once '../../queries.php';
include_once '../../template_header.php';

// Recupera l'ID della manifestazione dalla query string
$idManifestazione = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Recupera i dettagli della manifestazione
$manifestazione = getManifestazioneById($pdo, $idManifestazione);

if (!$manifestazione) {
    echo "<p style='color: red;'>Manifestazione non trovata.</p>";
    exit;
}

// Inizializza il messaggio di errore o successo
$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recupera i dati dal form (i campi non sono obbligatori)
    $nome = !empty($_POST['Nome']) ? $_POST['Nome'] : $manifestazione['Nome'];
    $descrizione = !empty($_POST['Descrizione']) ? $_POST['Descrizione'] : $manifestazione['Descrizione'];
    $luogo = !empty($_POST['Luogo']) ? $_POST['Luogo'] : $manifestazione['Luogo'];
    $durata = !empty($_POST['Durata']) ? $_POST['Durata'] : $manifestazione['Durata'];
    $data = !empty($_POST['Data']) ? $_POST['Data'] : $manifestazione['Data'];

    

    // Aggiorna la manifestazione nel database
    if (updateManifestazione($pdo, $idManifestazione, $nome, $descrizione, $luogo, $durata, $data)) {
        $successMessage = "Manifestazione aggiornata con successo.";
        // Ricarica i dettagli aggiornati della manifestazione
        $manifestazione = getManifestazioneById($pdo, $idManifestazione);
    } else {
        $errorMessage = "Errore durante l'aggiornamento della manifestazione.";
    }
}

?>

<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(/progetto-espositori/resources/images/bg-breadcrumbs-07-1920x480.jpg);">
  <div class="container">
      <h2 class="breadcrumbs-custom-title">Modifica Dettagli Manifestazione</h2>
  </div>
  <ul class="breadcrumbs-custom-path">
    <li><a href="../dashboard_personale.php">Dashboard</a></li>
    <li><a href="gestisci_manifestazione.php">Gestione Manifestazione</a></li>
    <li><a href="modifica_manifestazione.php">Modifica Manifestazione</a></li>
    <li class="active">Modifica Dettagli Manifestazione</li>
  </ul>
</section>

<!-- Main Content-->
<section class="section section-lg bg-default text-center">
  <div class="container">
    <h2>Modifica Manifestazione</h2>
    <p>Compila il modulo sottostante per modificare la manifestazione.</p>

    <!-- Mostra i messaggi di errore o successo -->
    <?php if (!empty($successMessage)): ?>
        <p style="color: green;"><?php echo htmlspecialchars($successMessage); ?></p>
    <?php endif; ?>
    <?php if (!empty($errorMessage)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($errorMessage); ?></p>
    <?php endif; ?>

    <form class="rd-form rd-mailform" method="post" action="">
      <div class="row row-50">
        <div class="col-md-6">
          <div class="form-wrap">
            <label class="form-label" for="manifestazione-nome">Nome</label>
            <input class="form-input" id="manifestazione-nome" type="text" name="Nome" value="<?php echo htmlspecialchars($manifestazione['Nome']); ?>">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-wrap">
            <label class="form-label" for="manifestazione-luogo">Luogo</label>
            <input class="form-input" id="manifestazione-luogo" type="text" name="Luogo" value="<?php echo htmlspecialchars($manifestazione['Luogo']); ?>">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-wrap">
            <label class="form-label" for="manifestazione-durata">Durata</label>
            <input class="form-input" id="manifestazione-durata" type="text" name="Durata" value="<?php echo htmlspecialchars($manifestazione['Durata']); ?>">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-wrap">
            <label class="form-label" for="manifestazione-data">Data</label>
            <input class="form-input" id="manifestazione-data" type="date" name="Data" value="<?php echo htmlspecialchars($manifestazione['Data']); ?>">
          </div>
        </div>
        <div class="col-12">
          <div class="form-wrap">
            <label class="form-label" for="manifestazione-descrizione">Descrizione</label>
            <textarea class="form-input" id="manifestazione-descrizione" name="Descrizione"><?php echo htmlspecialchars($manifestazione['Descrizione']); ?></textarea>
          </div>
        </div>
      </div>
      <button class="button button-primary button-lg" type="submit">Modifica</button>
    </form>
  </div>
</section>

<?php
include_once '../../template_footer.php';
?>
