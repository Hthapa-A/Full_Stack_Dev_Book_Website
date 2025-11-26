<?php
include('db_connect.php');
include('templates/header.php');
include('audit_log.php');
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id'])) {
    die('Please login to edit books.');
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) die('Invalid book id.');

$r = $mysqli->prepare("SELECT * FROM books WHERE id=?");
$r->bind_param("i", $id);
$r->execute();
$book = $r->get_result()->fetch_assoc();
$r->close();

if (!$book) die('Book not found.');

// generate captcha for the form (GET)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $a = rand(1,9);
    $b = rand(1,9);
    $op = rand(0,1) ? '+' : '-';
    $code = ($op === '+') ? ($a+$b) : ($a-$b);
    $_SESSION['captcha_code'] = (string)$code;
    $_SESSION['captcha_question'] = "$a $op $b = ?";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // CAPTCHA check
    $provided = trim($_POST['captcha'] ?? '');
    if (!isset($_SESSION['captcha_code']) || $provided === '' || $provided !== (string)$_SESSION['captcha_code']) {
        die('CAPTCHA incorrect. Please go back and try again.');
    }

    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $genre = trim($_POST['genre'] ?? '');
    $published_year = isset($_POST['published_year']) && $_POST['published_year'] !== '' ? intval($_POST['published_year']) : null;

    if ($title === '') {
        die('Title required.');
    }

    if ($published_year === null) {
        $stmt = $mysqli->prepare("UPDATE books SET title=?, author=?, genre=?, published_year=NULL WHERE id=?");
        $stmt->bind_param("sssi", $title, $author, $genre, $id);
    } else {
        $stmt = $mysqli->prepare("UPDATE books SET title=?, author=?, genre=?, published_year=? WHERE id=?");
        $stmt->bind_param("sssii", $title, $author, $genre, $published_year, $id);
    }
    if (!$stmt->execute()) {
        die('Update failed: ' . htmlspecialchars($stmt->error));
    }
    $stmt->close();

    // Log the edit action
    $user = $_SESSION['username'] ?? 'unknown';
    log_action($mysqli, $user, 'edit', 'books', $id, "Edited book: $title");

    header("Location: list_books.php");
    exit;
}
?>
<div class="container mt-4">
  <h3>Edit Book</h3>
  <form method="post">
    <input class="form-control mb-2" name="title" value="<?= htmlspecialchars($book['title']) ?>" required>
    <input class="form-control mb-2" name="author" value="<?= htmlspecialchars($book['author']) ?>">
    <input class="form-control mb-2" name="genre" value="<?= htmlspecialchars($book['genre']) ?>">
    <input class="form-control mb-2" name="published_year" type="number" value="<?= htmlspecialchars($book['published_year']) ?>">
    <div class="mb-2">
      <span class="captcha-box"><?= htmlspecialchars($_SESSION['captcha_question'] ?? '') ?></span>
      <small class="text-muted ms-2">Enter the result</small>
    </div>
    <input class="form-control mb-2" name="captcha" placeholder="Enter CAPTCHA" required>
    <button class="btn btn-primary">Save</button>
  </form>
</div>
<?php include('templates/footer.php'); ?>
