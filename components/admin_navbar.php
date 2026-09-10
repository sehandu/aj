<?php
$rel = (file_exists('db/db.php')) ? '.' : ((file_exists('../db/db.php')) ? '..' : '../..');
?>
<nav>
    <div class="logo">NSBM <span>EventHub</span></div>
    <ul>
        <li><a href="<?php echo $rel; ?>/admin/eventhub_dashboard.php">Dashboard</a></li>
        <li><a href="<?php echo $rel; ?>/admin/event/event.php">Events</a></li>
        <li><a href="<?php echo $rel; ?>/admin/event/upcomming.php">Add Event</a></li>
        <li><a href="<?php echo $rel; ?>/admin/event/categories.php">Categories</a></li>
        <li><a href="<?php echo $rel; ?>/admin/event/announcements.php">Announcements</a></li>
        <li><a href="<?php echo $rel; ?>/admin/event/registrations.php">Registrations</a></li>
        <li><a href="<?php echo $rel; ?>/admin/event/participants.php">Participants</a></li>
        <li><a href="<?php echo $rel; ?>/authentication/login/logout.php">Logout</a></li>
    </ul>
</nav>
