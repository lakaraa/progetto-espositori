<?php
include_once("../../config.php");
include_once("../../session.php");
include_once("../../queries.php");

// Recupera gli id dell'utente e del turno
$Id_Utente = isset($_GET['Id_Utente']) ? intval($_GET['Id_Utente']) : 0;
$Id_Turno = isset($_GET['Id_Turno']) ? intval($_GET['Id_Turno']) : 0;

// Recupera i dettagli della prenotazione
$prenotazione = getPrenotazioneById($pdo, $Id_Utente, $Id_Turno);

if (!$prenotazione) {
    $_SESSION['error'] = "Prenotazione non trovata.";
    header("Location: modifica_prenotazione.php");
    exit;
}

$manifestazioni = getManifestazioni($pdo);
$turni = getTurniByArea($pdo, $prenotazione['Id_Area']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newIdTurno = isset($_POST['Id_Turno']) ? intval($_POST['Id_Turno']) : 0;
    $originalIdTurno = isset($_POST['original_Id_Turno']) ? intval($_POST['original_Id_Turno']) : 0;
    
    // Se è una richiesta AJAX
    if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        
        if ($newIdTurno === 0 || $originalIdTurno === 0) {
            echo json_encode(['success' => false, 'error' => 'Dati mancanti per l\'aggiornamento.']);
            exit;
        }
        
        if ($newIdTurno === $originalIdTurno) {
            echo json_encode(['success' => false, 'error' => 'Nessuna modifica da salvare.']);
            exit;
        }
        
        try {
            // Controlla se esiste già una prenotazione con gli stessi dati
            $existingPrenotazione = checkExistingPrenotazione($pdo, $Id_Utente, $newIdTurno);
            if ($existingPrenotazione) {
                echo json_encode(['success' => false, 'error' => 'Esiste già una prenotazione per questo utente nel turno selezionato.']);
                exit;
            }
            
            $result = updatePrenotazione($pdo, $Id_Utente, $originalIdTurno, $newIdTurno);
            if ($result) {
                echo json_encode(['success' => true]);
                exit;
            } else {
                echo json_encode(['success' => false, 'error' => 'Errore durante l\'aggiornamento della prenotazione.']);
                exit;
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => 'Errore database: ' . $e->getMessage()]);
            exit;
        }
    }
}
include_once("../../template_header.php");

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
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['warning'])): ?>
            <div class="alert alert-warning"><?php echo $_SESSION['warning']; unset($_SESSION['warning']); ?></div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <form class="rd-form rd-mailform" method="post" action="" id="modificaPrenotazioneForm">
            <input type="hidden" name="Id_Utente" value="<?php echo $Id_Utente; ?>">
            <input type="hidden" name="original_Id_Turno" value="<?php echo $Id_Turno; ?>">
            
            <div class="row row-50">
                <div class="col-md-12">
                    <div class="form-wrap">
                        <input class="form-input" id="prenotazione-nome-cognome" type="text" name="NomeCognome" 
                               value="<?php echo htmlspecialchars($prenotazione['Nome_Visitatore'] . ' ' . $prenotazione['Cognome_Visitatore']); ?>" 
                               readonly>
                        <label class="form-label" for="prenotazione-nome-cognome">Nome e Cognome</label>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-wrap">
                        <input class="form-input" id="prenotazione-manifestazione" type="text" name="manifestazione" 
                               value="<?php echo htmlspecialchars($prenotazione['Nome_Manifestazione']); ?>" 
                               readonly>
                        <label class="form-label" for="prenotazione-manifestazione">Manifestazione</label>
                    </div>
                </div>

                <!-- Seleziona Area -->
                <div class="col-md-6">
                    <select class="form-input" id="area" name="Id_Area">
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Carica le aree iniziali per la manifestazione esistente
    caricaAree(<?php echo $prenotazione['Id_Manifestazione']; ?>, <?php echo $prenotazione['Id_Area'] ?? 'null'; ?>, <?php echo $prenotazione['Id_Turno'] ?? 'null'; ?>);

    // Gestione cambio area
    $('#area').change(function() {
        var selectedArea = $(this).val();
        if (selectedArea) {
            caricaTurni(selectedArea, <?php echo $prenotazione['Id_Manifestazione']; ?>, <?php echo $prenotazione['Id_Turno'] ?? 'null'; ?>);
        } else {
            $('#turno').html('<option value="">Seleziona prima un\'area</option>');
        }
    });

    // Funzione per caricare le aree
    function caricaAree(idManifestazione, idAreaSelezionata = null, idTurnoSelezionato = null) {
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
                },
                error: function(xhr, status, error) {
                    console.error("Errore nel caricamento delle aree:", error);
                    $('#area').html('<option value="">Errore nel caricamento delle aree</option>');
                }
            });
        } else {
            $('#area').html('<option value="">Errore nel caricamento delle aree</option>');
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
                    area_id: idArea 
                },
                success: function(data) {
                    $('#turno').html(data);
                    if (idTurnoSelezionato) {
                        $('#turno').val(idTurnoSelezionato);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Errore nel caricamento dei turni:", error);
                    $('#turno').html('<option value="">Errore nel caricamento dei turni</option>');
                }
            });
        } else {
            $('#turno').html('<option value="">Seleziona prima un\'area</option>');
        }
    }

    // Gestione submit del form
    $('#modificaPrenotazioneForm').on('submit', function(e) {
        e.preventDefault();
        
        var currentTurno = <?php echo $Id_Turno; ?>;
        var selectedTurno = $('#turno').val();
        var selectedArea = $('#area').val();
        
        if (!selectedArea) {
            alert('Seleziona un\'area');
            return false;
        }
        
        if (!selectedTurno) {
            alert('Seleziona un turno');
            return false;
        }
        
        if (currentTurno == selectedTurno) {
            alert('Nessuna modifica da salvare');
            return false;
        }

        // Invia il form tramite AJAX
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    window.location.href = 'modifica_prenotazione.php?Id_Utente=<?php echo $Id_Utente; ?>&Id_Turno=' + selectedTurno;
                } else {
                    alert(response.error || 'Errore durante il salvataggio');
                }
            },
            error: function(xhr, status, error) {
                console.error('Errore:', xhr.responseText);
                alert('Errore durante il salvataggio. Riprova più tardi.');
            }
        });
    });
});
</script>

<?php
include_once("../../template_footer.php");
?>