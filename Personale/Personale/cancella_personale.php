<?php
include_once '../../config.php';
include_once '../../queries.php';

// Gestione della cancellazione tramite POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && is_numeric($_POST['id'])) {
    header('Content-Type: application/json');
    ob_clean(); // Pulisce il buffer di output
    $idUtente = intval($_POST['id']);
    try {
        // Cancella il personale dal database
        if (deletePersonale($pdo, $idUtente)) {
            echo json_encode(['success' => true, 'message' => 'Personale cancellato con successo.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Errore durante la cancellazione del personale.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Errore di connessione al database: ' . $e->getMessage()]);
    }
    exit;
}

// Solo se non è una richiesta POST, includi il template
include_once '../../template_header.php';

// Recupera tutto il personale dal database
$personale = getPersonale($pdo);
?>

<!-- Breadcrumbs -->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(../../resources/images/sfondo.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Cancella Personale</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="../dashboard_personale.php">Dashboard</a></li>
        <li><a href="gestisci_personale.php">Gestione Personale</a></li>
        <li class="active">Cancella Personale</li>
    </ul>
</section>

<!-- Main Content -->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Cancella Personale</h2>
        <p>Seleziona un membro del personale dalla lista sottostante per cancellarlo.</p>

        <!-- Messaggi di successo o errore -->
        <div id="form-message"></div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Nome</th>
                        <th>Cognome</th>
                        <th>Email</th>
                        <th>Telefono</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="personaleTableBody">
                    <?php if (!empty($personale)): ?>
                        <?php foreach ($personale as $p): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($p['Username']); ?></td>
                                <td><?php echo htmlspecialchars($p['Nome']); ?></td>
                                <td><?php echo htmlspecialchars($p['Cognome']); ?></td>
                                <td><?php echo htmlspecialchars($p['Email']); ?></td>
                                <td><?php echo htmlspecialchars($p['Telefono']); ?></td>
                                <td>
                                    <button type="button" class="button button-primary button-sm btn-delete" 
                                            data-id="<?php echo htmlspecialchars($p['Id_Utente']); ?>">
                                        Cancella
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">Nessun membro del personale trovato.</td>
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
        
        if (confirm('Sei sicuro di voler cancellare questo membro del personale?')) {
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
                                // Se non ci sono più righe, mostra il messaggio "Nessun membro del personale trovato"
                                if ($('#personaleTableBody tr').length === 0) {
                                    $('#personaleTableBody').html('<tr><td colspan="6">Nessun membro del personale trovato.</td></tr>');
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
