<?php include('header.php'); ?>
<?php
session_start();
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] != 'Admin') {
    header("Location: login.php");
    exit();
}

$adminName = $_SESSION['admin_name'];
$adminId = $_SESSION['admin_id'];

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "vetcare1";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch doctor_id of the logged-in admin from the admins table
$stmtDoctorId = $conn->prepare("SELECT doctor_id FROM admins WHERE id = ? LIMIT 1");
$stmtDoctorId->bind_param("i", $adminId);
$stmtDoctorId->execute();
$result = $stmtDoctorId->get_result();

// Check if doctor_id is fetched
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $doctorId = $row['doctor_id'];
} else {
    echo "Doctor ID not found for the admin!";
    exit();
}

// Fetch appointments along with pet details and reason
$stmtAppointments = $conn->prepare("SELECT a.*, u.name AS user_name, p.pet_name, p.pet_type, p.pet_age 
                                    FROM appointments a
                                    LEFT JOIN users u ON a.user_id = u.id
                                    LEFT JOIN pet_records p ON a.pet_id = p.id  -- Correcting the join to reference pet_records.id
                                    WHERE a.doctor_id = ? 
                                    ORDER BY a.appointment_date DESC");
$stmtAppointments->bind_param("i", $doctorId);
$stmtAppointments->execute();
$appointments = $stmtAppointments->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Appointments</title>
    <link rel="stylesheet" href="admin_dashboard.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #000;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: url('images/duck.jpg') no-repeat center center/cover;
        }
        .appointments-container {
            background-color: rgba(0, 0, 0, 0.7);
            padding: 20px;
            border-radius: 8px;
            width: 100%;
            max-width: 1200px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f5c71a;
            color: black;
        }
        tr:hover {
            background-color: #f5c71a;
            color: black;
        }
        .action-btn {
            padding: 5px 10px;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .confirm-btn {
            background-color: #4CAF50;
        }
        .cancel-btn {
            background-color: #f44336;
        }
        .action-btn:hover {
            opacity: 0.8;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $(".update-status-btn").click(function (e) {
                e.preventDefault();
                var appointmentId = $(this).data("id");
                var newStatus = $(this).data("status");

                $.ajax({
                    url: "update_appointment_status.php",
                    type: "POST",
                    data: { appointment_id: appointmentId, update_status: newStatus },
                    success: function (response) {
                        if (response === "success") {
                            alert("Appointment status updated successfully!");
                            location.reload(); // Reload the page to reflect changes
                        } else {
                            alert("Failed to update appointment status. Please try again.");
                        }
                    },
                    error: function () {
                        alert("An error occurred. Please try again.");
                    }
                });
            });
        });
    </script>
</head>
<body>
    <div class="appointments-container">
        <header>
            <h1>View Appointments</h1>
        </header>

        <main>
            <h2>Appointments</h2>
            <table>
                <thead>
                    <tr>
                        <th>Appointment ID</th>
                        <th>User Name</th>
                        <th>Pet Name</th>
                        <th>Pet Type</th>
                        <th>Pet Age</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($appointments->num_rows > 0) {
                        while ($row = $appointments->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['user_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['pet_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['pet_type']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['pet_age']) . "</td>";
                            echo "<td>" . date('d/m/Y', strtotime($row['appointment_date'])) . "</td>";
                            echo "<td>" . htmlspecialchars($row['appointment_time']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['reason']) . "</td>";  
                            echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                            echo "<td>";
                            if ($row['status'] == 'Pending') {
                                echo "<button class='action-btn confirm-btn update-status-btn' data-id='" . $row['id'] . "' data-status='Confirmed'>Confirm</button> ";
                                echo "<button class='action-btn cancel-btn update-status-btn' data-id='" . $row['id'] . "' data-status='Cancelled'>Cancel</button>";
                            } else {
                                echo "<span>No actions</span>";
                            }
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='10'>No appointments found.</td></tr>"; 
                    }
                    ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
