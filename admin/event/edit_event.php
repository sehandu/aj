<?php
session_start();
include '../../db/db.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../authentication/login/login.php');
    exit();
}

$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($event_id <= 0) {
    header('Location: event.php');
    exit();
}

// Fetch event details
$res = $conn->query("SELECT * FROM events WHERE event_id = $event_id");
$event = $res ? $res->fetch_assoc() : null;

if (!$event) {
    header('Location: event.php');
    exit();
}

$error_msg = '';
$success_msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_title = $_POST['event_title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $event_venue = $_POST['event_venue'];
    $max_participants = $_POST['max_participants'];
    $organizer = $_POST['organizer'];

    // Handle Image Upload
    $image_filename = $event['image'];
    if (isset($_FILES['event_image']) && $_FILES['event_image']['name'] != '') {
        $image_filename = time() . '_' . basename($_FILES['event_image']['name']);
        $target_dir = '../../uploads/';
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        move_uploaded_file($_FILES['event_image']['tmp_name'], $target_dir . $image_filename);
    }

    if (empty($event_title) || empty($description) || empty($category) || empty($event_date)) {
        $error_msg = 'Please fill out all required fields.';
    } else {
        $sql = "UPDATE events SET 
                event_title='$event_title', 
                description='$description', 
                category='$category', 
                event_date='$event_date', 
                event_time='$event_time', 
                event_venue='$event_venue', 
                max_participants='$max_participants', 
                organizer='$organizer', 
                image='$image_filename' 
                WHERE event_id=$event_id";

        if ($conn->query($sql)) {
            $success_msg = 'Event updated successfully!';
            // Refresh event data
            $res = $conn->query("SELECT * FROM events WHERE event_id = $event_id");
            $event = $res->fetch_assoc();
        } else {
            $error_msg = 'Database Error: ' . $conn->error;
        }
    }
}

// Fetch categories
$categories_res = $conn->query('SELECT * FROM categories ORDER BY category_name ASC');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event - NSBM EventHub</title>
    <link rel="stylesheet" href="../../components/style.css">
    <link rel="stylesheet" href="../../main.css">
    <link rel="stylesheet" href="upcomming.css">
    <script src="upcomming.js" defer></script>
</head>
<body>
    <div class="page">
        <?php include '../../components/admin_navbar.php'; ?>

        <section class="page-header">
            <h1>Edit Event Details</h1>
            <p>Update existing university event details</p>
        </section>

        <main class="container">
            <div class="form-card">
                <?php if (!empty($success_msg)): ?>
                    <div style="background:#dcfce7; color:#166534; padding:15px; border-radius:8px; margin-bottom:20px; text-align:center; font-weight:bold;">
                        <?php echo $success_msg; ?><br>
                        <a href="event.php" style="color:#006633; text-decoration:underline; display:inline-block; margin-top:10px;">Return to Events List</a>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error_msg)): ?>
                    <div style="background:#fee2e2; color:#991b1b; padding:12px; border-radius:8px; margin-bottom:20px; text-align:center; font-weight:bold;">
                        <?php echo $error_msg; ?>
                    </div>
                <?php endif; ?>

                <form id="eventForm" action="edit_event.php?id=<?php echo $event_id; ?>" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
                    <div class="form-group full-width">
                        <label for="eventTitle">Event Title *</label>
                        <input type="text" id="eventTitle" name="event_title" value="<?php echo $event['event_title']; ?>">
                    </div>

                    <div class="form-group full-width">
                        <label for="description">Event Description *</label>
                        <textarea id="description" name="description"><?php echo $event['description']; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="category">Category *</label>
                        <select id="category" name="category">
                            <option value="">Select Category</option>
                            <?php if ($categories_res && $categories_res->num_rows > 0): ?>
                                <?php while ($cat = $categories_res->fetch_assoc()): ?>
                                    <option value="<?php echo $cat['category_name']; ?>" <?php if ($event['category'] == $cat['category_name']) echo 'selected'; ?>>
                                        <?php echo $cat['category_name']; ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group full-width">
                        <label for="event_date">Event Date *</label>
                        <input type="date" id="event_date" name="event_date" value="<?php echo $event['event_date']; ?>">
                    </div>

                    <div class="form-group full-width">
                        <label for="eventTime">Event Time *</label>
                        <input type="time" id="eventTime" name="event_time" value="<?php echo $event['event_time']; ?>">
                    </div>

                    <div class="form-group full-width">
                        <label for="eventVenue">Event Venue *</label>
                        <input type="text" id="eventVenue" name="event_venue" value="<?php echo $event['event_venue']; ?>">
                    </div>

                    <div class="form-group full-width">
                        <label for="participants">Maximum Participants *</label>
                        <input type="number" id="participants" name="max_participants" value="<?php echo $event['max_participants']; ?>">
                    </div>

                    <div class="form-group full-width">
                        <label for="organizer">Organizer *</label>
                        <input type="text" id="organizer" name="organizer" value="<?php echo $event['organizer']; ?>">
                    </div>

                    <div class="form-group full-width">
                        <label for="eventImage">Change Event Image (Optional)</label>
                        <input type="file" id="eventImage" name="event_image">
                        <?php if (!empty($event['image'])): ?>
                            <small>Current Image: <?php echo $event['image']; ?></small>
                        <?php endif; ?>
                    </div>

                    <div id="errorMessage" class="error-message" style="color:#dc2626; font-weight:bold; margin-bottom:15px;"></div>

                    <div class="form-buttons">
                        <button type="button" class="cancel-button" onclick="window.location.href='event.php'">Cancel</button>
                        <button type="submit" class="submit-button">Update Event</button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <footer>
        <p>© 2026 NSBM EventHub | University Event Management System</p>
    </footer>
</body>
</html>

