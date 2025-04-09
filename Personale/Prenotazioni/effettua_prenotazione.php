<?php
include_once '../../config.php';
include_once '../../queries.php';

$visitatori = getVisitatori($pdo);  // Recupera i visitatori
$manifestazioni = getManifestazioni($pdo);  // Recupera le manifestazioni

// Controlla se il modulo è stato inviato
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    ob_clean(); // Pulisce il buffer di output

    $visitatore = $_POST['visitatore'];
    $manifestazione = $_POST['manifestazione'];
    $turno = $_POST['turno'];
    $area = $_POST['area'];

    // Log dei dati ricevuti
    error_log("Dati POST ricevuti: " . print_r($_POST, true));

    if (empty($visitatore) || empty($manifestazione) || empty($turno) || empty($area)) {
        echo json_encode([
            'success' => false,
            'message' => 'Tutti i campi sono obbligatori.'
        ]);
        exit;
    }

    try {
        // Funzione per aggiungere la prenotazione
        $result = addPrenotazioneByPersonale($pdo, $visitatore, $manifestazione, $turno);
        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Prenotazione effettuata con successo!' : 'Errore durante l\'effettuazione della prenotazione.'
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

function addPrenotazioneByPersonale($pdo, $visitatore, $manifestazione, $turno) {
    error_log("Inizio funzione addPrenotazioneByPersonale");
    error_log("Dati ricevuti: Visitatore: $visitatore, Manifestazione: $manifestazione, Turno: $turno");

    // Verifica che il visitatore esista ed abbia ruolo 'Visitatore'
    $sqlVisitatore = "SELECT Id_Utente FROM utente WHERE Id_Utente = :visitatore AND Ruolo = 'Visitatore'";
    $stmtVisitatore = $pdo->prepare($sqlVisitatore);
    $stmtVisitatore->bindParam(':visitatore', $visitatore, PDO::PARAM_INT);
    $stmtVisitatore->execute();
    $idVisitatore = $stmtVisitatore->fetchColumn();

    if (!$idVisitatore) {
        error_log("Visitatore non trovato o non ha il ruolo 'Visitatore'.");
        return false;
    }
    error_log("Visitatore trovato: $idVisitatore");

    // Verifica che il turno esista ed sia collegato alla manifestazione selezionata
    $sqlTurno = "SELECT Id_Turno 
        FROM manifestazione 
        JOIN area ON manifestazione.Id_Manifestazione = area.Id_Manifestazione
        JOIN turno ON turno.Id_Area = area.Id_Area
        WHERE manifestazione.Id_Manifestazione = :manifestazione AND area.Id_Area = :area AND turno.Id_Turno = :turno";
    $stmtTurno = $pdo->prepare($sqlTurno);
    $stmtTurno->bindParam(':manifestazione', $manifestazione, PDO::PARAM_INT);
    $stmtTurno->bindParam(':area', $area, PDO::PARAM_INT);
    $stmtTurno->bindParam(':turno', $turno, PDO::PARAM_INT);
    $stmtTurno->execute();
    $idTurno = $stmtTurno->fetchColumn();

    if (!$idTurno) {
        error_log("Turno non trovato o non collegato alla manifestazione e area selezionate.");
        return false;
    }
    error_log("Turno trovato: $idTurno");

    // Verifica se esiste già una prenotazione per questo visitatore e turno
    $sqlCheck = "SELECT COUNT(*) FROM prenotazione WHERE Id_Utente = :idUtente AND Id_Turno = :idTurno";
    $stmtCheck = $pdo->prepare($sqlCheck);
    $stmtCheck->bindParam(':idUtente', $idVisitatore, PDO::PARAM_INT);
    $stmtCheck->bindParam(':idTurno', $idTurno, PDO::PARAM_INT);
    $stmtCheck->execute();

    if ($stmtCheck->fetchColumn() > 0) {
        error_log("Prenotazione già esistente per il visitatore e il turno selezionati.");
        return false; // Prenotazione già esistente
    }
    error_log("Nessuna prenotazione esistente trovata");

    // Inserisce la prenotazione
    $sqlInsert = "INSERT INTO prenotazione (Id_Utente, Id_Turno) VALUES (:idUtente, :idTurno)";
    $stmtInsert = $pdo->prepare($sqlInsert);
    $stmtInsert->bindParam(':idUtente', $idVisitatore, PDO::PARAM_INT);
    $stmtInsert->bindParam(':idTurno', $idTurno, PDO::PARAM_INT);

    if ($stmtInsert->execute()) {
        error_log("Prenotazione inserita con successo.");
        return true;
    } else {
        error_log("Errore durante l'inserimento della prenotazione.");
        return false;
    }
}
include_once '../../template_header.php';
?>

<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(/progetto-espositori/resources/images/bg-breadcrumbs-07-1920x480.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Effettua Prenotazione</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="gestione_prenotazioni.php">Gestisci Prenotazioni</a></li>
        <li class="active">Effettua Prenotazione</li>
    </ul>
</section>
<!-- Main Content-->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Effettua Prenotazione</h2>
        <p>Compila il modulo sottostante per effettuare una prenotazione.</p>

        <!-- Output del messaggio -->
        <div id="form-message"></div>

        <form class="form-effettua-pre" method="post" action="">
            <div class="row row-50">

                <!-- Seleziona Visitatore -->
                <div class="col-md-6">
                    <select class="form-input" id="visitatore" name="visitatore">
                        <option value="">Seleziona Visitatore</option>
                        <?php foreach ($visitatori as $visitatore): ?>
                            <option value="<?php echo htmlspecialchars($visitatore['Id_Utente']); ?>" style="color: black; background-color: white;"><?php echo htmlspecialchars($visitatore['Nome']) . ' ' . htmlspecialchars($visitatore['Cognome']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Seleziona Manifestazione -->
                <div class="col-md-6">
                    <select class="form-input" id="manifestazione" name="manifestazione">
                        <option value="">Seleziona Manifestazione</option>
                        <?php foreach ($manifestazioni as $manifestazione): ?>
                            <option value="<?php echo htmlspecialchars($manifestazione['Id_Manifestazione']); ?>" style="color: black; background-color: white;"><?php echo htmlspecialchars($manifestazione['Nome']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Seleziona Area -->
                <div class="col-md-6">
                    <select class="form-input" id="area" name="area" disabled>
                        <option value="">Seleziona Area</option>
                        <!-- Le aree verranno caricate dinamicamente in base alla manifestazione -->
                    </select>
                </div>
                <!-- Seleziona Turno -->
                <div class="col-md-6">
                    <select class="form-input" id="turno" name="turno" disabled>
                        <option value="">Seleziona Turno</option>
                        <!-- I turni verranno caricati dinamicamente -->
                    </select>
                </div>
            </div>
            <?php
                error_log("Dati POST ricevuti: " . print_r($_POST, true));
            ?>
            <button class="button button-primary button-lg" type="submit">Effettua Prenotazione</button>
            <?php error_log("Dati POST ricevuti: " . print_r($_POST, true));?>
        </form>
    </div>
</section>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
   $(document).ready(function () {
    // Gestisce il cambio della selezione della manifestazione
    $('#manifestazione').change(function () {
        var manifestazioneId = $(this).val();

        // Abilita o disabilita il campo area in base alla selezione
        if (manifestazioneId) {
            $('#area').prop('disabled', false); 
            $('#turno').prop('disabled', true).html('<option value="">Seleziona Area prima</option>'); // Reset dei turni

            // Effettua la richiesta AJAX per ottenere le aree
            $.ajax({
                url: 'get_aree.php',
                type: 'POST',
                data: { manifestazione_id: manifestazioneId },
                success: function (data) {
                    $('#area').html(data); // Aggiorna le opzioni dell'area
                },
                error: function () {
                    $('#area').html('<option value="">Errore nel recupero delle aree</option>');
                }
            });
        } else {
            $('#area').prop('disabled', true).html('<option value="">Seleziona Manifestazione prima</option>');
            $('#turno').prop('disabled', true).html('<option value="">Seleziona Manifestazione prima</option>');
        }
    });

    // Gestisce il cambio della selezione dell'area
    $('#area').change(function () {
    var areaId = $(this).val();
    var manifestazioneId = $('#manifestazione').val();

    if (areaId && manifestazioneId) {
        $('#turno').prop('disabled', false); // Abilita la selezione dei turni

        // Effettua la richiesta AJAX per ottenere i turni
        $.ajax({
            url: 'get_turni.php',
            type: 'POST',
            data: { 
                manifestazione_id: manifestazioneId,
                area_id: areaId // Invia anche l'ID dell'area
            },
            success: function (data) {
                $('#turno').html(data); // Aggiorna le opzioni dei turni
            },
            error: function () {
                $('#turno').html('<option value="">Errore nel recupero dei turni</option>');
            }
        });
    } else {
        $('#turno').prop('disabled', true).html('<option value="">Seleziona Area prima</option>');
    }
});

});

$(function () {
    $('.form-effettua-pre').on('submit', function (e) {
        e.preventDefault();

        const formData = $(this).serialize();
        console.log("Dati inviati:", formData);

        $.ajax({
            url: '', // stessa pagina
            method: 'POST',
            data: formData,
            success: function (response) {
                console.log("Risposta dal server:", response);
                try {
                    const data = typeof response === 'string' ? JSON.parse(response) : response;
                    const message = data.message || 'Errore sconosciuto.';
                    const isSuccess = data.success === true;

                    $('#form-message').html(
                        '<p style="color: ' + (isSuccess ? 'rgb(74, 196, 207)' : 'red') + ';">' + message + '</p>'
                    );

                    if (isSuccess) {
                        $('.form-effettua-pre')[0].reset(); // Reset del modulo
                    }
                } catch (e) {
                    console.error("Errore JSON.parse:", e);
                    $('#form-message').html('<p style="color: red;">Risposta non valida dal server.</p>');
                }
            },
            error: function () {
                $('#form-message').append('<p style="color: red;">Errore durante l\'effettuazione della prenotazione.</p>');
            }
        });
    });
});

</script>

<?php
include_once '../../template_footer.php';
?>

