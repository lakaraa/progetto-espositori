<?php
include_once '../../config.php';
include_once '../../queries.php';

// Abilita la segnalazione degli errori
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '../../logs/php_errors.log');

$successMessage = '';
$errorMessage = '';

// Recupera l'ID della candidatura dalla query string
$idCandidatura = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($idCandidatura <= 0) {
    die("ID candidatura non valido.");
}

// Gestisci l'invio del modulo (AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debug: log dei dati ricevuti
    error_log('POST data: ' . print_r($_POST, true));
    error_log('FILES data: ' . print_r($_FILES, true));
    
    // Assicuriamoci che non ci sia output prima della risposta JSON
    ob_clean();
    header('Content-Type: application/json');

    try {
        $titolo = $_POST['Titolo'] ?? '';
        $sintesi = $_POST['Sintesi'] ?? '';
        $url = $_POST['URL'] ?? '';
        $accettazione = $_POST['Accettazione'] ?? '';
        $idManifestazione = $_POST['Id_Manifestazione'] ?? '';
        $categorieSelezionate = $_POST['categorie'] ?? [];
        $immagine = null;

        // Recupera i dettagli attuali della candidatura
        $candidatura = getCandidaturaCompleta($pdo, $idCandidatura);
        if (!$candidatura) {
            throw new Exception('Candidatura non trovata.');
        }

        error_log('Candidatura recuperata: ' . print_r($candidatura, true));

        // Gestione immagine se presente
        if (isset($_FILES['Immagine']) && $_FILES['Immagine']['error'] === UPLOAD_ERR_OK) {
            error_log('Inizio gestione immagine');
            error_log('Dettagli file caricato: ' . print_r($_FILES['Immagine'], true));
            
            // Verifica il tipo di file
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            $fileType = $_FILES['Immagine']['type'];
            if (!in_array($fileType, $allowedTypes)) {
                throw new Exception('Tipo di file non supportato. Usa JPG o PNG.');
            }

            $immagineTmpPath = $_FILES['Immagine']['tmp_name'];
            if (!file_exists($immagineTmpPath)) {
                throw new Exception('File temporaneo non trovato.');
            }

            $fileExtension = strtolower(pathinfo($_FILES['Immagine']['name'], PATHINFO_EXTENSION));
            error_log('Estensione file: ' . $fileExtension);
            
            // Recupera le informazioni dell'utente dalla candidatura
            $userInfo = getUserInfo($pdo, $idCandidatura);
            if (!$userInfo) {
                throw new Exception('Informazioni utente non trovate.');
            }
            error_log('Info utente: ' . print_r($userInfo, true));
            
            $manifestazioneInfo = getManifestazioneById($pdo, $idManifestazione);
            if (!$manifestazioneInfo) {
                throw new Exception('Informazioni manifestazione non trovate.');
            }
            error_log('Info manifestazione: ' . print_r($manifestazioneInfo, true));
            
            $manifestazioneNome = str_replace(' ', '_', $manifestazioneInfo['Nome']);
            $data = date('Ymd');
            
            // Genera il nome del file
            $immagineName = sprintf(
                '%s_%s_%s_%s.%s',
                $userInfo['Nome'],
                $userInfo['Cognome'],
                $manifestazioneNome,
                $data,
                $fileExtension
            );
            
            error_log('Nome file generato: ' . $immagineName);
            
            // Verifica e crea la directory di upload se necessario
            $uploadDir = "../../uploads/img/";
            if (!file_exists($uploadDir)) {
                error_log('Creazione directory upload: ' . $uploadDir);
                if (!mkdir($uploadDir, 0777, true)) {
                    throw new Exception('Impossibile creare la directory di upload.');
                }
            }

            // Verifica i permessi della directory
            if (!is_writable($uploadDir)) {
                error_log('Directory non scrivibile: ' . $uploadDir);
                chmod($uploadDir, 0777);
                if (!is_writable($uploadDir)) {
                    throw new Exception('La directory di upload non è scrivibile.');
                }
            }

            $immagineUploadPath = $uploadDir . $immagineName;
            error_log('Percorso upload: ' . $immagineUploadPath);

            // Se esiste un'immagine precedente, eliminarla
            if (!empty($candidatura['Immagine'])) {
                $oldImagePath = $uploadDir . $candidatura['Immagine'];
                error_log('Controllo immagine precedente: ' . $oldImagePath);
                
                if (file_exists($oldImagePath)) {
                    error_log('Tentativo eliminazione immagine vecchia');
                    if (!is_writable($oldImagePath)) {
                        error_log('Immagine vecchia non è scrivibile');
                        chmod($oldImagePath, 0666);
                    }
                    if (!unlink($oldImagePath)) {
                        error_log('Impossibile eliminare l\'immagine vecchia: ' . $oldImagePath);
                        $error = error_get_last();
                        error_log('Errore eliminazione: ' . print_r($error, true));
                        throw new Exception('Errore nell\'eliminazione dell\'immagine precedente: ' . ($error['message'] ?? 'Errore sconosciuto'));
                    } else {
                        error_log('Immagine vecchia eliminata con successo');
                    }
                } else {
                    error_log('Immagine vecchia non trovata nel percorso: ' . $oldImagePath);
                }
            }

            // Salva il file
            error_log('Tentativo salvataggio file');
            if (!move_uploaded_file($immagineTmpPath, $immagineUploadPath)) {
                $uploadError = error_get_last();
                error_log('Errore nel salvataggio del file: ' . print_r($uploadError, true));
                throw new Exception('Errore nel salvataggio dell\'immagine: ' . ($uploadError['message'] ?? 'Errore sconosciuto'));
            }
            
            // Verifica che il file sia stato effettivamente salvato
            if (!file_exists($immagineUploadPath)) {
                error_log('File non trovato dopo il salvataggio');
                throw new Exception('Errore: il file non è stato salvato correttamente');
            }
            
            $immagine = $immagineName;
            error_log('File salvato con successo');
        } else {
            $immagine = $candidatura['Immagine'];
            error_log('Nessuna nuova immagine caricata, mantengo quella esistente: ' . $immagine);
        }

        // Controlla se almeno un campo è stato modificato
        $modificheEffettuate = $titolo !== $candidatura['Titolo'] || 
                              $sintesi !== $candidatura['Sintesi'] || 
                              $url !== $candidatura['URL'] || 
                              $accettazione !== $candidatura['Accettazione'] || 
                              $idManifestazione != $candidatura['Id_Manifestazione'] ||
                              !empty($immagine) ||
                              array_diff($categorieSelezionate, $candidatura['Categorie']) ||
                              array_diff($candidatura['Categorie'], $categorieSelezionate);

        if (!$modificheEffettuate) {
            echo json_encode(['success' => false, 'message' => 'Nessuna modifica rilevata.']);
            exit;
        }

        error_log('Tentativo aggiornamento candidatura');
        $result = updateCandidaturaCompleta($pdo, $idCandidatura, $titolo, $sintesi, $url, $accettazione, $idManifestazione, $categorieSelezionate, $immagine);
        
        if ($result) {
            error_log('Aggiornamento completato con successo');
            echo json_encode(['success' => true, 'message' => 'Dati aggiornati con successo.']);
        } else {
            error_log('Errore nell\'aggiornamento');
            throw new Exception('Errore nell\'aggiornamento dei dati.');
        }
    } catch (Exception $e) {
        error_log('Errore durante l\'aggiornamento: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Errore: ' . $e->getMessage()]);
    }
    exit;
}

// Include il template header solo se non è una richiesta AJAX
include_once '../../template_header.php';

// Recupera le manifestazioni e le categorie disponibili
$manifestazioni = getManifestazioni($pdo);
$categorie = getCategorie($pdo);

// Recupera i dettagli della candidatura per la visualizzazione del form
$candidatura = getCandidaturaCompleta($pdo, $idCandidatura);
if (!$candidatura) {
    die("Candidatura non trovata.");
}

// Recupera le categorie attuali della candidatura
$candidaturaCategorie = getCandidaturaCategorie($pdo, $idCandidatura);
$categorieAttuali = array_column($candidaturaCategorie, 'Id_Categoria');
?>

<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(../../resources/images/sfondo.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Modifica Dettagli Candidatura</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="../dashboard_personale.php">Dashboard</a></li>
        <li><a href="gestisci_candidature.php">Gestione Candidature</a></li>
        <li><a href="modifica_candidatura.php">Modifica Candidatura</a></li>
        <li class="active">Modifica Dettagli Candidatura</li>
    </ul>
</section>

<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Modifica Candidatura</h2>
        <p>Compila il modulo sottostante per modificare i dettagli della candidatura.</p> <br>

        <div id="form-message"></div>

        <form class="form-modifica-candidatura" method="post" action="" enctype="multipart/form-data">
            <div class="row row-50">
                <div class="col-md-6">
                    <div class="form-wrap" style="margin-bottom: 20px;">
                        <label class="form-label" for="candidatura-titolo" style="display: block; margin-bottom: 5px; font-weight: bold;">Titolo</label> <br>
                        <input class="form-input" id="candidatura-titolo" type="text" name="Titolo" value="<?php echo htmlspecialchars($candidatura['Titolo']); ?>">
                    </div>

                    <div class="form-wrap" style="margin-bottom: 20px;">
                        <label class="form-label" for="candidatura-manifestazione" style="display: block; margin-bottom: 5px; font-weight: bold;">Manifestazione</label> <br>
                        <select class="form-input" id="candidatura-manifestazione" name="Id_Manifestazione" required disabled style="cursor: not-allowed;">
                            <?php foreach ($manifestazioni as $manifestazione): ?>
                                <option value="<?php echo htmlspecialchars($manifestazione['Id_Manifestazione']); ?>" style="color: black;" 
                                        <?php echo $manifestazione['Id_Manifestazione'] == $candidatura['Id_Manifestazione'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($manifestazione['Nome']) . ' - ' . date('d/m/Y', strtotime($manifestazione['Data'])); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="Id_Manifestazione" value="<?php echo htmlspecialchars($candidatura['Id_Manifestazione']); ?>">
                    </div>

                    <div class="form-wrap" style="margin-bottom: 20px;">
                        <label class="form-label" for="candidatura-sintesi" style="display: block; margin-bottom: 5px; font-weight: bold;">Sintesi</label> <br>
                        <textarea class="form-input" id="candidatura-sintesi" name="Sintesi"><?php echo htmlspecialchars($candidatura['Sintesi']); ?></textarea>
                    </div>

                    <div class="form-wrap" style="margin-bottom: 20px;">
                        <label class="form-label" for="candidatura-url" style="display: block; margin-bottom: 5px; font-weight: bold;">URL</label> <br>
                        <input class="form-input" id="candidatura-url" type="text" name="URL" value="<?php echo htmlspecialchars($candidatura['URL']); ?>">
                    </div>

                    <div class="form-wrap" style="margin-bottom: 20px;">
                        <label class="form-label" for="candidatura-accettazione" style="display: block; margin-bottom: 5px; font-weight: bold;">Accettazione</label> <br>
                        <select class="form-input" id="candidatura-accettazione" name="Accettazione">
                            <option value="In Approvazione" <?php echo $candidatura['Accettazione'] === 'In Approvazione' ? 'selected' : ''; ?> style="color: black;">In Approvazione</option>
                            <option value="Accettato" <?php echo $candidatura['Accettazione'] === 'Accettato' ? 'selected' : ''; ?> style="color: black;">Accettato</option>
                            <option value="Rifiutato" <?php echo $candidatura['Accettazione'] === 'Rifiutato' ? 'selected' : ''; ?> style="color: black;">Rifiutato</option>
                        </select>
                    </div>

                    <div class="form-wrap" style="margin-bottom: 20px;">
                        <label class="form-label" for="candidatura-immagine" style="display: block; margin-bottom: 5px; font-weight: bold;">Immagine</label> <br>   
                        <input class="form-input" id="candidatura-immagine" type="file" name="Immagine" accept="image/jpeg,image/png,image/jpg" onchange="previewImage(this)">
                        <div class="image-container">
                            <?php if (!empty($candidatura['Immagine'])): ?>
                                <div class="current-image">
                                    <img src="../../uploads/img/<?php echo htmlspecialchars($candidatura['Immagine']); ?>" 
                                         alt="Immagine" 
                                         style="width: 100px; height: auto; cursor: pointer;" 
                                         onclick="showImageModal('../../uploads/img/<?php echo htmlspecialchars($candidatura['Immagine']); ?>')">
                                    <p class="text-muted">Immagine attuale</p>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">Nessuna immagine</p>
                            <?php endif; ?>

                            <div id="image-preview" style="display: none; margin-top: 10px;">
                                <img id="preview" src="#" alt="Anteprima" style="width: 100px; height: auto; cursor: pointer;" onclick="showImageModal(this.src)">
                                <p class="text-muted">Anteprima nuova immagine</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal per visualizzare l'immagine -->
                <div id="imageModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.8); z-index: 1000; justify-content: center; align-items: center;">
                    <span style="position: absolute; top: 20px; right: 30px; font-size: 30px; color: white; cursor: pointer;" onclick="closeImageModal()">&times;</span>
                    <img id="modalImage" src="" alt="Immagine" style="max-width: 90%; max-height: 90%; margin: auto; display: block;">
                </div>

                <div class="col-md-6">
                    <div>                        
                        <label class="form-label">Categorie <p style="font-size: 0.85em; display: block; margin-top: 5px;">Seleziona una o più categorie</p> </label> <br>
                        
                        <div class="row">
                            <?php foreach ($categorie as $categoria): ?>
                                <div class="col-md-4">
                                    <div class="category-card <?php echo in_array($categoria['Id_Categoria'], $categorieAttuali) ? 'selected' : ''; ?>" 
                                         onclick="toggleCategory(this, <?php echo htmlspecialchars($categoria['Id_Categoria']); ?>)">
                                        <input type="checkbox" 
                                               id="categoria_<?php echo htmlspecialchars($categoria['Id_Categoria']); ?>"
                                               name="categorie[]" 
                                               value="<?php echo htmlspecialchars($categoria['Id_Categoria']); ?>" 
                                               <?php echo in_array($categoria['Id_Categoria'], $categorieAttuali) ? 'checked' : ''; ?>
                                               style="display: none;">
                                        <div class="category-name"><?php echo htmlspecialchars($categoria['Nome']); ?></div>
                                        <?php if (!empty($categoria['Descrizione'])): ?>
                                            <div class="category-desc text-muted"><?php echo htmlspecialchars($categoria['Descrizione']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function () {
    $('.form-modifica-candidatura').on('submit', function (e) {
        e.preventDefault();
        
        // Verifica se almeno una categoria è selezionata
        var categorieSelezionate = $('input[name="categorie[]"]:checked').length;
        if (categorieSelezionate === 0) {
            $('#form-message').html('<p style="color: red;">Seleziona almeno una categoria.</p>');
            return false;
        }
        
        var formData = new FormData(this);
        
        // Verifica se è stata selezionata una nuova immagine
        var fileInput = document.getElementById('candidatura-immagine');
        if (fileInput.files.length > 0) {
            var file = fileInput.files[0];
            // Verifica il tipo di file
            if (!file.type.match('image/jpeg') && !file.type.match('image/jpg') && !file.type.match('image/png')) {
                $('#form-message').html('<p style="color: red;">Formato immagine non supportato. Usa JPG o PNG.</p>');
                return false;
            }
            // Verifica la dimensione del file (max 5MB)
            if (file.size > 5 * 1024 * 1024) {
                $('#form-message').html('<p style="color: red;">L\'immagine non può superare i 5MB.</p>');
                return false;
            }
        }
        
        // Debug: mostra i dati che stiamo inviando
        for (var pair of formData.entries()) {
            console.log(pair[0] + ': ' + (pair[1] instanceof File ? pair[1].name : pair[1]));
        }
        
        // Mostra un messaggio di caricamento
        $('#form-message').html('<p style="color: #4ac4cf;">Elaborazione in corso...</p>');
        
        // Disabilita il pulsante di submit durante l'upload
        $('button[type="submit"]').prop('disabled', true);
        
        $.ajax({
            url: window.location.href,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            timeout: 30000, // 30 secondi di timeout
            success: function (response) {
                console.log('Risposta server:', response);
                
                let data;
                try {
                    data = typeof response === "string" ? JSON.parse(response) : response;
                    console.log('Dati parsati:', data);
                } catch (e) {
                    console.error('Errore parsing JSON:', e);
                    console.log('Risposta ricevuta:', response);
                    $('#form-message').html('<p style="color: red;">Errore nella risposta del server. Riprova più tardi.</p>');
                    $('button[type="submit"]').prop('disabled', false);
                    return;
                }
                
                if (data.success) {
                    $('#form-message').html(`<p style="color: #4ac4cf;">${data.message}</p>`);
                    setTimeout(function() { 
                        window.location.href = 'modifica_candidatura.php'; 
                    }, 1000);
                } else {
                    $('#form-message').html(`<p style="color: red;">${data.message || 'Errore durante l\'aggiornamento'}</p>`);
                    $('button[type="submit"]').prop('disabled', false);
                }
            },
            error: function (xhr, status, error) {
                console.error('Errore AJAX:', status, error);
                console.log('Risposta server:', xhr.responseText);
                
                let errorMessage = 'Errore di comunicazione con il server. ';
                if (xhr.status === 500) {
                    errorMessage += 'Errore interno del server. ';
                    // Log dell'errore completo
                    console.error('Dettagli errore:', {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        responseText: xhr.responseText,
                        error: error
                    });
                } else if (xhr.status === 404) {
                    errorMessage += 'Risorsa non trovata. ';
                } else if (status === 'timeout') {
                    errorMessage += 'Timeout della richiesta. ';
                }
                
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        errorMessage += response.message;
                    }
                } catch (e) {
                    errorMessage += 'Riprova più tardi.';
                }
                
                $('#form-message').html(`<p style="color: red;">${errorMessage}</p>`);
                $('button[type="submit"]').prop('disabled', false);
            }
        });
    });
});

function toggleCategory(element, categoryId) {
    const checkbox = element.querySelector('input[type="checkbox"]');
    element.classList.toggle('selected');
    checkbox.checked = !checkbox.checked;
}

function previewImage(input) {
    const preview = document.getElementById('preview');
    const previewContainer = document.getElementById('image-preview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewContainer.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.src = '#';
        previewContainer.style.display = 'none';
    }
}

// Funzione per mostrare la finestra modale con l'immagine
function showImageModal(imageSrc) {
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    modalImage.src = imageSrc;
    modal.style.display = 'flex';
}

// Funzione per chiudere la finestra modale
function closeImageModal() {
    const modal = document.getElementById('imageModal');
    modal.style.display = 'none';
}
</script>

<style>
/* Stili per gli input con autofill */


.form-input:-webkit-autofill,
.form-input:-webkit-autofill:hover,
.form-input:-webkit-autofill:focus,
.form-input:-webkit-autofill:active {
    -webkit-box-shadow: 0 0 0 30px white inset !important;
    -webkit-text-fill-color: #000 !important;
    transition: background-color 5000s ease-in-out 0s;
}

/* Container per le immagini */
.image-container {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
    margin-top: 10px;
    width: 100%;
}

/* Stile per l'immagine corrente */
.current-image {
    border: 1px solid #ddd;
    padding: 10px;
    border-radius: 5px;
    background-color: white;
    text-align: center;
    width: 100%;
}

/* Stile per le immagini all'interno del container */
.current-image img {
    border-radius: 4px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    max-width: 100%;
}

/* Stile per l'anteprima dell'immagine */
#image-preview {
    border: 1px solid #ddd;
    padding: 10px;
    border-radius: 5px;
    background-color: white;
    text-align: center;
    width: 100%;
}

/* Stile per l'immagine di anteprima */
#image-preview img {
    border-radius: 4px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    max-width: 100%;
}

/* Stile per il testo secondario */
    .text-muted {
        color: #6c757d;
        font-size: 0.875em;
        margin: 0;
    }
    .category-card {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        cursor: pointer;
        transition: all 0.3s ease;
        background-color: white;
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
    }
    .category-card:hover {
        border-color: #4ac4cf;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .category-card input[type="checkbox"] {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }
    .category-card.selected {
        background-color: #4ac4cf;
        border-color: #4ac4cf;
        color: white;
    }
    .category-card.selected .text-muted {
        color: rgba(255,255,255,0.8) !important;
    }
    .category-name {
        font-weight: 700;
        margin-bottom: 5px;
        color: #000;
        font-size: 1.1em;
    }
    .category-desc {
        font-size: 0.9em;
        color: #666;
        flex-grow: 1;
    }
    .category-card.selected .category-name {
        color: white;
    }
    .row {
        display: flex;
        flex-wrap: wrap;
    }
    .col-md-4 {
        display: flex;
        flex-direction: column;
    }
</style>

<?php
include_once '../../template_footer.php';
?>