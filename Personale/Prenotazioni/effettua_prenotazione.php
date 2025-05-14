<?php
include_once '../../config.php';
include_once '../../queries.php';

$visitatori = getVisitatori($pdo);  // Recupera i visitatori
$manifestazioni = getManifestazioni($pdo);  // Recupera le manifestazioni

// Controlla se il modulo è stato inviato
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);

    ob_start(); // Inizia il buffer di output
    header('Content-Type: application/json');
    ob_clean(); // Pulisce il buffer di output

    $visitatore = $_POST['visitatore'];
    $manifestazione = $_POST['manifestazione'];
    $turno = $_POST['Id_Turno'];
    $area = $_POST['area'];

    if (!isset($visitatore, $manifestazione, $turno, $area) ||
        $visitatore === '' || $manifestazione === '' || $turno === '' || $area === ''
    ) {
        echo json_encode([
            'success' => false,
            'message' => 'Tutti i campi sono obbligatori.'
        ]);
        exit;
    }
    
    try {
        // Controlla se la prenotazione esiste già
        $prenotazioneEsistente = checkExistingPrenotazione($pdo, $visitatore, $turno);
        if ($prenotazioneEsistente) {
            echo json_encode([
                'success' => false,
                'message' => 'Esiste già una prenotazione per questo visitatore in questo turno.'
            ]);
            exit;
        }

        // Funzione per aggiungere la prenotazione
        $result = addPrenotazioneByPersonale($pdo, $visitatore, $turno);
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
include_once '../../template_header.php';
?>

<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(/progetto-espositori/resources/images/sfondo.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Effettua Prenotazione</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="../dashboard_personale.php">Dashboard</a></li>
        <li><a href="gestisci_prenotazione.php">Gestisci Prenotazioni</a></li>
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
                    <select class="form-input" id="turno" name="Id_Turno" disabled>
                        <option value="">Seleziona Turno</option>
                        <!-- I turni verranno caricati dinamicamente -->
                    </select>
                </div>
            </div>
            <button class="button button-primary button-lg" type="submit" id="btn-prenota" disabled>Effettua Prenotazione</button>
        </form>
    </div>
</section>

<!--script AJAX-->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {

        // Funzione per controllare se il bottone può essere abilitato
        function aggiornaStatoBottonePrenota() {
            const turnoSelezionato = $('#turno').val();
            if (turnoSelezionato) {
                $('#btn-prenota').prop('disabled', false);
            } else {
                $('#btn-prenota').prop('disabled', true);
            }
        }

        // Gestisce il cambio della selezione della manifestazione
        $('#manifestazione').change(function () {
            var manifestazioneId = $(this).val();

            if (manifestazioneId) {
                $('#area').prop('disabled', false); 
                $('#turno').prop('disabled', true).html('<option value="">Seleziona Area prima</option>'); // Reset dei turni
                aggiornaStatoBottonePrenota();

                $.ajax({
                    url: 'get_aree.php',
                    type: 'POST',
                    data: { manifestazione_id: manifestazioneId },
                    success: function (data) {
                        $('#area').html(data);
                    },
                    error: function () {
                        $('#area').html('<option value="">Errore nel recupero delle aree</option>');
                    }
                });
            } else {
                $('#area').prop('disabled', true).html('<option value="">Seleziona Manifestazione prima</option>');
                $('#turno').prop('disabled', true).html('<option value="">Seleziona Manifestazione prima</option>');
                aggiornaStatoBottonePrenota();
            }
        });

        // Gestisce il cambio della selezione dell'area
        $('#area').change(function () {
            var areaId = $(this).val();
            var manifestazioneId = $('#manifestazione').val();

            if (areaId && manifestazioneId) {
                $('#turno').prop('disabled', false);

                $.ajax({
                    url: 'get_turni.php',
                    type: 'POST',
                    data: { 
                        manifestazione_id: manifestazioneId,
                        area_id: areaId
                    },
                    success: function (data) {
                        $('#turno').html(data);
                        aggiornaStatoBottonePrenota(); // Verifica se abilitare il bottone
                    },
                    error: function () {
                        $('#turno').html('<option value="">Errore nel recupero dei turni</option>');
                        aggiornaStatoBottonePrenota();
                    }
                });
            } else {
                $('#turno').prop('disabled', true).html('<option value="">Seleziona Area prima</option>');
                aggiornaStatoBottonePrenota();
            }
        });

        // Quando l'utente cambia il turno manualmente
        $('#turno').change(function () {
            aggiornaStatoBottonePrenota();
        });

        // Gestione invio form
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
                        // Resetta il form
                        //$('.form-effettua-pre')[0].reset();
                        //aggiornaStatoBottonePrenota(); // Disabilita il bottone dopo reset
                        location.reload();
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

