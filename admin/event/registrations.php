<?php
session_start();
include '../../db/db.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../authentication/login/login.php");
    exit();
}

// Fetch all event registrations with user and event details
$query = "SELECT er.registration_id, er.registered_at, e.event_title, e.category, e.event_date, u.full_name, u.email 
          FROM event_registrations er 
          JOIN events e ON er.event_id = e.event_id 
          JOIN users u ON er.user_id = u.user_id 
          ORDER BY er.registered_at DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Registrations - NSBM EventHub Admin</title>
    <link rel="stylesheet" href="../../components/style.css">
    <link rel="stylesheet" href="../../main.css">
    <link rel="stylesheet" href="event.css">
</head>
<body>
    <div class="page">
        <?php include '../../components/admin_navbar.php'; ?>

        <section class="page-header">
            <h1>Student Event Registrations</h1>
            <p><i>View all student event registrations in real time</i></p>
        </section>

        <main style="max-width: 1100px; margin: 0 auto 40px; padding: 0 20px;">
            <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                    <h3 style="color:#006633; margin:0;">All Event Registrations</h3>
                    <a href="participants.php" style="background:#006633; color:white; padding:8px 16px; border-radius:6px; text-decoration:none; font-weight:bold; font-size:14px;">Generate Participant List per Event →</a>
                </div>

                <table style="width:100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background:#f1f5f9; text-align:left; border-bottom:2px solid #e2e8f0;">
                            <th style="padding:12px;">Reg ID</th>
                            <th style="padding:12px;">Student Name</th>
                            <th style="padding:12px;">Student Email</th>
                            <th style="padding:12px;">Event Title</th>
                            <th style="padding:12px;">Category</th>
                            <th style="padding:12px;">Event Date</th>
                            <th style="padding:12px;">Registration Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr style="border-bottom:1px solid #e2e8f0;">
                                    <td style="padding:12px;"><?php echo $row['registration_id']; ?></td>
                                    <td style="padding:12px; font-weight:bold;"><?php echo htmlspecialchars($row['full_name']); ?></td>
                                    <td style="padding:12px; color:#2563eb;"><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td style="padding:12px; font-weight:bold; color:#0f172a;"><?php echo htmlspecialchars($row['event_title']); ?></td>
                                    <td style="padding:12px;"><span style="background:#d8f3dc; color:#006633; padding:4px 8px; border-radius:4px; font-size:12px; font-weight:bold;"><?php echo htmlspecialchars($row['category']); ?></span></td>
                                    <td style="padding:12px;"><?php echo htmlspecialchars($row['event_date']); ?></td>
                                    <td style="padding:12px; color:#64748b; font-size:13px;"><?php echo date('M d, Y H:i', strtotime($row['registered_at'])); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="padding:30px; text-align:center; color:#94a3b8;">No event registrations found yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <footer>
        <p>© 2026 NSBM EventHub | University Event Management System</p>
    </footer>
</body>
</html>
