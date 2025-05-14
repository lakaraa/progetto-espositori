<?php
include_once '../../config.php';
include_once '../../queries.php';

// Gestione delle richieste POST (AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    ob_clean(); // Pulisce il buffer di output

    $idManifestazione = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $nome = $_POST['Nome'] ?? '';
    $descrizione = $_POST['Descrizione'] ?? '';
    $luogo = $_POST['Luogo'] ?? '';
    $durata = $_POST['Durata'] ?? '';
    $data = $_POST['Data'] ?? '';

    if (empty($nome) || empty($descrizione) || empty($luogo) || empty($durata) || empty($data)) {
        echo json_encode([
            'success' => false,
            'message' => 'Tutti i campi sono obbligatori.'
        ]);
        exit;
    }

    try {
        if (updateManifestazione($pdo, $idManifestazione, $nome, $descrizione, $luogo, $durata, $data)) {
            echo json_encode([
                'success' => true,
                'message' => 'Manifestazione aggiornata con successo.'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Errore durante l\'aggiornamento della manifestazione.'
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Errore di connessione al database: ' . $e->getMessage()
        ]);
    }
    exit;
}

// Solo se non è una richiesta POST, includi il template
include_once '../../template_header.php';

// Recupera l'ID della manifestazione dalla query string
$idManifestazione = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Recupera i dettagli della manifestazione
$manifestazione = getManifestazioneById($pdo, $idManifestazione);

if (!$manifestazione) {
    echo "<p style='color: red;'>Manifestazione non trovata.</p>";
    exit;
}
?>

<style>
/* Stile per il calendario */
input[type="date"]::-webkit-calendar-picker-indicator {
    filter: invert(1);
    cursor: pointer;
}

input[type="date"]::-webkit-datetime-edit {
    color: white;
}

input[type="date"]::-webkit-datetime-edit-fields-wrapper {
    color: white;
}

input[type="date"]::-webkit-datetime-edit-text {
    color: white;
}

input[type="date"]::-webkit-datetime-edit-year-field {
    color: white;
}

input[type="date"]::-webkit-datetime-edit-month-field {
    color: white;
}

input[type="date"]::-webkit-datetime-edit-day-field {
    color: white;
}
</style>

<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(/progetto-espositori/resources/images/sfondo.jpg);">
  <div class="container">
      <h2 class="breadcrumbs-custom-title">Modifica Dettagli Manifestazione</h2>
  </div>
  <ul class="breadcrumbs-custom-path">
    <li><a href="../dashboard_personale.php">Dashboard</a></li>
    <li><a href="gestisci_manifestazione.php">Gestione Manifestazioni</a></li>
    <li><a href="modifica_manifestazione.php">Modifica Manifestazione</a></li>
    <li class="active">Modifica Dettagli Manifestazione</li>
  </ul>
</section>

<!-- Main Content-->
<section class="section section-lg bg-default text-center">
  <div class="container">
    <h2>Modifica Manifestazione</h2>
    <p>Compila il modulo sottostante per modificare la manifestazione.</p>

    <!-- Output del messaggio -->
    <div id="form-message"></div>

    <form class="form-modifica-manifestazione" method="post" action="">
      <input type="hidden" name="id" value="<?php echo htmlspecialchars($idManifestazione); ?>">
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
          <div class="form-wrap" style="color: white;">
            <label class="form-label" for="manifestazione-data">Data</label>
            <input class="form-input" id="manifestazione-data" type="date" name="Data" value="<?php echo htmlspecialchars($manifestazione['Data']); ?>" style="color: white; background-color: transparent;">
          </div>
        </div>
        <div class="col-12">
          <div class="form-wrap">
            <label class="form-label" for="manifestazione-descrizione">Descrizione</label>
            <textarea class="form-input" id="manifestazione-descrizione" name="Descrizione"><?php echo htmlspecialchars($manifestazione['Descrizione']); ?></textarea>
          </div>
        </div>
      </div>
      <div class="col-md-12 text-center"> 
        <button class="button button-primary button-lg" type="submit">Modifica</button>
      </div>
    </form>
  </div>
</section>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function () {
    $('.form-modifica-manifestazione').on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            url: '', // stessa pagina
            method: 'POST',
            data: $(this).serialize(),
            success: function (response) {
                try {
                    const data = typeof response === "string" ? JSON.parse(response) : response;
                    const message = data.message || "Messaggio non disponibile.";
                    const isSuccess = data.success === true;

                    $('#form-message').html(
                        `<p style="color: ${isSuccess ? 'rgb(74, 196, 207)' : 'red'};">${message}</p>`
                    );

                    // Rimuovi il messaggio dopo 3 secondi
                    setTimeout(() => {
                        $('#form-message').fadeOut(400, function() {
                            $(this).empty().show();
                        });
                    }, 3000);
                } catch (e) {
                    console.error("Errore JSON.parse:", e);
                    $('#form-message').html('<p style="color: red;">Risposta non valida dal server.</p>');
                }
            },
            error: function (xhr, status, error) {
                console.error("Errore AJAX:", status, error);
                $('#form-message').html('<p style="color: red;">Errore di comunicazione con il server.</p>');
            }
        });
    });
});
</script>

<?php
include_once '../../template_footer.php';
?>
