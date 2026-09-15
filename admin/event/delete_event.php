<?php
session_start();
include '../../db/db.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../authentication/login/login.php');
    exit();
}

$event_id = $_GET['id'];

$conn->query("DELETE FROM event_registrations WHERE event_id = $event_id");
$conn->query("DELETE FROM events WHERE event_id = $event_id");

header('Location: event.php');
exit();
?>

