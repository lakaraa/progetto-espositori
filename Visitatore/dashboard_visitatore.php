<?php
include_once '../config.php';
include_once '../session.php';
//include_once '../queries.php';
include_once '../template_header.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

$userId = $_SESSION['id_utente']; 
$nomeUtente = $_SESSION['nome'];  

?>

<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(/progetto-espositori/resources/images/bg-breadcrumbs-07-1920x480.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Dashboard</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="../index.php">Home</a></li>
        <li class="active">Dashboard</li>
    </ul>
</section>

<section class="section py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-4">CIAO <?= htmlspecialchars($nomeUtente) ?></h2>
        <p class="text-center mb-5">Qui puoi visualizzare le prenotazioni effettuate, quelle in corso, quelle passate e quelle disponibili per te.</p>
        
        <!-- Prenotazioni in corso -->
        <div class="row mb-5">
            <div class="col-md-12">
                <h3 class="text-center">Prenotazioni in Corso</h3>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>                                 
                            <th>Manifestazione</th>
                            <th>Data Inizio</th>
                            <th>Durata gg</th>
                            <th>Area</th>
                            <th>Turno</th>
                            <th>Orario</th>
                        </tr>
                    </thead>
                    <tbody id="prenotazioni-in-corso">
                        <!-- Le prenotazioni in corso verranno caricate qui tramite AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Prenotazioni disponibili -->
        <div class="row mb-5">
            <div class="col-md-12">
                <h3 class="text-center">Prenotazioni Disponibili</h3>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>                                 
                            <th>Manifestazione</th>
                            <th>Data Inizio</th>
                            <th>Durata gg</th>
                            <th>Posti disponibili</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody id="prenotazioni-disponibili">
                        <!-- Le prenotazioni disponibili verranno caricate qui tramite AJAX -->>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Prenotazioni passate -->
        <div class="row mb-5">
            <div class="col-md-12">
                <h3 class="text-center">Prenotazioni Passate</h3>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Manifestazione</th>
                            <th>Data Inizio</th>
                            <th>Durata gg</th>
                            <th>Area</th>
                            <th>Turno</th>
                            <th>Orario</th>
                        </tr>
                    </thead>
                    <tbody id="prenotazioni-passate">
                        <!-- Le prenotazioni passate verranno caricate qui tramite AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<script>
    $(document).ready(function() {
        // Carica le prenotazioni in corso
        $.ajax({
            url: 'get_prenotazioni_in_corso.php',  // Percorso corretto al file PHP
            method: 'GET',
            success: function(data) {
                console.log("Dati in corso: ", data);  // Debug log
                $('#prenotazioni-in-corso').html(data);  // Popola la tabella con i dati ricevuti
            },
            error: function(xhr, status, error) {
                console.log("Errore AJAX: ", error);  // Mostra l'errore
            }
        });

        // Carica le prenotazioni disponibili
        $.ajax({
            url: 'get_prenotazioni_disponibili.php',
            method: 'GET',
            success: function (data) {
                const wrapper = $('<div>').html(data);
                const righe = wrapper.find('tr');
                const visualizzate = [];

                righe.each(function () {
                    const chiave = $(this).find('td:first').text().trim(); // manifestazione
                    if (!visualizzate.includes(chiave)) {
                        $('#prenotazioni-disponibili').append($(this));
                        visualizzate.push(chiave);
                    }
                });
            },
            error: function (xhr, status, error) {
                console.log("Errore AJAX disponibili: ", error);
            }
        });

        // Carica le prenotazioni passate
        $.ajax({
            url: 'get_prenotazioni_passate.php',  // Percorso corretto al file PHP
            method: 'GET',
            success: function(data) {
                $('#prenotazioni-passate').html(data);  // Popola la tabella con i dati ricevuti
            },
            error: function(xhr, status, error) {
                console.log("Errore AJAX: ", error);  // Mostra l'errore
            }
        });
    });

</script>
<?php
include_once('../template_footer.php');
?>
