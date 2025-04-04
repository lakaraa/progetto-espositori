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
// Recupera una manifestazione specifica in base al suo ID
function getManifestazioneById($pdo, $idManifestazione) {
    $sql = "SELECT * FROM manifestazione WHERE Id_Manifestazione = :idManifestazione";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idManifestazione', $idManifestazione, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
// Recupera tutti i contributi associati a una manifestazione specifica
function getContributiByManifestazione($pdo, $idManifestazione) 
{
    $sql = "
        SELECT c.*
        FROM contributo c
        INNER JOIN localizzazione l ON c.Id_Contributo = l.Id_Contributo
        WHERE l.Id_Manifestazione = :idManifestazione
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idManifestazione', $idManifestazione, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
//login
function getUserByEmail($pdo, $email) {
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
    try {
        // Inizia una transazione per garantire che tutte le operazioni vengano eseguite correttamente
        $pdo->beginTransaction();

        // Elimina i record nella tabella turno che fanno riferimento all'Id_Area
        $sqlTurno = "DELETE FROM turno WHERE Id_Area = :idArea";
        $stmtTurno = $pdo->prepare($sqlTurno);
        $stmtTurno->bindParam(':idArea', $idArea, PDO::PARAM_INT);
        $stmtTurno->execute();

        // Ora elimina l'area dalla tabella area
        $sqlArea = "DELETE FROM area WHERE Id_Area = :idArea";
        $stmtArea = $pdo->prepare($sqlArea);
        $stmtArea->bindParam(':idArea', $idArea, PDO::PARAM_INT);
        $stmtArea->execute();

        // Se entrambe le operazioni sono andate a buon fine, commit la transazione
        $pdo->commit();

        return true;
    } catch (Exception $e) {
        // Se c'è un errore, rollback della transazione
        $pdo->rollBack();
        // Puoi loggare l'errore o gestirlo come necessario
        return false;
    }
}

function updateArea($pdo, $idArea, $nome, $descrizione, $capienzaMassima, $idManifestazione) {
    $sql = "UPDATE area 
            SET Nome = :nome, 
                Descrizione = :descrizione, 
                Capienza_Massima = :capienzaMassima, 
                Id_Manifestazione = :idManifestazione
            WHERE Id_Area = :idArea";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
    $stmt->bindParam(':descrizione', $descrizione, PDO::PARAM_STR);
    $stmt->bindParam(':capienzaMassima', $capienzaMassima, PDO::PARAM_INT);
    $stmt->bindParam(':idManifestazione', $idManifestazione, PDO::PARAM_INT);
    $stmt->bindParam(':idArea', $idArea, PDO::PARAM_INT);
    return $stmt->execute();
}
function getAreaById($pdo, $idArea) {
    $sql = "SELECT a.Id_Area AS id, 
                a.Nome AS nome, 
                a.Descrizione AS descrizione, 
                a.Capienza_Massima AS capienza_massima, 
                a.Id_Manifestazione AS id_manifestazione
            FROM area a
            WHERE a.Id_Area = :idArea";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idArea', $idArea, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function getAree($pdo) 
{
    $sql = "
        SELECT a.Id_Area AS id, 
            a.Nome AS nome, 
            m.Nome AS manifestazione, 
            a.Descrizione AS descrizione, 
            a.Capienza_Massima AS capienza_massima
        FROM area a
        INNER JOIN manifestazione m ON a.Id_Manifestazione = m.Id_Manifestazione
    ";
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
    $result = $stmt->execute();
    if ($result) 
        // Reset the auto-increment value for the table
        $pdo->exec("ALTER TABLE contributo AUTO_INCREMENT = 1");
    return $result;
}
//Gestione Espositore
function addEspositore($pdo, $username, $hashed_password, $first_name, $last_name, $email, $phone, $qualification, $cv_content) {
    $sql = "INSERT INTO utente (Username, Password, Nome, Cognome, Email, Telefono, Ruolo, Qualifica, Curriculum) 
            VALUES (:username, :password, :nome, :cognome, :email, :telefono, 'Espositore', :qualifica, :curriculum)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->bindParam(':nome', $first_name);
    $stmt->bindParam(':cognome', $last_name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':telefono', $phone);
    $stmt->bindParam(':qualifica', $qualification);
    $stmt->bindParam(':curriculum', $cv_content, PDO::PARAM_LOB);
    return $stmt->execute();
}
function deleteEspositore($pdo, $idUtente) 
{
    $sql = "DELETE FROM utente WHERE Id_Utente = :idUtente AND Ruolo = 'Espositore'";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idUtente', $idUtente, PDO::PARAM_INT);
    $result = $stmt->execute();
    return $result;
}
function updateEspositore($pdo, $idUtente, $username, $password, $nome, $cognome, $email, $telefono, $qualifica, $curriculum) {
    // Verifica che l'ID utente sia valido
    if ($idUtente <= 0) {
        return false;
    }

    // Se la password è vuota, non la modificare
    if (empty($password)) {
        $sql = "UPDATE utente 
                SET Username = :username, 
                    Nome = :nome, 
                    Cognome = :cognome, 
                    Email = :email, 
                    Telefono = :telefono, 
                    Qualifica = :qualifica, 
                    Curriculum = :curriculum
                WHERE Id_Utente = :idUtente AND Ruolo = 'Espositore'";
    } else {
        // Altrimenti, aggiorna anche la password
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
    }

    $stmt = $pdo->prepare($sql);

    // Binding dei parametri
    $stmt->bindParam(':idUtente', $idUtente, PDO::PARAM_INT);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
    $stmt->bindParam(':cognome', $cognome, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR);
    $stmt->bindParam(':qualifica', $qualifica, PDO::PARAM_STR);
    $stmt->bindParam(':curriculum', $curriculum, PDO::PARAM_LOB);

    // Se la password è stata cambiata, la hashiamo
    if (!empty($password)) {
        $passwordHashed = password_hash($password, PASSWORD_BCRYPT);
        $stmt->bindParam(':password', $passwordHashed, PDO::PARAM_STR);
    }

    // Eseguiamo la query e controlliamo se è andata a buon fine
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}
function emailExists($pdo, $email) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM utente WHERE Email = ?");
    $stmt->execute([$email]);
    return $stmt->fetchColumn() > 0;
}

function usernameExists($pdo, $username) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM utente WHERE Username = ?");
    $stmt->execute([$username]);
    return $stmt->fetchColumn() > 0;
}
function getEspositori($pdo) 
{
    $sql = "
        SELECT u.Id_Utente AS id, 
            u.Username AS username, 
            u.Password AS password, 
            u.Nome AS nome, 
            u.Cognome AS cognome, 
            u.Email AS email, 
            u.Telefono AS telefono, 
            u.Qualifica AS qualifica, 
            u.Curriculum AS curriculum
        FROM utente u
        WHERE u.Ruolo = 'Espositore'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getEspositoreById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM utente WHERE Id_Utente = ? AND Ruolo = 'Espositore'");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
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
    $result = $stmt->execute();
    return $result;
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
function getPersonale($pdo) {
    $stmt = $pdo->query("SELECT * FROM utente WHERE Ruolo = 'Personale'");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getPersonaleById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM utente WHERE Id_Utente = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

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
    $result = $stmt->execute();
    return $result;
}
function updatePersonale($pdo, $idUtente, $username, $password, $nome, $cognome, $email, $telefono) {
    // Verifica che l'ID utente sia valido
    if ($idUtente <= 0) {
        return false;
    }

    // Se la password è vuota, non la modificare
    if (empty($password)) {
        $sql = "UPDATE utente 
                SET Username = :username, 
                    Nome = :nome, 
                    Cognome = :cognome, 
                    Email = :email, 
                    Telefono = :telefono
                WHERE Id_Utente = :idUtente AND Ruolo = 'Personale'";
    } else {
        // Altrimenti, aggiorna anche la password
        $sql = "UPDATE utente 
                SET Username = :username, 
                    Password = :password, 
                    Nome = :nome, 
                    Cognome = :cognome, 
                    Email = :email, 
                    Telefono = :telefono
                WHERE Id_Utente = :idUtente AND Ruolo = 'Personale'";
    }

    $stmt = $pdo->prepare($sql);

    // Binding dei parametri
    $stmt->bindParam(':idUtente', $idUtente, PDO::PARAM_INT);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
    $stmt->bindParam(':cognome', $cognome, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR);

    // Se la password è stata cambiata, la hashiamo
    if (!empty($password)) {
        $passwordHashed = password_hash($password, PASSWORD_BCRYPT);
        $stmt->bindParam(':password', $passwordHashed, PDO::PARAM_STR);
    }

    // Eseguiamo la query e controlliamo se è andata a buon fine
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
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
    $result = $stmt->execute();
    return $result;
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
    $result = $stmt->execute();
    return $result;
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