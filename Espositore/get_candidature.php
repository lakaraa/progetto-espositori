<?php
include_once '../config.php';
include_once '../session.php';
include_once '../queries.php';

// Verifica che l'utente sia loggato e sia un espositore
if (!isset($_SESSION['id_utente']) || $_SESSION['ruolo'] !== 'Espositore') {
    die('Accesso non autorizzato');
}

$userId = $_SESSION['id_utente'];

try {
    // Query per ottenere le candidature dell'espositore
    $query = "SELECT c.*, m.Nome as NomeManifestazione, m.Data as Data_Manifestazione,
              CASE 
                WHEN c.Accettazione = 'Accettato' THEN 'Accettata'
                WHEN c.Accettazione = 'Rifiutato' THEN 'Rifiutata'
                ELSE 'In Attesa'
              END as Stato
              FROM Contributo c 
              JOIN Esposizione e ON c.Id_Contributo = e.Id_Contributo
              JOIN Manifestazione m ON e.Id_Manifestazione = m.Id_Manifestazione 
              WHERE c.Id_Utente = :userId 
              ORDER BY c.Data_Candidatura DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute(['userId' => $userId]);
    $candidature = $stmt->fetchAll();

    if (empty($candidature)) {
        echo '<tr><td colspan="5" class="text-center">Nessuna candidatura trovata</td></tr>';
    } else {
        foreach ($candidature as $candidatura) {
            $statoClass = '';
            switch ($candidatura['Stato']) {
                case 'Accettata':
                    $statoClass = 'text-success';
                    break;
                case 'Rifiutata':
                    $statoClass = 'text-danger';
                    break;
                case 'In Attesa':
                    $statoClass = 'text-warning';
                    break;
            }
            
            echo '<tr>';
            echo '<td>' . htmlspecialchars($candidatura['NomeManifestazione']) . '</td>';
            echo '<td>' . date('d/m/Y', strtotime($candidatura['Data_Candidatura'])) . '</td>';
            echo '<td class="' . $statoClass . '">' . htmlspecialchars($candidatura['Stato']) . '</td>';
            echo '<td>' . htmlspecialchars($candidatura['Sintesi'] ?? '') . '</td>';
            echo '<td>';
            
            // Aggiungi azioni in base allo stato
            if ($candidatura['Stato'] === 'In Attesa') {
                echo '<button class="btn btn-danger btn-sm" onclick="annullaCandidatura(' . $candidatura['Id_Contributo'] . ')">Annulla</button>';
            } elseif ($candidatura['Stato'] === 'Accettata') {
                echo '<a href="gestisci_contributo.php?id=' . $candidatura['Id_Contributo'] . '" class="btn btn-primary btn-sm">Gestisci</a>';
            }
            
            echo '</td>';
            echo '</tr>';
        }
    }
} catch (PDOException $e) {
    error_log('Errore nel recupero delle candidature: ' . $e->getMessage());
    echo '<tr><td colspan="5" class="text-center text-danger">Errore nel caricamento delle candidature</td></tr>';
}
?>

<script>
function annullaCandidatura(idContributo) {
    if (confirm('Sei sicuro di voler annullare questa candidatura?')) {
        $.ajax({
            url: 'annulla_candidatura.php',
            method: 'POST',
            data: { id_contributo: idContributo },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Errore durante l\'annullamento della candidatura');
                }
            },
            error: function() {
                alert('Errore durante l\'annullamento della candidatura');
            }
        });
    }
}
</script> 