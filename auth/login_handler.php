<?php
include_once __DIR__ . '/config.php';
include_once __DIR__. '/session.php';
include_once __DIR__ . '/queries.php';

//verifica se il form è stato inviato
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $email = $_POST['email'];
    $password = $_POST['password'];

    if(empty($email) || empty($password))
        $error = 'Inserisci email e password';

    $user = getUserByEmail($email);
    if ($user)
    {
        if (password_verify($password, $user['Password']) || $password === $user['Password'])
        {
            $_SESSION['user_id'] = $user['Id_Utente'];
            $_SESSION['user_username'] = $user['Username'];
            $_SESSION['user_email'] = $user['Email'];
            $_SESSION['user_name'] = $user['Nome'];
            $_SESSION['user_surname'] = $user['Cognome'];
            $_SESSION['user_role'] = $user['Ruolo'];

            if ($user['Ruolo'] === 'Personale') 
                header('Location: ' . __DIR__ . '/dashboard_personale.php');
            else 
                header('Location: ' . __DIR__ . '/index.php');
            exit;
        }
        else
        {
            $error = 'Password errata';
        }
    }
    else
    {
        $error = 'Nessun utente trovato con quell\'email';
    }
}