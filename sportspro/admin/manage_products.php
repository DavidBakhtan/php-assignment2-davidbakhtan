<?php
// admin/manage_products.php

session_start();

// 1) Security check: only allow access if admin is logged in.
//    (Assumes you set $_SESSION['admin_id'] when the admin logged in.)
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// 2) Include the PDO database connection
require_once(__DIR__ . '/../config/database.php');

$action = $_GET['action'] ?? '';
$error  = '';
$name       = '';
$description= '';
$price      = 0.00;

// 3) Handle "Create" action: show form and insert on POST
if ($action === 'create') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Trim and validate inputs
        $name        = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price       = floatval($_POST['price'] ?? 0);

        if ($name === '' || $price <= 0) {
            $error = 'Please enter a valid product name and price.';
        } else {
            // Use a prepared statement to insert into products
            $stmt = $pdo->prepare(
                'INSERT INTO products (name, description, price) VALUES (:name, :description, :price)'
            );
            $stmt->execute([
                ':name'        => $name,
                ':description' => $description,
                ':price'       => $price,
            ]);
            header('Location: manage_products.php');
            exit;
        }
    }
}

// 4) Handle "Edit" action: fetch existing data, then update on POST
elseif ($action === 'edit') {
    $id = intval($_GET['id'] ?? 0);

    // 4a) First, fetch current product data so we can prefill the form
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$id]);
    $prod = $stmt->fetch();

    if (!$prod) {
        exit('Product not found.');
    }

    $name        = $prod['name'];
    $description = $prod['description'];
    $price       = $prod['price'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name        = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price       = floatval($_POST['price'] ?? 0);

        if ($name === '' || $price <= 0) {
            $error = 'Please enter a valid product name and price.';
        } else {
            // Prepared statement to update the product
            $stmt = $pdo->prepare(
                'UPDATE products SET name = :name, description = :description, price = :price WHERE id = :id'
            );
            $stmt->execute([
                ':name'        => $name,
                ':description' => $description,
                ':price'       => $price,
                ':id'          => $id,
            ]);
            header('Location: manage_products.php');
            exit;
        }
    }
}

// 5) Handle "Delete" action: remove product and redirect
elseif ($action === 'delete') {
    $id = intval($_GET['id'] ?? 0);
    $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: manage_products.php');
    exit;
}

// 6) By default (no action or after create/edit/delete), fetch all products to list
$stmt = $pdo->query('SELECT id, name, description, price FROM products ORDER BY id ASC');
$products = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Products</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    th { background-color: #f4f4f4; }
    form { max-width: 400px; }
    label { display: block; margin-bottom: 8px; }
    input[type="text"], textarea, input[type="number"] {
      width: 100%; padding: 6px; margin-bottom: 12px; box-sizing: border-box;
    }
    .error { color: red; margin-bottom: 12px; }
    .actions a { margin-right: 8px; }
  </style>
</head>
<body>
  <h1>Admin → Manage Products</h1>

  <!-- Link to Add New Product -->
  <p>
    <a href="manage_products.php?action=create">+ Add New Product</a> |
    <a href="dashboard.php">&larr; Back to Dashboard</a>
  </p>

  <!-- If action is "create", show the Add Product form -->
  <?php if ($action === 'create'): ?>
    <h2>Add New Product</h2>
    <?php if ($error): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="post" action="">
      <label>
        Product Name:
        <input type="text" name="name" value="<?= htmlspecialchars($name) ?>">
      </label>
      <label>
        Description:
        <textarea name="description"><?= htmlspecialchars($description) ?></textarea>
      </label>
      <label>
        Price:
        <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($price) ?>">
      </label>
      <button type="submit">Save Product</button>
    </form>

  <!-- If action is "edit", show the Edit Product form -->
  <?php elseif ($action === 'edit'): ?>
    <h2>Edit Product #<?= htmlspecialchars($prod['id']) ?></h2>
    <?php if ($error): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="post" action="">
      <label>
        Product Name:
        <input type="text" name="name" value="<?= htmlspecialchars($name) ?>">
      </label>
      <label>
        Description:
        <textarea name="description"><?= htmlspecialchars($description) ?></textarea>
      </label>
      <label>
        Price:
        <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($price) ?>">
      </label>
      <button type="submit">Update Product</button>
    </form>
    <p><a href="manage_products.php">&larr; Back to Product List</a></p>

  <?php else: ?>
    <!-- Default: show the table of existing products -->
    <h2>Existing Products</h2>
    <?php if (empty($products)): ?>
      <p>No products found.</p>
    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>ID</th><th>Name</th><th>Description</th><th>Price</th><th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($products as $p): ?>
            <tr>
              <td><?= htmlspecialchars($p['id']) ?></td>
              <td><?= htmlspecialchars($p['name']) ?></td>
              <td><?= nl2br(htmlspecialchars($p['description'])) ?></td>
              <td>$<?= htmlspecialchars(number_format($p['price'], 2)) ?></td>
              <td class="actions">
                <a href="manage_products.php?action=edit&id=<?= $p['id'] ?>">Edit</a>
                <a href="manage_products.php?action=delete&id=<?= $p['id'] ?>"
                   onclick="return confirm('Are you sure you want to delete this product?');">
                  Delete
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  <?php endif; ?>

</body>
</html>
