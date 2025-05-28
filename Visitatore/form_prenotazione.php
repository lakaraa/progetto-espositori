<?php
error_reporting(E_ALL); // Report all errors and warnings
ini_set('display_errors', 1); // Display errors on the screen

include_once '../config.php';
include_once '../session.php';
include_once '../queries.php';

// Verifica se l'utente è loggato
if (!isset($_SESSION['id_utente']) || $_SESSION['ruolo'] !== 'Visitatore') {
    header('Location: ../index.php');
    exit;
}

// Verifica se è stato fornito l'ID della manifestazione
if (!isset($_GET['id'])) {
    header('Location: dashboard_visitatore.php');
    exit;
}

$idManifestazione = $_GET['id'];
$userId = $_SESSION['id_utente'];

// Recupera i dettagli della manifestazione
$manifestazione = getManifestazioneById($pdo, $idManifestazione);
if (!$manifestazione) {
    header('Location: dashboard_visitatore.php');
    exit;
}

$nomeManifestazione = $manifestazione['Nome'];

// Controlla se il modulo è stato inviato
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);

    ob_start();
    header('Content-Type: application/json');
    ob_clean();

    $turno = $_POST['Id_Turno'];
    $area = $_POST['area'];

    if (!isset($turno, $area) || $turno === '' || $area === '') {
        echo json_encode([
            'success' => false,
            'message' => 'Tutti i campi sono obbligatori.'
        ]);
        exit;
    }
    
    try {
        // Controlla se la prenotazione esiste già
        $prenotazioneEsistente = checkExistingPrenotazione($pdo, $userId, $turno);
        if ($prenotazioneEsistente) {
            echo json_encode([
                'success' => false,
                'message' => 'Hai già una prenotazione per questo turno.'
            ]);
            exit;
        }

        // Aggiunge la prenotazione
        $result = addPrenotazione($pdo, $userId, $turno);
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

include_once '../template_header.php';
?>

<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(../resources/images/bg-breadcrumbs-07-1920x480.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Prenotazione</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="dashboard_visitatore.php">Dashboard</a></li>
        <li class="active">Prenotazione</li>
    </ul>
</section>

<!-- Main Content-->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Prenotazione per <?= htmlspecialchars($nomeManifestazione) ?></h2>
        <p>Compila il modulo sottostante per effettuare la tua prenotazione.</p>

        <!-- Output del messaggio -->
        <div id="form-message"></div>

        <form class="form-effettua-pre" method="post" action="">
            <input type="hidden" name="id_manifestazione" value="<?= htmlspecialchars($idManifestazione) ?>">
            <div class="row row-50">
                <!-- Seleziona Area -->
                <div class="col-md-6">
                    <select class="form-input" id="area" name="area" required>
                        <option value="">Seleziona Area</option>
                        <!-- Le aree verranno caricate dinamicamente -->
                    </select>
                </div>

                <!-- Seleziona Turno -->
                <div class="col-md-6">
                    <select class="form-input" id="turno" name="Id_Turno" required disabled>
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
        const manifestazioneId = <?= $idManifestazione ?>;

        // Funzione per controllare se il bottone può essere abilitato
        function aggiornaStatoBottonePrenota() {
            const turnoSelezionato = $('#turno').val();
            if (turnoSelezionato) {
                $('#btn-prenota').prop('disabled', false);
            } else {
                $('#btn-prenota').prop('disabled', true);
            }
        }

        // Carica le aree all'avvio
        $.ajax({
            url: '../Personale/Prenotazioni/get_aree.php',
            type: 'POST',
            data: { manifestazione_id: manifestazioneId },
            success: function (data) {
                $('#area').html(data);
            },
            error: function () {
                $('#area').html('<option value="">Errore nel recupero delle aree</option>');
            }
        });

        // Gestisce il cambio della selezione dell'area
        $('#area').change(function () {
            var areaId = $(this).val();

            if (areaId) {
                $('#turno').prop('disabled', false);

                $.ajax({
                    url: '../Personale/Prenotazioni/get_turni.php',
                    type: 'POST',
                    data: { 
                        manifestazione_id: manifestazioneId,
                        area_id: areaId
                    },
                    success: function (data) {
                        $('#turno').html(data);
                        aggiornaStatoBottonePrenota();
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
                            setTimeout(function() {
                                window.location.href = 'dashboard_visitatore.php';
                            }, 2000);
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
include_once '../template_footer.php';
?>

