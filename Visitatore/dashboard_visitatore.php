<?php
include_once '../config.php';
include_once '../session.php';

// Verifica che l'utente sia loggato e sia visitatore
if (!isset($_SESSION['id_utente']) || $_SESSION['ruolo'] !== 'Visitatore') {
    // Se non è loggato o non è visitatore, reindirizza alla pagina di login
    header('Location: ../pages/login.php');
    exit;
}

//include_once '../queries.php';
include_once '../template_header.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

$userId = $_SESSION['id_utente']; 
$nomeUtente = $_SESSION['nome'];  

?>

<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(../resources/images/bg-breadcrumbs-07-1920x480.jpg);">
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
        <h2 class="text-center mb-4"><span class="text-primary">Ciao</span> <?= htmlspecialchars($nomeUtente) ?></h2>
        <p class="text-center mb-5">Qui puoi visualizzare le prenotazioni effettuate, quelle in corso, quelle passate e quelle disponibili per te.</p>
        
        <!-- Prenotazioni in corso -->
        <div class="row mb-5">
            <div class="col-md-12">
                <h3>Prenotazioni in Corso</h3>
                <table class="custom-table">
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
                <h3>Prenotazioni Disponibili</h3>
                <table class="custom-table">
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
                        <!-- Le prenotazioni disponibili verranno caricate qui tramite AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Prenotazioni passate -->
        <div class="row mb-5">
            <div class="col-md-12">
                <h3>Prenotazioni Passate</h3>
                <table class="custom-table">
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
                        // Mantieni il link originale con l'ID della manifestazione
                        const prenotaLink = $(this).find('a');
                        const href = prenotaLink.attr('href');
                        if (href) {
                            prenotaLink.attr('href', href);
                            // Aggiungi un gestore di eventi per prevenire il comportamento predefinito
                            prenotaLink.on('click', function(e) {
                                e.preventDefault();
                                window.location.href = href;
                            });
                        }
                        
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







<style>
.custom-table {
    font-family: Arial, Helvetica, sans-serif;
    border-collapse: collapse;
    width: 100%;
    background-color: transparent;
    color: white;
}

.custom-table th,
.custom-table td {
    border: none;
    padding: 12px;
}

.custom-table thead th {
    background-color: transparent;
    color: white;
    font-weight: bold;
}

.custom-table tbody tr:nth-child(even) {
  background-color: rgb(34, 45, 79); /* stesso colore dello sfondo */
}

.custom-table tbody tr:nth-child(odd) {
  background-color: rgb(44, 56, 99); /* una tonalità più chiara/sicura */
}

.custom-table tbody tr:hover {
  background-color: rgb(166, 169, 181); /* leggera variazione per hover */
}
</style>