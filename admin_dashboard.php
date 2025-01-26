<?php include('header.php'); ?>
<?php
session_start();

// Ensure admin is logged in and has the correct role
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] != 'Admin') {
    header("Location: login.php");
    exit();
}

$adminName = $_SESSION['admin_name'];
$adminId = $_SESSION['admin_id'];
$doctorId = $_SESSION['doctor_id'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "vetcare1";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch total number of appointments
$stmtAppointments = $conn->prepare("SELECT COUNT(*) AS total_appointments FROM appointments WHERE doctor_id = ?");
$stmtAppointments->bind_param("i", $doctorId);
$stmtAppointments->execute();
$resultAppointments = $stmtAppointments->get_result();
$totalAppointments = $resultAppointments->fetch_assoc()['total_appointments'];

// Fetch total number of users
$stmtUsers = $conn->prepare("SELECT COUNT(DISTINCT u.id) AS total_users 
                             FROM users u
                             JOIN appointments a ON u.id = a.user_id
                             WHERE a.doctor_id = ?");
$stmtUsers->bind_param("i", $doctorId);
$stmtUsers->execute();
$resultUsers = $stmtUsers->get_result();
$totalUsers = $resultUsers->fetch_assoc()['total_users'];

// Fetch pending appointments
$stmtPendingAppointments = $conn->prepare("SELECT COUNT(*) AS pending_appointments FROM appointments WHERE status = 'Pending' AND doctor_id = ?");
$stmtPendingAppointments->bind_param("i", $doctorId);
$stmtPendingAppointments->execute();
$resultPendingAppointments = $stmtPendingAppointments->get_result();
$pendingAppointments = $resultPendingAppointments->fetch_assoc()['pending_appointments'];

// Fetch confirmed appointments
$stmtConfirmedAppointments = $conn->prepare("SELECT COUNT(*) AS confirmed_appointments FROM appointments WHERE status = 'Confirmed' AND doctor_id = ?");
$stmtConfirmedAppointments->bind_param("i", $doctorId);
$stmtConfirmedAppointments->execute();
$resultConfirmedAppointments = $stmtConfirmedAppointments->get_result();
$confirmedAppointments = $resultConfirmedAppointments->fetch_assoc()['confirmed_appointments'];

// Fetch appointment data for FullCalendar
$stmtCalendarData = $conn->prepare("SELECT appointment_date AS date, 
    COUNT(CASE WHEN status = 'Pending' THEN 1 END) AS pending, 
    COUNT(CASE WHEN status = 'Confirmed' THEN 1 END) AS confirmed 
    FROM appointments WHERE doctor_id = ? GROUP BY appointment_date");
$stmtCalendarData->bind_param("i", $doctorId);
$stmtCalendarData->execute();
$resultCalendarData = $stmtCalendarData->get_result();
$calendarData = [];
while ($row = $resultCalendarData->fetch_assoc()) {
    $calendarData[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin_dashboard.css">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar/main.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar/main.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: [
                    <?php foreach ($calendarData as $data) : ?>
                    {
                        title: 'Pending: <?= $data['pending'] ?> <br> Confirmed: <?= $data['confirmed'] ?>',
                        start: '<?= $data['date'] ?>',
                    },
                    <?php endforeach; ?>
                ],
                eventContent: function (arg) {
                    // Render custom content for events
                    return {
                        html: `<div style="text-align: center;">${arg.event.title}</div>`
                    };
                },
                height: 'auto',
                contentHeight: 'auto',
                windowResize: true,
                aspectRatio: 2,
            });
            calendar.render();
        });
    </script>
    <style>
        #calendar {
            max-width: 100%;
            margin: 40px auto;
            padding: 20px;
            background: lavender;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: black;
            background-image: url("images/duck.jpg");
        }
        .dashboard-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background: black;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        header {
            text-align: center;
            margin-bottom: 30px;
        }
        header h1 {
            font-size: 2rem;
            color: #f5c71a;
        }
        nav ul {
            list-style-type: none;
            padding: 0;
            display: flex;
            justify-content: center;
            gap: 20px;
        }
        nav ul li a {
            text-decoration: none;
            color: #4CAF50;
            font-weight: bold;
            padding: 10px 15px;
            border-radius: 5px;
        }
        nav ul li a:hover {
            background-color: #4CAF50;
            color: #fff;
        }
        .stats {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .stat {
            padding: 20px;
            background: #4CAF50;
            color: #fff;
            border-radius: 5px;
            text-align: center;
            flex: 1;
            margin: 0 10px;
        }
        .overview {
            margin-bottom: 30px;
        }
        h2 {
            color: #f5c71a;
            font-size: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <header>
            <h1>Welcome, <?php echo htmlspecialchars($adminName); ?>!</h1>
            <nav>
                <ul>
                    <li><a href="view_appointments1.php">View Appointments</a></li>
                    <li><a href="manage_users.php">Manage Users</a></li>
                    <li><a href="view_pets.php">Cattle Records</a></li>
                    <li><a href="admin_inquiry.php">Manage Inquiries</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </header>

        <main>
            <section class="overview">
                <h2>Dashboard Overview</h2>
                <div class="stats">
                    <div class="stat">
                        <h3>Total Appointments</h3>
                        <p><?php echo $totalAppointments; ?></p>
                    </div>
                    <div class="stat">
                        <h3>Total Users</h3>
                        <p><?php echo $totalUsers; ?></p>
                    </div>
                    <div class="stat">
                        <h3>Pending Appointments</h3>
                        <p><?php echo $pendingAppointments; ?></p>
                    </div>
                    <div class="stat">
                        <h3>Confirmed Appointments</h3>
                        <p><?php echo $confirmedAppointments; ?></p>
                    </div>
                </div>
            </section>

            <section id="calendar-section">
                <h2>Appointment Calendar</h2>
                <div id="calendar"></div>
            </section>
        </main>
    </div>
</body>
</html>
