<?php include('header.php'); ?>
<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "vetcare");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get admin details
$id = $_GET['id'];
$query = "SELECT * FROM admins WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $contact = $conn->real_escape_string($_POST['contact']);
    $clinic_name = $conn->real_escape_string($_POST['clinic_name']);
    $specialization = $conn->real_escape_string($_POST['specialization']);

    $updateQuery = "UPDATE admins SET name = ?, email = ?, contact = ?, clinic_name = ?, specialization = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sssssi", $name, $email, $contact, $clinic_name, $specialization, $id);

    if ($stmt->execute()) {
        header("Location: manage_admins.php");
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Admin</title>
</head>
<body>
    <h1>Edit Admin</h1>
    <form method="POST">
        <label>Name:</label>
        <input type="text" name="name" value="<?php echo $admin['name']; ?>" required><br>
        <label>Email:</label>
        <input type="email" name="email" value="<?php echo $admin['email']; ?>" required><br>
        <label>Contact:</label>
        <input type="text" name="contact" value="<?php echo $admin['contact']; ?>" required><br>
        <label>Clinic Name:</label>
        <input type="text" name="clinic_name" value="<?php echo $admin['clinic_name']; ?>" required><br>
        <label>Specialization:</label>
        <input type="text" name="specialization" value="<?php echo $admin['specialization']; ?>" required><br>
        <button type="submit">Update Admin</button>
    </form>
</body>
</html>
