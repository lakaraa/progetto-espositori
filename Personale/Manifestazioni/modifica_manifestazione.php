<?php
include_once '../../config.php';
include_once '../../queries.php';
include_once '../../template_header.php';

$manifestazioni = getManifestazioni($pdo);

?>
<!-- Breadcrumbs -->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(images/bg-breadcrumbs-07-1920x480.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Modifica Manifestazione</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="../dashboard_personale.php">Dashboard</a></li>
        <li><a href="gestisci_manifestazione.php">Gestione Manifestazioni</a></li>
        <li class="active">Modifica Manifestazione</li>
    </ul>
</section>

<!-- Main Content -->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Modifica Manifestazione</h2>
        <p>Seleziona una manifestazione dalla lista sottostante per modificarla.</p>
        <br>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Luogo</th>
                        <th>Data</th>
                        <th>Durata (giorni)</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($manifestazioni)): ?>
                        <?php foreach ($manifestazioni as $manifestazione): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($manifestazione['Nome']); ?></td>
                                <td><?php echo htmlspecialchars($manifestazione['Luogo']); ?></td>
                                <td><?php echo htmlspecialchars($manifestazione['Data']); ?></td>
                                <td><?php echo htmlspecialchars($manifestazione['Durata']); ?></td>
                                <td>
                                    <a class="button button-primary button-sm" 
                                       href="modifica_manifestazione_dettagli.php?id=<?php echo urlencode($manifestazione['Id_Manifestazione']); ?>">
                                       Modifica
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">Nessuna manifestazione trovata.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php
include_once '../../template_footer.php';
?>
