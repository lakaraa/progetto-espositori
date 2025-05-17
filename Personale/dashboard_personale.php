<?php
include_once '../config.php';
include_once '../queries.php';
include_once '../session.php';

// Verifica che l'utente sia loggato e sia personale
if (!isset($_SESSION['id_utente']) || $_SESSION['ruolo'] !== 'Personale') {
    // Se non è loggato o non è personale, reindirizza alla pagina di login
    header('Location: ../pages/login.php');
    exit;
}

include_once '../template_header.php';

$nomeUtente = $_SESSION['nome'];  

?>

<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(../resources/images/sfondo.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Dashboard</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="../index.php">Home</a></li>
        <li class="active">Dashboard</li>
    </ul>
</section>

<!-- Dashboard Section-->
<section class="section section-lg bg-default">
    <div class="container">
        <div class="text-center">
            <h2 class="heading-decoration"><span class="text-primary">Buongiorno</span> <?= htmlspecialchars($nomeUtente) ?></h2>
            <p class="subtitle">Gestisci tutte le funzionalità del sistema da un'unica interfaccia</p>
        </div>

        <div class="row row-30 row-md-50 justify-content-center mt-5">
            <!-- Prenotazioni -->
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="dashboard-card">
                    <a class="dashboard-card-link" href="Prenotazioni/gestisci_prenotazione.php">
                        <div class="dashboard-card-icon">
                            <span class="icon-lg mdi mdi-calendar-check"></span>
                        </div>
                        <h5 class="dashboard-card-title">Prenotazioni</h5>
                        <p class="dashboard-card-text">Gestisci tutte le prenotazioni</p>
                    </a>
                </div>
            </div>
            
            <!-- Candidature -->
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="dashboard-card">
                    <a class="dashboard-card-link" href="Candidature/gestisci_candidature.php">
                        <div class="dashboard-card-icon">
                            <span class="icon-lg mdi mdi-account-multiple-plus"></span>
                        </div>
                        <h5 class="dashboard-card-title">Candidature</h5>
                        <p class="dashboard-card-text">Gestisci le candidature</p>
                    </a>
                </div>
            </div>
            
            <!-- Aree -->
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="dashboard-card">
                    <a class="dashboard-card-link" href="Aree/gestisci_aree.php">
                        <div class="dashboard-card-icon">
                            <span class="icon-lg mdi mdi-map-marker-multiple"></span>
                        </div>
                        <h5 class="dashboard-card-title">Aree</h5>
                        <p class="dashboard-card-text">Gestisci le aree espositive</p>
                    </a>
                </div>
            </div>
            
            <!-- Personale (con omino semplice) -->
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="dashboard-card">
                    <a class="dashboard-card-link" href="Personale/gestisci_personale.php">
                        <div class="dashboard-card-icon">
                            <span class="icon-lg mdi mdi-account"></span>
                        </div>
                        <h5 class="dashboard-card-title">Personale</h5>
                        <p class="dashboard-card-text">Gestisci il personale</p>
                    </a>
                </div>
            </div>
            
            <!-- Espositori -->
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="dashboard-card">
                    <a class="dashboard-card-link" href="Espositori/gestisci_espositori.php">
                        <div class="dashboard-card-icon">
                            <span class="icon-lg mdi mdi-store"></span>
                        </div>
                        <h5 class="dashboard-card-title">Espositori</h5>
                        <p class="dashboard-card-text">Gestisci gli espositori</p>
                    </a>
                </div>
            </div>
            
            <!-- Visitatori -->
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="dashboard-card">
                    <a class="dashboard-card-link" href="Visitori/gestisci_visitatore.php">
                        <div class="dashboard-card-icon">
                            <span class="icon-lg mdi mdi-account-star"></span>
                        </div>
                        <h5 class="dashboard-card-title">Visitatori</h5>
                        <p class="dashboard-card-text">Gestisci i visitatori</p>
                    </a>
                </div>
            </div>
            
            <!-- Manifestazioni (icona mondo/globo) -->
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="dashboard-card">
                    <a class="dashboard-card-link" href="Manifestazioni/gestisci_manifestazione.php">
                        <div class="dashboard-card-icon">
                            <span class="icon-lg mdi mdi-earth"></span>
                        </div>
                        <h5 class="dashboard-card-title">Manifestazioni</h5>
                        <p class="dashboard-card-text">Gestisci le manifestazioni</p>
                    </a>
                </div>
            </div>
            
            <!-- Statistiche -->
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="dashboard-card">
                    <a class="dashboard-card-link" href="Statistiche/statistiche.php">
                        <div class="dashboard-card-icon">
                            <span class="icon-lg mdi mdi-chart-bar"></span>
                        </div>
                        <h5 class="dashboard-card-title">Statistiche</h5>
                        <p class="dashboard-card-text">Visualizza le statistiche</p>
                    </a>
                </div>
            </div>
            
            <!-- Messaggi -->
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="dashboard-card">
                    <a class="dashboard-card-link" href="Messaggi/messaggi.php">
                        <div class="dashboard-card-icon">
                            <span class="icon-lg mdi mdi-message-text"></span>
                        </div>
                        <h5 class="dashboard-card-title">Messaggi</h5>
                        <p class="dashboard-card-text">Gestisci i messaggi</p>
                    </a>
                </div>
            </div>
            
            <!-- Categorie -->
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="dashboard-card">
                    <a class="dashboard-card-link" href="Categorie/gestisci_categorie.php">
                        <div class="dashboard-card-icon">
                            <span class="icon-lg mdi mdi-menu"></span>
                        </div>
                        <h5 class="dashboard-card-title">Categorie</h5>
                        <p class="dashboard-card-text">Gestisci le categorie</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .dashboard-card {
        background: white;
        border-radius: 10px;
        padding: 25px 15px;
        margin-bottom: 30px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    
    .dashboard-card-link {
        color: inherit;
        text-decoration: none;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    
    .dashboard-card-icon {
        font-size: 2.5rem;
        color: #4e66f8;
        margin-bottom: 15px;
    }
    
    .dashboard-card-title {
        font-weight: 600;
        margin-bottom: 10px;
        color: #2a2a2a;
    }
    
    .dashboard-card-text {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 0;
    }
    
    .heading-decoration {
        position: relative;
        padding-bottom: 15px;
        margin-bottom: 25px;
    }
    
    .heading-decoration:after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 3px;
        background: linear-gradient(to right, #4e66f8, #6f42c1);
    }
    
    .subtitle {
        color: #6c757d;
        font-size: 1.1rem;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }
</style>

<?php
include_once '../template_footer.php'; 
?>