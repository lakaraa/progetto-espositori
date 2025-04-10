<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once '../config.php';
include_once '../session.php';
include_once '../queries.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recupera e pulisce i dati del form
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $phone = trim($_POST['phone']);

    // Validazione dei campi obbligatori
    if (empty($username) || empty($password) || empty($first_name) || empty($last_name) || empty($email) || empty($phone)) {
        $_SESSION['error'] = 'Tutti i campi sono obbligatori.';
        header('Location: ../pages/registrazione_visitatore.php');
        exit;
    }

    // Validazione email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Inserisci un indirizzo email valido.';
        header('Location: ../pages/registrazione_visitatore.php');
        exit;
    }

    // Verifica se l'username esiste già
    if (usernameExists($pdo, $username)) {
        $_SESSION['error'] = 'Username già in uso. Scegline un altro.';
        header('Location: ../pages/registrazione_visitatore.php');
        exit;
    }

    // Verifica se l'email esiste già
    if (emailExists($pdo, $email)) {
        $_SESSION['error'] = 'Email già registrata. Usa un\'altra email o accedi.';
        header('Location: ../pages/registrazione_visitatore.php');
        exit;
    }

    // Hash della password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Inserimento nel database tramite la funzione specifica per visitatori
    if (addVisitatore($pdo, $username, $hashed_password, $first_name, $last_name, $email, $phone)) {
        $_SESSION['success'] = 'Registrazione completata con successo! Ora puoi accedere.';
        header('Location: ../pages/login.php');
        exit;
    } else {
        $_SESSION['error'] = 'Errore durante la registrazione. Riprova più tardi.';
        header('Location: ../pages/registrazione_visitatore.php');
        exit;
    }
} else {
    // Se qualcuno prova ad accedere direttamente a questo handler senza POST
    $_SESSION['error'] = 'Metodo di richiesta non valido.';
    header('Location: ../pages/registrazione_visitatore.php');
    exit;
}