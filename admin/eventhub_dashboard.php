<?php
session_start();
include '../db/db.php';

// Check admin session
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../authentication/login/login.php');
    exit();
}
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
        .admin-nav-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            margin: 40px auto 60px;
            max-width: 1100px;
            padding: 0 20px;
        }
        .nav-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 30px 25px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.05);
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
        }
        .nav-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 24px rgba(0, 102, 51, 0.12);
            border-color: #b7e4c7;
        }
        .nav-card-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 16px;
        }
        .nav-icon {
            font-size: 28px;
            background: #d8f3dc;
            width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            color: #006633;
            flex-shrink: 0;
        }
        .nav-card-title {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }
        .nav-card-desc {
            font-size: 14px;
            color: #64748b;
            line-height: 1.5;
            margin: 0 0 20px 0;
            flex-grow: 1;
        }
        .nav-card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-weight: 600;
            font-size: 14px;
            color: #006633;
            padding-top: 15px;
            border-top: 1px solid #f1f5f9;
        }
        .nav-card:hover .nav-card-footer {
            color: #004d26;
        }
        .arrow-icon {
            transition: transform 0.2s ease;
        }
        .nav-card:hover .arrow-icon {
            transform: translateX(5px);
        }
    </style>
</head>
<body>
    <div class="page">
        <?php include '../components/admin_navbar.php'; ?>

        <section class="page-header">
            <h1>Admin Dashboard</h1>
            <p>Select a portal section to manage NSBM EventHub</p>
        </section>

        <main class="admin-nav-grid">
            <a href="event/event.php" class="nav-card">
                <div>
                    <div class="nav-card-header">
                        <div class="nav-icon">📅</div>
                        <h2 class="nav-card-title">All Events</h2>
                    </div>
                    <p class="nav-card-desc">View, search, edit, or remove all existing university events in the system.</p>
                </div>
                <div class="nav-card-footer">
                    <span>Manage Events</span>
                    <span class="arrow-icon">→</span>
                </div>
            </a>

            <a href="event/upcomming.php" class="nav-card">
                <div>
                    <div class="nav-card-header">
                        <div class="nav-icon">➕</div>
                        <h2 class="nav-card-title">Add Event</h2>
                    </div>
                    <p class="nav-card-desc">Create and publish new upcoming events with full details, venue, and images.</p>
                </div>
                <div class="nav-card-footer">
                    <span>Create Event</span>
                    <span class="arrow-icon">→</span>
                </div>
            </a>

            <a href="event/categories.php" class="nav-card">
                <div>
                    <div class="nav-card-header">
                        <div class="nav-icon">🏷️</div>
                        <h2 class="nav-card-title">Categories</h2>
                    </div>
                    <p class="nav-card-desc">Organize and manage event categories to help students easily filter events.</p>
                </div>
                <div class="nav-card-footer">
                    <span>Manage Categories</span>
                    <span class="arrow-icon">→</span>
                </div>
            </a>

            <a href="event/announcements.php" class="nav-card">
                <div>
                    <div class="nav-card-header">
                        <div class="nav-icon">📢</div>
                        <h2 class="nav-card-title">Announcements</h2>
                    </div>
                    <p class="nav-card-desc">Post and broadcast official updates, news, and notifications to all users.</p>
                </div>
                <div class="nav-card-footer">
                    <span>Manage Announcements</span>
                    <span class="arrow-icon">→</span>
                </div>
            </a>

            <a href="event/registrations.php" class="nav-card">
                <div>
                    <div class="nav-card-header">
                        <div class="nav-icon">📋</div>
                        <h2 class="nav-card-title">Registrations</h2>
                    </div>
                    <p class="nav-card-desc">Monitor student event sign-ups, enrollment dates, and registration history.</p>
                </div>
                <div class="nav-card-footer">
                    <span>View Registrations</span>
                    <span class="arrow-icon">→</span>
                </div>
            </a>

            <a href="event/participants.php" class="nav-card">
                <div>
                    <div class="nav-card-header">
                        <div class="nav-icon">👥</div>
                        <h2 class="nav-card-title">Participants List</h2>
                    </div>
                    <p class="nav-card-desc">Generate and print event participant lists for attendance check-ins.</p>
                </div>
                <div class="nav-card-footer">
                    <span>View Participants</span>
                    <span class="arrow-icon">→</span>
                </div>
            </a>
        </main>
    </div>

    <footer>
        <p>© 2026 NSBM EventHub | University Event Management System</p>
    </footer>
</body>
</html>

