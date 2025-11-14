<?php
include('db_connect.php');
include('templates/header.php');
include('audit_log.php');

$id = intval($_GET['id']);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $author = isset($_POST['author']) ? trim($_POST['author']) : '';
    $genre = isset($_POST['genre']) ? trim($_POST['genre']) : '';
    $published_year = isset($_POST['published_year']) && $_POST['published_year'] !== '' ? intval($_POST['published_year']) : null;

    if ($published_year === null) {
        $stmt = $mysqli->prepare("UPDATE books SET title=?, author=?, genre=?, published_year=NULL WHERE id=?");
        $stmt->bind_param("sssi", $title, $author, $genre, $id);
    } else {
        $stmt = $mysqli->prepare("UPDATE books SET title=?, author=?, genre=?, published_year=? WHERE id=?");
        $stmt->bind_param("sssii", $title, $author, $genre, $published_year, $id);
    }
    $stmt->execute();
    $stmt->close();

    // Log the edit action
    log_action($mysqli, 'admin', 'edit', 'books', $id, "Edited book: $title");

    header("Location: list_books.php");
    exit;
}

$r = $mysqli->prepare("SELECT * FROM books WHERE id=?");
$r->bind_param("i", $id);
$r->execute();
$book = $r->get_result()->fetch_assoc();
?>
<div class="container mt-4">
  <h3>Edit Book</h3>
  <form method="post">
    <input class="form-control mb-2" name="title" value="<?= htmlspecialchars($book['title']) ?>">
    <input class="form-control mb-2" name="author" value="<?= htmlspecialchars($book['author']) ?>">
    <input class="form-control mb-2" name="genre" value="<?= htmlspecialchars($book['genre']) ?>">
    <input class="form-control mb-2" name="published_year" type="number" value="<?= htmlspecialchars($book['published_year']) ?>">
    <button class="btn btn-primary">Save</button>
  </form>
</div>
<?php include('templates/footer.php'); ?>
