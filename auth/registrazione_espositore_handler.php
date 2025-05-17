<?php
// Disable error display in production
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Start output buffering
ob_start();

include_once '../config.php';
include_once '../session.php';
include_once '../queries.php';

// Clear any previous output
ob_clean();

// Set JSON header
header('Content-Type: application/json');

try {
    // Verifica che la richiesta sia POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Metodo non consentito');
    }

    // Recupera e sanitizza i dati dal form
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $qualification = trim($_POST['qualification'] ?? '');

    // Mappa i valori della qualifica
    $qualification_map = [
        'professional' => 'professionista del settore',
        'amateur' => 'amatore',
        'expert' => 'esperto non professionista'
    ];

    // Verifica che la qualifica sia valida
    if (!isset($qualification_map[$qualification])) {
        throw new Exception('Qualifica non valida');
    }

    // Converti la qualifica nel formato corretto per il database
    $qualification = $qualification_map[$qualification];

    // Validazione campi obbligatori
    if (empty($username) || empty($password) || empty($first_name) || empty($last_name) || empty($email) || empty($phone) || empty($qualification)) {
        throw new Exception('Tutti i campi sono obbligatori');
    }

    // Validazione email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Formato email non valido');
    }

    // Validazione username (solo lettere, numeri e underscore)
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        throw new Exception('Username non valido. Usa solo lettere, numeri e underscore');
    }

    // Validazione password (minimo 8 caratteri)
    if (strlen($password) < 8) {
        throw new Exception('La password deve essere di almeno 8 caratteri');
    }

    // Validazione telefono (solo numeri e spazi)
    if (!preg_match('/^[0-9\s]+$/', $phone)) {
        throw new Exception('Il numero di telefono può contenere solo numeri e spazi');
    }

    // Verifica se username o email esistono già
    if (usernameExists($pdo, $username)) {
        throw new Exception('Username già in uso');
    }
    if (emailExists($pdo, $email)) {
        throw new Exception('Email già registrata');
    }

    // Gestione del file CV
    if (!isset($_FILES['cv']) || $_FILES['cv']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Errore nel caricamento del CV');
    }

    $cv = $_FILES['cv'];
    
    // Verifica tipo file (solo PDF)
    if ($cv['type'] !== 'application/pdf') {
        throw new Exception('Il file deve essere in formato PDF');
    }

    // Verifica dimensione file (max 16MB)
    if ($cv['size'] > 16 * 1024 * 1024) {
        throw new Exception('Il file è troppo grande. Dimensione massima: 16MB');
    }

    // Gestione del caricamento del file
    $upload_dir = '../uploads/cv/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    $cv_filename = 'cv_' . $username . '.pdf';
    $cv_path = $upload_dir . $cv_filename;
    if (!move_uploaded_file($cv['tmp_name'], $cv_path)) {
        throw new Exception('Errore durante il salvataggio del file.');
    }

    // Hash della password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Inserimento nel database (salva il path, non il contenuto)
    if (!addEspositore($pdo, $username, $hashed_password, $first_name, $last_name, $email, $phone, $qualification, $cv_path)) {
        // Se c'è un errore nel database, elimina il file caricato
        if (file_exists($cv_path)) {
            unlink($cv_path);
        }
        throw new Exception('Errore durante la registrazione');
    }

    // Risposta di successo
    echo json_encode([
        'success' => true,
        'message' => 'Registrazione completata con successo'
    ]);

} catch (Exception $e) {
    // Risposta di errore
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// End output buffering and flush
ob_end_flush();