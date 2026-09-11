<?php
session_start();
include '../../db/db.php';

// Ensure user is logged in as admin
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
  header('Location: ../../authentication/login/login.php');
  exit();
}

$msg = $_GET['msg'] ?? '';

// Fetch categories for filter dropdown
$categories_res = $conn->query('SELECT * FROM categories ORDER BY category_name ASC');

// Fetch events from database
$sql = 'SELECT * FROM events ORDER BY event_date ASC';
$result = $conn->query($sql);
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events | NSBM EventHub Admin</title>
    <link rel="stylesheet" href="../../components/style.css">
    <link rel="stylesheet" href="../../main.css">
    <link rel="stylesheet" href="event.css">
    <script src="event.js" defer></script>
  </head>
  <body>
    <div class="page">
      <?php include '../../components/admin_navbar.php'; ?>

      <section class="page-header">
        <h1>Event Management</h1>
        <p><i>View, Edit, and Manage Campus Events</i></p>
      </section>

      <div style="max-width: 1100px; margin: 0 auto 20px; padding: 0 20px;">
        <?php if (!empty($msg)): ?>
          <div style="background:#dcfce7; color:#166534; padding:12px; border-radius:8px; text-align:center; font-weight:bold; margin-bottom:20px;">
            <?php echo $msg; ?>
          </div>
        <?php endif; ?>
      </div>

      <div class="search-area">
       
        <select id="categoryFilter" onchange="filterEvents()">
          <option value="all">All Categories</option>
          <?php if ($categories_res && $categories_res->num_rows > 0): ?>
            <?php while ($cat = $categories_res->fetch_assoc()): ?>
              <option value="<?php echo $cat['category_name']; ?>">
                <?php echo $cat['category_name']; ?>
              </option>
            <?php endwhile; ?>
          <?php endif; ?>
        </select>
        <a href="upcomming.php" style="background:#006633; color:white; padding:12px 20px; border-radius:8px; text-decoration:none; font-weight:bold; display:inline-flex; align-items:center; whitespace:nowrap;">+ Create New Event</a>
      </div>

      <section class="events-container" id="eventsContainer">
        <?php if ($result && $result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <div class="event-card" data-category="<?php echo strtolower($row['category']); ?>">
              <?php if (!empty($row['image'])): ?>
                <img 
                  src="../../uploads/<?php echo $row['image']; ?>" 
                  alt="<?php echo $row['event_title']; ?>" 
                  style="width:100%; height:180px; object-fit:cover; border-radius:8px 8px 0 0;"
                />
              <?php endif; ?>
              <div class="event-content">
                <span class="category"><?php echo $row['category']; ?></span>
                <h2><?php echo $row['event_title']; ?></h2>
                <div class="event-info">
                  <strong>Date:</strong> <?php echo $row['event_date']; ?>
                  
                    | <strong>Time:</strong> <?php echo $row['event_time']; ?>
                 
                  <br />
                  
                    <strong>Venue:</strong> <?php echo $row['event_venue']; ?><br />
                
                    <strong>Organizer:</strong> <?php echo $row['organizer']; ?><br />
                  
                  <strong>Max Participants:</strong> <?php echo $row['max_participants']; ?><br />
                  <i><?php echo $row['description']; ?></i>
                </div>
                <div style="margin-top:15px; display:flex; gap:10px;">
                  <a href="edit_event.php?id=<?php echo $row['event_id']; ?>" style="background:#2563eb; color:white; padding:8px 14px; border-radius:6px; text-decoration:none; font-size:13px; font-weight:bold;">Edit Event</a>
                  <a href="delete_event.php?id=<?php echo $row['event_id']; ?>" onclick="return confirmDelete('<?php echo addslashes($row['event_title']); ?>');" style="background:#dc2626; color:white; padding:8px 14px; border-radius:6px; text-decoration:none; font-size:13px; font-weight:bold;">Delete Event</a>
                </div>
              </div>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <p style="text-align:center; width:100%; color:#666; padding: 30px; font-size:16px;">No events found in database. Click "+ Create New Event" to add one.</p>
        <?php endif; ?>
      </section>
    </div>

    <footer>
      <p>© 2026 NSBM EventHub | University Event Management System</p>
    </footer>
  </body>
</html>
