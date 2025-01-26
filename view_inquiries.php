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

$message = ""; // Variable to hold success or error messages

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // Fetch current user details to prefill the form
    $result = $conn->query("SELECT * FROM users WHERE id='$userId'");
    $user = $result->fetch_assoc();

    // Fetch available doctors (admins)
    $doctors = $conn->query("SELECT * FROM admins WHERE role='Admin'");

    // Submit Inquiry
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_inquiry'])) {
        $subject = mysqli_real_escape_string($conn, $_POST['subject']);
        $messageContent = mysqli_real_escape_string($conn, $_POST['message']);
        $urgency = mysqli_real_escape_string($conn, $_POST['urgency']);
        $petType = mysqli_real_escape_string($conn, $_POST['pet_type']);
        $doctorId = mysqli_real_escape_string($conn, $_POST['doctor_id']);
        
        $sql = "INSERT INTO inquiries (user_id, subject, message, urgency, pet_type, status, doctor_id, created_at) 
                VALUES ('$userId', '$subject', '$messageContent', '$urgency', '$petType', 'Pending', '$doctorId', NOW())";

        if ($conn->query($sql) === TRUE) {
            $message = "Your inquiry has been submitted successfully!";
        } else {
            $message = "Error: " . $conn->error;
        }
    }

    // Fetch user's inquiries along with doctor's name and ID
    $inquiries = $conn->query("SELECT inquiries.*, admins.name AS doctor_name, admins.doctor_id 
                               FROM inquiries 
                               LEFT JOIN admins ON inquiries.doctor_id = admins.id 
                               WHERE inquiries.user_id='$userId' ORDER BY inquiries.created_at DESC");

} else {
    $message = "Please log in first.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inquiry - Livestock Care</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #000;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: url('images/duck.jpg') no-repeat center center/cover;
        }
        .container {
            background-color: rgba(0, 0, 0, 0.7);
            padding: 20px;
            border-radius: 8px;
            width: 100%;
            max-width: 800px;
        }
        h2, h3 {
            text-align: center;
            color: #f5c71a;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        label {
            color: #f5c71a;
        }
        input, select, textarea, button {
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
        .message {
            margin-top: 10px;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }
        .inquiry-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .inquiry-table th {
            background-color: #6e8efb;
            color: black;
            padding: 15px;
        }
        .inquiry-table tr {
            background-color: black; /* Uniform row color */
        }
        .inquiry-table tr:hover {
            background-color: #ffc107; /* Optional hover effect */
        }
        .inquiry-table td {
            padding: 15px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .inquiry-status {
            padding: 5px 10px;
            font-weight: bold;
            border-radius: 3px;
            display: inline-block;
        }
        .pending {
            background-color: #ffc107;
            color: #212529;
        }
        .resolved {
            background-color: #28a745;
            color: white;
        }
        a {
            color: #f5c71a;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .inquiry-section {
            display: none;
        }
    </style>
    <script>
        function toggleInquiries() {
            var inquiriesSection = document.getElementById("inquiriesSection");
            if (inquiriesSection.style.display === "none" || inquiriesSection.style.display === "") {
                inquiriesSection.style.display = "block";
            } else {
                inquiriesSection.style.display = "none";
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Inquiry Management</h2>

        <!-- Display success or error message -->
        <?php if ($message != ""): ?>
            <div class="message <?php echo strpos($message, 'Error') === false ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Submit Inquiry Form -->
        <form action="" method="POST">
            <label for="pet_type">Select Pet Type:</label>
            <select name="pet_type" required>
                <option value="Cow">Cow</option>
                <option value="Goat">Goat</option>
                <option value="Sheep">Sheep</option>
                <option value="Horse">Horse</option>
            </select>

            <label for="doctor_id">Select Doctor:</label>
            <select name="doctor_id" required>
                <?php while ($doctor = $doctors->fetch_assoc()): ?>
                    <option value="<?php echo $doctor['id']; ?>">
                        <?php echo 'Dr. ' . $doctor['doctor_id'] . ' - ' . $doctor['name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="subject">Inquiry Subject:</label>
            <input type="text" name="subject" required>

            <label for="message">Detailed Message:</label>
            <textarea name="message" rows="4" required></textarea>

            <label for="urgency">Urgency:</label>
            <select name="urgency" required>
                <option value="Normal">Normal</option>
                <option value="Urgent">Urgent</option>
            </select>

            <button type="submit" name="submit_inquiry">Submit Inquiry</button>
        </form>

        <!-- Past Inquiries Link -->
        <h3><a href="javascript:void(0);" onclick="toggleInquiries()">Your Past Inquiries</a></h3>

        <!-- Display Past Inquiries Only If Link Clicked -->
        <div id="inquiriesSection" class="inquiry-section">
            <h3>Your Past Inquiries</h3>
            <table class="inquiry-table">
                <thead>
                    <tr>
                        <th>Pet Type</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Submitted On</th>
                        <th>Assigned Doctor</th> <!-- New column for Doctor's Name -->
                    </tr>
                </thead>
                <tbody>
                    <?php while ($inquiry = $inquiries->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $inquiry['pet_type']; ?></td>
                            <td>
                                <a href="?inquiry_id=<?php echo $inquiry['id']; ?>">
                                    <?php echo $inquiry['subject']; ?>
                                </a>
                            </td>
                            <td>
                                <span class="inquiry-status <?php echo strtolower(str_replace(' ', '-', $inquiry['status'])); ?>">
                                    <?php echo $inquiry['status']; ?>
                                </span>
                            </td>
                            <td><?php echo date('d M Y, H:i', strtotime($inquiry['created_at'])); ?></td>
                            <td><?php echo 'Dr. ' . $inquiry['doctor_id'] . ' - ' . $inquiry['doctor_name']; ?></td> <!-- Display Doctor's Name with ID as Prefix -->
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

<?php
$conn->close(); // Close connection after all queries are done
?>
