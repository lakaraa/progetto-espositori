<?php
include_once '../../config.php';
//include_once '../../queries.php';
include_once '../../template_header.php';


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Recupera l'ID dell'espositore dalla query string
$idEspositore = isset($_GET['id']) ? intval($_GET['id']) : 0;

function updateEspositore($pdo, $idUtente, $username, $password, $nome, $cognome, $email, $telefono, $qualifica, $curriculum = null) {
    if ($idUtente <= 0) {
        return false;
    }
    
    $sql = "UPDATE utente 
            SET Username = :username, 
                Nome = :nome, 
                Cognome = :cognome, 
                Email = :email, 
                Telefono = :telefono, 
                Qualifica = :qualifica";
                
    if (!empty($password)) {
        $sql .= ", Password = :password";
        $passwordHashed = password_hash($password, PASSWORD_BCRYPT);
    }
    
    $sql .= " WHERE Id_Utente = :idUtente AND Ruolo = 'Espositore'";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idUtente', $idUtente, PDO::PARAM_INT);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
    $stmt->bindParam(':cognome', $cognome, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR);
    $stmt->bindParam(':qualifica', $qualifica, PDO::PARAM_STR);
    
    if (!empty($password)) {
        $stmt->bindParam(':password', $passwordHashed, PDO::PARAM_STR);
    }
    
    return $stmt->execute();
}

function getEspositoreById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM utente WHERE Id_Utente = ? AND Ruolo = 'Espositore'");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
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
    $username = $_POST['Username'] ?? $espositore['Username'];
    $password = !empty($_POST['Password']) ? $_POST['Password'] : '';
    $nome = $_POST['Nome'] ?? $espositore['Nome'];
    $cognome = $_POST['Cognome'] ?? $espositore['Cognome'];
    $email = $_POST['Email'] ?? $espositore['Email'];
    $telefono = $_POST['Telefono'] ?? $espositore['Telefono'];
    $qualifica = $_POST['Qualifica'] ?? $espositore['Qualifica'];
    
    // Gestione del curriculum
    $curriculum = null;
    if (isset($_FILES['Curriculum']) && $_FILES['Curriculum']['error'] === UPLOAD_ERR_OK) {
        $curriculum = $_FILES['Curriculum'];
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

        <form class="rd-form rd-mailform" method="post" id="form-modifica-espositore" action="">
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
                        </div>
                        <small id="fileName" class="form-text text-muted mt-2"></small>
                        <div id="cv-upload-status" class="mt-2"></div>

                        <?php if (!empty($espositore['Curriculum'])): ?>
                            <div class="mt-3">
                                <a href="/progetto-espositori/uploads/cv_<?php echo $espositore['Username']; ?>.pdf" 
                                    class="btn btn-sm btn-outline-primary" 
                                    style="width: 100%; font-size: larger;"
                                    target="_blank">
                                    <i class="fas fa-file-pdf"></i> Visualizza Curriculum                    
                                    <small class="form-text text-muted">Dimensioni: <?php echo round(strlen($espositore['Curriculum']) / 1024, 2); ?> KB</small>
                                </a>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mt-2">Nessun curriculum caricato</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <button class="button button-primary button-lg" type="submit">Modifica</button>
        </form>
    </div>
</section>
<script>
function showFileName(input) {
    const fileNameDisplay = document.getElementById('fileName');
    if (fileNameDisplay && input.files[0]) {
        fileNameDisplay.textContent = "File selezionato: " + input.files[0].name;
    }
}
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-modifica-espositore');
    const cvInput = document.getElementById('espositore-curriculum');
    const statusDiv = document.getElementById('cv-upload-status');

    // Previeni il submit del form quando si carica solo il CV
    form.addEventListener('submit', function(e) {
        if (cvInput.files.length > 0 && !statusDiv.querySelector('.alert-success')) {
            e.preventDefault();
            alert('Completare prima il caricamento del CV');
        }
    });

    if (cvInput && statusDiv) {
        cvInput.addEventListener('change', async function(e) {
            const file = e.target.files[0];
            if (!file) return;

            statusDiv.innerHTML = '<div class="alert alert-info">Caricamento in corso...</div>';

            try {
                const formData = new FormData();
                formData.append('cv_file', file);
                formData.append('id_espositore', <?php echo $idEspositore; ?>);
                formData.append('username', '<?php echo $espositore["Username"]; ?>');

                const response = await fetch('save_cv.php', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                if (!response.ok) {
                    const errorText = await response.text();
                    throw new Error(`Errore server (${response.status}): ${errorText}`);
                }

                const data = await response.json();
                
                if (!data.success) {
                    throw new Error(data.message || 'Operazione fallita');
                }

                statusDiv.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                updateCvLink(data.file_url, file.size);

            } catch (error) {
                console.error('Upload error:', error);
                statusDiv.innerHTML = `<div class="alert alert-danger">${error.message}</div>`;
                e.target.value = '';
            }
        });
    }

    function updateCvLink(fileUrl, fileSize) {
        let cvLink = document.querySelector('.cv-download-link');
        const fileSizeKB = (fileSize / 1024).toFixed(2);
        
        if (!cvLink) {
            const cvContainer = document.createElement('div');
            cvContainer.className = 'mt-3';
            cvContainer.innerHTML = `
                <a href="${fileUrl}" 
                   class="btn btn-sm btn-outline-primary cv-download-link" 
                   style="width: 100%; font-size: larger;"
                   target="_blank">
                    <i class="fas fa-file-pdf"></i> Visualizza CV
                </a>
                <small class="form-text text-muted">${fileSizeKB} KB</small>
            `;
            
            document.querySelector('.form-wrap')?.appendChild(cvContainer);
        } else {
            cvLink.href = fileUrl;
            const sizeElement = cvLink.nextElementSibling;
            if (sizeElement) {
                sizeElement.textContent = `${fileSizeKB} KB`;
            }
        }
    }
});
</script>
<?php
include_once '../../template_footer.php';
?>
