<?php
include '../../config.php';
include '../../queries.php';
include '../../template_header.php';
?>
<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(images/bg-breadcrumbs-07-1920x480.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Dashboard</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="../dashboard_personale.php">DashBoard</a></li>
        <li class="active">Gestione Visitatori</li>
    </ul>
</section>
<!-- Main Content-->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Gestione Visitatori</h2>
        <p>Gestisci i visitatori registrati, aggiungi nuovi visitatori o modifica quelli esistenti.</p>
        <div class="row row-50">
            <div class="col-md-4">
                <a class="button button-primary button-lg" href="aggiungi_visitatore.php">Aggiungi Visitatore</a>
            </div>
            <div class="col-md-4">
                <a class="button button-primary button-lg" href="modifica_visitatore.php">Modifica Visitatore</a>
            </div>
            <div class="col-md-4">
                <a class="button button-primary button-lg" href="cancella_visitatore.php">Cancella Visitatore</a>
            </div>
        </div>
        
        
    </div>
</section>
<?php
include '../../template_footer.php';
?>