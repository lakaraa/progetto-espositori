<?php
include_once('../config.php');
include_once('../queries.php');
include_once('../session.php');
include_once('../template_header.php');

// Recupera l'ID dell'espositore dalla query string
$id_espositore = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Recupera i dati dell'espositore
$espositore = getEspositoreById($pdo, $id_espositore);

// Imposta valori di default per campi mancanti
$espositore['Qualifica'] = $espositore['Qualifica'] ?? 'Non specificata';
$espositore['Curriculum'] = $espositore['Curriculum'] ?? null;

// Verifica se l'espositore esiste
if (!$espositore) {
    die("Espositore non trovato.");
}
?>
    <style>
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
            color: white; /* Testo bianco nel corpo della card */
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
    </style>

    <!-- Breadcrumbs -->
    <section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(images/bg-breadcrumbs-05-1920x480.jpg);">
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
    <section class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="profile-card card">
                    <div class="card-header">
                        <h3 class="mb-0">Informazioni Espositore</h3>
                    </div>
                    <div class="card-body">
                        
                        <div class="detail-item">
                            <span class="detail-label">Username:</span>
                            <span><?php echo htmlspecialchars($espositore['Username']); ?></span>
                        </div>
                        
                        <div class="detail-item">
                            <span class="detail-label">Nome:</span>
                            <span><?php echo htmlspecialchars($espositore['Nome']); ?></span>
                        </div>
                        
                        <div class="detail-item">
                            <span class="detail-label">Cognome:</span>
                            <span><?php echo htmlspecialchars($espositore['Cognome']); ?></span>
                        </div>
                        
                        <div class="detail-item">
                            <span class="detail-label">Email:</span>
                            <span><?php echo htmlspecialchars($espositore['Email']); ?></span>
                        </div>
                        
                        <div class="detail-item">
                            <span class="detail-label">Telefono:</span>
                            <span><?php echo htmlspecialchars($espositore['Telefono']); ?></span>
                        </div>
                        
                        <div class="detail-item">
                            <span class="detail-label">Qualifica:</span>
                            <span><?php echo htmlspecialchars($espositore['Qualifica']); ?></span>
                        </div>
                        
                        <?php if (!empty($espositore['Curriculum'])): ?>
                        <div class="detail-item">
                            <span class="detail-label">Curriculum:</span>
                            <a href="../uploads/<?php echo htmlspecialchars($espositore['Curriculum']); ?>" 
                               class="btn btn-sm btn-outline-primary" 
                               target="_blank">
                                <i class="fas fa-file-pdf"></i> Visualizza Curriculum
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <a href="javascript:history.back()" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Torna indietro
                    </a>
                </div>
            </div>
        </div>
    </section>

    <?php include_once('../template_footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>