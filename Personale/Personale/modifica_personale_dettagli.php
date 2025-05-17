<?php
include_once '../../config.php';
include_once '../../queries.php';
include_once '../../template_header.php';

// Recupera l'ID della persona dalla query string
$idPersonale = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Recupera i dettagli del personale
$personale = getPersonaleById($pdo, $idPersonale);

if (!$personale) {
    echo "<p style='color: red;'>Personale non trovato.</p>";
    exit;
}

// Inizializza il messaggio di errore o successo
$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recupera i dati dal form
    $username = !empty($_POST['Username']) ? $_POST['Username'] : $personale['Username'];
    $password = !empty($_POST['Password']) ? $_POST['Password'] : $personale['Password'];
    $nome = !empty($_POST['Nome']) ? $_POST['Nome'] : $personale['Nome'];
    $cognome = !empty($_POST['Cognome']) ? $_POST['Cognome'] : $personale['Cognome'];
    $email = !empty($_POST['Email']) ? $_POST['Email'] : $personale['Email'];
    $telefono = !empty($_POST['Telefono']) ? $_POST['Telefono'] : $personale['Telefono'];

    // Aggiorna il personale nel database
    if (updatePersonale($pdo, $idPersonale, $username, $password, $nome, $cognome, $email, $telefono)) {
        $successMessage = "Personale aggiornato con successo.";
        // Ricarica i dettagli aggiornati del personale
        $personale = getPersonaleById($pdo, $idPersonale);
    } else {
        $errorMessage = "Errore durante l'aggiornamento del personale.";
    }
}



?>

<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(../../resources/images/sfondo.jpg);">
  <div class="container">
      <h2 class="breadcrumbs-custom-title">Modifica Dettagli Personale</h2>
  </div>
  <ul class="breadcrumbs-custom-path">
    <li><a href="../dashboard_personale.php">Dashboard</a></li>
    <li><a href="gestisci_personale.php">Gestione Personale</a></li>
    <li><a href="modifica_personale.php">Modifica Personale</a></li>
    <li class="active">Modifica Dettagli Personale</li>
  </ul>
</section>

<!-- Main Content-->
<section class="section section-lg bg-default text-center">
  <div class="container">
    <h2>Modifica Personale</h2>
    <p>Compila il modulo sottostante per modificare i dettagli del personale.</p>

    <!-- Mostra i messaggi di errore o successo -->
    <?php if (!empty($successMessage)): ?>
        <p style="color: rgb(74, 196, 207);"><?php echo htmlspecialchars($successMessage); ?></p>
    <?php endif; ?>
    <?php if (!empty($errorMessage)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($errorMessage); ?></p>
    <?php endif; ?>

    <form class="rd-form rd-mailform" method="post" action="">
      <div class="row row-50">
        <div class="col-md-6">
          <div class="form-wrap">
            <label class="form-label" for="personale-nome">Nome</label>
            <input class="form-input" id="personale-nome" type="text" name="Nome" value="<?php echo htmlspecialchars($personale['Nome']); ?>">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-wrap">
            <label class="form-label" for="personale-cognome">Cognome</label>
            <input class="form-input" id="personale-cognome" type="text" name="Cognome" value="<?php echo htmlspecialchars($personale['Cognome']); ?>">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-wrap">
            <label class="form-label" for="personale-email">Email</label>
            <input class="form-input" id="personale-email" type="email" name="Email" value="<?php echo htmlspecialchars($personale['Email']); ?>">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-wrap">
            <label class="form-label" for="personale-telefono">Telefono</label>
            <input class="form-input" id="personale-telefono" type="text" name="Telefono" value="<?php echo htmlspecialchars($personale['Telefono']); ?>">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-wrap">
            <label class="form-label" for="personale-username">Username</label>
            <input class="form-input" id="personale-username" type="text" name="Username" value="<?php echo htmlspecialchars($personale['Username']); ?>">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-wrap">
            <label class="form-label" for="personale-password">Password</label>
            <input class="form-input" id="personale-password" type="password" name="Password" value="">
          </div>
        </div>
      </div>
      <div class="col-md-12 text-center">
        <button class="button button-primary button-lg" type="submit">Modifica</button>
      </div>
    </form>
  </div>
</section>

<?php
include_once '../../template_footer.php';
?>
