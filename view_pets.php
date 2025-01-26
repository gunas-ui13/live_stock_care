<?php include('header.php'); ?>
<?php
session_start();
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] != 'Admin') {
    header("Location: login.php");
    exit();
}

$adminName = $_SESSION['admin_name'];
$adminId = $_SESSION['admin_id'];

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "vetcare1";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if a user is selected, otherwise show all pets for the admin's doctor_id
$userId = isset($_GET['user_id']) ? $_GET['user_id'] : null;

if ($userId) {
    // Fetch pets for the selected user and the logged-in admin
    $stmtPets = $conn->prepare("
        SELECT p.id, p.pet_name, p.pet_type, p.pet_age, p.health_status, p.vaccination_details, u.name AS owner_name, u.email AS owner_email
        FROM pet_records p
        JOIN users u ON p.user_id = u.id
        WHERE u.doctor_id = ? AND u.id = ?");
    $stmtPets->bind_param("ii", $adminId, $userId);  // Filter by both admin's ID and selected user's ID
} else {
    // Fetch all pets for the logged-in admin's doctor_id
    $stmtPets = $conn->prepare("
        SELECT p.id, p.pet_name, p.pet_type, p.pet_age, p.health_status, p.vaccination_details, u.name AS owner_name, u.email AS owner_email
        FROM pet_records p
        JOIN users u ON p.user_id = u.id
        WHERE u.doctor_id = ?");
    $stmtPets->bind_param("i", $adminId);  // Filter only by the admin's ID
}

$stmtPets->execute();
$pets = $stmtPets->get_result();

// Fetch all users assigned to the current admin (if you're offering user selection)
$stmtUsers = $conn->prepare("SELECT id, name FROM users WHERE doctor_id = ?");
$stmtUsers->bind_param("i", $adminId);
$stmtUsers->execute();
$usersResult = $stmtUsers->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Pets</title>
    <link rel="stylesheet" href="admin_dashboard.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #000;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: url('images/duck.jpg') no-repeat center center/cover;
        }
        .pets-container {
            background-color: rgba(0, 0, 0, 0.7);
            padding: 20px;
            border-radius: 8px;
            width: 100%;
            max-width: 1200px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f5c71a;
            color: black;
        }
        tr:hover {
            background-color: #f5c71a;
            color: black;
        }
    </style>
</head>
<body>
    <div class="pets-container">
        <main>
            <h2>Cattle Details</h2>

            <!-- User Selection Form (only if needed) -->
            <form method="get" action="">
                <label for="user_id">Select User:</label>
                <select name="user_id" id="user_id">
                    <option value="">Select User</option>
                    <?php while ($user = $usersResult->fetch_assoc()): ?>
                        <option value="<?php echo $user['id']; ?>" <?php echo (isset($_GET['user_id']) && $_GET['user_id'] == $user['id']) ? 'selected' : ''; ?>>
                            <?php echo $user['name']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <button type="submit">View Pets</button>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>Pet ID</th>
                        <th>Pet Name</th>
                        <th>Pet Type</th>
                        <th>Pet Age</th>
                        <th>Health Status</th>
                        <th>Vaccination Details</th>
                        <th>Owner Name</th>
                        <th>Owner Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($pets->num_rows > 0) {
                        while ($row = $pets->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['pet_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['pet_type']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['pet_age']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['health_status']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['vaccination_details']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['owner_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['owner_email']) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8'>No pets found for this doctor.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
