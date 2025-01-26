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

// Fetch all users
$resultUsers = $conn->query("SELECT * FROM users");

// Add new user functionality
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // hash the password
    $contact = $_POST['contact'];

    // Check if the email already exists
    $emailCheckQuery = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($emailCheckQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $error_message = "Email already exists!";
    } else {
        // Insert the new user
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, contact) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $password, $contact);
        $stmt->execute();
        $stmt->close();
        header("Location: manage_users.php"); // Redirect to the same page to prevent re-submission
        exit();
    }
    $stmt->close();
}

// Remove user functionality
if (isset($_GET['remove_user_id'])) {
    $removeUserId = $_GET['remove_user_id'];
    $conn->query("DELETE FROM users WHERE id = $removeUserId");
    header("Location: manage_users.php"); // Redirect after deletion
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
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
        .user-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        .user-table th, .user-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
            color: white;
            background-color: black;
        }
        .user-table th {
            background-color: #4CAF50;
            color: white;
        }
        .user-table tr:nth-child(even) {
            background-color: #333;
        }
        .user-table tr:nth-child(odd) {
            background-color: #444;
        }
        .user-table tr:hover {
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
        .error-message {
            color: red;
            font-weight: bold;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <header>
            <h1>Manage Users</h1>
        </header>

        <main>
            <!-- User Table -->
            <section id="user-table">
                <h2>Existing Users</h2>
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>Sl No</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $serialNo = 1;
                        while ($user = $resultUsers->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $serialNo++; ?></td>
                                <td><?php echo $user['name']; ?></td>
                                <td><?php echo $user['email']; ?></td>
                                <td><?php echo $user['contact']; ?></td>
                                <td>
                                    <a href="?remove_user_id=<?php echo $user['id']; ?>" class="btn">Remove</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </section>

            <!-- Add New User Form -->
            <section id="add-user-form">
                <h3>Add New User</h3>
                <div class="form-container">
                    <!-- Display Error Message if Email Exists -->
                    <?php if (isset($error_message)): ?>
                        <div class="error-message"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    <form method="POST" action="">
                        <input type="text" name="name" placeholder="User Name" required><br>
                        <input type="email" name="email" placeholder="Email" required><br>
                        <input type="password" name="password" placeholder="Password" required><br>
                        <input type="text" name="contact" placeholder="Contact" required><br>
                        <button type="submit" name="add_user" class="btn">Add User</button>
                    </form>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
