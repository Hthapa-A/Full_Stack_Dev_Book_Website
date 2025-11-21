<?php include('db_connect.php'); include('templates/header.php'); ?>
<div class="container mt-4">
  <h3>All Books</h3>
  <table class="table table-striped">
    <thead>
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
<?php
$r = $mysqli->query("SELECT * FROM books ORDER BY id ASC"); // keep ID order
while($row = $r->fetch_assoc()):
    $id = (int)$row['id'];
    $title = htmlspecialchars($row['title'], ENT_QUOTES);
?>
<tr>
  <td><?= $id ?></td>
  <td>
    <img id="cover-<?= $id ?>" width="60" height="90" style="object-fit:cover; border:1px solid #ccc;" src="https://via.placeholder.com/60x90?text=Loading...">
  </td>
  <td><?= $title ?></td>
  <td><?= htmlspecialchars($row['author']) ?></td>
  <td><?= htmlspecialchars($row['genre']) ?></td>
  <td><?= htmlspecialchars($row['published_year']) ?></td>
  <td>
    <a href="details.php?id=<?= $id ?>" class="btn btn-info btn-sm">View</a>
    <a href="edit.php?id=<?= $id ?>" class="btn btn-warning btn-sm">Edit</a>
    <a href="delete.php?id=<?= $id ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete?');">Del</a>
  </td>
</tr>
<?php endwhile; ?>
    </tbody>
  </table>
</div>

<script>
const booksList = <?= json_encode($r->fetch_all(MYSQLI_ASSOC)) ?>;
document.addEventListener('DOMContentLoaded', async () => {
    const imgs = document.querySelectorAll('img[id^="cover-"]');
    for (let img of imgs) {
        const title = img.getAttribute('id').split('-')[1];
        try {
            const res = await fetch(`https://openlibrary.org/search.json?title=${encodeURIComponent(img.closest('tr').children[2].textContent)}`);
            const data = await res.json();
            const coverId = data.docs?.[0]?.cover_i;
            img.src = coverId 
                ? `https://covers.openlibrary.org/b/id/${coverId}-M.jpg` 
                : 'https://via.placeholder.com/60x90?text=No+Cover';
        } catch {
            img.src = 'https://via.placeholder.com/60x90?text=No+Cover';
        }
    }
});
</script>

<?php include('templates/footer.php'); ?>
