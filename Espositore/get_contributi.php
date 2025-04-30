<?php
include_once '../config.php';
include_once '../session.php';
include_once '../queries.php';

// Verifica che l'utente sia loggato e sia un espositore
if (!isset($_SESSION['id_utente']) || $_SESSION['ruolo'] !== 'Espositore') {
    die('Accesso non autorizzato');
}

try {
    // Query per ottenere i contributi dell'espositore
    $query = "SELECT c.*, m.Nome as NomeManifestazione, m.Data as Data_Manifestazione,
              c.Accettazione as StatoContributo
              FROM Contributo c 
              JOIN Esposizione e ON c.Id_Contributo = e.Id_Contributo
              JOIN Manifestazione m ON e.Id_Manifestazione = m.Id_Manifestazione 
              WHERE c.Id_Utente = :userId 
              ORDER BY m.Data DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute(['userId' => $_SESSION['id_utente']]);
    $contributi = $stmt->fetchAll();

    if (empty($contributi)) {
        echo '<tr><td colspan="5" class="text-center">Nessun contributo trovato</td></tr>';
    } else {
        foreach ($contributi as $contributo) {
            $statoClass = '';
            switch ($contributo['StatoContributo']) {
                case 'Accettato':
                    $statoClass = 'text-success';
                    break;
                case 'Rifiutato':
                    $statoClass = 'text-danger';
                    break;
                case 'In Approvazione':
                    $statoClass = 'text-warning';
                    break;
            }
            
            echo '<tr>';
            echo '<td>' . htmlspecialchars($contributo['NomeManifestazione']) . '</td>';
            echo '<td>' . date('d/m/Y', strtotime($contributo['Data_Manifestazione'])) . '</td>';
            echo '<td>' . date('d/m/Y', strtotime($contributo['Data_Manifestazione'])) . '</td>';
            echo '<td>' . htmlspecialchars($contributo['Titolo']) . '</td>';
            echo '<td class="' . $statoClass . '">' . htmlspecialchars($contributo['StatoContributo']) . '</td>';
            echo '</tr>';
        }
    }
} catch (PDOException $e) {
    error_log('Errore nel recupero dei contributi: ' . $e->getMessage());
    echo '<tr><td colspan="5" class="text-center text-danger">Errore nel caricamento dei contributi</td></tr>';
}
?> 