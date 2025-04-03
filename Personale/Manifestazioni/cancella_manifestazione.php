<?php
include_once '../../config.php';
include_once '../../queries.php';
include_once '../../template_header.php';

// Gestione della cancellazione tramite POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && is_numeric($_POST['id'])) {
    $idManifestazione = intval($_POST['id']);
    try {
        // Cancella l'area dal database
        if (deleteManifestazione($pdo, $idManifestazione)) {
            $successMessage = "Manifestazione cancellata con successo.";
        } else {
            $errorMessage = "Errore durante la cancellazione della manifestazione.";
        }
    } catch (PDOException $e) {
        $errorMessage = "Errore di connessione al database: " . $e->getMessage();
    }
}

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
        <?php if (!empty($successMessage)): ?>
            <p style="color: green;"><?php echo htmlspecialchars($successMessage); ?></p>
        <?php endif; ?>
        <?php if (!empty($errorMessage)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($errorMessage); ?></p>
        <?php endif; ?>

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
                <tbody>
                    <?php if (!empty($manifestazioni)): ?>
                        <?php foreach ($manifestazioni as $manifestazione): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($manifestazione['Nome']); ?></td>
                                <td><?php echo htmlspecialchars($manifestazione['Luogo']); ?></td>
                                <td><?php echo htmlspecialchars($manifestazione['Data']); ?></td>
                                <td><?php echo htmlspecialchars($manifestazione['Durata']); ?></td>
                                <td>
                                    <!-- Modulo per la cancellazione -->
                                    <form method="post" action="cancella_manifestazione.php" onsubmit="return confirm('Sei sicuro di voler cancellare questa manifestazione?');">
                                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($manifestazione['Id_Manifestazione']); ?>">
                                        <button type="submit" class="button button-primary button-sm">Cancella</button>
                                    </form>
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

<?php 
include_once '../../template_footer.php';
?>