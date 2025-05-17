<?php
include_once '../config.php';
include_once '../session.php';

// Verifica che l'utente sia loggato e sia espositore
if (!isset($_SESSION['id_utente']) || $_SESSION['ruolo'] !== 'Espositore') {
    // Se non è loggato o non è espositore, reindirizza alla pagina di login
    header('Location: ../pages/login.php');
    exit;
}

include_once '../queries.php';
include_once '../template_header.php';

// Abilita la visualizzazione degli errori
ini_set('display_errors', 1);
error_reporting(E_ALL);


try {
    $userId = $_SESSION['id_utente']; 
    $nomeUtente = $_SESSION['nome'];  

    // Ottieni le statistiche delle candidature
    $candidatureInApprovazione = getCandidatureInApprovazioneCount($pdo, $userId);
    $candidatureAccettate = getCandidatureAccettateCount($pdo, $userId);
    $candidatureRifiutate = getCandidatureRifiutateCount($pdo, $userId);
    $candidatureTotali = $candidatureInApprovazione + $candidatureAccettate + $candidatureRifiutate;

    // Ottieni il numero totale di contributi
    $contributiTotali = getContributiTotaliCount($pdo, $userId);

} catch (Exception $e) {
    error_log("Errore nella dashboard espositore: " . $e->getMessage());
    $candidatureInApprovazione = $candidatureAccettate = $candidatureRifiutate = $candidatureTotali = $contributiTotali = 0;
}
?>

<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(../resources/images/sfondo.jpg);">
        <div class="container">
        <h2 class="breadcrumbs-custom-title">Dashboard Espositore</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="../index.php">Home</a></li>
        <li class="active">Dashboard</li>
    </ul>
</section>

<!-- Dashboard Section-->
<section class="section section-lg bg-default">
    <div class="container">
        <!-- Welcome Banner -->
        <div class="container">
        <h2 class="text-center mb-4"><span class="text-primary">CIAO</span> <?= htmlspecialchars($nomeUtente) ?></h2>
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

        <h3 class="mb-4">Azioni Rapide</h3>
        Seleziona una delle seguenti opzioni per gestire le tue attività:

        <!-- Main Sections -->
        <div class="row actions-row justify-content-center">
            <!-- Candidature Card -->
            <div class="col-md-4 mb-4">
                <div class="action-card" onclick="window.location.href='candidature.php'">
                    <div class="action-card-body text-center">
                        <div class="action-icon-wrapper">
                            <i class="material-icons action-icon">Candidature</i>
                        </div>
                        <h4 class="action-title">Le Mie Candidature</h4>
                        <p class="action-text">Visualizza e gestisci tutte le tue candidature</p>
                    </div>
                </div>
            </div>

            <!-- Contributi Card -->
            <div class="col-md-4 mb-4">
                <div class="action-card" onclick="window.location.href='contributi.php'">
                    <div class="action-card-body text-center">
                        <div class="action-icon-wrapper">
                            <i class="material-icons action-icon">Contributi</i>
                        </div>
                        <h4 class="action-title">I Miei Contributi</h4>
                        <p class="action-text">Gestisci i tuoi contributi alle manifestazioni</p>
                    </div>
                </div>
            </div>

            <!-- Manifestazioni Card -->
            <div class="col-md-4 mb-4">
                <div class="action-card" onclick="window.location.href='manifestazioni.php'">
                    <div class="action-card-body text-center">
                        <div class="action-icon-wrapper">
                            <i class="material-icons action-icon">Manifestazioni</i>
                        </div>
                        <h4 class="action-title">Manifestazioni</h4>
                        <p class="action-text">Scopri e candidati alle manifestazioni</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>

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


/* Action Cards */
.actions-row {
    margin-bottom: 2rem;
}

.action-card {
    background: white;
    border-radius: 12px;
    padding: 2rem 1.5rem;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    height: 100%;
    cursor: pointer;
    border: none;
    position: relative;
    overflow: hidden;
}

.action-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.12);
}

.action-card:after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, #4e66f8, #6f42c1);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.action-card:hover:after {
    opacity: 1;
}

.action-icon-wrapper {
    width: 70px;
    height: 70px;
    margin: 0 auto 1.5rem;
    background: rgba(78, 102, 248, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.action-card:hover .action-icon-wrapper {
    background: rgba(78, 102, 248, 0.2);
    transform: scale(1.1);
}

.action-icon {
    font-size: 2.2rem;
    color: #4e66f8;
}

.action-title {
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 0.8rem;
    color: #2a2a2a;
}

.action-text {
    color: #6c757d;
    font-size: 0.95rem;
    margin-bottom: 1.5rem;
    line-height: 1.5;
}

.action-link {
    color: #4e66f8;
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.action-card:hover .action-link {
    color: #3a56e0;
}

.action-link i {
    margin-left: 5px;
    transition: transform 0.3s ease;
}

.action-card:hover .action-link i {
    transform: translateX(5px);
}

/* Responsive adjustments */
@media (max-width: 992px) {
    .welcome-title {
        font-size: 1.8rem;
    }
    
    .stat-number {
        font-size: 1.8rem;
    }
    
    .action-title {
        font-size: 1.2rem;
    }
}

@media (max-width: 768px) {
    .welcome-banner {
        padding: 1.5rem;
    }
    
    .stat-card, .action-card {
        margin-bottom: 1.5rem;
    }
}
</style>

<?php
include_once('../template_footer.php');
?>