<?php include('header.php'); ?>
<?php
session_start();
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
$message_type = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : '';
unset($_SESSION['message'], $_SESSION['message_type']);  // Clear the message after showing
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="sign.css">
    <title>Vetcare - Sign Up</title>
    <style>
        /* Popup styling */
        .popup {
            display: none;
            position: fixed;
            top: 20%;
            left: 50%;
            transform: translateX(-50%);
            padding: 20px;
            background-color: #28a745;
            color: white;
            border-radius: 5px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .popup.error {
            background-color: #dc3545;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="form-container">
            <h1>Create a Pet Parent Account</h1>
            

            <form action="connect.php" method="post">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" placeholder="Enter your name" required>

                <label for="email">Email</label>
                <input type="email" id="email" name="em" placeholder="Enter your email" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>

                <label for="contact">Contact No.</label>
                <input type="text" id="contact" name="number" placeholder="Enter your contact number" required>

                <label for="petname">Pet Name</label>
                <input type="text" id="petname" name="petname" placeholder="Enter your pet's name" required>

                <label for="pettype">Pet Type</label>
                <select id="pettype" name="petType" required>
                    <option value="select">Select Pet Type</option>
                    <option value="dog">Dog</option>
                    <option value="cat">Cat</option>
                    <option value="goat">Goat</option>
                    <option value="sheep">Sheep</option>
                    <option value="other">Other</option>
                </select>

                <label for="petage">Pet Age</label>
                <input type="number" id="petage" name="age" placeholder="Enter your pet's age" required>

                <button type="submit" name="submit">Create Account</button>
                <p class="link">Already have an account? <a href="login.html">Login</a></p>
            </form>
        </div>
    </div>
    <!-- Display success or error message as a popup -->
    <?php if ($message): ?>
        <div id="popup" class="popup <?php echo $message_type; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <script>
        window.onload = function() {
            <?php if ($message): ?>
                var popup = document.getElementById('popup');
                popup.style.display = 'block';  // Show the popup
                setTimeout(function() {
                    popup.style.display = 'none';  // Hide the popup after 3 seconds
                }, 3000);
            <?php endif; ?>
        }
    </script>
</body>

</html>
