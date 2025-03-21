<?php

function getManifestazioniTop6($pdo) 
{
    $sql = "SELECT * FROM manifestazione LIMIT 6";  
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getManifestazioni($pdo) 
{
    $sql = "SELECT * FROM manifestazione"; 
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function insertMessaggio($pdo, $nome, $telefono, $messaggio) 
{
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
function getContributi($pdo) 
{
    $query = "SELECT * FROM contributo";
    $stmt = $pdo->prepare($query); 
    
    if (!$stmt) {
        echo "Errore nella preparazione della query!";
        return [];
    }
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC); 
}
function getUserByEmail($pdo, $email) 
{
    $sql = "SELECT * FROM utente WHERE Email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function AddArea($pdo, $nome, $descrizione, $capienzaMassima, $idManifestazione) 
{
    $sql = "INSERT INTO area (Nome, Descrizione, Capienza_Massima, Id_Manifestazione) 
            VALUES (:nome, :descrizione, :capienzaMassima, :idManifestazione)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
    $stmt->bindParam(':descrizione', $descrizione, PDO::PARAM_STR);
    $stmt->bindParam(':capienzaMassima', $capienzaMassima, PDO::PARAM_INT);
    $stmt->bindParam(':idManifestazione', $idManifestazione, PDO::PARAM_INT);
    return $stmt->execute();
}
function DeleteArea($pdo, $idArea) 
{
    $sql = "DELETE FROM area WHERE Id_Area = :idArea";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idArea', $idArea, PDO::PARAM_INT);
    return $stmt->execute();
}
function UpdateArea($pdo, $idArea, $nome, $descrizione, $capienzaMassima, $idManifestazione) {
    $sql = "UPDATE area 
            SET Nome = :nome, Descrizione = :descrizione, Capienza_Massima = :capienzaMassima, Id_Manifestazione = :idManifestazione 
            WHERE Id_Area = :idArea";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idArea', $idArea, PDO::PARAM_INT);
    $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
    $stmt->bindParam(':descrizione', $descrizione, PDO::PARAM_STR);
    $stmt->bindParam(':capienzaMassima', $capienzaMassima, PDO::PARAM_INT);
    $stmt->bindParam(':idManifestazione', $idManifestazione, PDO::PARAM_INT);
    return $stmt->execute();
}