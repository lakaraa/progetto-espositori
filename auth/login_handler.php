<?php
include_once('../config.php');
include_once('../session.php');
include_once('../queries.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $_SESSION['error'] = 'Inserisci email e password';
        header('Location: ../pages/login.php');
        exit;
    }

    $user = getUserByEmail($pdo, $email);

    if (!$user) {
        $_SESSION['error'] = 'Nessun utente trovato con questa email';
        header('Location: ../pages/login.php');
        exit;
    }

    if (password_verify($password, $user['Password']) || $password === $user['Password']) {
        // Salvo i dati in sessione per usarli nella navbar
        $_SESSION['id_utente'] = $user['Id_Utente'];
        $_SESSION['email'] = $user['Email'];
        $_SESSION['ruolo'] = $user['Ruolo'];
        $_SESSION['nome'] = $user['Nome'];
        $_SESSION['cognome'] = $user['Cognome'];

        // Reindirizzamento alla dashboard in base al ruolo
        switch ($user['Ruolo']) {
            case 'Personale':
                header('Location: ../Personale/dashboard_personale.php');
                break;
            case 'Espositore':
                header('Location: ../Espositore/dashboard_espositore.php');
                break;
            case 'Visitatore':
                header('Location: ../Visitatore/dashboard_visitatore.php');
                break;
            default:
                header('Location: ../index.php');
        }
        exit;
    } else {
        $_SESSION['error'] = 'Password errata';
        header('Location: ../pages/login.php');
        exit;
    }
} else {
    $_SESSION['error'] = 'Metodo di richiesta non valido';
    header('Location: ../pages/login.php');
    exit;
}
?>
