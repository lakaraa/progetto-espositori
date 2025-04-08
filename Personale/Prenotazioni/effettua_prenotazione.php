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

    if (empty($visitatore) || empty($manifestazione) || empty($turno)) {
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
    // Recupera l'ID del visitatore (Id_Utente)
    $sqlVisitatore = "SELECT Id_Utente FROM utente WHERE Id_Utente = :visitatore AND Ruolo = 'Visitatore'";
    $stmtVisitatore = $pdo->prepare($sqlVisitatore);
    $stmtVisitatore->bindParam(':visitatore', $visitatore, PDO::PARAM_INT);
    $stmtVisitatore->execute();
    $idVisitatore = $stmtVisitatore->fetchColumn();  // Ottiene l'ID del visitatore

    if (!$idVisitatore) {
        return false;  // Se non esiste il visitatore, ritorna false
    }

    // Recupera l'ID del turno (Id_Turno)
    $sqlTurno = "SELECT Id_Turno FROM turno WHERE Id_Turno = :turno";
    $stmtTurno = $pdo->prepare($sqlTurno);
    $stmtTurno->bindParam(':turno', $turno, PDO::PARAM_INT);
    $stmtTurno->execute();
    $idTurno = $stmtTurno->fetchColumn();  // Ottiene l'ID del turno

    if (!$idTurno) {
        return false;  // Se non esiste il turno, ritorna false
    }

    // Inserisce la prenotazione nella tabella prenotazione
    $sqlInsert = "INSERT INTO prenotazione (Id_Utente, Id_Turno) VALUES (:idUtente, :idTurno)";
    $stmtInsert = $pdo->prepare($sqlInsert);
    
    // Associa i parametri
    $stmtInsert->bindParam(':idUtente', $idVisitatore, PDO::PARAM_INT);
    $stmtInsert->bindParam(':idTurno', $idTurno, PDO::PARAM_INT);

    // Esegui la query e verifica se l'inserimento è andato a buon fine
    if ($stmtInsert->execute()) {
        return true;  // Prenotazione effettuata con successo
    } else {
        return false;  // Errore durante l'inserimento
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
                            <option value="<?php echo htmlspecialchars($manifestazione['Id_Manifestazione']); ?>"  style="color: black; background-color: white;"><?php echo htmlspecialchars($manifestazione['Nome']); ?></option>
                        <?php endforeach; ?>
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
            <button class="button button-primary button-lg" type="submit">Effettua Prenotazione</button>
        </form>
    </div>
</section>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        // Gestisce il cambio della selezione della manifestazione
        $('#manifestazione').change(function () {
            var manifestazioneId = $(this).val();

            // Abilita o disabilita il campo turno in base alla selezione
            if (manifestazioneId) {
                $('#turno').prop('disabled', false);

                // Effettua la richiesta AJAX per ottenere i turni
                $.ajax({
                    url: 'get_turni.php',
                    type: 'POST',
                    data: { manifestazione_id: manifestazioneId },
                    success: function (data) {
                        $('#turno').html(data); // Aggiorna le opzioni del turno
                    },
                    error: function () {
                        $('#turno').html('<option value="">Errore nel recupero dei turni</option>');
                    }
                });
            } else {
                $('#turno').prop('disabled', true).html('<option value="">Seleziona Manifestazione prima</option>');
            }
        });
    });

    $(function (){
        $('.form-effettua-pre').on('submit', function (e) {
            e.preventDefault();

            $.ajax({
                url: '', // stessa pagina
                method: 'POST',
                data: $(this).serialize(),
                success: function (response) {
                    console.log("RISPOSTA RAW:", response); // Stampa la risposta grezza

                    try{
                        const data = typeof response === 'string' ? JSON.parse(response) : response;
                        const message = data.message || 'Errore sconosciuto.';
                        const isSuccess = data.success === true;

                        $('#form-message').html(
                            '<p style="color: ' + (isSuccess ? 'rgb(74, 196, 207)' : 'red') + ';">' + message + '</p>'
                        );

                        if(isSuccess) {
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
    })
</script>

<?php
include_once '../../template_footer.php';
?>

