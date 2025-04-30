<?php
include_once '../config.php';
include_once '../session.php';
include_once '../queries.php';

// Verifica che l'utente sia loggato e sia un espositore
if (!isset($_SESSION['id_utente']) || $_SESSION['ruolo'] !== 'Espositore') {
    die('Accesso non autorizzato');
}

try {
    // Query per ottenere i contributi accettati dell'espositore
    $query = "SELECT c.*, m.Nome as NomeManifestazione, m.Data as Data_Manifestazione,
              CASE 
                WHEN m.Data > CURDATE() THEN 'In Attesa'
                WHEN DATE_ADD(m.Data, INTERVAL m.Durata DAY) < CURDATE() THEN 'Completato'
                ELSE 'In Corso'
              END as StatoContributo
              FROM Contributo c 
              JOIN Esposizione e ON c.Id_Contributo = e.Id_Contributo
              JOIN Manifestazione m ON e.Id_Manifestazione = m.Id_Manifestazione 
              WHERE c.Id_Utente = :userId 
              AND c.Accettazione = 'Accettato'
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
                case 'In Corso':
                    $statoClass = 'text-success';
                    break;
                case 'Completato':
                    $statoClass = 'text-info';
                    break;
                case 'In Attesa':
                    $statoClass = 'text-warning';
                    break;
            }
            
            echo '<tr>';
            echo '<td>' . htmlspecialchars($contributo['NomeManifestazione']) . '</td>';
            echo '<td>' . date('d/m/Y', strtotime($contributo['Data_Manifestazione'])) . '</td>';
            echo '<td>' . htmlspecialchars($contributo['Titolo']) . '</td>';
            echo '<td class="' . $statoClass . '">' . htmlspecialchars($contributo['StatoContributo']) . '</td>';
            echo '<td>';
            
            // Aggiungi azioni in base allo stato
            if ($contributo['StatoContributo'] === 'In Corso') {
                echo '<a href="gestisci_contributo.php?id=' . $contributo['Id_Contributo'] . '" class="btn btn-primary btn-sm">Gestisci</a>';
            } elseif ($contributo['StatoContributo'] === 'In Attesa') {
                echo '<span class="text-info">In attesa di inizio</span>';
            } else {
                echo '<a href="visualizza_contributo.php?id=' . $contributo['Id_Contributo'] . '" class="btn btn-primary btn-sm">Visualizza</a>';
            }
            
            echo '</td>';
            echo '</tr>';
        }
    }
} catch (PDOException $e) {
    error_log('Errore nel recupero dei contributi: ' . $e->getMessage());
    echo '<tr><td colspan="5" class="text-center text-danger">Errore nel caricamento dei contributi</td></tr>';
}
?> 