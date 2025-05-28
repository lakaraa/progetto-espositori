<?php
error_reporting(E_ALL); // Report all errors and warnings
ini_set('display_errors', 1); // Display errors on the screen

include_once('../config.php');
include_once('../queries.php');
include_once('../template_header.php'); 

$manifestazioni = getManifestazioni($pdo); 
?>

<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(../resources/images/sfondo.jpg);">
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
        <p>Scopri tutte le manifestazioni che gestiamo. Clicca su una manifestazione per vedere maggiori dettagli e contribuenti.</p>
        <div class="row row-30">
            <?php foreach ($manifestazioni as $row): ?>
                <div class="col-md-6 col-lg-4">
                    <!-- Card Modern-->
                    <article class="card-modern">
                        <div class="card-modern-body">
                            <h4 class="card-modern-title">
                                <a href="manifestazione.php?id=<?php echo $row['Id_Manifestazione']; ?>">
                                    <?php echo htmlspecialchars($row['Nome']); ?>
                                </a>
                            </h4>
                            <p class="card-modern-text"><?php echo htmlspecialchars($row['Descrizione'] ?? 'Descrizione non disponibile'); ?></p>
                            <div class="card-modern-meta">
                                <div class="card-modern-meta-item">
                                    <span class="icon mdi mdi-map-marker"></span>
                                    <span><?php echo htmlspecialchars($row['Luogo'] ?? 'Luogo non specificato'); ?></span>
                                </div>
                            </div>
                            <a class="button button-primary button-sm" href="manifestazione.php?id=<?php echo $row['Id_Manifestazione']; ?>">Scopri di più</a>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<style>
    /* Stili personalizzati per le card */
    .card-modern {
        position: relative;
        text-align: left;
        background: #fff;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        transition: all 0.3s;
        height: 100%;
        display: flex;
        flex-direction: column;
        border-radius: 8px;
        overflow: hidden;
        color: #000; /* Testo nero */
    }
    
    .card-modern:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }
    
    .card-modern-figure {
        overflow: hidden;
    }
    
    .card-modern-figure img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        transition: transform 0.3s;
    }
    
    .card-modern:hover .card-modern-figure img {
        transform: scale(1.05);
    }
    
    .card-modern-body {
        padding: 25px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    
    .card-modern-title {
        font-weight: 600;
        font-size: 1.25rem;
        margin-bottom: 12px;
        color: #000; /* Testo nero */
    }
    
    .card-modern-title a {
        color: inherit;
        text-decoration: none;
        transition: color 0.3s;
    }
    
    .card-modern-title a:hover {
        color: #4299e1;
    }
    
    .card-modern-text {
        color: #555; /* Testo grigio scuro */
        margin-bottom: 20px;
        flex-grow: 1;
    }
    
    .card-modern-meta {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        font-size: 0.875rem;
        color: #777;
    }
    
    .card-modern-meta-item {
        display: flex;
        align-items: center;
        margin-right: 15px;
    }
    
    .card-modern-meta-item .icon {
        margin-right: 5px;
        color: #4299e1;
    }
    
    .button-sm {
        padding: 8px 20px;
        align-self: flex-start;
    }
    
    .row-30 {
        margin-left: -15px;
        margin-right: -15px;
    }
    
    .row-30 > [class*="col-"] {
        padding-left: 15px;
        padding-right: 15px;
        margin-bottom: 30px;
    }
</style>

<?php
include_once('../template_footer.php'); 
?>