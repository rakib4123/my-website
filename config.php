<?php
// --- ERROR REPORTING (Helpful for debugging) ---
// ini_set('display_errors', 1);
// error_reporting(E_ALL);

// --- DATABASE CREDENTIALS ---
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'electrob_rakib');
define('DB_PASSWORD', 'Hossain@#*&67');
define('DB_NAME', 'electrob_eats_db');

// --- DATABASE CONNECTION ---
$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if($mysqli === false){ die("ERROR: Could not connect. " . $mysqli->connect_error); }

// --- SESSION MANAGEMENT ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>