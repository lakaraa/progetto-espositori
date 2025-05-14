<?php
include_once("../../config.php");
include_once("../../queries.php");
include_once("../../session.php");

// Se la richiesta è AJAX, evita di caricare il template
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', '../../error_log.txt'); // Percorso del file log

    // Pulizia buffer per evitare output HTML
    ob_start();
    header('Content-Type: application/json');
    ob_clean();

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
        $result = addManifestazione($pdo, $nome, $descrizione, $luogo, $durata, $data);
        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Manifestazione creata con successo!' : 'Errore durante l\'aggiunta della manifestazione.'
        ]);
        exit;
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Errore di connessione al database: ' . $e->getMessage()
        ]);
        exit;
    }
}

// **Solo se non è una richiesta AJAX, includi il template**
include_once("../../template_header.php");
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
        <h2 class="breadcrumbs-custom-title">Aggiungi Manifestazione</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="../dashboard_personale.php">Dashboard</a></li>
        <li><a href="gestisci_manifestazione.php">Gestione Manifestazioni</a></li>
        <li class="active">Aggiungi Manifestazione</li>
    </ul>
</section>

<!-- Main Content-->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Aggiungi Manifestazione</h2>
        <p>Compila il modulo sottostante per creare una nuova manifestazione.</p>

        <!-- Output del messaggio -->
        <div id="form-message"></div>

        <form class="form-crea-manifestazione" method="post" action="">
            <div class="row row-50">
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="manifestazione-nome">Nome</label>
                        <input class="form-input" id="manifestazione-nome" type="text" name="Nome">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="manifestazione-luogo">Luogo</label>
                        <input class="form-input" id="manifestazione-luogo" type="text" name="Luogo">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="manifestazione-durata">Durata (gg)</label> <br>
                        <input class="form-input" id="manifestazione-durata" type="text" name="Durata">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap" style="color: white;">
                        <label class="form-label" for="manifestazione-data">Data</label> <br>
                        <input class="form-input" id="manifestazione-data" type="date" name="Data" style="color: white; background-color: transparent;">
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-wrap">
                        <label class="form-label" for="manifestazione-descrizione">Descrizione</label>
                        <textarea class="form-input" id="manifestazione-descrizione" name="Descrizione"></textarea>
                    </div>
                </div>
            </div>
            <button class="button button-primary button-lg" type="submit">Crea</button>
        </form>
    </div>
</section>

<!-- Script AJAX -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function () {
    $('.form-crea-manifestazione').on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            url: '', // stessa pagina
            method: 'POST',
            data: $(this).serialize(),
            success: function (response) {
                console.log("RISPOSTA RAW:", response); // Log della risposta
                try {
                    const data = typeof response === "string" ? JSON.parse(response) : response;
                    const message = data.message || "Messaggio non disponibile.";
                    const isSuccess = data.success === true;

                    $('#form-message').html(
                        `<p style="color: ${isSuccess ? 'rgb(74, 196, 207)' : 'red'};">${message}</p>`
                    );

                    if (isSuccess) {
                        $('.form-crea-manifestazione')[0].reset();
                    }
                } catch (e) {
                    console.error("Errore JSON.parse:", e, response);
                    $('#form-message').html('<p style="color: red;">Risposta non valida dal server.</p>');
                }
            },
            error: function (xhr, status, error) {
                console.error("Errore AJAX:", status, error, xhr.responseText);
                $('#form-message').html('<p style="color: red;">Errore di comunicazione con il server.</p>');
            }
        });
    });
});
</script>

<?php
include_once("../../template_footer.php");
?>