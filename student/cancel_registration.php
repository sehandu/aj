<?php
session_start();
include '../db/db.php';

$status = '';
$message = '';

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'student') {
    header('Location: ../authentication/login/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$event_id = intval($_GET['id'] ?? $_POST['event_id'] ?? 0);

if ($event_id <= 0) {
    $status = 'error';
    $message = 'Invalid event selected.';
} else {
    // Delete registration
    $conn->query("DELETE FROM event_registrations WHERE event_id = $event_id AND user_id = $user_id");

    if ($conn->affected_rows > 0) {
        $status = 'success';
        $message = 'Registration was successfully cancelled.';
    } else {
        $status = 'error';
        $message = 'No active registration found to cancel for this event.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cancellation Status - NSBM EventHub</title>
    <link rel="stylesheet" href="../components/style.css">
    <link rel="stylesheet" href="../main.css">
    <style>
        .msg-container {
            max-width: 550px;
            margin: 60px auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            text-align: center;
        }
        .icon-circle {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            margin: 0 auto 20px;
        }
        .success-icon { background: #dcfce7; color: #166534; }
        .error-icon { background: #fee2e2; color: #991b1b; }
        .msg-title { font-size: 22px; margin-bottom: 10px; color: #222; }
        .msg-text { font-size: 16px; color: #555; margin-bottom: 25px; line-height: 1.5; }
        .btn-group { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
        .btn-primary { background: #006633; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: bold; }
        .btn-primary:hover { background: #004d26; }
        .btn-secondary { background: #e2e8f0; color: #334155; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: bold; }
        .btn-secondary:hover { background: #cbd5e1; }
    </style>
</head>
<body>
    <div class="page">
        <?php include '../components/navbar.php'; ?>

        <div class="msg-container">
            <?php if ($status === 'success'): ?>
                <div class="icon-circle success-icon">✓</div>
                <h2 class="msg-title">Registration Cancelled</h2>
                <p class="msg-text"><?php echo $message; ?></p>
            <?php else: ?>
                <div class="icon-circle error-icon">✕</div>
                <h2 class="msg-title">Cancellation Failed</h2>
                <p class="msg-text"><?php echo $message; ?></p>
            <?php endif; ?>

            <div class="btn-group">
                <a href="my_schedule.php" class="btn-primary">Back to My Schedule</a>
                <a href="../event/event.php" class="btn-secondary">Browse Events</a>
            </div>
        </div>

        <footer>
            <p>© 2026 NSBM EventHub | University Event Management System</p>
        </footer>
    </div>
</body>
</html>
