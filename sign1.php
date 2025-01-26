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
    <title>Vetcare - Sign Up</title>
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

        select, button {
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
    </style>
</head>

<body>
    <div class="container">
        <h1>Sign Up</h1>

        <form>
            <label for="role">Select Role:</label>
            <select id="role" name="role" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>

            <button type="button" id="proceed">Proceed</button>
        </form>

        <p class="link">Already have an account? <a href="login.php">Login</a></p>
    </div>

    <script>
        document.getElementById('proceed').addEventListener('click', function() {
            var role = document.getElementById('role').value;
            
            // Redirect to the user or admin sign up page
            if (role === 'user') {
                window.location.href = 'user_signup.php'; // Redirect to user sign-up page
            } else if (role === 'admin') {
                window.location.href = 'admin_signup.php'; // Redirect to admin sign-up page
            }
        });
    </script>
</body>

</html>
