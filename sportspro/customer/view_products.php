<?php
// customer/view_products.php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header('Location: login.php');
    exit;
}
require_once('../config/database.php');

$customer_id = $_SESSION['customer_id'];

// Fetch list of already registered product IDs for this customer
$stmt = $pdo->prepare('SELECT product_id FROM registrations WHERE customer_id = ?');
$stmt->execute([$customer_id]);
$registered = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Fetch all products
$stmt = $pdo->query('SELECT id, name, price FROM products');
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Available Products</title>
</head>
<body>
  <h2>Available Products</h2>
  <table cellpadding="5" cellspacing="0">
    <thead>
      <tr>
        <th>ID</th><th>Name</th><th>Price</th><th>Status</th><th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($products as $prod): ?>
        <tr>
          <td><?= $prod['id'] ?></td>
          <td><?= htmlspecialchars($prod['name']) ?></td>
          <td><?= $prod['price'] ?></td>
          <td>
            <?= in_array($prod['id'], $registered) ? 'Registered' : 'Not Registered' ?>
          </td>
          <td>
            <?php if (!in_array($prod['id'], $registered)): ?>
              <a href="register_product.php?id=<?= $prod['id'] ?>">Register</a>
            <?php else: ?>
              &mdash;
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <p><a href="report_incident.php">Report an Incident</a></p>
  <p><a href="logout.php">Log Out</a></p>
</body>
</html>
