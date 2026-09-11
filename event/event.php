<?php
session_start();
include '../db/db.php';

$user_id = $_SESSION['user_id'] ?? 0;
$user_role = $_SESSION['role'] ?? '';
$category = $_GET['category'] ?? 'all';

// Registered events list
$registered_ids = [];
if ($user_id && $user_role === 'student') {
    $res = $conn->query("SELECT event_id FROM event_registrations WHERE user_id = $user_id");
    if ($res) {
        while ($r = $res->fetch_assoc()) {
            $registered_ids[] = $r['event_id'];
        }
    }
}

// Categories & Events
$categories = $conn->query('SELECT * FROM categories ORDER BY category_name ASC');

if (!empty($category) && $category !== 'all') {
    $sql = "SELECT * FROM events WHERE category = '$category' ORDER BY event_date ASC";
} else {
    $sql = 'SELECT * FROM events ORDER BY event_date ASC';
}
$events = $conn->query($sql);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upcoming Events | NSBM EventHub</title>
    <link rel="stylesheet" href="../components/style.css">
    <link rel="stylesheet" href="event.css">
    <link rel="stylesheet" href="../main.css">
</head>
<body>
    <div class="page">
        <?php include '../components/navbar.php'; ?>

        <section class="page-header">
            <h1>Upcoming Events</h1>
            <p><i>Where Campus Life Comes Alive</i></p>
            <p>Find exciting events, connect with your community, and <b>make memories last</b></p>
        </section>

        <div class="search-area">
            <select id="categoryFilter" onchange="window.location.href='event.php?category=' + encodeURIComponent(this.value)">
                <option value="all" <?php if ($category === 'all') echo 'selected'; ?>>All Categories</option>
                <?php if ($categories && $categories->num_rows > 0): ?>
                    <?php while ($cat = $categories->fetch_assoc()): ?>
                        <option value="<?php echo $cat['category_name']; ?>" <?php if ($category === $cat['category_name']) echo 'selected'; ?>>
                            <?php echo $cat['category_name']; ?>
                        </option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>
        </div>

        <section class="events-container">
            <?php if ($events && $events->num_rows > 0): ?>
                <?php
                while ($row = $events->fetch_assoc()):
                    $is_registered = in_array($row['event_id'], $registered_ids);
                    ?>
                    <div class="event-card">
                        <?php if (!empty($row['image'])): ?>
                            <img src="../uploads/<?php echo $row['image']; ?>" alt="<?php echo $row['event_title']; ?>" style="width:100%; height:180px; object-fit:cover; border-radius:8px 8px 0 0;">
                        <?php endif; ?>
                        <div class="event-content">
                            <span class="category"><?php echo $row['category']; ?></span>
                            <h2><?php echo $row['event_title']; ?></h2>
                            <div class="event-info">
                                <strong>Date:</strong> <?php echo $row['event_date']; ?>
                                <?php if (!empty($row['event_time'])): ?>
                                    | <strong>Time:</strong> <?php echo $row['event_time']; ?>
                                <?php endif; ?>
                                <br>
                                <?php if (!empty($row['event_venue'])): ?>
                                    <strong>Venue:</strong> <?php echo $row['event_venue']; ?><br>
                                <?php endif; ?>
                                <?php if (!empty($row['organizer'])): ?>
                                    <strong>Organizer:</strong> <?php echo $row['organizer']; ?><br>
                                <?php endif; ?>
                                <i><?php echo $row['description']; ?></i>
                            </div>
                            <div style="margin-top:15px;">
                                <?php if ($is_registered): ?>
                                    <span style="background:#dcfce7; color:#15803d; padding:8px 14px; border-radius:6px; font-weight:bold; font-size:13px; display:inline-block;">✓ Registered</span>
                                <?php else: ?>
                                    <a href="register_event.php?id=<?php echo $row['event_id']; ?>" style="background:#006633; color:white; padding:8px 16px; border-radius:6px; text-decoration:none; font-weight:bold; font-size:13px; display:inline-block;">Register Now</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align:center; width:100%; color:#666; padding: 20px;">No upcoming events found.</p>
            <?php endif; ?>
        </section>
    </div>

    <footer>
        <p>© 2026 NSBM EventHub | University Event Management System</p>
    </footer>
</body>
</html>



