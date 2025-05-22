<?php
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

// Query prenotazioni per data
$sqlPrenotazioni = getQueryPrenotazioniPerData();


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
    $stmt = $pdo->query($sqlPrenotazioni);
    $prenotazioniDate = [];
    $prenotazioniCount = [];
    $hasPrenotazioni = false;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $prenotazioniDate[] = $row['turno_data'];
        $prenotazioniCount[] = (int)$row['numero_prenotazioni'];
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