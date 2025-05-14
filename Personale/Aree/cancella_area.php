<?php
include_once '../../config.php';
include_once '../../queries.php';
include_once '../../template_header.php';

// Gestione della cancellazione tramite POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && is_numeric($_POST['id'])) {
    $idArea = intval($_POST['id']);
    try {
        // Cancella l'area dal database
        if (deleteArea($pdo, $idArea)) {
            $successMessage = "Area cancellata con successo.";
            $successStyle = "color: rgb(74, 196, 207);";
        } else {
            $errorMessage = "Errore durante la cancellazione dell'area.";
            $errorStyle = "color: red;";
        }

    } catch (PDOException $e) {
        $errorMessage = "Errore di connessione al database: " . $e->getMessage();
    }
}

// Recupera tutte le aree dal database
$aree = getAree($pdo);
?>

<!-- Breadcrumbs -->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(/progetto-espositori/resources/images/sfondo.jpg);">
        <h2 class="breadcrumbs-custom-title">Cancella Area</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="../dashboard_personale.php">Dashboard</a></li>
        <li><a href="gestisci_aree.php">Gestione Aree</a></li>
        <li class="active">Cancella Area</li>
    </ul>
</section>

<!-- Main Content -->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Cancella Area</h2>
        <p>Seleziona una Area dalla lista sottostante per Cancellarla.</p>

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
                        <th>Manifestazione</th>
                        <th>Descrizione</th>
                        <th>Capienza Massima</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($aree)): ?>
                        <?php foreach ($aree as $area): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($area['nome']); ?></td>
                                <td><?php echo htmlspecialchars($area['manifestazione']); ?></td>
                                <td><?php echo htmlspecialchars($area['descrizione']); ?></td>
                                <td><?php echo htmlspecialchars($area['capienza_massima']); ?></td>
                                <td>
                                    <!-- Modulo per la cancellazione -->
                                    <form method="post" action="cancella_area.php" onsubmit="return confirm('Sei sicuro di voler cancellare questa area?');">
                                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($area['id']); ?>">
                                        <button type="submit" class="button button-primary button-sm">Cancella</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">Nessuna area trovata.</td>
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