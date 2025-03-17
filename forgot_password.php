<?php
@include '../CHURCH/config.php';

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    $select = "SELECT * FROM user WHERE email = '$email'";
    $result = mysqli_query($conn, $select);
    
    if (mysqli_num_rows($result) > 0) {
        $token = bin2hex(random_bytes(50));
        $update = "UPDATE user SET reset_token = '$token', reset_expires = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = '$email'";
        
        if (mysqli_query($conn, $update)) {
            // Send reset link to user email
            $reset_link = "http://localhost/../CHURCH/reset_password.php?token=$token";
            $subject = "Password Reset";
            $message = "Click this link to reset your password: <a href='$reset_link'>$reset_link</a>";
            $headers = "From: owuorjoy42@gmail.com\r\n";
            $headers .= "Reply-To: owuorjoy42@gmail.com\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            
            if (mail($email, $subject, $message, $headers)) {
                echo "A reset link has been sent to your email.";
            } else {
                error_log("Mail function failed for email: $email", 0);
                echo "There was an error sending the email.";
            }
        } else {
            error_log("Database update failed for email: $email", 0);
            echo "There was an error updating the token.";
        }
    } else {
        echo "No account found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Forgot Password</title>
   <link rel="stylesheet" href="../CHURCH/css/registration.css">
</head>
<body>

<div class="form-container">
   <form action="" method="post">
      <h3>Reset Password</h3>
      <input type="email" name="email" required placeholder="Enter your email">
      <input type="submit" name="submit" value="Submit" class="form-btn">
   </form>
</div>

</body>
</html>

