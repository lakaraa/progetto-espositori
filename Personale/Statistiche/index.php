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

// Query prenotazioni per data
$sqlPrenotazioni = getQueryPrenotazioniPerData();

print_r($sqlPartecipanti);
print_r($sqlContributi);
print_r($sqlEspositori);