<?php
include_once '../../config.php';

// Recupera i turni in base alla manifestazione
if (isset($_POST['manifestazione_id'])) {
    $manifestazione_id = $_POST['manifestazione_id'];

    // Ottieni i turni per questa manifestazione
    $sql = "SELECT t.Id_Turno, t.Nome 
            FROM turno t
            JOIN area a ON t.Id_Area = a.Id_Area
            JOIN manifestazione m ON a.Id_Manifestazione = m.Id_Manifestazione
            WHERE m.Id_Manifestazione = :manifestazione_id";  // Aggiunto filtro per manifestazione_id

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':manifestazione_id', $manifestazione_id, PDO::PARAM_INT);
    $stmt->execute();

    $turni = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Costruisci le opzioni dei turni
    foreach ($turni as $turno) {
        echo '<option value="' . $turno['Id_Turno'] . '">' . htmlspecialchars($turno['Nome']) . '</option>';
    }
}
?>
