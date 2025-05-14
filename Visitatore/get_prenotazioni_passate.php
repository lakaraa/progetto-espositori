<?php
include_once '../config.php';
include_once '../queries.php';
include_once '../session.php';

$userId = $_SESSION['id_utente'];
$prenotazioni = getPrenotazioniPassate($pdo, $userId);  // Passa il parametro $pdo per la connessione al DB

if (count($prenotazioni) > 0) {
    foreach ($prenotazioni as $row) {
        echo "<tr>
            <td>{$row['Manifestazione']}</td>
            <td>{$row['DataInizio']}</td>
            <td>{$row['Durata']}</td>
            <td>{$row['Area']}</td>
            <td>{$row['Turno']}</td>
            <td>{$row['Ora']}</td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='6' class='text-center'>Nessuna prenotazione passata trovata.</td></tr>";
}
?>
