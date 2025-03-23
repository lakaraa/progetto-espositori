<?php
//durante la modifica i campi non devono essere obbligatori.
//Reindirizza alla pagina 'modifica_area.php'.
?>
<?php
include_once '../../config.php';
include_once '../../queries.php';
include_once '../../template_header.php';

// Recupera l'ID dell'area dalla query string
$idArea = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Recupera i dettagli dell'area
$area = getAreaById($pdo, $idArea);

if (!$area) {
    echo "<p style='color: red;'>Area non trovata.</p>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recupera i dati dal form
    $nome = $_POST['nome'];
    $descrizione = $_POST['descrizione'];
    $capienza = $_POST['capienza'];
    $idManifestazione = $_POST['manifestazione'];

    // Aggiorna l'area nel database
    if (updateArea($pdo, $idArea, $nome, $descrizione, $capienza, $idManifestazione)) {
      //echo "<p style='color: green;'>Area aggiornata con successo.</p>";
    } else {
      echo "<p style='color: red;'>Errore durante l'aggiornamento dell'area.</p>";
    }
}
?>

<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(/progetto-espositori/resources/images/bg-breadcrumbs-07-1920x480.jpg);">
  <div class="container">
      <h2 class="breadcrumbs-custom-title">Modifica Dettagli Area</h2>
  </div>
  <ul class="breadcrumbs-custom-path">
    <li><a href="../dashboard_personale.php">Dashboard</a></li>
    <li><a href="gestisci_aree.php">Gestione Aree</a></li>
    <li><a href="modifica_area.php">Modifica Aree</a></li>
    <li class="active">Modifica Dettagli Area</li>
  </ul>
</section>

<!-- Main Content-->
<section class="section section-lg bg-default text-center">
  <div class="container">
    <h2>Modifica Aree</h2>
    <p>Compila il modulo sottostante per modificare le aree.</p>
    <form class="rd-form rd-mailform" method="post" action="">
      <div class="row row-50">
        <div class="col-md-6">
          <div class="form-wrap">
            <label class="form-label" for="area-manifestazione">Manifestazione</label>
            <select class="form-input" id="area-manifestazione" name="manifestazione" required>
              <?php foreach (getManifestazioni($pdo) as $manifestazione): ?>
                <option value="<?php echo $manifestazione['Id_Manifestazione']; ?>" 
                  <?php echo $manifestazione['Id_Manifestazione'] == $area['id_manifestazione'] ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($manifestazione['Nome']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-wrap">
            <label class="form-label" for="area-nome">Nome</label>
            <input class="form-input" id="area-nome" type="text" name="nome" value="<?php echo htmlspecialchars($area['nome']); ?>" required>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-wrap">
            <label class="form-label" for="area-capienza">Capienza Massima</label>
            <input class="form-input" id="area-capienza" type="number" name="capienza" value="<?php echo htmlspecialchars($area['capienza_massima']); ?>" required>
          </div>
        </div>
        <div class="col-12">
          <div class="form-wrap">
            <label class="form-label" for="area-descrizione">Descrizione</label>
            <textarea class="form-input" id="area-descrizione" name="descrizione" required><?php echo htmlspecialchars($area['descrizione']); ?></textarea>
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