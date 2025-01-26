<?php include('header.php'); ?>
<?php
// If needed, you can add server-side logic here
// For example, processing form data, checking user authentication, etc.
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vetcare - Login</title>
    <style>
        /* Background Styling */
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
        p, a{
            color: #f5c71a;
        }

        /* Centered Container Styling */
        .container {
            background-color: rgba(0, 0, 0, 0.7); /* Transparent background */
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        h1 {
            margin-bottom: 20px;
            text-align: center;
            color: #f5c71a; /* Golden color for heading */
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

        input, button {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        /* Button Styling */
        button {
            background-color: #f5c71a;
            color: black;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background-color: #e5b810;
        }

        .link {
            text-align: center;
            color: #f5c71a;
            margin-top: 10px;
        }

        .link a {
            color: #f5c71a;
            text-decoration: none;
        }

        .link a:hover {
            text-decoration: underline;
        }

        /* Forgot Password Form Styling */
        #forgotPasswordForm {
            display: none; /* Hidden by default */
            margin-top: 20px;
            background-color: rgba(255, 255, 255, 0.1);
            padding: 15px;
            border-radius: 8px;
        }

        #forgotPasswordForm h2 {
            margin-top: 0;
            color: #f5c71a;
        }

        /* Success Message Styling */
        #successMessage {
            display: none; /* Hidden by default */
            margin-top: 10px;
            color: #28a745; /* Green for success */
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Login</h1>
        <form action="login.php" method="POST">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" maxlength="8" required>

            <button type="submit" name="submit">Login</button>

            <p class="link">Don't have an account? <a href="sign1.php">Sign Up</a></p>
            <p><a href="#" onclick="toggleForgotPassword()">Forgot Password?</a></p>
        </form>

        <!-- Forgot Password Form -->
        <div id="forgotPasswordForm">
            <h2>Forgot Password</h2>
            <form id="resetForm">
                <label for="resetEmail">Enter your registered email</label>
                <input type="email" id="resetEmail" name="resetEmail" placeholder="Enter your email" required>
                <button type="submit">Send Reset Link</button>
            </form>
            <p id="successMessage">A reset link has been sent to your email!</p>
        </div>
    </div>

    <script>
        // Function to toggle the visibility of the forgot password form
        function toggleForgotPassword() {
            const form = document.getElementById('forgotPasswordForm');
            if (form.style.display === 'none' || form.style.display === '') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        }

        // Handle the reset form submission
        document.getElementById('resetForm').addEventListener('submit', function (e) {
            e.preventDefault(); // Prevent default form submission

            // Simulate sending the reset link
            setTimeout(function () {
                document.getElementById('successMessage').style.display = 'block'; // Show success message
            }, 500); // Simulate delay
        });
    </script>
</body>

</html>
