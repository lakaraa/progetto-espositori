<?php
include_once '../../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['manifestazione_id'])) {
    $manifestazioneId = $_POST['manifestazione_id'];

    // Recupera le aree collegate alla manifestazione
    $sqlAree = "
        SELECT area.Id_Area, area.Nome, area.Capienza_Massima
        FROM manifestazione
        JOIN area ON manifestazione.Id_Manifestazione = area.Id_Manifestazione
        WHERE manifestazione.Id_Manifestazione = :manifestazione_id
    ";
    $stmtAree = $pdo->prepare($sqlAree);
    $stmtAree->bindParam(':manifestazione_id', $manifestazioneId, PDO::PARAM_INT);
    $stmtAree->execute();
    $aree = $stmtAree->fetchAll(PDO::FETCH_ASSOC);

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
