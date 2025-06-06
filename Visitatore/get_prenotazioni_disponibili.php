<?php
error_reporting(E_ALL); // Report all errors and warnings
ini_set('display_errors', 1); // Display errors on the screen

include_once '../config.php';  
include_once '../queries.php';  
include_once '../session.php';      

$prenotazioni = getPrenotazioniDisponibili($pdo);  

if (count($prenotazioni) > 0) {
    foreach ($prenotazioni as $row) {
        echo "<tr>
            <td>" . htmlspecialchars($row['Manifestazione']) . "</td>
            <td>" . htmlspecialchars($row['DataInizio']) . "</td>
            <td>" . htmlspecialchars($row['Durata']) . "</td>
            <td>" . htmlspecialchars($row['PostiDisponibili']) . "</td>
            <td><a href='form_prenotazione.php?id=" . urlencode($row['Id_Manifestazione']) . "' style='color: rgb(74, 196, 207); text-decoration: underline;'>Prenota</a></td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='5' class='text-center'>Nessuna prenotazione disponibile al momento.</td></tr>";
}
?>

