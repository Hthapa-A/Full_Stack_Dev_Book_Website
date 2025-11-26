<?php
include 'db_connect.php';

function log_action($mysqli, $user, $action, $table, $book_id, $details = null) {
    // truncate fields to safe lengths (match your DB)
    $user = substr($user ?? '', 0, 50);
    $action = substr($action ?? '', 0, 20);
    $table = substr($table ?? '', 0, 50);
    $details = $details !== null ? substr($details, 0, 255) : null;

    $stmt = $mysqli->prepare("INSERT INTO audit_log (user, action, table_name, book_id, details, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    if (!$stmt) {
        error_log("Audit log prepare failed: " . $mysqli->error);
        return false;
    }
    $stmt->bind_param("sssis", $user, $action, $table, $book_id, $details);
    if (!$stmt->execute()) {
        error_log("Audit log execute failed: " . $stmt->error);
        $stmt->close();
        return false;
    }
    $stmt->close();
    return true;
}
?>
