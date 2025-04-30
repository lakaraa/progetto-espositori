<?php
include_once '../../config.php';
include_once '../../template_header.php';

$successMessage = '';
$errorMessage = '';

// Recupera l'ID della candidatura dalla query string
$idCandidatura = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($idCandidatura <= 0) {
    die("ID candidatura non valido.");
}

// Recupera i dettagli della candidatura
$stmt = $pdo->prepare("SELECT * FROM Contributo WHERE Id_Contributo = :idCandidatura");
$stmt->bindParam(':idCandidatura', $idCandidatura, PDO::PARAM_INT);
$stmt->execute();
$candidatura = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$candidatura) {
    die("Candidatura non trovata.");
}

// Gestisci l'invio del modulo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titolo = $_POST['Titolo'] ?? '';
    $sintesi = $_POST['Sintesi'] ?? '';
    $url = $_POST['URL'] ?? '';
    $accettazione = $_POST['Accettazione'] ?? '';
    $immagine = null;

    // Gestione immagine se presente
    if (isset($_FILES['Immagine']) && $_FILES['Immagine']['error'] === UPLOAD_ERR_OK) {
        $immagineTmpPath = $_FILES['Immagine']['tmp_name'];
        $immagineName = "img_" . $idCandidatura . "_" . basename($_FILES['Immagine']['name']);
        $immagineUploadPath = "../../uploads/img/" . $immagineName;

        // Crea cartella uploads se non esiste
        if (!is_dir('../../uploads/img/')) {
            mkdir('../../uploads/img/', 0777, true);
        }

        // Salva il file nella cartella uploads
        if (move_uploaded_file($immagineTmpPath, $immagineUploadPath)) {
            $immagine = $immagineName;
        } else {
            $errorMessage .= " Errore nel salvataggio dell'immagine.";
        }
    }

    // Controlla se almeno un campo è stato modificato
    $modificheEffettuate = $titolo !== $candidatura['Titolo'] || $sintesi !== $candidatura['Sintesi'] || 
                           $url !== $candidatura['URL'] || $accettazione !== $candidatura['Accettazione'] || 
                           !empty($immagine);

    if (!$modificheEffettuate) {
        $errorMessage .= " Nessuna modifica rilevata.";
    }

    if (empty($errorMessage)) {
        try {
            // Costruisci query dinamica
            $sql = "UPDATE Contributo 
                    SET Titolo = :titolo, Sintesi = :sintesi, URL = :url, Accettazione = :accettazione";

            if (!empty($immagine)) {
                $sql .= ", Immagine = :immagine";
            }

            $sql .= " WHERE Id_Contributo = :idCandidatura";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':titolo', $titolo);
            $stmt->bindParam(':sintesi', $sintesi);
            $stmt->bindParam(':url', $url);
            $stmt->bindParam(':accettazione', $accettazione);
            $stmt->bindParam(':idCandidatura', $idCandidatura, PDO::PARAM_INT);

            if (!empty($immagine)) {
                $stmt->bindParam(':immagine', $immagine);
            }

            // Esegui l'aggiornamento
            if ($stmt->execute()) {
                $successMessage = "Dati aggiornati con successo.";
            } else {
                $errorMessage = "Errore nell'aggiornamento dei dati.";
            }
        } catch (Exception $e) {
            $errorMessage = "Errore: " . $e->getMessage();
        }
    }
}
?>

<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(images/bg-breadcrumbs-07-1920x480.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Modifica Dettagli Candidatura</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="../dashboard_personale.php">Dashboard</a></li>
        <li><a href="gestisci_candidature.php">Gestione Candidature</a></li>
        <li class="active">Modifica Dettagli Candidatura</li>
    </ul>
</section>

<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Modifica Candidatura</h2>
        <p>Compila il modulo sottostante per modificare i dettagli della candidatura.</p>

        <?php if (!empty($successMessage)): ?>
            <p style="color: rgb(74, 196, 207);"><?php echo htmlspecialchars($successMessage); ?></p>
        <?php endif; ?>
        <?php if (!empty($errorMessage)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($errorMessage); ?></p>
        <?php endif; ?>

        <form class="rd-form rd-mailform" method="post" action="" enctype="multipart/form-data">
            <div class="row row-50">
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="candidatura-titolo">Titolo</label>
                        <input class="form-input" id="candidatura-titolo" type="text" name="Titolo" value="<?php echo htmlspecialchars($candidatura['Titolo']); ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="candidatura-sintesi">Sintesi</label>
                        <textarea class="form-input" id="candidatura-sintesi" name="Sintesi"><?php echo htmlspecialchars($candidatura['Sintesi']); ?></textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="candidatura-url">URL</label>
                        <input class="form-input" id="candidatura-url" type="text" name="URL" value="<?php echo htmlspecialchars($candidatura['URL']); ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="candidatura-accettazione">Accettazione</label>
                        <select class="form-input" id="candidatura-accettazione" name="Accettazione">
                            <option value="In Approvazione" <?php echo $candidatura['Accettazione'] === 'In Approvazione' ? 'selected' : ''; ?>>In Approvazione</option>
                            <option value="Accettato" <?php echo $candidatura['Accettazione'] === 'Accettato' ? 'selected' : ''; ?>>Accettato</option>
                            <option value="Rifiutato" <?php echo $candidatura['Accettazione'] === 'Rifiutato' ? 'selected' : ''; ?>>Rifiutato</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-wrap">
                        <label class="form-label" for="candidatura-immagine">Immagine</label> <br>
                        <input class="form-input" id="candidatura-immagine" type="file" name="Immagine" accept="image/*">
                    </div>
                </div>
            </div>
            <div class="col-md-12 text-center">
                <button class="button button-primary button-lg" type="submit">Modifica</button>
            </div>
        </form>
    </div>
</section>

<?php
include_once '../../template_footer.php';
?>