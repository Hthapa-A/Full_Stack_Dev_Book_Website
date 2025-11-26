<?php
// login.php - page version (math CAPTCHA)
if (session_status() === PHP_SESSION_NONE) session_start();
include 'db_connect.php';
include 'audit_log.php';

$error = '';

// Generate captcha on GET only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // two small numbers + operator
    $a = rand(1,9);
    $b = rand(1,9);
    $op = rand(0,1) ? '+' : '-';
    if ($op === '+') {
        $ans = $a + $b;
    } else {
        $ans = $a - $b;
    }
    $_SESSION['captcha_question'] = "$a $op $b = ?";
    $_SESSION['captcha_answer'] = (string)$ans;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate captcha
    $provided = trim($_POST['captcha'] ?? '');
    if (!isset($_SESSION['captcha_answer']) || $provided === '' || $provided !== (string)($_SESSION['captcha_answer'])) {
        $error = "Incorrect captcha.";
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $error = "Fill username and password.";
        } else {
            $stmt = $mysqli->prepare("SELECT id, password FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ($user && password_verify($password, $user['password'])) {
                // login success
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $username;

                // audit log
                log_action($mysqli, $username, 'login', 'users', $user['id'], "User logged in");

                header("Location: index.php");
                exit;
            } else {
                $error = "Invalid login.";
            }
        }
    }
    // regenerate captcha for show after POST (so user sees new one next try)
    $a = rand(1,9);
    $b = rand(1,9);
    $op = rand(0,1) ? '+' : '-';
    $_SESSION['captcha_question'] = "$a $op $b = ?";
    $_SESSION['captcha_answer'] = (string)( $op === '+' ? $a+$b : $a-$b );
}

include 'templates/header.php';
?>
<div class="container mt-4">
  <h3>Login</h3>

  <?php if($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="post">
      <input class="form-control mb-2" name="username" placeholder="Username" required value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
      <input class="form-control mb-2" name="password" type="password" placeholder="Password" required>

      <label><b>Captcha: <?= htmlspecialchars($_SESSION['captcha_question'] ?? '') ?></b></label>
      <input class="form-control mb-2" name="captcha" required placeholder="Enter answer (numbers only)">

      <button class="btn btn-primary">Login</button>
      <a class="btn btn-link" href="register.php">Register</a>
  </form>
</div>

<?php include 'templates/footer.php'; ?>
