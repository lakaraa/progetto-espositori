<?php
//i primi 6 manifestazioni per la pagina home
function getManifestazioniTop6($pdo) 
{
    $sql = "SELECT * FROM manifestazione LIMIT 6";  
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
//tutte le manifestazioni per la pagina manifestazioni
function getManifestazioni($pdo) 
{
    $sql = "SELECT * FROM manifestazione"; 
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
//mandare un messaggio
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
//tutti i contributi per la pagina contributi
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
//login
function getUserByEmail($pdo, $email) 
{
    $sql = "SELECT * FROM utente WHERE Email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
//Gestione Area
function addArea($pdo, $nome, $descrizione, $capienzaMassima, $idManifestazione) 
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
function deleteArea($pdo, $idArea) 
{
    $sql = "DELETE FROM area WHERE Id_Area = :idArea";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idArea', $idArea, PDO::PARAM_INT);
    return $stmt->execute();
}
function updateArea($pdo, $idArea, $nome, $descrizione, $capienzaMassima, $idManifestazione) 
{
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
function getAreaById($pdo, $idArea) 
{
    $sql = "SELECT * FROM area WHERE Id_Area = :idArea";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idArea', $idArea, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function getAree($pdo) 
{
    $sql = "SELECT * FROM area";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
//Gestione Candidatura
function addCandidatura($pdo, $idUtente, $immagine, $titolo, $sintesi) 
{
    $sql = "INSERT INTO contributo (Id_Utente, Immagine, Titolo, Sintesi, Accettazione) 
            VALUES (:idUtente, :immagine, :titolo, :sintesi, 0)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idUtente', $idUtente, PDO::PARAM_INT);
    $stmt->bindParam(':immagine', $immagine, PDO::PARAM_LOB);
    $stmt->bindParam(':titolo', $titolo, PDO::PARAM_STR);
    $stmt->bindParam(':sintesi', $sintesi, PDO::PARAM_STR);
    return $stmt->execute();
}
function updateCandidatura($pdo, $idContributo, $immagine, $titolo, $sintesi, $accettazione) 
{
    $sql = "UPDATE contributo 
            SET Immagine = :immagine, Titolo = :titolo, Sintesi = :sintesi, Accettazione = :accettazione 
            WHERE Id_Contributo = :idContributo";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idContributo', $idContributo, PDO::PARAM_INT);
    $stmt->bindParam(':immagine', $immagine, PDO::PARAM_LOB);
    $stmt->bindParam(':titolo', $titolo, PDO::PARAM_STR);
    $stmt->bindParam(':sintesi', $sintesi, PDO::PARAM_STR);
    $stmt->bindParam(':accettazione', $accettazione, PDO::PARAM_BOOL);
    return $stmt->execute();
}
function deleteCandidatura($pdo, $idContributo) 
{
    $sql = "DELETE FROM contributo WHERE Id_Contributo = :idContributo";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idContributo', $idContributo, PDO::PARAM_INT);
    return $stmt->execute();
}
//Gestione Espositore
function addEspositore($pdo, $username, $password, $nome, $cognome, $email, $telefono, $qualifica, $curriculum) 
{
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT); // Hash della password
    $sql = "INSERT INTO utente (Username, Password, Nome, Cognome, Email, Telefono, Ruolo, Qualifica, Curriculum) 
            VALUES (:username, :password, :nome, :cognome, :email, :telefono, 'Espositore', :qualifica, :curriculum)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
    $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
    $stmt->bindParam(':cognome', $cognome, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR);
    $stmt->bindParam(':qualifica', $qualifica, PDO::PARAM_STR);
    $stmt->bindParam(':curriculum', $curriculum, PDO::PARAM_LOB);
    return $stmt->execute();
}
function deleteEspositore($pdo, $idUtente) 
{
    $sql = "DELETE FROM utente WHERE Id_Utente = :idUtente AND Ruolo = 'Espositore'";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idUtente', $idUtente, PDO::PARAM_INT);
    return $stmt->execute();
}
function updateEspositore($pdo, $idUtente, $username, $password, $nome, $cognome, $email, $telefono, $qualifica, $curriculum) 
{
    $sql = "UPDATE utente 
            SET Username = :username, 
                Password = :password, 
                Nome = :nome, 
                Cognome = :cognome, 
                Email = :email, 
                Telefono = :telefono, 
                Qualifica = :qualifica, 
                Curriculum = :curriculum 
            WHERE Id_Utente = :idUtente AND Ruolo = 'Espositore'";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idUtente', $idUtente, PDO::PARAM_INT);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':password', $password ? password_hash($password, PASSWORD_BCRYPT) : null, PDO::PARAM_STR);
    $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
    $stmt->bindParam(':cognome', $cognome, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR);
    $stmt->bindParam(':qualifica', $qualifica, PDO::PARAM_STR);
    $stmt->bindParam(':curriculum', $curriculum, PDO::PARAM_LOB);
    return $stmt->execute();
}
//Gestione Manifestazione
function addManifestazione($pdo, $nome, $descrizione, $luogo, $durata, $data) 
{
    $sql = "INSERT INTO manifestazione (Nome, Descrizione, Luogo, Durata, Data) 
            VALUES (:nome, :descrizione, :luogo, :durata, :data)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
    $stmt->bindParam(':descrizione', $descrizione, PDO::PARAM_STR);
    $stmt->bindParam(':luogo', $luogo, PDO::PARAM_STR);
    $stmt->bindParam(':durata', $durata, PDO::PARAM_INT);
    $stmt->bindParam(':data', $data, PDO::PARAM_STR);
    return $stmt->execute();
}
function deleteManifestazione($pdo, $idManifestazione) 
{
    $sql = "DELETE FROM manifestazione WHERE Id_Manifestazione = :idManifestazione";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idManifestazione', $idManifestazione, PDO::PARAM_INT);
    return $stmt->execute();
}
function updateManifestazione($pdo, $idManifestazione, $nome, $descrizione, $luogo, $durata, $data) 
{
    $sql = "UPDATE manifestazione 
            SET Nome = :nome, Descrizione = :descrizione, Luogo = :luogo, Durata = :durata, Data = :data 
            WHERE Id_Manifestazione = :idManifestazione";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idManifestazione', $idManifestazione, PDO::PARAM_INT);
    $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
    $stmt->bindParam(':descrizione', $descrizione, PDO::PARAM_STR);
    $stmt->bindParam(':luogo', $luogo, PDO::PARAM_STR);
    $stmt->bindParam(':durata', $durata, PDO::PARAM_INT);
    $stmt->bindParam(':data', $data, PDO::PARAM_STR);
    return $stmt->execute();
}
//Gestione Personale
function addPersonale($pdo, $username, $password, $nome, $cognome, $email, $telefono) 
{
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $sql = "INSERT INTO utente (Username, Password, Nome, Cognome, Email, Telefono, Ruolo) 
            VALUES (:username, :password, :nome, :cognome, :email, :telefono, 'Personale')";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
    $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
    $stmt->bindParam(':cognome', $cognome, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR);
    return $stmt->execute();
}
function deletePersonale($pdo, $idUtente) {
    $sql = "DELETE FROM utente WHERE Id_Utente = :idUtente AND Ruolo = 'Personale'";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idUtente', $idUtente, PDO::PARAM_INT);
    return $stmt->execute();
}
function updatePersonale($pdo, $idUtente, $username, $password, $nome, $cognome, $email, $telefono) 
{
    $sql = "UPDATE utente 
            SET Username = :username, 
                Password = :password, 
                Nome = :nome, 
                Cognome = :cognome, 
                Email = :email, 
                Telefono = :telefono 
            WHERE Id_Utente = :idUtente AND Ruolo = 'Personale'";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idUtente', $idUtente, PDO::PARAM_INT);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':password', $password ? password_hash($password, PASSWORD_BCRYPT) : null, PDO::PARAM_STR);
    $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
    $stmt->bindParam(':cognome', $cognome, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR);
    return $stmt->execute();
}
//Gestione Prenoatazione
function addPrenotazione($pdo, $idUtente, $idTurno) 
{
    $sql = "INSERT INTO prenotazione (Id_Utente, Id_Turno) 
            VALUES (:idUtente, :idTurno)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idUtente', $idUtente, PDO::PARAM_INT);
    $stmt->bindParam(':idTurno', $idTurno, PDO::PARAM_INT);
    return $stmt->execute();
}
function deletePrenotazione($pdo, $idUtente, $idTurno) 
{
    $sql = "DELETE FROM prenotazione 
            WHERE Id_Utente = :idUtente AND Id_Turno = :idTurno";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idUtente', $idUtente, PDO::PARAM_INT);
    $stmt->bindParam(':idTurno', $idTurno, PDO::PARAM_INT);
    return $stmt->execute();
}
function updatePrenotazione($pdo, $idUtente, $idTurno, $newIdTurno) 
{
    $sql = "UPDATE prenotazione 
            SET Id_Turno = :newIdTurno 
            WHERE Id_Utente = :idUtente AND Id_Turno = :idTurno";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idUtente', $idUtente, PDO::PARAM_INT);
    $stmt->bindParam(':idTurno', $idTurno, PDO::PARAM_INT);
    $stmt->bindParam(':newIdTurno', $newIdTurno, PDO::PARAM_INT);
    return $stmt->execute();
}
//Gestione visitore
function addVisitatore($pdo, $username, $password, $nome, $cognome, $email, $telefono) 
{
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT); 
    $sql = "INSERT INTO utente (Username, Password, Nome, Cognome, Email, Telefono, Ruolo) 
            VALUES (:username, :password, :nome, :cognome, :email, :telefono, 'Visitatore')";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
    $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
    $stmt->bindParam(':cognome', $cognome, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR);
    return $stmt->execute();
}
function deleteVisitatore($pdo, $idUtente) 
{
    $sql = "DELETE FROM utente WHERE Id_Utente = :idUtente AND Ruolo = 'Visitatore'";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idUtente', $idUtente, PDO::PARAM_INT);
    return $stmt->execute();
}
function updateVisitatore($pdo, $idUtente, $username, $password, $nome, $cognome, $email, $telefono) 
{
    $sql = "UPDATE utente 
            SET Username = :username, 
                Password = :password, 
                Nome = :nome, 
                Cognome = :cognome, 
                Email = :email, 
                Telefono = :telefono 
            WHERE Id_Utente = :idUtente AND Ruolo = 'Visitatore'";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idUtente', $idUtente, PDO::PARAM_INT);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':password', $password ? password_hash($password, PASSWORD_BCRYPT) : null, PDO::PARAM_STR);
    $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
    $stmt->bindParam(':cognome', $cognome, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR);
    return $stmt->execute();
}