<?php
include_once('../config.php');
include_once('../queries.php');
include_once('../session.php');
include_once('../template_header.php'); 


// Recupera l'ID della manifestazione dalla query string
// Accetta sia 'id' che 'manifestazione_id'
$idManifestazione = isset($_GET['id']) ? intval($_GET['id']) : 
  (isset($_GET['manifestazione_id']) ? intval($_GET['manifestazione_id']) : 0);

  
// Recupera i dati della manifestazione
$manifestazione = getManifestazioneById($pdo, $idManifestazione);

// Verifica se la manifestazione esiste
if (!$manifestazione) {
    die("Manifestazione non trovata.");
}

// Recupera i contributi legati alla manifestazione
$contributi = getContributiByManifestazione($pdo, $idManifestazione);


?>
 <style>
    :root {
        --primary-color: #2a4365; /* Blu scuro */
        --secondary-color: #4299e1; /* Blu chiaro */
        --accent-color: #f6ad55;
        --light-bg: #f8f9fa;
        --dark-bg: #1a202c;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: var(--primary-color); /* Colore di sfondo blu scuro */
        color: white;
    }

    .hero-section {
        background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), 
                    url('../images/event-banner.jpg') center/cover no-repeat;
        color: white;
        padding: 5rem 0;
        margin-bottom: 3rem;
    }

    .card-contributo {
        border: none;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
        margin-bottom: 2rem;
        overflow: hidden;
        background-color: white; /* Card bianca */
    }

    .card-contributo:hover {
        transform: translateY(-5px);
    }

    .card-header {
        background-color: var(--primary-color); /* Blu scuro per il titolo */
        color: white;
        padding: 1.5rem;
    }

    
    .card-body, .card-footer{
        color: black; /* Testo all'interno delle card in nero */
    }

    .card-title{
      color: black;
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: bold;
        font-size: 0.9rem;
    }

    .status-accepted {
        background-color: #48bb78;
        color: white;
    }

    .status-pending {
        background-color: #ed8936;
        color: white;
    }

    .status-rejected {
        background-color: #f56565;
        color: white;
    }

    .btn-primary-custom {
        background-color: var(--secondary-color);
        border: none;
        padding: 0.8rem 2rem;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-primary-custom:hover {
        background-color: var(--primary-color);
        transform: translateY(-2px);
    }

    .event-details {
        background-color: white;
        border-radius: 10px;
        padding: 2rem;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .detail-icon {
        font-size: 1.5rem;
        color: var(--secondary-color);
        margin-right: 0.5rem;
    }

    /* Centrare il titolo h2 nella sezione Contributi */
    .container h2 {
        text-align: center;
    }
</style>


<!-- Breadcrumbs -->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(images/bg-breadcrumbs-05-1920x480.jpg);">
  <div class="container">
    <h2 class="breadcrumbs-custom-title"><?php echo htmlspecialchars($manifestazione['Nome']); ?></h2>
  </div>
  <ul class="breadcrumbs-custom-path">
    <li><a href="manifestazione.php">Manifestazioni</a></li>
    <li class="active">Contributi</li>
  </ul>
</section>

    <!-- Contributions Section -->
    <section class="container mb-5">
      <br><br>
    <h2 class="mb-0 text-center">Contributi</h2>
    <div style="text-align: right;">
        <span class="badge bg-primary rounded-pill"><?php echo count($contributi); ?> contributi</span>
    </div>
    
    <?php if (!empty($contributi)): ?>
        <div class="row">
            <?php foreach ($contributi as $contributo): ?>
                <div class="col-lg-6 mb-4">
                    <div class="card card-contributo h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="mb-0"><?php echo htmlspecialchars($contributo['Titolo']); ?></h4>
                            <span class="status-badge 
                                <?php echo $contributo['Accettazione'] === 'Accettato' ? 'status-accepted' : 
                                    ($contributo['Accettazione'] === 'In Approvazione' ? 'status-pending' : 'status-rejected'); ?>">
                                <?php echo htmlspecialchars($contributo['Accettazione']); ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <!-- Sintesi del contributo -->
                            <h5 class="card-title">Sintesi</h5>
                            <p class="card-text"><?php echo htmlspecialchars($contributo['Sintesi']); ?></p>
                            
                            <!-- Mostra l'immagine se presente -->
                            <?php if (!empty($contributo['Immagine'])): ?>
                                <img src="<?php echo htmlspecialchars($contributo['Immagine']); ?>" alt="Immagine Contributo" class="img-fluid mb-3">
                            <?php endif; ?>
                            
                            
                            <!-- Link URL se presente -->
                            <?php if (!empty($contributo['URL'])): ?>
                                <a href="<?php echo htmlspecialchars($contributo['URL']); ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                                    <i class="fas fa-external-link-alt"></i> Vedi risorse
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer bg-transparent">
                            <small class="text-muted">
                                <i class="fas fa-user"></i> Espositore #<?php echo htmlspecialchars($contributo['Id_Utente']); ?>
                            </small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-inbox fa-4x mb-3 text-muted"></i>
            <h3>Nessun contributo trovato</h3>
            <p class="text-muted">Non ci sono ancora contributi per questa manifestazione</p>
        </div>
    <?php endif; ?>
</section>


    <!-- Action Buttons -->
    <section class="container mb-5">
        <div class="text-center">
                <a href="/pages/registrazione_espositore.php echo $idManifestazione; ?>" class="btn btn-primary-custom btn-lg me-3">
                    <i></i>Candidati come espositore
                </a>
                <a href="registrazione_visitatore.php?id=<?php echo $idManifestazione; ?>" class="btn btn-primary-custom btn-lg">
                    <i class="fas fa-ticket-alt me-2"></i>Prenota il tuo posto
                </a> 
            <a href="../manifestazioni.php" class="btn btn-outline-secondary btn-lg ms-3">
                <i class="fas fa-arrow-left me-2"></i>Torna alle manifestazioni
            </a>
        </div>
    </section>

<?php
// Includi il footer del template
include_once('../template_footer.php');
?>
