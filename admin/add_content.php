<?php
include '../components/connect.php';

if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   $tutor_id = ''; // Fixed the space to an empty string
   header('location: login.php');
}

if(isset($_POST['submit'])){
    $id = unique_id();
    $title = $_POST['title'];
    $title = filter_var($title, FILTER_SANITIZE_STRING);
    $description = $_POST['description'];
    $description = filter_var($description, FILTER_SANITIZE_STRING);
    $status = $_POST['status'];
    $status = filter_var($status, FILTER_SANITIZE_STRING);
    $playlist = $_POST['playlist'];
      $playlist = filter_var($playlist, FILTER_SANITIZE_STRING);
    $image = $_FILES['image']['name'];
    $image = filter_var($image, FILTER_SANITIZE_STRING);
    $ext = pathinfo($image, PATHINFO_EXTENSION);
    $rename_image = unique_id().'.'.$ext;
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = '../uploaded_files/'.$rename_image;

    $videos = $_FILES['video']['name'];
    $videos = filter_var($videos, FILTER_SANITIZE_STRING);
    $video_ext = pathinfo($videos, PATHINFO_EXTENSION);
    $rename_video = unique_id().'.'.$video_ext;
    $video_tmp_name = $_FILES['video']['tmp_name'];
    $video_folder = '../uploaded_files/'.$rename_video;

    if($image_size > 2000000){
        $message[] = 'image size is too large!';
    }else{
      $add_playlist = $conn->prepare("INSERT INTO `content`(id, tutor_id, playlist_id, title, description, video, thumb, status) VALUES(?,?,?,?,?,?,?,?)");
      $add_playlist->execute([$id, $tutor_id, $playlist, $title, $description, $rename_video, $rename_image, $status]);
      move_uploaded_file($image_tmp_name, $image_folder);
      move_uploaded_file($video_tmp_name, $video_folder);
      $message[] = 'Content uploaded successfully!';
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
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>add playlist</title>

   <!-- Boxicons link -->
   <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

   <!-- Custom CSS link -->
   <link rel="stylesheet" href="../css/admin_style.css">


</head>
<body>

   <?php include '../components/admin_header.php'; ?>
   <section class="video-form">

<h1 class="heading"> Upload Content</h1>

<form action="" method="post" enctype="multipart/form-data">

  <p>Playlist Status <span>*</span></p>
  <select name="status" class="box" required>
    <option value="" selected disabled>--- Select Status ---</option>
    <option value="active">Active</option>
    <option value="deactive">Deactive</option>
  </select>

  <p>Video Title <span>*</span></p>
  <input type="text" name="title" maxlength="150" required placeholder="Enter playlist title" class="box">

  <p>Video Description <span>*</span></p>
  <textarea name="description" class="box" placeholder="Write description" maxlength="1000" cols="30" rows="10" required></textarea>

  <p>Video Playlist<span>*</span></p>
  
  <select name="playlist" id="" class="box" required>
   <option value="selected disabled">---select playlist---</option>
   <?php
   $select_playlist = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id = ?");
   $select_playlist->execute([$tutor_id]);
   if($select_playlist->rowCount() > 0){
      while($fetch_playlist = $select_playlist->fetch(PDO::FETCH_ASSOC)){
         ?>
         <option value="<?= $fetch_playlist['id']; ?>"><?= $fetch_playlist['title']; ?>></option>
         <?php
      }
   ?>
   <?php
   }else{
      echo '<option value="">No playlist available</option>';
   }
   ?>
  </select>
  <p>Select Thumbnail<span>*</span></p>
  <input type="file" name="image" accept="image/*" required class="box">
  <p>Select Video<span>*</span></p>
  <input type="file" name="video" accept="video/*" required class="box">
  <input type="submit" name="submit" value="Upload video" class="btn">

</form>

</section>

<?php include '../components/footer.php'; ?>
   <script src="../js/admin_script.js"></script>
</body>
</html>
