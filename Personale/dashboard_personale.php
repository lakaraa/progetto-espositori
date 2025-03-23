<?php
include_once '../config.php';
include_once '../queries.php';
include_once '../template_header.php'; 
?>

<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(/progetto-espositori/resources/images/bg-breadcrumbs-07-1920x480.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Dashboard</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="../index.html">Home</a></li>
        <li class="active">Dashboard</li>
    </ul>
</section>
<!-- Dashboard Section-->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Dashboard Personale</h2>
        <div class="row row-50">
            <div class="col-md-4 dashboard-button">
                <a class="button button-primary button-lg" href="Prenotazioni/gestisci_prenotazione.php">Gestisci Prenotazioni</a>
            </div>
            <div class="col-md-4 dashboard-button">
                <a class="button button-primary button-lg" href="Candidature/gestisci_candidature.php">Gestisci Candidature</a>
            </div>
            <div class="col-md-4 dashboard-button">
                <a class="button button-primary button-lg" href="Aree/gestisci_aree.php">Gestisci Aree</a>
            </div>
            <div class="col-md-4 dashboard-button">
                <a class="button button-primary button-lg" href="Personale/gestisci_personale.php">Gestisci Personale</a>
            </div>
            <div class="col-md-4 dashboard-button">
                <a class="button button-primary button-lg" href="Espositori/gestisci_espositori.php">Gestisci Espositori</a>
            </div>
            <div class="col-md-4 dashboard-button">
                <a class="button button-primary button-lg" href="Visitori/gestisci_visitatore.php">Gestisci Visitatori</a>
            </div>
            <div class="col-md-4 dashboard-button">
                <a class="button button-primary button-lg" href="Manifestazioni/gestisci_manifestazione.php">Gestisci Manifestazioni</a>
            </div>
            <div class="col-md-4 dashboard-button">
                <a class="button button-primary button-lg" href="Statiche/statiche.php">Statistiche</a>
            </div>
        </div>
    </div>
</section>
<?php
include_once '../template_footer.php'; 
?>