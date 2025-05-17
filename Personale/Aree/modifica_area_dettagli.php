<?php
ob_start(); // Inizia il buffer di output

include_once("../../config.php");
include_once("../../queries.php");
include_once("../../session.php");
include_once("../../template_header.php");

// Recupera l'ID dell'area dalla query string
$idArea = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Recupera i dettagli dell'area
$area = getAreaById($pdo, $idArea);

if (!$area) {
    echo "<p style='color: red;'>Area non trovata.</p>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recupera i dati dal form, usa i valori esistenti se i campi sono vuoti
    $nome = !empty($_POST['nome']) ? $_POST['nome'] : $area['nome'];
    $descrizione = !empty($_POST['descrizione']) ? $_POST['descrizione'] : $area['descrizione'];
    $capienza = !empty($_POST['capienza']) ? $_POST['capienza'] : $area['capienza_massima'];
    $idManifestazione = !empty($_POST['manifestazione']) ? $_POST['manifestazione'] : $area['id_manifestazione'];

    // Aggiorna l'area nel database
    if (updateArea($pdo, $idArea, $nome, $descrizione, $capienza, $idManifestazione)) {
        header('Location: modifica_area.php');
        exit;
    } else {
        echo "<p style='color: red;'>Errore durante l'aggiornamento dell'area.</p>";
    }
}
?>

<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(../../resources/images/sfondo.jpg);">
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
            <select class="form-input" id="area-manifestazione" name="manifestazione" disabled>
              <?php 
                $currentManifestazione = getManifestazioneById($pdo, $area['id_manifestazione']);
                if ($currentManifestazione): 
              ?>
                <option value="<?php echo $currentManifestazione['Id_Manifestazione']; ?>" selected style="color: black; background-color: white;">
                  <?php echo htmlspecialchars($currentManifestazione['Nome']); ?>
                </option>
              <?php endif; ?>
            </select>
            <input type="hidden" name="manifestazione" value="<?php echo $area['id_manifestazione']; ?>">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-wrap">
            <label class="form-label" for="area-nome">Nome</label>
            <input class="form-input" id="area-nome" type="text" name="nome" value="<?php echo htmlspecialchars($area['nome']); ?>">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-wrap">
            <label class="form-label" for="area-capienza">Capienza Massima</label>
            <input class="form-input" id="area-capienza" type="number" name="capienza" style="color: white;"  value="<?php echo htmlspecialchars($area['capienza_massima']); ?>">
          </div>
        </div>
        <div class="col-12">
          <div class="form-wrap">
            <label class="form-label" for="area-descrizione">Descrizione</label>
            <textarea class="form-input" id="area-descrizione" name="descrizione"><?php echo htmlspecialchars($area['descrizione']); ?></textarea>
          </div>
        </div>
      </div>
      <div class="text-center">
        <button class="button button-primary button-lg" type="submit">Modifica</button>
      </div>
    </form>
  </div>
</section>

<?php
include_once ('../../template_footer.php');
ob_end_flush(); // Termina il buffer di output
?>