<?php
error_reporting(E_ALL); // Report all errors and warnings
ini_set('display_errors', 1); // Display errors on the screen

include_once("../../config.php");
include_once("../../queries.php");
include_once("../../session.php");

// Abilita la visualizzazione degli errori per il debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Funzione per inviare una risposta JSON
function sendJsonResponse($success, $message, $debug = null) {
    header('Content-Type: application/json');
    $response = [
        'success' => $success,
        'message' => $message
    ];
    if ($debug !== null) {
        $response['debug'] = $debug;
    }
    echo json_encode($response);
    exit;
}

// Verifica se la richiesta è POST per accettare o rifiutare una candidatura
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');

    try {
        $idContributo = $_POST['Id_Contributo'] ?? '';
        $azione = $_POST['Azione'] ?? '';

        error_log("Ricevuta richiesta POST - Id_Contributo: $idContributo, Azione: $azione");

        if (empty($idContributo) || !in_array($azione, ['Accettato', 'Rifiutato'])) {
            throw new Exception('Dati non validi.');
        }

        // Inizia una transazione
        $pdo->beginTransaction();
        
        // Aggiorna lo stato della candidatura
        $stmt = $pdo->prepare("UPDATE contributo SET Accettazione = ? WHERE Id_Contributo = ?");
        $result = $stmt->execute([$azione, $idContributo]);
        
        if (!$result) {
            throw new PDOException("Errore nell'aggiornamento dello stato della candidatura");
        }

        error_log("Stato candidatura aggiornato con successo");
        
        // Commit della transazione
        $pdo->commit();
        error_log("Transazione completata con successo");
        
        $message = $azione === 'Accettato' ? 
            "Candidatura accettata con successo!" : 
            "Candidatura rifiutata con successo!";
        
        sendJsonResponse(true, $message);
        
    } catch (Exception $e) {
        // Rollback in caso di errore
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("Errore durante l'aggiornamento della candidatura: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        
        http_response_code(500);
        sendJsonResponse(false, 'Errore durante l\'aggiornamento della candidatura: ' . $e->getMessage(), [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
    exit;
}

// Recupera le candidature in approvazione per la visualizzazione della pagina
$manifestazione = $_GET['manifestazione'] ?? '';
$candidature = getCandidatureInApprovazione($pdo, $manifestazione);

// Se non è una richiesta POST, mostra la pagina normale
include_once("../../template_header.php");
?>


<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(../../resources/images/sfondo.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Accetta Candidature</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="../dashboard_personale.php">Dashboard</a></li>
        <li><a href="gestisci_candidature.php">Gestione Candidature</a></li>
        <li class="active">Accetta Candidature</li>
    </ul>
</section>

<!-- Main Content-->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Accetta o Rifiuta Candidature</h2>
        <p>Filtra le candidature per manifestazione e aggiorna il loro stato.</p>

        <!-- Barra di ricerca -->
        <form method="get" action="" id="searchForm">
            <div class="form-wrap">
                <input class="form-input" type="text" name="manifestazione" id="searchInput" placeholder="Cerca per manifestazione" value="<?php echo htmlspecialchars($manifestazione); ?>">
                <button class="button button-primary" type="submit">Cerca</button>
            </div>
        </form>

        <br>

        <!-- Output del messaggio -->
        <div id="form-message"></div>

        <!-- Tabella delle candidature -->
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Utente</th>
                        <th>Manifestazione</th>
                        <th>Titolo</th>
                        <th>Sintesi</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody id="candidatureTableBody">
                    <?php if (!empty($candidature)): ?>
                        <?php foreach ($candidature as $candidatura): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($candidatura['Email']); ?></td>
                                <td><?php echo htmlspecialchars($candidatura['Manifestazione']); ?></td>
                                <td><?php echo htmlspecialchars($candidatura['Titolo']); ?></td>
                                <td><?php echo htmlspecialchars($candidatura['Sintesi']); ?></td>
                                <td>
                                    <button class="button-accept btn-azione" data-id="<?php echo htmlspecialchars($candidatura['Id_Contributo']); ?>" data-azione="Accettato">Accetta</button>
                                    <button class="button-reject btn-azione" data-id="<?php echo htmlspecialchars($candidatura['Id_Contributo']); ?>" data-azione="Rifiutato">Rifiuta</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">Nessuna candidatura trovata.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- Script AJAX -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function () {
    // Gestione della ricerca
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        const manifestazione = $('#searchInput').val();
        
        $.ajax({
            url: 'search_candidature.php',
            method: 'GET',
            data: { manifestazione: manifestazione },
            success: function(response) {
                try {
                    const data = typeof response === "string" ? JSON.parse(response) : response;
                    updateTable(data);
                } catch (e) {
                    console.error("Errore nel parsing della risposta:", e);
                    $('#form-message').html('<p style="color: red;">Errore nel caricamento dei dati.</p>');
                }
            },
            error: function(xhr, status, error) {
                console.error("Errore AJAX:", status, error);
                $('#form-message').html('<p style="color: red;">Errore di comunicazione con il server.</p>');
            }
        });
    });

    // Funzione per gestire le azioni (accetta/rifiuta)
    function attachActionHandlers() {
        $('.btn-azione').off('click').on('click', function () {
            const button = $(this);
            const idContributo = button.data('id');
            const azione = button.data('azione');
            const row = button.closest('tr');

            if (confirm(`Sei sicuro di voler ${azione.toLowerCase()} questa candidatura?`)) {
                // Disabilita il pulsante durante l'elaborazione
                button.prop('disabled', true);
                
                $.ajax({
                    url: '',
                    method: 'POST',
                    data: { Id_Contributo: idContributo, Azione: azione },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            // Rimuovi la riga dalla tabella
                            row.fadeOut(400, function() {
                                $(this).remove();
                                // Se non ci sono più righe, mostra il messaggio "Nessuna candidatura trovata"
                                if ($('#candidatureTableBody tr').length === 0) {
                                    $('#candidatureTableBody').html('<tr><td colspan="5">Nessuna candidatura trovata.</td></tr>');
                                }
                            });
                            
                            $('#form-message').html(`<p style="color: rgb(74, 196, 207);">${response.message}</p>`);
                        } else {    
                            $('#form-message').html(`<p style="color: red;">${response.message}</p>`);
                            button.prop('disabled', false);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Errore AJAX:", {
                            status: status,
                            error: error,
                            response: xhr.responseText
                        });
                        
                        let errorMessage = 'Errore di comunicazione con il server.';
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.message) {
                                errorMessage = response.message;
                            }
                        } catch (e) {
                            console.error("Errore nel parsing della risposta:", e);
                        }
                        
                        $('#form-message').html(`<p style="color: red;">${errorMessage}</p>`);
                        button.prop('disabled', false);
                    }
                });
            }
        });
    }

    // Attach initial handlers
    attachActionHandlers();
});
</script>

<style>
.button-accept {
    background-color: rgb(74, 196, 207);
    border: none;
    color: white;
    padding: 10px 20px;
    font-size: 14px;
    border-radius: 25px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.button-accept:hover {
    background-color: rgb(60, 170, 180);
    transform: scale(1.05);
}

.button-reject {
    background-color: rgb(255, 77, 77);
    border: none;
    color: white;
    padding: 10px 20px;
    font-size: 14px;
    border-radius: 25px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.button-reject:hover {
    background-color: rgb(230, 50, 50);
    transform: scale(1.05);
}
</style>
<?php
include_once("../../template_footer.php");
?>