<?php
// technician/update_incident.php
session_start();
if (!isset($_SESSION['tech_id'])) {
    header('Location: login.php');
    exit;
}
require_once('../config/database.php');

$tech_id = $_SESSION['tech_id'];
$incident_id = intval($_GET['id'] ?? 0);

// Verify this incident belongs to this technician
$stmt = $pdo->prepare('SELECT * FROM incidents WHERE id = ? AND technician_id = ?');
$stmt->execute([$incident_id, $tech_id]);
$incident = $stmt->fetch();

if (!$incident) {
    echo 'Incident not found or not assigned to you.';
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_status = $_POST['status'] ?? 'open';
    $date_closed = $_POST['date_closed'] ?: null;

    $stmt = $pdo->prepare('UPDATE incidents SET status = ?, date_closed = ? WHERE id = ?');
    $stmt->execute([$new_status, $date_closed, $incident_id]);
    header('Location: incidents.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Update Incident #<?= $incident_id ?></title>
</head>
<body>
  <h2>Update Incident #<?= $incident_id ?></h2>
  <?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>
  <form method="post" action="">
    <label>Status:
      <select name="status">
        <option value="open" <?= ($incident['status'] === 'open') ? 'selected' : '' ?>>Open</option>
        <option value="in_progress" <?= ($incident['status'] === 'in_progress') ? 'selected' : '' ?>>In Progress</option>
        <option value="closed" <?= ($incident['status'] === 'closed') ? 'selected' : '' ?>>Closed</option>
      </select>
    </label><br>
    <label>Date Closed:
      <input type="datetime-local" name="date_closed"
        value="<?= $incident['date_closed'] ? date('Y-m-d\TH:i', strtotime($incident['date_closed'])) : '' ?>">
    </label><br>
    <button type="submit">Save Updates</button>
  </form>
  <p><a href="incidents.php">Back to My Incidents</a></p>
</body>
</html>
