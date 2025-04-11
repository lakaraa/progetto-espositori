<?php
include_once("../../config.php");
include_once("../../session.php");
include_once("../../queries.php");
include_once("../../template_header.php");

// Recupera gli id dell'utente e del turno
$Id_Utente = isset($_GET['Id_Utente']) ? intval($_GET['Id_Utente']) : 0;
$Id_Turno = isset($_GET['Id_Turno']) ? intval($_GET['Id_Turno']) : 0;

// Recupera i dettagli della prenotazione
$prenotazione = getPrenotazioneById($pdo, $Id_Utente, $Id_Turno);

if (!$prenotazione) {
    echo "<p style='color: red;'>Prenotazione non trovata.</p>";
    exit;
}

$manifestazioni = getManifestazioni($pdo);
$turni = getTurniByArea($pdo, $prenotazione['Id_Area']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newIdTurno = isset($_POST['Id_Turno']) ? intval($_POST['Id_Turno']) : 0;
    $originalIdTurno = isset($_POST['original_Id_Turno']) ? intval($_POST['original_Id_Turno']) : 0;
    
    if ($newIdTurno === 0 || $originalIdTurno === 0) {
        echo "<script>document.getElementById('form-message').innerHTML = '<p style=\"color: red;\">Dati mancanti per l\'aggiornamento.</p>';</script>";
    } elseif ($newIdTurno === $originalIdTurno) {
        echo "<script>document.getElementById('form-message').innerHTML = '<p style=\"color: orange;\">Nessuna modifica da salvare.</p>';</script>";
    } else {
        try {
            // Controlla se esiste già una prenotazione con gli stessi dati
            $existingPrenotazione = checkExistingPrenotazione($pdo, $Id_Utente, $newIdTurno);
            if ($existingPrenotazione) {
                echo "<script>setTimeout(function() { document.getElementById('form-message').innerHTML = '<p style=\"color: red;\">Esiste già una prenotazione per questo utente nel turno selezionato.</p>'; }, 100);</script>";
            } else {
                $result = updatePrenotazione($pdo, $Id_Utente, $originalIdTurno, $newIdTurno);
                if ($result) { 
                    echo '<script>window.location.href = "modifica_prenotazione.php?Id_Utente='.$Id_Utente.'&Id_Turno='.$newIdTurno.'&success=1";</script>';
                    exit;
                } else {
                    echo "<script>document.getElementById('form-message').innerHTML = '<p style=\"color: red;\">Errore durante l\'aggiornamento della prenotazione.</p>';</script>";
                }
            }
        } catch (PDOException $e) {
            echo "<script>document.getElementById('form-message').innerHTML = '<p style=\"color: red;\">Errore database: " . $e->getMessage() . "</p>';</script>";
        }
    }
}
?>

<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(images/bg-breadcrumbs-07-1920x480.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Modifica Dettagli Prenotazione</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="../dashboard_personale.php">Dashboard</a></li>
        <li><a href="gestisci_prenotazione.php">Gestione Prenotazioni</a></li>
        <li><a href="modifica_prenotazione.php">Modifica Prenotazione</a></li>
        <li class="active">Modifica Dettagli Prenotazione</li>
    </ul>
</section>

<!-- Main Content-->
<section class="section section-lg bg-default text-center">
    <div class="container">

        <h2>Modifica Dettagli Prenotazione</h2>
        <p>Compila il modulo sottostante per modificare i dettagli della prenotazione.</p>
        
        <!-- Output del messaggio -->
        <div id="form-message"></div>

        <form class="rd-form rd-mailform" method="post" action="">
            <input type="hidden" name="Id_Utente" value="<?php echo $Id_Utente; ?>">
            <input type="hidden" name="original_Id_Turno" value="<?php echo $Id_Turno; ?>">
            
            <div class="row row-50">
                <div class="col-md-12">
                    <div class="form-wrap">
                        <input class="form-input" id="prenotazione-nome-cognome" type="text" name="NomeCognome" 
                                value="<?php echo htmlspecialchars($prenotazione['Nome_Visitatore'] . ' ' . $prenotazione['Cognome_Visitatore']); ?>" readonly 
                                title="Modificabile solo dalla pagina Gestione Visitatori">
                        <label class="form-label" for="prenotazione-nome-cognome">Nome e Cognome (Modificabile solo da Gestione Visitatori)</label>
                    </div>
                </div>

                <!-- Seleziona Manifestazione -->
                <div class="col-md-12">
                    <select class="form-input" id="manifestazione" name="manifestazione">
                        <option value="">Seleziona Manifestazione</option>
                        <?php foreach ($manifestazioni as $manifestazione): ?>
                            <option value="<?php echo htmlspecialchars($manifestazione['Id_Manifestazione']); ?>"
                                <?php echo ($prenotazione['Id_Manifestazione'] == $manifestazione['Id_Manifestazione']) ? 'selected' : ''; ?>
                                style="color: black; background-color: white;">
                                <?php echo htmlspecialchars($manifestazione['Nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Seleziona Area -->
                <div class="col-md-6">
                    <select class="form-input" id="area" name="area">
                        <option value="">Seleziona Area</option>
                        <!-- Le aree verranno caricate dinamicamente via AJAX -->
                    </select>
                </div>

                <!-- Seleziona Turno -->
                <div class="col-md-6">
                    <select class="form-input" id="turno" name="Id_Turno">
                        <option value="">Seleziona Turno</option>
                        <?php foreach ($turni as $turno): ?>
                            <option value="<?php echo htmlspecialchars($turno['Id_Turno']); ?>" 
                                    <?php echo ($prenotazione['Id_Turno'] == $turno['Id_Turno']) ? 'selected' : ''; ?> 
                                    style="color: black; background-color: white;">
                                <?php echo htmlspecialchars($turno['Data'] . ' - ' . $turno['Ora']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-12 text-center">
                <button class="button button-primary button-lg" type="submit">Salva Modifiche</button>
                <button class="button button-secondary button-lg" type="button" onclick="window.location.href='modifica_prenotazione.php?Id_Utente=<?php echo $Id_Utente; ?>&Id_Turno=<?php echo $Id_Turno; ?>';">Annulla</button>
            </div>
        </form>
    </div>
</section>

<!-- Script per caricare le aree e i turni dinamicamente -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Carica le aree iniziali per la manifestazione selezionata
    caricaAree($('#manifestazione').val(), <?php echo $prenotazione['Id_Area'] ?? 'null'; ?>);

    // Gestione cambio manifestazione
    $('#manifestazione').change(function() {
        caricaAree($(this).val());
    });

    // Gestione cambio area
    $('#area').change(function() {
        caricaTurni($(this).val(), $('#manifestazione').val(), <?php echo $prenotazione['Id_Turno'] ?? 'null'; ?>);
    });

    // Funzione per caricare le aree
    function caricaAree(idManifestazione, idAreaSelezionata = null) {
        if (idManifestazione) {
            $.ajax({
                url: 'get_aree.php',
                type: 'POST',
                data: { manifestazione_id: idManifestazione },
                success: function(data) {
                    $('#area').html(data);
                    if (idAreaSelezionata) {
                        $('#area').val(idAreaSelezionata).trigger('change');
                    }
                }
            });
        } else {
            $('#area').html('<option value="">Seleziona prima una manifestazione</option>');
            $('#turno').html('<option value="">Seleziona prima un\'area</option>');
        }
    }

    // Funzione per caricare i turni
    function caricaTurni(idArea, idManifestazione, idTurnoSelezionato = null) {
        if (idArea && idManifestazione) {
            $.ajax({
                url: 'get_turni.php',
                type: 'POST',
                data: { 
                    manifestazione_id: idManifestazione,
                    area_id: idArea 
                },
                success: function(data) {
                    $('#turno').html(data);
                    if (idTurnoSelezionato) {
                        $('#turno').val(idTurnoSelezionato);
                    }
                }
            });
        } else {
            $('#turno').html('<option value="">Seleziona prima un\'area</option>');
        }
    }
});

// Aggiungi questo al tuo script JavaScript
$('form').submit(function(e) {
    var currentTurno = <?php echo $Id_Turno; ?>;
    var selectedTurno = $('#turno').val();
    
    if (currentTurno == selectedTurno) {
        alert('Nessuna modifica da salvare');
        e.preventDefault();
        return false;
    }
    
    // Potresti aggiungere qui una chiamata AJAX per verificare se la prenotazione esiste già
});
</script>

<?php
include_once("../../template_footer.php");
?>