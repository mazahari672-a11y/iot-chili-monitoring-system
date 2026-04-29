<?php
// Gantikan ini dengan sambungan ke pangkalan data anda
require 'config.php';

// Mulakan sesi
session_start();

$errorMessage = '';

// Semak jika borang dihantar
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari borang
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Semak jika username adalah 'admin' dan kata laluan betul
    if ($username === 'admin' && $password === 'admin') {
        // Login berjaya sebagai admin
        $_SESSION['username'] = $username;

        // Log user action to the user_log table
        $action = 'login';
        $stmt = $conn->prepare("INSERT INTO user_log (username, action, timestamp) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $username, $action);
        $stmt->execute();
        $stmt->close();

        // Redirect ke admin dashboard
        header('Location: admin_dashboard.php');
        exit(); // Penting untuk menghentikan skrip selepas pengalihan
    } else {
        // Kueri untuk mendapatkan hash password dari pangkalan data untuk pengguna biasa
        $query = "SELECT password FROM User WHERE username = ?";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->bind_result($hash);
            $stmt->fetch();
            $stmt->close();

            // Semak jika $hash tidak kosong
            if ($hash) {
                // Semak kata laluan untuk pengguna biasa
                if (password_verify($password, $hash)) {
                    // Login berjaya sebagai pengguna biasa
                    $_SESSION['username'] = $username;

                    // Log user action to the user_log table
                    $action = 'login';
                    $stmt = $conn->prepare("INSERT INTO user_log (username, action, timestamp) VALUES (?, ?, NOW())");
                    $stmt->bind_param("ss", $username, $action);
                    $stmt->execute();
                    $stmt->close();

                    // Redirect ke dashboard biasa
                    header('Location: dashboard.php');
                    exit(); // Penting untuk menghentikan skrip selepas pengalihan
                } else {
                    $errorMessage = "Wrong Password.";
                }
            } else {
                $errorMessage = "User Not found.";
            }
        } else {
            $errorMessage = "Ralat dalam persediaan kueri.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
       body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #ff7f7f, #ffb84d);

        }

        h1 {
            text-align: center;
            color: black;
            margin-bottom: 20px;
            font-size: 2.5em;
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
        }

        input {
            width: 80%;
            margin-bottom: 15px;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background-color: #f0f0f0;
            font-size: 1em;
            color: #333;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        button {
            width: 85%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background-color: #ff5722;
            color: #fff;
            font-size: 1em;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #e64a19;
        }

        .register-button {
            background-color: #1e88e5;
        }

        .register-button:hover {
            background-color: #1565c0;
        }

        footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9em;
            color: black;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.4);
        }
    </style>
</head>
<body>
    <div>
        <h1>Smart Chili Farm System</h1>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
            <button type="button" class="register-button" onclick="window.location.href='register.php'">Sign Up</button>
        </form>
        <footer>
            <p>&copy; 2024 HAA.co. All rights reserved.</p>
            <p>Version 1.0</p>
        </footer>
    </div>

    <!-- Display error message as a JavaScript alert if an error exists -->
    <?php if ($errorMessage): ?>
        <script>
            alert("<?php echo $errorMessage; ?>");
        </script>
    <?php endif; ?>
</body>
</html>
