<?php include('header.php'); ?>
<?php
// Step 1: Connect to the database
$servername = "localhost";  // Change to your database server if it's not localhost
$username = "root";         // Your database username
$password = "";             // Your database password
$dbname = "vetcare1";        // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Step 2: Check if the form is submitted
if (isset($_POST['submit'])) {
    // Step 3: Get the form data
    $name = $_POST['name'];
    $email = $_POST['em'];
    $password = $_POST['password'];
    $contact = $_POST['number'];

    // Step 4: Encrypt the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password

    // Step 5: Prepare the SQL statement to check if email exists
    $emailCheckQuery = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($emailCheckQuery);
    $stmt->bind_param("s", $email);  // Bind the email parameter
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // If email already exists, show a message
        echo "Email is already registered. Please try with a different email.";
    } else {
        // Step 6: Prepare the SQL statement to insert data into the database
        $sql = "INSERT INTO users (name, email, password, contact) 
                VALUES (?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $name, $email, $hashed_password, $contact);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            // Successful insertion, redirect to login page
            header("Location: login.html"); // Redirect to the login page
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}

// Step 7: Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="sign.css">
    <title>Vetcare - User Sign Up</title>
</head>

<body>
    <div class="container">
        <div class="form-container">
            <h1>Create a Pet Parent Account</h1>
            <form action="" method="post">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" placeholder="Enter your name" required>

                <label for="email">Email</label>
                <input type="email" id="email" name="em" placeholder="Enter your email" required>

                <label for="password">Password</label>
                <div class="password-container">
                    <input type="password" id="password" name="password" placeholder="Enter your password" maxlength="8" required>
                    <button type="button" id="togglePassword">👁️</button>
                </div>

                <label for="contact">Contact No.</label>
                <input type="text" id="contact" name="number" placeholder="Enter your contact number" maxlength="10" required>

                <button type="submit" name="submit">Create Account</button>
                <p class="link">Already have an account? <a href="login.html">Login</a></p>
            </form>
        </div>
    </div>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordField = document.getElementById('password');

        togglePassword.addEventListener('click', () => {
            const type = passwordField.type === 'password' ? 'text' : 'password';
            passwordField.type = type;
            togglePassword.textContent = type === 'password' ? '👁️' : '👁️‍🗨️';
        });
    </script>
</body>

</html>
