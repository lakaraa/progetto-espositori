<?php
include_once '../../config.php';
include_once '../../queries.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['manifestazione_id']) && isset($_POST['area_id'])) {
    $manifestazioneId = $_POST['manifestazione_id'];
    $areaId = $_POST['area_id'];
    $turni = getTurniByArea($pdo, $manifestazioneId, $areaId);

    if ($turni) {            
        echo '<option value="" style="color: black; background-color: white;">Seleziona un turno</option>';
        foreach ($turni as $turno) {
            echo '<option value="' . htmlspecialchars($turno['Id_Turno']) . '" style="color: black; background-color: white;">' . 
                 htmlspecialchars($turno['Data']) . ' - ' . htmlspecialchars($turno['Ora']) . 
                 '</option>';
        }
    } else {
        echo '<option value="">Nessun turno disponibile</option>';
    }
} else {
    echo '<option value="">Richiesta non valida</option>';
}
?>
