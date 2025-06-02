<?php
// admin/manage_technicians.php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
require_once('../config/database.php');

$action = $_GET['action'] ?? '';
$error = '';

if ($action === 'create') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $fullname = trim($_POST['fullname'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $error = 'Please enter a username and password.';
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO technicians (username, password_hash, fullname) VALUES (?, ?, ?)');
            $stmt->execute([$username, $passwordHash, $fullname]);
            header('Location: manage_technicians.php');
            exit;
        }
    }
} elseif ($action === 'edit') {
    $id = intval($_GET['id'] ?? 0);
    $stmt = $pdo->prepare('SELECT * FROM technicians WHERE id = ?');
    $stmt->execute([$id]);
    $technician = $stmt->fetch();

    if (!$technician) {
        echo 'Technician not found.';
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $fullname = trim($_POST['fullname'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '') {
            $error = 'Username cannot be empty.';
        } else {
            if ($password !== '') {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('UPDATE technicians SET username = ?, fullname = ?, password_hash = ? WHERE id = ?');
                $stmt->execute([$username, $fullname, $passwordHash, $id]);
            } else {
                $stmt = $pdo->prepare('UPDATE technicians SET username = ?, fullname = ? WHERE id = ?');
                $stmt->execute([$username, $fullname, $id]);
            }
            header('Location: manage_technicians.php');
            exit;
        }
    }
} elseif ($action === 'delete') {
    $id = intval($_GET['id'] ?? 0);
    $stmt = $pdo->prepare('DELETE FROM technicians WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: manage_technicians.php');
    exit;
}

// Fetch all technicians
$stmt = $pdo->query('SELECT id, username, fullname, created_at FROM technicians');
$technicians = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Technicians</title>
</head>
<body>
  <h2>Technicians</h2>
  <a href="manage_technicians.php?action=create">Add New Technician</a>
  <?php if ($action === 'create'): ?>
    <h3>Add Technician</h3>
    <?php if ($error): ?>
      <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="post" action="">
      <label>Username:
        <input type="text" name="username" value="<?= htmlspecialchars($username ?? '') ?>">
      </label><br>
      <label>Full Name:
        <input type="text" name="fullname" value="<?= htmlspecialchars($fullname ?? '') ?>">
      </label><br>
      <label>Password:
        <input type="password" name="password">
      </label><br>
      <button type="submit">Save</button>
    </form>
    <p><a href="manage_technicians.php">Back to Technician List</a></p>

  <?php elseif ($action === 'edit'): ?>
    <h3>Edit Technician #<?= $technician['id'] ?></h3>
    <?php if ($error): ?>
      <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="post" action="">
      <label>Username:
        <input type="text" name="username" value="<?= htmlspecialchars($technician['username']) ?>">
      </label><br>
      <label>Full Name:
        <input type="text" name="fullname" value="<?= htmlspecialchars($technician['fullname']) ?>">
      </label><br>
      <label>New Password (leave blank to keep current):
        <input type="password" name="password">
      </label><br>
      <button type="submit">Update</button>
    </form>
    <p><a href="manage_technicians.php">Back to Technician List</a></p>
  <?php else: ?>
    <table cellpadding="5" cellspacing="0">
      <thead>
        <tr>
          <th>ID</th><th>Username</th><th>Full Name</th><th>Created At</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($technicians as $tech): ?>
          <tr>
            <td><?= $tech['id'] ?></td>
            <td><?= htmlspecialchars($tech['username']) ?></td>
            <td><?= htmlspecialchars($tech['fullname']) ?></td>
            <td><?= $tech['created_at'] ?></td>
            <td>
              <a href="manage_technicians.php?action=edit&id=<?= $tech['id'] ?>">Edit</a> |
              <a href="manage_technicians.php?action=delete&id=<?= $tech['id'] ?>"
                 onclick="return confirm('Are you sure you want to delete this technician?');">
                Delete
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
  <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>
