<?php
include_once("../../config.php");
include_once("../../queries.php");
include_once("../../session.php");
include_once("../../template_header.php");

// Gestione della cancellazione tramite POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && is_numeric($_POST['id'])) {
    $idEspositore = intval($_POST['id']);
    try {
        // Cancella l'espositore dal database
        if (deleteEspositore($pdo, $idEspositore)) {
            $successMessage = "Espositore cancellato con successo.";
            $successStyle = "color: rgb(74, 196, 207);";
        } else {
            $errorMessage = "Errore durante la cancellazione dell'espositore.";
            $errorStyle = "color: red;";
        }
    } catch (PDOException $e) {
        $errorMessage = "Errore di connessione al database: " . $e->getMessage();
    }
}

// Recupera tutti gli espositori dal database
$espositori = getEspositori($pdo);
?>
<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(images/bg-breadcrumbs-07-1920x480.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Elimina Espositore</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="../dashboard_personale.php">Dashboard</a></li>
        <li><a href="gestisci_espositori.php">Gestione Espositori</a></li>
        <li class="active">Elimina Espositore</li>
    </ul>
</section>
<!-- Main Content-->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Elimina Espositore</h2>
        <p>Seleziona un Espositore dalla lista sottostante per eliminarlo.</p>

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
                        <th>Username</th>
                        <th>Nome</th>
                        <th>Cognome</th>
                        <th>Email</th>
                        <th>Telefono</th>
                        <th>Qualifica</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($espositori as $espositore): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($espositore['username']); ?></td>
                            <td><?php echo htmlspecialchars($espositore['nome']); ?></td>
                            <td><?php echo htmlspecialchars($espositore['cognome']); ?></td>
                            <td><?php echo htmlspecialchars($espositore['email']); ?></td>
                            <td><?php echo htmlspecialchars($espositore['telefono']); ?></td>
                            <td><?php echo htmlspecialchars($espositore['qualifica']); ?></td>
                            <td>
                                <form method="POST" action="cancella_espositore.php" onsubmit="return confirm('Sei sicuro di voler eliminare questo Espositore?');">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($espositore['id']); ?>">
                                    <button type="submit" class="button button-primary button-sm">Elimina</button>
                                </form>
                            </td>
                            <td>
                                <a class="button button-danger button-sm"
                                    href="visualizza_cv.php?id=<?php echo urlencode($espositore['id']); ?>"
                                    target="_blank">
                                    Visualizza CV
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($espositori)): ?>
                        <tr>
                            <td colspan="9">Nessun espositore trovato.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
<?php
include_once("../../template_footer.php");
?>