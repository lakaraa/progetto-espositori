<?php
error_reporting(E_ALL); // Report all errors and warnings
ini_set('display_errors', 1); // Display errors on the screen

include_once '../../config.php';
include_once '../../queries.php';
include_once '../../template_header.php';

$visitatori = getVisitatori($pdo);

?>
<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(../../resources/images/sfondo.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Modifica Visitatore</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="../dashboard_personale.php">Dashboard</a></li>
        <li><a href="gestisci_visitatore.php">Gestione Visitatori</a></li>
        <li class="active">Modifica Visitatore</li>
    </ul>
</section>
<!-- Main Content-->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Modifica Visitatore</h2>
        <p>Seleziona un visitatore dalla lista sottostante per modificarlo.</p>
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
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($visitatori)): ?>
                        <?php foreach ($visitatori as $visitatore): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($visitatore['Username']); ?></td>
                                <td><?php echo htmlspecialchars($visitatore['Nome']); ?></td>
                                <td><?php echo htmlspecialchars($visitatore['Cognome']); ?></td>
                                <td><?php echo htmlspecialchars($visitatore['Email']); ?></td>
                                <td><?php echo htmlspecialchars($visitatore['Telefono']); ?></td>
                                <td>
                                    <a class="button button-primary button-sm" 
                                        href="modifica_visitatore_dettagli.php?id=<?php echo urlencode($visitatore['Id_Utente']); ?>" >
                                        Modifica
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">Nessun visitatore trovato.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
<?php
include_once '../../template_footer.php';