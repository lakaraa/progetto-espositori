<?php
include_once('config.php'); 
include_once('queries.php'); 
include_once('template_header.php');
$manifestazioni = getManifestazioniTop6($pdo); 
?>

<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(images/bg-breadcrumbs-07-1920x480.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Manifestazioni</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="index.php">Home</a></li>
        <li class="active">Manifestazioni</li>
    </ul>
</section>

<!-- Main Content-->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Manifestazioni</h2>
        <p>Scopri le varie manifestazioni che gestiamo. Clicca su una manifestazione per vedere la descrizione e i vari contribuenti.</p>
        <div class="row">
            <?php foreach ($manifestazioni as $row): ?>
                <div class="col-md-4">
                    <img src="/progetto-espositori/resources/images/events-01-553x316.jpg" alt="<?php echo htmlspecialchars($row['Nome']); ?>" class="manifestazione-img">
                    <a class="button button-primary button-lg" href="manifestazione.php?id=<?php echo $row['Id_Manifestazione']; ?>">
                        <?php echo htmlspecialchars($row['Nome']); ?>
                    </a> <br> <br> <br>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php
include_once('template_footer.php'); 
?> 
