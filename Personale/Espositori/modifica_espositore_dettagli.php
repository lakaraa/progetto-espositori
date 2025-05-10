<?php
include_once '../../config.php';
include_once '../../queries.php';

error_log("Inizio modifica_espositore_dettagli.php");

$successMessage = '';
$errorMessage = '';

// Recupera l'ID dell'espositore dalla query string
$idEspositore = isset($_GET['id']) ? intval($_GET['id']) : 0;

error_log("ID Espositore ricevuto: " . $idEspositore);

if ($idEspositore <= 0) {
    error_log("ID espositore non valido: " . $idEspositore);
    die("ID espositore non valido.");
}

// Recupera i dettagli dell'espositore
$espositore = getEspositoreById($pdo, $idEspositore);
error_log("Dettagli espositore recuperati: " . print_r($espositore, true));

if (!$espositore) {
    error_log("Espositore non trovato per ID: " . $idEspositore);
    die("Espositore non trovato.");
}

// Gestisci l'invio del modulo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("Ricevuta richiesta POST");
    ob_clean();
    header('Content-Type: application/json');
    
    try {
        $nome = $_POST['nome'] ?? '';
        $cognome = $_POST['cognome'] ?? '';
        $email = $_POST['email'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $qualifica = $_POST['qualifica'] ?? '';
        $cvData = null;

        error_log("Dati ricevuti: " . print_r($_POST, true));
        error_log("Files ricevuti: " . print_r($_FILES, true));

        // Controllo dello username (almeno 4 caratteri, solo lettere, numeri e underscore)
        if (!preg_match("/^[a-zA-Z0-9_]{4,}$/", $username)) {
            throw new Exception("Lo username deve essere lungo almeno 4 caratteri e può contenere solo lettere, numeri e underscore.");
        }

        // Gestione CV se presente
        if (isset($_FILES['CV']) && $_FILES['CV']['error'] === UPLOAD_ERR_OK) {
            error_log("Gestione upload CV iniziata");
            $cvTmpPath = $_FILES['CV']['tmp_name'];
            $cvName = "cv_" . $username . ".pdf";
            $cvUploadPath = "../../uploads/cv/" . $cvName;

            // Verifica che il file sia un PDF
            if ($_FILES['CV']['type'] !== 'application/pdf') {
                throw new Exception("Il curriculum deve essere un file PDF.");
            }

            // Verifica la dimensione del file (5MB)
            if ($_FILES['CV']['size'] > 5 * 1024 * 1024) {
                throw new Exception("Il file è troppo grande. La dimensione massima consentita è 5 MB.");
            }

            error_log("Percorso file temporaneo: " . $cvTmpPath);
            error_log("Nome file finale: " . $cvName);
            error_log("Percorso upload: " . $cvUploadPath);

            // Crea cartella uploads se non esiste
            if (!is_dir('../../uploads')) {
                error_log("Creazione cartella uploads");
                if (!mkdir('../../uploads', 0777, true)) {
                    throw new Exception("Impossibile creare la cartella uploads.");
                }
            }

            // Verifica i permessi della cartella
            if (!is_writable('../../uploads')) {
                throw new Exception("La cartella uploads non è scrivibile.");
            }

            // Se esiste un CV precedente, eliminarlo
            $oldCvPath = "../../uploads/cv/cv_" . $espositore['username'] . ".pdf";
            if (file_exists($oldCvPath)) {
                error_log("Eliminazione CV precedente: " . $oldCvPath);
                if (!unlink($oldCvPath)) {
                    error_log("Impossibile eliminare il CV precedente");
                }
            }

            // Salva il file nella cartella uploads
            error_log("Tentativo di salvataggio del nuovo CV");
            if (!move_uploaded_file($cvTmpPath, $cvUploadPath)) {
                $uploadError = error_get_last();
                error_log("Errore nel salvataggio del file: " . print_r($uploadError, true));
                throw new Exception("Errore nel salvataggio del file PDF.");
            }
            
            error_log("CV salvato con successo");
            
            // Leggi il contenuto del file (per il DB)
            $cvData = file_get_contents($cvUploadPath);
            if ($cvData === false) {
                throw new Exception("Impossibile leggere il contenuto del file PDF.");
            }
        }

        // Controlla se almeno un campo è stato modificato
        $modificheEffettuate = $nome !== $espositore['nome'] || 
                              $cognome !== $espositore['cognome'] || 
                              $email !== $espositore['email'] ||
                              $telefono !== $espositore['telefono'] || 
                              $username !== $espositore['username'] || 
                              $qualifica !== $espositore['qualifica'] || 
                              !empty($password) || 
                              !empty($cvData);

        if (!$modificheEffettuate) {
            throw new Exception("Nessuna modifica rilevata.");
        }

        // Aggiorna i dati dell'espositore
        if (!updateEspositoreDettagli($pdo, $idEspositore, $nome, $cognome, $email, $telefono, $username, $qualifica, $password, $cvData)) {
            throw new Exception("Errore nell'aggiornamento dei dati.");
        }

        // Rinomina il file del CV se esiste già e se lo username è stato cambiato
        if ($espositore['username'] !== $username) {
            $cvOldName = "../../uploads/cv/cv_" . $espositore['username'] . ".pdf";
            $cvNewName = "../../uploads/cv/cv_" . $username . ".pdf";

            if (file_exists($cvOldName)) {
                if (!rename($cvOldName, $cvNewName)) {
                    error_log("Impossibile rinominare il file del CV da $cvOldName a $cvNewName");
                }
            }
        }

        echo json_encode(['success' => true, 'message' => 'Dati aggiornati con successo.']);
        exit;

    } catch (Exception $e) {
        error_log("Errore durante l'aggiornamento: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}

// Include il template header solo se non è una richiesta AJAX
include_once '../../template_header.php';
?>

<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(../../images/bg-breadcrumbs-07-1920x480.jpg);">
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

<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Modifica Espositore</h2>
        <p>Compila il modulo sottostante per modificare i dettagli dell'espositore.</p>

        <div id="form-message"></div>

        <form class="rd-form rd-mailform" method="post" id="form-modifica-espositore" action="" enctype="multipart/form-data">
            <div class="row row-50">
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="espositore-nome">Nome</label>
                        <input class="form-input" id="espositore-nome" type="text" name="nome" value="<?php echo htmlspecialchars($espositore['nome']); ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="espositore-cognome">Cognome</label>
                        <input class="form-input" id="espositore-cognome" type="text" name="cognome" value="<?php echo htmlspecialchars($espositore['cognome']); ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="espositore-email">Email</label>
                        <input class="form-input" id="espositore-email" type="email" name="email" value="<?php echo htmlspecialchars($espositore['email']); ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="espositore-telefono">Telefono</label>
                        <input class="form-input" id="espositore-telefono" type="text" name="telefono" value="<?php echo htmlspecialchars($espositore['telefono']); ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="espositore-username">Username</label>
                        <input class="form-input" id="espositore-username" type="text" name="username" value="<?php echo htmlspecialchars($espositore['username']); ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="espositore-password">Password</label>
                        <input class="form-input" id="espositore-password" type="password" name="password" value="">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-wrap">
                        <label class="form-label" for="espositore-qualifica">Qualifica</label>
                        <input class="form-input" id="espositore-qualifica" type="text" name="qualifica" value="<?php echo htmlspecialchars($espositore['qualifica']); ?>">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <div class="file-upload-wrapper">
                            <input class="file-upload-input" id="espositore-cv" type="file" name="CV" accept=".pdf" onchange="previewCV(this)">
                            <div class="file-upload-meta">
                                <label class="form-label" for="espositore-cv" style="display: block; margin-bottom: 10px; font-weight: bold; color: #333;">Curriculum Vitae</label>
                                <span class="file-upload-text">Trascina il file qui o clicca per selezionare</span>
                                <span class="file-upload-hint">Formato accettato: PDF (max 5MB)</span>
                            </div>
                        </div>
                        
                        <div class="row cv-display-area" id="cv-container-row">
                            <div class="col-md-12" id="current-cv-col">
                                <div class="cv-card">
                                    <div class="cv-card-header">
                                        <i class="fas fa-file-alt cv-icon"></i>
                                        <h4>CV Attuale</h4>
                                    </div>
                                    <div class="cv-card-body">
                                        <?php if (!empty($espositore['curriculum'])): ?>
                                            <div class="cv-action-buttons">
                                                <a href="#" 
                                                   onclick="previewNewCV('../../uploads/cv/cv_<?php echo htmlspecialchars($espositore['username']); ?>.pdf'); return false;"
                                                   class="btn btn-view">
                                                    <i class="fas fa-eye"></i> Visualizza
                                                </a>
                                                <a href="#" 
                                                   onclick="downloadCV('<?php echo htmlspecialchars($espositore['username']); ?>'); return false;"
                                                   class="btn btn-download">
                                                    <i class="fas fa-download"></i> Scarica
                                                </a>
                                            </div>
                                            <div class="cv-info">
                                                <p><i class="fas fa-file-pdf"></i> <?php echo "cv_".htmlspecialchars($espositore['username']).".pdf"; ?></p>
                                                <p class="text-muted">Ultimo aggiornamento: <?php echo date("d/m/Y H:i", filemtime("../../uploads/cv/cv_".htmlspecialchars($espositore['username']).".pdf")); ?></p>
                                            </div>
                                        <?php else: ?>
                                            <div class="no-cv">
                                                <i class="fas fa-exclamation-circle"></i>
                                                <p>Nessun CV caricato</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-12" id="new-cv-col" style="display: none;">
                                <div class="cv-card">
                                    <div class="cv-card-header">
                                        <i class="fas fa-file-upload cv-icon"></i>
                                        <h4>Nuovo CV</h4>
                                    </div>
                                    <div class="cv-card-body" id="cv-preview">
                                        <div class="cv-preview-content">
                                            <i class="fas fa-file-pdf preview-icon"></i>
                                            <p id="new-cv-filename"></p>
                                            <div class="preview-actions">
                                                <a href="#" 
                                                   onclick="previewNewCV(this.getAttribute('data-url')); return false;" 
                                                   id="preview-cv-link" 
                                                   class="btn btn-view">
                                                    <i class="fas fa-eye"></i> Anteprima
                                                </a>
                                                <button type="button" class="btn btn-remove" onclick="clearCVSelection()">
                                                    <i class="fas fa-times"></i> Rimuovi
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 text-center">
                    <button class="button button-primary button-lg" type="submit">Modifica</button>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
$(document).ready(function() {
    $('#form-modifica-espositore').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        
        // Mostra messaggio di caricamento
        $('#form-message').html('<p style="color: #4ac4cf;">Elaborazione in corso...</p>');
        
        $.ajax({
            url: window.location.href,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#form-message').html('<p style="color: #4ac4cf;">' + response.message + '</p>');
                    
                    // Aggiorna il link del CV attuale
                    var username = formData.get('username');
                    var currentCvLink = $('.current-cv a');
                    currentCvLink.attr('href', '../../uploads/cv/cv_' + username + '.pdf');
                    currentCvLink.attr('target', '_blank');
                    currentCvLink.attr('rel', 'noopener noreferrer');
                    
                    // Nascondi l'anteprima del nuovo CV e ripristina il layout
                    $('#new-cv-col').hide();
                    $('#current-cv-col').removeClass('col-md-6').addClass('col-md-12');
                    
                    // Resetta il campo file
                    $('#espositore-cv').val('');
                    
                    // Aggiorna il testo del pulsante
                    currentCvLink.text('Visualizza CV attuale');
                } else {
                    $('#form-message').html('<p style="color: red;">' + response.message + '</p>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Errore AJAX:', error);
                $('#form-message').html('<p style="color: red;">Errore durante l\'aggiornamento. Riprova più tardi.</p>');
            }
        });
    });
});

function previewNewCV(url) {
    const filename = url.split('/').pop();
    
    // Mostra l'icona di caricamento
    const loadingOverlay = document.createElement('div');
    loadingOverlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    `;
    loadingOverlay.innerHTML = `
        <div style="text-align: center;">
            <i class="fas fa-spinner fa-spin" style="font-size: 2em; color: #4ac4cf;"></i>
            <p style="margin-top: 10px; color: #333;">Caricamento PDF in corso...</p>
        </div>
    `;
    document.body.appendChild(loadingOverlay);

    // Verifica se il file esiste prima di aprirlo
    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error('File non trovato o non accessibile');
            }
            return response.blob();
        })
        .then(blob => {
            if (blob.type !== 'application/pdf') {
                throw new Error('Il file non è un PDF valido');
            }
            const newWindow = window.open(url, '_blank');
            if (newWindow) {
                newWindow.document.title = filename;
            }
        })
        .catch(error => {
            alert('Errore durante l\'apertura del PDF: ' + error.message);
        })
        .finally(() => {
            // Rimuovi l'overlay di caricamento
            document.body.removeChild(loadingOverlay);
        });
}

function previewCV(input) {
    const previewContainer = document.getElementById('cv-preview');
    const previewLink = document.getElementById('preview-cv-link');
    const filenameDisplay = document.getElementById('new-cv-filename');
    const currentCvCol = document.getElementById('current-cv-col');
    const newCvCol = document.getElementById('new-cv-col');
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const fileURL = URL.createObjectURL(file);
        
        // Mostra il nome del file
        filenameDisplay.textContent = file.name;
        
        // Imposta il link per l'anteprima
        previewLink.setAttribute('data-url', fileURL);
        
        // Mostra la colonna del nuovo CV e imposta entrambe le colonne a col-md-6
        newCvCol.style.display = 'block';
        currentCvCol.classList.remove('col-md-12');
        currentCvCol.classList.add('col-md-6');
        newCvCol.classList.remove('col-md-12');
        newCvCol.classList.add('col-md-6');
    } else {
        newCvCol.style.display = 'none';
        currentCvCol.classList.remove('col-md-6');
        currentCvCol.classList.add('col-md-12');
    }
}

function clearCVSelection() {
    const fileInput = document.getElementById('espositore-cv');
    fileInput.value = '';
    
    // Nascondi la preview e ripristina il CV attuale a col-md-12
    document.getElementById('new-cv-col').style.display = 'none';
    document.getElementById('current-cv-col').classList.remove('col-md-6');
    document.getElementById('current-cv-col').classList.add('col-md-12');
}

function downloadCV(username) {
    fetch('download_cv.php?username=' + encodeURIComponent(username))
        .then(response => response.blob())
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'cv_' + username + '.pdf';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            a.remove();
        })
        .catch(error => {
            console.error('Errore durante il download:', error);
            alert('Errore durante il download del file.');
        });
}

// Aggiungi tooltip ai pulsanti di visualizzazione CV
document.addEventListener('DOMContentLoaded', function() {
    const cvButtons = document.querySelectorAll('[onclick*="previewNewCV"]');
    cvButtons.forEach(button => {
        const url = button.getAttribute('onclick').match(/'([^']+)'/)[1];
        const filename = url.split('/').pop();
        button.title = `Visualizza ${filename}`;
    });
});
</script>

<style>
/* File Upload Styling */
.file-upload-wrapper {
    position: relative;
    margin-bottom: 20px;
    border: 2px dashed #4ac4cf;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    transition: all 0.3s ease;
    background-color: #f8f9fa;
}

.file-upload-wrapper:hover {
    border-color: #3aa8b2;
    background-color: #f0f8f9;
}

.file-upload-input {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}

.file-upload-meta {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.file-upload-text {
    font-size: 16px;
    color: #333;
    font-weight: 500;
}

.file-upload-hint {
    font-size: 13px;
    color: #6c757d;
}

/* CV Card Styling */
.cv-display-area {
    margin-top: 20px;
}

.cv-card {
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    height: 100%;
    border: 1px solid #e0e0e0;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.cv-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
}

.cv-card-header {
    background-color: #4ac4cf;
    color: white;
    padding: 15px 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.cv-card-header h4 {
    margin: 0;
    font-size: 18px;
}

.cv-icon {
    font-size: 20px;
}

.cv-card-body {
    padding: 20px;
    background-color: white;
    height: calc(100% - 53px);
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.cv-action-buttons {
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
    justify-content: center;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 8px 15px;
    border-radius: 5px;
    text-decoration: none;
    font-size: 14px;
    transition: all 0.2s ease;
}

.btn-view {
    background-color: #4ac4cf;
    color: white;
    border: 1px solid #3aa8b2;
}

.btn-view:hover {
    background-color: #3aa8b2;
    color: white;
}

.btn-download {
    background-color: white;
    color: #4ac4cf;
    border: 1px solid #4ac4cf;
}

.btn-download:hover {
    background-color: #f0f8f9;
}

.btn-preview {
    background-color: #28a745;
    color: white;
    border: 1px solid #218838;
}

.btn-preview:hover {
    background-color: #218838;
    color: white;
}

.btn-remove {
    background-color: #dc3545;
    color: white;
    border: 1px solid #c82333;
    cursor: pointer;
}

.btn-remove:hover {
    background-color: #c82333;
    color: white;
}

.cv-info {
    text-align: center;
}

.cv-info p {
    margin: 5px 0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
}

.no-cv {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    color: #6c757d;
    padding: 20px 0;
}

.no-cv i {
    font-size: 24px;
    color: #6c757d;
}

.cv-preview-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
}

.preview-icon {
    font-size: 40px;
    color: #4ac4cf;
}

.preview-actions {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}
</style>

<?php
include_once '../../template_footer.php';
?>