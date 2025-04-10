<?php
include_once('../config.php');
include_once('../queries.php');
include_once('../session.php');
include_once('../template_header.php'); 


// Recupera l'ID della manifestazione dalla query string
// Accetta sia 'id' che 'manifestazione_id'
$idManifestazione = isset($_GET['id']) ? intval($_GET['id']) : 
  (isset($_GET['manifestazione_id']) ? intval($_GET['manifestazione_id']) : 0);

// Recupera i dati della manifestazione
$manifestazione = getManifestazioneById($pdo, $idManifestazione);

// Verifica se la manifestazione esiste
if (!$manifestazione) {
    die("Manifestazione non trovata.");
}

// Recupera i contributi legati alla manifestazione
$contributi = getContributiByManifestazione($pdo, $idManifestazione);


?>

<!-- Breadcrumbs -->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(images/bg-breadcrumbs-05-1920x480.jpg);">
  <div class="container">
    <h2 class="breadcrumbs-custom-title"><?php echo htmlspecialchars($manifestazione['Nome']); ?></h2>
  </div>
  <ul class="breadcrumbs-custom-path">
    <li><a href="manifestazione.php">Manifestazioni</a></li>
    <li class="active">Contributi</li>
  </ul>
</section>

<!-- Manifestazione Section-->
<section class="section section-lg bg-default">
  <div class="container">
    <div class="row row-50 justify-content-center">
      <div class="col-md-10 col-lg-8">
        <h3>Manifestazione</h3>
        <p><strong>Nome:</strong> <?php echo htmlspecialchars($manifestazione['Nome']); ?></p>
        <p><strong>Descrizione:</strong> <?php echo htmlspecialchars($manifestazione['Descrizione']); ?></p>
        <p><strong>Luogo:</strong> <?php echo htmlspecialchars($manifestazione['Luogo']); ?></p>
        <p><strong>Durata:</strong> <?php echo htmlspecialchars($manifestazione['Durata']); ?> giorni</p>
        <p><strong>Data:</strong> <?php echo htmlspecialchars($manifestazione['Data']); ?></p>
      </div>
    </div>
  </div>
</section>

<!-- Contributions Section-->
<section class="section section-lg bg-default">
  <div class="container">
    <div class="row row-50 justify-content-center">
      <div class="col-md-10 col-lg-8">
        <h3>Contributi</h3>
        <ul class="list-group" style="color: rgb(34, 45, 79);">
          <?php
          // Verifica se ci sono contributi
          if (!empty($contributi)) {
              foreach ($contributi as $contributo) {
                  echo "<li class='list-group-item'>";
                  echo "<h5 style='color: rgb(34, 45, 79);'>" . htmlspecialchars($contributo['Titolo']) . "</h5>";
                  echo "<p><strong>Sintesi:</strong> " . htmlspecialchars($contributo['Sintesi']) . "</p>";
                  echo "<p><strong>Accettazione:</strong> " . ($contributo['Accettazione'] ? "Accettato" : "Non accettato") . "</p>";
                  echo "</li>";
              }
          } else {
              echo "<li class='list-group-item'>Nessun contributo trovato.</li>";
          }
          ?>
        </ul>

        <!-- Visualizza il pulsante per i contribuenti (Espositore) -->
        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'Espositore'): ?>
            <div class="text-center mt-4">
                <a href="candidati.php?id=<?php echo $idManifestazione; ?>" class="button-primary button-lg">Canditati</a>
            </div>
        <?php endif; ?>

        <!-- Visualizza il pulsante per i visitatori -->
        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'Visitatore'): ?>
            <div class="text-center mt-4">
                <a href="prenotazione.php?id=<?php echo $idManifestazione; ?>" class="button-primary button-lg">Prenota</a>
            </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<?php
// Includi il footer del template
include_once('../template_footer.php');
?>
