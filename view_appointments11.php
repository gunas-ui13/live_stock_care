<?php include('header.php'); ?>
<?php
// If needed, you can add server-side logic here
// For example, processing form data, checking user authentication, etc.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Appointments</title>
    <link rel="stylesheet" href="admin_dashboard.css"> <!-- Link to your CSS file -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .appointments-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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
            background-color: #4CAF50;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .action-btn {
            padding: 5px 10px;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .confirm-btn {
            background-color: #4CAF50;
        }
        .cancel-btn {
            background-color: #f44336;
        }
        .action-btn:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="appointments-container">
        <header>
            <h1>View Appointments</h1>
        </header>

        <main>
            <h2>Appointments</h2>
            <table>
                <thead>
                    <tr>
                        <th>Appointment ID</th>
                        <th>User Name</th>
                        <th>Pet Type</th>
                        <th>Pet Age</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="appointmentsTable">
                    <?php
                    if ($appointments->num_rows > 0) {
                        while ($row = $appointments->fetch_assoc()) {
                            echo "<tr id='appointment_" . $row['id'] . "'>";
                            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['user_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['petType']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['petAge']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['appointment_date']) . "</td>";
                            echo "<td id='status_" . $row['id'] . "'>" . htmlspecialchars($row['status']) . "</td>";
                            echo "<td>";
                            if ($row['status'] == 'Pending') {
                                echo "<button class='action-btn confirm-btn' onclick='updateStatus(" . $row['id'] . ", \"Confirmed\")'>Confirm</button>";
                                echo " <button class='action-btn cancel-btn' onclick='updateStatus(" . $row['id'] . ", \"Cancelled\")'>Cancel</button>";
                            } else {
                                echo "<span>No actions</span>";
                            }
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No appointments found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </main>
    </div>

    <script>
        function updateStatus(appointmentId, status) {
            // Create a new XMLHttpRequest object
            var xhr = new XMLHttpRequest();

            // Open the request
            xhr.open("POST", "update_appointment_status.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            // Define the data to send
            var data = "appointment_id=" + appointmentId + "&status=" + status;

            // Handle the response from the server
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Update the status in the table without refreshing the page
                    if (xhr.responseText == "success") {
                        document.getElementById("status_" + appointmentId).innerText = status;
                    } else {
                        alert("Error updating status.");
                    }
                }
            };

            // Send the request
            xhr.send(data);
        }
    </script>
</body>
</html>
