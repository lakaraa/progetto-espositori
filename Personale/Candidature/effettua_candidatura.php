<?php
include_once("../../config.php");
include_once("../../queries.php");
include_once("../../session.php");

// Recupera gli utenti per il menu a tendina
$utenti = getUtenti($pdo); // Funzione per ottenere gli utenti dalla tabella Utente

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', '../../error_log.txt'); // Percorso del file log

    // Pulizia buffer per evitare output HTML
    ob_start();
    header('Content-Type: application/json');
    ob_clean();

    $idUtente = $_POST['Id_Utente'] ?? '';
    $titolo = $_POST['Titolo'] ?? '';
    $sintesi = $_POST['Sintesi'] ?? '';
    $accettazione = "Accettato"; // Valore fisso
    $url = $_POST['URL'] ?? '';

    // Gestione caricamento immagine
    $immagine = '';
    if (isset($_FILES['Immagine']) && $_FILES['Immagine']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../uploads/img/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        // Recupera username, nome e cognome dell'utente
        $userInfo = getUsernameById($pdo, $idUtente);
        
        // Genera un nome file con username_nome_cognome
        $fileExtension = pathinfo($_FILES['Immagine']['name'], PATHINFO_EXTENSION);
        $immagine = $userInfo['Username'] . '_' . $userInfo['Nome'] . '_' . $userInfo['Cognome'] . '.' . $fileExtension;
        $targetFile = $uploadDir . $immagine;

        // Controlla che il file sia un'immagine
        $check = getimagesize($_FILES['Immagine']['tmp_name']);
        if ($check === false) {
            echo json_encode([
                'success' => false,
                'message' => 'Il file caricato non è un\'immagine valida.'
            ]);
            exit;
        }

        // Sposta il file nella directory di destinazione
        if (!move_uploaded_file($_FILES['Immagine']['tmp_name'], $targetFile)) {
            echo json_encode([
                'success' => false,
                'message' => 'Errore durante il caricamento dell\'immagine.'
            ]);
            exit;
        }
    }

    if (empty($idUtente) || empty($titolo) || empty($sintesi)) {
        echo json_encode([
            'success' => false,
            'message' => 'Tutti i campi sono obbligatori.'
        ]);
        exit;
    }

    try {
        $result = addContributo($pdo, $idUtente, $immagine, $titolo, $sintesi, $accettazione, $url);
        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Candidatura effettuata con successo!' : 'Errore durante l\'aggiunta della candidatura.'
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

<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(/progetto-espositori/resources/images/bg-breadcrumbs-07-1920x480.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Effettua Candidatura</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="../dashboard_personale.php">Dashboard</a></li>
        <li><a href="gestisci_candidature.php">Gestione Candidature</a></li>
        <li class="active">Effettua Candidatura</li>
    </ul>
</section>

<!-- Main Content-->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Effettua Candidatura</h2>
        <p>Compila il modulo sottostante per effettuare una candidatura.</p>

        <!-- Output del messaggio -->
        <div id="form-message"></div>

        <form class="form-effettua-candidatura" method="post" action="" enctype="multipart/form-data">
            <div class="row row-50">
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="utente">Utente</label>
                        <select class="form-input" id="utente" name="Id_Utente" required>
                            <option value="" style="color: black;">Seleziona Utente</option>
                            <?php foreach ($utenti as $utente): ?>
                                <option value="<?php echo htmlspecialchars($utente['Id_Utente']); ?>" style="color: black;">
                                    <?php echo htmlspecialchars($utente['Email']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="titolo">Titolo</label>
                        <input class="form-input" id="titolo" type="text" name="Titolo" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="sintesi">Sintesi</label>
                        <textarea class="form-input" id="sintesi" name="Sintesi" required></textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="url">URL</label>
                        <input class="form-input" id="url" type="url" name="URL">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="immagine">Immagine</label>
                        <br>
                        <input class="form-input" id="immagine" type="file" name="Immagine" accept="image/*">
                    </div>
                </div>
            </div>
            <button class="button button-primary button-lg" type="submit">Effettua Candidatura</button>
        </form>
    </div>
</section>

<!-- Script AJAX -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function () {
    $('.form-effettua-candidatura').on('submit', function (e) {
        e.preventDefault();

        var formData = new FormData(this);

        $.ajax({
            url: '', // stessa pagina
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
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
                        $('.form-effettua-candidatura')[0].reset();
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