<?php
$_db_host = '127.0.0.1';
$db_dbnam = 'espositori';
$db_username = 'espositori';
$db_password = 'espositori.123';

$pdo = new PDO(dsn: "mysql:host=$_db_host;dbname=$db_dbnam", username: $db_username, password: $db_password);
$pdo->setAttribute(attribute: PDO::ATTR_ERRMODE, value: PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(attribute: PDO::ATTR_DEFAULT_FETCH_MODE, value: PDO::FETCH_ASSOC);