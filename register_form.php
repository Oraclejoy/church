<?php

@include 'config.php';

if (isset($_POST['submit'])) {

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = md5($_POST['password']);
    $cpass = md5($_POST['cpassword']);
    $user_type = $_POST['user_type'];

    // Check if the email is already registered
    $select = "SELECT * FROM user WHERE email = '$email'";
    $result = mysqli_query($conn, $select);

    if (mysqli_num_rows($result) > 0) {
        $error[] = 'User already exists!';
    } else {
        // Check if the passwords match
        if ($pass != $cpass) {
            $error[] = 'Passwords do not match!';
        } else {
            // Check the number of existing admins
            if ($user_type == 'admin') {
                $admin_check = "SELECT COUNT(*) as admin_count FROM user WHERE user_type = 'admin'";
                $admin_result = mysqli_query($conn, $admin_check);
                $admin_row = mysqli_fetch_assoc($admin_result);

                if ($admin_row['admin_count'] >= 1) {
                    $error[] = 'Admin registration is not allowed. Kindly register as a member or pastor.';
                } else {
                    // Insert new user as admin
                    $insert = "INSERT INTO user(name, email, password, user_type) VALUES('$name','$email','$pass','$user_type')";
                    mysqli_query($conn, $insert);
                    header('location:login_form.php');
                }
            } else {
                // Insert new user as regular user or pastor
                $insert = "INSERT INTO user(name, email, password, user_type) VALUES('$name','$email','$pass','$user_type')";
                mysqli_query($conn, $insert);
                header('location:login_form.php');
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Form</title>

    <!-- custom css file link  -->
    <link rel="stylesheet" href="../CHURCH/css/registration.css">

</head>
<body>
    
<div class="form-container">

    <form action="" method="post">
        <h3>REGISTER</h3>
        <?php
        if (isset($error)) {
            foreach ($error as $errorMsg) {
                echo '<span class="error-msg">' . $errorMsg . '</span>';
            }
        }
        ?>
        <input type="text" name="name" required placeholder="Enter your name">
        <input type="email" name="email" required placeholder="Enter your email">
        <input type="password" name="password" required placeholder="Enter your password">
        <input type="password" name="cpassword" required placeholder="Confirm your password">
        <select name="user_type">
            <option value="member">Member</option>
            <option value="admin">Admin</option>
            <option value="pastor">Pastor</option>
        </select>
        <input type="submit" name="submit" value="Register" class="form-btn">
        <p>Already have an account? <a href="login_form.php">Login Now</a></p>
    </form>

</div>

</body>
</html>
