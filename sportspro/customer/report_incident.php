<?php
// customer/report_incident.php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header('Location: login.php');
    exit;
}
require_once('../config/database.php');

$customer_id = $_SESSION['customer_id'];

// Fetch products registered to this customer
$stmt = $pdo->prepare('
    SELECT p.id, p.name
    FROM products p
    JOIN registrations r ON p.id = r.product_id
    WHERE r.customer_id = ?
');
$stmt->execute([$customer_id]);
$myProducts = $stmt->fetchAll();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = intval($_POST['product_id'] ?? 0);
    $description = trim($_POST['description'] ?? '');

    if ($product_id <= 0 || $description === '') {
        $error = 'Please choose a product and enter a description.';
    } else {
        $stmt = $pdo->prepare('INSERT INTO incidents (customer_id, product_id, description, status) VALUES (?, ?, ?, \'open\')');
        $stmt->execute([$customer_id, $product_id, $description]);
        header('Location: view_products.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Report an Incident</title>
</head>
<body>
  <h2>Report an Incident</h2>
  <?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>
  <form method="post" action="">
    <label>Product:
      <select name="product_id">
        <?php foreach ($myProducts as $mp): ?>
          <option value="<?= $mp['id'] ?>">
            <?= htmlspecialchars($mp['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label><br>
    <label>Description:
      <textarea name="description"><?= htmlspecialchars($description ?? '') ?></textarea>
    </label><br>
    <button type="submit">Submit Incident</button>
  </form>
  <p><a href="view_products.php">Back to Product List</a></p>
</body>
</html>
