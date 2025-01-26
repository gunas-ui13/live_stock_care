<?php include('header.php'); ?>
<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "vetcare1";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = ""; // Feedback message

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // Handle form submission to add a new bill
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $petId = $_POST['pet_id'];
        $serviceType = $_POST['service_type'];
        $amount = $_POST['amount'];

        $sql = "INSERT INTO billing (user_id, pet_id, service_type, amount, payment_status) 
                VALUES ('$userId', '$petId', '$serviceType', '$amount', 'Pending')";

        if ($conn->query($sql) === TRUE) {
            $message = "Bill added successfully!";
        } else {
            $message = "Error: " . $conn->error;
        }

        // Redirect to avoid form resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    // Fetch distinct pets for the "Select Pet" dropdown
    $petsResult = $conn->query("SELECT DISTINCT  pet_name FROM pet_records WHERE user_id = '$userId'");

    // Fetch available services for the "Service Type" dropdown
    $servicesResult = $conn->query("SELECT id, service_name FROM services");

    // Fetch the user's billing history
    $billsQuery = "SELECT b.id, b.service_type, b.amount, b.payment_status, b.payment_date, p.pet_name
                   FROM billing b
                   JOIN pet_records p ON b.pet_id = p.id
                   WHERE b.user_id = '$userId'";
    $billsResult = $conn->query($billsQuery);
} else {
    echo "Please log in first.";
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing and Payments</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: url('images/duck.jpg') no-repeat center center/cover;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: rgba(0, 0, 0, 0.7);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 800px;
        }
        h2 {
            text-align: center;
            color: #f5c71a;
        }
        form {
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
        }
        label {
            margin: 10px 0 5px;
            font-weight: bold;
            color: #f5c71a;
        }
        input, select, button {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #f5c71a;
            color: black;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #e5b810;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
            color: black;
        }
        .status-pending {
            color: orange;
        }
        .status-paid {
            color: green;
        }
        .status-failed {
            color: red;
        }
        .message {
            text-align: center;
            margin-bottom: 20px;
            color: lightgreen;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Feedback Message -->
        <?php if (!empty($message)): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>

        <!-- Add Bill Form -->
        <h2>Add New Bill</h2>
        <form action="" method="POST">
            <label for="pet_id">Select Pet:</label>
            <select name="pet_id" required>
                <?php while ($pet = $petsResult->fetch_assoc()): ?>
                    <option value=""><?php echo $pet['pet_name']; ?></option>
                <?php endwhile; ?>
            </select>

            <label for="service_type">Service Type:</label>
            <select name="service_type" required>
                <?php while ($service = $servicesResult->fetch_assoc()): ?>
                    <option value="<?php echo $service['service_name']; ?>"><?php echo $service['service_name']; ?></option>
                <?php endwhile; ?>
            </select>

            <label for="amount">Amount:</label>
            <input type="number" name="amount" step="0.01" required>

            <button type="submit">Add Bill</button>
        </form>

        <!-- Billing History Table -->
        <h2>Your Billing History</h2>
        <?php if ($billsResult->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Pet Name</th>
                        <th>Service</th>
                        <th>Amount</th>
                        <th>Payment Status</th>
                        <th>Payment Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($bill = $billsResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($bill['pet_name']); ?></td>
                            <td><?php echo htmlspecialchars($bill['service_type']); ?></td>
                            <td><?php echo htmlspecialchars($bill['amount']); ?></td>
                            <td class="<?php echo 'status-' . strtolower($bill['payment_status']); ?>">
                                <?php echo htmlspecialchars($bill['payment_status']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($bill['payment_date']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No billing records found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
