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
                        <th>Titolo</th>
                        <th>Sintesi</th>
                        <th>Accettazione</th>
                        <th>Utente</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($candidature)): ?>
                        <?php foreach ($candidature as $candidatura): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($candidatura['Titolo']); ?></td>
                                <td><?php echo htmlspecialchars($candidatura['Sintesi']); ?></td>
                                <td><?php echo htmlspecialchars($candidatura['Accettazione']); ?></td>
                                <td><?php echo htmlspecialchars($candidatura['Email']); ?></td>
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
                            <td colspan="5">Nessuna candidatura trovata.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php
include_once("../../template_footer.php");
?>