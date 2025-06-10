<?php
// File: admin/manage_products.php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/../config/database.php';

$action = $_GET['action'] ?? '';
$error  = '';
$name        = '';
$description = '';
$price       = '';

// Handle Create
if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price       = trim($_POST['price'] ?? '');
    if ($name === '' || !is_numeric($price) || $price <= 0) {
        $error = 'Please enter a valid product name and price.';
    } else {
        $stmt = $pdo->prepare(
            'INSERT INTO products (name, description, price) VALUES (:name, :description, :price)'
        );
        $stmt->execute([':name' => $name, ':description' => $description, ':price' => $price]);
        header('Location: manage_products.php'); exit;
    }
}
// Handle Edit
elseif ($action === 'edit') {
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$id]);
    $prod = $stmt->fetch();
    if (!$prod) { header('Location: manage_products.php'); exit; }
    $name        = $prod['name'];
    $description = $prod['description'];
    $price       = $prod['price'];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name        = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price       = trim($_POST['price'] ?? '');
        if ($name === '' || !is_numeric($price) || $price <= 0) {
            $error = 'Please enter a valid product name and price.';
        } else {
            $stmt = $pdo->prepare(
                'UPDATE products SET name=:name, description=:description, price=:price WHERE id=:id'
            );
            $stmt->execute([':name'=>$name,':description'=>$description,':price'=>$price,':id'=>$id]);
            header('Location: manage_products.php'); exit;
        }
    }
}
// Handle Delete
elseif ($action === 'delete') {
    $id = (int)($_GET['id'] ?? 0);
    $pdo->prepare('DELETE FROM products WHERE id = ?')->execute([$id]);
    header('Location: manage_products.php'); exit;
}

// Fetch products list
$stmt = $pdo->query('SELECT id, name, description, price FROM products ORDER BY id ASC');
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manage Products</title>
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
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family: 'Poppins', sans-serif;
      background: var(--bg-gradient);
      min-height: 100vh;
      display: flex;
      flex-direction: column;}
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
    .card{background:var(--card-bg);backdrop-filter:blur(12px);border-radius:16px;width:100%;max-width:900px;box-shadow:0 8px 32px rgba(0,0,0,0.1);animation:fadeIn 0.8s ease-out;padding:2rem;}
    h1{text-align:center;margin-bottom:1.5rem;font-size:2rem}
    .actions{margin-bottom:1rem}
    .actions .btn{background:var(--primary);color:#fff;padding:0.5rem 1rem;text-decoration:none;border-radius:8px;transition:background0.2s}
    .actions .btn:hover{background:var(--secondary)}
    .error{background:var(--error-bg);padding:0.75rem;border-radius:6px;margin-bottom:1rem;color:#dc3545}
    form{margin-bottom:2rem}
    .form-group{margin-bottom:1rem}
    .form-group label{display:block;font-weight:600;margin-bottom:0.25rem}
    .form-group input,.form-group textarea{width:100%;padding:0.75rem;border:1px solid#ccc;border-radius:8px}
    .btn-primary{background:var(--primary);color:#fff;padding:0.75rem 1rem;border:none;border-radius:8px;cursor:pointer;transition:background0.2s}
    .btn-primary:hover{background:var(--secondary)}
    .btn-secondary{background:var(--btn-secondary);color:#fff;text-decoration:none;padding:0.75rem 1rem;border-radius:8px;display:inline-block;transition:background0.2s}
    .btn-secondary:hover{background:#5a6268}
    table{width:100%;border-collapse:collapse}
    th,td{padding:0.75rem 1rem;border-bottom:1px solid#ddd}
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
    <h1>Manage Products</h1>
    <?php if ($error): ?><div class="error"><?=htmlspecialchars($error)?></div><?php endif;?>
    <?php if($action==='create'||$action==='edit'): ?>
      <form method="post" novalidate>
        <div class="form-group"><label for="name">Product Name</label><input id="name" name="name" type="text" required value="<?=htmlspecialchars($name)?>"></div>
        <div class="form-group"><label for="description">Description</label><textarea id="description" name="description"><?=htmlspecialchars($description)?></textarea></div>
        <div class="form-group"><label for="price">Price</label><input id="price" name="price" type="number" step="0.01" required value="<?=htmlspecialchars($price)?>"></div>
        <button type="submit" class="btn-primary"><?= $action==='create'?'Save Product':'Update Product'?></button>
        <a href="manage_products.php" class="btn-secondary">Cancel</a>
      </form>
    <?php else: ?>
      <div class="actions">
        <a href="dashboard.php" class="btn">Back</a>
        <a href="manage_products.php?action=create" class="btn">Add New Product</a>
      </div>
      <?php if(empty($products)): ?>
        <p>No products found.</p>
      <?php else: ?>
        <table>
          <thead><tr><th>ID</th><th>Name</th><th>Description</th><th>Price</th><th>Actions</th></tr></thead>
          <tbody>
            <?php foreach($products as $p): ?>
            <tr>
              <td><?=htmlspecialchars($p['id'])?></td>
              <td><?=htmlspecialchars($p['name'])?></td>
              <td><?=nl2br(htmlspecialchars($p['description']))?></td>
              <td>$<?=number_format($p['price'],2)?></td>
              <td>
                <a href="manage_products.php?action=edit&id=<?=$p['id']?>" class="btn-secondary">Edit</a>
                <a href="manage_products.php?action=delete&id=<?=$p['id']?>" class="btn-secondary" onclick="return confirm('Delete this product?');">Delete</a>
              </td>
            </tr>
            <?php endforeach;?>
          </tbody>
        </table>
      <?php endif; ?>
    <?php endif; ?>
  </section>
  </main>
</body>
</html>