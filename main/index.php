<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: menu.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
            localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
        }

        document.addEventListener('DOMContentLoaded', function() {
            const darkMode = localStorage.getItem('darkMode');
            if (darkMode === 'true') {
                document.body.classList.add('dark-mode');
            }
        });
    </script>
</head>
<body>
    <button class="toggle-button" onclick="toggleDarkMode()">Toggle Dark Mode</button>
    <h1>Welcome to the Authentication System</h1>
    <a href="register.php">Register</a> | <a href="login.php">Login</a>
</body>
</html>
