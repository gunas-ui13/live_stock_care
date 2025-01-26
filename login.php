<?php include('header.php'); ?>
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start(); // Start the session

// Database connection settings
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

if (isset($_POST['submit'])) {
    // Sanitize form data to prevent SQL Injection
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);

    // Query to check if the email exists in the admins table
    $stmt = $conn->prepare("SELECT * FROM admins WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $adminResult = $stmt->get_result();

    if ($adminResult->num_rows > 0) {
        // Admin found
        $admin = $adminResult->fetch_assoc();
        
        // Verify the password
        if (password_verify($password, $admin['password'])) {
            // Store admin info in session
            $_SESSION['role'] = $admin['role']; // Save admin role (Admin or Super Admin)
            $_SESSION['admin_id'] = $admin['id']; // Admin's table ID
            $_SESSION['doctor_id'] = $admin['doctor_id']; // Admin's doctor ID
            $_SESSION['admin_name'] = $admin['name']; // Admin's name
            
            // Redirect to the appropriate dashboard based on the role
            if ($admin['role'] === 'Super Admin') {
                header("Location: super_admin_dashboard.php");
            } else {
                header("Location: admin_dashboard.php");
            }
            exit();
        } else {
            echo "Invalid password!";
        }
    } else {
        // Admin not found, check in users table
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $userResult = $stmt->get_result();

        if ($userResult->num_rows > 0) {
            // User found
            $user = $userResult->fetch_assoc();
            
            // Verify the password for user
            if (password_verify($password, $user['password'])) {
                // Store user info in session
                $_SESSION['role'] = 'user'; // Set role to user
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                
                // Redirect to the user dashboard
                header("Location: dashboard.php");
                exit();
            } else {
                echo "Invalid password!";
            }
        } else {
            echo "No account found with that email!";
        }
    }
}

// Close the database connection
$conn->close();
?>
