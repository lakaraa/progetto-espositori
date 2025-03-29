<?php
include_once("../../config.php");
include_once("../../queries.php");
include_once("../../session.php");
include_once("../../template_header.php");

// Recupera le manifestazioni dal database
$manifestazioni = getManifestazioni($pdo);

// Gestione invio del form via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $idManifestazione = $_POST['manifestazione'] ?? '';
    $nome = $_POST['nome'] ?? '';
    $capienza = $_POST['capienza'] ?? '';
    $descrizione = $_POST['descrizione'] ?? '';

    if (empty($idManifestazione) || empty($nome) || empty($capienza) || empty($descrizione)) {
        echo json_encode([
            'success' => false,
            'message' => 'Tutti i campi sono obbligatori.'
        ]);
        exit;
    }

    try {
        $result = addArea($pdo, $nome, $descrizione, $capienza, $idManifestazione);
        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Area creata con successo!' : 'Errore durante l\'aggiunta dell\'area.'
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
?>

<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(/progetto-espositori/resources/images/bg-breadcrumbs-07-1920x480.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Crea Aree</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="../dashboard_personale.php">Dashboard</a></li>
        <li><a href="gestisci_aree.php">Gestione Aree</a></li>
        <li class="active">Crea Area</li>
    </ul>
</section>

<!-- Main Content-->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Crea Aree</h2>
        <p>Compila il modulo sottostante per creare le aree.</p>

        <!-- Output del messaggio -->
        <div id="form-message"></div>

        <form class="form-crea-area" method="post" action="">
            <div class="row row-50">
                <div class="col-md-6">
                    <div class="form-wrap">
                        <select class="form-input" id="area-manifestazione" name="manifestazione">
                            <option value="">Seleziona Manifestazione</option>
                            <?php foreach ($manifestazioni as $manifestazione): ?>
                                <option value="<?php echo htmlspecialchars($manifestazione['Id_Manifestazione']); ?>" style="color: black; background-color: white;">
                                    <?php echo htmlspecialchars($manifestazione['Nome']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="area-nome">Nome</label>
                        <input class="form-input" id="area-nome" type="text" name="nome">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="area-capienza">Capienza Massima</label>
                        <input class="form-input" id="area-capienza" type="number" name="capienza">
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-wrap">
                        <label class="form-label" for="area-descrizione">Descrizione</label>
                        <textarea class="form-input" id="area-descrizione" name="descrizione"></textarea>
                    </div>
                </div>
            </div>
            <button class="button button-primary button-lg" type="submit">Crea</button>
        </form>
    </div>
</section>

<!-- Script AJAX -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function () {
    $('.form-crea-area').on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            url: '', // stessa pagina
            method: 'POST',
            data: $(this).serialize(),
            success: function (response) {
    console.log("RISPOSTA RAW:", response); // << stampa nel browser
    try {
        const data = typeof response === "string" ? JSON.parse(response) : response;
        const message = data.message || "Messaggio non disponibile.";
        const isSuccess = data.success === true;

        $('#form-message').html(
            `<p style="color: ${isSuccess ? 'green' : 'red'};">${message}</p>`
        );

        if (isSuccess) {
            $('.form-crea-area')[0].reset();
        }
    } catch (e) {
        console.error("Errore JSON.parse:", e);
        $('#form-message').html('<p style="color: red;">Risposta non valida dal server.</p>');
    }
}
,
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
