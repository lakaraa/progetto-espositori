<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once '../config.php';
include_once '../session.php';
include_once '../queries.php';

header('Content-Type: application/json');
ob_clean();

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

    // Validazione campi obbligatori
    if (empty($username) || empty($password) || empty($first_name) || empty($last_name) || empty($email) || empty($phone)) {
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

    // Hash della password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Inserimento nel database
    if (!addVisitatore($pdo, $username, $hashed_password, $first_name, $last_name, $email, $phone)) {
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