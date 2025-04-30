<?php
include_once("../../config.php");
include_once("../../queries.php");
include_once("../../template_header.php");

// Recupera le candidature utilizzando la funzione definita in queries.php
$candidature = getCandidature($pdo);
?>

<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(images/bg-breadcrumbs-07-1920x480.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Modifica Candidatura</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="../dashboard_personale.php">Dashboard</a></li>
        <li><a href="gestisci_candidature.php">Gestione Candidature</a></li>
        <li class="active">Modifica Candidatura</li>
    </ul>
</section>

<!-- Main Content-->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Modifica Candidatura</h2>
        <p>Seleziona una candidatura dalla lista sottostante per modificarla.</p>
        <br>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Utente</th>
                        <th>Immagine</th>
                        <th>Titolo</th>
                        <th>Sintesi</th>
                        <th>Accettazione</th>
                        <th>URL</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($candidature)): ?>
                        <?php foreach ($candidature as $candidatura): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($candidatura['Email']); ?></td>
                                <td>
                                    <?php if (!empty($candidatura['Immagine'])): ?>
                                        <img src="../../uploads/<?php echo htmlspecialchars($candidatura['Immagine']); ?>" alt="Immagine" style="width: 50px; height: auto; cursor: pointer;" onclick="showImageModal('../../uploads/<?php echo htmlspecialchars($candidatura['Immagine']); ?>')">
                                    <?php else: ?>
                                        Nessuna immagine
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($candidatura['Titolo']); ?></td>
                                <td><?php echo htmlspecialchars($candidatura['Sintesi']); ?></td>
                                <td><?php echo htmlspecialchars($candidatura['Accettazione']); ?></td>
                                <td>
                                    <?php if (!empty($candidatura['URL'])): ?>
                                        <a href="<?php echo htmlspecialchars($candidatura['URL']); ?>" target="_blank">Visita</a>
                                    <?php else: ?>
                                        Nessun URL
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a class="button button-primary button-sm" 
                                        href="modifica_candidatura_dettagli.php?id=<?php echo urlencode($candidatura['Id_Contributo']); ?>">
                                        Modifica
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">Nessuna candidatura trovata.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- Modal per visualizzare l'immagine -->
<div id="imageModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.8); z-index: 1000; justify-content: center; align-items: center;">
    <span style="position: absolute; top: 20px; right: 30px; font-size: 30px; color: white; cursor: pointer;" onclick="closeImageModal()">&times;</span>
    <img id="modalImage" src="" alt="Immagine" style="max-width: 90%; max-height: 90%; margin: auto; display: block;">
</div>

<script>
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
</script>

<?php
include_once("../../template_footer.php");
?>