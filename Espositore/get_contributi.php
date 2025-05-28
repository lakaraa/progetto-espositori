<?php
error_reporting(E_ALL); // Report all errors and warnings
ini_set('display_errors', 1); // Display errors on the screen

include_once '../config.php';
include_once '../session.php';
include_once '../queries.php';

// Verifica che l'utente sia loggato
if (!isset($_SESSION['id_utente'])) {
    die('Accesso non autorizzato');
}

try {
    $contributi = getContributiByUser($pdo, $_SESSION['id_utente']);

    if (empty($contributi)) {
        echo '<tr><td colspan="5" class="text-center">Nessun contributo trovato</td></tr>';
    } else {
        foreach ($contributi as $contributo) {
            $statoClass = '';
            switch ($contributo['Stato']) {
                case 'Accettato':
                    $statoClass = 'text-success font-weight-bold';
                    break;
                case 'Rifiutato':
                    $statoClass = 'text-danger font-weight-bold';
                    break;
                case 'In Approvazione':
                    $statoClass = 'text-warning font-weight-bold';
                    break;
            }
            
            // Gestione del testo lungo per titolo e sintesi
            $titolo = htmlspecialchars($contributo['Titolo']);
            $titoloId = 'titolo-' . uniqid();
            $titoloShort = strlen($titolo) > 10 ? substr($titolo, 0, 10) . '...' : $titolo;
            $hasMoreTitolo = strlen($titolo) > 10;

            $sintesi = htmlspecialchars($contributo['Sintesi']);
            $sintesiId = 'sintesi-' . uniqid();
            $sintesiShort = strlen($sintesi) > 10 ? substr($sintesi, 0, 10) . '...' : $sintesi;
            $hasMore = strlen($sintesi) > 10;
            
            echo '<tr>';
            echo '<td>' . htmlspecialchars($contributo['Nome_Manifestazione'] ?? 'Non assegnato') . '</td>';
            echo '<td>' . ($contributo['Data_Manifestazione'] ? date('d/m/Y', strtotime($contributo['Data_Manifestazione'])) : 'N/A') . '</td>';
            echo '<td class="titolo-cell">';
            echo '<div class="titolo-content">';
            echo '<span class="titolo-short" id="' . $titoloId . '">' . $titoloShort . '</span>';
            if ($hasMoreTitolo) {
                echo '<button class="btn btn-link btn-sm leggi-piu" onclick="toggleText(\'' . $titoloId . '\', \'' . addslashes($titolo) . '\')">Leggi di più</button>';
            }
            echo '</div>';
            echo '</td>';
            echo '<td class="sintesi-cell">';
            echo '<div class="sintesi-content">';
            echo '<span class="sintesi-short" id="' . $sintesiId . '">' . $sintesiShort . '</span>';
            if ($hasMore) {
                echo '<button class="btn btn-link btn-sm leggi-piu" onclick="toggleText(\'' . $sintesiId . '\', \'' . addslashes($sintesi) . '\')">Leggi di più</button>';
            }
            echo '</div>';
            echo '</td>';
            echo '<td class="' . $statoClass . '">' . htmlspecialchars($contributo['Stato']) . '</td>';
            echo '</tr>';
        }
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Errore nel recupero dei contributi']);
}
?>

<style>
.sintesi-cell, .titolo-cell {
    max-width: 300px;
    position: relative;
}

.sintesi-content, .titolo-content {
    position: relative;
}

.leggi-piu {
    color: rgb(74, 196, 207);
    padding: 0;
    margin-left: 5px;
    font-size: 0.9em;
    text-decoration: none;
    background: none;
    border: none;
    cursor: pointer;
}

.leggi-piu:hover {
    text-decoration: underline;
    color: rgb(74, 196, 207);
}

.sintesi-expanded, .titolo-expanded {
    white-space: normal;
    word-wrap: break-word;
}
</style>

<script>
function toggleText(id, fullText) {
    const element = document.getElementById(id);
    const button = element.nextElementSibling;
    
    if (element.classList.contains('sintesi-expanded') || element.classList.contains('titolo-expanded')) {
        // Collapse
        element.textContent = fullText.substring(0, 10) + '...';
        element.classList.remove('sintesi-expanded', 'titolo-expanded');
        button.textContent = 'Leggi di più';
    } else {
        // Expand
        element.textContent = fullText;
        element.classList.add(id.startsWith('sintesi') ? 'sintesi-expanded' : 'titolo-expanded');
        button.textContent = 'Mostra meno';
    }
}
</script> 