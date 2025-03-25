<?php
include_once __DIR__ . '/config.php';
include_once __DIR__ . '/session.php';
include_once __DIR__ . '/queries.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $first_name = trim($_POST['nome']);
    $last_name = trim($_POST['cognome']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $phone = trim($_POST['telefono']);
    $qualification = trim($_POST['qualifica']);
    $cv = $_FILES['curriculum'];

    // Validazione dei campi
    if (empty($username) || empty($password) || empty($first_name) || empty($last_name) || empty($email) || empty($phone) || empty($qualification) || empty($cv)) {
        header('Location: ../registration.php?message=Tutti i campi sono obbligatori.');
        exit;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: ../registration.php?message=Inserisci un indirizzo email valido.');
        exit;
    } elseif ($cv['type'] !== 'application/pdf') {
        header('Location: ../registration.php?message=Il curriculum deve essere un file PDF.');
        exit;
    }

    // Salvataggio del file CV
    $cv_path = __DIR__ . '/../uploads/' . basename($cv['name']);
    if (!move_uploaded_file($cv['tmp_name'], $cv_path)) {
        header('Location: ../registration.php?message=Errore durante il caricamento del file.');
        exit;
    }

    // Hash della password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Inserimento nel database tramite la funzione
    if (addEspositore($pdo, $username, $hashed_password, $first_name, $last_name, $email, $phone, $qualification, $cv_path)) {
        header('Location: ../registration.php?message=Registrazione completata con successo!');
    } else {
        header('Location: ../registration.php?message=Errore durante la registrazione. Riprova.');
    }
    exit;
}