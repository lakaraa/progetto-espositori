<?php
include_once '../config.php';
include_once '../session.php';
include_once '../queries.php';

// Verifica che l'utente sia loggato e sia un espositore
if (!isset($_SESSION['id_utente']) || $_SESSION['ruolo'] !== 'Espositore') {
    die('Accesso non autorizzato');
}

// Verifica che sia stato fornito un ID manifestazione
if (!isset($_GET['id'])) {
    die('ID manifestazione non specificato');
}

$idManifestazione = $_GET['id'];

// Recupera i dettagli della manifestazione
try {
    $manifestazione = getManifestazioneById($pdo, $idManifestazione);
    if (!$manifestazione) {
        die('Manifestazione non trovata');
    }
} catch (PDOException $e) {
    die('Errore nel recupero della manifestazione');
}

// Recupera le categorie disponibili
try {
    $categorie = getCategorie($pdo);
} catch (PDOException $e) {
    $categorie = [];
}

// Controlla se il modulo è stato inviato
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);

    ob_start();
    header('Content-Type: application/json');
    ob_clean();

    $titolo = $_POST['titolo'];
    $sintesi = $_POST['sintesi'];
    $url = $_POST['url'];
    $immagine = null;
    $categorieSelezionate = $_POST['categorie'] ?? [];

    // Gestione dell'immagine
    if (isset($_FILES['immagine']) && $_FILES['immagine']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/img/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        // Recupera username, nome e cognome dell'utente
        $userInfo = getUsernameById($pdo, $_SESSION['id_utente']);
        if (!$userInfo) {
            throw new Exception('Utente non trovato');
        }
        
        // Genera un nome file con nome_cognome_manifestazione_giorno-mese-anno
        $fileExtension = pathinfo($_FILES['immagine']['name'], PATHINFO_EXTENSION);
        $data = date('d-m-Y');
        $manifestazioneNome = preg_replace('/[^a-zA-Z0-9]/', '_', $manifestazione['Nome']); // Rimuove caratteri speciali
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
        $check = getimagesize($_FILES['immagine']['tmp_name']);
        if ($check === false) {
            throw new Exception('Il file caricato non è un\'immagine valida.');
        }

        // Sposta il file nella directory di destinazione
        if (!move_uploaded_file($_FILES['immagine']['tmp_name'], $targetFile)) {
            throw new Exception('Errore durante il caricamento dell\'immagine.');
        }
    }

    if (empty($titolo) || empty($sintesi) || empty($categorieSelezionate)) {
        echo json_encode([
            'success' => false,
            'message' => 'Titolo, sintesi e categorie sono campi obbligatori.'
        ]);
        exit;
    }

    try {
        // Inizia la transazione
        $pdo->beginTransaction();

        // Aggiungi il contributo
        $idContributo = addContributo($pdo, $_SESSION['id_utente'], $immagine, $titolo, $sintesi, 'In Approvazione', $url, $idManifestazione);
        
        if (!$idContributo) {
            throw new Exception('Errore durante l\'aggiunta della candidatura');
        }

        // Inserisci le categorie selezionate nella tabella tipologia
        foreach ($categorieSelezionate as $idCategoria) {
            $result = addTipologia($pdo, $idContributo, $idCategoria);
            
            if (!$result) {
                throw new Exception('Errore durante l\'inserimento della categoria');
            }
        }
        
        // Se tutto è andato bene, committa la transazione
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Candidatura inviata con successo!'
        ]);
        exit;
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo json_encode([
            'success' => false,
            'message' => 'Errore durante l\'invio della candidatura: ' . $e->getMessage()
        ]);
        exit;
    }
}

include_once '../template_header.php';
?>

<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(/progetto-espositori/resources/images/bg-breadcrumbs-07-1920x480.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Invia Candidatura</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="dashboard_espositore.php">Dashboard</a></li>
        <li class="active">Invia Candidatura</li>
    </ul>
</section>

<!-- Main Content-->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Invia Candidatura per <?php echo htmlspecialchars($manifestazione['Nome']); ?></h2>
        <p>Compila il modulo sottostante per inviare la tua candidatura.</p>

        <!-- Output del messaggio -->
        <div id="form-message"></div>

        <form class="form-effettua-candidatura" method="post" action="" enctype="multipart/form-data">
            <div class="row row-50">
                <!-- Manifestazione (disabled) -->
                <div class="col-md-12">
                    <div class="form-wrap">
                        <label class="form-label" for="manifestazione">Manifestazione</label>
                        <select class="form-input" id="manifestazione" name="manifestazione" disabled >
                            <option value="<?php echo htmlspecialchars($manifestazione['Id_Manifestazione']); ?>" selected >
                                <?php echo htmlspecialchars($manifestazione['Nome']) . ' - ' . date('d/m/Y', strtotime($manifestazione['Data'])); ?>
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Titolo -->
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="titolo">Titolo</label>
                        <input class="form-input" id="titolo" type="text" name="titolo" required>
                    </div>
                </div>

                <!-- URL -->
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="url">URL</label>
                        <input class="form-input" id="url" type="url" name="url">
                    </div>
                </div>

                <!-- Sintesi -->
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="sintesi">Sintesi</label>
                        <textarea class="form-input" id="sintesi" name="sintesi" required></textarea>
                    </div>
                </div>

                <!-- Immagine -->
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="immagine">Immagine</label>
                        <br>
                        <input class="form-input" id="immagine" type="file" name="immagine" accept="image/*">
                    </div>
                </div>

                <!-- Categorie -->
                <div class="col-md-12">
                    <div class="form-wrap">
                        <label class="form-label">Categorie <p style="font-size: 0.85em; display: block; margin-top: 5px;">Seleziona una o più categorie</p></label> <br>
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
            <button class="button button-primary button-lg" type="submit">Invia Candidatura</button>
        </form>
    </div>
</section>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.form-effettua-candidatura').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        $.ajax({
            url: '',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                try {
                    const data = typeof response === 'string' ? JSON.parse(response) : response;
                    const message = data.message || 'Errore sconosciuto.';
                    const isSuccess = data.success === true;

                    $('#form-message').html(
                        '<p style="color: ' + (isSuccess ? 'rgb(74, 196, 207)' : 'red') + ';">' + message + '</p>'
                    );

                    if (isSuccess) {
                        setTimeout(function() {
                            window.location.href = 'dashboard_espositore.php';
                        }, 2000);
                    }
                } catch (e) {
                    console.error("Errore JSON.parse:", e);
                    $('#form-message').html('<p style="color: red;">Risposta non valida dal server.</p>');
                }
            },
            error: function() {
                $('#form-message').html('<p style="color: red;">Errore durante l\'invio della candidatura.</p>');
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
</script>

<style>

select.form-input:disabled {
    cursor: not-allowed;
    opacity: 0.8;
    color: #FFFFFF ;
}

select.form-input:disabled option {
    color: #FFFFFF !important;
    background-color: #4ac4cf;
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
</style>

<?php
include_once '../template_footer.php';
?> 