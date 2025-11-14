<?php
include('db_connect.php');
include('audit_log.php');

$id = intval($_GET['id']);

// Get book title for audit details
$result = $mysqli->prepare("SELECT title FROM books WHERE id=?");
$result->bind_param("i", $id);
$result->execute();
$book = $result->get_result()->fetch_assoc();
$result->close();

// Delete the book
$stmt = $mysqli->prepare("DELETE FROM books WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

// Log the delete action
log_action($mysqli, 'admin', 'delete', 'books', $id, "Deleted book: ".$book['title']);

header("Location:list_books.php");
exit;
?>
