<?php
include_once '../../config.php';
include_once '../../queries.php';

// Gestione della cancellazione tramite POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && is_numeric($_POST['id'])) {
    header('Content-Type: application/json');
    ob_clean(); // Pulisce il buffer di output
    $idManifestazione = intval($_POST['id']);
    try {
        // Cancella la manifestazione dal database
        if (deleteManifestazione($pdo, $idManifestazione)) {
            echo json_encode(['success' => true, 'message' => 'Manifestazione cancellata con successo.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Errore durante la cancellazione della manifestazione.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Errore di connessione al database: ' . $e->getMessage()]);
    }
    exit;
}

// Solo se non è una richiesta POST, includi il template
include_once '../../template_header.php';

// Recupera tutte le manifestazioni dal database
$manifestazioni = getManifestazioni($pdo);
?>

<!-- Breadcrumbs -->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(/progetto-espositori/resources/images/bg-breadcrumbs-07-1920x480.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Cancella Manifestazioni</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="../dashboard_personale.php">Dashboard</a></li>
        <li><a href="gestisci_manifestazione.php">Gestione Manifestazioni</a></li>
        <li class="active">Cancella Manifestazioni</li>
    </ul>
</section>

<!-- Main Content -->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Cancella Manifestazioni</h2>
        <p>Seleziona una manifestazione dalla lista sottostante per Cancellarla.</p>
        <br>

        <!-- Messaggi di successo o errore -->
        <div id="form-message"></div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Luogo</th>
                        <th>Data</th>
                        <th>Durata (giorni)</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="manifestazioniTableBody">
                    <?php if (!empty($manifestazioni)): ?>
                        <?php foreach ($manifestazioni as $manifestazione): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($manifestazione['Nome']); ?></td>
                                <td><?php echo htmlspecialchars($manifestazione['Luogo']); ?></td>
                                <td><?php echo htmlspecialchars($manifestazione['Data']); ?></td>
                                <td><?php echo htmlspecialchars($manifestazione['Durata']); ?></td>
                                <td>
                                    <button type="button" class="button button-primary button-sm btn-delete" 
                                            data-id="<?php echo htmlspecialchars($manifestazione['Id_Manifestazione']); ?>">
                                        Cancella
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">Nessuna manifestazione trovata.</td>
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
        const id = $(this).data('id');
        const row = $(this).closest('tr');
        
        if (confirm('Sei sicuro di voler cancellare questa manifestazione?')) {
            $.ajax({
                url: '',
                method: 'POST',
                data: { id: id },
                success: function(response) {
                    try {
                        const data = typeof response === "string" ? JSON.parse(response) : response;
                        
                        if (data.success) {
                            // Rimuovi la riga dalla tabella con un'animazione fadeOut
                            row.fadeOut(400, function() {
                                $(this).remove();
                                // Se non ci sono più righe, mostra il messaggio "Nessuna manifestazione trovata"
                                if ($('#manifestazioniTableBody tr').length === 0) {
                                    $('#manifestazioniTableBody').html('<tr><td colspan="5">Nessuna manifestazione trovata.</td></tr>');
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