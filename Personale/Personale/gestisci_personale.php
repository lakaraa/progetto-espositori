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
        <li class="active">Gestione Personale</li>
    </ul>
</section>
<!-- Main Content-->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Gestione Personale</h2>
        <p>Aggiungi personale, cancella o modifica dati esistenti.</p>
        <div class="row row-50">
            <div class="col-md-4">
                <a class="button button-primary button-lg" href="aggiungi_personale.php">Aggiungi Personale</a>
            </div>
            <div class="col-md-4">
                <a class="button button-primary button-lg" href="modifica_personale.php">Modifica Personale</a>
            </div>
            <div class="col-md-4">
                <a class="button button-primary button-lg" href="cancella_personale.php">Cancella Personale</a>
            </div>
        </div>
    </div>
</section>
<?php
include '../../template_footer.php';
?>