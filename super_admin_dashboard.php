<?php include('header.php'); ?>
<?php
session_start();

// Ensure super admin is logged in
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] != 'Super Admin') {
    header("Location: login.php");
    exit();
}

$adminName = $_SESSION['admin_name'];

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "vetcare1";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch total users
$resultUsers = $conn->query("SELECT COUNT(*) AS total_users FROM users");
$totalUsers = $resultUsers->fetch_assoc()['total_users'];

// Fetch total appointments
$resultAppointments = $conn->query("SELECT COUNT(*) AS total_appointments FROM appointments");
$totalAppointments = $resultAppointments->fetch_assoc()['total_appointments'];

// Fetch pending appointments
$resultPending = $conn->query("SELECT COUNT(*) AS pending_appointments FROM appointments WHERE status = 'Pending'");
$pendingAppointments = $resultPending->fetch_assoc()['pending_appointments'];

// Fetch confirmed appointments
$resultConfirmed = $conn->query("SELECT COUNT(*) AS confirmed_appointments FROM appointments WHERE status = 'Confirmed'");
$confirmedAppointments = $resultConfirmed->fetch_assoc()['confirmed_appointments'];

// Fetch appointments by date for displaying on calendar
$resultAppointmentsByDate = $conn->query("SELECT DATE(appointment_date) AS appointment_date, 
    SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) AS pending_count, 
    SUM(CASE WHEN status = 'Confirmed' THEN 1 ELSE 0 END) AS confirmed_count
    FROM appointments GROUP BY DATE(appointment_date)");

$appointmentsData = [];
while ($row = $resultAppointmentsByDate->fetch_assoc()) {
    $appointmentsData[] = [
        'date' => $row['appointment_date'],
        'pending_count' => $row['pending_count'],
        'confirmed_count' => $row['confirmed_count']
    ];

}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard</title>
    <link rel="stylesheet" href="super_admin_dashboard.css">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar/main.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar/main.min.js"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: black;
            background-image: url("images/duck.jpg");
            background-size: cover;
            background-position: center;
            color: #fff;
        }
        .dashboard-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background: black;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
        }
        header {
            text-align: center;
            margin-bottom: 30px;
        }
        header h1 {
            font-size: 2.5rem;
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
            font-size: 1.8rem;
        }
        #calendar {
            max-width: 100%;
            margin: 40px auto;
            padding: 20px;
            background: lavender;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .fc-daygrid-day-number {
            color: black !important;
        }
        .fc-event-title {
            color: black !important;
        }
        .fc-daygrid-day-top {
            color: black !important;
        }
        .fc-col-header-cell {
            color: black !important;
        }
        .fc-col-header-cell-cushion {
            color: black !important;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <header>
            <h1>Welcome, <?php echo htmlspecialchars($adminName); ?>!</h1>
            <nav>
                <ul>
                    <li><a href="manage_admins.php">Manage Admins</a></li>
                    <li><a href="manage_users1.php">Manage Users</a></li>
                    <li><a href="manage_pets1.php">Manage Pets</a></li>
                    <li><a href="manage_services.php">Manage Services</a></li>
                    <li><a href="manage_accounts.php">Manage Accounts</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </header>

        <main>
            <section class="overview">
                <h2>Dashboard Overview</h2>
                <div class="stats">
                    <div class="stat">
                        <h3>Total Users</h3>
                        <p><?php echo $totalUsers; ?></p>
                    </div>
                    <div class="stat">
                        <h3>Total Appointments</h3>
                        <p><?php echo $totalAppointments; ?></p>
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('calendar');
            var eventsData = <?php echo json_encode($appointmentsData); ?>; // Passing data from PHP to JavaScript

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: eventsData.map(function (appointment) {
                    // Adding confirmed and pending counts to the calendar as events
                    var event = [];
                    if (appointment.pending_count > 0) {
                        event.push({
                            title: `Pending: ${appointment.pending_count}`,
                            start: appointment.date,
                            color: 'orange', // Customize the color
                        });
                    }
                    if (appointment.confirmed_count > 0) {
                        event.push({
                            title: `Confirmed: ${appointment.confirmed_count}`,
                            start: appointment.date,
                            color: 'green', // Customize the color
                        });
                    }
                    return event;
                }).flat(),
            });
            calendar.render();
        });
    </script>
</body>
</html>
