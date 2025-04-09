<?php
include_once '../../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['manifestazione_id'])) {
    $manifestazioneId = $_POST['manifestazione_id'];

    // Recupera direttamente i turni collegati alla manifestazione
    $sqlTurni = "
        SELECT turno.Id_Turno, turno.Data, turno.Ora
        FROM manifestazione
        JOIN area ON manifestazione.Id_Manifestazione = area.Id_Manifestazione
        JOIN turno ON turno.Id_Area = area.Id_Area
        WHERE manifestazione.Id_Manifestazione = :id
    ";
    $stmtTurni = $pdo->prepare($sqlTurni);
    $stmtTurni->bindParam(':id', $manifestazioneId, PDO::PARAM_INT);
    $stmtTurni->execute();
    $turni = $stmtTurni->fetchAll(PDO::FETCH_ASSOC);

    if ($turni) {
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
