<?php include('header.php'); ?>
<?php
session_start();  // Start the session
session_destroy();  // Destroy the session (log the user out)
header("Location: login.html");  // Redirect to the login page
exit();
?>
