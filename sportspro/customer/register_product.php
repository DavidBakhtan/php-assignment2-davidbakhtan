<?php
// customer/register_product.php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header('Location: login.php');
    exit;
}
require_once('../config/database.php');

$customer_id = $_SESSION['customer_id'];
$product_id = intval($_GET['id'] ?? 0);

// Check if already registered
$stmt = $pdo->prepare('SELECT COUNT(*) FROM registrations WHERE customer_id = ? AND product_id = ?');
$stmt->execute([$customer_id, $product_id]);
$already = $stmt->fetchColumn();

if ($already) {
    echo 'This product is already registered to your account.';
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serial = trim($_POST['serial'] ?? '');
    if ($serial === '') {
        $error = 'Please enter a valid serial number.';
    } else {
        $stmt = $pdo->prepare('INSERT INTO registrations (customer_id, product_id, serial_number) VALUES (?, ?, ?)');
        $stmt->execute([$customer_id, $product_id, $serial]);
        header('Location: view_products.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register Product #<?= $product_id ?></title>
</head>
<body>
  <h2>Register Product #<?= $product_id ?></h2>
  <?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>
  <form method="post" action="">
    <label>Serial Number:
      <input type="text" name="serial" value="<?= htmlspecialchars($serial ?? '') ?>">
    </label><br>
    <button type="submit">Register Product</button>
  </form>
  <p><a href="view_products.php">Back to Product List</a></p>
</body>
</html>
