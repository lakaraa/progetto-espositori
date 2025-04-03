<?php
include_once '../../config.php';
include_once '../../queries.php';
include_once '../../template_header.php';

// Gestione della cancellazione tramite POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && is_numeric($_POST['id'])) {
    $idPersonale = intval($_POST['id']);
    try {
        // Cancella il personale dal database
        if (deletePersonale($pdo, $idPersonale)) {
            $successMessage = "Personale cancellato con successo.";
        } else {
            $errorMessage = "Errore durante la cancellazione del personale.";
        }
    } catch (PDOException $e) {
        $errorMessage = "Errore di connessione al database: " . $e->getMessage();
    }
}

// Recupera tutte le informazioni del personale dal database
$personale = getPersonale($pdo);
?>

<!-- Breadcrumbs -->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(/progetto-espositori/resources/images/bg-breadcrumbs-07-1920x480.jpg);">
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
                        <th>Cognome</th>
                        <th>Email</th>
                        <th>Telefono</th>
                        <th>Ruolo</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($personale)): ?>
                        <?php foreach ($personale as $membro): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($membro['Nome']); ?></td>
                                <td><?php echo htmlspecialchars($membro['Cognome']); ?></td>
                                <td><?php echo htmlspecialchars($membro['Email']); ?></td>
                                <td><?php echo htmlspecialchars($membro['Telefono']); ?></td>
                                <td><?php echo htmlspecialchars($membro['Ruolo']); ?></td>
                                <td>
                                    <!-- Modulo per la cancellazione -->
                                    <form method="post" action="cancella_personale.php" onsubmit="return confirm('Sei sicuro di voler cancellare questo membro del personale?');">
                                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($membro['Id_Utente']); ?>">
                                        <button type="submit" class="button button-primary button-sm">Cancella</button>
                                    </form>
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

<?php 
include_once '../../template_footer.php';
?>
