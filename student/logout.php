<?php
session_start();
session_unset();
session_destroy();
header('Location: ../authentication/login/login.php');
exit();
?>
