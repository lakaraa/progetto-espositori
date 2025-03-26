<?php
include('../config.php');
include('../session.php');
include('../queries.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) 
    {
        $_SESSION['error'] = 'Inserisci email e password';
        header('Location: /progetto-espositori/login.php');
        exit;
    }    
    $user = getUserByEmail($pdo, $email);

    if ($user) 
    {
        if (password_verify($password, $user['Password'])) 
        {
            $_SESSION['user_id'] = $user['Id_Utente'];
            $_SESSION['user_email'] = $user['Email'];
            $_SESSION['user_role'] = $user['Ruolo'];

            if ($user['Ruolo'] === 'Personale') 
            {
                header('Location: /progetto-espositori/Personale/dashboard_personale.php');
                exit;
            } 
            else 
            {
                header('Location: /progetto-espositori/index.php');
                exit;
            }
            exit;
        } 
        else 
        {
            $_SESSION['error'] = 'Password errata';
        }
    } 
    else 
    {
        $_SESSION['error'] = 'Metodo di richiesta non valido';
        header('Location: /progetto-espositori/login.php');
        exit;
    }
    } 
else 
{
    $_SESSION['error'] = 'Nessun utente trovato con questa email';
}
?>