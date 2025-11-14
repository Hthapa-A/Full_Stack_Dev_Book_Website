<?php
header('Content-Type: application/json; charset=utf-8');

include('db_connect.php');

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$limit = 30; // limit returned suggestions

if ($q === '') {
    // default list (most recent)
    $sql = "SELECT id, title, author, genre, published_year FROM books ORDER BY published_year DESC LIMIT ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // if numeric, also try matching year
    $isNumeric = is_numeric($q) ? intval($q) : null;
    $like = '%' . $q . '%';

    if ($isNumeric !== null) {
        $sql = "SELECT id, title, author, genre, published_year FROM books
                WHERE title LIKE ? OR author LIKE ? OR genre LIKE ? OR published_year = ?
                ORDER BY published_year DESC LIMIT ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sssii", $like, $like, $like, $isNumeric, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $sql = "SELECT id, title, author, genre, published_year FROM books
                WHERE title LIKE ? OR author LIKE ? OR genre LIKE ?
                ORDER BY published_year DESC LIMIT ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sssi", $like, $like, $like, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
    }
}

$books = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $books[] = [
            'id' => (int)$row['id'],
            'title' => $row['title'],
            'author' => $row['author'],
            'genre' => $row['genre'],
            'published_year' => $row['published_year']
        ];
    }
}
echo json_encode($books);
