<?php
include '../components/connect.php';

if(isset($_COOKIE['tutor_id'])){
    $tutor_id = $_COOKIE['tutor_id'];
 }else{
    $tutor_id = '';
    header('location: login.php');
 }

 if(isset($_POST['submit'])){
    $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE id = ? LIMIT 1");
    $select_tutor->execute([$tutor_id]);
    $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);

    $prev_pass = $fetch_tutor['password'];
    $prev_image = $fetch_tutor['image'];
    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $profession = $_POST['profession'];
    $profession = filter_var($profession, FILTER_SANITIZE_STRING);
    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_STRING);

    if(!empty($name)){
        $update_name = $conn->prepare("UPDATE `tutors` SET name = ? WHERE id = ?");
        $update_name->execute([$name, $tutor_id]);
        $message[] = 'username updated successfully!';
    }
    if(!empty($profession)){
        $update_profession = $conn->prepare("UPDATE `tutors` SET profession = ? WHERE id = ?");
        $update_profession->execute([$profession, $tutor_id]);
        $message[] = 'profession updated successfully!';
    }
    if(!empty($email)){
        $select_email = $conn->prepare("SELECT * FROM `tutors` WHERE email = ? AND id != ?");
        $select_email->execute([$email, $tutor_id]);
        if($select_email->rowCount() > 0){
            $message[] = 'Email already exists!';
        }else{
            $update_email = $conn->prepare("UPDATE `tutors` SET email = ? WHERE id = ?");
            $update_email->execute([$email, $tutor_id]);
            $message[] = 'Email updated successfully!';
        }
    }

 $image = $_FILES['image']['name'];
 $image = filter_var($image, FILTER_SANITIZE_STRING);
    $ext = pathinfo($image, PATHINFO_EXTENSION);
    $rename_image = unique_id().'.'.$ext;
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = '../uploaded_files/'.$rename_image;
    if(!empty($image)){
        if($image_size > 2000000){
            $message[] = 'image size is too large!';
        }else{
            $update_image = $conn->prepare("UPDATE `tutors` SET image = ? WHERE id = ?");
            $update_image->execute([$rename_image, $tutor_id]);
            move_uploaded_file($image_tmp_name, $image_folder);
            if($prev_image != 'default.png' && $prev_image != $rename_image){
                unlink('../uploaded_files/'.$prev_image);
            }
        }
    }

    $empty_pass  = 'da39a3ee5e6b4b0d3255bfef95601890afd80709';
    $old_pass = $_POST['old_pass'];
    $old_pass = filter_var($old_pass, FILTER_SANITIZE_STRING);
    $new_pass = $_POST['new_pass'];
    $new_pass = filter_var($new_pass, FILTER_SANITIZE_STRING);
    $cpass = $_POST['cpass'];
    $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

    // Debugging: Log the values of $prev_pass and $old_pass to the browser console
    echo "<script>console.log('Stored password (hashed): $prev_pass');</script>";
    echo "<script>console.log('Entered old password: $old_pass');</script>";

    if($old_pass !== $empty_pass){
        // Use password_verify to compare the entered old password with the stored hashed password
        if (!password_verify($old_pass, $prev_pass)) {
            $message[] = 'Old password does not match!';
        } else {
            if ($new_pass !== $cpass) {
                $message[] = 'Confirm password does not match!';
            } else {
                $hashed_new_pass = password_hash($cpass, PASSWORD_DEFAULT); // Hash the new password before storing
                $update_pass = $conn->prepare("UPDATE `tutors` SET password = ? WHERE id = ?");
                $update_pass->execute([$hashed_new_pass, $tutor_id]);
                $message[] = 'Password updated successfully!';
            }
        }
    } else {
        $message[] = 'image updates successfully!';
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
<?php include '../components/admin_header.php'; ?>
<div class="form-container" style="min-height: calc(100vh - 19rem); padding: 5rem 0">
  
   <form action="" method="post" enctype="multipart/form-data" class="register">
      <h3>Update Profile</h3>
      <div class="flex">
         <div class="col">
            <p>your name <span>*</span></p>
            <input type="text" name="name" placeholder="<?= $fetch_profile['name']; ?>" maxlength="50" required class="box">
            <p>your profession <span>*</span></p>
            <select name="profession" required class="box">
               <option value="" disabled selected><?= $fetch_profile['name'];?></option>
               <option value="developer">developer</option>
               <option value="designer">designer</option>
               <option value="musician">musician</option>
               <option value="biologist">biologist</option>
               <option value="teacher">teacher</option>
               <option value="engineer">engineer</option>
               <option value="lawyer">lawyer</option>
               <option value="accountant">accountant</option>
               <option value="doctor">doctor</option>
               <option value="journalist">journalist</option>
               <option value="photographer">photographer</option>
               <option value="software developer">software developer</option>
            </select>

            <p>your email <span>*</span></p>
            <input type="email" name="email" placeholder="<?= $fetch_profile['email']; ?>" maxlength="50" required class="box">
         </div>

         <div class="col">
            <p>old password <span>*</span></p>
            <input type="password" name="old_pass" placeholder="enter your old password" maxlength="20" required class="box">

            <p>new password <span>*</span></p>
            <input type="password" name="new_pass" placeholder="enter your new  password" maxlength="20" required class="box">

            <p>confirm password <span>*</span></p>
            <input type="password" name="cpass" placeholder="confirm new  password" maxlength="20" required class="box">
         </div>
      </div>
      <p>Update pic <span>*</span></p>
      <input type="file" name="image" accept="image/*" required class="box">
      <input type="submit" name="submit" class="btn" value="Update Profile">
   </form>
</div>
<?php include '../components/footer.php'; ?>
<script src="../js/admin_script.js"></script>
</body>
</html>