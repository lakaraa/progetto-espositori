<?php
include_once("../../config.php");
include_once("../../queries.php");
include_once("../../session.php");

// Gestione delle richieste POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    ob_clean(); // Pulisce il buffer di output

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $nome = $_POST['nome'] ?? '';
    $cognome = $_POST['cognome'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefono = $_POST['telefono'] ?? '';

    if (empty($username) || empty($password) || empty($nome) || empty($cognome) || empty($email) || empty($telefono)) {
        echo json_encode([
            'success' => false,
            'message' => 'Tutti i campi sono obbligatori.'
        ]);
        exit;
    }

    try {
        // Verifica se l'username esiste già
        if (usernameExists($pdo, $username)) {
            echo json_encode([
                'success' => false,
                'message' => 'Username già in uso. Scegli un altro username.'
            ]);
            exit;
        }

        // Verifica se l'email esiste già
        if (emailExists($pdo, $email)) {
            echo json_encode([
                'success' => false,
                'message' => 'Email già in uso. Scegli un\'altra email.'
            ]);
            exit;
        }

        // Hash della password prima di salvarla
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        $result = addVisitatore($pdo, $username, $passwordHash, $nome, $cognome, $email, $telefono);
        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Visitatore aggiunto con successo!' : 'Errore durante l\'aggiunta del visitatore.'
        ]);
        exit;
    } catch (PDOException $e) {
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
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(/progetto-espositori/resources/images/sfondo.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Aggiungi Visitatore</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="../dashboard_personale.php">Dashboard</a></li>
        <li><a href="gestisci_visitatore.php">Gestione Visitatori</a></li>
        <li class="active">Aggiungi Visitatore</li>
    </ul>
</section>

<!-- Main Content -->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Aggiungi Visitatore</h2>
        <p>Compila il modulo sottostante per registrare un nuovo visitatore.</p>

        <!-- Output del messaggio -->
        <div id="form-message"></div>

        <form class="form-crea-visitatore" method="post" action="">
            <div class="row row-50">
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="visitatore-username">Username</label>
                        <input class="form-input" id="visitatore-username" type="text" name="username" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="visitatore-password">Password</label>
                        <input class="form-input" id="visitatore-password" type="password" name="password" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="visitatore-nome">Nome</label>
                        <input class="form-input" id="visitatore-nome" type="text" name="nome" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="visitatore-cognome">Cognome</label>
                        <input class="form-input" id="visitatore-cognome" type="text" name="cognome" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="visitatore-email">Email</label>
                        <input class="form-input" id="visitatore-email" type="email" name="email" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="visitatore-telefono">Telefono</label>
                        <input class="form-input" id="visitatore-telefono" type="tel" name="telefono" required>
                    </div>
                </div>
            </div>
            <button class="button button-primary button-lg" type="submit">Aggiungi Visitatore</button>
        </form>
    </div>
</section>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function () {
    $('.form-crea-visitatore').on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            url: '', // stessa pagina
            method: 'POST',
            data: $(this).serialize(),
            success: function (response) {
                try {
                    const data = typeof response === "string" ? JSON.parse(response) : response;
                    const message = data.message || "Messaggio non disponibile.";
                    const isSuccess = data.success === true;

                    $('#form-message').html(
                        `<p style="color: ${isSuccess ? 'rgb(74, 196, 207)' : 'red'};">${message}</p>`
                    );

                    if (isSuccess) {
                        $('.form-crea-visitatore')[0].reset();
                        // Rimuovi il messaggio dopo 3 secondi
                        setTimeout(() => {
                            $('#form-message').fadeOut(400, function() {
                                $(this).empty().show();
                            });
                        }, 3000);
                    }
                } catch (e) {
                    console.error("Errore JSON.parse:", e);
                    $('#form-message').html('<p style="color: red;">Risposta non valida dal server.</p>');
                }
            },
            error: function (xhr, status, error) {
                console.error("Errore AJAX:", status, error);
                $('#form-message').html('<p style="color: red;">Errore di comunicazione con il server.</p>');
            }
        });
    });
});
</script>

<?php
include_once("../../template_footer.php");
?>