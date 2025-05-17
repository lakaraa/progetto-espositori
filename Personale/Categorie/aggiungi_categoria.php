<?php
include_once("../../config.php");
include_once("../../queries.php");
include_once("../../session.php");

// Gestione delle richieste POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    ob_clean(); // Pulisce il buffer di output

    $nome = $_POST['nome'] ?? '';
    $descrizione = $_POST['descrizione'] ?? '';

    if (empty($nome) || empty($descrizione)) {
        echo json_encode([
            'success' => false,
            'message' => 'Tutti i campi sono obbligatori.'
        ]);
        exit;
    }

    try {
        $result = addCategoria($pdo, $nome, $descrizione);
        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Categoria creata con successo!' : 'Errore durante l\'aggiunta della categoria.'
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
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(../../resources/images/sfondo.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Aggiungi Categoria</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="../dashboard_personale.php">Dashboard</a></li>
        <li><a href="gestisci_categorie.php">Gestione Categorie</a></li>
        <li class="active">Aggiungi Categoria</li>
    </ul>
</section>

<!-- Main Content -->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Aggiungi Categoria</h2>
        <p>Compila il modulo sottostante per aggiungere una nuova categoria.</p>

        <!-- Output del messaggio -->
        <div id="form-message"></div>

        <form class="form-crea-categoria" method="post" action="">
            <div class="row row-50">
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="categoria-nome">Nome Categoria</label>
                        <input class="form-input" id="categoria-nome" type="text" name="nome" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="categoria-descrizione">Descrizione</label>
                        <input class="form-input" id="categoria-descrizione" type="text" name="descrizione" required>
                    </div>
                </div>
            </div>
            <button class="button button-primary button-lg" type="submit">Aggiungi Categoria</button>
        </form>
    </div>
</section>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function () {
    $('.form-crea-categoria').on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            url: '', // stessa pagina
            method: 'POST',
            data: $(this).serialize(),
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
                        $('.form-crea-categoria')[0].reset();
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