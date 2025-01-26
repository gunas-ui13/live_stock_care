<?php include('header.php'); ?>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointmentId = $_POST['appointment_id'];
    $status = $_POST['update_status'];

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "vetcare1";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmtUpdateStatus = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ?");
    $stmtUpdateStatus->bind_param("si", $status, $appointmentId);
    if ($stmtUpdateStatus->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmtUpdateStatus->close();
    $conn->close();
} else {
    echo "Invalid request method";
}
