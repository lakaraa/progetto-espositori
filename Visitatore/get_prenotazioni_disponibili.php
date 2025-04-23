<?php
include_once '../config.php';  
include_once '../queries.php';  

$prenotazioni = getPrenotazioniDisponibili($pdo);  

if (count($prenotazioni) > 0) {
    foreach ($prenotazioni as $row) {
        echo "<tr>
            <td>{$row['Manifestazione']}</td>
            <td>{$row['DataInizio']}</td>
            <td>{$row['Durata']}</td>
            <td>{$row['PostiDisponibili']}</td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='4' class='text-center'>Nessuna prenotazione disponibile al momento.</td></tr>";
}
?>
