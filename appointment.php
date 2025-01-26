<?php include('header.php'); ?>
<?php
// Start session to get the logged-in user's ID
session_start();

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "vetcare1"; // Replace with your database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "Please log in to book an appointment.";
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch doctors (admins excluding super admin)
$sqlDoctors = "SELECT doctor_id, name FROM admins WHERE role = 'Admin'";
$resultDoctors = $conn->query($sqlDoctors);

if ($resultDoctors->num_rows == 0) {
    $error = "No doctors are available at the moment. Please try again later.";
}

// Initialize variables for success and error messages
$success = $error = "";

// Handle form submission only when the 'Book Appointment' button is clicked
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_appointment'])) {
    $date = $_POST['appointment_date'];
    $time = $_POST['appointment_time'];
    $reason = $_POST['reason'];
    $petName = $_POST['pet_name'];  // Pet name field
    $petType = $_POST['pet_type'];  // Pet type field
    $petAge = $_POST['pet_age'];  // Pet age field
    $doctorId = $_POST['doctor_id'];

    // Validate inputs
    if (empty($date) || empty($time) || empty($reason) || empty($petName) || empty($petType) || empty($petAge) || empty($doctorId)) {
        $error = "All fields are required.";
    } else {
        // Insert pet details into pet_records table
        $sqlPet = "INSERT INTO pet_records (user_id, pet_name, pet_type, pet_age) 
                   VALUES ('$userId', '$petName', '$petType', '$petAge')";

        if ($conn->query($sqlPet) === TRUE) {
            // Get the pet_id of the inserted pet
            $petId = $conn->insert_id;

            // Insert pet name and other details into appointments table
            $sqlAppointment = "INSERT INTO appointments (user_id, appointment_date, appointment_time, reason, pet_id, pet_name, doctor_id) 
                               VALUES ('$userId', '$date', '$time', '$reason', '$petId', '$petName', '$doctorId')";

            if ($conn->query($sqlAppointment) === TRUE) {
                // Set success message in session
                $_SESSION['success_message'] = "Your appointment has been booked successfully!";
                
                // Redirect to avoid resubmission on page refresh
                header("Location: " . $_SERVER['PHP_SELF']); // Redirect to the same page
                exit;
            } else {
                $_SESSION['error_message'] = "Error booking appointment: " . $conn->error;
            }
        } else {
            $_SESSION['error_message'] = "Error adding pet details: " . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #000; /* Black background */
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: url('images/duck.jpg') no-repeat center center/cover;
            background-size: cover;
        }
        .container {
            background-color: rgba(0, 0, 0, 0.7); /* Transparent background */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        h2 {
            margin-bottom: 20px;
            text-align: center;
            color: #f5c71a; /* Golden color for text */
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 5px;
            font-weight: bold;
            color: #f5c71a; /* Golden color for labels */
        }
        input, select, textarea, button {
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
        .message {
            margin-top: 10px;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Book an Appointment</h2>

        <!-- Display success or error message -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="message success"><?php echo $_SESSION['success_message']; ?></div>
            <?php unset($_SESSION['success_message']); ?>
        <?php elseif (isset($_SESSION['error_message'])): ?>
            <div class="message error"><?php echo $_SESSION['error_message']; ?></div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <!-- Appointment booking form -->
        <form id="appointmentForm" action="" method="POST">
            <label for="appointment_date">Date:</label>
            <input type="date" id="appointment_date" name="appointment_date" min="<?php echo date('Y-m-d'); ?>" required>

            <label for="appointment_time">Time:</label>
            <input type="time" id="appointment_time" name="appointment_time" required>

            <label for="reason">Reason for Appointment:</label>
            <textarea id="reason" name="reason" rows="3" required></textarea>

            <label for="pet_name">Pet Name:</label>
            <input type="text" id="pet_name" name="pet_name" required>

            <label for="pet_type">Select Pet Type:</label>
            <select id="pet_type" name="pet_type" required>
                <option value="">Select Pet Type</option>
                <option value="Cow">Cow</option>
                <option value="Goat">Goat</option>
                <option value="Sheep">Sheep</option>
                <option value="Chicken">Chicken</option>
                <option value="Duck">Duck</option>
                <!-- Add more pet types as needed -->
            </select>

            <label for="pet_age">Pet Age:</label>
            <input type="number" id="pet_age" name="pet_age" required>

            <label for="doctor_id">Select Doctor:</label>
            <select id="doctor_id" name="doctor_id" required>
                <option value="">Select Doctor</option>
                <?php while ($row = $resultDoctors->fetch_assoc()): ?>
                    <option value="<?php echo $row['doctor_id']; ?>"><?php echo $row['doctor_id']; ?>-<?php echo $row['name']; ?></option>
                <?php endwhile; ?>
            </select>

            <button type="submit" name="book_appointment">Book Appointment</button>
        </form>
    </div>
</body>
</html>
