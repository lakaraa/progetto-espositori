<?php
include_once '../../config.php';
include_once '../../queries.php';
include_once '../../template_header.php';

// Gestione della cancellazione tramite POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && is_numeric($_POST['id'])) {
    $idCategoria = intval($_POST['id']);
    try {
        // Cancella la categoria dal database
        if (deleteCategoria($pdo, $idCategoria)) {
            $successMessage = "Categoria cancellata con successo.";
            $successStyle = "color: rgb(74, 196, 207);";
        } else {
            $errorMessage = "Errore durante la cancellazione della categoria.";
            $errorStyle = "color: red;";
        }
    } catch (PDOException $e) {
        $errorMessage = "Errore di connessione al database: " . $e->getMessage();
    }
}

// Recupera tutte le categorie dal database
$categorie = getCategorie($pdo);
?>

<!-- Breadcrumbs -->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(/progetto-espositori/resources/images/bg-breadcrumbs-07-1920x480.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Cancella Categoria</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="../dashboard_personale.php">Dashboard</a></li>
        <li><a href="/progetto-espositori/Personale/Categorie/gestisci_categorie.php">Gestione Categorie</a></li>
        <li class="active">Cancella Categoria</li>
    </ul>
</section>

<!-- Main Content -->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Cancella Categoria</h2>
        <p>Seleziona una categoria dalla lista sottostante per cancellarla.</p>

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
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Descrizione</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($categorie)): ?>
                        <?php foreach ($categorie as $categoria): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($categoria['Id_Categoria']); ?></td>
                                <td><?php echo htmlspecialchars($categoria['Nome']); ?></td>
                                <td><?php echo htmlspecialchars($categoria['Descrizione']); ?></td>
                                <td>
                                    <!-- Modulo per la cancellazione -->
                                    <form method="post" action="cancella_categoria.php" onsubmit="return confirm('Sei sicuro di voler cancellare questa categoria?');">
                                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($categoria['Id_Categoria']); ?>">
                                        <button type="submit" class="button button-primary button-sm">Cancella</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">Nessuna categoria trovata.</td>
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