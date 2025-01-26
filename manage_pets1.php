<?php include('header.php'); ?>
<?php
session_start();

// Ensure user is logged in and has the 'Super Admin' role
if (!isset($_SESSION['doctor_id']) || $_SESSION['role'] != 'Super Admin') {
    header("Location: login.php"); // Redirect if not logged in or not a Super Admin
    exit();
}

$doctorId = $_SESSION['doctor_id']; // Super Admin ID from session

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "vetcare1";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all pets for the super admin (or any other query as per the requirement)
$query = "SELECT * FROM pet_records"; // No user_id condition for Super Admin
$resultPets = $conn->query($query);

// Add new pet functionality
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_pet'])) {
    $pet_name = $_POST['pet_name'];
    $pet_type = $_POST['pet_type'];
    $pet_age = $_POST['pet_age'];
    $health_status = $_POST['health_status'];
    $vaccination_details = $_POST['vaccination_details'];

    // Insert the new pet
    $stmt = $conn->prepare("INSERT INTO pet_records (pet_name, pet_type, pet_age, health_status, vaccination_details) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiss", $pet_name, $pet_type, $pet_age, $health_status, $vaccination_details);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_pets.php"); // Redirect to the same page to prevent re-submission
    exit();
}

// Remove pet functionality
if (isset($_GET['remove_pet_id'])) {
    $removePetId = $_GET['remove_pet_id'];
    $conn->query("DELETE FROM pet_records WHERE id = $removePetId");
    header("Location: manage_pets.php"); // Redirect after deletion
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Pets</title>
    <link rel="stylesheet" href="user_dashboard.css">
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
        .pet-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        .pet-table th, .pet-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
            color: white;
            background-color: black;
        }
        .pet-table th {
            background-color: #4CAF50;
            color: white;
        }
        .pet-table tr:nth-child(even) {
            background-color: #333;
        }
        .pet-table tr:nth-child(odd) {
            background-color: #444;
        }
        .pet-table tr:hover {
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
        .form-container input[type="text"], .form-container input[type="number"] {
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
            <h1>Manage Pets</h1>
        </header>

        <main>
            <!-- Pet Table -->
            <section id="pet-table">
                <h2>Existing Pets</h2>
                <table class="pet-table">
                    <thead>
                        <tr>
                            <th>Sl No</th>
                            <th>Pet Name</th>
                            <th>Pet Type</th>
                            <th>Age</th>
                            <th>Health Status</th>
                            <th>Vaccination Details</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $serialNo = 1;
                        while ($pet = $resultPets->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $serialNo++; ?></td>
                                <td><?php echo $pet['pet_name']; ?></td>
                                <td><?php echo $pet['pet_type']; ?></td>
                                <td><?php echo $pet['pet_age']; ?></td>
                                <td><?php echo $pet['health_status']; ?></td>
                                <td><?php echo $pet['vaccination_details']; ?></td>
                                <td>
                                    <a href="?remove_pet_id=<?php echo $pet['id']; ?>" class="btn">Remove</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </section>

            <!-- Add New Pet Form -->
            <section id="add-pet-form">
                <h3>Add New Pet</h3>
                <div class="form-container">
                    <form method="POST" action="">
                        <input type="text" name="pet_name" placeholder="Pet Name" required><br>
                        <input type="text" name="pet_type" placeholder="Pet Type" required><br>
                        <input type="number" name="pet_age" placeholder="Pet Age" required><br>
                        <input type="text" name="health_status" placeholder="Health Status" required><br>
                        <textarea name="vaccination_details" placeholder="Vaccination Details" required></textarea><br>
                        <button type="submit" name="add_pet" class="btn">Add Pet</button>
                    </form>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
