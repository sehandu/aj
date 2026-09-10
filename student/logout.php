<?php
session_start();
session_unset();
session_destroy();
header("Location: ../authentication/login/login.php?msg=" . urlencode("You have successfully logged out!"));
exit();
?>
