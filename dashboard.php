<?php include('header.php'); ?>
<?php
session_start();
if (isset($_SESSION['user_id'])) {
    // Retrieve user data from session
    $user_id = $_SESSION['user_id'];

    // Connect to the database
    $conn = new mysqli('localhost', 'root', '', 'vetcare1');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch user details
    $sql = "SELECT name, email, contact FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $name = $user['name'];
        $email = $user['email'];
        $contact = $user['contact'];
    } else {
        $name = $email = $contact = "N/A";
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>
        /* Your existing styles remain unchanged */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #000;
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
            opacity: 0.2;
            z-index: -1;
        }

        .content h2 {
            color: #f5c71a;
            margin-bottom: 20px;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .user-details {
            display: none;
            background: #202020;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }

        .user-details div {
            margin-bottom: 10px;
            padding: 10px;
            background: #303030;
            border-radius: 5px;
        }

        button {
            padding: 10px 20px;
            background: #f5c71a;
            color: #000;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background: #e5b810;
        }

        a.add-pet-link {
            color: #f5c71a;
            text-decoration: underline;
            cursor: pointer;
            background: none;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>User Dashboard</h2>
        <ul>
            <li><a href="javascript:void(0)" onclick="toggleUserDetails()">Dashboard Overview</a></li>
            <li><a href="appointment.php">Appointments</a></li>
            <li><a href="view_appointment.php">View Appointments</a></li>
            <li><a href="medical_reports.php">Medical Reports</a></li>
            <li><a href="billing.php">Billing & Payments</a></li>
            <li><a href="settings.php">Settings</a></li>
            <li><a href="view_inquiries.php">Inquiries</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="content">
        <div id="user-details" class="user-details">
            <h3>User Profile</h3>
            <div><strong>Name:</strong> <?php echo htmlspecialchars($name); ?></div>
            <div><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></div>
            <div><strong>Contact:</strong> <?php echo htmlspecialchars($contact); ?></div>
        </div>

        <h2>
            <a class="add-pet-link" href="manage_pets.php">Add Your Pet Details for Specialized Care</a>
        </h2>
    </div>

    <script>
        function toggleUserDetails() {
            var userDetails = document.getElementById('user-details');
            userDetails.style.display = userDetails.style.display === 'block' ? 'none' : 'block';
        }
    </script>
</body>
</html>
