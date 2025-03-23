<?php
include_once("../../config.php");
include_once("../../queries.php");
include_once("../../session.php");
include_once("../../template_header.php");

// Recupera le manifestazioni dal database
$manifestazioni = getManifestazioni($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recupera i dati dal form
    $idManifestazione = $_POST['manifestazione'];
    $nome = $_POST['nome'];
    $capienza = $_POST['capienza'];
    $descrizione = $_POST['descrizione'];

    // Controlla che tutti i campi siano compilati
    if (empty($idManifestazione) || empty($nome) || empty($capienza) || empty($descrizione)) {
        echo "<p style='color: red;'>Tutti i campi sono obbligatori.</p>";
    } else {
        // Aggiungi l'area al database
        try {
            $result = addArea($pdo, $nome, $descrizione, $capienza, $idManifestazione);

            if ($result) {
                header("Location: gestisci_aree.php?success=1");
                exit;
            } else {
                echo "<p style='color: red;'>Errore durante l'aggiunta dell'area.</p>";
            }
        } catch (PDOException $e) {
            echo "<p style='color: red;'>Errore di connessione al database: " . $e->getMessage() . "</p>";
        }
    }
}
?>

<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(/progetto-espositori/resources/images/bg-breadcrumbs-07-1920x480.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Crea Aree</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="../dashboard_personale.php">Dashboard</a></li>
        <li><a href="gestisci_aree.php">Gestione Aree</a></li>
        <li class="active">Crea Area</li>
    </ul>
</section>
<!-- Main Content-->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Crea Aree</h2>
        <p>Compila il modulo sottostante per creare le aree.</p>
        <form class="rd-form rd-mailform" data-form-output="form-output-global" data-form-type="contact" method="post" action="">
            <div class="row row-50">
                <div class="col-md-6">
                    <div class="form-wrap">
                    <select class="form-input" id="area-manifestazione" name="manifestazione" data-constraints="@Required">
                        <option value="">Seleziona Manifestazione</option>
                            <?php foreach ($manifestazioni as $manifestazione): ?>
                                <option value="<?php echo htmlspecialchars($manifestazione['Id_Manifestazione']); ?>">
                                    <?php echo htmlspecialchars($manifestazione['Nome']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="area-nome">Nome</label>
                        <input class="form-input" id="area-nome" type="text" name="nome" data-constraints="@Required">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="area-capienza" style="display: block;" >Capienza Massima</label><br>
                        <input class="form-input" id="area-capienza" type="number" name="capienza" data-constraints="@Required">
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-wrap">
                        <label class="form-label" for="area-descrizione">Descrizione</label>
                        <textarea class="form-input" id="area-descrizione" name="descrizione" data-constraints="@Required"></textarea>
                    </div>
                </div>
            </div>
            <button class="button button-primary button-lg" type="submit">Crea</button>
        </form>
    </div>
</section>
<?php
include_once("../../template_footer.php");
?>