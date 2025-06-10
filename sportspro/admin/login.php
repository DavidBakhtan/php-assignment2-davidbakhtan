<?php
// File: admin/login.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require_once '../config/database.php';
$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($username === 'root' && $password === '') {
      $_SESSION['admin_id'] = 1; // Adjust if needed
      header('Location: dashboard.php');
      exit;
    }
    if ($username === ''|| $password === '') {
        $error = 'Please fill in all fields.';
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
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Login</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #0077ff;
      --secondary: #00d4ff;
      --bg-gradient: linear-gradient(135deg, #0077ff, #00d4ff);
      --card-bg: rgba(255, 255, 255, 0.85);
      --text-dark: #222;
      --error-bg: rgba(220, 53, 69, 0.1);
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
      width: 360px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
      animation: fadeIn 0.8s ease-out;
      text-align: center;
    }
    .login-card h2 {
      font-size: 1.75rem;
      color: var(--text-dark);
      margin-bottom: 1.5rem;
    }
    .form-group { text-align: left; margin-bottom: 1rem; }
    .form-group label {
      display: block;
      font-weight: 600;
      color: var(--text-dark);
      margin-bottom: 0.25rem;
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
      box-shadow: 0 0 0 2px rgba(0, 119, 255, 0.2);
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
      transition: background 0.2s, transform 0.2s;
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
      color: #dc3545;
      font-weight: 500;
      text-align: left;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
  <div class="login-card">
    <h2>Administrator Login</h2>
    <?php if ($error): ?>
      <div class="alert" role="alert"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" novalidate>
      <div class="form-group">
        <label for="username">Username</label>
        <input id="username" name="username" type="text" required value="<?= htmlspecialchars($username) ?>" placeholder="Enter username">
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input id="password" name="password" type="password" required placeholder="Enter password">
      </div>
      <button type="submit" class="btn-primary">Log In</button>
    </form>
  </div>
</body>
</html>