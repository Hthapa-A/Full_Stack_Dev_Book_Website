<?php
include('db_connect.php');
include('templates/header.php');

$id = intval($_GET['id']);
$s = $mysqli->prepare("SELECT * FROM books WHERE id=?");
$s->bind_param("i", $id);
$s->execute();
$b = $s->get_result()->fetch_assoc();
?>
<div class="container mt-4 text-center">
  <h3><?= htmlspecialchars($b['title']) ?></h3>
  <p><b>Author:</b> <?= htmlspecialchars($b['author']) ?></p>
  <p><b>Genre:</b> <?= htmlspecialchars($b['genre']) ?></p>
  <p><b>Year:</b> <?= htmlspecialchars($b['published_year']) ?></p>
  <img id="cover" class="img-thumbnail" width="128" height="180">
</div>

<script>
document.addEventListener('DOMContentLoaded',async()=>{
 const img=document.getElementById('cover');
 const title="<?= addslashes($b['title']) ?>";
 try{
   const r=await fetch(`https://www.googleapis.com/books/v1/volumes?q=intitle:${encodeURIComponent(title)}`);
   const d=await r.json();
   img.src=d.items?.[0]?.volumeInfo?.imageLinks?.thumbnail||'https://via.placeholder.com/128x180?text=No+Cover';
 }catch{img.src='https://via.placeholder.com/128x180?text=No+Cover';}
});
</script>

<?php include('templates/footer.php'); ?>
