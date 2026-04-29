<?php
// Replace this with your database connection
require 'config.php';

$message = '';
$redirect = false;

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get data from the form
    $username = $_POST['username'];
    $password = $_POST['password'];
    $nama = $_POST['nama'];

    // Hash the password before saving
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Query to insert data into the database
    $query = "INSERT INTO User (username, password, Nama) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param("sss", $username, $hashedPassword, $nama);
        if ($stmt->execute()) {
            $message = "Registration successful!";
            $redirect = true; // Set the redirect flag to true if registration is successful
        } else {
            $message = "Error during registration.";
        }
        $stmt->close();
    } else {
        $message = "Error in query preparation.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #ffcc00, #ff9966);
        }

        h1 {
            text-align: center;
            color: black;
            margin-bottom: 20px;
            font-size: 2.5em;
            text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.3);
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            border: 2px solid #fff;
            border-radius: 15px;
            padding: 30px;
            background-color: rgba(255, 255, 255, 0.9);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
            width: 350px;
        }

        input {
            width: 85%;
            margin-bottom: 15px;
            padding: 12px;
            border: 2px solid #ccc;
            border-radius: 8px;
            background-color: #f7f7f7;
            font-size: 1em;
            color: #333;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: border-color 0.3s ease;
        }

        input:focus {
            border-color: #ff9966;
            outline: none;
        }

        button {
            width: 90%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background-color: #ff5722;
            color: #fff;
            font-size: 1em;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-bottom: 10px;
        }

        button:hover {
            background-color: #e64a19;
        }

        .back-button {
            background-color: #2196f3;
        }

        .back-button:hover {
            background-color: #1976d2;
        }

        .error-message {
            color: #e53935;
            font-size: 0.9em;
            margin-bottom: 10px;
        }

        footer {
            margin-top: 20px;
            text-align: center;
            font-size: 0.9em;
            color: black;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body>
    <div>
        <h1>Create Account</h1>
        <form method="POST" action="" onsubmit="return validateForm()">
            <input type="text" id="username" name="username" placeholder="Username" required>
            <div id="usernameError" class="error-message"></div>

            <input type="password" id="password" name="password" placeholder="Password" required>
            <div id="passwordError" class="error-message"></div>

            <input type="text" id="nama" name="nama" placeholder="Full Name" required>
            <div id="namaError" class="error-message"></div>

            <button type="submit" name="register">Register</button>
            <button type="button" class="back-button" onclick="window.location.href='index.php'">Back to Login</button>
        </form>
        <footer>&copy; 2024 HAA.co. All rights reserved.</footer>
    </div>

    <script>
        function validateForm() {
            let isValid = true;

            // Clear previous error messages
            document.getElementById("usernameError").textContent = "";
            document.getElementById("passwordError").textContent = "";
            document.getElementById("namaError").textContent = "";

            // Username validation
            const username = document.getElementById("username").value;
            if (username.length < 3 || username.length > 20) {
                document.getElementById("usernameError").textContent = "Username must be 3-20 characters.";
                isValid = false;
            }

            // Password validation
            const password = document.getElementById("password").value;
            if (password.length < 8) {
                document.getElementById("passwordError").textContent = "Password must be at least 8 characters.";
                isValid = false;
            }

            // Name validation
            const nama = document.getElementById("nama").value;
            if (nama.trim() === "") {
                document.getElementById("namaError").textContent = "Name cannot be empty.";
                isValid = false;
            }

            return isValid;
        }
    </script>

    <?php if ($message): ?>
        <script>
            alert("<?php echo $message; ?>");
            <?php if ($redirect): ?>
                window.location.href = 'index.php';
            <?php endif; ?>
        </script>
    <?php endif; ?>
</body>
</html>
