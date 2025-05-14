<?php
include_once '../config.php';  
include_once '../queries.php';  
include_once '../session.php';  

session_start(); 

$userId = $_SESSION['id_utente'];  

$prenotazioni = getPrenotazioniInCorso($pdo, $userId);  

if (count($prenotazioni) > 0) {
    foreach ($prenotazioni as $row) {
        echo "<tr>
            <td>" . htmlspecialchars($row['Manifestazione']) . "</td>
            <td>" . htmlspecialchars($row['DataInizio']) . "</td>
            <td>" . htmlspecialchars($row['Durata']) . "</td>
            <td>" . htmlspecialchars($row['Area']) . "</td>
            <td>" . htmlspecialchars($row['Data']) . "</td>
            <td>" . htmlspecialchars($row['Ora']) . "</td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='6' class='text-center'>Nessuna prenotazione in corso trovata.</td></tr>";
}
?>
