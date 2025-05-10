<?php
header('Content-Type: application/json');
include_once '../config.php';
include_once '../queries.php';

// Leggi il corpo della richiesta JSON
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data || !isset($data['username']) || !isset($data['email'])) {
    echo json_encode([
        'error' => true,
        'message' => 'Dati mancanti'
    ]);
    exit;
}

$username = trim($data['username']);
$email = filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL);

// Verifica se l'email esiste
$emailExists = emailExists($pdo, $email);

// Verifica se lo username esiste
$usernameExists = usernameExists($pdo, $username);

echo json_encode([
    'error' => false,
    'emailExists' => $emailExists,
    'usernameExists' => $usernameExists
]); 