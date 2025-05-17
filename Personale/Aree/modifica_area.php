<?php
include_once '../../config.php';
include_once '../../queries.php';
include_once '../../template_header.php';

$aree = getAree($pdo);

?>
<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(../../resources/images/sfondo.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Modifica Area</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="../dashboard_personale.php">Dashboard</a></li>
        <li><a href="gestisci_aree.php">Gestione Aree</a></li>
        <li class="active">Modifica Area</li>
    </ul>
</section>
<!-- Main Content-->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Modifica Area</h2>
        <p>Seleziona una Area dalla lista sottostante per modificarla.</p>
        <br>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Manifestazione</th>
                        <th>Descrizione</th>
                        <th>Capienza Massima</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($aree)): ?>
                        <?php foreach ($aree as $area): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($area['nome']); ?></td>
                                <td><?php echo htmlspecialchars($area['manifestazione']); ?></td>
                                <td><?php echo htmlspecialchars($area['descrizione']); ?></td>
                                <td><?php echo htmlspecialchars($area['capienza_massima']); ?></td>
                                <td>
                                <a class="button button-primary button-sm" 
                                    href="modifica_area_dettagli.php?id=<?php echo urlencode($area['id']); ?>" >
                                    Modifica
                                </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">Nessuna area trovata.</td>
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