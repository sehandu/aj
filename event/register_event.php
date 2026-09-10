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
$event_id = intval($_GET['id'] ?? 0);

if ($event_id <= 0) {
    $status = 'error';
    $message = 'Invalid event selected.';
} else {
    // 1. Get event details
    $event_res = $conn->query("SELECT * FROM events WHERE event_id = $event_id");
    $event = $event_res ? $event_res->fetch_assoc() : null;

    if (!$event) {
        $status = 'error';
        $message = 'Event not found.';
    } else {
        // 2. Check registration capacity
        $count_res = $conn->query("SELECT COUNT(*) AS total FROM event_registrations WHERE event_id = $event_id");
        $reg_count = $count_res ? $count_res->fetch_assoc()['total'] : 0;

        if ($reg_count >= $event['max_participants']) {
            $status = 'error';
            $message = 'Registration is full for "' . $event['event_title'] . '".';
        } else {
            // 3. Check if already registered
            $check_res = $conn->query("SELECT * FROM event_registrations WHERE event_id = $event_id AND user_id = $user_id");
            if ($check_res && $check_res->num_rows > 0) {
                $status = 'info';
                $message = 'You are already registered for "' . $event['event_title'] . '".';
            } else {
                // 4. Insert registration
                if ($conn->query("INSERT INTO event_registrations (event_id, user_id) VALUES ($event_id, $user_id)")) {
                    $status = 'success';
                    $message = 'Successfully registered for "' . $event['event_title'] . '"!';
                } else {
                    $status = 'error';
                    $message = 'Failed to register. Please try again.';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration Status - NSBM EventHub</title>
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
        .info-icon { background: #e0f2fe; color: #0369a1; }
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
                <h2 class="msg-title">Registration Successful</h2>
                <p class="msg-text"><?php echo htmlspecialchars($message); ?></p>
            <?php elseif ($status === 'info'): ?>
                <div class="icon-circle info-icon">ℹ</div>
                <h2 class="msg-title">Already Registered</h2>
                <p class="msg-text"><?php echo htmlspecialchars($message); ?></p>
            <?php else: ?>
                <div class="icon-circle error-icon">✕</div>
                <h2 class="msg-title">Registration Failed</h2>
                <p class="msg-text"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>

            <div class="btn-group">
                <a href="../student/my_schedule.php" class="btn-primary">View My Schedule</a>
                <a href="event.php" class="btn-secondary">Browse More Events</a>
            </div>
        </div>

       
    </div>
     <footer>
            <p>© 2026 NSBM EventHub | University Event Management System</p>
        </footer>
</body>
</html>


