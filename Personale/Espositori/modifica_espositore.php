<?php
include_once ("../../config.php");
include_once ("../../queries.php");
include_once ("../../session.php");


include_once ("../../template_header.php");

$espositori = getEspositori($pdo);
?>
<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(../../resources/images/sfondo.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Modifica Espositore</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
    <li><a href="../dashboard_personale.php">Dashboard</a></li>
    <li><a href="gestisci_espositori.php">Gestione Espositori</a></li>
    <li class="active">Modifica Espositore</li>
    </ul>
</section>
<!-- Main Content-->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Modifica Espositore</h2>
        <p>Seleziona un Espositore dalla lista sottostante per modificarlo.</p>
        <br>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Nome</th>
                        <th>Cognome</th>
                        <th>Email</th>
                        <th>Telefono</th>
                        <th>Qualifica</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($espositori)): ?>
                        <?php foreach ($espositori as $espositore): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($espositore['username']); ?></td>
                                <td><?php echo htmlspecialchars($espositore['nome']); ?></td>
                                <td><?php echo htmlspecialchars($espositore['cognome']); ?></td>
                                <td><?php echo htmlspecialchars($espositore['email']); ?></td>
                                <td><?php echo htmlspecialchars($espositore['telefono']); ?></td>
                                <td><?php echo htmlspecialchars($espositore['qualifica']); ?></td>
                                <td>
                                    <a class="button button-primary button-sm" 
                                        href="modifica_espositore_dettagli.php?id=<?php echo urlencode($espositore['Id_Utente']); ?>" >
                                         Modifica
                                    </a>
                                </td>
                                <td>
                                    <a class="button button-danger button-sm"
                                        href="#"
                                        onclick="previewNewCV('../../uploads/cv/cv_<?php echo htmlspecialchars($espositore['username']); ?>.pdf'); return false;">
                                        Visualizza CV
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">Nessun espositore trovato.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<script>
function previewNewCV(url) {
    const filename = url.split('/').pop();
    
    // Mostra l'icona di caricamento
    const loadingOverlay = document.createElement('div');
    loadingOverlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    `;
    loadingOverlay.innerHTML = `
        <div style="text-align: center;">
            <i class="fas fa-spinner fa-spin" style="font-size: 2em; color: #4ac4cf;"></i>
            <p style="margin-top: 10px; color: #333;">Caricamento PDF in corso...</p>
        </div>
    `;
    document.body.appendChild(loadingOverlay);

    // Verifica se il file esiste prima di aprirlo
    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error('File non trovato o non accessibile');
            }
            return response.blob();
        })
        .then(blob => {
            if (blob.type !== 'application/pdf') {
                throw new Error('Il file non è un PDF valido');
            }
            const newWindow = window.open(url, '_blank');
            if (newWindow) {
                newWindow.document.title = filename;
            }
        })
        .catch(error => {
            alert('Errore durante l\'apertura del PDF: ' + error.message);
        })
        .finally(() => {
            // Rimuovi l'overlay di caricamento
            document.body.removeChild(loadingOverlay);
        });
}

// Aggiungi tooltip ai pulsanti di visualizzazione CV
document.addEventListener('DOMContentLoaded', function() {
    const cvButtons = document.querySelectorAll('[onclick*="previewNewCV"]');
    cvButtons.forEach(button => {
        const url = button.getAttribute('onclick').match(/'([^']+)'/)[1];
        const filename = url.split('/').pop();
        button.title = `Visualizza ${filename}`;
    });
});
</script>

<?php
include_once ("../../template_footer.php");
?>