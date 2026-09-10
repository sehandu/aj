<?php
session_start();
include '../db/db.php';

// Check admin session
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../authentication/login/login.php");
    exit();
}

// Stats Queries
$total_events        = $conn->query("SELECT COUNT(*) FROM events")->fetch_row()[0] ?? 0;
$total_regs          = $conn->query("SELECT COUNT(*) FROM event_registrations")->fetch_row()[0] ?? 0;
$upcoming_events     = $conn->query("SELECT COUNT(*) FROM events WHERE event_date >= CURDATE()")->fetch_row()[0] ?? 0;
$total_announcements = $conn->query("SELECT COUNT(*) FROM announcements")->fetch_row()[0] ?? 0;

// Data Queries
$events_res = $conn->query("SELECT * FROM events ORDER BY event_date ASC LIMIT 5");
$regs_res   = $conn->query("SELECT er.registered_at, e.event_title, u.full_name 
                            FROM event_registrations er 
                            JOIN events e ON er.event_id = e.event_id 
                            JOIN users u ON er.user_id = u.user_id 
                            ORDER BY er.registered_at DESC LIMIT 5");
$ann_res    = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC LIMIT 4");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - NSBM EventHub</title>
    <link rel="stylesheet" href="../components/style.css">
    <link rel="stylesheet" href="../main.css">
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            display: flex;
            gap: 15px;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
        }
        .stat-icon {
            font-size: 26px;
            background: #d8f3dc;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            color: #006633;
        }
        .dashboard-grid, .bottom-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
            margin-bottom: 30px;
        }
        .panel {
            background: white;
            border-radius: 12px;
            padding: 25px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .primary-btn {
            background: #006633;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            cursor: pointer;
        }
        .primary-btn:hover {
            background: #004d26;
        }
        .quick-actions button {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 12px;
            text-align: left;
            padding: 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #f8fafc;
            margin-bottom: 10px;
            cursor: pointer;
            font-size: 14px;
            transition: 0.2s;
        }
        .quick-actions button:hover {
            background: #e2e8f0;
        }
        .event-row {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .date-box {
            background: #d8f3dc;
            padding: 8px 12px;
            border-radius: 8px;
            text-align: center;
            min-width: 50px;
        }
        @media (max-width: 768px) {
            .dashboard-grid, .bottom-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <?php include '../components/admin_navbar.php'; ?>

        <section class="page-header">
            <h1>Admin Dashboard</h1>
            <p><i>NSBM EventHub Administrator Portal</i></p>
        </section>

        <main style="max-width: 1100px; margin: 0 auto 50px; padding: 0 20px;">
            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">📅</div>
                    <div>
                        <p style="margin:0; color:#64748b; font-size:13px;">Total Events</p>
                        <h2 style="margin:4px 0; color:#0f172a;"><?= $total_events ?></h2>
                        <span style="color:#006633; font-size:12px;">Active system events</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <div>
                        <p style="margin:0; color:#64748b; font-size:13px;">Registrations</p>
                        <h2 style="margin:4px 0; color:#0f172a;"><?= $total_regs ?></h2>
                        <span style="color:#006633; font-size:12px;">Student registrations</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">✓</div>
                    <div>
                        <p style="margin:0; color:#64748b; font-size:13px;">Upcoming Events</p>
                        <h2 style="margin:4px 0; color:#0f172a;"><?= $upcoming_events ?></h2>
                        <span style="color:#006633; font-size:12px;">Next upcoming dates</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">📢</div>
                    <div>
                        <p style="margin:0; color:#64748b; font-size:13px;">Announcements</p>
                        <h2 style="margin:4px 0; color:#0f172a;"><?= $total_announcements ?></h2>
                        <span style="color:#64748b; font-size:12px;">Campus announcements</span>
                    </div>
                </div>
            </div>

            <!-- Main Dashboard Grid -->
            <div class="dashboard-grid">
                <section class="panel events-panel">
                    <div class="panel-header">
                        <div>
                            <h2 style="margin:0 0 4px 0; color:#0f172a;">Upcoming Events</h2>
                            <p style="margin:0; color:#64748b; font-size:13px;">Manage and monitor university events.</p>
                        </div>
                        <a href="event/upcomming.php" class="primary-btn">+ Create Event</a>
                    </div>

                    <div class="event-list">
                        <?php if ($events_res && $events_res->num_rows > 0): ?>
                            <?php while ($ev = $events_res->fetch_assoc()): 
                                $evDate = strtotime($ev['event_date']);
                                $day = date('d', $evDate);
                                $month = strtoupper(date('M', $evDate));
                            ?>
                                <div class="event-row">
                                    <div class="date-box">
                                        <strong style="display:block; color:#006633; font-size:16px;"><?= $day ?></strong>
                                        <span style="font-size:11px; color:#006633; font-weight:bold;"><?= $month ?></span>
                                    </div>
                                    <div style="flex:1;">
                                        <h4 style="margin:0 0 4px 0; color:#0f172a;"><?= htmlspecialchars($ev['event_title']) ?></h4>
                                        <p style="margin:0; color:#64748b; font-size:13px;"><?= htmlspecialchars($ev['event_venue']) ?> · <?= htmlspecialchars($ev['event_time']) ?> · <span style="background:#d8f3dc; color:#006633; padding:2px 6px; border-radius:4px; font-weight:bold; font-size:11px;"><?= htmlspecialchars($ev['category']) ?></span></p>
                                    </div>
                                    <a href="event/edit_event.php?id=<?= $ev['event_id'] ?>" style="color:#2563eb; text-decoration:none; font-weight:bold; font-size:13px;">Edit</a>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p style="color:#94a3b8; text-align:center; padding:20px;">No events created yet.</p>
                        <?php endif; ?>
                    </div>
                </section>

                <section class="panel quick-panel">
                    <div class="panel-header">
                        <h2 style="margin:0; color:#0f172a;">Quick Actions</h2>
                    </div>

                    <div class="quick-actions">
                        <button onclick="window.location.href='event/upcomming.php'">
                            <span style="font-size:20px; color:#006633;">＋</span>
                            <div><strong>Create Event</strong><br><small style="color:#64748b;">Add a new upcoming event</small></div>
                        </button>

                        <button onclick="window.location.href='event/categories.php'">
                            <span style="font-size:20px; color:#006633;">▣</span>
                            <div><strong>Manage Categories</strong><br><small style="color:#64748b;">Create or edit categories</small></div>
                        </button>

                        <button onclick="window.location.href='event/announcements.php'">
                            <span style="font-size:20px; color:#006633;">📢</span>
                            <div><strong>New Announcement</strong><br><small style="color:#64748b;">Publish notice to students</small></div>
                        </button>

                        <button onclick="window.location.href='event/participants.php'">
                            <span style="font-size:20px; color:#006633;">☷</span>
                            <div><strong>Participant Lists</strong><br><small style="color:#64748b;">Generate printable list</small></div>
                        </button>
                    </div>
                </section>
            </div>

            <!-- Bottom Grid -->
            <div class="bottom-grid">
                <section class="panel">
                    <div class="panel-header">
                        <div>
                            <h2 style="margin:0 0 4px 0; color:#0f172a;">Recent Registrations</h2>
                            <p style="margin:0; color:#64748b; font-size:13px;">Latest student event registrations.</p>
                        </div>
                        <a href="event/registrations.php" style="color:#006633; text-decoration:none; font-weight:bold; font-size:13px;">View All →</a>
                    </div>

                    <div class="registration-list">
                        <?php if ($regs_res && $regs_res->num_rows > 0): ?>
                            <?php while ($reg = $regs_res->fetch_assoc()): ?>
                                <div style="display:flex; align-items:center; gap:12px; padding:10px 0; border-bottom:1px solid #f1f5f9;">
                                    <div style="background:#d8f3dc; color:#006633; width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:13px;">
                                        <?= strtoupper(substr($reg['full_name'], 0, 2)) ?>
                                    </div>
                                    <div style="flex:1;">
                                        <strong style="display:block; color:#0f172a; font-size:14px;"><?= htmlspecialchars($reg['full_name']) ?></strong>
                                        <span style="color:#64748b; font-size:12px;"><?= htmlspecialchars($reg['event_title']) ?></span>
                                    </div>
                                    <small style="color:#94a3b8; font-size:11px;"><?= date('M d, H:i', strtotime($reg['registered_at'])) ?></small>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p style="color:#94a3b8; text-align:center; padding:15px;">No registrations recorded yet.</p>
                        <?php endif; ?>
                    </div>
                </section>

                <section class="panel announcement-panel">
                    <div class="panel-header">
                        <div>
                            <h2 style="margin:0 0 4px 0; color:#0f172a;">Latest Announcements</h2>
                            <p style="margin:0; color:#64748b; font-size:13px;">Recent updates published to portal.</p>
                        </div>
                        <a href="event/announcements.php" style="color:#006633; text-decoration:none; font-weight:bold; font-size:13px;">Manage →</a>
                    </div>

                    <div class="announcement-list">
                        <?php if ($ann_res && $ann_res->num_rows > 0): ?>
                            <?php while ($ann = $ann_res->fetch_assoc()): ?>
                                <div style="padding:10px 0; border-bottom:1px solid #f1f5f9;">
                                    <strong style="color:#0f172a; font-size:14px; display:block;"><?= htmlspecialchars($ann['title']) ?></strong>
                                    <p style="margin:3px 0; color:#64748b; font-size:13px;"><?= htmlspecialchars(substr($ann['text'], 0, 80)) ?>...</p>
                                    <small style="color:#94a3b8; font-size:11px;"><?= date('M d, Y', strtotime($ann['created_at'])) ?></small>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p style="color:#94a3b8; text-align:center; padding:15px;">No announcements posted.</p>
                        <?php endif; ?>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <footer>
        <p>© 2026 NSBM EventHub | University Event Management System</p>
    </footer>
</body>
</html>
