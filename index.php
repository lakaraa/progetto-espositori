<?php
include 'config.php'; 
include 'queries.php'; 

$manifestazioni = getManifestazioni($pdo); 

include 'template_header.php'; 
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
                    <img src="images/events-01-553x316.jpg" alt="<?php echo htmlspecialchars($row['Nome']); ?>" class="manifestazione-img">
                    <a class="button button-primary button-lg" href="contributions.php?id=<?php echo $row['Id_Manifestazione']; ?>">
                        <?php echo htmlspecialchars($row['Nome']); ?>
                    </a> <br> <br> <br>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php
include 'template_footer.php'; 
?>
