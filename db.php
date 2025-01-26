<?php include('header.php'); ?>
<?php
// Database connection details
$servername = "localhost";  // Database server
$username = "root";         // Database username
$password = "";             // Database password
$dbname = "vetcare1";        // Your database name

// Create a new connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
