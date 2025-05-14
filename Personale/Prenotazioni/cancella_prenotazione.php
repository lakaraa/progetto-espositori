<?php
include_once '../../config.php';
include_once '../../queries.php';

// Gestione della cancellazione tramite POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idUtente']) && isset($_POST['idTurno']) && is_numeric($_POST['idUtente']) && is_numeric($_POST['idTurno'])) {
    header('Content-Type: application/json');
    $idUtente = intval($_POST['idUtente']);
    $idTurno = intval($_POST['idTurno']);
    try {
        // Cancella la prenotazione dal database
        if (deletePrenotazione($pdo, $idUtente, $idTurno)) {
            echo json_encode(['success' => true, 'message' => 'Prenotazione cancellata con successo.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Errore durante la cancellazione della prenotazione.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Errore di connessione al database: ' . $e->getMessage()]);
    }
    exit;
}

// Solo se non è una richiesta POST, includi il template
include_once '../../template_header.php';

//Recupera le prenotazioni dal database tramite la funzione getPrenotazioni
$prenotazioni = getPrenotazioni($pdo);
?>
<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(/progetto-espositori/resources/images/sfondo.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Elimina Prenotazione</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="../dashboard_personale.php">Dashboard</a></li>
        <li><a href="gestisci_prenotazione.php">Gestione Prenotazioni</a></li>
        <li class="active">Elimina Prenotazione</li>
    </ul>
</section>
<!-- Main Content-->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Elimina Prenotazione</h2>
        <p>Seleziona una prenotazione dalla lista sottostante per eliminarla.</p>
        <br>

        <!-- Messaggi di successo o errore -->
        <div id="form-message"></div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Cognome</th>
                        <th>Email</th>
                        <th>Manifestazione</th>
                        <th>Data</th>
                        <th>Orario</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="prenotazioniTableBody">
                    <?php if (!empty($prenotazioni)): ?>
                        <?php foreach ($prenotazioni as $prenotazione): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($prenotazione['Nome_Visitatore']); ?></td>
                                <td><?php echo htmlspecialchars($prenotazione['Cognome_Visitatore']); ?></td>
                                <td><?php echo htmlspecialchars($prenotazione['Email']); ?></td>
                                <td><?php echo htmlspecialchars($prenotazione['Nome_Manifestazione']); ?></td>
                                <td><?php echo htmlspecialchars($prenotazione['Data_Turno']); ?></td>
                                <td><?php echo htmlspecialchars($prenotazione['Ora_Turno']); ?></td>
                                <td>
                                    <button type="button" class="button button-primary button-sm btn-delete" 
                                            data-id-utente="<?php echo htmlspecialchars($prenotazione['Id_Utente']); ?>"
                                            data-id-turno="<?php echo htmlspecialchars($prenotazione['Id_Turno']); ?>">
                                        Elimina
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">Nessuna prenotazione trovata.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function() {
    $('.btn-delete').on('click', function() {
        const idUtente = $(this).data('id-utente');
        const idTurno = $(this).data('id-turno');
        const row = $(this).closest('tr');
        
        if (confirm('Sei sicuro di voler cancellare questa prenotazione?')) {
            $.ajax({
                url: '',
                method: 'POST',
                data: { 
                    idUtente: idUtente,
                    idTurno: idTurno
                },
                success: function(response) {
                    try {
                        const data = typeof response === "string" ? JSON.parse(response) : response;
                        
                        if (data.success) {
                            // Rimuovi la riga dalla tabella con un'animazione fadeOut
                            row.fadeOut(400, function() {
                                $(this).remove();
                                // Se non ci sono più righe, mostra il messaggio "Nessuna prenotazione trovata"
                                if ($('#prenotazioniTableBody tr').length === 0) {
                                    $('#prenotazioniTableBody').html('<tr><td colspan="7">Nessuna prenotazione trovata.</td></tr>');
                                }
                            });
                        }
                        
                        // Mostra il messaggio di successo/errore
                        $('#form-message').html(
                            `<p style="color: ${data.success ? 'rgb(74, 196, 207)' : 'red'};">${data.message}</p>`
                        );
                        
                        // Rimuovi il messaggio dopo 3 secondi
                        setTimeout(() => {
                            $('#form-message').fadeOut(400, function() {
                                $(this).empty().show();
                            });
                        }, 3000);
                        
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
        }
    });
});
</script>

<?php
include_once '../../template_footer.php';
?>