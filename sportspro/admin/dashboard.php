<?php
// admin/dashboard.php
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
  <title>Admin Dashboard</title>
</head>
<body>
  <h1>Admin Dashboard</h1>
  <ul>
    <li><a href="manage_products.php">Manage Products</a></li>
    <li><a href="manage_technicians.php">Manage Technicians</a></li>
    <li><a href="manage_customers.php">Manage Customers</a></li>
  </ul>
  <p><a href="logout.php">Log Out</a></p>
</body>
</html>
