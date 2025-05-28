<?php
error_reporting(E_ALL); // Report all errors and warnings
ini_set('display_errors', 1); // Display errors on the screen

include_once("../../config.php");
include_once("../../queries.php");
include_once("../../template_header.php");

// Recupera le candidature utilizzando la funzione definita in queries.php
$candidature = getCandidature($pdo);

?>

<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(../../resources/images/sfondo.jpg);">
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
        <div class="table-responsive" style="min-width: 1200px;">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Utente</th>
                        <th>Immagine</th>
                        <th>Manifestazione</th>
                        <th>Titolo</th>
                        <th>Sintesi</th>
                        <th>Categorie</th>
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
                                        <img src="../../uploads/img/<?php echo htmlspecialchars($candidatura['Immagine']); ?>" alt="Immagine" style="width: 50px; height: auto; cursor: pointer;" onclick="showImageModal('../../uploads/img/<?php echo htmlspecialchars($candidatura['Immagine']); ?>')">
                                    <?php else: ?>
                                        Nessuna immagine
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($candidatura['Manifestazione']); ?></td>
                                <td class="titolo-cell">
                                    <?php
                                    $titolo = htmlspecialchars($candidatura['Titolo']);
                                    $titoloId = 'titolo-' . uniqid();
                                    $titoloShort = strlen($titolo) > 10 ? substr($titolo, 0, 10) . '...' : $titolo;
                                    $hasMoreTitolo = strlen($titolo) > 10;
                                    ?>
                                    <div class="titolo-content">
                                        <span class="titolo-short" id="<?php echo $titoloId; ?>"><?php echo $titoloShort; ?></span>
                                        <?php if ($hasMoreTitolo): ?>
                                            <button class="btn btn-link btn-sm leggi-piu" onclick="toggleText('<?php echo $titoloId; ?>', '<?php echo addslashes($titolo); ?>')">Leggi di più</button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="sintesi-cell">
                                    <?php
                                    $sintesi = htmlspecialchars($candidatura['Sintesi']);
                                    $sintesiId = 'sintesi-' . uniqid();
                                    $sintesiShort = strlen($sintesi) > 10 ? substr($sintesi, 0, 10) . '...' : $sintesi;
                                    $hasMore = strlen($sintesi) > 10;
                                    ?>
                                    <div class="sintesi-content">
                                        <span class="sintesi-short" id="<?php echo $sintesiId; ?>"><?php echo $sintesiShort; ?></span>
                                        <?php if ($hasMore): ?>
                                            <button class="btn btn-link btn-sm leggi-piu" onclick="toggleText('<?php echo $sintesiId; ?>', '<?php echo addslashes($sintesi); ?>')">Leggi di più</button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="categorie-cell">
                                    <?php
                                    $categorie = htmlspecialchars($candidatura['Categorie']);
                                    $categorieId = 'categorie-' . uniqid();
                                    $categorieShort = strlen($categorie) > 10 ? substr($categorie, 0, 10) . '...' : $categorie;
                                    $hasMoreCategorie = strlen($categorie) > 10;
                                    ?>
                                    <div class="categorie-content">
                                        <span class="categorie-short" id="<?php echo $categorieId; ?>"><?php echo $categorieShort; ?></span>
                                        <?php if ($hasMoreCategorie): ?>
                                            <button class="btn btn-link btn-sm leggi-piu" onclick="toggleText('<?php echo $categorieId; ?>', '<?php echo addslashes($categorie); ?>')">Leggi di più</button>
                                        <?php endif; ?>
                                    </div>
                                </td>
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

    // Funzione per gestire il testo espandibile
    function toggleText(id, fullText) {
        const element = document.getElementById(id);
        const button = element.nextElementSibling;
        
        if (element.classList.contains('sintesi-expanded') || 
            element.classList.contains('titolo-expanded') || 
            element.classList.contains('categorie-expanded')) {
            // Collapse
            element.textContent = fullText.substring(0, 10) + '...';
            element.classList.remove('sintesi-expanded', 'titolo-expanded', 'categorie-expanded');
            button.textContent = 'Leggi di più';
        } else {
            // Expand
            element.textContent = fullText;
            element.classList.add(id.startsWith('sintesi') ? 'sintesi-expanded' : 
                                id.startsWith('titolo') ? 'titolo-expanded' : 
                                'categorie-expanded');
            button.textContent = 'Mostra meno';
        }
    }
</script>

<style>
.sintesi-cell, .titolo-cell, .categorie-cell {
    max-width: 300px;
    position: relative;
}

.sintesi-content, .titolo-content, .categorie-content {
    position: relative;
}

.leggi-piu {
    color: rgb(74, 196, 207);
    padding: 0;
    margin-left: 5px;
    font-size: 0.9em;
    text-decoration: none;
    background: none;
    border: none;
    cursor: pointer;
}

.leggi-piu:hover {
    text-decoration: underline;
    color: rgb(74, 196, 207);
}

.sintesi-expanded, .titolo-expanded, .categorie-expanded {
    white-space: normal;
    word-wrap: break-word;
}
</style>

<?php
include_once("../../template_footer.php");
?>