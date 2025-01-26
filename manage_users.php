<?php include('header.php'); ?>
<?php
session_start();
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] != 'Admin') {
    header("Location: login.php");
    exit();
}

$adminId = $_SESSION['admin_id'];
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

// Add User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $userName = $_POST['user_name'];
    $userEmail = $_POST['user_email'];
    $userPassword = password_hash($_POST['user_password'], PASSWORD_DEFAULT);
    $userContact = $_POST['user_contact']; // Added contact field

    // Check if the email already exists
    $checkEmailStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkEmailStmt->bind_param("s", $userEmail);
    $checkEmailStmt->execute();
    $checkEmailStmt->store_result();

    if ($checkEmailStmt->num_rows > 0) {
        $errorMessage = "Email already exists!";
        $checkEmailStmt->close();
    } else {
        $checkEmailStmt->close();
        // Proceed to insert user if email is unique
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, contact, doctor_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $userName, $userEmail, $userPassword, $userContact, $adminId); // Include contact in bind_param
        if ($stmt->execute()) {
            $successMessage = "User added successfully!";
        } else {
            $errorMessage = "Error adding user.";
        }
        $stmt->close();
    }
}

// Remove User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_user'])) {
    $userId = $_POST['user_id'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND doctor_id = ?");
    $stmt->bind_param("ii", $userId, $adminId);
    if ($stmt->execute()) {
        $successMessage = "User removed successfully!";
    } else {
        $errorMessage = "Error removing user.";
    }
    $stmt->close();
}

// Fetch Users
$stmt = $conn->prepare("SELECT * FROM users WHERE doctor_id = ?");
$stmt->bind_param("i", $adminId);
$stmt->execute();
$usersResult = $stmt->get_result();
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url("images/duck.jpg");
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: black;
            padding: 30px;
            border-radius: 8px;
            background-color: rgba(0, 0, 0, 0.7);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);color:  #f5c71a;
        }
        h1 {
            text-align: center;
            color:  #f5c71a;
            font-size: 36px;
            text-transform: uppercase;
            background-color: rgba(0, 0, 0, 0.7);
        }
        .message {
            text-align: center;
            margin-bottom: 20px;
            color: green;
        }
        .error {
            color: red;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
            color:white;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        form {
            margin-bottom: 20px;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
        .table-container {
            overflow-x: auto;
        }
        .table-container table {
            width: 100%;
            min-width: 800px;
        }
        .eye-icon {
            position: absolute;
            right: 10px;
            top: 10px;
            cursor: pointer;
            color: #4CAF50;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Users</h1>

        <?php if (isset($successMessage)) { ?>
            <p class="message"><?php echo $successMessage; ?></p>
        <?php } elseif (isset($errorMessage)) { ?>
            <p class="message error"><?php echo $errorMessage; ?></p>
        <?php } ?>

        <form method="POST" action="">
            <h2>Add New User</h2>
            <input type="text" name="user_name" placeholder="User Name" required>
            <input type="email" name="user_email" placeholder="User Email" required>
            <div style="position: relative;">
                <input type="password" id="user_password" name="user_password" placeholder="User Password" maxlength="8" required>
                <span class="eye-icon" id="togglePassword" onclick="togglePassword()">👁️</span>
            </div>
            <input type="text" name="user_contact" placeholder="User Contact" required> <!-- Contact Field -->
            <button type="submit" name="add_user">Add User</button>
        </form>

        <h2>Existing Users</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Serial No.</th>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Contact</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $serialNo = 1; // Start serial number at 1
                    while ($row = $usersResult->fetch_assoc()) {
                    ?>
                        <tr>
                            <td><?php echo $serialNo++; ?></td> <!-- Display serial number -->
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['contact']); ?></td>
                            <td>
                                <form method="POST" action="" style="display: inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="remove_user">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Toggle password visibility
        function togglePassword() {
            var passwordField = document.getElementById("user_password");
            var toggleIcon = document.getElementById("togglePassword");
            if (passwordField.type === "password") {
                passwordField.type = "text";
                toggleIcon.textContent = "🙈"; // Change icon to closed eye
            } else {
                passwordField.type = "password";
                toggleIcon.textContent = "👁️"; // Change icon to open eye
            }
        }
    </script>
</body>
</html>
