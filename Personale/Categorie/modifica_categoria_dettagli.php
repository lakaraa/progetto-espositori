<?php
error_reporting(E_ALL); // Report all errors and warnings
ini_set('display_errors', 1); // Display errors on the screen

ob_start(); // Inizia il buffer di output

include_once("../../config.php");
include_once("../../queries.php");
include_once("../../session.php");
include_once("../../template_header.php");

// Recupera l'ID della categoria dalla query string
$idCategoria = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Recupera i dettagli della categoria
$categoria = getCategoriaById($pdo, $idCategoria);

if (!$categoria) {
    echo "<p style='color: red;'>Categoria non trovata.</p>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recupera i dati dal form, usa i valori esistenti se i campi sono vuoti
    $nome = !empty($_POST['nome']) ? $_POST['nome'] : $categoria['Nome'];
    $descrizione = !empty($_POST['descrizione']) ? $_POST['descrizione'] : $categoria['Descrizione'];
    
    // Aggiorna la categoria nel database
    if (updateCategoria($pdo, $idCategoria, $nome, $descrizione)) {
        header('Location: modifica_categoria.php');
        exit;
    } else {
        echo "<p style='color: red;'>Errore durante l'aggiornamento della categoria.</p>";
    }
}
?>

<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(../../resources/images/sfondo.jpg);">
  <div class="container">
      <h2 class="breadcrumbs-custom-title">Modifica Dettagli Categoria</h2>
  </div>
  <ul class="breadcrumbs-custom-path">
    <li><a href="../dashboard_personale.php">Dashboard</a></li>
    <li><a href="gestisci_categorie.php">Gestione Categorie</a></li>
    <li><a href="modifica_categoria.php">Modifica Categorie</a></li>
    <li class="active">Modifica Dettagli Categoria</li>
  </ul>
</section>

<!-- Main Content-->
<section class="section section-lg bg-default text-center">
  <div class="container">
    <h2>Modifica Categoria</h2>
    <p>Compila il modulo sottostante per modificare i dati della categoria.</p>
    <form class="rd-form rd-mailform" method="post" action="">
      <div class="row row-50">
        <div class="col-md-6">
          <div class="form-wrap">
            <label class="form-label" for="categoria-nome">Nome Categoria</label>
            <input class="form-input" id="categoria-nome" type="text" name="nome" value="<?php echo htmlspecialchars($categoria['Nome']); ?>" required>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-wrap">
            <label class="form-label" for="categoria-descrizione">Descrizione</label>
            <input class="form-input" id="categoria-descrizione" type="text" name="descrizione" value="<?php echo htmlspecialchars($categoria['Descrizione']); ?>" required>
          </div>
        </div>
      </div>
      <div class="col-md-12">
        <div class="text-center">
          <br>
          <button class="button button-primary button-lg" type="submit">Salva Modifiche</button>
        </div>
      </div>
    </form>
  </div>
</section>

<?php
include_once ('../../template_footer.php');
ob_end_flush(); // Termina il buffer di output
?>