<?php
include('db_connect.php');
include('audit_log.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $genre = $_POST['genre'];
    $published_year = $_POST['published_year'] !== '' ? intval($_POST['published_year']) : null;

    if ($published_year === null) {
        $stmt = $mysqli->prepare("INSERT INTO books (title, author, genre, published_year) VALUES (?, ?, ?, NULL)");
        $stmt->bind_param("sss", $title, $author, $genre);
    } else {
        $stmt = $mysqli->prepare("INSERT INTO books (title, author, genre, published_year) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $title, $author, $genre, $published_year);
    }

    $stmt->execute();
    $book_id = $stmt->insert_id; // get the inserted book id
    $stmt->close();

    // Log the add action
    log_action($mysqli, 'admin', 'add', 'books', $book_id, "Added book: $title");

    header("Location: list_books.php");
    exit;
}
?>
