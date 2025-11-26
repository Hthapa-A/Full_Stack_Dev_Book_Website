<?php
include('db_connect.php');
include('audit_log.php');
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id'])) {
    die('Please login to add books.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CAPTCHA check
    $provided = trim($_POST['captcha'] ?? '');
    if (!isset($_SESSION['captcha_code']) || $provided === '' || $provided !== (string)$_SESSION['captcha_code']) {
        die('CAPTCHA incorrect. Please go back and try again.');
    }

    // input filtering
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $genre = trim($_POST['genre'] ?? '');
    $published_year = isset($_POST['published_year']) && $_POST['published_year'] !== '' ? intval($_POST['published_year']) : null;

    if ($title === '') {
        die('Title is required.');
    }

    if ($published_year === null) {
        $stmt = $mysqli->prepare("INSERT INTO books (title, author, genre, published_year, created_at) VALUES (?, ?, ?, NULL, NOW())");
        $stmt->bind_param("sss", $title, $author, $genre);
    } else {
        $stmt = $mysqli->prepare("INSERT INTO books (title, author, genre, published_year, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("sssi", $title, $author, $genre, $published_year);
    }

    if (!$stmt->execute()) {
        die('Insert failed: ' . htmlspecialchars($stmt->error));
    }
    $book_id = $stmt->insert_id;
    $stmt->close();

    // Log the add action using session username if set
    $user = $_SESSION['username'] ?? 'unknown';
    log_action($mysqli, $user, 'add', 'books', $book_id, "Added book: $title");

    header("Location: list_books.php");
    exit;
}
