<?php
$_SERVER['REQUEST_METHOD'] = 'GET';
if (session_status() === PHP_SESSION_NONE) session_start();
$_SESSION['detsuid'] = 68;
include 'transactions.php';
?>