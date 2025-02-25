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
    $dateOfBirth = $_POST['dateOfBirth'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $country = $_POST['country'];
    $postalCode = $_POST['postalCode'];
    $phoneNumber = $_POST['phoneNumber'];

    $sql = "UPDATE UserProfile SET
            DateOfBirth = ?,
            Gender = ?,
            Address = ?,
            City = ?,
            State = ?,
            Country = ?,
            PostalCode = ?,
            PhoneNumber = ?
            WHERE UserID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssi", $dateOfBirth, $gender, $address, $city, $state, $country, $postalCode, $phoneNumber, $userID);

    if ($stmt->execute()) {
        $success = "Personal information updated successfully!";
    } else {
        $error = "Error updating personal information: " . $stmt->error;
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
    <title>Edit Personal Information</title>
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
    <h1>Edit Personal Information</h1>
    <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>
    <form method="POST" action="edit_personal_info.php">
        <label for="dateOfBirth">Date of Birth:</label>
        <input type="date" id="dateOfBirth" name="dateOfBirth" value="<?php echo $user['DateOfBirth']; ?>" required><br>
        <label for="gender">Gender:</label>
        <select id="gender" name="gender" required>
            <option value="Male" <?php if ($user['Gender'] == 'Male') echo 'selected'; ?>>Male</option>
            <option value="Female" <?php if ($user['Gender'] == 'Female') echo 'selected'; ?>>Female</option>
            <option value="Other" <?php if ($user['Gender'] == 'Other') echo 'selected'; ?>>Other</option>
        </select><br>
        <label for="address">Address:</label>
        <input type="text" id="address" name="address" value="<?php echo $user['Address']; ?>" required><br>
        <label for="city">City:</label>
        <input type="text" id="city" name="city" value="<?php echo $user['City']; ?>" required><br>
        <label for="state">State:</label>
        <input type="text" id="state" name="state" value="<?php echo $user['State']; ?>" required><br>
        <label for="country">Country:</label>
        <input type="text" id="country" name="country" value="<?php echo $user['Country']; ?>" required><br>
        <label for="postalCode">Postal Code:</label>
        <input type="text" id="postalCode" name="postalCode" value="<?php echo $user['PostalCode']; ?>" required><br>
        <label for="phoneNumber">Phone Number:</label>
        <input type="text" id="phoneNumber" name="phoneNumber" value="<?php echo $user['PhoneNumber']; ?>" required><br>
        <button type="submit">Update Personal Information</button>
    </form>
    <a href="menu.php">Back to Menu</a>
</body>
</html>
