<?php
include_once '../../config.php';
include_once '../../queries.php';
include_once '../../template_header.php';
?>
<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(images/bg-breadcrumbs-07-1920x480.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Dashboard</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="../dashboard_personale.php">DashBoard</a></li>
        <li class="active">Gestione Espositori</li>
    </ul>
</section>
<!-- Main Content-->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Gestione Espositori</h2>
        <p>Aggiungi Espositori per i contribuenti, cancella o modifica le Espositori esistenti.</p>
        <div class="row row-50">
            <div class="col-md-4">
                <a class="button button-primary button-lg" href="aggiungi_espositore.php">Aggiungi Espositori</a>
            </div>
            <div class="col-md-4">
                <a class="button button-primary button-lg" href="modifica_espositore.php">Modifica Espositori</a>
            </div>
            <div class="col-md-4">
                <a class="button button-primary button-lg" href="cancella_espositore.php">Cancella Espositori</a>
            </div>
        </div>
    </div>
</section>
<?php
include_once '../../template_footer.php';
?>