<?php
// config.php - DB connection
session_start();
$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = ''; // change if you set a root password
$DB_NAME = 'lostmate_db';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
    die("DB connect failed: " . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');

function is_logged() {
    return isset($_SESSION['user_id']);
}
function is_admin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}
function esc($s) {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
?>