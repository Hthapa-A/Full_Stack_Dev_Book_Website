<?php include('db_connect.php'); include('templates/header.php'); ?>
<div class="container mt-4">
  <h3>All Books</h3>
  <table class="table table-striped">
    <thead><tr><th>ID</th><th>Cover</th><th>Title</th><th>Author</th><th>Genre</th><th>Year</th><th>Actions</th></tr></thead>
    <tbody>
<?php
$r = $mysqli->query("SELECT * FROM books ORDER BY published_year DESC");
while($row = $r->fetch_assoc()):
?>
<tr>
  <td><?= (int)$row['id'] ?></td>
  <td><img id="cover-<?= (int)$row['id'] ?>" width="60" height="90" onload="loadCover('<?= addslashes($row['title']) ?>',<?= (int)$row['id'] ?>)"></td>
  <td><?= htmlspecialchars($row['title']) ?></td>
  <td><?= htmlspecialchars($row['author']) ?></td>
  <td><?= htmlspecialchars($row['genre']) ?></td>
  <td><?= htmlspecialchars($row['published_year']) ?></td>
  <td>
    <a href="details.php?id=<?= (int)$row['id'] ?>" class="btn btn-info btn-sm">View</a>
    <a href="edit.php?id=<?= (int)$row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
    <a href="delete.php?id=<?= (int)$row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete?');">Del</a>
  </td>
</tr>
<?php endwhile; ?>
    </tbody>
  </table>
</div>

<script>
async function loadCover(title,id){
  const img=document.getElementById('cover-'+id);
  try{
    const r=await fetch(`https://www.googleapis.com/books/v1/volumes?q=intitle:${encodeURIComponent(title)}`);
    const d=await r.json();
    img.src=d.items?.[0]?.volumeInfo?.imageLinks?.thumbnail || 'https://via.placeholder.com/60x90?text=No+Cover';
  }catch{
    img.src='https://via.placeholder.com/60x90?text=No+Cover';
  }
}
</script>

<?php include('templates/footer.php'); ?>
