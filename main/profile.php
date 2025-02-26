<?php
session_start();
require 'db.php';
require 'mail.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['user_id'];
$sql = "SELECT * FROM UserProfile WHERE UserID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['updateProfile'])) {
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $mainEmail = $_POST['mainEmail'];

        if ($mainEmail != $user['MainEmail']) {
            $verificationCode = bin2hex(random_bytes(16));
            $sql = "UPDATE UserProfile SET VerificationCode = ? WHERE UserID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $verificationCode, $userID);
            $stmt->execute();
            $stmt->close();

            sendVerificationEmail($mainEmail, $verificationCode);
            $success = "Verification email sent to new main email address.";
        } else {
            $sql = "UPDATE UserProfile SET FirstName = ?, LastName = ? WHERE UserID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $firstName, $lastName, $userID);
            $stmt->execute();
            $stmt->close();
            $success = "Profile updated successfully!";
        }
    } elseif (isset($_POST['changePassword'])) {
        $currentPassword = $_POST['currentPassword'];
        $newPassword = $_POST['newPassword'];
        $confirmPassword = $_POST['confirmPassword'];

        if (password_verify($currentPassword, $user['MainPassword'])) {
            if ($newPassword === $confirmPassword) {
                $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
                $sql = "UPDATE UserProfile SET MainPassword = ? WHERE UserID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $hashedPassword, $userID);
                $stmt->execute();
                $stmt->close();
                $success = "Main Password updated successfully!";
            } else {
                $error = "New passwords do not match.";
            }
        } else {
            $error = "Current password is incorrect.";
        }
    } elseif (isset($_POST['addAdditionalPassword'])) {
        $additionalPassword = password_hash($_POST['additionalPassword'], PASSWORD_BCRYPT);
        for ($i = 1; $i <= 4; $i++) {
            if (empty($user["AdditionalPassword$i"])) {
                $sql = "UPDATE UserProfile SET AdditionalPassword$i = ? WHERE UserID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $additionalPassword, $userID);
                $stmt->execute();
                $stmt->close();
                $success = "Additional Password $i added successfully!";
                break;
            }
        }
    } elseif (isset($_POST['editAdditionalPassword'])) {
        $passwordIndex = $_POST['passwordIndex'];
        $newPassword = password_hash($_POST['newPassword'], PASSWORD_BCRYPT);

        $sql = "UPDATE UserProfile SET AdditionalPassword$passwordIndex = ? WHERE UserID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $newPassword, $userID);
        $stmt->execute();
        $stmt->close();
        $success = "Additional Password $passwordIndex updated successfully!";
    } elseif (isset($_POST['addEmail'])) {
        $newEmail = $_POST['newEmail'];
        for ($i = 2; $i <= 6; $i++) {
            if (empty($user["Email$i"])) {
                $sql = "UPDATE UserProfile SET Email$i = ? WHERE UserID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $newEmail, $userID);
                $stmt->execute();
                $stmt->close();
                $success = "Email $i added successfully!";
                break;
            }
        }
    } elseif (isset($_POST['editEmail'])) {
        $emailIndex = $_POST['emailIndex'];
        $newEmail = $_POST['newEmail'];

        $sql = "UPDATE UserProfile SET Email$emailIndex = ? WHERE UserID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $newEmail, $userID);
        $stmt->execute();
        $stmt->close();
        $success = "Email $emailIndex updated successfully!";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
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
    <h1>Profile</h1>
    <button class="toggle-button" onclick="toggleDarkMode()">Toggle Dark Mode</button>
    <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>
    <form method="POST" action="profile.php">
        <label for="firstName">First Name:</label>
        <input type="text" id="firstName" name="firstName" value="<?php echo $user['FirstName']; ?>" required><br>
        <label for="lastName">Last Name:</label>
        <input type="text" id="lastName" name="lastName" value="<?php echo $user['LastName']; ?>" required><br>
        <label for="mainEmail">Main Email:</label>
        <input type="email" id="mainEmail" name="mainEmail" value="<?php echo $user['MainEmail']; ?>" required><br>
        <button type="submit" name="updateProfile">Update Profile</button>
    </form>

    <h2>Change Main Password</h2>
    <form method="POST" action="profile.php">
        <label for="currentPassword">Current Password:</label>
        <input type="password" id="currentPassword" name="currentPassword" required><br>
        <label for="newPassword">New Password:</label>
        <input type="password" id="newPassword" name="newPassword" required><br>
        <label for="confirmPassword">Confirm New Password:</label>
        <input type="password" id="confirmPassword" name="confirmPassword" required><br>
        <button type="submit" name="changePassword">Change Password</button>
    </form>

    <h2>Manage Additional Passwords</h2>
    <form method="POST" action="profile.php">
        <label for="additionalPassword">Add Additional Password:</label>
        <input type="password" id="additionalPassword" name="additionalPassword"><br>
        <button type="submit" name="addAdditionalPassword">Add Password</button>
    </form>

    <?php for ($i = 1; $i <= 4; $i++): ?>
        <?php if (!empty($user["AdditionalPassword$i"])): ?>
            <h3>Edit Additional Password <?php echo $i; ?></h3>
            <form method="POST" action="profile.php">
                <input type="hidden" name="passwordIndex" value="<?php echo $i; ?>">
                <label for="newPassword<?php echo $i; ?>">New Password:</label>
                <input type="password" id="newPassword<?php echo $i; ?>" name="newPassword" required><br>
                <button type="submit" name="editAdditionalPassword">Update Password</button>
            </form>
        <?php endif; ?>
    <?php endfor; ?>

    <h2>Manage Emails</h2>
    <form method="POST" action="profile.php">
        <label for="newEmail">Add Additional Email:</label>
        <input type="email" id="newEmail" name="newEmail"><br>
        <button type="submit" name="addEmail">Add Email</button>
    </form>

    <?php for ($i = 2; $i <= 6; $i++): ?>
        <?php if (!empty($user["Email$i"])): ?>
            <h3>Edit Email <?php echo $i; ?></h3>
            <form method="POST" action="profile.php">
                <input type="hidden" name="emailIndex" value="<?php echo $i; ?>">
                <label for="newEmail<?php echo $i; ?>">New Email:</label>
                <input type="email" id="newEmail<?php echo $i; ?>" name="newEmail" value="<?php echo $user["Email$i"]; ?>" required><br>
                <button type="submit" name="editEmail">Update Email</button>
            </form>
        <?php endif; ?>
    <?php endfor; ?>

    <a href="logout.php">Logout</a>
</body>
</html>
