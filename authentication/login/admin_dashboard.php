<?php
session_start();

if (!isset($_SESSION['user_name']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <div class="login-card" style="max-width: 480px;">
    <div class="brand-header">
      <div class="brand-logo">🛡️</div>
      <h2>Admin Console</h2>
      <p class="subtitle"><?php echo $_SESSION['user_name']; ?></p>
      <div class="role-badge">Admin Clearance</div>
    </div>

    <div style="background:#f8fafc; padding:15px; border-radius:8px; border:1px solid #e2e8f0; margin-bottom:20px;">
      <p style="margin-bottom:8px;"><strong>Admin Code:</strong> <?php echo $_SESSION['user_code']; ?></p>
      <p style="margin-bottom:8px;"><strong>Department:</strong> <?php echo $_SESSION['department']; ?></p>
      <p style="margin-bottom:0; color:#16a34a;"><strong>Access:</strong> Full Access</p>
    </div>

    <div style="text-align:center;">
      <a href="logout.php" class="btn-submit" style="text-decoration:none; display:inline-block; background-color:#dc2626;">Sign Out</a>
    </div>
  </div>

</body>
</html>
