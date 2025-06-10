<?php
// File: admin/dashboard.php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #0077ff;
      --secondary: #00d4ff;
      --bg-gradient: linear-gradient(135deg, #0077ff, #00d4ff);
      --card-bg: rgba(255, 255, 255, 0.85);
      --text-dark: #222;
      --header-bg: rgba(255, 255, 255, 0.1);
    }
    * { margin: 0; padding: 0; box-sizing: border-box; }
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
    .dashboard h2 {
      color: #fff;
      text-align: center;
      margin-bottom: 2rem;
      font-size: 2rem;
    }
    .dashboard-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1.5rem;
    }
    .card {
      background: var(--card-bg);
      backdrop-filter: blur(12px);
      border-radius: 16px;
      padding: 2rem;
      text-align: center;
      color: var(--text-dark);
      text-decoration: none;
      box-shadow: 0 8px 32px rgba(0,0,0,0.1);
      transition: transform 0.2s, background 0.2s;
      animation: fadeIn 0.8s ease-out;
    }
    .card h3 {
      font-size: 1.25rem;
      margin-bottom: 0.5rem;
    }
    .card:hover {
      transform: translateY(-5px);
      background: rgba(255,255,255,0.95);
    }
    .site-footer {
      text-align: center;
      padding: 1rem 0;
      color: #fff;
      font-size: 0.9rem;
    }
    .btn-primary { background: var(--primary); color:#fff; padding:0.75rem 1rem; border:none; border-radius:8px; cursor:pointer; transition:background 0.2s; }
    .btn-primary:hover { background: var(--secondary); }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
  <header class="site-header">
    <div class="container">
      <h1 class="logo"><a href="dashboard.php">SportsPro Admin</a></h1>
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
    <section class="dashboard">
      <h2>Welcome, Administrator</h2>
      <div class="dashboard-grid">
        <a href="manage_products.php" class="card">
          <h3>Manage Products</h3>
        </a>
        <a href="manage_technicians.php" class="card">
          <h3>Manage Technicians</h3>
        </a>
        <a href="manage_customers.php" class="card">
          <h3>Manage Customers</h3>
        </a>
      </div>
    </section>
  </main>
  <footer class="site-footer">
    &copy; <?= date('Y') ?> SportsPro Inc.
  </footer>
</body>
</html>
