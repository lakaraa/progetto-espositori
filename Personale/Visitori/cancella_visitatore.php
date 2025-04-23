<?php
include_once '../../config.php';
include_once '../../queries.php';
include_once '../../template_header.php';

// Gestione della cancellazione tramite POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && is_numeric($_POST['id'])) {
    $idVisitatore = intval($_POST['id']);
    try {
        // Cancella il visitatore dal database
        if (deleteVisitatore($pdo, $idVisitatore)) {
            $successMessage = "Visitatore cancellato con successo.";
            $successStyle = "color: rgb(74, 196, 207);";
        } else {
            $errorMessage = "Errore durante la cancellazione del visitatore.";
            $errorStyle = "color: red;";
        }
    } catch (PDOException $e) {
        $errorMessage = "Errore di connessione al database: " . $e->getMessage();
    }
}

// Recupera tutti i visitatori dal database
$visitatori = getVisitatori($pdo);
?>

<!-- Breadcrumbs -->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(/progetto-espositori/resources/images/bg-breadcrumbs-07-1920x480.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Cancella Visitatore</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="../dashboard_personale.php">Dashboard</a></li>
        <li><a href="/progetto-espositori/Personale/Visitori/gestisci_visitatore.php">Gestione Visitatori</a></li>
        <li class="active">Cancella Visitatore</li>
    </ul>
</section>

<!-- Main Content -->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Cancella Visitatore</h2>
        <p>Seleziona un visitatore dalla lista sottostante per cancellarlo.</p>

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
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($visitatori)): ?>
                        <?php foreach ($visitatori as $visitatore): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($visitatore['Username']); ?></td>
                                <td><?php echo htmlspecialchars($visitatore['Nome']); ?></td>
                                <td><?php echo htmlspecialchars($visitatore['Cognome']); ?></td>
                                <td><?php echo htmlspecialchars($visitatore['Email']); ?></td>
                                <td><?php echo htmlspecialchars($visitatore['Telefono']); ?></td>
                                <td>
                                    <!-- Modulo per la cancellazione -->
                                    <form method="post" action="cancella_visitatore.php" onsubmit="return confirm('Sei sicuro di voler cancellare questo visitatore?');">
                                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($visitatore['Id_Utente']); ?>">
                                        <button type="submit" class="button button-primary button-sm">Cancella</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">Nessun visitatore trovato.</td>
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