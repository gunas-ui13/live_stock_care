<?php include('header.php'); ?>
<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "vetcare1";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['admin_id']) || $_SESSION['role'] != 'Admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_inquiry'])) {
    $inquiryId = $_POST['inquiry_id'];
    $status = mysqli_real_escape_string($conn, $_POST['status']); // New status from form

    // Debugging the status value
    echo "Status: $status<br>";
    echo "Inquiry ID: $inquiryId<br>";

    // Update the inquiry status in the database
    $updateStmt = $conn->prepare("UPDATE inquiries SET status = ? WHERE id = ?");
    $updateStmt->bind_param("si", $status, $inquiryId);

    if ($updateStmt->execute()) {
        $message = "Inquiry status updated successfully!";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $message = "Error updating inquiry: " . $conn->error;
    }
}

// Fetch all inquiries for admin with message and urgency fields
$sql = "SELECT i.id, i.subject, i.pet_type, i.status, i.message, i.urgency, i.created_at, u.name AS user_name 
        FROM inquiries i 
        JOIN users u ON i.user_id = u.id 
        ORDER BY i.created_at DESC";
$inquiries = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Inquiries</title>
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
        .container {
            background-color: rgba(0, 0, 0, 0.7);
            padding: 20px;
            border-radius: 8px;
            width: 100%;
            max-width: 1200px;
        }
        h2 {
            text-align: center;
            color: #fff;
        }
        .message {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            margin-bottom: 20px;
            text-align: center;
            border-radius: 5px;
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
        
        textarea {
            width: 100%;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ddd;
            background-color: #333;
            color: #fff;
            resize: none;
            margin-bottom: 10px;
        }
        button {
            padding: 10px 20px;
            background-color: #f5c71a;
            border: none;
            color: black;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #e4b90c;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Manage Inquiries</h2>

        <?php if (isset($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Pet Type</th>
                    <th>Subject</th>
                    <th>User Name</th>
                    <th>Status</th>
                    <th>Urgency</th>
                    <th>Message</th>
                    <th>Submitted On</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $serialNumber = 1; // Initialize serial number
                while ($inquiry = $inquiries->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $serialNumber++; ?></td> <!-- Display Serial Number -->
                        <td><?php echo $inquiry['pet_type']; ?></td>
                        <td><?php echo $inquiry['subject']; ?></td>
                        <td><?php echo $inquiry['user_name']; ?></td>
                        <td><?php echo $inquiry['status']; ?></td>
                        <td><?php echo $inquiry['urgency']; ?></td>
                        <td><?php echo $inquiry['message']; ?></td>
                        <td><?php echo date('d M Y, H:i', strtotime($inquiry['created_at'])); ?></td>
                        <td>
                            <form action="" method="POST">
                                <input type="hidden" name="inquiry_id" value="<?php echo $inquiry['id']; ?>">
                                <textarea name="status" rows="4" placeholder="Enter status update" required><?php echo $inquiry['status']; ?></textarea>
                                <button type="submit" name="update_inquiry">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>
