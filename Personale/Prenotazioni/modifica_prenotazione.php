<?php
include_once '../../config.php';
include_once '../../queries.php';
include_once '../../template_header.php';

//Recupera le prenotazioni dal database tramite la funzione getPrenotazioni
$prenotazioni = getPrenotazioni($pdo);
?>
<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(../../resources/images/sfondo.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Modifica Prenotazione</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="../dashboard_personale.php">Dashboard</a></li>
        <li><a href="gestisci_prenotazione.php">Gestione Prenotazioni</a></li>
        <li class="active">Modifica Prenotazione</li>
    </ul>
</section>
<!-- Main Content-->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Modifica  Prenotazione</h2>
        <p>Seleziona una prenotazione dalla lista sottostante per modificarla.</p>
        <br>

        <!-- Messaggi di successo o errore -->
        <?php if (!empty($successMessage)): ?>
            <p style="<?php echo $successStyle; ?>"><?php echo htmlspecialchars($successMessage); ?></p>
        <?php endif; ?>
        <?php if (!empty($errorMessage)): ?>
            <p style="<?php echo $errorStyle; ?>"><?php echo htmlspecialchars($errorMessage); ?></p>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Cognome</th>
                        <th>Email</th>
                        <th>Manifestazione</th>
                        <th>Data</th>
                        <th>Orario</th>
                        <th>Area</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($prenotazioni)): ?>
                        <?php foreach ($prenotazioni as $prenotazione): ?>
                            <tr>
                            <td><?php echo htmlspecialchars($prenotazione['Nome_Visitatore']); ?></td>
                            <td><?php echo htmlspecialchars($prenotazione['Cognome_Visitatore']); ?></td>
                            <td><?php echo htmlspecialchars($prenotazione['Email']); ?></td>
                            <td><?php echo htmlspecialchars($prenotazione['Nome_Manifestazione']); ?></td>
                            <td><?php echo htmlspecialchars($prenotazione['Data_Turno']); ?></td>
                            <td><?php echo htmlspecialchars($prenotazione['Ora_Turno']); ?></td>
                            <td><?php echo htmlspecialchars($prenotazione['Nome_Area']); ?></td>
                                <td>
                                <a class="button button-primary button-sm" 
                                    href="modifica_prenotazione_dettagli.php?Id_Utente=<?php echo urlencode($prenotazione['Id_Utente']); ?>&Id_Turno=<?php echo urlencode($prenotazione['Id_Turno']); ?>">
                                    Modifica
                                </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">Nessuna prenotazione trovata.</td>
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