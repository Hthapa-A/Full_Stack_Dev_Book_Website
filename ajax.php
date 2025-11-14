<?php
include('db_connect.php'); 

header('Content-Type: application/json; charset=utf-8');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $mysqli->prepare("SELECT id, title, author, genre, published_year, description FROM books WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $book = $result->fetch_assoc();
        // Ensure null if not found
        if (!$book) $book = null;
        echo json_encode($book);
        $stmt->close();
    } else {
        echo json_encode(null);
    }
} else {
    echo json_encode(null);
}
?>
