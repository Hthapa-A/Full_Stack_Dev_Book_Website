<?php
include 'db_connect.php';

function log_action($mysqli, $user, $action, $table, $book_id, $details = null) {
    $stmt = $mysqli->prepare("INSERT INTO audit_log (user, action, table_name, book_id, details) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssis", $user, $action, $table, $book_id, $details);
    $stmt->execute();
    $stmt->close();
}
?>
