<?php
// admin/manage_customers.php
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
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '' || $email === '') {
            $error = 'Please fill in username, email, and password.';
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO customers (username, password_hash, fullname, email) VALUES (?, ?, ?, ?)');
            $stmt->execute([$username, $passwordHash, $fullname, $email]);
            header('Location: manage_customers.php');
            exit;
        }
    }
} elseif ($action === 'edit') {
    $id = intval($_GET['id'] ?? 0);
    $stmt = $pdo->prepare('SELECT * FROM customers WHERE id = ?');
    $stmt->execute([$id]);
    $customer = $stmt->fetch();

    if (!$customer) {
        echo 'Customer not found.';
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $fullname = trim($_POST['fullname'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $email === '') {
            $error = 'Username and email cannot be empty.';
        } else {
            if ($password !== '') {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('UPDATE customers SET username = ?, fullname = ?, email = ?, password_hash = ? WHERE id = ?');
                $stmt->execute([$username, $fullname, $email, $passwordHash, $id]);
            } else {
                $stmt = $pdo->prepare('UPDATE customers SET username = ?, fullname = ?, email = ? WHERE id = ?');
                $stmt->execute([$username, $fullname, $email, $id]);
            }
            header('Location: manage_customers.php');
            exit;
        }
    }
} elseif ($action === 'delete') {
    $id = intval($_GET['id'] ?? 0);
    $stmt = $pdo->prepare('DELETE FROM customers WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: manage_customers.php');
    exit;
}

// Fetch all customers
$stmt = $pdo->query('SELECT id, username, fullname, email, created_at FROM customers');
$customers = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Customers</title>
</head>
<body>
  <h2>Customers</h2>
  <a href="manage_customers.php?action=create">Add New Customer</a>
  <?php if ($action === 'create'): ?>
    <h3>Add Customer</h3>
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
      <label>Email:
        <input type="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>">
      </label><br>
      <label>Password:
        <input type="password" name="password">
      </label><br>
      <button type="submit">Save</button>
    </form>
    <p><a href="manage_customers.php">Back to Customer List</a></p>

  <?php elseif ($action === 'edit'): ?>
    <h3>Edit Customer #<?= $customer['id'] ?></h3>
    <?php if ($error): ?>
      <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="post" action="">
      <label>Username:
        <input type="text" name="username" value="<?= htmlspecialchars($customer['username']) ?>">
      </label><br>
      <label>Full Name:
        <input type="text" name="fullname" value="<?= htmlspecialchars($customer['fullname']) ?>">
      </label><br>
      <label>Email:
        <input type="email" name="email" value="<?= htmlspecialchars($customer['email']) ?>">
      </label><br>
      <label>New Password (leave blank to keep current):
        <input type="password" name="password">
      </label><br>
      <button type="submit">Update</button>
    </form>
    <p><a href="manage_customers.php">Back to Customer List</a></p>
  <?php else: ?>
    <table cellpadding="5" cellspacing="0">
      <thead>
        <tr>
          <th>ID</th><th>Username</th><th>Full Name</th><th>Email</th><th>Created At</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($customers as $cust): ?>
          <tr>
            <td><?= $cust['id'] ?></td>
            <td><?= htmlspecialchars($cust['username']) ?></td>
            <td><?= htmlspecialchars($cust['fullname']) ?></td>
            <td><?= htmlspecialchars($cust['email']) ?></td>
            <td><?= $cust['created_at'] ?></td>
            <td>
              <a href="manage_customers.php?action=edit&id=<?= $cust['id'] ?>">Edit</a> |
              <a href="manage_customers.php?action=delete&id=<?= $cust['id'] ?>"
                 onclick="return confirm('Are you sure you want to delete this customer?');">
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
