<?php
// technician/incidents.php
session_start();
if (!isset($_SESSION['tech_id'])) {
    header('Location: login.php');
    exit;
}
require_once('../config/database.php');

$tech_id = $_SESSION['tech_id'];
$stmt = $pdo->prepare('
  SELECT i.id, i.description, i.date_reported, i.date_closed, i.status,
         c.fullname AS customer_name, p.name AS product_name
  FROM incidents i
  JOIN customers c ON i.customer_id = c.id
  JOIN products p ON i.product_id = p.id
  WHERE i.technician_id = ?
  ORDER BY i.date_reported DESC
');
$stmt->execute([$tech_id]);
$incidents = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Assigned Incidents</title>
</head>
<body>
  <h2>My Assigned Incidents</h2>
  <table cellpadding="5" cellspacing="0">
    <thead>
      <tr>
        <th>ID</th><th>Product</th><th>Customer</th><th>Description</th>
        <th>Date Reported</th><th>Status</th><th>Date Closed</th><th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($incidents as $inc): ?>
        <tr>
          <td><?= $inc['id'] ?></td>
          <td><?= htmlspecialchars($inc['product_name']) ?></td>
          <td><?= htmlspecialchars($inc['customer_name']) ?></td>
          <td><?= htmlspecialchars($inc['description']) ?></td>
          <td><?= $inc['date_reported'] ?></td>
          <td><?= htmlspecialchars($inc['status']) ?></td>
          <td><?= $inc['date_closed'] ?: '—' ?></td>
          <td>
            <a href="update_incident.php?id=<?= $inc['id'] ?>">Update</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <p><a href="logout.php">Log Out</a></p>
</body>
</html>
