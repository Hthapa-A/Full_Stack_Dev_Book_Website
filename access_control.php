<?php
// include where needed: e.g. include 'access_control.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) {
    // redirect to login with a friendly message (or die with link)
    header('Location: login.php');
    exit;
}
