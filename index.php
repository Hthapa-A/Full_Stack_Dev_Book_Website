<?php include 'templates/header.php'; ?> 
<?php
include 'db_connect.php';
$result = $mysqli->query("SELECT * FROM books ORDER BY id ASC"); // order by ID 123..67
$books = [];
while($book = $result->fetch_assoc()) {
    $books[] = [
        'id' => (int)$book['id'], 
        'title' => htmlspecialchars($book['title'], ENT_QUOTES),
        'author' => htmlspecialchars($book['author']),
        'genre' => htmlspecialchars($book['genre']),
        'published_year' => htmlspecialchars($book['published_year'])
    ];
}
?>
<h2>All Books</h2>
<table class="table table-bordered table-hover">
  <thead class="table-dark">
    <tr>
      <th>ID</th>
      <th>Cover</th>
      <th>Title</th>
      <th>Author</th>
      <th>Genre</th>
      <th>Year</th>
      <th>Actions</th>
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
  <td>
    <a href="edit.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
    <a href="delete.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this book?')">Delete</a>
  </td>
</tr>
<?php endforeach; ?>
  </tbody>
</table>

<script>
const booksHome = <?= json_encode($books) ?>;

// Batch fetching covers from Open Library
async function loadCoversHome() {
    const batchSize = 20; // fetch 67 books at a time
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
