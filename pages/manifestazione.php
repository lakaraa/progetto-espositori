<?php
include_once('../config.php'); 
include_once('../queries.php'); 
include_once('../template_header.php');

// Recupera ID dalla query string
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Recupera dati della manifestazione
$manifestazione = getManifestazioneById($pdo, $id);

// Recupera gli espositori legati alla manifestazione
$espositori = getEspositoriByManifestazioneTop4($pdo, $id);
?>

<?php if ($manifestazione): ?>
    <!-- Breadcrumbs-->
    <section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(/progetto-espositori/resources/images/sfondo.jpg);">
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
    <section class="section section-lg bg-default">
        <div class="container">
            <div class="dashboard-detail-card">
                <div class="text-center mb-5">
                    <h3 class="heading-decoration"><span class="text-primary"><?php echo htmlspecialchars($manifestazione['Nome']); ?></span></h3>
                </div>

                <div class="detail-section mb-5">
                    <h5 class="detail-title">Descrizione</h5>
                    <div class="detail-content">
                        <p style="color: black;"><?php echo htmlspecialchars($manifestazione['Descrizione']); ?></p>
                    </div>
                </div>

                <div class="row detail-info-boxes mb-5">
                    <div class="col-md-4">
                        <div class="info-box">
                            <div class="info-icon">
                                <span class="mdi mdi-map-marker"></span>
                            </div>
                            <h6 style="color: #4e66f8;">Luogo</h6>
                            <p style="color: black;"><?php echo htmlspecialchars($manifestazione['Luogo']); ?></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box">
                            <div class="info-icon">
                                <span class="mdi mdi-calendar"></span>
                            </div>
                            <h6 style="color: #4e66f8;">Data</h6>
                            <p style="color: black;"><?php echo date('d/m/Y', strtotime($manifestazione['Data'])); ?></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box">
                            <div class="info-icon">
                                <span class="mdi mdi-clock"></span>
                            </div>
                            <h6 style="color: #4e66f8;">Durata</h6>
                            <p style="color: black;"><?php echo htmlspecialchars($manifestazione['Durata']); ?> giorni</p>
                        </div>
                    </div>
                </div>

                <div class="detail-section">
                    <h5 class="detail-title text-center">Espositori partecipanti</h5>
                    <div class="expositors-tags">
                        <?php foreach ($espositori as $e): ?>
                            <a href="espositore.php?id=<?php echo urlencode($e['Id_Utente']); ?>" class="tag-link" style="color: black;">
                                <?php echo htmlspecialchars($e['Nome'] . ' ' . $e['Cognome']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="text-center mt-5">
                    <a href="javascript:history.back()" class="button button-primary mt-3">
                        Torna indietro
                        <span class="mdi mdi-arrow-left"></span>
                    </a>
                    <br>
                    <a href="contributions.php?manifestazione_id=<?php echo $id; ?>" class="button button-primary button-lg">
                        Vedi tutti i contributi
                        <span class="mdi mdi-arrow-right"></span>
                    </a>
                    
                </div>
            </div>
        </div>
    </section>

<?php else: ?>
    <div class="container text-center mt-5">
        <div class="dashboard-card">
            <h4>Manifestazione non trovata</h4>
            <a href="manifestazioni.php" class="button button-primary mt-3">
                Torna alle manifestazioni
                <span class="mdi mdi-arrow-left"></span>
            </a>
        </div>
    </div>
<?php endif; ?>

<style>
.dashboard-detail-card {
    background: white;
    border-radius: 10px;
    padding: 40px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    margin-top: 30px;
}

.heading-decoration {
    position: relative;
    padding-bottom: 15px;
    margin-bottom: 25px;
}

.heading-decoration:after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 3px;
    background: linear-gradient(to right, #4e66f8, #6f42c1);
}

.detail-title {
    color: #4e66f8;
    margin-bottom: 15px;
    font-weight: 600;
    border-bottom: 2px solid #f0f0f0;
    padding-bottom: 8px;
}

.detail-content {
    background: #f9f9f9;
    padding: 20px;
    border-radius: 8px;
}

.info-box {
    text-align: center;
    padding: 20px;
    height: 100%;
    background: #f9f9f9;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.info-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.info-icon {
    font-size: 2rem;
    color: #4e66f8;
    margin-bottom: 10px;
}

.expositors-tags {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 10px;
    margin-top: 20px;
}

.tag-link {
    display: inline-block;
    padding: 8px 20px;
    background: #f0f0f0;
    color: #333;
    border-radius: 50px;
    text-decoration: none;
    transition: all 0.3s ease;
}

.tag-link:hover {
    background: #4e66f8;
    color: white;
    transform: translateY(-2px);
}

.button-primary {
    padding: 12px 30px;
}

.button-primary .mdi {
    margin-left: 8px;
    vertical-align: middle;
}
</style>

<?php include_once('../template_footer.php'); ?>