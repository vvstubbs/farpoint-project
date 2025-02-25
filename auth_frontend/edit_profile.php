<?php
session_start();
require 'db.php';

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
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $mainEmail = $_POST['mainEmail'];
    $email2 = $_POST['email2'];
    $email3 = $_POST['email3'];
    $email4 = $_POST['email4'];
    $email5 = $_POST['email5'];
    $email6 = $_POST['email6'];
    $additionalPassword1 = !empty($_POST['additionalPassword1']) ? password_hash($_POST['additionalPassword1'], PASSWORD_BCRYPT) : null;
    $additionalPassword2 = !empty($_POST['additionalPassword2']) ? password_hash($_POST['additionalPassword2'], PASSWORD_BCRYPT) : null;
    $additionalPassword3 = !empty($_POST['additionalPassword3']) ? password_hash($_POST['additionalPassword3'], PASSWORD_BCRYPT) : null;
    $additionalPassword4 = !empty($_POST['additionalPassword4']) ? password_hash($_POST['additionalPassword4'], PASSWORD_BCRYPT) : null;

    $sql = "UPDATE UserProfile SET
            FirstName = ?,
            LastName = ?,
            MainEmail = ?,
            Email2 = ?,
            Email3 = ?,
            Email4 = ?,
            Email5 = ?,
            Email6 = ?,
            AdditionalPassword1 = ?,
            AdditionalPassword2 = ?,
            AdditionalPassword3 = ?,
            AdditionalPassword4 = ?
            WHERE UserID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssssssi", $firstName, $lastName, $mainEmail, $email2, $email3, $email4, $email5, $email6, $additionalPassword1, $additionalPassword2, $additionalPassword3, $additionalPassword4, $userID);

    if ($stmt->execute()) {
        $success = "Profile updated successfully!";
        // Option 1: Reload the page
        header("Location: edit_profile.php");
        exit();

        // Option 2: Redirect to the main menu
        // header("Location: menu.php");
        // exit();
    } else {
        $error = "Error updating profile: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
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
    <h1>Edit Profile</h1>
    <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>
    <form method="POST" action="edit_profile.php">
        <label for="firstName">First Name:</label>
        <input type="text" id="firstName" name="firstName" value="<?php echo $user['FirstName']; ?>" required><br>
        <label for="lastName">Last Name:</label>
        <input type="text" id="lastName" name="lastName" value="<?php echo $user['LastName']; ?>" required><br>
        <label for="mainEmail">Main Email:</label>
        <input type="email" id="mainEmail" name="mainEmail" value="<?php echo $user['MainEmail']; ?>" required><br>
        <label for="email2">Email 2:</label>
        <input type="email" id="email2" name="email2" value="<?php echo $user['Email2']; ?>"><br>
        <label for="email3">Email 3:</label>
        <input type="email" id="email3" name="email3" value="<?php echo $user['Email3']; ?>"><br>
        <label for="email4">Email 4:</label>
        <input type="email" id="email4" name="email4" value="<?php echo $user['Email4']; ?>"><br>
        <label for="email5">Email 5:</label>
        <input type="email" id="email5" name="email5" value="<?php echo $user['Email5']; ?>"><br>
        <label for="email6">Email 6:</label>
        <input type="email" id="email6" name="email6" value="<?php echo $user['Email6']; ?>"><br>
        <label for="additionalPassword1">Additional Password 1:</label>
        <input type="password" id="additionalPassword1" name="additionalPassword1" placeholder="Leave blank to keep current"><br>
        <label for="additionalPassword2">Additional Password 2:</label>
        <input type="password" id="additionalPassword2" name="additionalPassword2" placeholder="Leave blank to keep current"><br>
        <label for="additionalPassword3">Additional Password 3:</label>
        <input type="password" id="additionalPassword3" name="additionalPassword3" placeholder="Leave blank to keep current"><br>
        <label for="additionalPassword4">Additional Password 4:</label>
        <input type="password" id="additionalPassword4" name="additionalPassword4" placeholder="Leave blank to keep current"><br>
        <button type="submit">Update Profile</button>
    </form>
    <a href="menu.php">Back to Menu</a>
</body>
</html>
