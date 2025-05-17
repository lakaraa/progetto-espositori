<?php
// Define the base path for the website
// Check if we're in production (Azure) or local environment
if (strpos($_SERVER['HTTP_HOST'], 'azurewebsites.net') !== false) {
    $base_path = '/';  // In production, use root path
} else {
    $base_path = '/progetto-espositori/';  // In local environment
}

$_db_host = '134.149.24.61';
$db_dbname = 'MySQLDB';
$db_username = 'azureuser';
$db_password = 'Espositori.123';

$pdo = new PDO("mysql:host=$_db_host;dbname=$db_dbname", $db_username, $db_password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

#echo "Connessione riuscita!";

?>
