<?php
session_start();
include '../../db/db.php';

// Ensure user is logged in as admin
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../authentication/login/login.php");
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

    if (
        empty($event_title) ||
        empty($description) ||
        empty($category) ||
        empty($event_date) ||
        empty($event_time) ||
        empty($event_venue) ||
        empty($max_participants) ||
        empty($organizer)
    ) {
        $error_msg = "Please fill out all required fields.";
    } else {
        // Handle Image File Upload
        $image_filename = "";
        if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
            $file_tmp  = $_FILES['event_image']['tmp_name'];
            $file_name = $_FILES['event_image']['name'];
            $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (!in_array($file_ext, $allowed_exts)) {
                $error_msg = "Invalid image format. Allowed formats: JPG, JPEG, PNG, GIF, WEBP.";
            } else {
                $target_dir = __DIR__ . '/../../uploads/';
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                $image_filename = time() . '_' . basename($file_name);
                $target_filepath = $target_dir . $image_filename;

                if (!move_uploaded_file($file_tmp, $target_filepath)) {
                    $error_msg = "Failed to upload image.";
                }
            }
        } else {
            $error_msg = "Please upload an event image.";
        }

        if (empty($error_msg)) {
            $sql = "INSERT INTO events (event_title, description, category, event_date, event_time, event_venue, max_participants, organizer, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("ssssssiss", $event_title, $description, $category, $event_date, $event_time, $event_venue, $max_participants, $organizer, $image_filename);
                if ($stmt->execute()) {
                    $success_msg = "Event Added Successfully!";
                } else {
                    $error_msg = "Database Error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $error_msg = "SQL Error: " . $conn->error;
            }
        }
    }
}

// Fetch categories for select dropdown
$categories_res = $conn->query("SELECT * FROM categories ORDER BY category_name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Event - NSBM EventHub</title>
    <link rel="stylesheet" href="../../components/style.css">
    <link rel="stylesheet" href="../../main.css">
    <link rel="stylesheet" href="upcomming.css">
    <script src="upcomming.js" defer></script>
</head>
<body>
    <div class="page">
        <?php include '../../components/admin_navbar.php'; ?>

        <section class="page-header">
            <h1>Add New Upcoming Event</h1>
            <p><i>Create and publish a new event for NSBM Students</i></p>
        </section>

        <main class="container">
            <div class="form-card">
                <?php if (!empty($success_msg)): ?>
                    <div style="background:#dcfce7; color:#166534; padding:15px; border-radius:8px; margin-bottom:20px; text-align:center; font-weight:bold;">
                        <?php echo htmlspecialchars($success_msg); ?><br>
                        <a href="event.php" style="color:#006633; text-decoration:underline; display:inline-block; margin-top:10px;">View All Events</a> | 
                        <a href="upcomming.php" style="color:#006633; text-decoration:underline; display:inline-block; margin-top:10px;">Add Another Event</a>
                    </div>
                <?php else: ?>

                    <?php if (!empty($error_msg)): ?>
                        <div style="background:#fee2e2; color:#991b1b; padding:12px; border-radius:8px; margin-bottom:20px; text-align:center; font-weight:bold;">
                            <?php echo htmlspecialchars($error_msg); ?>
                        </div>
                    <?php endif; ?>

                    <form id="eventForm" action="upcomming.php" method="post" enctype="multipart/form-data">
                        <div class="form-group full-width">
                            <label for="eventTitle">Event Title <span>*</span></label>
                            <input type="text" id="eventTitle" name="event_title" placeholder="Enter event title">
                        </div>

                        <div class="form-group full-width">
                            <label for="description">Event Description <span>*</span></label>
                            <textarea id="description" name="description" placeholder="Describe the Event"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="category">Category <span>*</span></label>
                            <select id="category" name="category">
                                <option value="">Select Category</option>
                                <?php if ($categories_res && $categories_res->num_rows > 0): ?>
                                    <?php while ($cat = $categories_res->fetch_assoc()): ?>
                                        <option value="<?php echo htmlspecialchars($cat['category_name']); ?>">
                                            <?php echo htmlspecialchars($cat['category_name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <option value="Academic">Academic</option>
                                    <option value="Workshop">Workshop</option>
                                    <option value="Sports">Sports</option>
                                    <option value="Social">Social</option>
                                    <option value="Club Event">Club Event</option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="form-group full-width">
                            <label for="event_date">Event Date <span>*</span></label>
                            <input type="date" id="event_date" name="event_date">
                        </div>

                        <div class="form-group full-width">
                            <label for="eventTime">Event Time <span>*</span></label>
                            <input type="time" id="eventTime" name="event_time">
                        </div>

                        <div class="form-group full-width">
                            <label for="eventVenue">Event Venue <span>*</span></label>
                            <input type="text" id="eventVenue" name="event_venue" placeholder="Example: NSBM main auditorium">
                        </div>

                        <div class="form-group full-width">
                            <label for="participants">Maximum Participants <span>*</span></label>
                            <input type="number" id="participants" name="max_participants" placeholder="Example: 100" value="100">
                        </div>

                        <div class="form-group full-width">
                            <label for="organizer">Organizer <span>*</span></label>
                            <input type="text" id="organizer" name="organizer" placeholder="Example: NSBM IT Club">
                        </div>

                        <div class="form-group full-width">
                            <label for="eventImage">Event Image <span>*</span></label>
                            <input type="file" id="eventImage" name="event_image">
                            <small>JPG, PNG, GIF or WEBP format</small>
                        </div>

                        <div id="errorMessage" class="error-message" style="color:#dc2626; font-weight:bold; margin-bottom:15px;"></div>

                        <div class="form-buttons">
                            <button type="button" class="cancel-button" onclick="clearForm()">Cancel</button>
                            <button type="submit" class="submit-button">Add Event</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <footer>
        <p>© 2026 NSBM EventHub | University Event Management System</p>
    </footer>
</body>
</html>