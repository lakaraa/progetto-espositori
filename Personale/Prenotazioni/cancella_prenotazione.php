<?php
include_once '../../config.php';
include_once '../../queries.php';
include_once '../../template_header.php';

// Gestione della cancellazione tramite POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idUtente'])&& isset($_POST['idTurno']) && is_numeric($_POST['idUtente']) && is_numeric($_POST['idTurno'])) {
    $idUtente = intval($_POST['idUtente']);
    $idTurno = intval($_POST['idTurno']);
    try {
        // Cancella la prenotazione dal database
        if (deletePrenotazione($pdo, $idUtente, $idTurno)) {
            $successMessage = "Prenotazione cancellata con successo.";
            $successStyle = "color: rgb(74, 196, 207);";
        } else {
            $errorMessage = "Errore durante la cancellazione della prenotazione.";
            $errorStyle = "color: red;";
        }
    } catch (PDOException $e) {
        $errorMessage = "Errore di connessione al database: " . $e->getMessage();
    }
}

//Recupera le prenotazioni dal database tramite la funzione getPrenotazioni
$prenotazioni = getPrenotazioni($pdo);
?>
<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(/progetto-espositori/resources/images/bg-breadcrumbs-07-1920x480.jpg);">
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
        <h2>Elimina  Prenotazione</h2>
        <p>Seleziona una prenotazione dalla lista sottostante per eliminarla.</p>
        <br>

        <!-- Messaggi di successo o errore -->
        <?php if (!empty($successMessage)): ?>
            <p style="<?php echo $successStyle; ?>"><?php echo htmlspecialchars($successMessage); ?></p>
        <?php endif; ?>
        <?php if (!empty($errorMessage)): ?>
            <p style="<?php echo $errorStyle; ?>"><?php echo htmlspecialchars($errorMessage); ?></p>
        <?php endif; ?>

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
                <tbody>
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
                                    <form method="post" action="cancella_prenotazione.php" onsubmit="return confirm('Sei sicuro di voler cancellare questa prenotazione?');">
                                        <input type="hidden" name="idUtente" value="<?php echo htmlspecialchars($prenotazione['Id_Utente']); ?>">
                                        <input type="hidden" name="idTurno" value="<?php echo htmlspecialchars($prenotazione['Id_Turno']); ?>">
                                        <button type="submit" class="button button-primary button-sm">Elimina</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">Nessuna prenotazione trovata.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
<?php
include_once '../../template_footer.php';
?>