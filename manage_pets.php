<?php include('header.php'); ?>
<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "vetcare1";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "Please log in to manage your pet records.";
    exit;
}

$userId = $_SESSION['user_id']; // Fetch user ID from session
$message = ""; // To store success/error messages

// Handling form submission for adding a new pet record
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_pet'])) {
    $petName = $conn->real_escape_string($_POST['pet_name']);
    $petType = $conn->real_escape_string($_POST['pet_type']);
    $petAge = (int)$_POST['pet_age'];
    $healthStatus = $conn->real_escape_string($_POST['health_status']);
    $vaccinationDetails = $conn->real_escape_string($_POST['vaccination_details']);

    $stmt = $conn->prepare("INSERT INTO pet_records (user_id, pet_name, pet_type, pet_age, health_status, vaccination_details) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ississ", $userId, $petName, $petType, $petAge, $healthStatus, $vaccinationDetails);
    
    if ($stmt->execute()) {
        $message = "Pet record added successfully!";
    } else {
        $message = "Error adding pet record: " . $stmt->error;
    }
    
    $stmt->close();
}

// Handling form submission for updating a pet record
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_pet'])) {
    $petId = (int)$_POST['pet_id'];
    $petName = $conn->real_escape_string($_POST['pet_name']);
    $petType = $conn->real_escape_string($_POST['pet_type']);
    $petAge = (int)$_POST['pet_age'];
    $healthStatus = $conn->real_escape_string($_POST['health_status']);
    $vaccinationDetails = $conn->real_escape_string($_POST['vaccination_details']);

    $stmt = $conn->prepare("UPDATE pet_records SET pet_name = ?, pet_type = ?, pet_age = ?, health_status = ?, vaccination_details = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ssissii", $petName, $petType, $petAge, $healthStatus, $vaccinationDetails, $petId, $userId);

    if ($stmt->execute()) {
        $message = "Pet record updated successfully!";
    } else {
        $message = "Error updating pet record: " . $stmt->error;
    }
    
    $stmt->close();
}

// Fetch pet records for the logged-in user
$petRecords = $conn->query("SELECT * FROM pet_records WHERE user_id = $userId");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Pet Records</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f2f2f2; margin: 0; padding: 20px; background-image: url("images/duck.jpg"); }
        .container { max-width: 800px; margin: auto; padding: 20px; background-color: rgba(0, 0, 0, 0.7); border-radius: 8px; box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1); }
        h1 { text-align: center; color: #f5c71a; }
        form { margin-bottom: 20px; }
        h2{color: #f5c71a;  }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #f5c71a; }
        input, textarea, button { width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px; }
        button { background-color: #4CAF50; color: white; cursor: pointer; }
        button:hover { background-color: #45a049; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        .message { text-align: center; margin-bottom: 20px; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add Records</h1>

        <!-- Display messages -->
        <?php if ($message): ?>
            <p class="message <?php echo strpos($message, 'Error') === false ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </p>
        <?php endif; ?>

        <!-- Add Pet Record Form -->
        <form method="POST" action="">
            <input type="hidden" name="add_pet" value="1">
            <label for="pet_name">Pet Name:</label>
            <input type="text" id="pet_name" name="pet_name" required>

            <label for="pet_type">Pet Type:</label>
            <select id="pet_type" name="pet_type" required>
                <option value="Duck">Duck</option>
                <option value="Donkey">Donkey</option>
                <option value="Dog">Dog</option>
                <option value="Cat">Cat</option>
                <option value="Horse">Horse</option>
                <option value="Rabbit">Rabbit</option>
                <!-- Add other pet types as needed -->
            </select>

            <label for="pet_age">Pet Age:</label>
            <input type="number" id="pet_age" name="pet_age" required>

            <label for="health_status">Health Status:</label>
            <input type="text" id="health_status" name="health_status">

            <label for="vaccination_details">Vaccination Details:</label>
            <textarea id="vaccination_details" name="vaccination_details"></textarea>

            <button type="submit">Add Pet</button>
        </form>

        <!-- Existing Pet Records -->
        <?php if ($petRecords->num_rows > 0): ?>
            <h2>Your Pet Records</h2>
            <table>
                <thead>
                    <tr>
                        <th>Pet Name</th>
                        <th>Type</th>
                        <th>Age</th>
                        <th>Health</th>
                        <th>Vaccination</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($pet = $petRecords->fetch_assoc()): ?>
                        <tr>
                            <form method="POST" action="">
                                <input type="hidden" name="pet_id" value="<?php echo $pet['id']; ?>">
                                <td><input type="text" name="pet_name" value="<?php echo htmlspecialchars($pet['pet_name']); ?>"></td>
                                <td>
                                    <select name="pet_type" required>
                                        <option value="Duck" <?php echo $pet['pet_type'] == 'Duck' ? 'selected' : ''; ?>>Duck</option>
                                        <option value="Donkey" <?php echo $pet['pet_type'] == 'Donkey' ? 'selected' : ''; ?>>Donkey</option>
                                        <option value="Dog" <?php echo $pet['pet_type'] == 'Dog' ? 'selected' : ''; ?>>Dog</option>
                                        <option value="Cat" <?php echo $pet['pet_type'] == 'Cat' ? 'selected' : ''; ?>>Cat</option>
                                        <option value="Horse" <?php echo $pet['pet_type'] == 'Horse' ? 'selected' : ''; ?>>Horse</option>
                                        <option value="Rabbit" <?php echo $pet['pet_type'] == 'Rabbit' ? 'selected' : ''; ?>>Rabbit</option>
                                    </select>
                                </td>
                                <td><input type="number" name="pet_age" value="<?php echo htmlspecialchars($pet['pet_age']); ?>"></td>
                                <td><input type="text" name="health_status" value="<?php echo htmlspecialchars($pet['health_status']); ?>"></td>
                                <td><input type="text" name="vaccination_details" value="<?php echo htmlspecialchars($pet['vaccination_details']); ?>"></td>
                                <td><button type="submit" name="update_pet">Update</button></td>
                            </form>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No pet records found.</p>
        <?php endif; ?>
    </div>
</body>
</html> 