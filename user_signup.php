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
    <title>Vetcare - User Sign Up</title>
    <style>
        /* Basic Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body and Background Styles */
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #ff7e5f, #feb47b); /* Background gradient effect */
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
            position: relative;
        }

        /* Container Styling */
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        /* Form Styling */
        .form-container {
            background-color: rgba(0, 0, 0, 0.7); /* Semi-transparent background for form */
            padding: 40px;
            border-radius: 10px;
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        .form-container h1 {
            color: #fff;
            margin-bottom: 20px;
        }

        /* Form Labels and Inputs */
        form label {
            display: block;
            font-size: 14px;
            margin: 10px 0 5px;
        }

        form input,
        form button {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border-radius: 5px;
            border: none;
            font-size: 14px;
        }

        form input[type="text"],
        form input[type="email"],
        form input[type="password"],
        form input[type="number"] {
            background-color: #fff;
            color: #333;
        }

        form button {
            background-color: #ff7e5f; /* Button color */
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }

        form button:hover {
            background-color: #feb47b; /* Button hover effect */
        }

        /* Password Container (for visibility toggle) */
        .password-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Password input field */
        #password {
            padding-right: 40px; /* Add space for the icon */
            flex-grow: 1; /* Makes the input take available space */
        }

        /* Eye Icon Box Styling */
        .eye-icon-box {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #fff;
            border-radius: 5px;
            width: 40px;
            height: 40px;
            cursor: pointer;
            padding: 0;
        }

        /* Eye Icon Styling */
        .eye-icon {
            font-size: 18px;
            color: #333;
        }

        /* Link styling */
        p.link {
            color: #fff;
            margin-top: 15px;
        }

        p.link a {
            color: #feb47b;
            text-decoration: none;
        }

        p.link a:hover {
            text-decoration: underline;
        }

        /* Background Element */
        .background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('images/background.jpg') no-repeat center center fixed;
            background-size: cover;
            z-index: -1; /* Place the background behind the form */
        }
    </style>
</head>

<body>
    <!-- Signup Page Container -->
    <div class="container">
        <div class="form-container">
            <h1>Create a Pet Parent Account</h1>
            <form action="connect.php" method="post">
                <!-- Name Field -->
                <label for="name">Name</label>
                <input type="text" id="name" name="name" placeholder="Enter your name" required>

                <!-- Email Field -->
                <label for="email">Email</label>
                <input type="email" id="email" name="em" placeholder="Enter your email" required>

                <!-- Password Field with Toggle Visibility -->
                <label for="password">Password</label>
                <div class="password-container">
                    <input type="password" id="password" name="password" placeholder="Enter your password" maxlength="8" required>
                    <div class="eye-icon-box" id="togglePassword">
                        <span class="eye-icon">👁️</span>
                    </div>
                </div>

                <!-- Contact Number Field -->
                <label for="contact">Contact No.</label>
                <input type="text" id="contact" name="number" placeholder="Enter your contact number" maxlength="10" required>

                <!-- Submit Button -->
                <button type="submit" name="submit">Create Account</button>
                <p class="link">Already have an account? <a href="login1.php">Login</a></p>
            </form>
        </div>
    </div>

    <!-- Background Effect -->
    <div class="background"></div>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordField = document.getElementById('password');

        togglePassword.addEventListener('click', () => {
            const type = passwordField.type === 'password' ? 'text' : 'password';
            passwordField.type = type;
            togglePassword.innerHTML = type === 'password' ? '<span class="eye-icon">👁️</span>' : '<span class="eye-icon">👁️‍🗨️</span>';
        });
    </script>
</body>

</html>
