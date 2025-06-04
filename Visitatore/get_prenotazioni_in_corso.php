<?php
error_reporting(E_ALL); // Report all errors and warnings
ini_set('display_errors', 1); // Display errors on the screen

include_once '../config.php';  
include_once '../queries.php';  
include_once '../session.php';  


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
