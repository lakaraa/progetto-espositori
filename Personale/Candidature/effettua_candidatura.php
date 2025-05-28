<?php
error_reporting(E_ALL); // Report all errors and warnings
ini_set('display_errors', 1); // Display errors on the screen

include_once("../../config.php");
include_once("../../queries.php");
include_once("../../session.php");

// Recupera gli utenti per il menu a tendina
$utenti = getUtenti($pdo);
$manifestazioni = getManifestazioni($pdo);

// Recupera le categorie disponibili
try {
    $categorie = getCategorie($pdo);
} catch (PDOException $e) {
    // Gestione silenziosa dell'errore
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pulizia buffer per evitare output HTML
    ob_start();
    header('Content-Type: application/json');
    ob_clean();

    try {
        // Se è una richiesta per ricaricare le categorie
        if (isset($_POST['reload_categories'])) {
            $categorie = getCategorie($pdo);
            
            $html = '';
            foreach ($categorie as $categoria) {
                $html .= '<div class="col-md-4">
                    <div class="category-card" onclick="toggleCategory(this, ' . htmlspecialchars($categoria['Id_Categoria']) . ')">
                        <input type="checkbox" 
                               id="categoria_' . htmlspecialchars($categoria['Id_Categoria']) . '"
                               name="categorie[]" 
                               value="' . htmlspecialchars($categoria['Id_Categoria']) . '" 
                               required>
                        <div class="category-name">' . htmlspecialchars($categoria['Nome']) . '</div>';
                if (!empty($categoria['Descrizione'])) {
                    $html .= '<div class="category-desc text-muted">' . htmlspecialchars($categoria['Descrizione']) . '</div>';
                }
                $html .= '</div></div>';
            }
            
            echo json_encode([
                'success' => true,
                'html' => $html
            ]);
            exit;
        }

        $idUtente = $_POST['Id_Utente'] ?? '';
        $titolo = $_POST['Titolo'] ?? '';
        $sintesi = $_POST['Sintesi'] ?? '';
        $accettazione = "Accettato"; // Valore fisso
        $url = $_POST['URL'] ?? '';
        $idManifestazione = $_POST['Id_Manifestazione'] ?? '';
        $categorieSelezionate = $_POST['categorie'] ?? [];

        if (empty($idUtente) || empty($titolo) || empty($sintesi) || empty($idManifestazione) || empty($categorieSelezionate)) {
            throw new Exception('Tutti i campi sono obbligatori, incluse le categorie.');
        }

        // Gestione caricamento immagine
        $immagine = '';
        if (isset($_FILES['Immagine']) && $_FILES['Immagine']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../../uploads/img/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }

            // Recupera username, nome e cognome dell'utente
            $userInfo = getUsernameById($pdo, $idUtente);
            if (!$userInfo) {
                throw new Exception('Utente non trovato');
            }
            
            // Genera un nome file con nome_cognome_giorno-mese-anno
            $fileExtension = pathinfo($_FILES['Immagine']['name'], PATHINFO_EXTENSION);
            $data = date('d-m-Y');
            $manifestazioneNome = getManifestazioneNome($pdo, $idManifestazione);
            $immagine = sprintf(
                '%s_%s_%s_%s.%s',
                $userInfo['Nome'],
                $userInfo['Cognome'],
                $manifestazioneNome,
                $data,
                $fileExtension
            );
            $targetFile = $uploadDir . $immagine;

            // Controlla che il file sia un'immagine
            $check = getimagesize($_FILES['Immagine']['tmp_name']);
            if ($check === false) {
                throw new Exception('Il file caricato non è un\'immagine valida.');
            }

            // Sposta il file nella directory di destinazione
            if (!move_uploaded_file($_FILES['Immagine']['tmp_name'], $targetFile)) {
                throw new Exception('Errore durante il caricamento dell\'immagine.');
            }
        }

        // Inserisci la candidatura completa
        $idContributo = addCandidaturaCompleta($pdo, $idUtente, $immagine, $titolo, $sintesi, $accettazione, $url, $idManifestazione, $categorieSelezionate);
        
        echo json_encode([
            'success' => true,
            'message' => 'Candidatura effettuata con successo!'
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit;
}

// **Solo se non è una richiesta AJAX, includi il template**
include_once("../../template_header.php");
?>

<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(../../resources/images/sfondo.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Effettua Candidatura</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="../dashboard_personale.php">Dashboard</a></li>
        <li><a href="gestisci_candidature.php">Gestione Candidature</a></li>
        <li class="active">Effettua Candidatura</li>
    </ul>
</section>

<!-- Main Content-->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Effettua Candidatura</h2>
        <p>Compila il modulo sottostante per effettuare una candidatura.</p>

        <!-- Output del messaggio -->
        <div id="form-message"></div>

        <form class="form-effettua-candidatura" method="post" action="" enctype="multipart/form-data">
            <div class="row row-50">
                <div class="col-md-6">
                    <div class="form-wrap">
                        <select class="form-input" id="utente" name="Id_Utente" required>
                            <option value="" style="color: black;">Seleziona Utente</option>
                            <?php foreach ($utenti as $utente): ?>
                                <option value="<?php echo htmlspecialchars($utente['Id_Utente']); ?>" style="color: black;">
                                    <?php echo htmlspecialchars($utente['Email']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap">
                        <select class="form-input" id="manifestazione" name="Id_Manifestazione" required>
                            <option value="" style="color: black;">Seleziona Manifestazione</option>
                            <?php foreach ($manifestazioni as $manifestazione): ?>
                                <option value="<?php echo htmlspecialchars($manifestazione['Id_Manifestazione']); ?>" style="color: black;">
                                    <?php echo htmlspecialchars($manifestazione['Nome']) . ' - ' . date('d/m/Y', strtotime($manifestazione['Data'])); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="titolo">Titolo</label>
                        <input class="form-input" id="titolo" type="text" name="Titolo" required>
                    </div>
                    <div class="form-wrap">
                        <label class="form-label" for="url">URL</label>
                        <input class="form-input" id="url" type="url" name="URL">
                    </div>
                    <div class="form-wrap">
                        <label class="form-label" for="sintesi">Sintesi</label>
                        <textarea class="form-input" id="sintesi" name="Sintesi" required></textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="immagine">Immagine Principale</label>
                        <div class="image-upload-container">
                            <!-- Anteprima immagine -->
                            <div class="image-preview" id="imagePreview">
                                <img src="" alt="Anteprima immagine" class="image-preview__image" style="display: none;">
                                <div class="image-preview__default-text">Nessuna immagine selezionata</div>
                            </div>
                            
                            <!-- Input file personalizzato -->
                            <label for="immagine" class="custom-file-upload">
                                <i class="fa fa-cloud-upload"></i> Seleziona Immagine
                            </label>
                            <input id="immagine" type="file" name="Immagine" accept=".jpg,.jpeg,.png" style="display: none;">
                            
                            <!-- Requisiti immagine -->
                            <div class="image-requirements">
                                <small>Formati accettati: JPG, JPEG, PNG</small><br>
                                <small>Dimensione massima: 5MB</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-wrap">
                        <label class="form-label">Categorie <p style="font-size: 0.85em; display: block; margin-top: 5px;">Seleziona una o più categorie</p> </label> <br>
                        
                        <div class="row">
                            <?php foreach ($categorie as $categoria): ?>
                                <div class="col-md-4">
                                    <div class="category-card" onclick="toggleCategory(this, <?php echo htmlspecialchars($categoria['Id_Categoria']); ?>)">
                                        <input type="checkbox" 
                                               id="categoria_<?php echo htmlspecialchars($categoria['Id_Categoria']); ?>"
                                               name="categorie[]" 
                                               value="<?php echo htmlspecialchars($categoria['Id_Categoria']); ?>" 
                                               required>
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
            <button class="button button-primary button-lg" type="submit">Effettua Candidatura</button>
        </form>
    </div>
</section>

<!-- Script AJAX -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function () {
    $('.form-effettua-candidatura').on('submit', function (e) {
        e.preventDefault();

        var formData = new FormData(this);

        $.ajax({
            url: '', // stessa pagina
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                console.log("RISPOSTA RAW:", response); // Log della risposta
                try {
                    const data = typeof response === "string" ? JSON.parse(response) : response;
                    const message = data.message || "Messaggio non disponibile.";
                    const isSuccess = data.success === true;

                    if (isSuccess) {
                        // Mostra il messaggio di successo
                        $('#form-message').html(
                            `<p style="color: rgb(74, 196, 207);">${message}</p>`
                        );
                        
                        // Ricarica la pagina dopo 1 secondo
                        setTimeout(function() {
                            window.location.href = window.location.href;
                        }, 1000);
                    } else {
                        $('#form-message').html(
                            `<p style="color: red;">${message}</p>`
                        );
                    }
                } catch (e) {
                    console.error("Errore JSON.parse:", e, response);
                    $('#form-message').html('<p style="color: red;">Risposta non valida dal server.</p>');
                }
            },
            error: function (xhr, status, error) {
                console.error("Errore AJAX:", status, error);
                console.error("Risposta server:", xhr.responseText);
                $('#form-message').html('<p style="color: red;">Errore di comunicazione con il server. Controlla la console per i dettagli.</p>');
            }
        });
    });
});

function toggleCategory(element, categoryId) {
    const checkbox = element.querySelector('input[type="checkbox"]');
    const allCards = document.querySelectorAll('.category-card');
    
    // Toggle the selected class
    element.classList.toggle('selected');
    
    // Toggle the checkbox
    checkbox.checked = !checkbox.checked;
    
    // Update required validation
    const checkedBoxes = document.querySelectorAll('input[name="categorie[]"]:checked');
    if (checkedBoxes.length > 0) {
        allCards.forEach(card => {
            card.querySelector('input[type="checkbox"]').required = false;
        });
    } else {
        allCards.forEach(card => {
            card.querySelector('input[type="checkbox"]').required = true;
        });
    }
}

document.getElementById('immagine').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    // Verifica il tipo di file
    const validTypes = ['image/jpeg', 'image/png'];
    if (!validTypes.includes(file.type)) {
        alert('Per favore seleziona solo file JPG o PNG');
        this.value = '';
        return;
    }
    
    const preview = document.getElementById('imagePreview');
    const previewImage = preview.querySelector('.image-preview__image');
    const previewDefaultText = preview.querySelector('.image-preview__default-text');
    
    if (file.size > 5 * 1024 * 1024) {
        alert('L\'immagine è troppo grande! Dimensione massima consentita: 5MB');
        this.value = '';
        return;
    }
    
    const reader = new FileReader();
    reader.onload = function(e) {
        previewImage.src = e.target.result;
        previewImage.style.display = 'block';
        previewDefaultText.style.display = 'none';
        preview.style.borderColor = '#4ac4cf';
    }
    reader.readAsDataURL(file);
});
</script>

<?php
include_once("../../template_footer.php");
?>





<style>
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
    }
    .category-card:hover {
        border-color: #4ac4cf;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
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
    input[type="checkbox"] {
        display: none;
    }
    .row {
        display: flex;
        flex-wrap: wrap;
    }
    .col-md-4 {
        display: flex;
        flex-direction: column;
    }
     /* Stile per il caricamento immagine */
     .image-upload-container {
        margin-top: 10px;
        text-align: center;
    }
    
    .image-preview {
        width: 100%;
        height: 200px;
        border: 2px dashed #ddd;
        border-radius: 8px;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
        background-color: #f9f9f9;
        transition: all 0.3s ease;
    }
    
    .image-preview:hover {
        border-color: #4ac4cf;
    }
    
    .image-preview__image {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        display: none;
    }
    
    .image-preview__default-text {
        color: #888;
        font-size: 0.9em;
    }
    
    .custom-file-upload {
        display: inline-block;
        padding: 10px 20px;
        background-color: #4ac4cf;
        color: white;
        border-radius: 5px;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-bottom: 10px;
    }
    
    .custom-file-upload:hover {
        background-color: #3aa8b3;
    }
    
    .custom-file-upload i {
        margin-right: 8px;
    }
    
    .image-requirements {
        color: #666;
        font-size: 0.8em;
        margin-top: 5px;
    }
</style>