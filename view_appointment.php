<?php include('header.php'); ?>
<?php
// Include database connection
require_once 'db.php'; // Update this path if needed

// Fetch user ID (assuming it's stored in the session)
session_start();
$userId = $_SESSION['user_id']; // Adjust if user ID is fetched differently

// Prepare the SQL query with formatted dates
$stmt = $conn->prepare("
    SELECT 
        a.id, 
        DATE_FORMAT(a.appointment_date, '%d/%m/%Y') AS formatted_date, 
        a.appointment_time, 
        a.reason, 
        p.pet_name, 
        p.pet_type, 
        a.status, 
        DATE_FORMAT(a.created_at, '%d/%m/%Y %H:%i:%s') AS formatted_created_at, 
        d.name AS doctor_name
    FROM 
        appointments a
    LEFT JOIN 
        pet_records p ON a.pet_id = p.id  -- Link appointments to pet_records via pet_id
    LEFT JOIN 
        admins d ON a.doctor_id = d.doctor_id
    WHERE 
        a.user_id = ? 
    ORDER BY 
        a.appointment_date DESC, 
        a.appointment_time DESC
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Appointments</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #000; /* Black background */
            color: #fff;
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 20%;
            background-color: rgba(0, 0, 0, 0.9);
            padding: 20px;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.5);
        }

        .sidebar h2 {
            text-align: center;
            color: #f5c71a;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            margin-bottom: 15px;
        }

        .sidebar ul li a {
            color: #fff;
            text-decoration: none;
            font-size: 16px;
            padding: 10px;
            display: block;
            border-radius: 5px;
            transition: background 0.3s, color 0.3s;
        }

        .sidebar ul li a:hover {
            background: #f5c71a;
            color: #000;
        }

        .content {
            flex: 1;
            padding: 30px;
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        .content::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('images/duck.jpg') no-repeat center center/cover;
            opacity: 0.2; /* Faint background image */
            z-index: -1;
        }

        .content h2 {
            color: #f5c71a;
            margin-bottom: 20px;
            text-align: center;
            position: relative;
            z-index: 1; /* Ensures text is above the background image */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #303030;
            position: relative;
            z-index: 1;
        }

        table, th, td {
            border: 1px solid #555;
        }

        th {
            background: #444;
            color: #f5c71a;
            text-align: left;
            padding: 12px;
        }

        td {
            padding: 12px;
            color: #fff;
        }

        td a {
            color: #f5c71a;
            text-decoration: none;
        }

        td a:hover {
            text-decoration: underline;
        }

        /* Status colors */
        .status-confirmed {
            color: green;
            font-weight: bold;
        }

        .status-pending {
            color: yellow;
            font-weight: bold;
        }

        .status-cancelled {
            color: red;
            font-weight: bold;
        }

        .message {
            margin-top: 10px;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>User Dashboard</h2>
        <ul>
            <li><a href="dashboard.php">Dashboard Overview</a></li>
            <li><a href="appointment.php">Book Appointment</a></li>
            <li><a href="view_appointment.php">View Appointments</a></li>
            <li><a href="medical_reports.php">Medical Reports</a></li>
            <li><a href="billing.php">Billing & Payments</a></li>
            <li><a href="settings.php">Settings</a></li>
            <li><a href="view_inquiries.php">Inquiries</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="content">
        <h2>My Appointments</h2>

        <!-- Display success or error message -->
        <?php if ($result->num_rows == 0): ?>
            <p>No appointments found.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Sl. No.</th>
                        <th>Appointment Date</th>
                        <th>Time</th>
                        <th>Reason</th>
                        <th>Pet Name</th>
                        <th>Pet Type</th>
                        <th>Doctor</th>
                        <th>Status</th>
                        <th>Booked On</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $serialNumber = 1; // Initialize serial number
                    while ($row = $result->fetch_assoc()): 
                    ?>
                        <tr>
                            <td><?php echo $serialNumber++; ?></td> <!-- Increment serial number -->
                            <td><?php echo htmlspecialchars($row['formatted_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['appointment_time']); ?></td>
                            <td><?php echo htmlspecialchars($row['reason']); ?></td>
                            <td><?php echo htmlspecialchars($row['pet_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['pet_type']); ?></td>
                            <td><?php echo htmlspecialchars($row['doctor_name']); ?></td>
                            <td class="<?php echo 'status-' . htmlspecialchars(strtolower($row['status'])); ?>">
                                <?php echo htmlspecialchars($row['status']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['formatted_created_at']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

</body>
</html>

<?php
// Close the statement and connection
$stmt->close();
$conn->close();
?>
