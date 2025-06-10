<?php
// technician/login.php
session_start();
require_once('../config/database.php');

$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // ---- ROOT BYPASS: Allow username "root" with empty password ----
    if ($username === 'root' && $password === '') {
        $_SESSION['tech_id'] = 1; // Adjust if needed
        header('Location: incidents.php');
        exit;
    }
    // ----------------------------------------------------------------

    // Validation for other users
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
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Technician Login</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #0077ff;
      --secondary: #00d4ff;
      --bg-gradient: linear-gradient(135deg, #0077ff, #00d4ff);
      --card-bg: rgba(255, 255, 255, 0.8);
      --text-dark: #222;
      --error-bg: rgba(255, 0, 0, 0.1);
    }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Poppins', sans-serif;
      background: var(--bg-gradient);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .login-card {
      background: var(--card-bg);
      backdrop-filter: blur(12px);
      border-radius: 16px;
      padding: 2rem;
      width: 320px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.1);
      animation: fadeIn 0.8s ease-out;
      text-align: center;
    }
    .login-card h1 {
      font-size: 1.75rem;
      color: var(--text-dark);
      margin-bottom: 1.5rem;
    }
    .form-group {
      position: relative;
      margin-bottom: 1rem;
      text-align: left;
    }
    .form-group label {
      display: block;
      font-weight: 600;
      margin-bottom: 0.25rem;
      color: var(--text-dark);
    }
    .form-group input {
      width: 100%;
      padding: 0.75rem 1rem;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 1rem;
    }
    .form-group input:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 2px rgba(0,119,255,0.2);
    }
    .btn-primary {
      width: 100%;
      padding: 0.75rem;
      font-size: 1rem;
      font-weight: 600;
      color: #fff;
      background: var(--primary);
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background .2s, transform .2s;
    }
    .btn-primary:hover {
      background: var(--secondary);
      transform: translateY(-2px);
    }
    .alert {
      margin-bottom: 1rem;
      padding: 0.75rem;
      border-radius: 6px;
      background: var(--error-bg);
      color: #900;
      font-weight: 500;
      text-align: left;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .login-footer {
      position: fixed;
      bottom: 1rem;
      width: 100%;
      text-align: center;
      color: #fff;
      font-size: 0.9rem;
    }
  </style>
</head>
<body>
  <div class="login-card">
    <h1>Technician Login</h1>
    <?php if ($error): ?>
      <div class="alert" role="alert"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" class="login-form" novalidate>
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" value="<?= htmlspecialchars($username) ?>" required placeholder="Your username">
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required placeholder="Your password">
      </div>
      <button type="submit" class="btn-primary">Log In</button>
    </form>
  </div>
  <footer class="login-footer">
    &copy; <?= date('Y') ?> SportsPro Inc.
  </footer>
</body>
</html>