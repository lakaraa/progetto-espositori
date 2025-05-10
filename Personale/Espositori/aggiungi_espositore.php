<?php
include_once("../../config.php");
include_once("../../queries.php");
include_once("../../session.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    ob_clean(); // Pulisce il buffer di output

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $phone = trim($_POST['phone']);
    $qualification = trim($_POST['qualification']);
    $cv = $_FILES['cv'];

    // Validazione dei campi
    if (empty($username) || empty($password) || empty($first_name) || empty($last_name) || empty($email) || empty($phone) || empty($qualification)) {
        echo json_encode([
            'success' => false,
            'message' => 'Tutti i campi sono obbligatori.'
        ]);
        exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'success' => false,
            'message' => 'Inserisci un indirizzo email valido.'
        ]);
        exit;
    }

    // Controllo se l'email esiste già
    if (emailExists($pdo, $email)) {
        echo json_encode([
            'success' => false,
            'message' => 'L\'email è già registrata.'
        ]);
        exit;
    }
    
    // Controllo se lo username esiste già
    if (usernameExists($pdo, $username)) {
        echo json_encode([
            'success' => false,
            'message' => 'Lo username è già in uso.'
        ]);
        exit;
    }

    // Controllo del formato dello username
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        echo json_encode([
            'success' => false,
            'message' => 'Lo username può contenere solo lettere, numeri e underscore.'
        ]);
        exit;
    }

    if (!isset($_FILES['cv']) || $_FILES['cv']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode([
            'success' => false,
            'message' => 'Errore durante il caricamento del file.'
        ]);
        exit;
    }
    if ($cv['type'] !== 'application/pdf') {
        echo json_encode([
            'success' => false,
            'message' => 'Il curriculum deve essere un file PDF.'
        ]);
        exit;
    }
    if ($cv['size'] > 16 * 1024 * 1024) { // 16 MB
        echo json_encode([
            'success' => false,
            'message' => 'Il file è troppo grande. La dimensione massima consentita è 16 MB.'
        ]);
        exit;
    }

    // Mappa i valori ENUM
    $qualification_map = [
        'professional' => 'professionista del settore',
        'amateur' => 'amatore',
        'expert' => 'esperto non professionista'
    ];
    $qualification = $qualification_map[$qualification] ?? null;

    if (!$qualification) {
        echo json_encode([
            'success' => false,
            'message' => 'Qualifica non valida.'
        ]);
        exit;
    }

    // Gestione del caricamento del file
    $upload_dir = '../../uploads/cv/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $cv_filename = 'cv_' . $username . '.pdf';
    $cv_path = $upload_dir . $cv_filename;

    if (!move_uploaded_file($cv['tmp_name'], $cv_path)) {
        echo json_encode([
            'success' => false,
            'message' => 'Errore durante il salvataggio del file.'
        ]);
        exit;
    }

    // Hash della password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Inserimento nel database
    try {
        $result = addEspositore($pdo, $username, $hashed_password, $first_name, $last_name, $email, $phone, $qualification, $cv_path);
        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Espositore aggiunto con successo!' : 'Errore durante l\'aggiunta dell\'espositore.'
        ]);
        exit;
    } catch (PDOException $e) {
        // Se c'è un errore nel database, elimina il file caricato
        if (file_exists($cv_path)) {
            unlink($cv_path);
        }
        echo json_encode([
            'success' => false,
            'message' => 'Errore di connessione al database: ' . $e->getMessage()
        ]);
        exit;
    }
}

// Includi il template solo per richieste GET
include_once("../../template_header.php");
?>


<!-- Breadcrumbs -->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(images/bg-breadcrumbs-07-1920x480.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Aggiungi Espositore</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="../dashboard_personale.php">Dashboard</a></li>
        <li><a href="gestisci_espositori.php">Gestione Espositori</a></li>
        <li class="active">Aggiungi Espositore</li>
    </ul>
</section>

<!-- Main Content -->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h3>Aggiungi Espositore</h3>
        <div id="form-message"></div>
        <form class="form-aggiungi-espositore" method="post" enctype="multipart/form-data">
            <div class="row row-30">
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="registration-username">Username</label>
                        <input class="form-input" id="registration-username" type="text" name="username">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="registration-password">Password</label>
                        <input class="form-input" id="registration-password" type="password" name="password">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="registration-first-name">First Name</label>
                        <input class="form-input" id="registration-first-name" type="text" name="first_name">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="registration-last-name">Last Name</label>
                        <input class="form-input" id="registration-last-name" type="text" name="last_name">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="registration-email">Email</label>
                        <input class="form-input" id="registration-email" type="email" name="email">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="registration-phone">Phone</label>
                        <input class="form-input" id="registration-phone" type="text" name="phone">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-wrap">
                        <label class="form-label" for="registration-qualification">Qualification</label>
                        <select class="form-input" id="registration-qualification" name="qualification">
                            <option value="professional" style="color: black; background-color: white;">Professionista del settore</option>
                            <option value="amateur" style="color: black; background-color: white;">Amatore</option>
                            <option value="expert" style="color: black; background-color: white;">Esperto non professionista</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <div class="file-upload-wrapper">
                            <input class="file-upload-input" id="registration-cv" type="file" name="cv" accept=".pdf" onchange="previewCV(this)">
                            <div class="file-upload-meta">
                                <label class="form-label" for="registration-cv" style="display: block; margin-bottom: 10px; font-weight: bold; color: #333;">Curriculum Vitae</label>
                                <span class="file-upload-text">Trascina il file qui o clicca per selezionare</span>
                                <span class="file-upload-hint">Formato accettato: PDF (max 16MB)</span>
                            </div>
                        </div>
                        
                        <div class="row cv-display-area" id="cv-container-row">
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
                <div class="col-md-12">
                    <button class="button button-primary" type="submit">Aggiungi</button>
                </div>
            </div>
        </form>
    </div>
</section>

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

.preview-actions {
    display: flex;
    gap: 10px;
    margin-top: 15px;
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
</style>

<script>
function previewNewCV(url) {
    const filename = url.split('/').pop();
    const newWindow = window.open(url, '_blank');
    if (newWindow) {
        newWindow.document.title = filename;
    }
}

function previewCV(input) {
    const previewContainer = document.getElementById('cv-preview');
    const previewLink = document.getElementById('preview-cv-link');
    const filenameDisplay = document.getElementById('new-cv-filename');
    const newCvCol = document.getElementById('new-cv-col');
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const fileURL = URL.createObjectURL(file);
        
        // Mostra il nome del file
        filenameDisplay.textContent = file.name;
        
        // Imposta il link per l'anteprima
        previewLink.setAttribute('data-url', fileURL);
        
        // Mostra la colonna del nuovo CV
        newCvCol.style.display = 'block';
    } else {
        newCvCol.style.display = 'none';
    }
}

function clearCVSelection() {
    const fileInput = document.getElementById('registration-cv');
    fileInput.value = '';
    
    // Nascondi la preview
    document.getElementById('new-cv-col').style.display = 'none';
}

$(function () {
    $('.form-aggiungi-espositore').on('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);

        $.ajax({
            url: '', // stessa pagina
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                console.log("RISPOSTA RAW:", response);
                try {
                    const data = typeof response === "string" ? JSON.parse(response) : response;
                    const message = data.message || "Messaggio non disponibile.";
                    const isSuccess = data.success === true;

                    $('#form-message').html(
                        `<p style="color: ${isSuccess ? 'rgb(74, 196, 207)' : 'red'};">${message}</p>`
                    );

                    if (isSuccess) {
                        $('.form-aggiungi-espositore')[0].reset();
                        clearCVSelection();
                    }
                } catch (e) {
                    console.error("Errore JSON.parse:", e);
                    $('#form-message').html('<p style="color: red;">Risposta non valida dal server.</p>');
                }
            },
            error: function () {
                $('#form-message').html('<p style="color: red;">Errore di comunicazione con il server.</p>');
            }
        });
    });
});
</script>

<?php
include_once("../../template_footer.php");
?>