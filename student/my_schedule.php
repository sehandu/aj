<?php
session_start();
include '../db/db.php';

// Check student session
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'student') {
    header('Location: ../authentication/login/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch student's registered events
$result = $conn->query("SELECT e.*, er.registered_at 
                        FROM event_registrations er 
                        JOIN events e ON er.event_id = e.event_id 
                        WHERE er.user_id = $user_id 
                        ORDER BY e.event_date ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Event Schedule - NSBM EventHub</title>
    <link rel="stylesheet" href="../components/style.css">
    <link rel="stylesheet" href="../main.css">
    <style>
       
        .container { max-width: 950px; margin: 0 auto 50px; padding: 0 20px; }
        .card-box { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
        .badge { background: #d8f3dc; color: #006633; padding: 4px 10px; border-radius: 15px; font-size: 12px; font-weight: bold; }
        .btn-cancel { background: #dc2626; color: white; border: none; padding: 6px 12px; border-radius: 6px; font-weight: bold; font-size: 12px; cursor: pointer; display: inline-block; }
        .btn-cancel:hover { background: #b91c1c; }
    </style>
</head>
<body>
    <div class="page">

    <?php
    include '../components/navbar.php';
    ?>

    <section class="page-header">
        <h1>My Event Schedule</h1>
        <p><i>Events you are registered to attend</i></p>
    </section>

    <main class="container">
        

        <div class="card-box">
            <h2 style="color:#006633; margin-top:0; border-bottom:2px solid #eef2f0; padding-bottom:10px;">Registered Events</h2>

            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($event = $result->fetch_assoc()): ?>
                    <div style="border-bottom:1px solid #eee; padding:18px 0; display:flex; justify-content:space-between; align-items:center;">
                        <div>
                            <span class="badge"><?php echo $event['category']; ?></span>
                            <h3 style="margin:8px 0 6px 0; color:#222; font-size:18px;"><?php echo $event['event_title']; ?></h3>
                            <p style="margin:0 0 5px 0; color:#666; font-size:14px;">
                                <strong>Date:</strong> <?php echo $event['event_date']; ?> | 
                                <strong>Time:</strong> <?php echo $event['event_time']; ?> | 
                                <strong>Venue:</strong> <?php echo $event['event_venue']; ?>
                            </p>
                            <small style="color:#888;">Registered on <?php echo $event['registered_at']; ?></small>
                        </div>
                        <div>
                            <button type="button" class="btn-cancel" onclick="cancelRegistration(<?php echo $event['event_id']; ?>, '<?php echo addslashes($event['event_title']); ?>')">Cancel Registration</button>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="text-align:center; padding:40px 20px; color:#666;">
                    <p style="font-size:16px;">You haven't registered for any events yet.</p>
                    <a href="../event/event.php" style="background:#006633; color:white; padding:10px 20px; border-radius:6px; text-decoration:none; font-weight:bold; display:inline-block; margin-top:10px;">Browse Upcoming Events</a>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>
    <footer>
        <p>© 2026 NSBM EventHub | University Event Management System</p>
    </footer>

    <script>
        function cancelRegistration(eventId, eventTitle) {
            if (confirm("Cancel registration for " + eventTitle + "?")) {
                window.location.href = "cancel_registration.php?id=" + eventId;
            }
        }
    </script>
</body>
</html>

