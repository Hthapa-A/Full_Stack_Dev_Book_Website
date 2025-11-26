<?php
include 'templates/header.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) {
    die('<div class="container mt-4">Please <a href="login.php">login</a> or <a href="register.php">register</a> to add books.</div>');
}

// generate simple math captcha for the form (on GET)
$a = rand(1,9);
$b = rand(1,9);
$op = rand(0,1) ? '+' : '-';
$answer = ($op === '+') ? ($a+$b) : ($a-$b);
$_SESSION['captcha_code'] = (string)$answer;
$_SESSION['captcha_question'] = "$a $op $b = ?";
?>
<div class="container mt-4">
  <h3>Add Book</h3>
  <form action="add.php" method="post" novalidate>
    <input class="form-control mb-2" name="title" placeholder="Title" required>
    <input class="form-control mb-2" name="author" placeholder="Author">
    <input class="form-control mb-2" name="genre" placeholder="Genre">
    <input class="form-control mb-2" name="published_year" type="number" placeholder="Year (e.g. 2023)" min="0" max="9999">
    <div class="mb-2">
      <span class="captcha-box"><?= htmlspecialchars($_SESSION['captcha_question']) ?></span>
      <small class="text-muted ms-2">Enter the result</small>
    </div>
    <input class="form-control mb-2" name="captcha" placeholder="Enter CAPTCHA" required>
    <button class="btn btn-success">Add</button>
  </form>
</div>
<?php include 'templates/footer.php'; ?>
