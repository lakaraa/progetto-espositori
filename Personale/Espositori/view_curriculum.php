<?php
include_once '../../config.php';
include_once '../../queries.php';

// Recupera l'ID dell'espositore dalla query string
$idEspositore = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Recupera i dettagli dell'espositore
$espositore = getEspositoreById($pdo, $idEspositore);

if (!$espositore || empty($espositore['Curriculum'])) {
    echo "<p style='color: red;'>Curriculum non trovato o espositore non valido.</p>";
    exit;
}

// Imposta gli header per forzare la visualizzazione del PDF nel browser
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="curriculum.pdf"');

// Invia i dati binari del curriculum al browser
echo $espositore['Curriculum'];
exit;
?>
