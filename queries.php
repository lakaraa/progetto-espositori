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
    $sql = "SELECT * FROM manifestazione ORDER BY Nome ASC"; 
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

//Messaggi
function insertMessaggio($pdo, $nome, $mail, $telefono, $messaggio) 
{
    $sql = "INSERT INTO messaggio (Nome, Email, Telefono, Messaggio, Data_Invio) VALUES (:nome, :mail, :telefono, :messaggio, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
    $stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR);
    $stmt->bindParam(':mail', $mail, PDO::PARAM_STR);
    $stmt->bindParam(':messaggio', $messaggio, PDO::PARAM_STR);

    try {
        return $stmt->execute(); 
    } catch (PDOException $e) {
        return false;
    }
}
function getMessaggi($pdo) {
    $query = "SELECT Nome, Email, Telefono, Messaggio, DATE(Data_Invio) AS Data, TIME(Data_Invio) AS Ora FROM messaggio";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    try {
        // Prima verifica solo i contributi base
        $sql = "SELECT c.* FROM Contributo c 
                INNER JOIN Esposizione e ON c.Id_Contributo = e.Id_Contributo
                WHERE e.Id_Manifestazione = :idManifestazione";
                
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':idManifestazione', $idManifestazione, PDO::PARAM_INT);
        $stmt->execute();
        
        $contributi = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Poi per ogni contributo, recupera i dettagli aggiuntivi
        foreach ($contributi as &$contributo) {
            // Info espositore
            $sql_utente = "SELECT Nome, Cognome FROM Utente WHERE Id_Utente = ?";
            $stmt = $pdo->prepare($sql_utente);
            $stmt->execute([$contributo['Id_Utente']]);
            $utente = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $contributo['NomeEspositore'] = $utente['Nome'] ?? '';
            $contributo['CognomeEspositore'] = $utente['Cognome'] ?? '';
            
            // Categorie
            $sql_categorie = "SELECT cat.Nome FROM Categoria cat
                             INNER JOIN Tipologia t ON cat.Id_Categoria = t.Id_Categoria
                             WHERE t.Id_Contributo = ?";
            $stmt = $pdo->prepare($sql_categorie);
            $stmt->execute([$contributo['Id_Contributo']]);
            $categorie = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            $contributo['Categorie'] = implode(', ', $categorie);
        }
        
        return $contributi;
    } catch (PDOException $e) {
        return [];
    }
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
function getAreeByManifestazione($pdo, $manifestazioneId) {
    $sql = "SELECT area.Id_Area, area.Nome, area.Capienza_Massima
            FROM manifestazione
            JOIN area ON manifestazione.Id_Manifestazione = area.Id_Manifestazione
            WHERE manifestazione.Id_Manifestazione = :manifestazione_id";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':manifestazione_id', $manifestazioneId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
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
function updateEspositore($pdo, $idUtente, $username, $password, $nome, $cognome, $email, $telefono, $qualifica, $curriculum = null) {
    // Verifica che l'ID utente sia valido
    if ($idUtente <= 0) {
        return false;
    }

    // Se la password è vuota e il curriculum non è modificato
    if (empty($password) && $curriculum === null) {
        $sql = "UPDATE utente 
                SET Username = :username, 
                    Nome = :nome, 
                    Cognome = :cognome, 
                    Email = :email, 
                    Telefono = :telefono, 
                    Qualifica = :qualifica
                WHERE Id_Utente = :idUtente AND Ruolo = 'Espositore'";
    } 
    // Se la password è vuota ma il curriculum è modificato
    elseif (empty($password)) {
        $sql = "UPDATE utente 
                SET Username = :username, 
                    Nome = :nome, 
                    Cognome = :cognome, 
                    Email = :email, 
                    Telefono = :telefono, 
                    Qualifica = :qualifica, 
                    Curriculum = :curriculum
                WHERE Id_Utente = :idUtente AND Ruolo = 'Espositore'";
    } 
    // Se il curriculum non è modificato ma la password è cambiata
    elseif ($curriculum === null) {
        $sql = "UPDATE utente 
                SET Username = :username, 
                    Password = :password, 
                    Nome = :nome, 
                    Cognome = :cognome, 
                    Email = :email, 
                    Telefono = :telefono, 
                    Qualifica = :qualifica
                WHERE Id_Utente = :idUtente AND Ruolo = 'Espositore'";
    } 
    // Se sia la password che il curriculum sono modificati
    else {
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

    // Se la password è stata cambiata, la hashiamo
    if (!empty($password)) {
        $passwordHashed = password_hash($password, PASSWORD_BCRYPT);
        $stmt->bindParam(':password', $passwordHashed, PDO::PARAM_STR);
    }

    // Se il curriculum è stato modificato
    if ($curriculum !== null) {
        // Sostituisce il vecchio curriculum con il nuovo
        $stmt->bindParam(':curriculum', $curriculum, PDO::PARAM_LOB);
    }

    // Eseguiamo la query e controlliamo se è andata a buon fine
    return $stmt->execute();
}
function emailExists($pdo, $email) {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM utente WHERE Email = ?");
        $stmt->execute([$email]);
        return $stmt->fetchColumn() > 0;
    } catch (PDOException $e) {
        error_log("Errore nella verifica email: " . $e->getMessage());
        return false;
    }
}

function usernameExists($pdo, $username) {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM utente WHERE Username = ?");
        $stmt->execute([$username]);
        return $stmt->fetchColumn() > 0;
    } catch (PDOException $e) {
        error_log("Errore nella verifica username: " . $e->getMessage());
        return false;
    }
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
    $stmt = $pdo->prepare("SELECT 
        Id_Utente,
        Username,
        Nome,
        Cognome,
        Email,
        Telefono,
        Qualifica,
        Curriculum
        FROM utente WHERE Id_Utente = ? AND Ruolo = 'Espositore'");
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
    try {
        // Verifica se c'è ancora spazio disponibile
        if (!checkAreaCapacity($pdo, $idTurno)) {
            return false;
        }

        $sql = "INSERT INTO prenotazione (Id_Utente, Id_Turno) 
                VALUES (:idUtente, :idTurno)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':idUtente', $idUtente, PDO::PARAM_INT);
        $stmt->bindParam(':idTurno', $idTurno, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (PDOException $e) {
        return false;
    }
}
function deletePrenotazione($pdo, $idUtente, $idTurno) {
    $sql = "DELETE FROM prenotazione 
            WHERE Id_Utente = :idUtente AND Id_Turno = :idTurno";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idUtente', $idUtente, PDO::PARAM_INT);
    $stmt->bindParam(':idTurno', $idTurno, PDO::PARAM_INT);
    $result = $stmt->execute();
    return $result;
}
function getPrenotazioneById($pdo, $idUtente, $idTurno) 
{
    $sql = "
        SELECT 
            p.Id_Utente,
            p.Id_Turno,
            u.Nome AS Nome_Visitatore,
            u.Cognome AS Cognome_Visitatore,
            u.Email,
            u.Telefono,
            m.Nome AS Nome_Manifestazione,
            m.Id_Manifestazione,
            t.Data AS Data_Turno,
            t.Ora AS Ora_Turno,
            a.Nome AS Nome_Area,
            a.Id_Area
        FROM prenotazione p
        JOIN utente u ON p.Id_Utente = u.Id_Utente
        JOIN turno t ON p.Id_Turno = t.Id_Turno
        JOIN area a ON t.Id_Area = a.Id_Area
        JOIN manifestazione m ON a.Id_Manifestazione = m.Id_Manifestazione
        WHERE p.Id_Utente = :idUtente AND p.Id_Turno = :idTurno
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idUtente', $idUtente, PDO::PARAM_INT);
    $stmt->bindParam(':idTurno', $idTurno, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function getPrenotazioni($pdo) {
    $stmt = $pdo->prepare("
    SELECT 
        p.Id_Utente,
        p.Id_Turno,
        u.Nome AS Nome_Visitatore,
        u.Cognome AS Cognome_Visitatore,
        u.Email,
        m.Nome AS Nome_Manifestazione,
        t.Data AS Data_Turno,
        t.Ora AS Ora_Turno,
        a.Nome AS Nome_Area
    FROM prenotazione p
    JOIN utente u ON p.Id_Utente = u.Id_Utente
    JOIN turno t ON p.Id_Turno = t.Id_Turno
    JOIN area a ON t.Id_Area = a.Id_Area
    JOIN manifestazione m ON a.Id_Manifestazione = m.Id_Manifestazione
    WHERE u.Ruolo = 'Visitatore';
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getTurni($pdo) {
    $stmt = $pdo->prepare("
    SELECT t.Id_Turno, t.Data, t.Ora, a.Nome AS Nome_Area, m.Nome AS Nome_Manifestazione
    FROM turno t
    JOIN area a ON t.Id_Area = a.Id_Area
    JOIN manifestazione m ON a.Id_Manifestazione = m.Id_Manifestazione
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getTurniByArea($pdo, $areaId, $manifestazioneId = null) {
    if ($manifestazioneId === null) {
        // Se viene passato solo l'Id_Area
        $sql = "SELECT t.Id_Turno, t.Data, t.Ora
                FROM turno t
                JOIN area a ON t.Id_Area = a.Id_Area
                WHERE a.Id_Area = :area_id";
        
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':area_id', $areaId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    } else {
        // Se vengono passati entrambi i parametri
        $sql = "SELECT t.Id_Turno, t.Data, t.Ora
                FROM turno t
                JOIN area a ON t.Id_Area = a.Id_Area
                JOIN manifestazione m ON a.Id_Manifestazione = m.Id_Manifestazione
                WHERE m.Id_Manifestazione = :manifestazione_id 
                AND a.Id_Area = :area_id";
        
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':manifestazione_id', $manifestazioneId, PDO::PARAM_INT);
            $stmt->bindParam(':area_id', $areaId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}
function getTurniByManifestazione($pdo, $idManifestazione) {
    $stmt = $pdo->prepare("
    SELECT t.Id_Turno, t.Data, t.Ora, a.Nome AS Nome_Area, m.Nome AS Nome_Manifestazione
    FROM turno t
    JOIN area a ON t.Id_Area = a.Id_Area
    JOIN manifestazione m ON a.Id_Manifestazione = m.Id_Manifestazione
    WHERE m.Id_Manifestazione = :idManifestazione
    ");
    $stmt->bindParam(':idManifestazione', $idManifestazione, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function addPrenotazioneByPersonale($pdo, $idUtente, $idTurno) {
    // Verifica se esiste già una prenotazione per questo visitatore e turno
    $sqlCheck = "SELECT COUNT(*) FROM prenotazione WHERE Id_Utente = :idUtente AND Id_Turno = :idTurno";
    $stmtCheck = $pdo->prepare($sqlCheck);
    $stmtCheck->bindParam(':idUtente', $idUtente, PDO::PARAM_INT);
    $stmtCheck->bindParam(':idTurno', $idTurno, PDO::PARAM_INT);
    $stmtCheck->execute();

    if ($stmtCheck->fetchColumn() > 0) {
        return false; // Prenotazione già esistente
    }

    // Verifica se c'è ancora spazio disponibile
    if (!checkAreaCapacity($pdo, $idTurno)) {
        return false;
    }

    // Inserisce la prenotazione
    $sqlInsert = "INSERT INTO prenotazione (Id_Utente, Id_Turno) VALUES (:idUtente, :idTurno)";
    $stmtInsert = $pdo->prepare($sqlInsert);
    $stmtInsert->bindParam(':idUtente', $idUtente, PDO::PARAM_INT);
    $stmtInsert->bindParam(':idTurno', $idTurno, PDO::PARAM_INT);

    if ($stmtInsert->execute()) {
        return true;
    } else {
        return false;
    }
}

function checkExistingPrenotazione($pdo, $idUtente, $newIdTurno) {
    $sql = "SELECT COUNT(*) FROM prenotazione WHERE Id_Utente = :idUtente AND Id_Turno = :newIdTurno";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idUtente', $idUtente, PDO::PARAM_INT);
    $stmt->bindParam(':newIdTurno', $newIdTurno, PDO::PARAM_INT);
    $stmt->execute();

    // Restituisce true se esiste almeno una prenotazione, altrimenti false
    return $stmt->fetchColumn() > 0;
}

function updatePrenotazione($pdo, $idUtente, $idTurno, $newIdTurno) {
    // Prima verifica se esiste già una prenotazione per questo utente e nuovo turno
    $sqlCheck = "SELECT COUNT(*) FROM prenotazione 
                    WHERE Id_Utente = :idUtente AND Id_Turno = :newIdTurno";
    $stmtCheck = $pdo->prepare($sqlCheck);
    $stmtCheck->bindParam(':idUtente', $idUtente, PDO::PARAM_INT);
    $stmtCheck->bindParam(':newIdTurno', $newIdTurno, PDO::PARAM_INT);
    $stmtCheck->execute();
    
    if ($stmtCheck->fetchColumn() > 0) {
        // Esiste già una prenotazione per questo utente e turno
        return false;
    }

    // Verifica se c'è ancora spazio disponibile nel nuovo turno
    if (!checkAreaCapacity($pdo, $newIdTurno)) {
        return false;
    }

    // Procedi con l'aggiornamento
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
    // Non serve fare l'hash qui perché lo facciamo già prima di chiamare la funzione
    $sql = "INSERT INTO utente (Username, Password, Nome, Cognome, Email, Telefono, Ruolo) 
            VALUES (:username, :password, :nome, :cognome, :email, :telefono, 'Visitatore')";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':password', $password, PDO::PARAM_STR);
    $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
    $stmt->bindParam(':cognome', $cognome, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR);
    return $stmt->execute();
}
function deleteVisitatore($pdo, $id) {
    $sql = "DELETE FROM utente WHERE Id_Utente = ? AND Ruolo = 'Visitatore'";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$id]);
}
function updateVisitatore($pdo, $id, $username, $password, $nome, $cognome, $email, $telefono) {
    $sql = "UPDATE utente SET 
            Username = ?, 
            Password = ?, 
            Nome = ?, 
            Cognome = ?, 
            Email = ?, 
            Telefono = ?
            WHERE Id_Utente = ? AND Ruolo = 'Visitatore'";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$username, $password, $nome, $cognome, $email, $telefono, $id]);
}
function getVisitatori($pdo) {
    $sql = "SELECT Id_Utente, Username, Nome, Cognome, Email, Telefono 
            FROM utente 
            WHERE Ruolo = 'Visitatore'
            ORDER BY Cognome, Nome";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getVisitatoreById($pdo, $id) {
    $sql = "SELECT * FROM utente WHERE Id_Utente = ? AND Ruolo = 'Visitatore'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Query partecipanti per mese
function getQueryPartecipantiPerMese() {
    return "
        SELECT
            MONTH(Turno.data) AS mese,
            COUNT(DISTINCT Prenotazione.Id_Utente) AS numero_partecipanti
        FROM
            Prenotazione
        JOIN
            Turno ON Prenotazione.Id_Turno = Turno.Id_Turno
        GROUP BY mese
        ORDER BY mese;
    ";
}

// Query contributi per manifestazione
function getQueryContributiPerManifestazione() {
    return "
        SELECT
            m.Nome AS nome_manifestazione,
            COUNT(c.Id_Contributo) AS numero_contributi
        FROM
            Manifestazione m
        JOIN
            Esposizione e ON m.Id_Manifestazione = e.Id_Manifestazione
        JOIN
            Contributo c ON e.Id_Contributo = c.Id_Contributo
        GROUP BY m.Id_Manifestazione
        ORDER BY m.Nome;
    ";
}

// Query espositori per manifestazione
function getQueryEspositoriPerManifestazione() {
    return "
        SELECT
            m.Nome AS nome_manifestazione,
            COUNT(DISTINCT CASE WHEN u.Ruolo = 'Espositore' THEN u.Id_Utente ELSE NULL END) AS numero_espositori
        FROM
            Manifestazione m
        LEFT JOIN
            Area a ON m.Id_Manifestazione = a.Id_Manifestazione
        LEFT JOIN
            Turno t ON a.Id_Area = t.Id_Area
        LEFT JOIN
            Prenotazione p ON t.Id_Turno = p.Id_Turno
        LEFT JOIN
            Utente u ON p.Id_Utente = u.Id_Utente
        GROUP BY m.Id_Manifestazione
        ORDER BY m.Nome;
    ";
}

// Query prenotazioni per data
function getQueryPrenotazioniPerData($pdo, $anno) {
    $sql = "
        SELECT 
            DATE_FORMAT(t.Data, '%d/%m/%Y') as Data,
            COUNT(DISTINCT p.Id_Utente) as NumeroPartecipanti
        FROM Turno t
        JOIN Prenotazione p ON t.Id_Turno = p.Id_Turno
        JOIN Utente u ON p.Id_Utente = u.Id_Utente
        WHERE YEAR(t.Data) = :anno
        AND u.Ruolo = 'Visitatore'
        GROUP BY t.Data
        ORDER BY t.Data ASC";
        
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':anno', $anno, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getEspositoriByManifestazioneTop4($pdo, $id_manifestazione) {
    $sql = "
        SELECT DISTINCT u.Id_Utente, u.Nome, u.Cognome, u.Email
        FROM Utente u
        INNER JOIN Contributo c ON u.Id_Utente = c.Id_Utente
        INNER JOIN Esposizione e ON c.Id_Contributo = e.Id_Contributo
        WHERE e.Id_Manifestazione = :Id_Manifestazione
        AND u.Ruolo = 'Espositore'
        LIMIT 4
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['Id_Manifestazione' => $id_manifestazione]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

//Dashboard visitatore
function getPrenotazioniInCorso($pdo, $userId) {
    $sql = "
        SELECT 
            m.Nome AS Manifestazione, 
            m.Data AS DataInizio, 
            m.Durata, 
            a.Nome AS Area, 
            t.Id_Turno AS Turno, 
            t.Data AS Data,
            t.Ora 
        FROM prenotazione p
        JOIN turno t ON p.Id_Turno = t.Id_Turno
        JOIN area a ON t.Id_Area = a.Id_Area
        JOIN manifestazione m ON a.Id_Manifestazione = m.Id_Manifestazione
        WHERE p.Id_Utente = :userId AND t.Data >= CURDATE()
        ORDER BY t.Data ASC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getPrenotazioniDisponibili($pdo) {
    $sql = "
        SELECT 
            m.Id_Manifestazione,
            m.Nome AS Manifestazione, 
            t.Data AS DataInizio, 
            m.Durata,
            a.Nome AS Area,
            t.Id_Turno,
            t.Ora,
            (a.Capienza_Massima - COUNT(p.Id_Utente)) AS PostiDisponibili
        FROM turno t
        JOIN area a ON t.Id_Area = a.Id_Area
        JOIN manifestazione m ON a.Id_Manifestazione = m.Id_Manifestazione
        LEFT JOIN prenotazione p ON p.Id_Turno = t.Id_Turno
        WHERE t.Data > CURDATE()
        GROUP BY t.Id_Turno, m.Id_Manifestazione, m.Nome, t.Data, m.Durata, a.Nome, t.Ora
        HAVING PostiDisponibili > 0
        ORDER BY t.Data ASC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getPrenotazioniPassate($pdo, $userId) {
    $sql = "
        SELECT 
            m.Nome AS Manifestazione, 
            m.Data AS DataInizio, 
            m.Durata, 
            a.Nome AS Area, 
            t.Id_Turno AS Turno, 
            t.Ora 
        FROM prenotazione p
        JOIN turno t ON p.Id_Turno = t.Id_Turno
        JOIN area a ON t.Id_Area = a.Id_Area
        JOIN manifestazione m ON a.Id_Manifestazione = m.Id_Manifestazione
        WHERE p.Id_Utente = :userId AND t.Data < CURDATE()
        ORDER BY t.Data DESC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// Funzione per ottenere le aree disponibili per una manifestazione
function getAreeDisponibili($pdo, $idManifestazione) {
    $sql = "SELECT a.Id_Area, a.Nome as NomeArea
            FROM area a
            WHERE a.Id_Manifestazione = ?
            ORDER BY a.Nome ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idManifestazione]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Funzione per ottenere i turni disponibili per una manifestazione
function getTurniDisponibili($pdo, $idManifestazione) {
    $sql = "SELECT t.Id_Turno, t.Nome as NomeTurno, t.Orario
            FROM turno t
            WHERE t.Id_Manifestazione = ?
            ORDER BY t.Orario ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idManifestazione]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Funzione per creare una nuova prenotazione
function createPrenotazione($pdo, $userId, $idManifestazione, $idArea, $idTurno) {
    $sql = "INSERT INTO prenotazione (Id_Utente, Id_Manifestazione, Id_Area, Id_Turno, DataPrenotazione)
            VALUES (?, ?, ?, ?, CURDATE())";
    
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$userId, $idManifestazione, $idArea, $idTurno]);
}

// Funzione per verificare se un utente ha già una prenotazione per una manifestazione
function checkPrenotazioneEsistente($pdo, $userId, $idManifestazione) {
    $sql = "SELECT COUNT(*) as count
            FROM prenotazione
            WHERE Id_Utente = ? AND Id_Manifestazione = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId, $idManifestazione]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'] > 0;
}

function checkAreaCapacity($pdo, $idTurno) {
    $sql = "
        SELECT 
            a.Capienza_Massima,
            COUNT(p.Id_Utente) AS PrenotazioniAttuali
        FROM turno t
        JOIN area a ON t.Id_Area = a.Id_Area
        LEFT JOIN prenotazione p ON p.Id_Turno = t.Id_Turno
        WHERE t.Id_Turno = :idTurno
        GROUP BY a.Capienza_Massima
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idTurno', $idTurno, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$result) {
        return false;
    }
    
    return $result['Capienza_Massima'] > $result['PrenotazioniAttuali'];
}

function getUtenti($pdo) {
    $sql = "SELECT Id_Utente, Email FROM Utente WHERE Ruolo = 'Espositore'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getUsernameById($pdo, $idUtente) {
    $sql = "SELECT Username, Nome, Cognome FROM Utente WHERE Id_Utente = :idUtente";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idUtente', $idUtente, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function addContributo($pdo, $idUtente, $immagine, $titolo, $sintesi, $accettazione, $url, $idManifestazione) {
    try {
        // Verifica che l'utente esista
        $userInfo = getUsernameById($pdo, $idUtente);
        if (!$userInfo) {
            throw new PDOException('Utente non trovato');
        }

        // Valida il valore di Accettazione
        $validAccettazione = ['Rifiutato', 'Accettato', 'In Approvazione'];
        if (!in_array($accettazione, $validAccettazione)) {
            throw new PDOException('Valore non valido per Accettazione');
        }

        // Inserisci il contributo
        $query = "INSERT INTO Contributo (Id_Utente, Immagine, Titolo, Sintesi, Accettazione, URL) 
                 VALUES (:idUtente, :immagine, :titolo, :sintesi, :accettazione, :url)";
        
        $stmt = $pdo->prepare($query);
        $result = $stmt->execute([
            'idUtente' => $idUtente,
            'immagine' => $immagine,
            'titolo' => $titolo,
            'sintesi' => $sintesi,
            'accettazione' => $accettazione,
            'url' => $url
        ]);

        if (!$result) {
            throw new PDOException('Errore durante l\'inserimento del contributo');
        }

        // Ottieni l'ID del contributo appena inserito
        $idContributo = $pdo->lastInsertId();
        if (!$idContributo) {
            throw new PDOException('Impossibile ottenere l\'ID del contributo inserito');
        }

        // Verifica che la manifestazione esista
        $queryManifestazione = "SELECT Id_Manifestazione FROM Manifestazione WHERE Id_Manifestazione = :idManifestazione";
        $stmtManifestazione = $pdo->prepare($queryManifestazione);
        $stmtManifestazione->execute(['idManifestazione' => $idManifestazione]);
        if (!$stmtManifestazione->fetch()) {
            throw new PDOException('Manifestazione non trovata');
        }

        // Inserisci l'associazione nella tabella Esposizione
        $queryEsposizione = "INSERT INTO Esposizione (Id_Manifestazione, Id_Contributo) 
                           VALUES (:idManifestazione, :idContributo)";
        
        $stmtEsposizione = $pdo->prepare($queryEsposizione);
        $resultEsposizione = $stmtEsposizione->execute([
            'idManifestazione' => $idManifestazione,
            'idContributo' => $idContributo
        ]);

        if (!$resultEsposizione) {
            throw new PDOException('Errore durante l\'associazione con la manifestazione');
        }

        return $idContributo;

    } catch (PDOException $e) {
        throw $e;
    }
}

function getCandidature($pdo) {
    $sql = "SELECT c.Id_Contributo, u.Email, c.Titolo, c.Sintesi, c.Accettazione, c.Immagine, c.URL,
                   m.Nome as Manifestazione,
                   GROUP_CONCAT(cat.Nome SEPARATOR ', ') as Categorie
            FROM Contributo c
            JOIN Utente u ON c.Id_Utente = u.Id_Utente
            LEFT JOIN Esposizione e ON c.Id_Contributo = e.Id_Contributo
            LEFT JOIN Manifestazione m ON e.Id_Manifestazione = m.Id_Manifestazione
            LEFT JOIN Tipologia t ON c.Id_Contributo = t.Id_Contributo
            LEFT JOIN Categoria cat ON t.Id_Categoria = cat.Id_Categoria
            GROUP BY c.Id_Contributo, u.Email, c.Titolo, c.Sintesi, c.Accettazione, c.Immagine, c.URL, m.Nome";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function deleteContributo($pdo, $idContributo) {
    try {
        // Prima recupera il nome dell'immagine
        $stmt = $pdo->prepare("SELECT Immagine FROM Contributo WHERE Id_Contributo = ?");
        $stmt->execute([$idContributo]);
        $contributo = $stmt->fetch(PDO::FETCH_ASSOC);

        // Inizia la transazione
        $pdo->beginTransaction();

        // Elimina le relazioni con le categorie dalla tabella Tipologia
        $stmt = $pdo->prepare("DELETE FROM Tipologia WHERE Id_Contributo = ?");
        $stmt->execute([$idContributo]);

        // Elimina le relazioni con le manifestazioni
        $stmt = $pdo->prepare("DELETE FROM Esposizione WHERE Id_Contributo = ?");
        $stmt->execute([$idContributo]);

        // Elimina il contributo
        $stmt = $pdo->prepare("DELETE FROM Contributo WHERE Id_Contributo = ?");
        $stmt->execute([$idContributo]);

        // Commit della transazione
        $pdo->commit();

        // Se c'era un'immagine associata, eliminala
        if ($contributo && !empty($contributo['Immagine'])) {
            // Usa un percorso relativo alla root del progetto
            $imagePath = $_SERVER['DOCUMENT_ROOT'] . '/progetto-espositori/uploads/img/' . $contributo['Immagine'];
            
            error_log("Tentativo di eliminazione immagine. Percorso: " . $imagePath);
            
            if (file_exists($imagePath)) {
                if (!unlink($imagePath)) {
                    error_log("Errore durante l'eliminazione del file. Errore PHP: " . error_get_last()['message']);
                } else {
                    error_log("File eliminato con successo");
                }
            } else {
                error_log("File non trovato al percorso: " . $imagePath);
            }
        }

        return true;
    } catch (PDOException $e) {
        // Rollback in caso di errore
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("Errore durante l'eliminazione del contributo: " . $e->getMessage());
        throw $e;
    }
}

function getCandidatureInApprovazione($pdo, $manifestazione) {
    $sql = "SELECT c.Id_Contributo, u.Nome AS Nome_Utente, m.Nome AS Manifestazione, c.Titolo, c.Sintesi
            FROM Contributo c
            JOIN Utente u ON c.Id_Utente = u.Id_Utente
            JOIN Esposizione e ON c.Id_Contributo = e.Id_Contributo
            JOIN Manifestazione m ON e.Id_Manifestazione = m.Id_Manifestazione
            WHERE c.Accettazione = 'In Approvazione'";
    if (!empty($manifestazione)) {
        $sql .= " AND m.Nome LIKE :manifestazione";
    }
    $stmt = $pdo->prepare($sql);
    if (!empty($manifestazione)) {
        $stmt->bindValue(':manifestazione', '%' . $manifestazione . '%', PDO::PARAM_STR);
    }
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function aggiornaStatoCandidatura($pdo, $idContributo, $stato) {
    $sql = "UPDATE Contributo SET Accettazione = :stato WHERE Id_Contributo = :idContributo";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':stato', $stato, PDO::PARAM_STR);
    $stmt->bindParam(':idContributo', $idContributo, PDO::PARAM_INT);
    return $stmt->execute();
}

function getCandidatureByUser($pdo, $userId) {
    try {
        $query = "SELECT 
            c.Titolo,
            c.Sintesi,
            c.Accettazione AS Stato,
            c.URL,
            m.Nome AS Nome_Manifestazione,
            m.Data AS Data_Manifestazione
        FROM contributo c
        LEFT JOIN esposizione e ON c.Id_Contributo = e.Id_Contributo
        LEFT JOIN manifestazione m ON e.Id_Manifestazione = m.Id_Manifestazione
        WHERE c.Id_Utente = :userId AND c.Accettazione != 'Accettato'
        ORDER BY c.Id_Contributo DESC";

        $stmt = $pdo->prepare($query);
        $stmt->execute(['userId' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

function getManifestazioniDisponibili($pdo, $userId) {
    try {
        $query = "SELECT m.*, 
                  (SELECT COUNT(*) FROM Esposizione e WHERE e.Id_Manifestazione = m.Id_Manifestazione) as ContributiAttuali,
                  (SELECT COUNT(*) FROM Contributo c 
                   JOIN Esposizione e ON c.Id_Contributo = e.Id_Contributo 
                   WHERE e.Id_Manifestazione = m.Id_Manifestazione 
                   AND c.Id_Utente = :userId) as HaContributo
                  FROM Manifestazione m 
                  WHERE m.Data >= CURDATE()
                  ORDER BY m.Data ASC";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute(['userId' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

function getContributiByUser($pdo, $userId) {
    try {
        $query = "SELECT 
            c.Titolo,
            c.Sintesi,
            c.Accettazione AS Stato,
            c.URL,
            m.Nome AS Nome_Manifestazione,
            m.Data AS Data_Manifestazione
        FROM contributo c
        LEFT JOIN esposizione e ON c.Id_Contributo = e.Id_Contributo
        LEFT JOIN manifestazione m ON e.Id_Manifestazione = m.Id_Manifestazione
        WHERE c.Id_Utente = :userId AND c.Accettazione = 'Accettato'
        ORDER BY c.Id_Contributo DESC";

        $stmt = $pdo->prepare($query);
        $stmt->execute(['userId' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

function getCategorie($pdo) {
    $query = "SELECT Id_Categoria, Nome, Descrizione FROM categoria ORDER BY Nome";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Query per le statistiche delle candidature nella dashboard espositore
function getCandidatureInApprovazioneCount($pdo, $userId) {
    $sql = "SELECT COUNT(*) FROM contributo c 
            WHERE c.Id_Utente = :userId AND c.Accettazione = 'In Approvazione'";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchColumn();
}

function getCandidatureAccettateCount($pdo, $userId) {
    $sql = "SELECT COUNT(*) FROM contributo c 
            WHERE c.Id_Utente = :userId AND c.Accettazione = 'Accettato'";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchColumn();
}

function getCandidatureRifiutateCount($pdo, $userId) {
    $sql = "SELECT COUNT(*) FROM contributo c 
            WHERE c.Id_Utente = :userId AND c.Accettazione = 'Rifiutato'";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchColumn();
}

// Query per ottenere le manifestazioni disponibili
function getManifestazioniDisponibili_dashboard_espositore($pdo, $userId) {
    $sql = "SELECT m.*, 
        (SELECT COUNT(*) FROM Esposizione e WHERE e.Id_Manifestazione = m.Id_Manifestazione) as ContributiAttuali,
        (SELECT COUNT(*) FROM Contributo c 
         JOIN Esposizione e ON c.Id_Contributo = e.Id_Contributo 
         WHERE e.Id_Manifestazione = m.Id_Manifestazione 
         AND c.Id_Utente = :userId) as HaContributo
    FROM Manifestazione m 
    WHERE m.Data >= CURDATE()
    ORDER BY m.Data ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Funzione per inserire una categoria nella tabella tipologia
function addTipologia($pdo, $idContributo, $idCategoria) {
    $sql = "INSERT INTO tipologia (Id_Contributo, Id_Categoria) VALUES (:idContributo, :idCategoria)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        'idContributo' => $idContributo,
        'idCategoria' => $idCategoria
    ]);
}
// Funzione per recuperare lo username dell'espositore
function getUsername($pdo, $idEspositore) {
    $sql = "SELECT username FROM utente WHERE Id_Utente = :idEspositore";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idEspositore', $idEspositore, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? $row['username'] : null;
}

// Funzione per aggiornare i dati dell'espositore con gestione dinamica dei campi opzionali
function updateEspositoreDettagli($pdo, $idEspositore, $nome, $cognome, $email, $telefono, $username, $qualifica, $password = null, $cvData = null) {
    try {
        // Costruisci query dinamica
        $sql = "UPDATE utente 
                SET Nome = :nome, 
                    Cognome = :cognome, 
                    Email = :email, 
                    Telefono = :telefono, 
                    Username = :username, 
                    Qualifica = :qualifica";

        if (!empty($password)) {
            $sql .= ", Password = :password";
        }

        if (!empty($cvData)) {
            $sql .= ", Curriculum = :cv";
        }

        $sql .= " WHERE Id_Utente = :idEspositore AND Ruolo = 'Espositore'";

        $stmt = $pdo->prepare($sql);
        
        // Binding dei parametri obbligatori
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':cognome', $cognome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':qualifica', $qualifica);
        $stmt->bindParam(':idEspositore', $idEspositore, PDO::PARAM_INT);

        // Binding dei parametri opzionali
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $stmt->bindParam(':password', $hashedPassword);
        }

        if (!empty($cvData)) {
            $stmt->bindParam(':cv', $cvData, PDO::PARAM_LOB);
        }

        return $stmt->execute();
    } catch (PDOException $e) {
        throw $e;
    }
}

function getCandidaturaById($pdo, $idCandidatura) {
    $sql = "SELECT c.*, u.Email 
            FROM Contributo c
            JOIN Utente u ON c.Id_Utente = u.Id_Utente
            WHERE c.Id_Contributo = :idCandidatura";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idCandidatura', $idCandidatura, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateCandidaturaDettagli($pdo, $idCandidatura, $titolo, $sintesi, $url, $accettazione, $immagine = null) {
    try {
        // Costruisci la query base
        $sql = "UPDATE Contributo 
                SET Titolo = :titolo, 
                    Sintesi = :sintesi, 
                    URL = :url, 
                    Accettazione = :accettazione";
        
        // Aggiungi l'immagine alla query se presente
        if ($immagine !== null) {
            $sql .= ", Immagine = :immagine";
        }
        
        $sql .= " WHERE Id_Contributo = :idCandidatura";
        
        $stmt = $pdo->prepare($sql);
        
        // Binding dei parametri base
        $stmt->bindParam(':idCandidatura', $idCandidatura, PDO::PARAM_INT);
        $stmt->bindParam(':titolo', $titolo, PDO::PARAM_STR);
        $stmt->bindParam(':sintesi', $sintesi, PDO::PARAM_STR);
        $stmt->bindParam(':url', $url, PDO::PARAM_STR);
        $stmt->bindParam(':accettazione', $accettazione, PDO::PARAM_STR);
        
        // Binding dell'immagine se presente
        if ($immagine !== null) {
            $stmt->bindParam(':immagine', $immagine, PDO::PARAM_STR);
        }
        
        return $stmt->execute();
    } catch (PDOException $e) {
        throw $e;
    }
}

function getManifestazioneNome($pdo, $idManifestazione) {
    $sql = "SELECT Nome FROM Manifestazione WHERE Id_Manifestazione = :idManifestazione";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idManifestazione', $idManifestazione, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['Nome'] : null;
}

function addCandidaturaCompleta($pdo, $idUtente, $immagine, $titolo, $sintesi, $accettazione, $url, $idManifestazione, $categorieSelezionate) {
    try {
        $pdo->beginTransaction();

        // Inserisci il contributo
        $idContributo = addContributo($pdo, $idUtente, $immagine, $titolo, $sintesi, $accettazione, $url, $idManifestazione);
        
        if (!$idContributo) {
            throw new Exception('Errore durante l\'aggiunta della candidatura');
        }

        // Inserisci le categorie selezionate
        foreach ($categorieSelezionate as $idCategoria) {
            if (!addTipologia($pdo, $idContributo, $idCategoria)) {
                throw new Exception('Errore durante l\'inserimento della categoria');
            }
        }
        
        $pdo->commit();
        return $idContributo;
        
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}

function getCandidaturaCompleta($pdo, $idCandidatura) {
    $sql = "SELECT c.*, u.Email, m.Id_Manifestazione, m.Nome as Nome_Manifestazione, 
            GROUP_CONCAT(t.Id_Categoria) as Categorie, c.Immagine
            FROM Contributo c
            JOIN Utente u ON c.Id_Utente = u.Id_Utente
            JOIN Esposizione e ON c.Id_Contributo = e.Id_Contributo
            JOIN Manifestazione m ON e.Id_Manifestazione = m.Id_Manifestazione
            LEFT JOIN Tipologia t ON c.Id_Contributo = t.Id_Contributo
            WHERE c.Id_Contributo = :idCandidatura
            GROUP BY c.Id_Contributo, c.Immagine, c.Titolo, c.Sintesi, c.URL, c.Accettazione, m.Id_Manifestazione, u.Email, m.Nome, t.Id_Categoria";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idCandidatura', $idCandidatura, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        $result['Categorie'] = $result['Categorie'] ? explode(',', $result['Categorie']) : [];
    }
    
    return $result;
}

function updateCandidaturaCompleta($pdo, $idCandidatura, $titolo, $sintesi, $url, $accettazione, $idManifestazione, $categorieSelezionate, $immagine = null) {
    try {
        $pdo->beginTransaction();

        // Aggiorna i dettagli base della candidatura
        $result = updateCandidaturaDettagli($pdo, $idCandidatura, $titolo, $sintesi, $url, $accettazione, $immagine);
        if (!$result) {
            throw new Exception('Errore nell\'aggiornamento dei dettagli della candidatura');
        }

        // Aggiorna la manifestazione
        $sqlManifestazione = "UPDATE Esposizione 
                             SET Id_Manifestazione = :idManifestazione 
                             WHERE Id_Contributo = :idCandidatura";
        $stmtManifestazione = $pdo->prepare($sqlManifestazione);
        $resultManifestazione = $stmtManifestazione->execute([
            'idManifestazione' => $idManifestazione,
            'idCandidatura' => $idCandidatura
        ]);
        
        if (!$resultManifestazione) {
            throw new Exception('Errore nell\'aggiornamento della manifestazione');
        }

        // Aggiorna le categorie
        // Prima elimina tutte le categorie esistenti
        $sqlDeleteCategorie = "DELETE FROM Tipologia WHERE Id_Contributo = :idCandidatura";
        $stmtDeleteCategorie = $pdo->prepare($sqlDeleteCategorie);
        $stmtDeleteCategorie->execute(['idCandidatura' => $idCandidatura]);

        // Poi inserisci le nuove categorie
        foreach ($categorieSelezionate as $idCategoria) {
            if (!addTipologia($pdo, $idCandidatura, $idCategoria)) {
                throw new Exception('Errore nell\'aggiornamento delle categorie');
            }
        }

        $pdo->commit();
        return true;

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}

function getCandidaturaCategorie($pdo, $idCandidatura) {
    $sql = "SELECT c.Id_Categoria, c.Nome, c.Descrizione
            FROM Categoria c
            JOIN Tipologia t ON c.Id_Categoria = t.Id_Categoria
            WHERE t.Id_Contributo = :idCandidatura";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idCandidatura', $idCandidatura, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

//gestione categoria
function addCategoria($pdo, $nome, $descrizione)
{
    try {
        $sql = "INSERT INTO categoria (Nome, Descrizione) VALUES (:nome, :descrizione)";
        $stmt = $pdo->prepare($sql);
        
        $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindParam(':descrizione', $descrizione, PDO::PARAM_STR);
        
        return $stmt->execute();
    } catch (PDOException $e) {
        // Log dell'errore (opzionale)
        error_log("Errore durante l'aggiunta della categoria: " . $e->getMessage());
        return false;
    }
}

function getCategoriaById($pdo, $id) 
{
    try {
        $sql = "SELECT Id_Categoria, Nome, Descrizione FROM categoria WHERE Id_Categoria = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Errore durante il recupero della categoria: " . $e->getMessage());
        return false;
    }
}
function updateCategoria($pdo, $id, $nome, $descrizione)
{
    try {
        $sql = "UPDATE categoria SET Nome = :nome, Descrizione = :descrizione WHERE Id_Categoria = :id";
        $stmt = $pdo->prepare($sql);
        
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindParam(':descrizione', $descrizione, PDO::PARAM_STR);
        
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Errore durante l'aggiornamento della categoria: " . $e->getMessage());
        return false;
    }
}

function deleteCategoria($pdo, $id)
{
    try {
        $sql = "DELETE FROM categoria WHERE Id_Categoria = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Errore durante la cancellazione della categoria: " . $e->getMessage());
        return false;
    }
}

function getContributiTotaliCount($pdo, $userId) {
    $sql = "SELECT COUNT(*) as total FROM contributo WHERE Id_Utente = :userId";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'] ?? 0;
}

/**
 * Recupera l'elenco degli espositori ordinato per cognome e nome
 */
function getElencoEspositori($pdo) {
    $query = "SELECT u.Id_Utente as id, 
                     u.Nome as nome, 
                     u.Cognome as cognome, 
                     u.Email as email, 
                     u.Telefono as telefono,
                     u.Qualifica as qualifica,
                     GROUP_CONCAT(DISTINCT CONCAT(a.Nome, ' (', m.Nome, ')') SEPARATOR ', ') as aree_assegnate,
                     COUNT(DISTINCT c.Id_Contributo) as num_contributi
              FROM utente u
              LEFT JOIN contributo c ON u.Id_Utente = c.Id_Utente
              LEFT JOIN esposizione e ON c.Id_Contributo = e.Id_Contributo
              LEFT JOIN manifestazione m ON e.Id_Manifestazione = m.Id_Manifestazione
              LEFT JOIN area a ON m.Id_Manifestazione = a.Id_Manifestazione
              WHERE u.Ruolo = 'Espositore'
              GROUP BY u.Id_Utente, u.Nome, u.Cognome, u.Email, u.Telefono, u.Qualifica
              ORDER BY u.Cognome ASC, u.Nome ASC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Recupera le esposizioni per una specifica categoria
 */
function getEsposizioniByCategoria($pdo, $categoria_id) {
    if (!$categoria_id) {
        return [];
    }
    
    $sql = "SELECT c.Id_Contributo as id, c.Titolo as titolo, c.Immagine as immagine, 
                   u.Nome as nome, u.Cognome as cognome, 
                   GROUP_CONCAT(DISTINCT CONCAT(a.Nome, ' (', m.Nome, ')') SEPARATOR ', ') as area
            FROM contributo c
            JOIN utente u ON c.Id_Utente = u.Id_Utente
            JOIN tipologia t ON c.Id_Contributo = t.Id_Contributo
            LEFT JOIN esposizione e ON c.Id_Contributo = e.Id_Contributo
            LEFT JOIN manifestazione m ON e.Id_Manifestazione = m.Id_Manifestazione
            LEFT JOIN area a ON m.Id_Manifestazione = a.Id_Manifestazione
            WHERE t.Id_Categoria = :categoria_id
            GROUP BY c.Id_Contributo, c.Titolo, c.Immagine, u.Nome, u.Cognome
            ORDER BY c.Titolo ASC";
            
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':categoria_id', $categoria_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Recupera tutte le categorie per le esposizioni
 */
function getCategorieEsposizioni($pdo) {
    $sql = "SELECT Id_Categoria as id, Nome as nome 
            FROM categoria 
            ORDER BY Nome ASC";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Recupera la classifica delle categorie per numero di espositori
 */
function getClassificaCategorie($pdo) {
    $sql = "SELECT c.Id_Categoria as id, c.Nome as nome, 
                   COUNT(DISTINCT t.Id_Contributo) as numero_espositori,
                   (COUNT(DISTINCT t.Id_Contributo) * 100.0 / 
                    (SELECT COUNT(DISTINCT Id_Contributo) FROM tipologia)) as percentuale
            FROM categoria c
            LEFT JOIN tipologia t ON c.Id_Categoria = t.Id_Categoria
            GROUP BY c.Id_Categoria, c.Nome
            ORDER BY numero_espositori DESC";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getEspositoreDettagliato($pdo, $id) {
    $sql = "SELECT 
        u.Id_Utente,
        u.Username,
        u.Nome,
        u.Cognome,
        u.Email,
        u.Telefono,
        u.Qualifica,
        u.Curriculum,
        GROUP_CONCAT(DISTINCT c.Titolo) as Contributi,
        GROUP_CONCAT(DISTINCT cat.Nome) as Categorie
    FROM utente u
    LEFT JOIN contributo c ON u.Id_Utente = c.Id_Utente
    LEFT JOIN tipologia t ON c.Id_Contributo = t.Id_Contributo
    LEFT JOIN categoria cat ON t.Id_Categoria = cat.Id_Categoria
    WHERE u.Id_Utente = ? AND u.Ruolo = 'Espositore'
    GROUP BY u.Id_Utente, u.Username, u.Nome, u.Cognome, u.Email, u.Telefono, u.Qualifica, u.Curriculum";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getEspositoreContributi($pdo, $idEspositore) {
    $sql = "SELECT 
        c.Titolo as Contributo,
        GROUP_CONCAT(DISTINCT cat.Nome) as Categorie
    FROM contributo c
    LEFT JOIN tipologia t ON c.Id_Contributo = t.Id_Contributo
    LEFT JOIN categoria cat ON t.Id_Categoria = cat.Id_Categoria
    WHERE c.Id_Utente = :idEspositore
    GROUP BY c.Id_Contributo, c.Titolo";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idEspositore', $idEspositore, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getUserInfo($pdo, $idCandidatura) {
    try {
        $stmt = $pdo->prepare("
            SELECT u.Nome, u.Cognome 
            FROM Utente u 
            INNER JOIN Contributo c ON u.Id_Utente = c.Id_Utente 
            WHERE c.Id_Contributo = :idCandidatura
        ");
        $stmt->execute(['idCandidatura' => $idCandidatura]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Errore nel recupero delle informazioni utente: " . $e->getMessage());
        return false;
    }
}

function getElencoEspositoriAlfabetico($pdo) {
    $query = "SELECT 
        u.Cognome,
        u.Nome,
        GROUP_CONCAT(DISTINCT CONCAT(a.Nome, ' (', m.Nome, ')') SEPARATOR ', ') as Area
    FROM utente u
    LEFT JOIN contributo c ON u.Id_Utente = c.Id_Utente
    LEFT JOIN esposizione e ON c.Id_Contributo = e.Id_Contributo
    LEFT JOIN manifestazione m ON e.Id_Manifestazione = m.Id_Manifestazione
    LEFT JOIN area a ON m.Id_Manifestazione = a.Id_Manifestazione
    WHERE u.Ruolo = 'Espositore'
    GROUP BY u.Id_Utente, u.Cognome, u.Nome
    ORDER BY u.Cognome ASC, u.Nome ASC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getEspositoriByManifestazioneTop4_2($pdo, $idManifestazione) {
    $sql = "SELECT DISTINCT u.Id_Utente, u.Nome, u.Cognome 
            FROM utente u 
            INNER JOIN contributo c ON u.Id_Utente = c.Id_Utente 
            INNER JOIN esposizione e ON c.Id_Contributo = e.Id_Contributo 
            WHERE e.Id_Manifestazione = :idManifestazione 
            AND c.Accettazione = 'Accettato'
            LIMIT 4";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idManifestazione', $idManifestazione, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
