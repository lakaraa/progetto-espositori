<?php
include_once '../config.php';
include_once '../session.php';
include_once '../queries.php';

// Verifica che l'utente sia loggato e sia un espositore
if (!isset($_SESSION['id_utente']) || $_SESSION['ruolo'] !== 'Espositore') {
    die('Accesso non autorizzato');
}

try {
    // Query per ottenere le manifestazioni disponibili
    $query = "SELECT m.*, 
              (SELECT COUNT(*) FROM Esposizione e WHERE e.Id_Manifestazione = m.Id_Manifestazione) as ContributiAttuali,
              (SELECT COUNT(*) FROM Contributo c 
               JOIN Esposizione e ON c.Id_Contributo = e.Id_Contributo 
               WHERE e.Id_Manifestazione = m.Id_Manifestazione 
               AND c.Id_Utente = :userId) as HaContributo
              FROM Manifestazione m 
              WHERE m.Data >= CURDATE()
              ORDER BY m.Data ASC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute(['userId' => $_SESSION['id_utente']]);
    $manifestazioni = $stmt->fetchAll();

    if (empty($manifestazioni)) {
        echo '<tr><td colspan="5" class="text-center">Nessuna manifestazione disponibile</td></tr>';
    } else {
        foreach ($manifestazioni as $manifestazione) {
            $dataInizio = new DateTime($manifestazione['Data']);
            $dataFine = (clone $dataInizio)->modify('+' . $manifestazione['Durata'] . ' days');
            $oggi = new DateTime();
            
            // Calcola se la manifestazione è già iniziata
            $isStarted = $dataInizio <= $oggi;
            
            // Determina se mostrare il pulsante di candidatura
            $showCandidatura = !$isStarted && $manifestazione['HaContributo'] == 0;
            
            echo '<tr>';
            echo '<td>' . htmlspecialchars($manifestazione['Nome']) . '</td>';
            echo '<td>' . $dataInizio->format('d/m/Y') . '</td>';
            echo '<td>' . $manifestazione['Durata'] . ' giorni</td>';
            echo '<td>' . htmlspecialchars($manifestazione['Descrizione']) . '</td>';
            echo '<td>';
            
            if ($showCandidatura) {
                echo '<a href="candidati.php?id=' . $manifestazione['Id_Manifestazione'] . '" class="btn btn-primary btn-sm">Candidati</a>';
            } elseif ($manifestazione['HaContributo'] > 0) {
                echo '<span class="text-info">Candidatura già inviata</span>';
            } else {
                echo '<span class="text-muted">Manifestazione in corso</span>';
            }
            
            echo '</td>';
            echo '</tr>';
        }
    }
} catch (PDOException $e) {
    error_log('Errore nel recupero delle manifestazioni: ' . $e->getMessage());
    echo '<tr><td colspan="5" class="text-center text-danger">Errore nel caricamento delle manifestazioni</td></tr>';
}
?> 