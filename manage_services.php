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

// Fetch all services for the super admin
$query = "SELECT * FROM services";
$resultServices = $conn->query($query);

// Add new service functionality
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_service'])) {
    $service_name = isset($_POST['service_name']) ? $_POST['service_name'] : '';
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    $price = isset($_POST['price']) ? $_POST['price'] : null; // Price is optional

    // Check if fields are not empty before inserting
    if ($service_name && $description) {
        // Insert the new service
        $stmt = $conn->prepare("INSERT INTO services (service_name, description, price) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $service_name, $description, $price);  // 'sss' for string, string, string (price is nullable)
        $stmt->execute();
        $stmt->close();
        header("Location: manage_services.php"); // Redirect to the same page to prevent re-submission
        exit();
    } else {
        $error_message = "Service name and description are required.";
    }
}

// Remove service functionality
if (isset($_GET['remove_service_id'])) {
    $removeServiceId = $_GET['remove_service_id'];
    $conn->query("DELETE FROM services WHERE id = $removeServiceId");
    header("Location: manage_services.php"); // Redirect after deletion
    exit();
}

// Update service functionality
if (isset($_GET['edit_service_id'])) {
    $editServiceId = $_GET['edit_service_id'];
    $serviceResult = $conn->query("SELECT * FROM services WHERE id = $editServiceId");
    $serviceData = $serviceResult->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_service'])) {
    $service_name = isset($_POST['service_name']) ? $_POST['service_name'] : '';
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    $price = isset($_POST['price']) ? $_POST['price'] : null; // Price is optional
    $serviceId = $_POST['service_id'];

    // Check if fields are not empty before updating
    if ($service_name && $description) {
        // Update the service details
        $stmt = $conn->prepare("UPDATE services SET service_name = ?, description = ?, price = ? WHERE id = ?");
        $stmt->bind_param("ssdi", $service_name, $description, $price, $serviceId); // 'ssdi' for string, string, decimal, int
        $stmt->execute();
        $stmt->close();
        header("Location: manage_services.php"); // Redirect after updating
        exit();
    } else {
        $error_message = "Service name and description are required.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Services</title>
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
        .service-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        .service-table th, .service-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
            color: white;
            background-color: black;
        }
        .service-table th {
            background-color: #4CAF50;
            color: white;
        }
        .service-table tr:nth-child(even) {
            background-color: #333;
        }
        .service-table tr:nth-child(odd) {
            background-color: #444;
        }
        .service-table tr:hover {
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
        .btn-remove {
            background-color: red;
        }
        .btn-remove:hover {
            background-color: darkred;
        }
        .form-container {
            margin-top: 30px;
            background-color: #333;
            padding: 20px;
            border-radius: 10px;
        }
        .form-container input, .form-container textarea, .form-container button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
        }
        .form-container input[type="text"], .form-container input[type="number"], .form-container textarea {
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
            <h1>Manage Services</h1>
        </header>

        <main>
            <!-- Services Table -->
            <section id="service-table">
                <h2>Existing Services</h2>
                <table class="service-table">
                    <thead>
                        <tr>
                            <th>Sl No</th>
                            <th>Service Name</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $serialNo = 1;
                        while ($service = $resultServices->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $serialNo++; ?></td>
                                <td><?php echo $service['service_name']; ?></td>
                                <td><?php echo $service['description']; ?></td>
                                <td><?php echo isset($service['price']) ? $service['price'] : 'N/A'; ?></td>
                                <td>
                                    <a href="?edit_service_id=<?php echo $service['id']; ?>" class="btn">Edit</a>
                                    <a href="?remove_service_id=<?php echo $service['id']; ?>" class="btn btn-remove">Remove</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </section>

            <!-- Add New Service Form -->
            <section id="add-service-form">
                <h3>Add New Service</h3>
                <div class="form-container">
                    <?php if (isset($error_message)): ?>
                        <div class="error-message"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    <form method="POST" action="">
                        <input type="text" name="service_name" placeholder="Service Name" required><br>
                        <textarea name="description" placeholder="Service Description" required></textarea><br>
                        <input type="number" name="price" step="0.01" placeholder="Price (Optional)"><br>
                        <button type="submit" name="add_service" class="btn">Add Service</button>
                    </form>
                </div>
            </section>

            <!-- Edit Service Form (if editing) -->
            <?php if (isset($_GET['edit_service_id'])): ?>
                <section id="edit-service-form">
                    <h3>Edit Service</h3>
                    <div class="form-container">
                        <form method="POST" action="">
                            <input type="hidden" name="service_id" value="<?php echo $serviceData['id']; ?>">
                            <input type="text" name="service_name" value="<?php echo $serviceData['service_name']; ?>" required><br>
                            <textarea name="description" required><?php echo $serviceData['description']; ?></textarea><br>
                            <input type="number" name="price" step="0.01" value="<?php echo isset($serviceData['price']) ? $serviceData['price'] : ''; ?>" placeholder="Price (Optional)"><br>
                            <button type="submit" name="update_service" class="btn">Update Service</button>
                        </form>
                    </div>
                </section>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
