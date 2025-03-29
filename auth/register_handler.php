<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once '../config.php';
include_once '../session.php';
include_once '../queries.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $phone = trim($_POST['phone']);
    $qualification = trim($_POST['qualification']);
    $cv = $_FILES['cv'];

    // Validazione dei campi
    if (empty($username) || empty($password) || empty($first_name) || empty($last_name) || empty($email) || empty($phone) || empty($qualification)) {
        $_SESSION['error'] = 'Tutti i campi sono obbligatori.';
        header('Location: ../pages/registration.php');
        exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Inserisci un indirizzo email valido.';
        header('Location: ../pages/registration.php');
        exit;
    }
    if (!isset($_FILES['cv']) || $_FILES['cv']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['error'] = 'Errore durante il caricamento del file.';
        header('Location: ../pages/registration.php');
        exit;
    }
    if ($cv['type'] !== 'application/pdf') {
        $_SESSION['error'] = 'Il curriculum deve essere un file PDF.';
        header('Location: ../pages/registration.php');
        exit;
    }

    // Controlla la dimensione del file (16 MB massimo)
    if ($cv['size'] > 16 * 1024 * 1024) { // 16 MB
        $_SESSION['error'] = 'Il file è troppo grande. La dimensione massima consentita è 16 MB.';
        header('Location: ../pages/registration.php');
        exit;
    }

    // Mappa i valori ENUM
    $qualification_map = [
        'professional' => 'professionista del settore',
        'amateur' => 'amatore',
        'expert' => 'esperto non professionista'
    ];
    $qualification = $qualification_map[$qualification] ?? null;

    if (!$qualification) {
        $_SESSION['error'] = 'Qualifica non valida.';
        header('Location: ../pages/registration.php');
        exit;
    }

    // Crea la directory uploads se non esiste
    $upload_dir = '../uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Salva il file con un nome personalizzato
    $cv_filename = 'cv_' . $username . '.pdf';
    $cv_path = $upload_dir . $cv_filename;

    if (!move_uploaded_file($cv['tmp_name'], $cv_path)) {
        $_SESSION['error'] = 'Errore durante il salvataggio del file.';
        header('Location: ../pages/registration.php');
        exit;
    }

    // Hash della password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Inserimento nel database tramite la funzione
    if (addEspositore($pdo, $username, $hashed_password, $first_name, $last_name, $email, $phone, $qualification, $cv_path)) {
        $_SESSION['success'] = 'Registrazione completata con successo!';
        header('Location: ../pages/login.php');
        exit;
    } else {
        $_SESSION['error'] = 'Errore durante la registrazione. Riprova.';
        header('Location: ../pages/registration.php');
        exit;
    }
}