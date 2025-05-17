<?php
include_once '../../config.php';
include_once '../../queries.php';
include_once '../../template_header.php';

$categorie = getCategorie($pdo);

?>
<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(../../resources/images/sfondo.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Modifica Categoria</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="../dashboard_personale.php">Dashboard</a></li>
        <li><a href="gestisci_categorie.php">Gestione Categorie</a></li>
        <li class="active">Modifica Categoria</li>
    </ul>
</section>
<!-- Main Content-->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Modifica Categoria</h2>
        <p>Seleziona una categoria dalla lista sottostante per modificarla.</p>
        <br>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Descrizione</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($categorie)): ?>
                        <?php foreach ($categorie as $categoria): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($categoria['Nome']); ?></td>
                                <td><?php echo htmlspecialchars($categoria['Descrizione']); ?></td>
                                <td>
                                    <a class="button button-primary button-sm" 
                                        href="modifica_categoria_dettagli.php?id=<?php echo urlencode($categoria['Id_Categoria']); ?>" >
                                        Modifica
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">Nessuna categoria trovata.</td>
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