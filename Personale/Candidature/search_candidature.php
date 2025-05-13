<?php
include_once("../../config.php");
include_once("../../queries.php");
include_once("../../session.php");

// Imposta l'header per la risposta JSON
header('Content-Type: application/json');

// Recupera il parametro di ricerca
$manifestazione = $_GET['manifestazione'] ?? '';

// Recupera le candidature filtrate
$candidature = getCandidatureInApprovazione($pdo, $manifestazione);

// Restituisci i risultati come JSON
echo json_encode($candidature);
?> 