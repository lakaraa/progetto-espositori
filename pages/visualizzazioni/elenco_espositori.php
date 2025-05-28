<?php
error_reporting(E_ALL); // Report all errors and warnings
ini_set('display_errors', 1); // Display errors on the screen

include_once '../../config.php';
include_once '../../queries.php';
include_once '../../session.php';
include_once '../../template_header.php';

$espositori = getElencoEspositoriAlfabetico($pdo);
?>


<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(../../resources/images/sfondo.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Elenco Espositori</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="../../index.php">Home</a></li>
        <li class="active">Elenco Espositori</li>
    </ul>
</section>
<!-- Main Content-->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Elenco Espositori</h2>
        <p>Scopri tutti gli espositori che partecipano alle manifestazioni, ordinati alfabeticamente.</p>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Cognome</th>
                        <th>Nome</th>
                        <th>Area Assegnata</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($espositori)): ?>
                        <?php foreach ($espositori as $espositore): ?>
                            <tr>
                                <td><?= htmlspecialchars($espositore['Cognome']) ?></td>
                                <td><?= htmlspecialchars($espositore['Nome']) ?></td>
                                <td><?= htmlspecialchars($espositore['Area'] ?? 'Nessuna area assegnata') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">Nessun espositore trovato.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php include_once '../../template_footer.php'; ?>
