<?php
include '../components/connect.php';

if (!isset($_COOKIE['tutor_id'])) {
    header('location: login.php');
    exit;
}
$tutor_id = $_COOKIE['tutor_id'];

$playlist_id = $_GET['get_id'] ?? '';
if (empty($playlist_id)) {
    header('location: playlists.php');
    exit;
}

if(isset($_POST['update'])){
   $title = $_POST['title'];
   $title = filter_var($title, FILTER_SANITIZE_STRING);
   $description = $_POST['description'];
   $description = filter_var($description, FILTER_SANITIZE_STRING);
   $status = $_POST['status'];
   $status = filter_var($status, FILTER_SANITIZE_STRING);

   $rename_image = '';

   $update_playlist = $conn->prepare("UPDATE `playlist` SET title = ?, description = ?, thumb = ?, status = ? WHERE id = ?");
   $update_playlist->execute([$title, $description, $rename_image, $status, $playlist_id]);
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
         $update_image->execute([ $rename_image, $playlist_id]);
         move_uploaded_file($image_tmp_name, $image_folder);

         if($old_image !='' AND $old_image != $rename_image){
            unlink('../uploaded_files/'.$old_image);
         }
      }
   }
   $message[] = 'Playlist updated successfully!';
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

if (isset($_POST['delete_video'])) {
   $delete_id = $_POST['video_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   $verify_video = $conn->prepare("SELECT * FROM `content` WHERE id = ? AND tutor_id = ? LIMIT 1");
   $verify_video->execute([$delete_id, $tutor_id]);   

   if ($verify_video->rowCount() > 0) {
       $delete_video_thumb = $conn->prepare("SELECT * FROM `content` WHERE id = ? LIMIT 1");
       $delete_video_thumb->execute([$delete_id]);
       $fetch_thumb = $delete_video_thumb->fetch(PDO::FETCH_ASSOC);
       if (!empty($fetch_thumb['thumb']) && file_exists('../uploaded_files/' . $fetch_thumb['thumb']) && !is_dir('../uploaded_files/' . $fetch_thumb['thumb'])) {
           unlink('../uploaded_files/' . $fetch_thumb['thumb']);
       }

       $delete_video = $conn->prepare("DELETE FROM `content` WHERE id = ? LIMIT 1");
       $delete_video->execute([$delete_id]);
       $fetch_video = $delete_video->fetch(PDO::FETCH_ASSOC);
       unlink('../uploaded_files/'.$delete_video);
       
       $delete_likes = $conn->prepare("DELETE FROM `likes` WHERE content_id = ?");
       $delete_likes->execute([$delete_id]);

       $delete_comments = $conn->prepare("DELETE FROM `comments` WHERE content_id = ?");
       $delete_comments->execute([$delete_id]);

       $delete_content = $conn->prepare("DELETE FROM `content` WHERE id = ?");
       $delete_content->execute([$delete_id]);
       $message[] = 'Content deleted successfully!';
   } else {
       $message[] = 'Content already deleted!';
   }
}

$total_videos = 0;

?>
   <style>
    <?php
    include '../css/admin_style.css';
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
   <section class="view-playlist">

    <h1 class="heading">Playlist Detail</h1>
    <?php
    $select_playlist = $conn->prepare("SELECT * FROM `playlist` WHERE id = ? AND tutor_id = ?");
    $select_playlist->execute([$playlist_id, $tutor_id]);
    if($select_playlist->rowCount() > 0){
        while($fetch_playlist = $select_playlist->fetch(PDO::FETCH_ASSOC)){
            $playlist_id = $fetch_playlist['id'];
            $count_videos = $conn->prepare("SELECT * FROM `content` WHERE playlist_id = ?");
            $count_videos->execute([$playlist_id]);
            $total_videos = $count_videos->rowCount();
     ?>
        <div class="row">
            <div class="thumb">
                <span><?= $total_videos; ?></span>
                <img src="../uploaded_files/<?= $fetch_playlist['thumb']; ?>" alt="image">
            </div>
            <div class="details">
                <h3 class="title"><?= $fetch_playlist['title']; ?></h3>
                <div class="date">
                    <i class='bx bx-calendar'></i> <span><?= $fetch_playlist['date']; ?></span>
                </div>
                <div class="description">
                    <p><?= $fetch_playlist['description']; ?></p>
                </div>
                <form action="" method="post" class="flex-btn">
                    <input type="hidden" name="playlist_id" value="<?= $playlist_id; ?>">
                    <a href="update_playlist.php?get_id=<?= $playlist_id; ?>" class="btn">Update Playlist</a>
                    <input type="submit" name="delete" value="delete playlist" class="btn" onclick="return confirm('delete this playlist?');">
                </form>
            </div>
        </div>
     <?php
        }
    }else{
        echo '<p class="empty">No playlist found!</p>';
    }
    ?>
    </section>
    <section class="contents">
        <h1 class="heading">Playlists</h1>

        <div class="box-container">
            <?php
        $select_content = $conn->prepare("SELECT * FROM `content` WHERE playlist_id = ? ORDER BY date DESC");
        $select_content->execute([$playlist_id]);
        if ($select_content->rowCount() > 0) {
            while ($fetch_videos = $select_content->fetch(PDO::FETCH_ASSOC)) {
                $video_id = $fetch_videos['id'];
                $status_color = ($fetch_videos['status'] === 'active') ? 'limegreen' : 'red';
             ?>
             <div class="box">
                <div class="flex justify-between items-center mb-4">
                    <div class="flex items-center gap-2">
                        <i class="bx bx-dots-vertical-rounded text-xl" style="color: <?= $status_color; ?>;"></i>
                        <span style="color: <?= $status_color; ?>;" class="font-semibold"><?= htmlspecialchars($fetch_videos['status']); ?></span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-500">
                        <i class="bx bx-calendar-alt"></i>
                        <span class="text-sm"><?= htmlspecialchars($fetch_videos['date']); ?></span>
                    </div>
                </div>
                <img src="../uploaded_files/<?= htmlspecialchars($fetch_videos['thumb']); ?>?v=<?= filemtime('../uploaded_files/' . $fetch_videos['thumb']); ?>" class="thumb mb-4" alt="Playlist Content Thumbnail">
                <h3 class="title mb-4"><?= htmlspecialchars($fetch_videos['title']); ?></h3>
                <form action="" method="post" class="flex-btn flex justify-center gap-2">
                    <input type="hidden" name="video_id" value="<?= $video_id; ?>">
                    <a href="update_content.php?get_id=<?= $video_id; ?>" class="btn">Update</a>
                    <input type="submit" name="delete_video" value="Delete" class="btn" onclick="return confirm('Delete this video?');">
                    <a href="view_content.php?get_id=<?= $video_id; ?>" class="btn">View</a>
                </form>
             </div>
        <?php
            }
        } else{
            echo '
      <div class="empty">
         <p style="margin-bottom: 1.5rem;">No video added yet</p>
         <a href="add_content.php" class="btn" style="margin-top: 1.5rem;">add Videos</a>
        </div>';
        }
        ?>
     </div>
    </section>

<?php include '../components/footer.php'; ?>
   <script src="../js/admin_script.js"></script>
</body>
</html>
