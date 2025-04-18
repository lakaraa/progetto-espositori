<?php
include_once ("../../config.php");
//include_once ("../../queries.php");
include_once ("../../template_header.php");

function getEspositori($pdo) 
{
    $sql = "
        SELECT u.Id_Utente AS id, 
            u.Username AS username, 
            u.Password AS password, 
            u.Nome AS nome, 
            u.Cognome AS cognome, 
            u.Email AS email, 
            u.Telefono AS telefono, 
            u.Qualifica AS qualifica, 
            u.Curriculum AS curriculum
        FROM utente u
        WHERE u.Ruolo = 'Espositore'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$espositori = getEspositori($pdo);
?>
<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(images/bg-breadcrumbs-07-1920x480.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Modifica Espositore</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
    <li><a href="../dashboard_personale.php">Dashboard</a></li>
    <li><a href="gestisci_espositori.php">Gestione Espositori</a></li>
    <li class="active">Modifica Espositore</li>
    </ul>
</section>
<!-- Main Content-->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Modifica Espositore</h2>
        <p>Seleziona un Espositore dalla lista sottostante per modificarlo.</p>
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
                        <th>Qualifica</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($espositori)): ?>
                        <?php foreach ($espositori as $espositore): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($espositore['username']); ?></td>
                                <td><?php echo htmlspecialchars($espositore['nome']); ?></td>
                                <td><?php echo htmlspecialchars($espositore['cognome']); ?></td>
                                <td><?php echo htmlspecialchars($espositore['email']); ?></td>
                                <td><?php echo htmlspecialchars($espositore['telefono']); ?></td>
                                <td><?php echo htmlspecialchars($espositore['qualifica']); ?></td>
                                <td>
                                    <a class="button button-primary button-sm" 
                                        href="modifica_espositore_dettagli.php?id=<?php echo urlencode($espositore['id']); ?>" >
                                        Modifica
                                    </a>
                                </td>
                                <td>
                                    <a class="button button-danger button-sm"
                                        href="visualizza_cv.php?id=<?php echo urlencode($espositore['id']); ?>"
                                        target="_blank">
                                        Visualizza CV
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">Nessun espositore trovato.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
<?php
include_once ("../../template_footer.php");
?>