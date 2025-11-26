<?php
include('db_connect.php');
include('audit_log.php');
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id'])) {
    die('Please login to delete books.');
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) die('Invalid id.');

$result = $mysqli->prepare("SELECT title FROM books WHERE id=?");
$result->bind_param("i", $id);
$result->execute();
$book = $result->get_result()->fetch_assoc();
$result->close();

if (!$book) die('Book not found.');

// Note: delete is done via GET link after user confirmed in UI.
// If you prefer POST with CAPTCHA, change UI to POST and validate captcha similarly.

$stmt = $mysqli->prepare("DELETE FROM books WHERE id=?");
$stmt->bind_param("i", $id);
if (!$stmt->execute()) {
    die('Delete failed: '.htmlspecialchars($stmt->error));
}
$stmt->close();

// Log delete
$user = $_SESSION['username'] ?? 'unknown';
log_action($mysqli, $user, 'delete', 'books', $id, "Deleted book: " . $book['title']);

header("Location: list_books.php");
exit;
