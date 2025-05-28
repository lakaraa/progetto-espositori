<?php
error_reporting(E_ALL); // Report all errors and warnings
ini_set('display_errors', 1); // Display errors on the screen

include_once '../../config.php';
include_once '../../queries.php';
include_once '../../template_header.php';
?>
<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(../../resources/images/sfondo.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Dashboard</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="../dashboard_personale.php">DashBoard</a></li>
        <li class="active">Gestione Espositori</li>
    </ul>
</section>
<section class="section section-lg bg-default">
    <div class="container">
        <div class="text-center">
            <h2 class="heading-decoration"><span class="text-primary">Gestione</span> Espositori</h2>
            <p class="subtitle">Aggiungi, modifica o elimina gli espositori del sistema</p>
        </div>
        
        <div class="row row-30 row-md-50 justify-content-center mt-5">
            <!-- Aggiungi Espositori -->
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="dashboard-card">
                    <a class="dashboard-card-link" href="aggiungi_espositore.php">
                        <div class="dashboard-card-icon">
                            <span class="icon-lg mdi mdi-account-plus"></span>
                        </div>
                        <h5 class="dashboard-card-title">Aggiungi</h5>
                        <p class="dashboard-card-text">Inserisci nuovi espositori</p>
                    </a>
                </div>
            </div>
            
            <!-- Modifica Espositori -->
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="dashboard-card">
                    <a class="dashboard-card-link" href="modifica_espositore.php">
                        <div class="dashboard-card-icon">
                            <span class="icon-lg mdi mdi-account-alert"></span>
                        </div>
                        <h5 class="dashboard-card-title">Modifica</h5>
                        <p class="dashboard-card-text">Gestisci gli espositori</p>
                    </a>
                </div>
            </div>
            
            <!-- Cancella Espositori -->
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="dashboard-card">
                    <a class="dashboard-card-link" href="cancella_espositore.php">
                        <div class="dashboard-card-icon">
                            <span class="icon-lg mdi mdi-account-remove"></span>
                        </div>
                        <h5 class="dashboard-card-title">Elimina</h5>
                        <p class="dashboard-card-text">Rimuovi espositori</p>
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
include_once '../../template_footer.php';
?>