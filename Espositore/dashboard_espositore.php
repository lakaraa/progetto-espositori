<?php
include_once '../config.php';
include_once '../session.php';
include_once '../queries.php';
include_once '../template_header.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

$userId = $_SESSION['id_utente']; 
$nomeUtente = $_SESSION['nome'];  

// Ottieni le statistiche delle candidature
$candidatureInApprovazione = getCandidatureInApprovazioneCount($pdo, $_SESSION['id_utente']);
$candidatureAccettate = getCandidatureAccettateCount($pdo, $_SESSION['id_utente']);
$candidatureRifiutate = getCandidatureRifiutateCount($pdo, $_SESSION['id_utente']);

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
        <p class="text-center mb-5">Qui puoi visualizzare le tue candidature e i contributi accettati.</p>
        
        <h3>Statistiche Candidature</h3>

        <!-- Statistiche Candidature -->
        <div class="row mb-5">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card bg-warning-light">
                            <div class="card-body text-center">
                        <h5 class="card-title">In Approvazione</h5>
                        <h2 class="card-text">
                            <?= $candidatureInApprovazione ?>
                        </h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success-light">
                    <div class="card-body text-center">
                        <h5 class="card-title">Accettate</h5>
                        <h2 class="card-text">
                            <?= $candidatureAccettate ?>
                        </h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger-light">
                    <div class="card-body text-center">
                        <h5 class="card-title">Rifiutate</h5>
                        <h2 class="card-text">
                            <?= $candidatureRifiutate ?>
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Candidature -->
        <div class="row mb-5">
            <div class="col-md-12">
                <h3>Le Mie Candidature</h3>
                <table class="custom-table">
                    <thead>
                        <tr>                                 
                            <th>Manifestazione</th>
                            <th>Data Manifestazione</th>
                            <th>Titolo</th>
                            <th>Sintesi</th>
                            <th>Stato</th>
                        </tr>
                    </thead>
                    <tbody id="candidature">
                        <!-- Le candidature verranno caricate qui tramite AJAX -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Contributi Accettati -->
        <div class="row mb-5">
            <div class="col-md-12">
                <h3>I Miei Contributi</h3>
                <table class="custom-table">
                    <thead>
                        <tr>                                 
                            <th>Manifestazione</th>
                            <th>Data Manifestazione</th>
                            <th>Titolo</th>
                            <th>Sintesi</th>
                            <th>Stato</th>
                        </tr>
                    </thead>
                    <tbody id="contributi">
                        <!-- I contributi verranno caricate qui tramite AJAX -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Manifestazioni Disponibili -->
        <div class="row mb-6">
            <div class="col-md-12">
                <h3>Manifestazioni Disponibili</h3>
                <table class="custom-table">
                    <thead>
                        <tr>                                 
                            <th>Manifestazione</th>
                            <th>Data Inizio</th>
                            <th>Durata gg</th>
                            <th>Descrizione</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody id="manifestazioni">
                        <!-- Le manifestazioni verranno caricate qui tramite AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<script>
    $(document).ready(function() {
        // Carica le candidature
        $.ajax({
            url: 'get_candidature.php',
            method: 'GET',
            success: function(data) {
                $('#candidature').html(data);
            },
            error: function(xhr, status, error) {
                console.log("Errore AJAX candidature: ", error);
            }
        });

        // Carica i contributi
        $.ajax({
            url: 'get_contributi.php',
            method: 'GET',
            success: function(data) {
                $('#contributi').html(data);
            },
            error: function(xhr, status, error) {
                console.log("Errore AJAX contributi: ", error);
            }
        });

        // Carica le manifestazioni
        $.ajax({
            url: 'get_manifestazioni.php',
            method: 'GET',
            success: function(data) {
                $('#manifestazioni').html(data);
            },
            error: function(xhr, status, error) {
                console.log("Errore AJAX manifestazioni: ", error);
            }
        });
    });
</script>

<?php
include_once('../template_footer.php');
?>

<style>
.text-info {
    color: #17a2b8 !important;
}

.card {
    transition: transform 0.2s;
    margin-bottom: 20px;
    background-color: white;
}

.card-title, .card-text {
    color: rgb(34, 45, 79);
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.table th {
    background-color: #f8f9fa;
}
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
    background-color: rgb(34, 45, 79);
}

.custom-table tbody tr:nth-child(odd) {
    background-color: rgb(44, 56, 99);
}

.custom-table tbody tr:hover {
    background-color: rgb(166, 169, 181);
}

.btn-primary {
    background-color: #4CAF50;
    border: none;
    color: white;
    padding: 8px 16px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 14px;
    margin: 4px 2px;
    cursor: pointer;
    border-radius: 4px;
}

.btn-primary:hover {
    background-color: #45a049;
}

.text-success {
    color: #4CAF50 !important;
}

.text-warning {
    color: #ffc107 !important;
}

.text-danger {
    color: #dc3545 !important;
}

.text-info {
    color: #17a2b8 !important;
}

.bg-warning-light {
    background-color: #fff3cd;
}

.bg-success-light {
    background-color: #d4edda;
}

.bg-danger-light {
    background-color: #f8d7da;
}
</style>