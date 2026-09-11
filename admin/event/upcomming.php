<?php
session_start();
include '../../db/db.php';

// Ensure user is logged in as admin
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../authentication/login/login.php');
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

    // Handle Image File Upload
    $image_filename = '';
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
        $sql = "INSERT INTO events (event_title, description, category, event_date, event_time, event_venue, max_participants, organizer, image) 
                VALUES ('$event_title', '$description', '$category', '$event_date', '$event_time', '$event_venue', '$max_participants', '$organizer', '$image_filename')";

        if ($conn->query($sql)) {
            $success_msg = 'Event Added Successfully!';
        } else {
            $error_msg = 'Database Error: ' . $conn->error;
        }
    }
}

// Fetch categories for select dropdown
$categories_res = $conn->query('SELECT * FROM categories ORDER BY category_name ASC');
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
            <p>Create and publish a new event for NSBM Students</p>
        </section>

        <main class="container">
            <div class="form-card">
                <?php if (!empty($success_msg)): ?>
                    <div style="background:#dcfce7; color:#166534; padding:15px; border-radius:8px; margin-bottom:20px; text-align:center; font-weight:bold;">
                        <?php echo $success_msg; ?><br>
                        <a href="event.php" style="color:#006633; text-decoration:underline; display:inline-block; margin-top:10px;">View All Events</a> | 
                        <a href="upcomming.php" style="color:#006633; text-decoration:underline; display:inline-block; margin-top:10px;">Add Another Event</a>
                    </div>
                <?php else: ?>

                    <?php if (!empty($error_msg)): ?>
                        <div style="background:#fee2e2; color:#991b1b; padding:12px; border-radius:8px; margin-bottom:20px; text-align:center; font-weight:bold;">
                            <?php echo $error_msg; ?>
                        </div>
                    <?php endif; ?>

                    <form id="eventForm" action="upcomming.php" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
                        <div class="form-group full-width">
                            <label for="eventTitle">Event Title *</label>
                            <input type="text" id="eventTitle" name="event_title" placeholder="Enter event title">
                        </div>

                        <div class="form-group full-width">
                            <label for="description">Event Description *</label>
                            <textarea id="description" name="description" placeholder="Describe the Event"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="category">Category *</label>
                            <select id="category" name="category">
                                <option value="">Select Category</option>
                                <?php if ($categories_res && $categories_res->num_rows > 0): ?>
                                    <?php while ($cat = $categories_res->fetch_assoc()): ?>
                                        <option value="<?php echo $cat['category_name']; ?>">
                                            <?php echo $cat['category_name']; ?>
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
                            <label for="event_date">Event Date *</label>
                            <input type="date" id="event_date" name="event_date">
                        </div>

                        <div class="form-group full-width">
                            <label for="eventTime">Event Time *</label>
                            <input type="time" id="eventTime" name="event_time">
                        </div>

                        <div class="form-group full-width">
                            <label for="eventVenue">Event Venue *</label>
                            <input type="text" id="eventVenue" name="event_venue" placeholder="Example: NSBM main auditorium">
                        </div>

                        <div class="form-group full-width">
                            <label for="participants">Maximum Participants *</label>
                            <input type="number" id="participants" name="max_participants" placeholder="Example: 100" value="100">
                        </div>

                        <div class="form-group full-width">
                            <label for="organizer">Organizer *</label>
                            <input type="text" id="organizer" name="organizer" placeholder="Example: NSBM IT Club">
                        </div>

                        <div class="form-group full-width">
                            <label for="eventImage">Event Image *</label>
                            <input type="file" id="eventImage" name="event_image">
                        </div>

                        <div id="errorMessage" class="error-message" style="color:#dc2626; font-weight:bold; margin-bottom:15px;"></div>

                        <div class="form-buttons">
                            <button type="button" class="cancel-button" onclick="clearForm()">Clear</button>
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