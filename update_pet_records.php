<?php include('header.php'); ?>
<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "vetcare"; 

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "Please log in first.";
    exit;
}

$userId = $_SESSION['user_id']; // Fetch user ID from session

// Fetch pet record by ID for editing
if (isset($_GET['pet_id'])) {
    $petId = $_GET['pet_id'];
    
    // Get pet details from database
    $stmt = $conn->prepare("SELECT * FROM pet_records WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $petId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $pet = $result->fetch_assoc();
    } else {
        echo "Pet record not found.";
        exit;
    }

    // Handle form submission to update pet record
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $petName = $_POST['pet_name'];
        $petType = $_POST['pet_type'];
        $petAge = $_POST['pet_age'];
        $breed = $_POST['breed'];
        $healthStatus = $_POST['health_status'];
        $vaccinationDetails = $_POST['vaccination_details'];

        $updateStmt = $conn->prepare("UPDATE pet_records SET pet_name = ?, pet_type = ?, pet_age = ?, breed = ?, health_status = ?, vaccination_details = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $updateStmt->bind_param("ssisssi", $petName, $petType, $petAge, $breed, $healthStatus, $vaccinationDetails, $petId);

        if ($updateStmt->execute()) {
            echo "Pet record updated successfully!";
        } else {
            echo "Error updating pet record: " . $updateStmt->error;
        }
    }

    // Close statement
    $stmt->close();
} else {
    echo "No pet selected to update.";
    exit;
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Pet Record</title>
</head>
<body>
    <h1>Update Pet Record</h1>
    <form method="POST" action="">
        <label for="pet_name">Pet Name:</label>
        <input type="text" id="pet_name" name="pet_name" value="<?php echo htmlspecialchars($pet['pet_name']); ?>" required><br>

        <label for="pet_type">Pet Type:</label>
        <input type="text" id="pet_type" name="pet_type" value="<?php echo htmlspecialchars($pet['pet_type']); ?>" required><br>

        <label for="pet_age">Pet Age:</label>
        <input type="number" id="pet_age" name="pet_age" value="<?php echo htmlspecialchars($pet['pet_age']); ?>" required><br>

        <label for="breed">Breed:</label>
        <input type="text" id="breed" name="breed" value="<?php echo htmlspecialchars($pet['breed']); ?>"><br>

        <label for="health_status">Health Status:</label>
        <input type="text" id="health_status" name="health_status" value="<?php echo htmlspecialchars($pet['health_status']); ?>"><br>

        <label for="vaccination_details">Vaccination Details:</label>
        <textarea id="vaccination_details" name="vaccination_details"><?php echo htmlspecialchars($pet['vaccination_details']); ?></textarea><br>

        <button type="submit">Update Pet Record</button>
    </form>
</body>
</html>
