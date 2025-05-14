<?php
include_once '../../config.php';
include_once '../../queries.php';
include_once '../../session.php';
include_once '../../template_header.php';

$categorie = getClassificaCategorie($pdo);
?>

<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(/progetto-espositori/resources/images/sfondo.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Elenco Categorie</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="/progetto-espositori/index.php">Home</a></li>
        <li class="active">Elenco Categorie</li>
    </ul>
</section>

<!-- Main Content-->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Elenco Categorie</h2>
        <p>Visualizza tutte le categorie di esposizioni, ordinate per numero di espositori.</p>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Categoria</th>
                        <th>Numero Espositori</th>
                        <th>Percentuale</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($categorie)): ?>
                        <?php foreach ($categorie as $categoria): ?>
                            <tr>
                                <td><?= htmlspecialchars($categoria['nome']) ?></td>
                                <td><?= htmlspecialchars($categoria['numero_espositori']) ?></td>
                                <td><?= number_format($categoria['percentuale'], 1) ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">Nessuna categoria trovata.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php include_once '../../template_footer.php'; ?> 