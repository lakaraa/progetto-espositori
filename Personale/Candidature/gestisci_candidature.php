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
        <li class="active">Gestione Candidature</li>
    </ul>
</section>
<!-- Main Content-->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Gestione Candidature</h2>
        <p>Crea Candidature pere gli espositori, cancella, modifica e accetta le Candidature.</p>
        <div class="row row-50 justify-content-center">
        <div class="col-md-4">
            <a class="button button-primary button-lg w-100" href="aggiungi_manifestazione.php">Aggiungi Candidatura</a>
        </div>
        <div class="col-md-4">
            <a class="button button-primary button-lg w-100" href="modifica_manifestazione.php">Modifica Candidatura</a>
        </div>
        </div>
        <div class="row row-50 justify-content-center mt-3">
            <div class="col-md-4">
                <a class="button button-primary button-lg w-100" href="cancella_manifestazione.php">Cancella Candidatura</a>
            </div>
            <div class="col-md-4">
                <a class="button button-primary button-lg w-100" href="cancella_manifestazione.php">Accetta Candidature</a>
            </div>
        </div>
    </div>
</section>
<?php
include '../../template_footer.php';
?>