<?php
@include '../CHURCH/config.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $select = "SELECT * FROM user WHERE reset_token = '$token' AND reset_expires > NOW()";
    $result = mysqli_query($conn, $select);

    if (mysqli_num_rows($result) > 0) {
        if (isset($_POST['submit'])) {
            $pass = md5($_POST['password']);
            $cpass = md5($_POST['cpassword']);

            if ($pass == $cpass) {
                $update = "UPDATE user SET password = '$pass', reset_token = NULL, reset_expires = NULL WHERE reset_token = '$token'";
                mysqli_query($conn, $update);
                echo "Password has been reset. <a href='../CHURCH/login_form.php'>Login now</a>";
            } else {
                echo "Passwords do not match!";
            }
        }
    } else {
        echo "Invalid or expired token!";
    }
} else {
    echo "No token provided!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Reset Password</title>
   <link rel="stylesheet" href="../CHURCH/css/registration.css">
</head>
<body>

<div class="form-container">
   <form action="" method="post">
      <h3>Reset Password</h3>
      <input type="password" name="password" required placeholder="Enter new password">
      <input type="password" name="cpassword" required placeholder="Confirm new password">
      <input type="submit" name="submit" value="Change Password" class="form-btn">
   </form>
</div>

</body>
</html>
