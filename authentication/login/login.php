<?php
session_start();
include '../../db/db.php';

$error = '';
$msg = $_GET['msg'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $password = trim($_POST['password'] ?? '');

  if (empty($email) || empty($password)) {
    $error = 'Please fill in all required fields.';
  } else {
    $result = $conn->query("SELECT * FROM users WHERE email = '$email'");

    if ($result && $result->num_rows > 0) {
      $user = $result->fetch_assoc();

      $passMatches = password_verify($password, $user['encripted_password']);

      if ($passMatches) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['logged_in'] = true;

        if ($user['role'] === 'admin') {
          header('Location: ../../admin/eventhub_dashboard.php');
        } else {
          header('Location: ../../student/student_dashboard.php');
        }
        exit();
      }
    }
    $error = 'Invalid email or password!';
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Portal Login - NSBM EventHub</title>
  <link rel="stylesheet" href="style.css">
  <script src="login.js" defer></script>
</head>
<body>

  <div class="login-card">
    <div class="brand-header">
      <div class="brand-logo">🎓</div>
      <h2>Portal Login</h2>
      <p class="subtitle">Sign in to continue to NSBM EventHub</p>
    </div>

    <!-- Error Message -->
    <?php if (!empty($error)): ?>
      <div style="background:#fee2e2; color:#991b1b; padding:10px; border-radius:8px; margin-bottom:15px; text-align:center; font-size:14px;">
        <?php echo htmlspecialchars($error); ?>
      </div>
    <?php endif; ?>

    <!-- Success Message -->
    <?php if (!empty($msg)): ?>
      <div style="background:#dcfce7; color:#166534; padding:10px; border-radius:8px; margin-bottom:15px; text-align:center; font-size:14px;">
        <?php echo htmlspecialchars($msg); ?>
      </div>
    <?php endif; ?>

    <form action="login.php" method="POST" onsubmit="return validateLoginForm();">
      

      <div class="form-group">
        <label id="userLabel">Student Email or ID</label>
        <input type="text" name="email" id="email" placeholder="student@students.nsbm.ac.lk" required style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:8px;">
      </div>

      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" id="password" placeholder="••••••••" required style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:8px;">
      </div>

      <button type="submit" name="login" class="btn-submit">Sign In</button>
    </form>
    
    <p style="text-align:center; margin-top:20px; font-size:14px; color:#64748b;">
      Don't have a student account? <a href="../register/index.html" style="color:#006633; text-decoration:none; font-weight:bold;">Register Here</a>
    </p>
  </div>

</body>
</html>

