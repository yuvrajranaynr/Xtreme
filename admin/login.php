<?php
include '../components/connect.php';

// Ensure the uploaded_files directory exists
$upload_dir = '../uploaded_files';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true); // Create the directory with write permissions
}

if(isset($_POST['submit'])){
    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_STRING);
    $pass = $_POST['pass'];
    $pass = filter_var($pass, FILTER_SANITIZE_STRING);
   
    $select_tutor = $conn->prepare("SELECT * FROM tutors WHERE email = ? AND password = ?");
    $select_tutor->execute([$email, $pass]);
    $row = $select_tutor->fetch(PDO::FETCH_ASSOC);
    if($select_tutor->rowCount() > 0){
        $message[] = 'email already exist!';
    }
    if($select_tutor->rowCount() > 0){
        setcookie('tutor_id', $row['id'], time() + 60*60*24, '/');
        header('location: dashboard.php');
    }else{
        $message[] = 'incorrect email or password!';
    }
}
?>
<style>
    <?php
    include '../css/admin_style.css'; // Include your CSS file here
    ?>
</style>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User LogIn</title>
     <!-- Boxicons link -->
   <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
<?php
if (isset($message)) {
   foreach ($message as $message) {
      echo '
      <div class="message">
         <span>' . $message . '</span>
         <i class="bx bx-x" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<div class="form-container">
   <img src="../image/fun.jpg" class="form-img" style="left: 4%;">
   <form action="" method="post" enctype="multipart/form-data" class="login">
      <h3>Login now</h3>
      <p>your email <span>*</span></p>
      <input type="email" name="email" placeholder="enter your email" maxlength="50" required class="box">
      <p>your password <span>*</span></p>
            <input type="password" name="pass" placeholder="enter your password" maxlength="20" required class="box">


      <p class="link">do not have an account? <a href="register.php">register now</a></p>
      <input type="submit" name="submit" class="btn" value="Login now">
   </form>
</div>
</body>
</html>