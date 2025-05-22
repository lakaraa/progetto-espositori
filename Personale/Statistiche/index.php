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

print_r($sqlPartecipanti);
print_r($sqlContributi);
print_r($sqlEspositori);
print_r($sqlPrenotazioni);