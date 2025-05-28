<?php
error_reporting(E_ALL); // Report all errors and warnings
ini_set('display_errors', 1); // Display errors on the screen

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
<!-- Breadcrumbs -->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(../../resources/images/sfondo.jpg);">
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
    <div class="section-header">
        <h2 class="section-title">Contributi</h2>
        <div class="contributi-counter">
            <span class="counter-badge">
                <i class="fas fa-file-alt me-2"></i><?php echo count($contributi); ?> contributi
            </span>
            <div class="filter-dropdown">
                <button class="filter-btn">
                    <i class="fas fa-filter me-2"></i>Filtra
                </button>
                <div class="filter-content">
                    <a href="#" class="filter-option active">Tutti</a>
                    <a href="#" class="filter-option">Accettati</a>
                    <a href="#" class="filter-option">In approvazione</a>
                    <a href="#" class="filter-option">Rifiutati</a>
                </div>
            </div>
        </div>
    </div>
    
    <?php if (!empty($contributi)): ?>
    <div class="contributi-grid">
        <?php foreach ($contributi as $contributo): ?>
            <div class="contributo-card" 
                 data-status="<?php echo strtolower(str_replace(' ', '-', $contributo['Accettazione'])); ?>"
                 onclick="window.location.href='espositore.php?id=<?php echo htmlspecialchars($contributo['Id_Utente']); ?>'"
                 style="cursor: pointer;">
                <div class="card-header">
                    <br>
                    <div class="status-badge 
                        <?php echo $contributo['Accettazione'] === 'Accettato' ? 'status-accepted' : 
                            ($contributo['Accettazione'] === 'In Approvazione' ? 'status-pending' : 'status-rejected'); ?>">
                        <?php echo htmlspecialchars($contributo['Accettazione']); ?>
                    </div>
                    <div class="mt-3">
                        <h3 class="card-title">
                            <?php 
                            if (strlen($contributo['Titolo']) > 20) {
                                echo htmlspecialchars(substr($contributo['Titolo'], 0, 20)) . '...';
                            } else {
                                echo htmlspecialchars($contributo['Titolo']);
                            }
                            ?>
                        </h3>
                    </div>
                </div>
                
                <div class="card-body">
                    <?php if (!empty($contributo['Immagine'])): ?>
                        <div class="contributo-image">
                            <img src="../uploads/img/<?php echo htmlspecialchars($contributo['Immagine']); ?>" 
                                 alt="Immagine Contributo" 
                                 class="img-fluid"
                                 style="cursor: pointer;"
                                 onclick="event.stopPropagation(); showImageModal('../uploads/img/<?php echo htmlspecialchars($contributo['Immagine']); ?>')">
                        </div>
                    <?php endif; ?>
                    
                    <div class="contributo-content">
                        <h4>Sintesi</h4>
                        <p>
                            <?php 
                            if (strlen($contributo['Sintesi']) > 20) {
                                echo htmlspecialchars(substr($contributo['Sintesi'], 0, 20)) . '...';
                                echo '<span class="read-more-link">';
                                echo '<a href="espositore.php?id=' . htmlspecialchars($contributo['Id_Utente']) . '" class="read-more">leggi di più</a>';
                                echo '</span>';
                            } else {
                                echo htmlspecialchars($contributo['Sintesi']);
                            }
                            ?>
                        </p>
                    </div>
                    
                    <?php if (!empty($contributo['URL'])): ?>
                        <a href="<?php echo htmlspecialchars($contributo['URL']); ?>" 
                           class="resource-link" 
                           target="_blank">
                            <i class="fas fa-external-link-alt me-2"></i>Vedi risorse esterne
                        </a>
                    <?php endif; ?>
                </div>
                
                <div class="card-footer">
                    <div class="contributo-author">
                        <div class="author-avatar">
                            <?php 
                            $initials = substr($contributo['NomeEspositore'], 0, 1) . substr($contributo['CognomeEspositore'], 0, 1);
                            $colors = ['#4ac4cf', '#f6ad55', '#68d391', '#f687b3'];
                            $color = $colors[array_rand($colors)];
                            ?>
                            <div class="avatar-circle" style="background-color: <?php echo $color; ?>">
                                <?php echo strtoupper($initials); ?>
                            </div>
                        </div>
                        <div class="author-info">
                            <a href="espositore.php?id=<?= htmlspecialchars($contributo['Id_Utente']) ?>" 
                               class="author-name">
                                <?= htmlspecialchars($contributo['NomeEspositore'] . ' ' . $contributo['CognomeEspositore']) ?>
                            </a>
                        </div>
                    </div>
                    
                    <?php if (!empty($contributo['Categorie'])): ?>
                        <div class="contributo-tags">
                            <?php 
                            $categorie = explode(',', $contributo['Categorie']);
                            $categorie = array_map('trim', $categorie);
                            $totale_categorie = count($categorie);
                            $categorie_da_mostrare = array_slice($categorie, 0, 4);
                            
                            foreach ($categorie_da_mostrare as $categoria): ?>
                                <span class="tag"><?php echo htmlspecialchars($categoria); ?></span>
                            <?php endforeach; 
                            
                            if ($totale_categorie > 4): ?>
                                <span class="tag" style="cursor: pointer; color: var(--secondary-color);">altro</span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="empty-state">
        <div class="empty-icon">
            <i class="fas fa-file-alt"></i>
        </div>
        <h3>Nessun contributo presente</h3>
        <p>Non sono ancora stati pubblicati contributi per questa manifestazione.</p>
        <?php
            $link = 'login.php';
            if (isset($_SESSION['user_id']) && isset($_SESSION['ruolo']) && $_SESSION['ruolo'] === 'Espositore') {
                $link = '../Espositore/form_candidatura.php?id=' . $idManifestazione;
            }
        ?>
        <a href="<?php echo $link; ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Diventa il primo contributore
        </a>
    </div>
<?php endif; ?>
</section>

<!-- Action Buttons -->
<section class="action-section">
    <div class="container">
        <div class="action-buttons">
            <div class="action-card">
                <div class="action-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h3 class="action-title">Diventa Espositore</h3>
                <p class="action-description">Partecipa alla manifestazione con il tuo contributo</p>
                <a href="<?php 
                    if (!isset($_SESSION['user_id'])) {
                        echo 'login.php';
                    } else if ($_SESSION['ruolo'] === 'Visitatore') {
                        echo 'registrazione_espositore.php';
                    } else if ($_SESSION['ruolo'] === 'Espositore') {
                        echo '../Espositore/form_candidatura.php?id=' . $idManifestazione;
                    }
                ?>" class="action-btn primary">
                    <i class="fas fa-arrow-right me-2"></i>Candidati ora
                </a>
            </div>
            
            <div class="action-card">
                <div class="action-icon">
                    <i class="fas fa-ticket"></i>
                </div>
                <h3 class="action-title">Prenota il Posto</h3>
                <p class="action-description">Riserva il tuo posto per partecipare all'evento</p>
                <a href="<?php 
                    if (!isset($_SESSION['user_id'])) {
                        echo 'login.php';
                    } else if ($_SESSION['ruolo'] === 'Visitatore') {
                        echo '../Visitatore/form_prenotazione.php?id=' . $idManifestazione;
                    } else if ($_SESSION['ruolo'] === 'Espositore') {
                        echo 'registrazione_visitatore.php';
                    }
                ?>" class="action-btn secondary">
                    <i class="fas fa-arrow-right me-2"></i>Prenota ora
                </a>
            </div>
            
            <div class="action-card">
                <div class="action-icon">
                    <i class="fas fa-arrow-left"></i>
                </div>
                <h3 class="action-title">Torna Indietro</h3>
                <p class="action-description">Ritorna alla pagina della manifestazione</p>
                <a href="manifestazione.php?id=<?php echo $idManifestazione; ?>" class="action-btn outline">
                    <i class="fas fa-arrow-left me-2"></i>Torna alla manifestazione
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Modal per visualizzare l'immagine -->
<div id="imageModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.8); z-index: 1000; justify-content: center; align-items: center;">
    <span style="position: absolute; top: 20px; right: 30px; font-size: 30px; color: white; cursor: pointer;" onclick="closeImageModal()">&times;</span>
    <img id="modalImage" src="" alt="Immagine" style="max-width: 90%; max-height: 90%; margin: auto; display: block;">
</div>

<script>
    // Filtro contributi
    document.addEventListener('DOMContentLoaded', function() {
        const filterOptions = document.querySelectorAll('.filter-option');
        
        filterOptions.forEach(option => {
            option.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Rimuovi active da tutti
                filterOptions.forEach(opt => opt.classList.remove('active'));
                // Aggiungi active a quello cliccato
                this.classList.add('active');
                
                const filterValue = this.textContent.toLowerCase();
                const cards = document.querySelectorAll('.contributo-card');
                
                cards.forEach(card => {
                    if (filterValue === 'tutti') {
                        card.style.display = 'flex';
                    } else {
                        const cardStatus = card.getAttribute('data-status');
                        const matchStatus = 
                            (filterValue === 'accettati' && cardStatus === 'accettato') ||
                            (filterValue === 'in approvazione' && cardStatus === 'in-approvazione') ||
                            (filterValue === 'rifiutati' && cardStatus === 'rifiutato');
                        
                        card.style.display = matchStatus ? 'flex' : 'none';
                    }
                });
            });
        });
    });

    // Funzione per mostrare la finestra modale con l'immagine
    function showImageModal(imageSrc) {
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        modalImage.src = imageSrc;
        modal.style.display = 'flex';
    }

    // Funzione per chiudere la finestra modale
    function closeImageModal() {
        const modal = document.getElementById('imageModal');
        modal.style.display = 'none';
    }

    // Previeni la propagazione del click sulla modal
    document.getElementById('imageModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeImageModal();
        }
    });
</script>



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
        margin-top: 25px;
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

    /* Aggiungi questo al tuo stile esistente */
    .row {
        display: flex;
        flex-wrap: wrap;
        margin-right: -15px;
        margin-left: -15px;
    }

    .col-lg-6 {
        flex: 0 0 50%;
        max-width: 50%;
        padding-right: 15px;
        padding-left: 15px;
    }

    @media (max-width: 992px) {
        .col-lg-6 {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }
    /* Stili per la sezione contributi */
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .section-title {
        font-size: 2rem;
        color: white;
        margin: 0;
        position: relative;
        padding-bottom: 0.5rem;
    }
    
    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 60px;
        height: 3px;
        background-color: var(--accent-color);
    }
    
    .contributi-counter {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .counter-badge {
        background-color: rgba(255, 255, 255, 0.1);
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
    }
    
    .filter-dropdown {
        position: relative;
        display: inline-block;
    }
    
    .filter-btn {
        background-color: rgba(255, 255, 255, 0.1);
        border: none;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        cursor: pointer;
        display: flex;
        align-items: center;
        transition: all 0.3s ease;
    }
    
    .filter-btn:hover {
        background-color: rgba(255, 255, 255, 0.2);
    }
    
    .filter-content {
        display: none;
        position: absolute;
        right: 0;
        background-color: white;
        min-width: 160px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        z-index: 1;
        border-radius: 8px;
        overflow: hidden;
        z-index: 1000;
    }
    
    .filter-dropdown:hover .filter-content {
        display: block;
    }
    
    .filter-option {
        color: var(--primary-color);
        padding: 0.75rem 1rem;
        text-decoration: none;
        display: block;
        transition: all 0.2s ease;
    }
    
    .filter-option:hover {
        background-color: #f8f9fa;
    }
    
    .filter-option.active {
        background-color: var(--accent-color);
        color: white;
    }
    
    .contributi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 2rem;
    }
    
    .contributo-card {
        background-color: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
    }
    
    .contributo-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }
    
    .card-header {
        padding: 1.5rem;
        background-color: white;
        position: relative;
    }
    
    .status-badge {
        position: absolute;
        top: -15px;
        right: 20px;
        padding: 0.5rem 1.25rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.8rem;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        z-index: 2;
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
    
    .card-title {
        margin: 0;
        color: var(--primary-color);
        font-size: 1.4rem;
        line-height: 1.3;
    }
    
    .card-body {
        padding: 0 1.5rem 1.5rem;
        flex-grow: 1;
    }
    
    .contributo-image {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 1.5rem;
        aspect-ratio: 16/9;
        background-color: #f8f9fa;
    }
    
    .contributo-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .contributo-image:hover .image-overlay {
        opacity: 1;
    }
    
    .view-image-btn {
        color: white;
        background-color: rgba(255,255,255,0.2);
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }
    
    .view-image-btn:hover {
        background-color: rgba(255,255,255,0.3);
        transform: scale(1.1);
    }
    
    .contributo-content h4 {
        color: var(--primary-color);
        margin-top: 0;
        margin-bottom: 0.75rem;
        font-size: 1.1rem;
    }
    
    .contributo-content p {
        color: #555;
        line-height: 1.6;
        margin-bottom: 1.5rem;
    }
    
    .resource-link {
        display: inline-flex;
        align-items: center;
        color: var(--secondary-color);
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .resource-link:hover {
        color: var(--primary-color);
    }
    
    .card-footer {
        padding: 1.5rem;
        background-color: #f9f9f9;
        border-top: 1px solid #eee;
    }
    
    .contributo-author {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    
    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        flex-shrink: 0;
    }
    
    .author-info {
        flex-grow: 1;
    }
    
    .author-name {
        color: var(--primary-color);
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    
    .author-name:hover {
        color: var(--secondary-color);
        text-decoration: underline;
    }
    
    .contributo-date {
        font-size: 0.8rem;
        color: #777;
    }
    
    .contributo-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    .tag {
        background-color: rgba(74, 196, 207, 0.1);
        color: #4ac4cf;
        padding: 0.35rem 0.75rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem;
        background-color: white;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    
    .empty-icon {
        font-size: 3rem;
        color: #ddd;
        margin-bottom: 1.5rem;
    }
    
    .empty-state h3 {
        color: var(--primary-color);
        margin-bottom: 0.5rem;
    }
    
    .empty-state p {
        color: #777;
        margin-bottom: 1.5rem;
    }
    
    @media (max-width: 768px) {
        .section-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .contributi-grid {
            grid-template-columns: 1fr;
        }
    }
    /* Stili per la sezione azioni */
    .action-section {
        padding: 4rem 0;
        background-color: rgba(255, 255, 255, 0.05);
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        margin-top: 3rem;
    }
    
    .action-buttons {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .action-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        background: rgba(255, 255, 255, 0.15);
    }
    
    .action-icon {
        font-size: 2.5rem;
        color: var(--accent-color);
        margin-bottom: 1.5rem;
    }
    
    .action-title {
        color: white;
        margin-bottom: 1rem;
        font-size: 1.5rem;
    }
    
    .action-description {
        color: rgba(255, 255, 255, 0.7);
        margin-bottom: 1.5rem;
        font-size: 0.95rem;
        line-height: 1.6;
    }
    
    .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.8rem 1.5rem;
        border-radius: 50px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        width: 100%;
        max-width: 200px;
        margin: 0 auto;
    }
    
    .action-btn i {
        transition: transform 0.3s ease;
    }
    
    .action-btn:hover i {
        transform: translateX(5px);
    }
    
    .action-btn.outline:hover i {
        transform: translateX(-5px);
    }
    
    .primary {
        background-color: var(--accent-color);
        color: var(--primary-color);
        border: 2px solid var(--accent-color);
    }
    
    .primary:hover {
        background-color: transparent;
        color: var(--accent-color);
    }
    
    .secondary {
        background-color: var(--secondary-color);
        color: white;
        border: 2px solid var(--secondary-color);
    }
    
    .secondary:hover {
        background-color: transparent;
        color: var(--secondary-color);
    }
    
    .outline {
        background-color: transparent;
        color: white;
        border: 2px solid rgba(255, 255, 255, 0.3);
    }
    
    .outline:hover {
        background-color: white;
        color: var(--primary-color);
        border-color: white;
    }
    
    @media (max-width: 768px) {
        .action-buttons {
            grid-template-columns: 1fr;
        }
        
        .action-card {
            padding: 1.5rem;
        }
    }
    
    .read-more-link {
        margin-left: 5px;
    }
    
    .read-more {
        color: var(--accent-color);
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s ease;
        font-size: 0.9em;
    }
    
    .read-more:hover {
        color: var(--secondary-color);
        text-decoration: underline;
    }
</style>
<?php
// Includi il footer del template
include_once('../template_footer.php');
?>
