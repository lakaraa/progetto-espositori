<?php

function getManifestazioniTop6($pdo) {
    $sql = "SELECT * FROM manifestazione LIMIT 6";  
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getManifestazioni($pdo) {
    $sql = "SELECT * FROM manifestazione"; 
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function insertMessaggio($pdo, $nome, $telefono, $messaggio) {
    $sql = "INSERT INTO messaggio (Nome, Telefono, Messaggio, Data_Invio) VALUES (:nome, :telefono, :messaggio, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
    $stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR);
    $stmt->bindParam(':messaggio', $messaggio, PDO::PARAM_STR);

    try {
        return $stmt->execute(); 
    } catch (PDOException $e) {
        return false;
    }
}

function getContributi($pdo) {
    $query = "SELECT * FROM contributi"; 
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC); 
}