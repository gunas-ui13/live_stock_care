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
    echo "Please log in to view your pet records.";
    exit;
}

$userId = $_SESSION['user_id']; // Fetch user ID from session

// Fetch user's pet records
$stmt = $conn->prepare("SELECT pet_name, pet_type, pet_age, breed, health_status, vaccination_details, created_at, updated_at FROM pet_records WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

// Check if records exist
if ($result->num_rows > 0) {
    echo "<h1>Your Pet Records</h1>";
    echo "<table border='1'>
            <tr>
                <th>Pet Name</th>
                <th>Pet Type</th>
                <th>Pet Age</th>
                <th>Breed</th>
                <th>Health Status</th>
                <th>Vaccination Details</th>
                <th>Created At</th>
                <th>Updated At</th>
            </tr>";
    
    // Display pet records
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['pet_name']) . "</td>
                <td>" . htmlspecialchars($row['pet_type']) . "</td>
                <td>" . htmlspecialchars($row['pet_age']) . "</td>
                <td>" . htmlspecialchars($row['breed']) . "</td>
                <td>" . htmlspecialchars($row['health_status']) . "</td>
                <td>" . htmlspecialchars($row['vaccination_details']) . "</td>
                <td>" . htmlspecialchars($row['created_at']) . "</td>
                <td>" . htmlspecialchars($row['updated_at']) . "</td>
              </tr>";
    }
    
    echo "</table>";
} else {
    echo "You don't have any pet records yet.";
}

$stmt->close();
$conn->close();
?>
