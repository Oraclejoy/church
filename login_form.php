<?php

@include '../config.php';

session_start();

if(isset($_POST['submit'])){

   // Sanitize and validate inputs
   $email = isset($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : '';
   $pass = isset($_POST['password']) ? md5($_POST['password']) : '';

   if($email && $pass) {
       // Query to check if the user exists
       $select = " SELECT * FROM user WHERE email = '$email' && password = '$pass' ";

       $result = mysqli_query($conn, $select);

       if(mysqli_num_rows($result) > 0){

          $row = mysqli_fetch_array($result);

          if($row['user_type'] == 'admin'){

             $_SESSION['admin_name'] = $row['name'];
             header('location:../CHURCH/admin/sermons.php');
             exit();

          }elseif($row['user_type'] == 'user'){

             $_SESSION['user_name'] = $row['name'];
             header('location:../CHURCH/user/sermonuser.php');
             exit();

          }
         
       }else{
          $error[] = 'incorrect email or password!';
       }
   } else {
       $error[] = 'Email and password are required!';
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>login form</title>

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../CHURCH/css/registration.css">

</head>
<body>
   
<div class="form-container">

   <form action="" method="post">
      <h3>LOGIN</h3>
      <?php
      if(isset($error)){
         foreach($error as $errorMsg){
            echo '<span class="error-msg">'.$errorMsg.'</span>';
         };
      };
      ?>
      <input type="email" name="email" required placeholder="enter your email">
      <input type="password" name="password" required placeholder="enter your password">
      <input type="submit" name="submit" value="login now" class="form-btn">
      <p>Forgot your password? <a href="forgot_password.php">Reset it here</a></p>

      <p>don't have an account? <a href="register_form.php">register </a></p>
   </form>

</div>

</body>
</html>
