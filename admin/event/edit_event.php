<?php
session_start();
include '../../db/db.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../authentication/login/login.php");
    exit();
}

$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($event_id <= 0) {
    header("Location: event.php");
    exit();
}

// Fetch event details
$stmt = $conn->prepare("SELECT * FROM events WHERE event_id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$event) {
    header("Location: event.php");
    exit();
}

$error_msg = "";
$success_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event_title      = trim($_POST["event_title"] ?? "");
    $description      = trim($_POST["description"] ?? "");
    $category         = trim($_POST["category"] ?? "");
    $event_date       = trim($_POST["event_date"] ?? "");
    $event_time       = trim($_POST["event_time"] ?? "");
    $event_venue      = trim($_POST["event_venue"] ?? "");
    $max_participants = trim($_POST["max_participants"] ?? "");
    $organizer        = trim($_POST["organizer"] ?? "");

    if (empty($event_title) || empty($description) || empty($category) || empty($event_date) || empty($event_time) || empty($event_venue) || empty($max_participants) || empty($organizer)) {
        $error_msg = "Please fill out all required fields.";
    } else {
        $image_filename = $event['image'];
        if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
            $file_tmp  = $_FILES['event_image']['tmp_name'];
            $file_name = $_FILES['event_image']['name'];
            $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (!in_array($file_ext, $allowed_exts)) {
                $error_msg = "Invalid image format.";
            } else {
                $target_dir = __DIR__ . '/../../uploads/';
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                $image_filename = time() . '_' . basename($file_name);
                move_uploaded_file($file_tmp, $target_dir . $image_filename);
            }
        }

        if (empty($error_msg)) {
            $update_stmt = $conn->prepare("UPDATE events SET event_title=?, description=?, category=?, event_date=?, event_time=?, event_venue=?, max_participants=?, organizer=?, image=? WHERE event_id=?");
            $update_stmt->bind_param("ssssssissi", $event_title, $description, $category, $event_date, $event_time, $event_venue, $max_participants, $organizer, $image_filename, $event_id);
            if ($update_stmt->execute()) {
                $success_msg = "Event updated successfully!";
                // Refresh event data
                $stmt = $conn->prepare("SELECT * FROM events WHERE event_id = ?");
                $stmt->bind_param("i", $event_id);
                $stmt->execute();
                $event = $stmt->get_result()->fetch_assoc();
                $stmt->close();
            } else {
                $error_msg = "Error updating event: " . $conn->error;
            }
            $update_stmt->close();
        }
    }
}

// Fetch categories
$categories_res = $conn->query("SELECT * FROM categories ORDER BY category_name ASC");
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
            <p><i>Update existing university event details</i></p>
        </section>

        <main class="container">
            <div class="form-card">
                <?php if (!empty($success_msg)): ?>
                    <div style="background:#dcfce7; color:#166534; padding:15px; border-radius:8px; margin-bottom:20px; text-align:center; font-weight:bold;">
                        <?php echo htmlspecialchars($success_msg); ?><br>
                        <a href="event.php" style="color:#006633; text-decoration:underline; display:inline-block; margin-top:10px;">Return to Events List</a>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error_msg)): ?>
                    <div style="background:#fee2e2; color:#991b1b; padding:12px; border-radius:8px; margin-bottom:20px; text-align:center; font-weight:bold;">
                        <?php echo htmlspecialchars($error_msg); ?>
                    </div>
                <?php endif; ?>

                <form id="eventForm" action="edit_event.php?id=<?php echo $event_id; ?>" method="post" enctype="multipart/form-data">
                    <div class="form-group full-width">
                        <label for="eventTitle">Event Title <span>*</span></label>
                        <input type="text" id="eventTitle" name="event_title" value="<?php echo htmlspecialchars($event['event_title']); ?>">
                    </div>

                    <div class="form-group full-width">
                        <label for="description">Event Description <span>*</span></label>
                        <textarea id="description" name="description"><?php echo htmlspecialchars($event['description']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="category">Category <span>*</span></label>
                        <select id="category" name="category">
                            <option value="">Select Category</option>
                            <?php if ($categories_res && $categories_res->num_rows > 0): ?>
                                <?php while ($cat = $categories_res->fetch_assoc()): ?>
                                    <option value="<?php echo htmlspecialchars($cat['category_name']); ?>" <?php if (strtolower($event['category']) == strtolower($cat['category_name'])) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($cat['category_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group full-width">
                        <label for="event_date">Event Date <span>*</span></label>
                        <input type="date" id="event_date" name="event_date" value="<?php echo htmlspecialchars($event['event_date']); ?>">
                    </div>

                    <div class="form-group full-width">
                        <label for="eventTime">Event Time <span>*</span></label>
                        <input type="time" id="eventTime" name="event_time" value="<?php echo htmlspecialchars($event['event_time']); ?>">
                    </div>

                    <div class="form-group full-width">
                        <label for="eventVenue">Event Venue <span>*</span></label>
                        <input type="text" id="eventVenue" name="event_venue" value="<?php echo htmlspecialchars($event['event_venue']); ?>">
                    </div>

                    <div class="form-group full-width">
                        <label for="participants">Maximum Participants <span>*</span></label>
                        <input type="number" id="participants" name="max_participants" value="<?php echo htmlspecialchars($event['max_participants']); ?>">
                    </div>

                    <div class="form-group full-width">
                        <label for="organizer">Organizer <span>*</span></label>
                        <input type="text" id="organizer" name="organizer" value="<?php echo htmlspecialchars($event['organizer']); ?>">
                    </div>

                    <div class="form-group full-width">
                        <label for="eventImage">Change Event Image (Optional)</label>
                        <input type="file" id="eventImage" name="event_image">
                        <?php if (!empty($event['image'])): ?>
                            <small>Current Image: <?php echo htmlspecialchars($event['image']); ?></small>
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
