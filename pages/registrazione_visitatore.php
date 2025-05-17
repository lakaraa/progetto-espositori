<?php 
include_once '../session.php';
include_once '../template_header.php'; 
?>
<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(../resources/images/sfondo.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Registrazione Visitatore</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="index.php">Home</a></li>
        <li class="active">Registrazione Visitatore</li>
    </ul>
</section>

<!-- Registration Section-->
<section class="section section-lg bg-default">
    <div class="container">
        <div class="row row-50 justify-content-center">
            <div class="col-md-10 col-lg-8">
                <h3>Registrati come Visitatore</h3>
                <div id="form-message"></div>
                <form method="post" action="../auth/registrazione_visitatore_handler.php" id="registration-form">
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
                                <input class="form-input" id="registration-first-name" type="text" name="first_name" required>
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
                        <div class="col-md-12 d-flex justify-content-center">
                            <button class="button button-primary" type="submit">Registrati</button>
                        </div>
                    </div>
                </form>
                <div class="col-md-12">
                    <div class="text-center">
                        <br>
                        <p>Hai già un account? <a href="login.php"> Sì</a></p>
                        Non hai un account? <a href="registrazione_espositore.php">Registrati come espositore</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
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
                
                $.ajax({
                    url: '../auth/registrazione_visitatore_handler.php',
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