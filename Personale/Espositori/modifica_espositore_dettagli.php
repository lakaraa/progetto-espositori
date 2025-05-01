<?php
include_once '../../config.php';
include_once '../../template_header.php';

$successMessage = '';
$errorMessage = '';

// Recupera l'ID dell'espositore dalla query string
$idEspositore = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($idEspositore <= 0) {
    die("ID espositore non valido.");
}

// Recupera i dettagli dell'espositore
$espositore = getEspositoreById($pdo, $idEspositore);

if (!$espositore) {
    die("Espositore non trovato.");
}

// Gestisci l'invio del modulo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['Nome'] ?? '';
    $cognome = $_POST['Cognome'] ?? '';
    $email = $_POST['Email'] ?? '';
    $telefono = $_POST['Telefono'] ?? '';
    $username = $_POST['Username'] ?? '';
    $password = $_POST['Password'] ?? '';
    $qualifica = $_POST['Qualifica'] ?? '';
    $cvData = null;

    // Controllo dello username (almeno 4 caratteri, solo lettere, numeri e underscore)
    if (!preg_match("/^[a-zA-Z0-9_]{4,}$/", $username)) {
        $errorMessage .= " Lo username deve essere lungo almeno 4 caratteri e può contenere solo lettere, numeri e underscore.";
    }

    // Gestione CV se presente
    if (isset($_FILES['CV']) && $_FILES['CV']['error'] === UPLOAD_ERR_OK) {
        $cvTmpPath = $_FILES['CV']['tmp_name'];
        $cvName = "cv_" . $username . ".pdf";
        $cvUploadPath = "../../uploads/" . $cvName;

        // Crea cartella uploads se non esiste
        if (!is_dir('../../uploads')) {
            mkdir('../../uploads', 0777, true);
        }

        // Salva il file nella cartella uploads
        if (move_uploaded_file($cvTmpPath, $cvUploadPath)) {
            // Leggi il contenuto del file (per il DB)
            $cvData = file_get_contents($cvUploadPath);
        } else {
            $errorMessage .= " Errore nel salvataggio del file PDF.";
        }
    }

    // Controlla se almeno un campo è stato modificato
    $modificheEffettuate = $nome !== $espositore['Nome'] || $cognome !== $espositore['Cognome'] || $email !== $espositore['Email'] ||
                            $telefono !== $espositore['Telefono'] || $username !== $espositore['Username'] || $qualifica !== $espositore['Qualifica'] || !empty($password) || !empty($cvData);

    if (!$modificheEffettuate) {
        $errorMessage .= " Nessuna modifica rilevata.";
    }

    if (empty($errorMessage)) {
        try {
            // Aggiorna i dati dell'espositore
            if (updateEspositoreDettagli($pdo, $idEspositore, $nome, $cognome, $email, $telefono, $username, $qualifica, $password, $cvData)) {
                $successMessage = "Dati aggiornati con successo.";

                // Rinomina il file del CV se esiste già e se lo username è stato cambiato
                if (!empty($cvData)) {
                    $successMessage .= " Curriculum aggiornato.";
                } else {
                    $cvOldName = "../../uploads/cv_" . $espositore['Username'] . ".pdf";
                    $cvNewName = "../../uploads/cv_" . $username . ".pdf";

                    if ($espositore['Username'] !== $username && file_exists($cvOldName)) {
                        if (rename($cvOldName, $cvNewName)) {
                            $cvDataRenamed = file_get_contents($cvNewName);
                            if (updateEspositoreDettagli($pdo, $idEspositore, $nome, $cognome, $email, $telefono, $username, $qualifica, null, $cvDataRenamed)) {
                                $successMessage .= " Curriculum aggiornato dopo la modifica dello username.";
                            } else {
                                $errorMessage .= " Errore durante l'aggiornamento del Curriculum.";
                            }
                        } else {
                            $errorMessage .= " Impossibile rinominare il file del CV.";
                        }
                    }
                }
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
        <h2 class="breadcrumbs-custom-title">Modifica Dettagli Espositore</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="../dashboard_personale.php">Dashboard</a></li>
        <li><a href="gestisci_espositori.php">Gestione Espositore</a></li>
        <li><a href="modifica_espositore.php">Modifica Espositore</a></li>
        <li class="active">Modifica Dettagli Espositore</li>
    </ul>
</section>

<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Modifica Espositore</h2>
        <p>Compila il modulo sottostante per modificare i dettagli dell'espositore.</p>

        <?php if (!empty($successMessage)): ?>
            <p style="color: rgb(74, 196, 207);"><?php echo htmlspecialchars($successMessage); ?></p>
        <?php endif; ?>
        <?php if (!empty($errorMessage)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($errorMessage); ?></p>
        <?php endif; ?>

        <form class="rd-form rd-mailform" method="post" id="form-modifica-espositore" action="" enctype="multipart/form-data">
            <div class="row row-50">
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="espositore-nome">Nome</label>
                        <input class="form-input" id="espositore-nome" type="text" name="Nome" value="<?php echo htmlspecialchars($espositore['Nome']); ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="espositore-cognome">Cognome</label>
                        <input class="form-input" id="espositore-cognome" type="text" name="Cognome" value="<?php echo htmlspecialchars($espositore['Cognome']); ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="espositore-email">Email</label>
                        <input class="form-input" id="espositore-email" type="email" name="Email" value="<?php echo htmlspecialchars($espositore['Email']); ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="espositore-telefono">Telefono</label>
                        <input class="form-input" id="espositore-telefono" type="text" name="Telefono" value="<?php echo htmlspecialchars($espositore['Telefono']); ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="espositore-username">Username</label>
                        <input class="form-input" id="espositore-username" type="text" name="Username" value="<?php echo htmlspecialchars($espositore['Username']); ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="espositore-password">Password</label>
                        <input class="form-input" id="espositore-password" type="password" name="Password" value="">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-wrap">
                        <label class="form-label" for="espositore-qualifica">Qualifica</label>
                        <input class="form-input" id="espositore-qualifica" type="text" name="Qualifica" value="<?php echo htmlspecialchars($espositore['Qualifica']); ?>">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-wrap">
                        <label class="form-label" for="espositore-cv">Curriculum Vitae (PDF)</label> <br>
                        <input class="form-input" id="espositore-cv" type="file" name="CV" accept="application/pdf">
                    </div>
                </div>
                <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['CV']) && $_FILES['CV']['error'] === UPLOAD_ERR_OK) {
                    $cvDir = '../../uploads/';
                    $cvPath = $cvDir . basename($_FILES['CV']['name']);
                    
                    // Verifica che la directory esista, altrimenti la crea
                    if (!is_dir($cvDir)) {
                        mkdir($cvDir, 0777, true);
                    }

                    // Sposta il file caricato nella directory specificata
                    if (move_uploaded_file($_FILES['CV']['tmp_name'], $cvPath)) {
                        try {
                            // Aggiorna il percorso del CV nel database
                            $sql = "UPDATE utente SET Curriculum = :cvPath WHERE Id_Utente = :idEspositore";
                            $stmt = $pdo->prepare($sql);
                            $stmt->bindParam(':cvPath', $cvPath, PDO::PARAM_STR);
                            $stmt->bindParam(':idEspositore', $idEspositore, PDO::PARAM_INT);

                            if ($stmt->execute()) {
                                $successMessage .= " Curriculum Vitae aggiornato con successo.";
                            } else {
                                $errorMessage .= " Errore durante l'aggiornamento del Curriculum Vitae.";
                            }
                        } catch (Exception $e) {
                            $errorMessage .= " Errore: " . $e->getMessage();
                        }
                    } else {
                        $errorMessage .= " Errore durante il caricamento del file.";
                    }
                }
                ?>
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