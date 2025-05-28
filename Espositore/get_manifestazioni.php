<?php
error_reporting(E_ALL); // Report all errors and warnings
ini_set('display_errors', 1); // Display errors on the screen

include_once '../config.php';
include_once '../session.php';
include_once '../queries.php';

// Verifica che l'utente sia loggato e sia un espositore
if (!isset($_SESSION['id_utente']) || $_SESSION['ruolo'] !== 'Espositore') {
    die('Accesso non autorizzato');
}

try {
    $manifestazioni = getManifestazioniDisponibili_dashboard_espositore($pdo, $_SESSION['id_utente']);

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
            echo '<td>' . $manifestazione['Durata'] . '</td>';
            echo '<td>' . htmlspecialchars($manifestazione['Descrizione']) . '</td>';
            echo '<td>';
            
            if ($showCandidatura) {
                echo '<a href="form_candidatura.php?id=' . $manifestazione['Id_Manifestazione'] . '" class="btn btn-primary btn-sm">Candidati</a>';
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
    echo json_encode(['error' => 'Errore nel recupero delle manifestazioni']);
}
?> 