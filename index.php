<?php include 'templates/header.php'; ?>
<?php
include 'db_connect.php';

// Fetch unique genres for category list
$genres = [];
$gRes = $mysqli->query("SELECT DISTINCT genre FROM books WHERE genre IS NOT NULL AND genre<>'' ORDER BY genre ASC");
if ($gRes) {
    while ($g = $gRes->fetch_assoc()) {
        $genre = trim($g['genre']);
        if ($genre !== '') $genres[] = htmlspecialchars($genre);
    }
    $gRes->free();
}

// If a genre filter is set (via ?genre=...), filter
$genreFilter = '';
$bindGenre = false;
if (!empty($_GET['genre'])) {
    $genreFilter = trim($_GET['genre']);
    $bindGenre = true;
}

// Fetch books (optionally filtered)
if ($bindGenre) {
    $stmt = $mysqli->prepare("SELECT * FROM books WHERE genre = ? ORDER BY id ASC");
    $stmt->bind_param("s", $genreFilter);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $mysqli->query("SELECT * FROM books ORDER BY id ASC");
}

$books = [];
while ($book = $result->fetch_assoc()) {
    $books[] = [
        'id' => (int)$book['id'],
        'title' => htmlspecialchars($book['title'], ENT_QUOTES),
        'author' => htmlspecialchars($book['author']),
        'genre' => htmlspecialchars($book['genre']),
        'published_year' => htmlspecialchars($book['published_year']),
        'description' => htmlspecialchars($book['description'] ?? '')
    ];
}
if (isset($stmt)) $stmt->close();
?>

<div class="container mt-4">
  <div class="row">
    <div class="col-md-3">
      <h5>Categories</h5>
      <div class="list-group category-sidebar">
        <a class="list-group-item list-group-item-action <?= !$bindGenre ? 'active' : '' ?>" href="index.php">All</a>
        <?php foreach ($genres as $g): ?>
            <a class="list-group-item list-group-item-action <?= ($bindGenre && $genreFilter === $g) ? 'active' : '' ?>" href="index.php?genre=<?= urlencode($g) ?>"><?= $g ?></a>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="col-md-9">
      <h2>All Books <?= $bindGenre ? ' — ' . htmlspecialchars($genreFilter) : '' ?></h2>
      <table class="table table-bordered table-hover">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Cover</th>
            <th>Title</th>
            <th>Author</th>
            <th>Genre</th>
            <th>Year</th>
            <?php if (isset($_SESSION['user_id'])): ?>
              <th>Actions</th>
            <?php endif; ?>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($books as $b): ?>
        <tr>
          <td><?= $b['id'] ?></td>
          <td><img id="cover-<?= $b['id'] ?>" width="50" height="75" style="object-fit:cover; border:1px solid #ccc;" src="https://via.placeholder.com/50x75?text=Loading..."></td>
          <td><?= $b['title'] ?></td>
          <td><?= $b['author'] ?></td>
          <td><?= $b['genre'] ?></td>
          <td><?= $b['published_year'] ?></td>
          <?php if (isset($_SESSION['user_id'])): ?>
          <td>
            <a href="edit.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
            <a href="delete.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this book?')">Delete</a>
          </td>
          <?php endif; ?>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<style>
/* Modern scrollable category sidebar */
.category-sidebar {
    max-height: 70vh;
    overflow-y: auto;
}
.category-sidebar .list-group-item.active {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: #fff;
}
.category-sidebar .list-group-item:hover {
    background-color: #e2e6ea;
}
</style>
<script>
const booksHome = <?= json_encode($books) ?>;
// Batch fetching covers from Open Library
async function loadCoversHome() {
    const batchSize = 20; // fetch all books at a time
    for (let i = 0; i < booksHome.length; i += batchSize) {
        const batch = booksHome.slice(i, i + batchSize);
        await Promise.all(batch.map(async book => {
            const img = document.getElementById('cover-' + book.id);
            try {
                const res = await fetch(`https://openlibrary.org/search.json?title=${encodeURIComponent(book.title)}`);
                const data = await res.json();
                const coverId = data.docs?.[0]?.cover_i;
                img.src = coverId 
                    ? `https://covers.openlibrary.org/b/id/${coverId}-M.jpg` 
                    : 'https://via.placeholder.com/50x75?text=No+Cover';
            } catch {
                img.src = 'https://via.placeholder.com/50x75?text=No+Cover';
            }
        }));
    }
}
document.addEventListener('DOMContentLoaded', loadCoversHome);
</script>
<?php include 'templates/footer.php'; ?>