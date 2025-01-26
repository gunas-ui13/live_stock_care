<?php include('header.php'); ?>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Database connection
    $conn = new mysqli("localhost", "root", "", "vetcare");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Sanitize and collect form data
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $contact = $conn->real_escape_string($_POST['contact']);
    $clinic_name = $conn->real_escape_string($_POST['clinic_name']);
    $specialization = $conn->real_escape_string($_POST['specialization']);

    // Insert new admin
    $query = "INSERT INTO admins (name, email, password, contact, clinic_name, specialization) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssss", $name, $email, $password, $contact, $clinic_name, $specialization);

    if ($stmt->execute()) {
        header("Location: manage_admins.php");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add New Admin</title>
</head>
<body>
    <h1>Add New Admin</h1>
    <form method="POST">
        <label>Name:</label>
        <input type="text" name="name" required><br>
        <label>Email:</label>
        <input type="email" name="email" required><br>
        <label>Password:</label>
        <input type="password" name="password" required><br>
        <label>Contact:</label>
        <input type="text" name="contact" required><br>
        <label>Clinic Name:</label>
        <input type="text" name="clinic_name" required><br>
        <label>Specialization:</label>
        <input type="text" name="specialization" required><br>
        <button type="submit">Add Admin</button>
    </form>
</body>
</html>
