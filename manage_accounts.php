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

// Fetch all billing records for the super admin
$query = "SELECT * FROM billing";
$resultBilling = $conn->query($query);

// Add new billing functionality
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_billing'])) {
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : '';
    $pet_id = isset($_POST['pet_id']) ? $_POST['pet_id'] : '';
    $service_type = isset($_POST['service_type']) ? $_POST['service_type'] : '';
    $amount = isset($_POST['amount']) ? $_POST['amount'] : null;
    $payment_status = isset($_POST['payment_status']) ? $_POST['payment_status'] : 'Pending';
    $payment_date = isset($_POST['payment_date']) ? $_POST['payment_date'] : null;

    // Check if fields are not empty before inserting
    if ($user_id && $pet_id && $service_type && $amount && $payment_status) {
        // Insert the new billing entry
        $stmt = $conn->prepare("INSERT INTO billing (user_id, pet_id, service_type, amount, payment_status, payment_date) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissss", $user_id, $pet_id, $service_type, $amount, $payment_status, $payment_date);
        $stmt->execute();
        $stmt->close();
        header("Location: manage_billing.php"); // Redirect to the same page to prevent re-submission
        exit();
    } else {
        $error_message = "All fields are required.";
    }
}

// Remove billing functionality
if (isset($_GET['remove_billing_id'])) {
    $removeBillingId = $_GET['remove_billing_id'];
    $conn->query("DELETE FROM billing WHERE id = $removeBillingId");
    header("Location: manage_billing.php"); // Redirect after deletion
    exit();
}

// Update billing functionality
if (isset($_GET['edit_billing_id'])) {
    $editBillingId = $_GET['edit_billing_id'];
    $billingResult = $conn->query("SELECT * FROM billing WHERE id = $editBillingId");
    $billingData = $billingResult->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_billing'])) {
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : '';
    $pet_id = isset($_POST['pet_id']) ? $_POST['pet_id'] : '';
    $service_type = isset($_POST['service_type']) ? $_POST['service_type'] : '';
    $amount = isset($_POST['amount']) ? $_POST['amount'] : null;
    $payment_status = isset($_POST['payment_status']) ? $_POST['payment_status'] : 'Pending';
    $payment_date = isset($_POST['payment_date']) ? $_POST['payment_date'] : null;
    $billingId = $_POST['billing_id'];

    // Check if fields are not empty before updating
    if ($user_id && $pet_id && $service_type && $amount && $payment_status) {
        // Update the billing details
        $stmt = $conn->prepare("UPDATE billing SET user_id = ?, pet_id = ?, service_type = ?, amount = ?, payment_status = ?, payment_date = ? WHERE id = ?");
        $stmt->bind_param("iissssi", $user_id, $pet_id, $service_type, $amount, $payment_status, $payment_date, $billingId);
        $stmt->execute();
        $stmt->close();
        header("Location: manage_billing.php"); // Redirect after updating
        exit();
    } else {
        $error_message = "All fields are required.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Billing</title>
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
        .billing-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        .billing-table th, .billing-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
            color: white;
            background-color: black;
        }
        .billing-table th {
            background-color: #4CAF50;
            color: white;
        }
        .billing-table tr:nth-child(even) {
            background-color: #333;
        }
        .billing-table tr:nth-child(odd) {
            background-color: #444;
        }
        .billing-table tr:hover {
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
            <h1>Manage Billing</h1>
        </header>

        <main>
            <!-- Billing Table -->
            <section id="billing-table">
                <h2>Existing Billing Records</h2>
                <table class="billing-table">
                    <thead>
                        <tr>
                            <th>Sl No</th>
                            <th>User ID</th>
                            <th>Pet ID</th>
                            <th>Service Type</th>
                            <th>Amount</th>
                            <th>Payment Status</th>
                            <th>Payment Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $serialNo = 1;
                        while ($billing = $resultBilling->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $serialNo++; ?></td>
                                <td><?php echo $billing['user_id']; ?></td>
                                <td><?php echo $billing['pet_id']; ?></td>
                                <td><?php echo $billing['service_type']; ?></td>
                                <td><?php echo $billing['amount']; ?></td>
                                <td><?php echo $billing['payment_status']; ?></td>
                                <td><?php echo $billing['payment_date']; ?></td>
                                <td>
                                    <a href="?edit_billing_id=<?php echo $billing['id']; ?>" class="btn">Edit</a>
                                    <a href="?remove_billing_id=<?php echo $billing['id']; ?>" class="btn btn-remove">Remove</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </section>

            <!-- Add New Billing Form -->
            <section id="add-billing-form">
                <h3>Add New Billing</h3>
                <div class="form-container">
                    <?php if (isset($error_message)): ?>
                        <div class="error-message"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    <form method="POST" action="">
                        <input type="text" name="user_id" placeholder="User ID" required><br>
                        <input type="text" name="pet_id" placeholder="Pet ID" required><br>
                        <input type="text" name="service_type" placeholder="Service Type" required><br>
                        <input type="number" name="amount" step="0.01" placeholder="Amount" required><br>
                        <input type="text" name="payment_status" placeholder="Payment Status" required><br>
                        <input type="text" name="payment_date" placeholder="Payment Date" required><br>
                        <button type="submit" name="add_billing" class="btn">Add Billing</button>
                    </form>
                </div>
            </section>

            <!-- Edit Billing Form (if editing) -->
            <?php if (isset($_GET['edit_billing_id'])): ?>
                <section id="edit-billing-form">
                    <h3>Edit Billing</h3>
                    <div class="form-container">
                        <form method="POST" action="">
                            <input type="hidden" name="billing_id" value="<?php echo $billingData['id']; ?>">
                            <input type="text" name="user_id" value="<?php echo $billingData['user_id']; ?>" required><br>
                            <input type="text" name="pet_id" value="<?php echo $billingData['pet_id']; ?>" required><br>
                            <input type="text" name="service_type" value="<?php echo $billingData['service_type']; ?>" required><br>
                            <input type="number" name="amount" step="0.01" value="<?php echo $billingData['amount']; ?>" required><br>
                            <input type="text" name="payment_status" value="<?php echo $billingData['payment_status']; ?>" required><br>
                            <input type="text" name="payment_date" value="<?php echo $billingData['payment_date']; ?>" required><br>
                            <button type="submit" name="update_billing" class="btn">Update Billing</button>
                        </form>
                    </div>
                </section>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
