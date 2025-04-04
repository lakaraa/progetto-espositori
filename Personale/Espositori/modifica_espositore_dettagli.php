<?php
include_once '../../config.php';
include_once '../../queries.php';
include_once '../../template_header.php';

// Recupera l'ID dell'espositore dalla query string
$idEspositore = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Recupera i dettagli dell'espositore
$espositore = getEspositoreById($pdo, $idEspositore);

if (!$espositore) {
    echo "<p style='color: red;'>Espositore non trovato.</p>";
    exit;
}

// Inizializza il messaggio di errore o successo
$successMessage = '';
$errorMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = !empty($_POST['Username']) ? $_POST['Username'] : $espositore['Username'];
    $password = !empty($_POST['Password']) ? password_hash($_POST['Password'], PASSWORD_DEFAULT) : ''; // Hash della password
    $nome = !empty($_POST['Nome']) ? $_POST['Nome'] : $espositore['Nome'];
    $cognome = !empty($_POST['Cognome']) ? $_POST['Cognome'] : $espositore['Cognome'];
    $email = !empty($_POST['Email']) ? $_POST['Email'] : $espositore['Email'];
    $telefono = !empty($_POST['Telefono']) ? $_POST['Telefono'] : $espositore['Telefono'];
    $qualifica = !empty($_POST['Qualifica']) ? $_POST['Qualifica'] : $espositore['Qualifica'];
    $curriculum = $espositore['Curriculum'];  // Contiene i dati binari del curriculum

    if (isset($_FILES['Curriculum']) && $_FILES['Curriculum']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['Curriculum']['tmp_name'];
        $fileType = mime_content_type($fileTmpPath);
        
        if ($fileType === 'application/pdf') { // Verifica MIME type
            $uploadDir = __DIR__ . '/../../uploads/'; // Percorso assoluto corretto
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
    
            $fileName = 'cv_' . $idEspositore . '_' . time() . '.pdf';
            $filePath = $uploadDir . $fileName;
    
            // Mostra il percorso assoluto per il debug
            echo "<p>Debug: Percorso di salvataggio: $filePath</p>";
    
            // A questo punto non cancelliamo il vecchio file, perché lo salviamo come dati binari
            // Carichiamo i dati del file come dati binari nel database
    
            $fileData = file_get_contents($fileTmpPath);  // Legge i dati binari del file
            
            if ($fileData !== false) {
                $curriculum = $fileData;  // Salviamo i dati binari del file
                echo "<p style='color: green;'>File caricato correttamente: $filePath</p>";
            } else {
                $errorMessage = "Errore durante la lettura del file.";
                echo "<p style='color: red;'>$errorMessage</p>";
            }
        } else {
            $errorMessage = "Il file caricato non è un PDF valido.";
            echo "<p style='color: red;'>$errorMessage</p>";
        }
    } elseif (isset($_FILES['Curriculum']) && $_FILES['Curriculum']['error'] !== UPLOAD_ERR_NO_FILE) {
        $errorMessage = "Errore durante il caricamento del file: " . $_FILES['Curriculum']['error'];
        echo "<p style='color: red;'>$errorMessage</p>";
    }
    

    if (updateEspositore($pdo, $idEspositore, $username, $password, $nome, $cognome, $email, $telefono, $qualifica, $curriculum)) {
        $successMessage = "Espositore aggiornato con successo.";
        $espositore = getEspositoreById($pdo, $idEspositore);
    } else {
        $errorMessage = "Errore durante l'aggiornamento dell'espositore.";
    }
}

?>

<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(/progetto-espositori/resources/images/bg-breadcrumbs-07-1920x480.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Modifica Dettagli Espositore</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="../dashboard_personale.php">Dashboard</a></li>
        <li><a href="gestisci_espositori.php">Gestione Espositori</a></li>
        <li><a href="modifica_espositore.php">Modifica Espositore</a></li>
        <li class="active">Modifica Dettagli Espositore</li>
    </ul>
</section>

<!-- Main Content-->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Modifica Espositore</h2>
        <p>Compila il modulo sottostante per modificare i dettagli dell'espositore.</p>

        <!-- Mostra i messaggi di errore o successo -->
        <?php if (!empty($successMessage)): ?>
            <p style="color: rgb(74, 196, 207);"><?php echo htmlspecialchars($successMessage); ?></p>
        <?php endif; ?>
        <?php if (!empty($errorMessage)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($errorMessage); ?></p>
        <?php endif; ?>

        <form class="rd-form rd-mailform" method="post" enctype="multipart/form-data" action="">
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
                        <input class="form-input" id="espositore-password" type="password" name="Password" value=""/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="espositore-qualifica">Qualifica</label>
                        <input class="form-input" id="espositore-qualifica" type="text" name="Qualifica" value="<?php echo htmlspecialchars($espositore['Qualifica']); ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="espositore-curriculum">Curriculum</label><br><br>
                        <div class="input-group">
                            <input class="form-control" id="espositore-curriculum" type="file" name="Curriculum" accept=".pdf" onchange="showFileName(this)">
                            <?php 
                                if (!empty($espositore['Curriculum'])): 
                            ?>
                                <a href="view_curriculum.php?id=<?php echo $idEspositore; ?>" target="_blank" class="btn btn-outline-info">
                                    <i class="fas fa-file-pdf"></i> Visualizza Curriculum Attuale
                                </a>
                            <?php else: ?>
                                <p style="color: red;">Curriculum non trovato.</p>
                            <?php endif; ?>
                        </div>
                        <small id="fileName" class="form-text text-muted mt-2"></small>
                    </div>
                </div>
            </div>
            <button class="button button-primary button-lg" type="submit">Modifica</button>
        </form>
    </div>
</section>

<!-- JavaScript per mostrare il nome del file selezionato -->
<script>
    function showFileName(input) {
        const fileName = input.files[0] ? input.files[0].name : "Nessun file selezionato.";
        document.getElementById('fileName').innerText = "File selezionato: " + fileName;
    }
</script>

<?php
include_once '../../template_footer.php';
?>
