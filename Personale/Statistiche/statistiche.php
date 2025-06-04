<?php
error_reporting(E_ALL); // Report all errors and warnings
ini_set('display_errors', 1); // Display errors on the screen

// Connessione al database
include_once '../../config.php';
include_once '../../queries.php';
include_once '../../session.php';

// Query partecipanti per mese
$sqlPartecipanti = getQueryPartecipantiPerMese();

// Query contributi per manifestazione
$sqlContributi = getQueryContributiPerManifestazione();

// Query espositori per manifestazione
$sqlEspositori = getQueryEspositoriPerManifestazione();

$anno = 2025;
// Query prenotazioni per data
$sqlPrenotazioni = getQueryPrenotazioniPerData($pdo, $anno);


// Recupero dati
try {
    // Partecipanti
    $stmt = $pdo->query($sqlPartecipanti);
    $partecipanti = array_fill(0, 12, 0);
    $hasPartecipanti = false;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $mese = (int)$row['mese'];
        $partecipanti[$mese - 1] = (int)$row['numero_partecipanti'];
        $hasPartecipanti = true;
    }
    error_log("Partecipanti: " . ($hasPartecipanti ? "Dati trovati" : "Nessun dato"));

    // Contributi
    $stmt = $pdo->query($sqlContributi);
    $contributi = [];
    $manifestazioniContributi = [];
    $hasContributi = false;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $manifestazioniContributi[] = $row['nome_manifestazione'];
        $contributi[] = (int)$row['numero_contributi'];
        $hasContributi = true;
    }
    error_log("Contributi: " . ($hasContributi ? "Dati trovati" : "Nessun dato"));

    // Espositori
    $stmt = $pdo->query($sqlEspositori);
    $espositori = [];
    $manifestazioniEspositori = [];
    $hasEspositori = false;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $manifestazioniEspositori[] = $row['nome_manifestazione'];
        $espositori[] = (int)$row['numero_espositori'];
        $hasEspositori = true;
    }
    error_log("Espositori: " . ($hasEspositori ? "Dati trovati" : "Nessun dato"));

    // Prenotazioni per data
    $prenotazioniData = getQueryPrenotazioniPerData($pdo, $anno);
    $prenotazioniDate = [];
    $prenotazioniCount = [];
    $hasPrenotazioni = false;
    foreach ($prenotazioniData as $row) {
        $prenotazioniDate[] = $row['Data'];
        $prenotazioniCount[] = (int)$row['NumeroPartecipanti'];
        $hasPrenotazioni = true;
    }
    error_log("Prenotazioni: " . ($hasPrenotazioni ? "Dati trovati" : "Nessun dato"));
    // Verifica se ci sono dati
    if (!$hasPartecipanti && !$hasContributi && !$hasEspositori && !$hasPrenotazioni) {
        error_log("Nessun dato disponibile per nessuna categoria");
        echo '<div class="alert alert-info text-center">Nessun dato disponibile.</div>';
        exit;
    }
} catch (PDOException $e) {
    error_log("Errore PDO: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Errore nel recupero dei dati: ' . $e->getMessage()
    ]);
    exit;
}
include_once '../../template_header.php';
?>

<!-- Breadcrumbs -->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(../../resources/images/sfondo.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Statistiche Partecipanti</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="../dashboard_personale.php">Dashboard</a></li>
        <li class="active">Statistiche Partecipanti</li>
    </ul>
</section>

<!-- Sezione Partecipanti -->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Andamento del Numero dei Partecipanti</h2>
        <p>Visualizza l'andamento del numero dei partecipanti mensile.</p>
        <canvas id="partecipantiChart" width="400" height="200"></canvas>
    </div>
</section>

<!-- Sezione Espositori -->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Numero di Espositori per Manifestazione</h2>
        <canvas id="espositoriChart" width="400" height="200"></canvas>
    </div>
</section>

<!-- Sezione Contributi -->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Numero di Contributi per Manifestazione</h2>
        <canvas id="contributiChart" width="400" height="200"></canvas>
    </div>
</section>

<!-- Sezione Prenotazioni -->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Numero di Prenotazioni per Data</h2>
        <canvas id="prenotazioniChart" width="400" height="200"></canvas>
    </div>
</section>

<!-- Script Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Partecipanti per mese
    const partecipantiData = <?php echo json_encode($partecipanti); ?>;
    const mesiLabels = ['Gennaio', 'Febbraio', 'Marzo', 'Aprile', 'Maggio', 'Giugno', 'Luglio', 'Agosto', 'Settembre', 'Ottobre', 'Novembre', 'Dicembre'];

    new Chart(document.getElementById('partecipantiChart'), {
        type: 'line',
        data: {
            labels: mesiLabels,
            datasets: [{
                label: 'Numero di Partecipanti',
                data: partecipantiData,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2
            }]
        },
        options: {
            scales: { y: { beginAtZero: true } }
        }
    });

    // Espositori
    const espositoriData = <?php echo json_encode($espositori); ?>;
    const espositoriLabels = <?php echo json_encode($manifestazioniEspositori); ?>;

    new Chart(document.getElementById('espositoriChart'), {
        type: 'bar',
        data: {
            labels: espositoriLabels,
            datasets: [{
                label: 'Espositori',
                data: espositoriData,
                backgroundColor: 'rgba(75, 192, 192, 0.3)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2
            }]
        },
        options: {
            scales: { y: { beginAtZero: true } }
        }
    });

    // Contributi
    const contributiData = <?php echo json_encode($contributi); ?>;
    const contributiLabels = <?php echo json_encode($manifestazioniContributi); ?>;

    new Chart(document.getElementById('contributiChart'), {
        type: 'bar',
        data: {
            labels: contributiLabels,
            datasets: [{
                label: 'Contributi',
                data: contributiData,
                backgroundColor: 'rgba(153, 102, 255, 0.3)',
                borderColor: 'rgba(153, 102, 255, 1)',
                borderWidth: 2
            }]
        },
        options: {
            scales: { y: { beginAtZero: true } }
        }
    });

    // Prenotazioni per data
    const prenotazioniLabels = <?php echo json_encode($prenotazioniDate); ?>;
    const prenotazioniData = <?php echo json_encode($prenotazioniCount); ?>;

    new Chart(document.getElementById('prenotazioniChart'), {
        type: 'line',
        data: {
            labels: prenotazioniLabels,
            datasets: [{
                label: 'Prenotazioni',
                data: prenotazioniData,
                backgroundColor: 'rgba(255, 159, 64, 0.2)',
                borderColor: 'rgba(255, 159, 64, 1)',
                borderWidth: 2
            }]
        },
        options: {
            scales: { y: { beginAtZero: true } }
        }
    });
</script>

<?php include_once '../../template_footer.php'; ?>
