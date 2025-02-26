<?php
require 'db.php';

if (isset($_GET['code'])) {
    $verificationCode = $_GET['code'];

    $sql = "SELECT * FROM UserProfile WHERE VerificationCode = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $verificationCode);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $sql = "UPDATE UserProfile SET MainEmail = ?, VerificationCode = NULL WHERE UserID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $user['MainEmail'], $user['UserID']);
        $stmt->execute();
        $stmt->close();
        echo "Email verified successfully!";
    } else {
        echo "Invalid verification code.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Verification code not provided.";
}
?>
