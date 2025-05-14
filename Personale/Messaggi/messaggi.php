<?php
include_once ("../../config.php");
include_once ("../../queries.php");
include_once("../../session.php");
include_once ("../../template_header.php");

$messaggi = getMessaggi($pdo);
?>
<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(/progetto-espositori/resources/images/sfondo.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Visualizza Messaggi</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="../dashboard_personale.php">Dashboard</a></li>
        <li class="active">Visualizza Messaggi</li>
    </ul>
</section>
<!-- Main Content-->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Visualizza Messaggi</h2>
        <p>Visualizzare i messaggi.</p>
        <br>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Telefono</th>
                        <th>Data</th>
                        <th>Ora</th>
                        <th>Messaggio</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($messaggi)): ?>
                        <?php foreach ($messaggi as $index => $messaggio): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($messaggio['Nome']); ?></td>
                                <td><?php echo htmlspecialchars($messaggio['Email']); ?></td>
                                <td><?php echo htmlspecialchars($messaggio['Telefono']); ?></td>
                                <td><?php echo htmlspecialchars($messaggio['Data']); ?></td>
                                <td><?php echo htmlspecialchars($messaggio['Ora']); ?></td>
                                <td style="white-space: normal;">
                                    <?php
                                        $testo = htmlspecialchars($messaggio['Messaggio']);
                                        if (strlen($testo) > 50) {
                                            $inizio = substr($testo, 0, 50);
                                            $resto = substr($testo, 50);
                                    ?>
                                        <span><?php echo $inizio; ?>...</span>
                                        <span id="resto-<?php echo $index; ?>" style="display:none;"><?php echo $resto; ?></span>
                                        <button class="btn btn-sm btn-link p-0" onclick="mostraAltro(<?php echo $index; ?>, this)">Altro</button>
                                    <?php } else {
                                        echo $testo;
                                    } ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    <?php else: ?>
                        <tr>
                            <td colspan="7">Nessun messaggio trovato.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
<script>
    function mostraAltro(index, bottone) {
        const resto = document.getElementById('resto-' + index);
        if (resto.style.display === 'none') {
            resto.style.display = 'inline';
            bottone.textContent = 'Nascondi';
        } else {
            resto.style.display = 'none';
            bottone.textContent = 'Altro';
        }
    }
</script>

<?php
include_once ("../../template_footer.php");
?>