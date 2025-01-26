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

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $petName = $_POST['pet_name']; // Get pet name from form

        // Fetch the pet_id corresponding to the selected pet_name from the appointments table
        $petQuery = $conn->query("SELECT pet_id FROM appointments WHERE pet_name = '$petName' AND user_id = '$userId' LIMIT 1");
        
        if ($petQuery->num_rows > 0) {
            $pet = $petQuery->fetch_assoc();
            $petId = $pet['pet_id']; // Get the pet_id from the appointments table

            $reportDate = $_POST['report_date'];
            $diagnosis = $_POST['diagnosis'];
            $treatment = $_POST['treatment'];
            $followUp = isset($_POST['follow_up']) ? 1 : 0;

            $attachment = '';
            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
                $uploadDir = 'uploads/';
                $fileName = basename($_FILES['attachment']['name']);
                $targetFile = $uploadDir . $fileName;
                if (move_uploaded_file($_FILES['attachment']['tmp_name'], $targetFile)) {
                    $attachment = $fileName;
                }
            }

            // Insert the report with the correct pet_id
            $sql = "INSERT INTO medical_reports (user_id, pet_id, report_date, diagnosis, treatment, follow_up, attachment) 
                    VALUES ('$userId', '$petId', '$reportDate', '$diagnosis', '$treatment', '$followUp', '$attachment')";

            if ($conn->query($sql) === TRUE) {
                $message = "Medical report added successfully!";
            } else {
                $message = "Error: " . $conn->error;
            }
        } else {
            $message = "Pet not found.";
        }
    }

    // Fetch pets from the appointments table for the logged-in user
    $petsResult = $conn->query("SELECT DISTINCT pet_name FROM appointments WHERE user_id = '$userId'");

    // Fetch medical reports for the logged-in user
    $reportsResult = $conn->query("SELECT mr.id, pr.pet_name, mr.report_date, mr.diagnosis, mr.treatment, mr.follow_up, mr.attachment 
                                   FROM medical_reports mr
                                   JOIN appointments pr ON mr.pet_id = pr.pet_id
                                   WHERE mr.user_id = '$userId'");
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
    <title>Add Medical Report</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: black;
            background: url('images/duck.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 700px;
            margin: 30px auto;
            padding: 20px;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            font-size: 28px;
            color: #f5c71a; /* Golden color for text */
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 3px;
            color: #f5c71a; /* Golden color for labels */
        }

        input[type="text"],
        input[type="date"],
        textarea,
        select,
        button {
            padding: 8px 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 13px;
            width: 100%;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        textarea {
            resize: none;
        }

        button {
            background-color: #f5c71a;
            color: black;
            border: none;
            cursor: pointer;
            font-size: 14px;
            padding: 10px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #e5b810;
        }

        .follow-up {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        h3{
            color:#e5b810;
        }
        .medical-reports {
            margin-top: 30px;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }

        .report {
            background-color: #fff;
            padding: 15px;
            margin: 15px 0;
            border-radius: 6px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
        }

        .report-header {
            font-size: 14px;
            margin-bottom: 8px;
        }

        .report-header span {
            display: block;
            margin-bottom: 4px;
            font-weight: bold;
            color: #444;
        }

        .report p {
            font-size: 13px;
            line-height: 1.4;
            color: #555;
            margin: 5px 0;
        }

        .alert {
            padding: 10px;
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #721c24;
            margin-bottom: 15px;
            border-radius: 6px;
        }

        .no-reports {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            color: #888;
            padding: 12px;
            background-color: #f9f9f9;
            border-radius: 6px;
        }
        .div h3{
            color: #e5b81
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add Medical Report</h2>

        <?php if (isset($message)): ?>
            <div class="alert"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <label for="pet_name">Select Pet:</label>
            <select name="pet_name" required>
                <?php while ($pet = $petsResult->fetch_assoc()): ?>
                    <option value="<?php echo $pet['pet_name']; ?>"><?php echo $pet['pet_name']; ?></option>
                <?php endwhile; ?>
            </select>

            <label for="report_date">Report Date:</label>
            <input type="date" name="report_date" required>

            <label for="diagnosis">Diagnosis:</label>
            <textarea name="diagnosis" rows="3" required></textarea>

            <label for="treatment">Treatment:</label>
            <textarea name="treatment" rows="3" required></textarea>

            <div class="follow-up">
                <label for="follow_up">Follow-up Needed:</label>
                <input type="checkbox" name="follow_up" id="follow_up">
            </div>

            <label for="attachment">Attachment (optional):</label>
            <input type="file" name="attachment">

            <button type="submit">Add Report</button>
        </form>

        <div class="medical-reports">
            <h3>Your Medical Reports</h3>
            <?php if ($reportsResult->num_rows > 0): ?>
                <?php while ($report = $reportsResult->fetch_assoc()): ?>
                    <div class="report">
                        <div class="report-header">
                            <span>Pet: <?php echo htmlspecialchars($report['pet_name']); ?></span>
                            <span>Report Date: <?php echo htmlspecialchars($report['report_date']); ?></span>
                            <span>Diagnosis: <?php echo htmlspecialchars($report['diagnosis']); ?></span>
                            <span>Treatment: <?php echo htmlspecialchars($report['treatment']); ?></span>
                            <span>Follow-up Needed: <?php echo $report['follow_up'] ? 'Yes' : 'No'; ?></span>
                        </div>
                        <?php if ($report['attachment']): ?>
                            <p><a href="uploads/<?php echo htmlspecialchars($report['attachment']); ?>" target="_blank">View Attachment</a></p>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-reports">No medical reports found.</div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
