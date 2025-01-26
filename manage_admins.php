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

// Fetch all admins
$resultAdmins = $conn->query("SELECT * FROM admins where Role='Admin'");

// Add new admin functionality
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_admin'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // hash the password
    $contact = $_POST['contact'];
    $specialization = $_POST['specialization'];
    $doctor_id = $_POST['doctor_id']; // Get the doctor ID
    $role = 'Admin'; // Default role

    // Check if email already exists
    $emailCheckQuery = "SELECT * FROM admins WHERE email = ?";
    $stmt = $conn->prepare($emailCheckQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo "<script>alert('Email already exists!');</script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO admins (name, email, password, contact, specialization, doctor_id, role) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $name, $email, $password, $contact, $specialization, $doctor_id, $role);
        $stmt->execute();
        $stmt->close();
        header("Location: manage_admins.php"); // Redirect to the same page to prevent re-submission
        exit();
    }
}

// Remove admin functionality
if (isset($_GET['remove_admin_id'])) {
    $removeAdminId = $_GET['remove_admin_id'];
    $conn->query("DELETE FROM admins WHERE id = $removeAdminId");
    header("Location: manage_admins.php"); // Redirect after deletion
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Admins</title>
    <link rel="stylesheet" href="super_admin_dashboard.css">
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
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        .admin-table th, .admin-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
            color: white;
            background-color: black;
        }
        .admin-table th {
            background-color: #4CAF50;
        }
        .admin-table tr:nth-child(even) {
            background-color: #333;
        }
        .admin-table tr:nth-child(odd) {
            background-color: #444;
        }
        .admin-table tr:hover {
            background-color: #555;
        }
        .btn {
            padding: 8px 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        .btn:hover {
            background-color: #45a049;
        }
        .form-container {
            margin-top: 30px;
            background-color: #333;
            padding: 20px;
            border-radius: 10px;
        }
        .form-container input, .form-container button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
        }
        .form-container input[type="text"], .form-container input[type="email"], .form-container input[type="password"] {
            background-color: #444;
            color: #fff;
        }
        .form-container button {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <header>
            <h1>Manage Admins</h1>
        </header>

        <main>
            <!-- Admin Table -->
            <section id="admin-table">
                <h2>Existing Admins</h2>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Sl No</th>
                            <th>Doctor ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th>Specialization</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $serialNo = 1;
                        while ($admin = $resultAdmins->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $serialNo++; ?></td>
                                <td><?php echo $admin['doctor_id']; ?></td>
                                <td><?php echo $admin['name']; ?></td>
                                <td><?php echo $admin['email']; ?></td>
                                <td><?php echo $admin['contact']; ?></td>
                                <td><?php echo $admin['specialization']; ?></td>
                                <td>
                                    <a href="?remove_admin_id=<?php echo $admin['id']; ?>" class="btn">Remove</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </section>

            <!-- Add New Admin Form -->
            <section id="add-admin-form">
                <h3>Add New Admin</h3>
                <div class="form-container">
                    <form method="POST" action="">
                        <input type="text" name="name" placeholder="Admin Name" required><br>
                        <input type="email" name="email" placeholder="Email" required><br>
                        <input type="password" name="password" placeholder="Password" required><br>
                        <input type="text" name="contact" placeholder="Contact" required><br>
                        <input type="text" name="specialization" placeholder="Specialization" required><br>
                        <input type="text" name="doctor_id" placeholder="Doctor ID" required><br>
                        <button type="submit" name="add_admin" class="btn">Add Admin</button>
                    </form>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
