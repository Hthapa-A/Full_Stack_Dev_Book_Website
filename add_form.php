<?php include('templates/header.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"><title>Add Book</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
  <h3>Add Book</h3>
  <form action="add.php" method="post" novalidate>
    <input class="form-control mb-2" name="title" placeholder="Title" required>
    <input class="form-control mb-2" name="author" placeholder="Author">
    <input class="form-control mb-2" name="genre" placeholder="Genre">
    <input class="form-control mb-2" name="published_year" type="number" placeholder="Year (e.g. 2023)" min="0" max="9999">
    <button class="btn btn-success">Add</button>
  </form>
</div>

<?php include('templates/footer.php'); ?>
