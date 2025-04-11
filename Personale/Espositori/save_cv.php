<?php
// Pulizia buffer di output
while (ob_get_level()) ob_end_clean();

// Configurazione errori (solo log)
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Percorso assoluto al config
require_once __DIR__ . '/../../config.php';

// Verifica connessione al database
if (!isset($pdo)) {
    header('Content-Type: application/json');
    die(json_encode(['success' => false, 'message' => 'Errore di connessione al database']));
}

// Forza output JSON
header('Content-Type: application/json');

try {
    // Verifica metodo richiesta
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Richiesta non valida: metodo non consentito");
    }

    // Verifica che sia una richiesta AJAX
    if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
        throw new Exception("Richiesta non valida: non è una richiesta AJAX");
    }

    // Verifica parametri obbligatori
    if (!isset($_POST['id_espositore'], $_FILES['cv_file'])) {
        throw new Exception("Parametri mancanti: id_espositore o file CV");
    }

    // Validazione ID
    $idEspositore = (int)$_POST['id_espositore'];
    if ($idEspositore <= 0) {
        throw new Exception("ID espositore non valido");
    }

    // Validazione username
    $username = preg_replace('/[^a-zA-Z0-9_-]/', '', $_POST['username'] ?? '');
    if (empty($username)) {
        throw new Exception("Username non valido");
    }

    // Gestione file upload
    $file = $_FILES['cv_file'];
    $maxSize = 5 * 1024 * 1024; // 5MB

    // Verifica errori upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("Errore durante il caricamento: " . $this->getUploadError($file['error']));
    }

    // Verifica dimensione
    if ($file['size'] > $maxSize) {
        throw new Exception("Il file supera la dimensione massima di 5MB");
    }

    // Verifica tipo file
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if ($mime !== 'application/pdf') {
        throw new Exception("Sono accettati solo file PDF");
    }

    // verifica che l'utente esista
    $checkStmt = $pdo->prepare("SELECT Id_Utente FROM utente WHERE Id_Utente = :id AND Ruolo = 'Espositore'");
    $checkStmt->bindParam(':id', $idEspositore, PDO::PARAM_INT);
    $checkStmt->execute();
    
    if ($checkStmt->rowCount() === 0) {
        throw new Exception("Utente non trovato o non è un espositore");
    }

    // Prepara directory uploads
    $uploadDir = __DIR__ . '/../../uploads/';
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            throw new Exception("Impossibile creare la cartella per il salvataggio");
        }
    }

    // Percorso file
    $filename = 'cv_' . $username . '.pdf';
    $filepath = $uploadDir . $filename;

    // Elimina vecchio file se esiste
    if (file_exists($filepath) && !unlink($filepath)) {
        throw new Exception("Impossibile eliminare il vecchio curriculum");
    }

    // Sposta nuovo file
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        throw new Exception("Errore durante il salvataggio del file");
    }

    // Leggi contenuto per il database
    $fileContent = file_get_contents($filepath);
    if ($fileContent === false) {
        throw new Exception("Impossibile leggere il contenuto del file");
    }

    // Aggiorna database - usando la struttura della tua tabella
    $stmt = $pdo->prepare("UPDATE utente SET Curriculum = :curriculum WHERE Id_Utente = :id AND Ruolo = 'Espositore'");
    $stmt->bindParam(':curriculum', $fileContent, PDO::PARAM_LOB);
    $stmt->bindParam(':id', $idEspositore, PDO::PARAM_INT);
    
    if (!$stmt->execute()) {
        $errorInfo = $stmt->errorInfo();
        throw new Exception("Errore durante l'aggiornamento del database: " . ($errorInfo[2] ?? 'Errore sconosciuto'));
    }

    // Risposta di successo
    echo json_encode([
        'success' => true,
        'message' => 'Curriculum aggiornato con successo',
        'file_url' => '/progetto-espositori/uploads/' . $filename
    ]);
    exit;

} catch (Exception $e) {
    error_log("Errore in save_cv.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit;
}

function getUploadError($code) {
    $errors = [
        UPLOAD_ERR_INI_SIZE => 'Il file supera la dimensione massima consentita',
        UPLOAD_ERR_FORM_SIZE => 'Il file supera la dimensione massima specificata nel form',
        UPLOAD_ERR_PARTIAL => 'Il file è stato caricato solo parzialmente',
        UPLOAD_ERR_NO_FILE => 'Nessun file è stato caricato',
        UPLOAD_ERR_NO_TMP_DIR => 'Cartella temporanea mancante',
        UPLOAD_ERR_CANT_WRITE => 'Impossibile scrivere il file su disco',
        UPLOAD_ERR_EXTENSION => 'Upload fermato da un\'estensione PHP'
    ];
    return $errors[$code] ?? 'Errore sconosciuto durante il caricamento';
}
?>