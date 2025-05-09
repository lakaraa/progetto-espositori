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
        <li class="active">Gestione Categorie</li>
    </ul>
</section>
<!-- Main Content-->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Gestione Categorie</h2>
        <p>Gestisci le categorie disponibili, aggiungi nuove categorie o modifica quelle esistenti.</p>
        <div class="row row-50">
            <div class="col-md-4">
                <a class="button button-primary button-lg" href="aggiungi_categoria.php">Aggiungi Categoria</a>
            </div>
            <div class="col-md-4">
                <a class="button button-primary button-lg" href="modifica_categoria.php">Modifica Categoria</a>
            </div>
            <div class="col-md-4">
                <a class="button button-primary button-lg" href="cancella_categoria.php">Cancella Categoria</a>
            </div>
        </div>
    </div>
</section>
<?php
include '../../template_footer.php';
?>