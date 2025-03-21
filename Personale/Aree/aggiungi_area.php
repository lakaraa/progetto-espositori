<?php
include_once("../../config.php");
include_once("../../queries.php");
include_once("../../session.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recupera i dati dal form
    $idManifestazione = $_POST['Id_Manifestazione'];
    $nome = $_POST['Nome'];
    $capienza = $_POST['Capienza_Massima'];
    $descrizione = $_POST['Descrizione'];

    // Controlla che tutti i campi siano compilati
    if (empty($idManifestazione) || empty($nome) || empty($capienza) || empty($descrizione)) {
        echo "Tutti i campi sono obbligatori.";
        exit;
    }

    // Aggiungi l'area al database
    try {
        $pdo = new PDO($dsn, $db_user, $db_password, $options);
        $result = addArea($pdo, $nome, $descrizione, $capienza, $idManifestazione);

        if ($result) {
            header("Location: gestisci_aree.php?success=1");
            exit;
        } else {
            echo "Errore durante l'aggiunta dell'area.";
        }
    } catch (PDOException $e) {
        echo "Errore di connessione al database: " . $e->getMessage();
    }
}
?>