<?php
session_start();
require 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mainEmail = $_POST['mainEmail'];
    $mainPassword = $_POST['mainPassword'];

    $sql = "SELECT * FROM UserProfile WHERE MainEmail = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $mainEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($mainPassword, $user['MainPassword'])) {
            $_SESSION['user_id'] = $user['UserID'];
            header("Location: menu.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No user found with this email.";
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
    <h1>Login</h1>
    <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="POST" action="login.php">
        <label for="mainEmail">Main Email:</label>
        <input type="email" id="mainEmail" name="mainEmail" required><br>
        <label for="mainPassword">Main Password:</label>
        <input type="password" id="mainPassword" name="mainPassword" required><br>
        <button type="submit">Login</button>
    </form>
    <a href="register.php">Don't have an account? Register</a>
</body>
</html>
