<?php
include_once __DIR__ . '/config.php';
include_once __DIR__ . '/session.php';
include_once __DIR__ . '/queries.php';

// Verifica se il form è stato inviato
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);

    // Controllo campi vuoti
    if (empty($email) || empty($password)) {
        $error = 'Inserisci email e password';
    } else {
        // Recupera l'utente dal database
        $user = getUserByEmail($pdo, $email);

        if ($user) {
            // Verifica della password
            if (password_verify($password, $user['Password'])) {
                $_SESSION['user_id'] = $user['Id_Utente'];
                $_SESSION['user_username'] = $user['Username'];
                $_SESSION['user_email'] = $user['Email'];
                $_SESSION['user_name'] = $user['Nome'];
                $_SESSION['user_surname'] = $user['Cognome'];
                $_SESSION['user_role'] = $user['Ruolo'];

                // Redirect in base al ruolo
                if ($user['Ruolo'] === 'Personale') {
                    header('Location: /dashboard_personale.php', true, 302);
                } else {
                    header('Location: /index.php', true, 302);
                }
                exit;
            } else {
                $error = 'Password errata';
            }
        } else {
            $error = 'Nessun utente trovato con quell\'email';
        }
    }

    // Mostra l'errore 
    if (isset($error)) {
        echo "<p style='color: red;'>$error</p>";
    }
}
?>