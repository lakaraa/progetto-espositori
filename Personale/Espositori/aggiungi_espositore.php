<?php
include_once("../../config.php");
include_once("../../queries.php");
include_once("../../session.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

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

    // Controlla la dimensione del file (16 MB massimo)
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

    // Crea la directory uploads se non esiste
    $upload_dir = '../../uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Salva il file con un nome personalizzato
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

    // Inserimento nel database tramite la funzione
    try {
        $result = addEspositore($pdo, $username, $hashed_password, $first_name, $last_name, $email, $phone, $qualification, $cv_path);
        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Espositore aggiunto con successo!' : 'Errore durante l\'aggiunta dell\'espositore.'
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

<!-- Breadcrumbs -->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(images/bg-breadcrumbs-07-1920x480.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Aggiungi Espositore</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="dashboardPersonale.html">Dashboard</a></li>
        <li><a href="gestioneEspositori.html">Gestione Espositori</a></li>
        <li class="active">Aggiungi Espositore</li>
    </ul>
</section>
<!-- Main Content -->
<section class="section section-lg bg-default">
    <div class="container">
        <div class="row row-50 justify-content-center">
            <div class="col-md-10 col-lg-8">
                <h3>Aggiungi Espositore</h3>
        <form class="form-aggiungi-espositore" method="post" enctype="multipart/form-data">
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
                        <label class="form-label" for="registration-first-name">First Name</label>
                        <input class="form-input" id="registration-first-name" type="text" name="first_name" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-wrap">
                        <label class="form-label" for="registration-last-name">Last Name</label>
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
                        <label class="form-label" for="registration-phone">Phone</label>
                        <input class="form-input" id="registration-phone" type="text" name="phone" required>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-wrap">
                        <label class="form-label" for="registration-qualification">Qualification</label>
                        <select class="form-input" id="registration-qualification" name="qualification" required>
                            <option value="professional" style="color: black; background-color: white;">Professional</option>
                            <option value="amateur" style="color: black; background-color: white;">Amateur</option>
                            <option value="expert" style="color: black; background-color: white;">Expert Non-Professional</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-wrap">
                        <label class="form-label" for="registration-cv">Curriculum Vitae (PDF)</label><br>
                        <input class="form-input" id="registration-cv" type="file" name="cv" accept=".pdf" required>
                    </div>
                </div>
                <div class="col-md-12">
                    <button class="button button-primary" type="submit">Aggiungi</button>
                </div>
            </div>
        </form>
        </div>
        </div>
    </div>
</section>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
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
                const message = response.message || 'Errore sconosciuto.';
                const isSuccess = response.success === true;

                $('#form-message').html(
                    `<p style="color: ${isSuccess ? 'green' : 'red'};">${message}</p>`
                );

                if (isSuccess) {
                    $('.form-aggiungi-espositore')[0].reset();
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