<?php

include_once('config.php'); 
include_once('queries.php'); 
include_once('template_header.php');

// Recupera ID dalla query string
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Recupera dati della manifestazione
$manifestazione = getManifestazioneById($pdo, $id);

//Recupera gli espositori legati alla manifestazione
$espositori = getEspositoriByManifestazioneTop4($pdo, $id);


?>

<?php if ($manifestazione): ?>
    <!-- Breadcrumbs-->
    <section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(images/bg-breadcrumbs-07-1920x480.jpg);">
        <div class="container">
            <h2 class="breadcrumbs-custom-title"><?php echo htmlspecialchars($manifestazione['Nome']); ?></h2>
        </div>
        <ul class="breadcrumbs-custom-path">
            <li><a href="index.php">Home</a></li>
            <li><a href="manifestazioni.php">Manifestazioni</a></li>
            <li class="active"><?php echo htmlspecialchars($manifestazione['Nome']); ?></li>
        </ul>
    </section>

    <!-- Dettagli Manifestazione -->
    <section class="section section-lg bg-default text-left">
        <div class="container">
            <h3 class="text-center mb-4"><?php echo htmlspecialchars($manifestazione['Nome']); ?></h3>
            <div class="text-center mb-4">
                <img src="/progetto-espositori/resources/images/event-placeholder.png" alt="Immagine Manifestazione" class="img-fluid" style="max-height: 300px;">
            </div>

            <h5><strong>Descrizione</strong></h5>
            <p><?php echo htmlspecialchars($manifestazione['Descrizione']); ?></p>

            <div class="row text-center mt-4 mb-4">
                <div class="col-md-4">
                    <h6><strong>Luogo</strong></h6>
                    <p><?php echo htmlspecialchars($manifestazione['Luogo']); ?></p>
                </div>
                <div class="col-md-4">
                    <h6><strong>Data</strong></h6>
                    <p><?php echo date('d/m/Y', strtotime($manifestazione['Data'])); ?></p>
                </div>
                <div class="col-md-4">
                    <h6><strong>Durata</strong></h6>
                    <p><?php echo htmlspecialchars($manifestazione['Durata']); ?> giorni</p>
                </div>
            </div>

            <h5 class="text-center mb-3"><strong>Tra gli espositori:</strong></h5>
            <div class="d-flex flex-wrap justify-content-center gap-2 mb-4">
                <?php foreach ($espositori as $e): ?>
                    <button class="btn btn-outline-dark rounded-pill px-4">
                        <?php echo htmlspecialchars($e['Nome']); ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="text-center mt-4">
                <a href="contributi.php?manifestazione_id=<?php echo $id; ?>" class="btn btn-primary">
                    Vedi tutti i contributi →
                </a>
            </div>
        </div>
    </section>

<?php else: ?>
    <div class="container text-center mt-5">
        <h4>Manifestazione non trovata.</h4>
    </div>
<?php endif; ?>

<?php include_once('template_footer.php'); ?>
