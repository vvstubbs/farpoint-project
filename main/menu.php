<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
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
    <h1>Menu</h1>
    <ul>
        <li><a href="command_interface.php">Command Line Interface</a></li>
        <li><a href="nmap.php">Network Scanner</a></li>
        <li><a href="placeholder3.php">Placeholder 3</a></li>
        <li><a href="placeholder4.php">Placeholder 4</a></li>
        <li><a href="placeholder5.php">Placeholder 5</a></li>
        <li><a href="edit_profile.php">Edit Profile</a></li>
        <li><a href="edit_personal_info.php">Edit Personal Information</a></li>
        <li><a href="change_password.php">Change Password</a></li>
    </ul>
    <a href="logout.php">Logout</a>
</body>
</html>
