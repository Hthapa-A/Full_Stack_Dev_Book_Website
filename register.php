<?php
// register.php
if (session_status() === PHP_SESSION_NONE) session_start();
include 'db_connect.php';
include 'audit_log.php';

$error = '';

// Generate captcha on GET only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $a = rand(1,9);
    $b = rand(1,9);
    $op = '+';
    $ans = $a + $b;
    $_SESSION['captcha_question'] = "$a $op $b = ?";
    $_SESSION['captcha_answer'] = (string)$ans;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $provided = trim($_POST['captcha'] ?? '');
    if (!isset($_SESSION['captcha_answer']) || $provided === '' || $provided !== (string)($_SESSION['captcha_answer'])) {
        $error = "Incorrect captcha.";
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $error = "Please provide username and password.";
        } else {
            // check duplicate
            $s = $mysqli->prepare("SELECT id FROM users WHERE username = ?");
            $s->bind_param("s", $username);
            $s->execute();
            $exists = $s->get_result()->fetch_assoc();
            $s->close();

            if ($exists) {
                $error = "Username already taken. Try logging in.";
            } else {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $ins = $mysqli->prepare("INSERT INTO users (username, password, created_at) VALUES (?, ?, NOW())");
                $ins->bind_param("ss", $username, $hash);
                if ($ins->execute()) {
                    $newId = $ins->insert_id;
                    $ins->close();

                    // set session (auto-login)
                    $_SESSION['user_id'] = $newId;
                    $_SESSION['username'] = $username;

                    log_action($mysqli, $username, 'register', 'users', $newId, "User registered");

                    header("Location: index.php");
                    exit;
                } else {
                    $error = "Registration failed.";
                }
            }
        }
    }

    // regenerate captcha after POST
    $a = rand(1,9);
    $b = rand(1,9);
    $_SESSION['captcha_question'] = "$a + $b = ?";
    $_SESSION['captcha_answer'] = (string)($a+$b);
}

include 'templates/header.php';
?>
<div class="container mt-4">
  <h3>Register</h3>

  <?php if($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="post">
      <input class="form-control mb-2" name="username" placeholder="Username" required value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
      <input class="form-control mb-2" name="password" type="password" placeholder="Password" required>

      <label><b>Captcha: <?= htmlspecialchars($_SESSION['captcha_question'] ?? '') ?></b></label>
      <input class="form-control mb-2" name="captcha" required placeholder="Enter answer (numbers only)">

      <button class="btn btn-success">Register</button>
      <a class="btn btn-link" href="login.php">Already have an account? Login</a>
  </form>
</div>

<?php include 'templates/footer.php'; ?>
