<?php
session_start();
include '../../db/db.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../authentication/login/login.php");
    exit();
}

$selected_event_id = intval($_GET['event_id'] ?? 0);

// Fetch all events for the dropdown selector
$events_result = $conn->query("SELECT event_id, event_title, event_date FROM events ORDER BY event_date DESC");

// Fetch participants if an event is selected
$participants = [];
$event_details = null;

if ($selected_event_id > 0) {
    $ev_res = $conn->query("SELECT * FROM events WHERE event_id = $selected_event_id");
    $event_details = $ev_res ? $ev_res->fetch_assoc() : null;

    $res = $conn->query("SELECT u.full_name, u.email, er.registered_at 
                         FROM event_registrations er 
                         JOIN users u ON er.user_id = u.user_id 
                         WHERE er.event_id = $selected_event_id 
                         ORDER BY u.full_name ASC");
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $participants[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Participant Lists - NSBM EventHub Admin</title>
    <link rel="stylesheet" href="../../components/style.css">
    <link rel="stylesheet" href="../../main.css">
    <link rel="stylesheet" href="event.css">
    <style>
        @media print {
            nav, .page-header, .no-print, footer { display: none !important; }
            body { background: white !important; }
            .print-container { box-shadow: none !important; margin: 0 !important; width: 100% !important; }
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="no-print">
            <?php include '../../components/admin_navbar.php'; ?>
        </div>

        <section class="page-header no-print">
            <h1>Generate Participant Lists</h1>
            <p><i>Select an event to view or print the list of registered participants</i></p>
        </section>

        <main style="max-width: 1000px; margin: 0 auto 40px; padding: 0 20px;">
            <div class="no-print" style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                <form method="GET" action="participants.php" style="display:flex; gap:15px; align-items:center; flex-wrap:wrap;">
                    <label style="font-weight:bold; color:#0f172a;">Select Event:</label>
                    <select name="event_id" style="flex:1; min-width:250px; padding:10px; border:1px solid #cbd5e1; border-radius:6px;" required>
                        <option value="">-- Choose an Event --</option>
                        <?php if ($events_result && $events_result->num_rows > 0): ?>
                            <?php while ($ev = $events_result->fetch_assoc()): ?>
                                <option value="<?php echo $ev['event_id']; ?>" <?php if ($selected_event_id == $ev['event_id']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($ev['event_title']) . " (" . $ev['event_date'] . ")"; ?>
                                </option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                    <button type="submit" style="background:#006633; color:white; padding:10px 20px; border:none; border-radius:6px; font-weight:bold; cursor:pointer;">Generate List</button>
                </form>
            </div>

            <?php if ($event_details): ?>
                <div class="print-container" style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; border-bottom:2px solid #006633; padding-bottom:15px; margin-bottom:20px;">
                        <div>
                            <h2 style="margin:0 0 5px 0; color:#006633;"><?php echo htmlspecialchars($event_details['event_title']); ?></h2>
                            <p style="margin:0; color:#64748b;">
                                <strong>Date:</strong> <?php echo htmlspecialchars($event_details['event_date']); ?> | 
                                <strong>Time:</strong> <?php echo htmlspecialchars($event_details['event_time']); ?> | 
                                <strong>Venue:</strong> <?php echo htmlspecialchars($event_details['event_venue']); ?>
                            </p>
                        </div>
                        <button onclick="window.print()" class="no-print" style="background:#2563eb; color:white; padding:8px 16px; border:none; border-radius:6px; font-weight:bold; cursor:pointer;">🖨️ Print Participant List</button>
                    </div>

                    <p style="font-weight:bold; color:#334155; margin-bottom:15px;">
                        Total Registered Participants: <?php echo count($participants); ?> / <?php echo $event_details['max_participants']; ?>
                    </p>

                    <table style="width:100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background:#f1f5f9; text-align:left; border-bottom:2px solid #cbd5e1;">
                                <th style="padding:10px; width:50px;">#</th>
                                <th style="padding:10px;">Participant Name</th>
                                <th style="padding:10px;">Email Address</th>
                                <th style="padding:10px;">Registration Date</th>
                                <th style="padding:10px; width:120px;" class="no-print">Attendance Signature</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($participants) > 0): ?>
                                <?php foreach ($participants as $idx => $p): ?>
                                    <tr style="border-bottom:1px solid #e2e8f0;">
                                        <td style="padding:10px;"><?php echo $idx + 1; ?></td>
                                        <td style="padding:10px; font-weight:bold;"><?php echo htmlspecialchars($p['full_name']); ?></td>
                                        <td style="padding:10px; color:#2563eb;"><?php echo htmlspecialchars($p['email']); ?></td>
                                        <td style="padding:10px; color:#64748b;"><?php echo date('M d, Y H:i', strtotime($p['registered_at'])); ?></td>
                                        <td style="padding:10px;" class="no-print">_________________</td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" style="padding:25px; text-align:center; color:#94a3b8;">No registered participants for this event yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php elseif ($selected_event_id == 0): ?>
                <div style="background: white; padding: 40px; border-radius: 12px; text-align: center; color: #64748b; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                    <p style="font-size: 16px; margin: 0;">Please select an event above and click <strong>Generate List</strong> to view or print participants.</p>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <footer class="no-print">
        <p>© 2026 NSBM EventHub | University Event Management System</p>
    </footer>
</body>
</html>
