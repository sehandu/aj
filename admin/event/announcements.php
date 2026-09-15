<?php
session_start();
include '../../db/db.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../authentication/login/login.php');
    exit();
}

$error_msg = '';
$success_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $title = $_POST['title'];
    $text = $_POST['text'];

    if (empty($title) || empty($text)) {
        $error_msg = 'Both title and text content are required.';
    } else {
        if ($conn->query("INSERT INTO announcements (title, text) VALUES ('$title', '$text')")) {
            $success_msg = 'Announcement published successfully!';
        } else {
            $error_msg = 'Error publishing announcement.';
        }
    }
}

if (isset($_GET['delete'])) {
    $ann_id = intval($_GET['delete']);
    if ($ann_id > 0) {
        $conn->query("DELETE FROM announcements WHERE announcement_id = $ann_id");
        $success_msg = 'Announcement deleted successfully!';
    }
}

$result = $conn->query('SELECT * FROM announcements ORDER BY created_at DESC');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Announcements - NSBM EventHub</title>
    <link rel="stylesheet" href="../../components/style.css">
    <link rel="stylesheet" href="../../main.css">
    <link rel="stylesheet" href="event.css">
    <script>
        function validateAnnouncementForm() {
            var title = document.getElementById("ann_title").value;
            var text = document.getElementById("ann_text").value;
            if (title === "") {
                alert("Please enter announcement title.");
                document.getElementById("ann_title").focus();
                return false;
            }
            if (text === "") {
                alert("Please enter announcement text content.");
                document.getElementById("ann_text").focus();
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <div class="page">
        <?php include '../../components/admin_navbar.php'; ?>

        <section class="page-header">
            <h1>Manage Announcements</h1>
            <p>Post and manage important updates for students</p>
        </section>

        <main style="max-width: 1000px; margin: 0 auto 40px; padding: 0 20px;">
            <?php if (!empty($success_msg)): ?>
                <div style="background:#dcfce7; color:#166534; padding:12px; border-radius:8px; margin-bottom:20px; text-align:center; font-weight:bold;">
                    <?php echo $success_msg; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error_msg)): ?>
                <div style="background:#fee2e2; color:#991b1b; padding:12px; border-radius:8px; margin-bottom:20px; text-align:center; font-weight:bold;">
                    <?php echo $error_msg; ?>
                </div>
            <?php endif; ?>

            <div style="display: flex; gap: 30px; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 300px; background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                    <h3 style="color:#006633; margin-top:0;">New Announcement</h3>
                    <form action="announcements.php" method="POST" onsubmit="return validateAnnouncementForm();">
                        <input type="hidden" name="action" value="create">
                        
                        <div style="margin-bottom: 15px;">
                            <label style="display:block; font-weight:bold; margin-bottom:5px;">Title *</label>
                            <input type="text" id="ann_title" name="title" placeholder="e.g. Registration deadline extended" style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:6px;" required>
                        </div>

                        <div style="margin-bottom: 20px;">
                            <label style="display:block; font-weight:bold; margin-bottom:5px;">Announcement Details *</label>
                            <textarea id="ann_text" name="text" placeholder="Enter announcement text..." style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:6px; height:100px;" required></textarea>
                        </div>

                        <button type="submit" style="background:#006633; color:white; padding:10px 20px; border:none; border-radius:6px; font-weight:bold; cursor:pointer;">📢 Post Announcement</button>
                    </form>
                </div>

                <div style="flex: 2; min-width: 320px; background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                    <h3 style="color:#006633; margin-top:0;">Published Announcements</h3>
                    
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($ann = $result->fetch_assoc()): ?>
                            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:15px; margin-bottom:15px; position:relative;">
                                <h4 style="margin:0 0 8px 0; color:#0f172a;"><?php echo $ann['title']; ?></h4>
                                <p style="margin:0 0 10px 0; color:#475569; font-size:14px;"><?php echo $ann['text']; ?></p>
                                <small style="color:#94a3b8;">Posted on: <?php echo $ann['created_at']; ?></small>
                                <div style="margin-top:10px;">
                                    <a href="announcements.php?delete=<?php echo $ann['announcement_id']; ?>" onclick="return confirm('Delete this announcement?');" style="background:#dc2626; color:white; padding:4px 10px; border-radius:4px; text-decoration:none; font-size:12px; font-weight:bold;">Delete</a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="text-align:center; color:#94a3b8; padding:20px;">No announcements published yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <footer>
        <p>© 2026 NSBM EventHub | University Event Management System</p>
    </footer>
</body>
</html>

