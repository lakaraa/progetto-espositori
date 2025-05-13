<?php
ob_start(); // Inizia il buffer di output

include_once("../../config.php");
include_once("../../queries.php");
include_once("../../session.php");
include_once("../../template_header.php");

// Recupera l'ID del visitatore dalla query string
$idVisitatore = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Recupera i dettagli del visitatore
$visitatore = getVisitatoreById($pdo, $idVisitatore);

if (!$visitatore) {
    echo "<p style='color: red;'>Visitatore non trovato.</p>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recupera i dati dal form, usa i valori esistenti se i campi sono vuoti
    $username = !empty($_POST['username']) ? $_POST['username'] : $visitatore['Username'];
    $nome = !empty($_POST['nome']) ? $_POST['nome'] : $visitatore['Nome'];
    $cognome = !empty($_POST['cognome']) ? $_POST['cognome'] : $visitatore['Cognome'];
    $email = !empty($_POST['email']) ? $_POST['email'] : $visitatore['Email'];
    $telefono = !empty($_POST['telefono']) ? $_POST['telefono'] : $visitatore['Telefono'];
    
    // Gestione password (solo se fornita)
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $visitatore['Password'];

    // Aggiorna il visitatore nel database
    if (updateVisitatore($pdo, $idVisitatore, $username, $password, $nome, $cognome, $email, $telefono)) {
        header('Location: modifica_visitatore.php');
        exit;
    } else {
        echo "<p style='color: red;'>Errore durante l'aggiornamento del visitatore.</p>";
    }
}
?>

<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(/progetto-espositori/resources/images/bg-breadcrumbs-07-1920x480.jpg);">
  <div class="container">
      <h2 class="breadcrumbs-custom-title">Modifica Dettagli Visitatore</h2>
  </div>
  <ul class="breadcrumbs-custom-path">
    <li><a href="../dashboard_personale.php">Dashboard</a></li>
    <li><a href="/progetto-espositori/Personale/Visitori/gestisci_visitatore.php">Gestione Visitatori</a></li>
    <li><a href="/progetto-espositori/Personale/Visitori/modifica_visitatore.php">Modifica Visitatori</a></li>
    <li class="active">Modifica Dettagli Visitatore</li>
  </ul>
</section>

<!-- Main Content-->
<section class="section section-lg bg-default text-center">
  <div class="container">
    <h2>Modifica Visitatore</h2>
    <p>Compila il modulo sottostante per modificare i dati del visitatore.</p>
    <form class="rd-form rd-mailform" method="post" action="">
      <div class="row row-50">
        <div class="col-md-6">
          <div class="form-wrap">
            <label class="form-label" for="visitatore-username">Username</label>
            <input class="form-input" id="visitatore-username" type="text" name="username" value="<?php echo htmlspecialchars($visitatore['Username']); ?>">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-wrap">
            <label class="form-label" for="visitatore-password">Nuova Password (lasciare vuoto per non modificare)</label>
            <input class="form-input" id="visitatore-password" type="password" name="password">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-wrap">
            <label class="form-label" for="visitatore-nome">Nome</label>
            <input class="form-input" id="visitatore-nome" type="text" name="nome" value="<?php echo htmlspecialchars($visitatore['Nome']); ?>">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-wrap">
            <label class="form-label" for="visitatore-cognome">Cognome</label>
            <input class="form-input" id="visitatore-cognome" type="text" name="cognome" value="<?php echo htmlspecialchars($visitatore['Cognome']); ?>">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-wrap">
            <label class="form-label" for="visitatore-email">Email</label>
            <input class="form-input" id="visitatore-email" type="email" name="email" value="<?php echo htmlspecialchars($visitatore['Email']); ?>">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-wrap">
            <label class="form-label" for="visitatore-telefono">Telefono</label>
            <input class="form-input" id="visitatore-telefono" type="tel" name="telefono" value="<?php echo htmlspecialchars($visitatore['Telefono']); ?>">
          </div>
        </div>
      </div>
      <div class="col-md-12 text-center"> 
        <button class="button button-primary button-lg" type="submit">Salva Modifiche</button>
      </div>
    </form>
  </div>
</section>

<?php
include_once ('../../template_footer.php');
ob_end_flush(); // Termina il buffer di output
?>