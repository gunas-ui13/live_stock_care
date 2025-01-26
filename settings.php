<?php include('header.php'); ?>
<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "vetcare1";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$messageProfile = ""; // Variable to hold success or error messages for profile
$messagePassword = ""; // Variable to hold success or error messages for password

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // Fetch current user details to prefill the form
    $result = $conn->query("SELECT * FROM users WHERE id='$userId'");
    $user = $result->fetch_assoc();

    // Update profile details (Separate form processing)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
        $newName = mysqli_real_escape_string($conn, $_POST['name']);
        $newEmail = mysqli_real_escape_string($conn, $_POST['email']);
        $newPhone = mysqli_real_escape_string($conn, $_POST['phone']);
        
        // Ensure the email is unique in the table
        $checkEmail = $conn->query("SELECT * FROM users WHERE email='$newEmail' AND id != '$userId'");
        if ($checkEmail->num_rows > 0) {
            $messageProfile = "This email is already taken.";
        } else {
            $sql = "UPDATE users SET name='$newName', email='$newEmail', contact='$newPhone' WHERE id='$userId'";

            if ($conn->query($sql) === TRUE) {
                $messageProfile = "User details updated successfully!";
                // Update session data for the changes made
                $_SESSION['user_name'] = $newName;
                $_SESSION['user_email'] = $newEmail;
            } else {
                $messageProfile = "Error: " . $conn->error;
            }
        }
    }

    // Change password (Separate form processing)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
        $currentPassword = mysqli_real_escape_string($conn, $_POST['current_password']);
        $newPassword = mysqli_real_escape_string($conn, $_POST['new_password']);
        $confirmPassword = mysqli_real_escape_string($conn, $_POST['confirm_password']);

        $result = $conn->query("SELECT password FROM users WHERE id='$userId'");
        $user = $result->fetch_assoc();

        if (password_verify($currentPassword, $user['password'])) {
            if ($newPassword === $confirmPassword) {
                // Password strength check (e.g., at least 8 characters, etc.)
                if (strlen($newPassword) < 8) {
                    $messagePassword = "New password must be at least 8 characters long.";
                } else {
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    $sql = "UPDATE users SET password='$hashedPassword' WHERE id='$userId'";

                    if ($conn->query($sql) === TRUE) {
                        $messagePassword = "Password changed successfully!";
                    } else {
                        $messagePassword = "Error: " . $conn->error;
                    }
                }
            } else {
                $messagePassword = "New passwords do not match.";
            }
        } else {
            $messagePassword = "Current password is incorrect.";
        }
    }
} else {
    $messageProfile = "Please log in first.";
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Settings</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #000; /* Black background */
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: url('images/duck.jpg') no-repeat center center/cover;
            background-size: cover;
        }
        .container {
            background-color: rgba(0, 0, 0, 0.7); /* Transparent background */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 800px;
        }
        h2 {
            text-align: center;
            color: #f5c71a; /* Golden color for header */
        }
        form {
            display: flex;
            flex-direction: column;
            margin-bottom: 30px;
        }
        label {
            margin: 10px 0 5px;
            font-weight: bold;
            color: #f5c71a; /* Golden color for labels */
        }
        input, button {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #f5c71a;
            color: black;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #e5b810;
        }
        .message {
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.1em;
            padding: 10px;
            border-radius: 5px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>User Settings</h2>

        <!-- Display success or error message for profile update -->
        <?php if ($messageProfile != ""): ?>
            <div class="message <?php echo strpos($messageProfile, 'Error') === false ? 'success' : 'error'; ?>">
                <?php echo $messageProfile; ?>
            </div>
        <?php endif; ?>

        <!-- Update Profile Form -->
        <form action="" method="POST">
            <label for="name">Full Name:</label>
            <input type="text" name="name" value="<?php echo isset($user['name']) ? $user['name'] : ''; ?>" required>
            
            <label for="email">Email:</label>
            <input type="email" name="email" value="<?php echo isset($user['email']) ? $user['email'] : ''; ?>" required>
            
            <label for="phone">Phone:</label>
            <input type="text" name="phone" value="<?php echo isset($user['contact']) ? $user['contact'] : ''; ?>" required>
            
            <button type="submit" name="update_profile">Update Profile</button>
        </form>

        <hr>

        <!-- Display success or error message for password change -->
        <?php if ($messagePassword != ""): ?>
            <div class="message <?php echo strpos($messagePassword, 'Error') === false ? 'success' : 'error'; ?>">
                <?php echo $messagePassword; ?>
            </div>
        <?php endif; ?>

        <!-- Change Password Form -->
        <form action="" method="POST">
            <label for="current_password">Current Password:</label>
            <input type="password" name="current_password" required>

            <label for="new_password">New Password:</label>
            <input type="password" name="new_password" required>

            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" name="confirm_password" required>

            <button type="submit" name="change_password">Change Password</button>
        </form>
    </div>
</body>
</html>
