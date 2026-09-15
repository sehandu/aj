<?php
session_start();
include '../../db/db.php';

$error = '';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $fullName = trim($_POST['fullName'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  $confirmPass = $_POST['confirmPassword'] ?? '';

  if (empty($fullName) || empty($email) || empty($password) || empty($confirmPass)) {
    $error = 'Please fill in all required fields.';
  } elseif ($password !== $confirmPass) {
    $error = 'Passwords do not match.';
  } elseif (strlen($password) < 6) {
    $error = 'Password must be at least 6 characters long.';
  } else {
    $checkQuery = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($checkQuery);

    if ($result && $result->num_rows > 0) {
      $error = 'Email address is already registered.';
    } else {
      $encryptedPassword = password_hash($password, PASSWORD_DEFAULT);
      $sql = "INSERT INTO users (full_name, encripted_password, email, role) 
                      VALUES ('$fullName', '$encryptedPassword', '$email', 'student')";

      if ($conn->query($sql) === TRUE) {
        header('Location: ../login/login.php');
        exit();
      } else {
        $error = 'Database Error: ' . $conn->error;
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - NSBM EventHub</title>
  <link rel="stylesheet" href="style.css">
  <script src="index.js" defer></script>
</head>
<body>
  <div class="register-card">
    <h2>Register</h2>
    <p class="subtitle">Create your account below</p>

    <?php if (!empty($error)): ?>
      <div style="background:#fee2e2; color:#991b1b; padding:10px; border-radius:8px; margin-bottom:15px; text-align:center; font-size:14px;">
        <?php echo $error; ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($msg)): ?>
      <div style="background:#dcfce7; color:#166534; padding:10px; border-radius:8px; margin-bottom:15px; text-align:center; font-size:14px;">
        <?php echo $msg; ?>
      </div>
    <?php endif; ?>

    <form id="registerForm" action="index.php" method="POST" enctype="multipart/form-data" onsubmit="return register(event)">
      
      <div class="form-group">
        <label for="fullName">Full Name</label>
        <input type="text" id="fullName" name="fullName" placeholder="Enter your full name" required>
      </div>

      <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" placeholder="Enter your email" required>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Enter password" required>
      </div>

      <div class="form-group">
        <label for="confirmPassword">Confirm Password</label>
        <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Re-enter password" required>
      </div>

      <button type="submit" class="btn-submit">Register</button>
    </form>

    <p class="login-link">
      Already have an account? <a href="../login/login.php">Sign in</a>
    </p>
  </div>
</body>
</html>
