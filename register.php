<?php
include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    $user_id = '';
}

if(isset($_POST['submit'])){
    $id = unique_id();
    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    
    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_STRING);
    $pass = $_POST['pass'];
    $pass = filter_var($pass, FILTER_SANITIZE_STRING);
    $cpass = $_POST['cpass'];
    $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);
    $image = $_FILES['image']['name'];
    $image = filter_var($image, FILTER_SANITIZE_STRING);
    $ext = pathinfo($image, PATHINFO_EXTENSION);
    $rename_image = unique_id().'.'.$ext;
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = $upload_dir.'/'.$rename_image;

    $select_user = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $select_user->execute([$email]);
    if($select_user->rowCount() > 0){
        $message[] = 'email already exist!';
    }else{
        if($pass != $cpass){
            $message[] = 'confirm password not matched!';
        }
        else{
            $insert_user = $conn->prepare("INSERT INTO `users` (id, name, email, password, image) VALUES(?,?,?,?,?)");
            $insert_user->execute([$id, $name, $email, $cpass, $rename_image]);
            move_uploaded_file($image_tmp_name, $image_folder);
            $verify_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND password = ? LIMIT 1");
            $verify_user->execute([$email, $cpass]);
            $row = $verify_user->fetch(PDO::FETCH_ASSOC);
            if($verify_user->rowCount() > 0){
                setcookie('user_id', $row['id'], time() + 60*60*24, '/');
                header('location: index.php');
            }
        }
    }
}
?>
<style>
    <?php include 'css/user_style.css'; ?>
</style>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HiStudy- Courses Page</title>

    <!-- Boxicons CDN -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/user_style.css">
</head>
<body>
<?php include 'components/user_header.php'; ?>

<!---------------------------------- banner section------------------------------------- -->
<div class="banner">
  <div class="detail">
    <div class="title">
      <a href="index.php">home</a>
      <span> <i class="bx bx-chevron-right"></i> register now  </span>
    </div>
    <h1>Register Now</h1>
    <p>Dive in and learn React.js from scratch! Learn Reactjs, Hooks, Redux, React Routing, Animations, Next.js and way more!</p>
    <div class="flex-btn">
      <a href="login.php" class="btn">login to start</a>
      <a href="contact.php" class="btn">contact us</a>
    </div>
  </div>
  <img src="image/about.png">
</div>

<!--------------------------registration section----------------------------------->
<section class="form-container">
    <div class="heading">
        <span>Join HiStudy</span>
        <h1>Create Account</h1>
    </div>
    <form class="register" action="" method="post" enctype="multipart/form-data">
        <div class="flex">
            <div class="col">
            <p>your name <span>*</span></p>
            <input type="text" name="name" placeholder="enter your name" maxlength="50" required class="box">
    
            <p>your email <span>*</span></p>
            <input type="email" name="email" placeholder="enter your email" maxlength="50" required class="box">
            </div>
            <div class="col">
            <p>your password <span>*</span></p>
            <input type="password" name="pass" placeholder="enter your password" maxlength="20" required class="box">

            <p>confirm password <span>*</span></p>
            <input type="password" name="cpass" placeholder="confirm your password" maxlength="20" required class="box">

    
         </div>
        </div>
        <p>select pic <span>*</span></p>
            <input type="file" name="image" accept="image/*" required class="box">
            <p class="link">already have an account? <a href="admin/login.php">login now</a></p>
      <input type="submit" name="submit" class="btn" value="register now">
    </form>
</section>



    <?php include 'components/footer.php'; ?>
    <script type="text/javascript" src="js/user_script.js"></script>
</body>
</html>