<?php
// File: admin/manage_technicians.php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/../config/database.php';

$action = $_GET['action'] ?? '';
$error = '';
$username = $fullname = '';

// Handle Create
if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $fullname = trim($_POST['fullname'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($username === '' || $password === '') {
        $error = 'Please enter a username and password.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare(
            'INSERT INTO technicians (username, password_hash, fullname) VALUES (?, ?, ?)'
        );
        $stmt->execute([$username, $hash, $fullname]);
        header('Location: manage_technicians.php'); exit;
    }
}
// Handle Edit
elseif ($action === 'edit') {
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $pdo->prepare('SELECT * FROM technicians WHERE id = ?');
    $stmt->execute([$id]);
    $tech = $stmt->fetch();
    if (!$tech) { header('Location: manage_technicians.php'); exit; }
    $username = $tech['username'];
    $fullname = $tech['fullname'];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $fullname = trim($_POST['fullname'] ?? '');
        $password = $_POST['password'] ?? '';
        if ($username === '') {
            $error = 'Username cannot be empty.';
        } else {
            if ($password !== '') {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare(
                    'UPDATE technicians SET username=?, fullname=?, password_hash=? WHERE id=?'
                );
                $stmt->execute([$username, $fullname, $hash, $id]);
            } else {
                $stmt = $pdo->prepare(
                    'UPDATE technicians SET username=?, fullname=? WHERE id=?'
                );
                $stmt->execute([$username, $fullname, $id]);
            }
            header('Location: manage_technicians.php'); exit;
        }
    }
}
// Handle Delete
elseif ($action === 'delete') {
    $id = (int)($_GET['id'] ?? 0);
    $pdo->prepare('DELETE FROM technicians WHERE id = ?')->execute([$id]);
    header('Location: manage_technicians.php'); exit;
}

// Fetch List
$stmt = $pdo->query('SELECT id, username, fullname, created_at FROM technicians');
$techs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manage Technicians</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #0077ff;
      --secondary: #00d4ff;
      --bg-gradient: linear-gradient(135deg, #0077ff, #00d4ff);
      --card-bg: rgba(255, 255, 255, 0.9);
      --text-dark: #222;
      --error-bg: rgba(220,53,69,0.1);
      --btn-secondary: #6c757d;
    }
    * { margin:0; padding:0; box-sizing:border-box; }
    body {
      font-family: 'Poppins', sans-serif;
      background: var(--bg-gradient);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    .site-header {
      backdrop-filter: blur(10px);
      background: var(--header-bg);
      padding: 1rem 0;
    }
    .site-header .container {
      display: flex;
      align-items: center;
      justify-content: space-between;
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 1rem;
    }
    .logo a {
      font-size: 1.5rem;
      font-weight: 600;
      color: #fff;
      text-decoration: none;
    }
    .main-nav ul {
      list-style: none;
      display: flex;
      gap: 1rem;
    }
    .main-nav a {
      color: #fff;
      text-decoration: none;
      font-weight: 500;
      padding: 0.5rem 1rem;
      border-radius: 6px;
      transition: background 0.2s;
    }
    .main-nav a:hover {
      background: rgba(255,255,255,0.2);
    }
    main.container {
      flex: 1;
      max-width: 1200px;
      margin: 2rem auto;
      padding: 0 1rem;
    }
    .card {
      background: var(--card-bg);
      backdrop-filter: blur(12px);
      border-radius: 16px;
      width: 100%;
      max-width: 900px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.1);
      animation: fadeIn 0.8s ease-out;
      padding: 2rem;
    }
    h1 { text-align: center; margin-bottom: 1.5rem; font-size: 2rem; }
    .actions {margin-bottom: 1rem; }
    .actions .btn {
      background: var(--primary);
      color: #fff;
      padding: 0.5rem 1rem;
      text-decoration: none;
      border-radius: 8px;
      transition: background 0.2s;
    }
    .actions .btn:hover { background: var(--secondary); }
    .error { background: var(--error-bg); padding: 0.75rem; border-radius: 6px; margin-bottom: 1rem; color: #dc3545; }
    form { margin-bottom: 2rem; }
    .form-group { margin-bottom: 1rem; }
    .form-group label { display:block; font-weight:600; margin-bottom:0.25rem; }
    .form-group input { width:100%; padding:0.75rem 1rem; border:1px solid #ccc; border-radius:8px; }
    .btn-primary { background: var(--primary); color:#fff; padding:0.75rem 1rem; border:none; border-radius:8px; cursor:pointer; transition:background 0.2s; }
    .btn-primary:hover { background: var(--secondary); }
    .btn-secondary { background: var(--btn-secondary); color:#fff; text-decoration:none; padding:0.75rem 1rem; border-radius:8px; display:inline-block; transition:background 0.2s; }
    .btn-secondary:hover { background: #5a6268; }
    table { width:100%; border-collapse:collapse; }
    th, td { padding:0.75rem 1rem; border-bottom:1px solid #ddd; }
    th { background: var(--primary); color:#fff; position:sticky; top:0; }
    tbody tr:hover { background: rgba(0,119,255,0.05); }
    @keyframes fadeIn { from { opacity:0; transform:translateY(20px);} to {opacity:1;transform:translateY(0);} }
  </style>
</head>
<body>
<header class="site-header">
    <div class="container">
      <h1 class="logo"><a >SportsPro Admin</a></h1>
      <nav class="main-nav">
        <ul>
          <li><a href="dashboard.php">Dashboard</a></li>
          <li><a href="logout.php">Log Out</a></li>
          <li><a href="../index.php">Back to Menu</a></li>
        </ul>
      </nav>
    </div>
  </header>
  <main class="container">
  <div class="card">
    <h1>Manage Technicians</h1>
    <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($action === 'create' || $action === 'edit'): ?>
      <form method="post" novalidate>
        <div class="form-group"><label for="username">Username</label><input id="username" name="username" type="text" required value="<?= htmlspecialchars($username) ?>"></div>
        <div class="form-group"><label for="fullname">Full Name</label><input id="fullname" name="fullname" type="text" value="<?= htmlspecialchars($fullname) ?>"></div>
        <div class="form-group"><label for="password"><?= $action==='create' ? 'Password' : 'New Password (leave blank to keep)' ?></label><input id="password" name="password" type="password" <?= $action==='create'?'required':'' ?>></div>
        <button type="submit" class="btn-primary"><?= $action==='create'?'Save Technician':'Update Technician' ?></button>
        <a href="manage_technicians.php" class="btn-secondary">Cancel</a>
      </form>
    <?php else: ?>
      <div class="actions">
        <a href="dashboard.php" class="btn">Back</a>
        <a href="manage_technicians.php?action=create" class="btn">Add New</a>
      </div>
      <?php if (empty($techs)): ?>
        <p>No technicians found.</p>
      <?php else: ?>
        <table>
          <thead><tr><th>ID</th><th>Username</th><th>Full Name</th><th>Created At</th><th>Actions</th></tr></thead>
          <tbody>
            <?php foreach ($techs as $t): ?>
            <tr>
              <td><?= htmlspecialchars($t['id']) ?></td>
              <td><?= htmlspecialchars($t['username']) ?></td>
              <td><?= htmlspecialchars($t['fullname']) ?></td>
              <td><?= htmlspecialchars($t['created_at']) ?></td>
              <td>
                <a href="manage_technicians.php?action=edit&id=<?= $t['id'] ?>" class="btn-secondary">Edit</a>
                <a href="manage_technicians.php?action=delete&id=<?= $t['id'] ?>" class="btn-secondary" onclick="return confirm('Delete this technician?');">Delete</a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    <?php endif; ?>
  </div>
  </main>
</body>
</html>