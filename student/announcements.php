<?php
session_start();
include '../db/db.php';

// Check if user is logged in as student
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'student') {
    header('Location: ../authentication/login/login.php');
    exit();
}

// Fetch all announcements ordered by date
$result = $conn->query('SELECT * FROM announcements ORDER BY created_at DESC');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Campus Announcements - NSBM EventHub</title>
    <link rel="stylesheet" href="../components/style.css">
    <link rel="stylesheet" href="../main.css">
    <style>
        .container { max-width: 950px; margin: 0 auto 50px; padding: 0 20px; }
        .card-box { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
        .announcement-box { background: #f9fbf9; border-left: 4px solid #006633; padding: 18px; border-radius: 0 8px 8px 0; margin-bottom: 18px; }
    </style>
</head>
<body>

    <div class="page">
    <?php include '../components/navbar.php' ?>
    <section class="page-header">
        <h1>Campus Announcements</h1>
        <p><i>Official event notices and university updates</i></p>
    </section>

    <main class="container">
        <div class="card-box">
            <h2 style="color:#006633; margin-top:0; border-bottom:2px solid #eef2f0; padding-bottom:10px;">Latest Announcements</h2>

            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($ann = $result->fetch_assoc()): ?>
                    <div class="announcement-box">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                            <h3 style="margin:0; color:#222; font-size:17px;"><?php echo htmlspecialchars($ann['title']); ?></h3>
                            <span style="color:#006633; font-weight:bold; font-size:12px;">
                                <?php echo date('M d, Y', strtotime($ann['created_at'])); ?>
                            </span>
                        </div>
                        <p style="margin:0; color:#555; font-size:14px; line-height:1.6; white-space:pre-line;"><?php echo htmlspecialchars($ann['text']); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="text-align:center; padding:30px; color:#666;">
                    <p style="font-size:15px;">No announcements posted at this time.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>
    <footer>
        <p>© 2026 NSBM EventHub | University Event Management System</p>
    </footer>

</body>
</html>
