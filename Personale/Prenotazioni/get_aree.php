<?php
include_once '../../config.php';
include_once '../../queries.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['manifestazione_id'])) {
    $manifestazioneId = $_POST['manifestazione_id'];
    $aree = getAreeByManifestazione($pdo, $manifestazioneId);

    if ($aree) {
        echo '<option value="" style="color: black; background-color: white;">Seleziona un\'area</option>';
        foreach ($aree as $area) {
            echo '<option value="' . htmlspecialchars($area['Id_Area']) . '" style="color: black; background-color: white;">' . 
             htmlspecialchars($area['Nome']) . ' (Capienza: ' . htmlspecialchars($area['Capienza_Massima']) . ')' . 
             '</option>';
        }
    } else {
        echo '<option value="">Nessuna area disponibile</option>';
    }
} else {
    echo '<option value="">Richiesta non valida</option>';
}
?>
