<?php include 'templates/header.php'; ?>
<?php
include 'db_connect.php';
$result = $mysqli->query("SELECT * FROM books ORDER BY created_at DESC");
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
    <?php while($book = $result->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($book['id']) ?></td>
      <td><img src="https://covers.openlibrary.org/b/isbn/<?= urlencode($book['id']) ?>-M.jpg" width="50"></td>
      <td><?= htmlspecialchars($book['title']) ?></td>
      <td><?= htmlspecialchars($book['author']) ?></td>
      <td><?= htmlspecialchars($book['genre']) ?></td>
      <td><?= htmlspecialchars($book['published_year']) ?></td>
      <td>
        <a href="edit.php?id=<?= $book['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
        <a href="delete.php?id=<?= $book['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this book?')">Delete</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </tbody>
</table>
<?php include 'templates/footer.php'; ?>
