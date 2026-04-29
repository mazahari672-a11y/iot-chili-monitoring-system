<?php
// Ensure user is logged in as admin
session_start();

if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header('Location: index.php'); // Redirect to login if not admin
    exit();
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FYP Selesai! 🎉</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(to right, #ff9a9e, #fad0c4);
            overflow: hidden;
            text-align: center;
        }
        h1 {
            font-size: 50px;
            color: white;
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.3);
            animation: fadeIn 2s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.8); }
            to { opacity: 1; transform: scale(1); }
        }
        .confetti {
            position: fixed;
            top: 0;
            left: 50%;
            width: 10px;
            height: 10px;
            background: red;
            opacity: 0.7;
            transform: rotate(45deg);
            animation: fall linear infinite;
        }
        @keyframes fall {
            from { transform: translateY(-10vh) rotate(45deg); }
            to { transform: translateY(100vh) rotate(90deg); }
        }
        .button {
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 18px;
            background-color: #ff4081;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body>
    <h1>🎉 Tahniah <?php echo $_SESSION['username']; ?>! FYP Selesai! 🎉</h1>
    <button class="button" onclick="playMusic()">Mainkan Muzik</button>
    <audio id="celebrationMusic" src="celebration.mp3" loop></audio>
    <script>
        function createConfetti() {
            for (let i = 0; i < 100; i++) {
                let confetti = document.createElement("div");
                confetti.classList.add("confetti");
                confetti.style.left = Math.random() * 100 + "vw";
                confetti.style.backgroundColor = `hsl(${Math.random() * 360}, 100%, 50%)`;
                confetti.style.animationDuration = Math.random() * 3 + 2 + "s";
                document.body.appendChild(confetti);
            }
        }
        createConfetti();
        function playMusic() {
            document.getElementById("celebrationMusic").play();
        }
    </script>
</body>
</html>
