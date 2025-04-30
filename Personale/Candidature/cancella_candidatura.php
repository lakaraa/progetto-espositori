<?php
include_once("../../config.php");
include_once("../../queries.php");
include_once("../../session.php");

// Recupera tutte le candidature dalla tabella Contributo
$candidature = getCandidature($pdo); // Funzione per ottenere tutte le candidature

// Verifica se la richiesta è POST per eliminare una candidatura
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', '../../error_log.txt'); // Percorso del file log

    // Pulizia buffer per evitare output HTML
    ob_start();
    header('Content-Type: application/json');
    ob_clean();

    $idContributo = $_POST['Id_Contributo'] ?? '';

    if (empty($idContributo)) {
        echo json_encode([
            'success' => false,
            'message' => 'ID Contributo non fornito.'
        ]);
        exit;
    }

    try {
        $result = deleteContributo($pdo, $idContributo);
        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Candidatura eliminata con successo!' : 'Errore durante l\'eliminazione della candidatura.'
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

<style>
.button-custom {
    background-color: rgb(74, 196, 207);
    border: none;
    color: white;
    padding: 10px 20px;
    font-size: 16px;
    border-radius: 25px; /* Arrotonda i bordi */
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Aggiunge un'ombra */
}

.button-custom:hover {
    background-color: rgb(60, 170, 180); /* Colore più scuro al passaggio del mouse */
    transform: scale(1.05); /* Leggero ingrandimento */
}

.button-custom:active {
    background-color: rgb(50, 150, 160); /* Colore più scuro al clic */
    transform: scale(0.95); /* Leggero rimpicciolimento */
}
</style>

<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(/progetto-espositori/resources/images/bg-breadcrumbs-07-1920x480.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Cancella Candidatura</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="../dashboard_personale.php">Dashboard</a></li>
        <li><a href="gestisci_candidature.php">Gestione Candidature</a></li>
        <li class="active">Cancella Candidatura</li>
    </ul>
</section>

<!-- Main Content-->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Cancella Candidatura</h2>
        <p>Seleziona una candidatura da eliminare.</p>

        <br>

        <!-- Output del messaggio -->
        <div id="form-message"></div>

        <!-- Tabella delle candidature -->
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Utente</th>
                        <th>Titolo</th>
                        <th>Sintesi</th>
                        <th>Accettazione</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($candidature)): ?>
                        <?php foreach ($candidature as $candidatura): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($candidatura['Id_Contributo']); ?></td>
                                <td><?php echo htmlspecialchars($candidatura['Nome_Utente']); ?></td>
                                <td><?php echo htmlspecialchars($candidatura['Titolo']); ?></td>
                                <td><?php echo htmlspecialchars($candidatura['Sintesi']); ?></td>
                                <td><?php echo htmlspecialchars($candidatura['Accettazione']); ?></td>
                                <td>
                                <button class="button-custom" 
                                        data-id="<?php echo htmlspecialchars($candidatura['Id_Contributo']); ?>">
                                    Cancella
                                </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">Nessuna candidatura trovata.</td>
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
    $('.button-custom').on('click', function () {
        const idContributo = $(this).data('id');

        if (confirm('Sei sicuro di voler eliminare questa candidatura?')) {
            $.ajax({
                url: '', // stessa pagina
                method: 'POST',
                data: { Id_Contributo: idContributo },
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
                            location.reload(); // Ricarica la pagina per aggiornare la tabella
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
        }
    });
});
</script>

<?php
include_once("../../template_footer.php");
?>