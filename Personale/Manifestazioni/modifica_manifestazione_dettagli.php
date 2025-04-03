<?php
include_once '../../config.php';
include_once '../../queries.php';
include_once '../../template_header.php';

// Recupera l'ID della manifestazione dalla query string
$idManifestazione = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Recupera i dettagli della manifestazione
$manifestazione = getManifestazioneById($pdo, $idManifestazione);

if (!$manifestazione) {
    echo "<p style='color: red;'>Errore: Manifestazione non trovata.</p>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recupera i dati dal form (se lasciati vuoti, mantieni i valori esistenti)
    $nome = isset($_POST['Nome']) ? trim($_POST['Nome']) : $manifestazione['Nome'];
    $descrizione = isset($_POST['Descrizione']) ? trim($_POST['Descrizione']) : $manifestazione['Descrizione'];
    $luogo = isset($_POST['Luogo']) ? trim($_POST['Luogo']) : $manifestazione['Luogo'];
    $data = isset($_POST['Data']) ? $_POST['Data'] : $manifestazione['Data'];
    $durata = isset($_POST['Durata']) ? intval($_POST['Durata']) : $manifestazione['Durata'];

    // Aggiorna solo se almeno un campo è cambiato
    if ($nome !== $manifestazione['Nome'] || $descrizione !== $manifestazione['Descrizione'] ||
        $luogo !== $manifestazione['Luogo'] || $data !== $manifestazione['Data'] ||
        $durata !== $manifestazione['Durata']) {

        if (updateManifestazione($pdo, $idManifestazione, $nome, $descrizione, $luogo, $data, $durata)) {
            // Redirect per evitare il reinvio del form con F5
            header("Location: modifica_manifestazione_dettagli.php?id=$idManifestazione&success=1");
            exit; // Interrompi l'esecuzione
        } else {
            echo "<p style='color: red;'>Errore durante l'aggiornamento della manifestazione.</p>";
        }
    } else {
        echo "<p style='color: orange;'>Nessuna modifica effettuata.</p>";
    }
}

// Se il parametro 'success' è presente nell'URL, mostra un messaggio di successo
if (isset($_GET['success']) && $_GET['success'] == 1) {
    echo "<p style='color: green;'>Modifica avvenuta con successo!</p>";
}
?>

<!-- Breadcrumbs -->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(/progetto-espositori/resources/images/bg-breadcrumbs-07-1920x480.jpg);">
  <div class="container">
      <h2 class="breadcrumbs-custom-title">Modifica Dettagli Manifestazione</h2>
  </div>
  <ul class="breadcrumbs-custom-path">
    <li><a href="../dashboard_personale.php">Dashboard</a></li>
    <li><a href="gestisci_manifestazioni.php">Gestione Manifestazioni</a></li>
    <li class="active">Modifica Dettagli Manifestazione</li>
  </ul>
</section>

<!-- Main Content -->
<section class="section section-lg bg-default text-center">
  <div class="container">
    <h2>Modifica Manifestazione</h2>
    <p>Compila il modulo per modificare la manifestazione. I campi non sono obbligatori.</p>
    <form class="rd-form rd-mailform" method="post">
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
            <label class="form-label" for="manifestazione-data">Data</label>
            <input class="form-input" id="manifestazione-data" type="date" name="Data" value="<?php echo htmlspecialchars($manifestazione['Data']); ?>">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-wrap">
            <label class="form-label" for="manifestazione-durata">Durata (giorni)</label>
            <input class="form-input" id="manifestazione-durata" type="number" name="Durata" value="<?php echo htmlspecialchars($manifestazione['Durata']); ?>">
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
