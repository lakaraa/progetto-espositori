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
        error_log("Errore in getContributiByManifestazione: " . $e->getMessage());
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
function getAreeByManifestazione($pdo, $idManifestazione) 
{
    $sql = "
        SELECT a.Id_Area AS id, 
            a.Nome AS nome, 
            m.Nome AS manifestazione, 
            a.Descrizione AS descrizione, 
            a.Capienza_Massima AS capienza_massima
        FROM area a
        INNER JOIN manifestazione m ON a.Id_Manifestazione = m.Id_Manifestazione
        WHERE m.Id_Manifestazione = :idManifestazione
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idManifestazione', $idManifestazione, PDO::PARAM_INT);
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
function updateEspositore($pdo, $idUtente, $username, $password, $nome, $cognome, $email, $telefono, $qualifica, $curriculum = null) {
    // Verifica che l'ID utente sia valido
    if ($idUtente <= 0) {
        return false;
    }

    // Prepara la query base
    $sqlBase = "UPDATE utente 
                SET Username = :username, 
                    Nome = :nome, 
                    Cognome = :cognome, 
                    Email = :email, 
                    Telefono = :telefono, 
                    Qualifica = :qualifica";
    
    // Aggiungi password se fornita
    if (!empty($password)) {
        $sqlBase .= ", Password = :password";
        $passwordHashed = password_hash($password, PASSWORD_BCRYPT);
    }

    // Gestione del curriculum
    $cvPath = null;
    $cvBlob = null;
    
    // Se è stato fornito un nuovo CV
    if ($curriculum !== null && is_array($curriculum) && $curriculum['error'] === UPLOAD_ERR_OK) {
        // 1. Validazione del file
        $maxSize = 5 * 1024 * 1024; // 5MB
        $allowedTypes = ['application/pdf'];
        
        if ($curriculum['size'] > $maxSize) {
            throw new Exception("Il file del CV supera la dimensione massima consentita (5MB)");
        }
        
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($curriculum['tmp_name']);
        
        if (!in_array($mime, $allowedTypes)) {
            throw new Exception("Sono accettati solo file PDF per il CV");
        }
        
        // 2. Salvataggio su filesystem
        $uploadDir = 'uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $cvPath = $uploadDir . uniqid('cv_') . '.pdf';
        if (!move_uploaded_file($curriculum['tmp_name'], $cvPath)) {
            throw new Exception("Errore nel salvataggio del file CV");
        }
        
        // 3. Preparazione BLOB per database
        $cvBlob = file_get_contents($cvPath);
        
        $sqlBase .= ", Curriculum = :curriculum, CurriculumPath = :curriculumPath";
    } else {
        // Se non viene fornito un nuovo CV, mantieni quello esistente
        $sqlBase .= ", Curriculum = Curriculum"; // Mantiene il valore corrente
    }
    
    $sqlBase .= " WHERE Id_Utente = :idUtente AND Ruolo = 'Espositore'";
    
    // Prepara e esegui la query
    $stmt = $pdo->prepare($sqlBase);
    
    // Binding dei parametri
    $stmt->bindParam(':idUtente', $idUtente, PDO::PARAM_INT);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
    $stmt->bindParam(':cognome', $cognome, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR);
    $stmt->bindParam(':qualifica', $qualifica, PDO::PARAM_STR);
    
    if (!empty($password)) {
        $stmt->bindParam(':password', $passwordHashed, PDO::PARAM_STR);
    }
    
    if ($curriculum !== null && $cvBlob !== null) {
        $stmt->bindParam(':curriculum', $cvBlob, PDO::PARAM_LOB);
        $stmt->bindParam(':curriculumPath', $cvPath, PDO::PARAM_STR);
    }
    
    try {
        return $stmt->execute();
    } catch (PDOException $e) {
        // Se c'è un errore, cancella il file eventualmente salvato
        if ($cvPath !== null && file_exists($cvPath)) {
            unlink($cvPath);
        }
        error_log("Errore aggiornamento espositore: " . $e->getMessage());
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
function getTurniByArea($pdo, $idArea) {
    $stmt = $pdo->prepare("
    SELECT t.Id_Turno, t.Data, t.Ora, a.Nome AS Nome_Area, m.Nome AS Nome_Manifestazione
    FROM turno t
    JOIN area a ON t.Id_Area = a.Id_Area
    JOIN manifestazione m ON a.Id_Manifestazione = m.Id_Manifestazione
    WHERE a.Id_Area = :idArea
    ");
    $stmt->bindParam(':idArea', $idArea, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        error_log("Prenotazione già esistente per il visitatore e il turno selezionati.");
        return false; // Prenotazione già esistente
    }

    // Inserisce la prenotazione
    $sqlInsert = "INSERT INTO prenotazione (Id_Utente, Id_Turno) VALUES (:idUtente, :idTurno)";
    $stmtInsert = $pdo->prepare($sqlInsert);
    $stmtInsert->bindParam(':idUtente', $idUtente, PDO::PARAM_INT);
    $stmtInsert->bindParam(':idTurno', $idTurno, PDO::PARAM_INT);

    if ($stmtInsert->execute()) {
        error_log("Prenotazione inserita con successo.");
        return true;
    } else {
        error_log("Errore durante l'inserimento della prenotazione.");
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
function getVisitatori($pdo) 
{
    $sql = "SELECT * FROM utente WHERE Ruolo = 'Visitatore'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
function getQueryPrenotazioniPerData() {
    return "
        SELECT
            t.data AS turno_data,
            COUNT(*) AS numero_prenotazioni
        FROM
            Turno t
        JOIN
            Prenotazione p ON t.Id_Turno = p.Id_Turno
        GROUP BY t.data
        ORDER BY turno_data;
    ";
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

?>