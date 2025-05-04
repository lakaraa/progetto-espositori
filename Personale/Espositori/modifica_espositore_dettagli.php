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
            $cvUploadPath = "../../uploads/" . $cvName;

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
            $oldCvPath = "../../uploads/cv_" . $espositore['username'] . ".pdf";
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
            $cvOldName = "../../uploads/cv_" . $espositore['username'] . ".pdf";
            $cvNewName = "../../uploads/cv_" . $username . ".pdf";

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
                    <div class="form-wrap" style="margin-bottom: 20px;">
                        <label class="form-label" for="espositore-cv" style="display: block; margin-bottom: 5px; font-weight: bold;">Curriculum Vitae</label> <br>
                        <input class="form-input" id="espositore-cv" type="file" name="CV" accept=".pdf,.doc,.docx" onchange="previewCV(this)">
                        <div class="row" id="cv-container-row">
                            <div class="col-md-12" id="current-cv-col">
                                <div class="cv-container">
                                    <?php if (!empty($espositore['curriculum'])): ?>
                                        <div class="current-cv">
                                            <a href="../../uploads/cv_<?php echo htmlspecialchars($espositore['username']); ?>.pdf" 
                                               target="_blank" 
                                               rel="noopener noreferrer"
                                               class="button button-primary">
                                                Visualizza CV attuale
                                            </a>
                                            <p class="text-muted">CV attuale</p>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted">Nessun CV caricato</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6" id="new-cv-col" style="display: none;">
                                <div id="cv-preview">
                                    <div class="current-cv">
                                        <a href="#" id="preview-cv-link" target="_blank" rel="noopener noreferrer" class="button button-primary">
                                            Visualizza nuovo CV
                                        </a>
                                        <p class="text-muted">Anteprima nuovo CV</p>
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
                    currentCvLink.attr('href', '../../uploads/cv_' + username + '.pdf');
                    
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

function previewCV(input) {
    const previewContainer = document.getElementById('cv-preview');
    const previewLink = document.getElementById('preview-cv-link');
    const currentCvCol = document.getElementById('current-cv-col');
    const newCvCol = document.getElementById('new-cv-col');
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const fileURL = URL.createObjectURL(file);
        previewLink.href = fileURL;
        previewContainer.style.display = 'block';
        newCvCol.style.display = 'block';
        currentCvCol.classList.remove('col-md-12');
        currentCvCol.classList.add('col-md-6');
    } else {
        previewContainer.style.display = 'none';
        newCvCol.style.display = 'none';
        currentCvCol.classList.remove('col-md-6');
        currentCvCol.classList.add('col-md-12');
    }
}
</script>

<style>
/* Container per il CV */
.cv-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    margin-top: 10px;
    width: 100%;
    height: 100%;
}

/* Stile per il CV corrente */
.current-cv {
    border: 1px solid #ddd;
    padding: 15px;
    border-radius: 5px;
    background-color: white;
    text-align: center;
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    transition: all 0.3s ease;
    min-height: 100px;
}

/* Stile per il pulsante di visualizzazione CV */
.button-primary {
    display: inline-block;
    padding: 10px 20px;
    background-color: #4ac4cf;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    transition: background-color 0.3s ease;
    margin-bottom: 10px;
}

.button-primary:hover {
    background-color: #3aa8b2;
    color: white;
    text-decoration: none;
}

.text-muted {
    color: #6c757d;
    margin: 0;
}

#cv-container-row {
    display: flex;
    align-items: stretch;
}

#current-cv-col, #new-cv-col {
    display: flex;
    align-items: stretch;
}

#cv-preview {
    width: 100%;
    height: 100%;
}
</style>

<?php
include_once '../../template_footer.php';
?>