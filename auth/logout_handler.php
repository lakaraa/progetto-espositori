<?php
error_reporting(E_ALL); // Report all errors and warnings
ini_set('display_errors', 1); // Display errors on the screen

include_once '../session.php';
session_unset();
session_destroy();
header('Location: ../index.php');
exit;
?>