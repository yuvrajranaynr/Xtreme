<?php
include '../components/connect.php';

if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   $tutor_id = ''; // Fixed the space to an empty string
   header('location: login.php');
}

$get_id = $_GET['get_id'] ?? '';
if (empty($get_id)) {
    header('location: contents.php');
    exit;
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
<?php
$select_video = $conn->prepare("SELECT * from `content` where id = ? AND tutor_id=?");
$select_video->execute([$get_id, $tutor_id]);

if($select_video->rowCount()>0){
    while($fetch_video = $select_video->fetch(PDO::FETCH_ASSOC)){
        $video_id = $fetch_video['id'];
?>

<form action="" method="post" enctype="multipart/form-data">
<input type="hidden" name="video_id" value="<?= $fetch_video['id']?>">
<input type="hidden" name="old_thumb" value="<?= $fetch_video['thumb']?>">
<input type="hidden" name="old_video" value="<?= $fetch_video['video']?>">
  <p>Update Status <span>*</span></p>
  <select name="status" class="box" required>
    <option value="<?= $fetch_video['status']?>" selected>--- Select Status ---</option>
    <option value="active">Active</option>
    <option value="deactive">Deactive</option>
  </select>

  <p>Update Title <span>*</span></p>
  <input type="text" name="title" maxlength="150" required placeholder="Enter playlist title" class="box" value="<?= $fetch_video['title']?>">

  <p>Update Description <span>*</span></p>
  <textarea name="description" class="box" placeholder="Write description" maxlength="1000" cols="30" rows="10" value="<?= $fetch_video['description']?>"  required></textarea>

  <p>Update Playlist<span>*</span></p>
  
  <select name="playlist" id="" class="box" required>
   <option selected disabled  value="<?= $fetch_video['id']?>"><?= $fetch_video['title']?></option>
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
  <img src="../uploaded_files/<?= htmlspecialchars($fetch_video['thumb']); ?>" alt="Thumbnail" class="thumb">
  <p>Update Thumbnail<span>*</span></p>
  <input type="file" name="image" accept="image/*" required class="box">

  <video src="../uploaded_files/<?= htmlspecialchars($fetch_video['video']); ?>" controls class="video"></video>
  <p>Update Video<span>*</span></p>
  <input type="file" name="video" accept="video/*" required class="box">

  <div class="flex-btn">
      <input type="submit" name="submit" value="Upload video" class="btn">
      <a href="view_content.php?get_id=<?= $video_id; ?>" class="btn">View Content</a>
      <input type="submit" name="delete_content" value="Delete Content" class="btn" onclick="return confirm('Delete this content?');">
  </div>

</form>
<?php
    }
}else{
    echo ' <div class="empty">
         <p style="margin-bottom: 1.5rem;">No video added yet</p>
         <a href="add_content.php" class="btn" style="margin-top: 1.5rem;">add Videos</a>
        </div>';
}
?>
</section>

<?php include '../components/footer.php'; ?>
   <script src="../js/admin_script.js"></script>
</body>
</html>
