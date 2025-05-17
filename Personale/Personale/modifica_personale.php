<?php
include_once '../../config.php';
include_once '../../queries.php';
include_once '../../template_header.php';

// Recupera i dati del personale dal database
$personale = getPersonale($pdo);

?>
<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(../../resources/images/sfondo.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Modifica Personale</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="../dashboard_personale.php">Dashboard</a></li>
        <li><a href="gestisci_personale.php">Gestione Personale</a></li>
        <li class="active">Modifica Personale</li>
    </ul>
</section>

<!-- Main Content-->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Modifica Personale</h2>
        <p>Seleziona un membro del personale dalla lista sottostante per modificarlo.</p>
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
                        <th>Ruolo</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($personale)): ?>
                        <?php foreach ($personale as $dipendente): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($dipendente['Username']); ?></td>
                                <td><?php echo htmlspecialchars($dipendente['Nome']); ?></td>
                                <td><?php echo htmlspecialchars($dipendente['Cognome']); ?></td>
                                <td><?php echo htmlspecialchars($dipendente['Email']); ?></td>
                                <td><?php echo htmlspecialchars($dipendente['Telefono']); ?></td>
                                <td><?php echo htmlspecialchars($dipendente['Ruolo']); ?></td>
                                <td>
                                    <a class="button button-primary button-sm" 
                                       href="modifica_personale_dettagli.php?id=<?php echo urlencode($dipendente['Id_Utente']); ?>" >
                                       Modifica
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">Nessun personale trovato.</td>
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
