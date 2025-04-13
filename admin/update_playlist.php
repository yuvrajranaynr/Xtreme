<?php
include '../components/connect.php';

if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   $tutor_id = ''; // Fixed the space to an empty string
   header('location: login.php');
}
if(isset($_GET['get_id'])){
   $get_id = $_GET['get_id'];
}else{
   $get_id = ''; // Fixed the space to an empty string
   header('location: playlists.php');
}
if(isset($_POST['update'])){
   $title = $_POST['title'];
   $title = filter_var($title, FILTER_SANITIZE_STRING);
   $description = $_POST['description'];
   $description = filter_var($description, FILTER_SANITIZE_STRING);
   $status = $_POST['status'];
   $status = filter_var($status, FILTER_SANITIZE_STRING);

   // Initialize $rename_image to avoid undefined variable warning
   $rename_image = '';

   $update_playlist = $conn->prepare("UPDATE `playlist` SET title = ?, description = ?, thumb = ?, status = ? WHERE id = ?");
   $update_playlist->execute([$title, $description, $rename_image, $status, $get_id]);
   $old_image = $_POST['old_image'];
   $old_image = filter_var($old_image, FILTER_SANITIZE_STRING);

    $image = $_FILES['image']['name'];
    $image = filter_var($image, FILTER_SANITIZE_STRING);
    $ext = pathinfo($image, PATHINFO_EXTENSION);

    $rename_image = unique_id().'.'.$ext;
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = '../uploaded_files/'.$rename_image;

    if (!empty($image)){
      if($image_size > 2000000){
         $message[] = 'Image size is too large!';
      }else{
         $update_image = $conn->prepare("UPDATE `playlist` SET thumb = ? WHERE id = ?");
         $update_image->execute([ $rename_image, $get_id]);
         move_uploaded_file($image_tmp_name, $image_folder);


         if($old_image !='' AND $old_image != $rename_image){
            unlink('../uploaded_files/'.$old_image);
      }
   }
}
$message[] = 'Playlist updated successfully!';
  // header('location: playlists.php');
}

if(isset($_POST['delete'])){
    $delete_id = $_POST['playlist_id'];
      $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);
      $delete_playlist_thumb = $conn->prepare("SELECT * FROM `playlist` WHERE id = ? LIMIT 1");
         $delete_playlist_thumb->execute([$delete_id]);
         $fetch_thumb = $delete_playlist_thumb->fetch(PDO::FETCH_ASSOC);
        unlink('../uploaded_files/' . $fetch_thumb['thumb']);

         $delete_bookmark = $conn->prepare("DELETE FROM `bookmark` WHERE playlist_id = ?");
         $delete_bookmark->execute([$delete_id]);
         $delete_playlist = $conn->prepare("DELETE FROM `playlist` WHERE id = ?");
         $delete_playlist->execute([$delete_id]);
         header('location: playlists.php');
}

// Initialize $total_videos to avoid undefined variable warning
$total_videos = 0;

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
   <section class="playlist-form">

<h1 class="heading">UpdatePlaylist</h1>
    <?php
      $select_playlist = $conn->prepare("SELECT * FROM `playlist` WHERE id = ?"); 
        $select_playlist->execute([$get_id]); 
        if($select_playlist->rowCount() > 0){
           while( $fetch_playlist = $select_playlist->fetch(PDO::FETCH_ASSOC)){
            $playlist_id = $fetch_playlist['id'];
            $count_videos = $conn->prepare("SELECT * FROM `content` WHERE playlist_id = ?");
            $count_videos->execute([$playlist_id]);
            $count_videos = $count_videos->rowCount();
           

    ?>
<form action="" method="post" enctype="multipart/form-data">
  <input type="hidden" name="old_image" value="<?= $fetch_playlist['thumb']; ?>">
  <input type="hidden" name="playlist_id" value="<?= $playlist_id; ?>">
  <p>Playlist Status <span>*</span></p>
  <select name="status" class="box" required>
    <option value="<?= $fetch_playlist['status'];?>" selected disabled>--- Select Status ---</option>
    <option value="active">Active</option>
    <option value="deactive">Deactive</option>
  </select>

  <p>Playlist Title <span>*</span></p>
  <input type="text" name="title" maxlength="150" required placeholder="Enter playlist title" value="<?= $fetch_playlist['title'];?>" class="box">

  <p>Playlist Description <span>*</span></p>
  <textarea name="description" class="box" placeholder="Write description" maxlength="1000" cols="30" rows="10" required><?= $fetch_playlist['description'];?></textarea>

  <p>Playlist Thumbnail <span>*</span></p>
  <div class="thumb">
    <span><?= $total_videos; ?></span>
    <img src="../uploaded_files/<?= $fetch_playlist['thumb']; ?>" alt="Playlist Thumbnail">
  </div>
  <input type="file" name="image" accept="image/*"  class="box">
  <div class="flex-btn">
  <input type="submit" name="update" value="Update Playlist" class="btn">
  <input type="submit" name="delete" value="Delete Playlist" class="btn" onclick="return confirm('Delete this playlist?');">
  <a href="view_playlist.php ? get_id = <?= $playlist_id ;?>" class="btn">View Playlist</a>
    </div>
</form>
<?php 
       }
    }else{
        echo '<p class="empty">No playlist found!</p>';
    }
    ?>
</section>

<?php include '../components/footer.php'; ?>
   <script src="../js/admin_script.js"></script>
</body>
</html>
