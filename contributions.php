<?php
include 'config.php'; 
include 'queries.php'; 

$manifestazioni = getManifestazioni($pdo); 
$contributi = getContributi($pdo); 

include 'template_header.php'; 
?>

<?php

?>
      <!-- Breadcrumbs-->
      <section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(images/bg-breadcrumbs-05-1920x480.jpg);">
        <div class="container">
          <h2 class="breadcrumbs-custom-title"><?php echo htmlspecialchars($manifestazione['Nome']); ?></h2>
        </div>
        <ul class="breadcrumbs-custom-path">
          <li><a href="index.php">Home</a></li>
          <li class="active">Contributions</li>
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
              <ul class="list-group">
                <?php
                // Visualizza i contributi
                if (!empty($contributi)) {
                    foreach ($contributi as $contributo) {
                        echo "<li class='list-group-item'>";
                        echo "<h5>" . htmlspecialchars($contributo['Titolo']) . "</h5>";
                        echo "<p><strong>Sintesi:</strong> " . htmlspecialchars($contributo['Sintesi']) . "</p>";
                        echo "<p><strong>Accettazione:</strong> " . ($contributo['Accettazione'] ? "Accettato" : "Non accettato") . "</p>";
                        echo "</li>";
                    }
                } else {
                    echo "<li class='list-group-item'>Nessun contributo trovato.</li>";
                }
                ?>
              </ul>
            </div>
          </div>
        </div>
      </section>
