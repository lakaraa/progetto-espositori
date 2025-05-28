<?php 
error_reporting(E_ALL); // Report all errors and warnings
ini_set('display_errors', 1); // Display errors on the screen

include_once '../session.php';
include_once '../template_header.php'; 
?>
<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(../resources/images/sfondo.jpg);">
       <div class="container">
        <h2 class="breadcrumbs-custom-title">Registrazione</h2>
    </div>
        <ul class="breadcrumbs-custom-path">
            <li><a href="index.php">Home</a></li>
            <li class="active">Registration</li>
        </ul>
</section>

<!-- Registration Section-->
<section class="section section-lg bg-default">
    <div class="container">
        <div class="row row-50 justify-content-center">
            <div class="col-md-10 col-lg-8">
                <h3>Registrati come Espositore</h3>
                <div id="form-message"></div>
                <form method="post" enctype="multipart/form-data" action="../auth/registrazione_espositore_handler.php" id="registration-form">
                    <div class="row row-30">
                        <div class="col-md-6">
                            <div class="form-wrap">
                                <label class="form-label" for="registration-username">Username</label>
                                <input class="form-input" id="registration-username" type="text" name="username" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-wrap">
                                <label class="form-label" for="registration-password">Password</label>
                                <input class="form-input" id="registration-password" type="password" name="password" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-wrap">
                                <label class="form-label" for="registration-first-name">Nome</label>
                                <input class="form-input" id="registration-first-name" type="text" name="first_name" data-validate="required" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-wrap">
                                <label class="form-label" for="registration-last-name">Cognome</label>
                                <input class="form-input" id="registration-last-name" type="text" name="last_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-wrap">
                                <label class="form-label" for="registration-email">Email</label>
                                <input class="form-input" id="registration-email" type="email" name="email" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-wrap">
                                <label class="form-label" for="registration-phone">Telefono</label>
                                <input class="form-input" id="registration-phone" type="text" name="phone" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-wrap">
                                <label class="form-label" for="registration-qualification">Qualifica</label>
                                <select class="form-input" id="registration-qualification" name="qualification" required >
                                    <option value="professional" style="color: black; background-color: white;">Professionista</option>
                                    <option value="amateur" style="color: black; background-color: white;">Amatore</option>
                                    <option value="expert" style="color: black; background-color: white;">Esperto Non-Professionista</option>
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
                        <div class="col-md-12 d-flex justify-content-center">
                            <button class="button button-primary" type="submit">Registrati</button>
                        </div>
                    </div>
                </form>
                <div class="col-md-12">
                    <div class="text-center">
                        <br>
                        <p>Hai già un account? <a href="login.php"> Sì</a></p>
                        Non hai un account? <a href="registrazione_visitatore.php">Registrati come visitatore</a>
                    </div>
                </div>
            </div>
        </div>
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

.alert {
    padding: 15px 20px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.alert i {
    font-size: 1.2em;
}

.alert-danger {
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
}

.alert-success {
    color: #155724;
    background-color: #d4edda;
    border-color: #c3e6cb;
}

.btn-close {
    margin-left: auto;
    padding: 0.5rem;
    background: transparent;
    border: 0;
    opacity: 0.5;
    transition: opacity 0.2s;
}

.btn-close:hover {
    opacity: 0.75;
}

#form-message {
    margin-bottom: 20px;
    text-align: center;
}

#form-message p {
    padding: 10px;
    border-radius: 4px;
    font-weight: 500;
}
</style>

<script>
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
            $('#form-message').html(`<p style="color: red;">${error.message}</p>`);
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
    const newCvCol = document.getElementById('new-cv-col');
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Verifica che il file sia un PDF
        if (file.type !== 'application/pdf') {
            $('#form-message').html('<p style="color: red;">Per favore, carica solo file PDF.</p>');
            input.value = '';
            return;
        }
        
        // Verifica la dimensione del file (16MB)
        if (file.size > 16 * 1024 * 1024) {
            $('#form-message').html('<p style="color: red;">Il file è troppo grande. La dimensione massima consentita è 16 MB.</p>');
            input.value = '';
            return;
        }
        
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

// Aggiungi validazione al form
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registration-form');
    
    // Funzione per verificare username ed email
    async function checkExistingUser(username, email) {
        try {
            const response = await fetch('../auth/check_user_exists.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ username, email })
            });
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Errore durante il controllo:', error);
            return { error: true };
        }
    }

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Pulisci eventuali messaggi precedenti
        $('#form-message').html('');
        
        const cvInput = document.getElementById('registration-cv');
        let hasError = false;
        
        // Validazione campi obbligatori
        const requiredFields = [
            { id: 'registration-username', label: 'Username' },
            { id: 'registration-password', label: 'Password' },
            { id: 'registration-first-name', label: 'Nome' },
            { id: 'registration-last-name', label: 'Cognome' },
            { id: 'registration-email', label: 'Email' },
            { id: 'registration-phone', label: 'Telefono' }
        ];

        for (const field of requiredFields) {
            const input = document.getElementById(field.id);
            if (!input || !input.value.trim()) {
                $('#form-message').html(`<p style="color: red;">Il campo ${field.label} è obbligatorio.</p>`);
                if (input) input.focus();
                hasError = true;
                return;
            }
        }

        // Validazione qualifica
        const qualificationSelect = document.getElementById('registration-qualification');
        if (!qualificationSelect || !qualificationSelect.value) {
            $('#form-message').html('<p style="color: red;">Seleziona una qualifica.</p>');
            if (qualificationSelect) qualificationSelect.focus();
            hasError = true;
            return;
        }
        
        // Validazione email
        const emailInput = document.getElementById('registration-email');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(emailInput.value.trim())) {
            $('#form-message').html('<p style="color: red;">Inserisci un indirizzo email valido.</p>');
            emailInput.focus();
            hasError = true;
            return;
        }
        
        // Validazione password
        const passwordInput = document.getElementById('registration-password');
        if (passwordInput.value.length < 8) {
            $('#form-message').html('<p style="color: red;">La password deve essere di almeno 8 caratteri.</p>');
            passwordInput.focus();
            hasError = true;
            return;
        }
        
        // Validazione telefono
        const phoneInput = document.getElementById('registration-phone');
        const phoneRegex = /^[0-9\s]+$/;
        if (!phoneRegex.test(phoneInput.value.trim())) {
            $('#form-message').html('<p style="color: red;">Il numero di telefono può contenere solo numeri e spazi.</p>');
            phoneInput.focus();
            hasError = true;
            return;
        }
        
        if (cvInput.files.length > 0) {
            const file = cvInput.files[0];
            
            // Verifica che il file sia un PDF
            if (file.type !== 'application/pdf') {
                $('#form-message').html('<p style="color: red;">Per favore, carica solo file PDF.</p>');
                hasError = true;
                return;
            }
            
            // Verifica la dimensione del file (16MB)
            if (file.size > 16 * 1024 * 1024) {
                $('#form-message').html('<p style="color: red;">Il file è troppo grande. La dimensione massima consentita è 16 MB.</p>');
                hasError = true;
                return;
            }
        } else {
            $('#form-message').html('<p style="color: red;">Per favore, carica un file PDF.</p>');
            hasError = true;
            return;
        }
        
        if (!hasError) {
            // Mostra l'overlay di caricamento
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
                    <p style="margin-top: 10px; color: #333;">Verifica in corso...</p>
                </div>
            `;
            document.body.appendChild(loadingOverlay);

            // Verifica username ed email
            const username = document.getElementById('registration-username').value.trim();
            const email = emailInput.value.trim();
            
            try {
                const checkResult = await checkExistingUser(username, email);
                
                if (checkResult.error) {
                    $('#form-message').html('<p style="color: red;">Errore durante la verifica dei dati. Riprova più tardi.</p>');
                    document.body.removeChild(loadingOverlay);
                    return;
                }
                
                if (checkResult.usernameExists) {
                    $('#form-message').html('<p style="color: red;">Lo username è già in uso. Scegline un altro.</p>');
                    document.getElementById('registration-username').focus();
                    document.body.removeChild(loadingOverlay);
                    return;
                }
                
                if (checkResult.emailExists) {
                    $('#form-message').html('<p style="color: red;">L\'email è già registrata. Usa un\'altra email.</p>');
                    emailInput.focus();
                    document.body.removeChild(loadingOverlay);
                    return;
                }
                
                // Se non ci sono duplicati, procedi con l'invio del form
                loadingOverlay.querySelector('p').textContent = 'Registrazione in corso...';
                
                // Invia il form tramite AJAX
                const formData = new FormData(form);
                
                // Verifica che il file CV sia stato selezionato
                if (!cvInput.files || !cvInput.files[0]) {
                    $('#form-message').html('<p style="color: red;">Per favore, carica un file CV.</p>');
                    document.body.removeChild(loadingOverlay);
                    return;
                }

                // Aggiungi il file CV al FormData
                formData.append('cv', cvInput.files[0]);
                
                $.ajax({
                    url: '../auth/registrazione_espositore_handler.php',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        try {
                            const data = typeof response === "string" ? JSON.parse(response) : response;
                            const message = data.message || "Messaggio non disponibile.";
                            const isSuccess = data.success === true;

                            $('#form-message').html(
                                `<p style="color: ${isSuccess ? 'rgb(74, 196, 207)' : 'red'};">${message}</p>`
                            );

                            if (isSuccess) {
                                form.reset();
                                clearCVSelection();
                                // Reindirizza alla pagina di login dopo 2 secondi
                                setTimeout(function() {
                                    window.location.href = 'login.php';
                                }, 2000);
                            }
                        } catch (e) {
                            console.error("Errore nel parsing della risposta:", e);
                            $('#form-message').html('<p style="color: red;">Errore nella risposta del server. Riprova più tardi.</p>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Errore AJAX:", status, error);
                        console.error("Risposta del server:", xhr.responseText);
                        
                        let errorMessage = 'Errore di comunicazione con il server.';
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.message) {
                                errorMessage = response.message;
                            }
                        } catch (e) {
                            console.error("Errore nel parsing della risposta di errore:", e);
                        }
                        
                        $('#form-message').html(`<p style="color: red;">${errorMessage}</p>`);
                    },
                    complete: function() {
                        // Rimuovi l'overlay di caricamento
                        document.body.removeChild(loadingOverlay);
                    }
                });
            } catch (error) {
                console.error('Errore durante la verifica:', error);
                $('#form-message').html('<p style="color: red;">Errore durante la verifica dei dati. Riprova più tardi.</p>');
                document.body.removeChild(loadingOverlay);
            }
        }
    });
});
</script>

<?php 
include_once '../template_footer.php'; 
?>