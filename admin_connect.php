<?php include('header.php'); ?>
<?php
// Database connection
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
    // Sanitize and collect input
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);
    $contact = $conn->real_escape_string($_POST['contact']);
    $clinic_name = $conn->real_escape_string($_POST['clinic_name']);
    $doctor_id = $conn->real_escape_string($_POST['doctor_id']);
    $specialization = $conn->real_escape_string($_POST['specialization']);

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if the email already exists
    $checkEmailQuery = "SELECT * FROM admins WHERE email = ?";
    $stmt = $conn->prepare($checkEmailQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "This email is already registered. Please use a different email.";
    } else {
        // Insert the admin data
        $insertQuery = "INSERT INTO admins (name, email, password, contact, clinic_name, doctor_id, specialization) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("sssssss", $name, $email, $hashed_password, $contact, $clinic_name, $doctor_id, $specialization);

        if ($stmt->execute()) {
            header("Location: login.html");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    $stmt->close();
}

$conn->close();
?>
