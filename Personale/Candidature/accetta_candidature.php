<?php
include_once("../../config.php");
include_once("../../queries.php");
include_once("../../session.php");

// Recupera le candidature in approvazione
$manifestazione = $_GET['manifestazione'] ?? '';
$candidature = getCandidatureInApprovazione($pdo, $manifestazione); // Funzione per ottenere le candidature filtrate

// Verifica se la richiesta è POST per accettare o rifiutare una candidatura
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', '../../error_log.txt'); // Percorso del file log

    // Pulizia buffer per evitare output HTML
    ob_start();
    header('Content-Type: application/json');
    ob_clean();

    $idContributo = $_POST['Id_Contributo'] ?? '';
    $azione = $_POST['Azione'] ?? '';

    if (empty($idContributo) || !in_array($azione, ['Accettato', 'Rifiutato'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Dati non validi.'
        ]);
        exit;
    }

    try {
        $result = aggiornaStatoCandidatura($pdo, $idContributo, $azione);
        echo json_encode([
            'success' => $result,
            'message' => $result ? "Candidatura $azione con successo!" : 'Errore durante l\'aggiornamento dello stato della candidatura.'
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

<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(/progetto-espositori/resources/images/bg-breadcrumbs-07-1920x480.jpg);">
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
                        <th>ID</th>
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
                                <td><?php echo htmlspecialchars($candidatura['Id_Contributo']); ?></td>
                                <td><?php echo htmlspecialchars($candidatura['Nome_Utente']); ?></td>
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
                    $('#form-message').html('<p style="color: rgb(74, 196, 207);">Errore nel caricamento dei dati.</p>');
                }
            },
            error: function(xhr, status, error) {
                console.error("Errore AJAX:", status, error);
                $('#form-message').html('<p style="color: rgb(74, 196, 207);">Errore di comunicazione con il server.</p>');
            }
        });
    });

    // Funzione per aggiornare la tabella
    function updateTable(candidature) {
        const tbody = $('#candidatureTableBody');
        tbody.empty();

        if (candidature.length === 0) {
            tbody.html('<tr><td colspan="6">Nessuna candidatura trovata.</td></tr>');
            return;
        }

        candidature.forEach(function(candidatura) {
            const row = `
                <tr>
                    <td>${candidatura.Id_Contributo}</td>
                    <td>${candidatura.Nome_Utente}</td>
                    <td>${candidatura.Manifestazione}</td>
                    <td>${candidatura.Titolo}</td>
                    <td>${candidatura.Sintesi}</td>
                    <td>
                        <button class="button-accept btn-azione" data-id="${candidatura.Id_Contributo}" data-azione="Accettato">Accetta</button>
                        <button class="button-reject btn-azione" data-id="${candidatura.Id_Contributo}" data-azione="Rifiutato">Rifiuta</button>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });

        // Reattach event handlers to new buttons
        attachActionHandlers();
    }

    // Funzione per gestire le azioni (accetta/rifiuta)
    function attachActionHandlers() {
        $('.btn-azione').off('click').on('click', function () {
            const idContributo = $(this).data('id');
            const azione = $(this).data('azione');

            if (confirm(`Sei sicuro di voler ${azione.toLowerCase()} questa candidatura?`)) {
                $.ajax({
                    url: '',
                    method: 'POST',
                    data: { Id_Contributo: idContributo, Azione: azione },
                    success: function (response) {
                        try {
                            const data = typeof response === "string" ? JSON.parse(response) : response;
                            const message = data.message || "Messaggio non disponibile.";
                            const isSuccess = data.success === true;

                            $('#form-message').html(
                                `<p style="color: ${isSuccess ? 'green' : 'red'};">${message}</p>`
                            );

                            if (isSuccess) {
                                // Ricarica i dati della tabella invece di ricaricare la pagina
                                $('#searchForm').trigger('submit');
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
    }

    // Attach initial handlers
    attachActionHandlers();
});
</script>

<?php
include_once("../../template_footer.php");
?>