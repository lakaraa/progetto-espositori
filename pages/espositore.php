<?php
error_reporting(E_ALL); // Report all errors and warnings
ini_set('display_errors', 1); // Display errors on the screen

include_once('../config.php');
include_once('../queries.php');
include_once('../session.php');
include_once('../template_header.php');

// Recupera l'ID dell'espositore dalla query string
$id_espositore = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Recupera i dati dell'espositore
$espositore = getEspositoreById($pdo, $id_espositore);

// Recupera i contributi e le categorie dell'espositore
$contributi = getEspositoreContributi($pdo, $id_espositore);

// Imposta valori di default per campi mancanti
$espositore['Qualifica'] = $espositore['Qualifica'] ?? 'Non specificata';
$espositore['Curriculum'] = $espositore['Curriculum'] ?? null;

// Verifica se l'espositore esiste
if (!$espositore) {
    die("Espositore non trovato.");
}
?>
    

    <!-- Breadcrumbs -->
    <section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(../resources/images/sfondo.jpg);">
        <div class="container">
            <h2 class="breadcrumbs-custom-title"><?php echo htmlspecialchars($espositore['Nome'] . ' ' . htmlspecialchars($espositore['Cognome'])); ?></h2>
        </div>
        <ul class="breadcrumbs-custom-path">
            <li><a href="manifestazione.php">Manifestazione</a></li>
            <li class="active">Espositore</li>
        </ul>
    </section>
<br><br>
    <!-- Dettagli Espositore -->
    <!-- Dettagli Espositore -->
<section class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="profile-card card">
                <!-- Header con foto profilo e nome -->
                <div class="card-header position-relative">
                    <div class="profile-header d-flex align-items-center">
                        <div class="profile-avatar me-4">
                            <div class="avatar-circle">
                                <?php 
                                $initials = substr($espositore['Nome'], 0, 1) . substr($espositore['Cognome'], 0, 1);
                                $colors = ['#4ac4cf', '#f6ad55', '#68d391', '#f687b3'];
                                $color = $colors[array_rand($colors)];
                                ?>
                                <div class="avatar-initials" style="background-color: <?php echo $color; ?>">
                                    <?php echo strtoupper($initials); ?>
                                </div>
                            </div>
                        </div>
                        <div class="profile-info">
                            <h3 class="mb-1"><?php echo htmlspecialchars($espositore['Nome'] . ' ' . htmlspecialchars($espositore['Cognome'])); ?></h3>
                            <i class="fas fa-user-tag me-2 mb-2" style="color: white;">  <?php echo htmlspecialchars($espositore['Qualifica']); ?></i>
                            <div class="social-links">
                                <?php if (!empty($espositore['Email'])): ?>
                                    <a href="mailto:<?php echo htmlspecialchars($espositore['Email']); ?>" class="me-2">
                                        <i class="fas fa-envelope"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($espositore['Telefono'])): ?>
                                    <a href="tel:<?php echo htmlspecialchars($espositore['Telefono']); ?>" class="me-2">
                                        <i class="fas fa-phone"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($espositore['Curriculum'])): ?>
                                    <a href="../uploads/cv/<?php echo htmlspecialchars($espositore['Curriculum']); ?>" target="_blank">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Corpo della card -->
                <div class="card-body">
                    <div class="row">
                        <!-- Colonna sinistra - Informazioni base -->
                        <div class="col-md-6">
                            <div class="detail-section">
                                <h5 class="section-title">
                                    <i class="fas fa-info-circle me-2"></i>  Informazioni Personali
                                </h5>
                                
                                <div class="detail-item">
                                    <div class="detail-label">
                                        <i class="fas fa-user me-2"></i>    Username
                                    </div>
                                    <div class="detail-value">
                                        <?php echo htmlspecialchars($espositore['Username']); ?>
                                    </div>
                                </div>
                                
                                <div class="detail-item">
                                    <div class="detail-label">
                                        <i class="fas fa-envelope me-2"></i>    Email
                                    </div>
                                    <div class="detail-value">
                                        <?php echo htmlspecialchars($espositore['Email']); ?>
                                    </div>
                                </div>
                                
                                <div class="detail-item">
                                    <div class="detail-label">
                                        <i class="fas fa-phone me-2"></i>   Telefono
                                    </div>
                                    <div class="detail-value">
                                        <?php echo htmlspecialchars($espositore['Telefono']); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Colonna destra - Curriculum e competenze -->
                        <div class="col-md-6">
                            <?php if (!empty($espositore['Curriculum'])): ?>
                            <div class="detail-section">
                                <h5 class="section-title">
                                    <i class="fas fa-file-alt me-2"></i>  Curriculum
                                </h5>
                                <div class="text-center">
                                    <a href="../cv/<?php echo htmlspecialchars($espositore['Curriculum']); ?>" 
                                        class="btn btn-download" 
                                        target="_blank">
                                        <i class="fas fa-eye me-2"></i>  Visualizza Curriculum
                                    </a>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($contributi)): ?>
                            <div class="detail-section mt-4">
                                <h5 class="section-title">
                                    <i class="fas fa-tasks me-2"></i>  Competenze
                                </h5>
                                <div class="skills-container">
                                    <?php 
                                    $allCategories = [];
                                    foreach ($contributi as $contributo) {
                                        if (!empty($contributo['Categorie'])) {
                                            $cats = explode(',', $contributo['Categorie']);
                                            $allCategories = array_merge($allCategories, $cats);
                                        }
                                    }
                                    $uniqueCategories = array_unique($allCategories);
                                    ?>
                                    <?php foreach ($uniqueCategories as $categoria): ?>
                                        <span class="skill-tag"><?php echo htmlspecialchars(trim($categoria)); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Sezione Contributi -->
                    <?php if (!empty($contributi)): ?>
                    <div class="detail-section mt-4">
                        <h5 class="section-title">
                            <i class="fas fa-star me-2"></i>  Contributi
                        </h5>
                        <div class="contributi-list">
                            <?php foreach ($contributi as $contributo): ?>
                                <div class="contributo-item">
                                    <div class="contributo-header">
                                        <h6 style="color: black;"><?php echo htmlspecialchars($contributo['Contributo']); ?></h6>
                                    </div>
                                    <?php if (!empty($contributo['Categorie'])): ?>
                                        <div class="contributo-categories mt-2">
                                            <?php foreach (explode(',', $contributo['Categorie']) as $categoria): ?>
                                                <span class="categoria-badge"><?php echo htmlspecialchars(trim($categoria)); ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="container text-center mt-5">
                <div class="dashboard-card">
                    <?php
                    // Recupera l'ID della manifestazione dal contributo
                    $manifestazione_id = null;
                    if (!empty($contributi)) {
                        $manifestazione_id = $contributi[0]['Id_Manifestazione'] ?? null;
                    }
                    $back_url = $manifestazione_id ? "manifestazione.php?id=" . $manifestazione_id : "manifestazioni.php";
                    ?>
                    <a href="manifestazioni.php" class="button button-primary mt-3">
                        Torna indietro
                        <span class="mdi mdi-arrow-left"></span>
                    </a>
                </div>
            </div>
        </div>
    </section>

<style>
    /* Stili migliorati per la sezione */
    .profile-card {
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .card-header {
        padding: 2rem;
        background: linear-gradient(135deg, var(--secondary-color), #1a365d);
        position: relative;
    }
    
    .profile-header {
        align-items: center;
    }
    
    .avatar-circle {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.1);
        border: 3px solid white;
    }
    
    .avatar-initials {
        font-size: 2rem;
        font-weight: bold;
        color: white;
    }
    
    .profile-info h3 {
        font-size: 1.8rem;
        margin-bottom: 0.5rem;
    }
    
    .social-links a {
        color: white;
        font-size: 1.2rem;
        margin-right: 15px;
        transition: all 0.3s ease;
    }
    
    .social-links a:hover {
        color: var(--accent-color);
        transform: translateY(-2px);
    }
    
    .detail-section {
        margin-bottom: 2rem;
    }
    
    .section-title {
        color: var(--accent-color);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding-bottom: 0.5rem;
        margin-bottom: 1.5rem;
        font-size: 1.2rem;
    }
    
    .detail-item {
        margin-bottom: 1.2rem;
        padding-bottom: 1.2rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    .detail-label {
        font-weight: 500;
        color: var(--accent-color);
        margin-bottom: 0.3rem;
        display: flex;
        align-items: center;
    }
    
    .detail-value {
        font-size: 1.1rem;
    }
    
    .btn-download {
        background-color: var(--accent-color);
        color: var(--primary-color);
        border: none;
        padding: 0.5rem 1.5rem;
        border-radius: 50px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-download:hover {
        background-color: white;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(246, 173, 85, 0.4);
    }
    
    .skills-container {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    .skill-tag {
        background-color: rgba(74, 196, 207, 0.2);
        color: #4ac4cf;
        padding: 0.4rem 0.8rem;
        border-radius: 50px;
        font-size: 0.85rem;
    }
    
    .contributi-list {
        display: grid;
        gap: 1rem;
    }
    
    .contributo-item {
        background: rgba(255, 255, 255, 0.03);
        border-left: 3px solid var(--accent-color);
        padding: 1.2rem;
        border-radius: 5px;
        transition: all 0.3s ease;
    }
    
    .contributo-item:hover {
        background: rgba(255, 255, 255, 0.07);
        transform: translateX(5px);
    }
    
    .contributo-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }
    
    .contributo-header h6 {
        margin: 0;
        font-size: 1.1rem;
    }
    
    .contributo-date {
        font-size: 0.85rem;
        color: rgba(255, 255, 255, 0.6);
    }
    
    .contributo-categories {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    .categoria-badge {
        background-color: rgba(246, 173, 85, 0.2);
        color: var(--accent-color);
        padding: 0.3rem 0.7rem;
        border-radius: 50px;
        font-size: 0.8rem;
    }
    
    .btn-back {
        background-color: transparent;
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.3);
        padding: 0.6rem 1.5rem;
        border-radius: 50px;
        transition: all 0.3s ease;
    }
    
    .btn-back:hover {
        background-color: white;
        color: var(--primary-color);
        border-color: white;
    }
    :root {
        --primary-color: #2a4365; /* Blu scuro */
        --secondary-color:rgb(19, 65, 102); /* Blu chiaro */
        --accent-color: #f6ad55;
        --light-bg: #f8f9fa;
        --dark-bg: #1a202c;
    }
    
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: var(--primary-color); /* Sfondo blu scuro */
        color: white; /* Testo bianco */
    }
    
    .breadcrumbs-custom-title {
        color: white;
    }
    
    .breadcrumbs-custom-path a {
        color: #4299e1;
    }
    
    .breadcrumbs-custom-path .active {
        color: white;
    }
    
    .profile-card {
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        margin-bottom: 2rem;
        overflow: hidden;
        background-color: rgba(255, 255, 255, 0.1); /* Card semi-trasparente */
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .card-header {
        background-color: var(--secondary-color) !important; /* Header card blu chiaro */
        color: white;
    }
    
    .card-body {
        background: white;
        color: black; /* Testo bianco nel corpo della card */
    }
    
    .detail-item {
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .detail-label {
        font-weight: 600;
        color: var(--accent-color); /* Etichette in color arancione */
    }
    
    .btn-outline-primary {
        color: white;
        border-color: white;
    }
    
    .btn-outline-primary:hover {
        background-color: white;
        color: var(--primary-color);
    }
    
    .btn-secondary {
        background-color: var(--secondary-color);
        border-color: var(--secondary-color);
    }
    .contributo-item {
        background-color: rgba(255, 255, 255, 0.05);
        padding: 1rem;
        margin-bottom: 1rem;
        border-radius: 5px;
    }
    .categoria-badge {
        background-color: var(--accent-color);
        color: var(--primary-color);
        padding: 0.25rem 0.5rem;
        border-radius: 3px;
        margin-right: 0.5rem;
        font-size: 0.9rem;
    }
</style>

    <?php include_once('../template_footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>