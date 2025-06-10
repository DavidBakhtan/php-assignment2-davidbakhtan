<?php
// File: admin/manage_customers.php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php'); exit;
}
require_once __DIR__ . '/../config/database.php';

$action = $_GET['action'] ?? '';
$error = '';
$username = $fullname = $email = '';

// Create
if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $fullname = trim($_POST['fullname'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($username === '' || $email === '' || $password === '') {
        $error = 'Please fill in username, email, and password.';
    } else {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare(
            'INSERT INTO customers (username, password_hash, fullname, email) VALUES (?, ?, ?, ?)'   
        );
        $stmt->execute([$username, $passwordHash, $fullname, $email]);
        header('Location: manage_customers.php'); exit;
    }
}
// Edit
elseif ($action === 'edit') {
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $pdo->prepare('SELECT * FROM customers WHERE id = ?');
    $stmt->execute([$id]);
    $cust = $stmt->fetch();
    if (!$cust) { header('Location: manage_customers.php'); exit; }
    $username = $cust['username']; $fullname = $cust['fullname']; $email = $cust['email'];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $fullname = trim($_POST['fullname'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        if ($username === '' || $email === '') {
            $error = 'Username and email cannot be empty.';
        } else {
            if ($password !== '') {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare(
                    'UPDATE customers SET username=?, fullname=?, email=?, password_hash=? WHERE id=?'
                );
                $stmt->execute([$username, $fullname, $email, $passwordHash, $id]);
            } else {
                $stmt = $pdo->prepare(
                    'UPDATE customers SET username=?, fullname=?, email=? WHERE id=?'
                );
                $stmt->execute([$username, $fullname, $email, $id]);
            }
            header('Location: manage_customers.php'); exit;
        }
    }
}
// Delete
elseif ($action === 'delete') {
    $id = (int)($_GET['id'] ?? 0);
    $pdo->prepare('DELETE FROM customers WHERE id = ?')->execute([$id]);
    header('Location: manage_customers.php'); exit;
}
// List
$stmt = $pdo->query('SELECT id, username, fullname, email, created_at FROM customers');
$customers = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manage Customers</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #0077ff; --secondary: #00d4ff; --bg-gradient: linear-gradient(135deg,#0077ff,#00d4ff);
      --card-bg: rgba(255,255,255,0.9); --text-dark: #222; --error-bg: rgba(220,53,69,0.1); --btn-secondary: #6c757d;
    }
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'Poppins',sans-serif;background:var(--bg-gradient);min-height:100vh;color:var(--text-dark);
      font-family: 'Poppins', sans-serif;
      background: var(--bg-gradient);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
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
    .card{background:var(--card-bg);backdrop-filter:blur(12px);border-radius:16px;max-width:900px;width:100%;
      box-shadow:0 8px 32px rgba(0,0,0,0.1);animation:fadeIn .8s ease-out;padding:2rem;}
    h1{text-align:center;margin-bottom:1.5rem;font-size:2rem}
    .error{background:var(--error-bg);padding:.75rem;border-radius:6px;margin-bottom:1rem;color:#dc3545}
    .actions{margin-bottom:1rem}
    .actions .btn{background:var(--primary);color:#fff;padding:.5rem 1rem;text-decoration:none;border-radius:8px;transition:background .2s}
    .actions .btn:hover{background:var(--secondary)}
    form{margin-bottom:2rem}
    .form-group{margin-bottom:1rem}
    .form-group label{display:block;font-weight:600;margin-bottom:.25rem}
    .form-group input,.form-group textarea{width:100%;padding:.75rem 1rem;border:1px solid #ccc;border-radius:8px}
    .btn-primary{background:var(--primary);color:#fff;padding:.75rem 1rem;border:none;border-radius:8px;cursor:pointer;transition:background .2s}
    .btn-primary:hover{background:var(--secondary)}
    .btn-secondary{background:var(--btn-secondary);color:#fff;text-decoration:none;padding:.75rem 1rem;border-radius:8px;display:inline-block;transition:background .2s}
    .btn-secondary:hover{background:#5a6268}
    table{width:100%;border-collapse:collapse}
    th,td{padding:.75rem 1rem;border-bottom:1px solid #ddd}
    th{background:var(--primary);color:#fff;position:sticky;top:0}
    tbody tr:hover{background:rgba(0,119,255,0.05)}
    @keyframes fadeIn{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
  </style>
</head>
<body>
<header class="site-header">
    <div class="container">
      <h1 class="logo"><a>SportsPro Admin</a></h1>
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
  <section class="card">
    <h1>Manage Customers</h1>
    <?php if ($error): ?><div class="error"><?=htmlspecialchars($error)?></div><?php endif;?>
    <?php if($action==='create'||$action==='edit'): ?>
      <form method="post" novalidate>
        <div class="form-group"><label for="username">Username</label><input id="username" name="username" type="text" required value="<?=htmlspecialchars($username)?>"></div>
        <div class="form-group"><label for="fullname">Full Name</label><input id="fullname" name="fullname" type="text" value="<?=htmlspecialchars($fullname)?>"></div>
        <div class="form-group"><label for="email">Email</label><input id="email" name="email" type="email" required value="<?=htmlspecialchars($email)?>"></div>
        <div class="form-group"><label for="password"><?= $action==='create'?'Password':'New Password (leave blank to keep)'?></label><input id="password" name="password" type="password" <?= $action==='create'?'required':''?>></div>
        <button type="submit" class="btn-primary"><?= $action==='create'?'Save Customer':'Update Customer'?></button>
        <a href="manage_customers.php" class="btn-secondary">Cancel</a>
      </form>
    <?php else: ?>
      <div class="actions">
        <a href="dashboard.php" class="btn">Back</a>
        <a href="manage_customers.php?action=create" class="btn">Add New Customer</a>
      </div>
      <?php if(empty($customers)): ?>
        <p>No customers found.</p>
      <?php else: ?>
        <table>
          <thead><tr><th>ID</th><th>Username</th><th>Full Name</th><th>Email</th><th>Created At</th><th>Actions</th></tr></thead>
          <tbody>
            <?php foreach($customers as $c): ?>
            <tr>
              <td><?=htmlspecialchars($c['id'])?></td>
              <td><?=htmlspecialchars($c['username'])?></td>
              <td><?=htmlspecialchars($c['fullname'])?></td>
              <td><?=htmlspecialchars($c['email'])?></td>
              <td><?=htmlspecialchars($c['created_at'])?></td>
              <td><a href="manage_customers.php?action=edit&id=<?=$c['id']?>" class="btn-secondary">Edit</a>
              <a href="manage_customers.php?action=delete&id=<?=$c['id']?>" class="btn-secondary" onclick="return confirm('Delete this customer?');">Delete</a></td>
            </tr>
            <?php endforeach;?>
          </tbody>
        </table>
      <?php endif;?>
    <?php endif;?>
  </section>
  </main>
</body>
</html>
