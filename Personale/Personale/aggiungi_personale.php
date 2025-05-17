<?php
include_once("../../config.php");
include_once("../../queries.php");
include_once("../../session.php");

// Gestione invio del form via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', '../../error_log.txt'); // Percorso del file log

    // Pulizia buffer per evitare output HTML
    ob_start();
    header('Content-Type: application/json');
    ob_clean();

    // Recupera i dati inviati dal form
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $nome = $_POST['nome'] ?? '';
    $cognome = $_POST['cognome'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefono = $_POST['telefono'] ?? '';

    // Verifica che tutti i campi siano compilati
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

        // Aggiungi il personale alla tabella utente
        $result = addPersonale($pdo, $username, $password, $nome, $cognome, $email, $telefono);
        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Personale aggiunto con successo!' : 'Errore durante l\'aggiunta del personale.'
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

include_once("../../template_header.php");
?>

<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(../../resources/images/sfondo.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Gestione Personale</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="../dashboard_personale.php">Dashboard</a></li>
        <li><a href="gestisci_personale.php">Gestione Personale</a></li>
        <li class="active">Aggiungi Personale</li>
    </ul>
</section>

<!-- Main Content-->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Aggiungi Personale</h2>
        <p>Compila il modulo sottostante per aggiungere il personale.</p>

        <!-- Output del messaggio -->
        <div id="form-message"></div>

        <form class="form-aggiungi-personale" method="post" action="">
            <div class="row row-50">
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="personale-username">Username</label>
                        <input class="form-input" id="personale-username" type="text" name="username">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="personale-password">Password</label>
                        <input class="form-input" id="personale-password" type="password" name="password">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="personale-nome">Nome</label>
                        <input class="form-input" id="personale-nome" type="text" name="nome">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="personale-cognome">Cognome</label>
                        <input class="form-input" id="personale-cognome" type="text" name="cognome">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="personale-email">Email</label>
                        <input class="form-input" id="personale-email" type="email" name="email">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="personale-telefono">Telefono</label>
                        <input class="form-input" id="personale-telefono" type="text" name="telefono">
                    </div>
                </div>
            </div>
            <button class="button button-primary button-lg" type="submit">Aggiungi</button>
        </form>
    </div>
</section>

<!-- Script AJAX -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function () {
    $('.form-aggiungi-personale').on('submit', function (e) {
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
                        $('.form-aggiungi-personale')[0].reset();
                        // Rimuovi il messaggio dopo 3 secondi
                        setTimeout(() => {
                            $('#form-message').fadeOut(400, function() {
                                $(this).empty().show();
                            });
                        }, 3000);
                    }
                } catch (e) {
                    console.error("Errore JSON.parse:", e, response);
                    $('#form-message').html('<p style="color: red;">Risposta non valida dal server.</p>');
                }
            },
            error: function (xhr, status, error) {
                console.error("Errore AJAX:", status, error, xhr.responseText);
                $('#form-message').html('<p style="color: red;">Errore di comunicazione con il server.</p>');
            }
        });
    });
});
</script>

<?php
include_once("../../template_footer.php");
?>
