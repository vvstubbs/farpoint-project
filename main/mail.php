<?php
function sendVerificationEmail($email, $verificationCode) {
    $subject = "Verify Your Email";
    $message = "Click the following link to verify your email: http://yourdomain.com/verify.php?code=$verificationCode";
    $headers = "From: no-reply@yourdomain.com";

    mail($email, $subject, $message, $headers);
}
?>
