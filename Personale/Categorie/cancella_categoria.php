<?php
error_reporting(E_ALL); // Report all errors and warnings
ini_set('display_errors', 1); // Display errors on the screen

include_once '../../config.php';
include_once '../../queries.php';

// Gestione della cancellazione tramite POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && is_numeric($_POST['id'])) {
    header('Content-Type: application/json');
    $idCategoria = intval($_POST['id']);
    try {
        // Cancella la categoria dal database
        if (deleteCategoria($pdo, $idCategoria)) {
            echo json_encode(['success' => true, 'message' => 'Categoria cancellata con successo.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Errore durante la cancellazione della categoria.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Errore di connessione al database: ' . $e->getMessage()]);
    }
    exit;
}

// Solo se non è una richiesta POST, includi il template
include_once '../../template_header.php';

// Recupera tutte le categorie dal database
$categorie = getCategorie($pdo);
?>


<!-- Breadcrumbs -->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(../../resources/images/sfondo.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Cancella Categoria</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="../dashboard_personale.php">Dashboard</a></li>
        <li><a href="gestisci_categorie.php">Gestione Categorie</a></li>
        <li class="active">Cancella Categoria</li>
    </ul>
</section>

<!-- Main Content -->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Cancella Categoria</h2>
        <p>Seleziona una categoria dalla lista sottostante per cancellarla.</p>

        <!-- Messaggi di successo o errore -->
        <div id="form-message"></div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Descrizione</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="categorieTableBody">
                    <?php if (!empty($categorie)): ?>
                        <?php foreach ($categorie as $categoria): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($categoria['Nome']); ?></td>
                                <td><?php echo htmlspecialchars($categoria['Descrizione']); ?></td>
                                <td>
                                    <button type="button" class="button button-primary button-sm btn-delete" 
                                            data-id="<?php echo htmlspecialchars($categoria['Id_Categoria']); ?>">
                                        Cancella
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">Nessuna categoria trovata.</td>
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
        
        if (confirm('Sei sicuro di voler cancellare questa categoria?')) {
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
                                // Se non ci sono più righe, mostra il messaggio "Nessuna categoria trovata"
                                if ($('#categorieTableBody tr').length === 0) {
                                    $('#categorieTableBody').html('<tr><td colspan="3">Nessuna categoria trovata.</td></tr>');
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
                        $('#form-message').html('<p style="color: rgb(74, 196, 207);">Errore nel caricamento dei dati.</p>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Errore AJAX:", status, error);
                    $('#form-message').html('<p style="color: rgb(74, 196, 207);">Errore di comunicazione con il server.</p>');
                }
            });
        }
    });
});
</script>

<?php 
include_once '../../template_footer.php';
?>