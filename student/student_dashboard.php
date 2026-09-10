<?php
session_start();
include '../db/db.php';

// Check student session
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'student') {
    header('Location: ../authentication/login/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Get 4 upcoming active events
$upcoming_res = $conn->query('SELECT * FROM events ORDER BY event_date ASC LIMIT 4');

// Get announcements
$announcements_res = $conn->query('SELECT * FROM announcements ORDER BY created_at DESC');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - NSBM EventHub</title>
    <link rel="stylesheet" href="../components/style.css">
    <link rel="stylesheet" href="../main.css">
    <link rel="stylesheet" href="student.css">
</head>
<body>
    <div class="page">

    <?php include '../components/navbar.php'; ?>

    <section class="page-header">
        <h1>Welcome, <?php echo htmlspecialchars($user_name); ?></h1>
        <p><i>Student Portal</i></p>
    </section>

    <main class="dashboard-container ">
        
        <div class="dashboard-grid">
            <!-- Left Column: Upcoming Events (More Space) -->
            <div class="card-panel">
                <div class="panel-title">
                    <h2>Upcoming Events</h2>
                    <a href="../event/event.php">Browse All Events →</a>
                </div>

                <div class="events-grid">
                    <?php if ($upcoming_res && $upcoming_res->num_rows > 0): ?>
                        <?php while ($up = $upcoming_res->fetch_assoc()): ?>
                            <div class="minimal-event-card">
                               
                                    <div class="card-img">
                                        <img src="../uploads/<?php echo $up['image']; ?>" alt="<?php echo $up['event_title']; ?>">
                                    </div>
                               
                                <div class="card-body">
                                    <span class="badge"><?php echo $up['category']; ?></span>
                                    <h3><?php echo $up['event_title']; ?></h3>
                                    <div class="event-meta">
                                        <span>📅 <?php echo $up['event_date']; ?></span>
                                        <?php if (!empty($up['event_time'])): ?>
                                            <span>⏰ <?php echo $up['event_time']; ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($up['event_venue'])): ?>
                                            <span>📍 <?php echo $up['event_venue']; ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="no-data">No upcoming events available.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Column: Announcements -->
            <div class="card-panel">
                <div class="panel-title">
                    <h2>Announcements</h2>
                    <a href="announcements.php">View All →</a>
                </div>

                <div class="announcements-list">
                    <?php if ($announcements_res && $announcements_res->num_rows > 0): ?>
                        <?php while ($ann = $announcements_res->fetch_assoc()): ?>
                            <div class="announcement-item">
                                <div class="announcement-header">
                                    <h3><?php echo $ann['title']; ?></h3>
                                    <div class="">
                                         <span class="announcement-date"><?php echo date('M d, Y', strtotime($ann['created_at'])); ?></span>
                                         <br>
                                    <span class="announcement-date"><?php echo date('H:i A', strtotime($ann['created_at'])); ?></span>
                                    </div>
                                   
                                </div>
                                <p><?php echo $ann['text']; ?></p>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="no-data">No announcements posted.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </main>
</div>
    <footer>
        <p>© 2026 NSBM EventHub | University Event Management System</p>
    </footer>

</body>
</html>


