<?php
include '../components/connect.php';

if (!isset($_COOKIE['tutor_id'])) {
    header('location: login.php');
    exit;
}
$tutor_id = $_COOKIE['tutor_id'];

// Fetch content ID from GET parameter and validate
$get_id = $_GET['get_id'] ?? '';
if (empty($get_id)) {
    header('location: contents.php');
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
 if(isset($_POST['delete_comment'])){
    $delete_id = $_POST['comment_id'];
    $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

    $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE id = ?");
    $verify_comment->execute([$delete_id]);

    if($verify_comment->rowCount()>0){
        $delete_comment = $conn->prepare("DELETE FROM `comments` WHERE id = ?");
        $delete_comment->execute([$delete_id]);
        $message[] = 'Comment deleted successfully!';
    }else{
        $message[] = 'Comment already deleted!';
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
   <section class="view-content">
    <h1 class="heading">Content Detail</h1>
    <?php
$select_content = $conn->prepare("SELECT * FROM content WHERE id = ? AND tutor_id = ?");
$select_content->execute([$get_id, $tutor_id]);

if ($select_content->rowCount() > 0) {
    while ($fetch_content = $select_content->fetch(PDO::FETCH_ASSOC)) {
        $video_id = $fetch_content['id'];
        $count_likes = $conn->prepare("SELECT * from `likes` where tutor_id= ? AND content_id = ?");
        $count_likes->execute([$tutor_id, $video_id]);
        $total_likes = $count_likes->rowCount();

        $count_comments = $conn->prepare("SELECT * from `comments` where tutor_id= ? AND content_id = ?");
        $count_comments->execute([$tutor_id, $video_id]);
        $total_comments = $count_comments->rowCount();

    ?>
<div class="container">

<video src="../uploaded_files/<?= $fetch_content['video']; ?>" autoplay controls poster="../uploaded_files/<?= $fetch_content['thumb']; ?>" class="video"></video>

<div class="date">
    <i class="bx bxs-calendar-alt"></i>
    <span><?= $fetch_content['date']; ?></span>
</div>

<h3 class="title"><?= $fetch_content['title']; ?></h3>

<div class="flex">
    <div><i class="bx bxs-heart"></i> <span><?= $total_likes; ?></span></div>
    <div><i class="bx bxs-chat"></i> <span><?= $total_comments; ?></span></div>
</div>

<div class="description">
    <?= $fetch_content['description']; ?>
</div>

<form action="" method="post">
    <input type="hidden" name="video_id" value="<?= $video_id; ?>">
    <a href="update_content.php?get_id=<?= $video_id; ?>" class="btn">update</a>
    <input type="submit" name="delete_video" value="delete video" class="btn" onclick="return confirm('delete this video');">
</form>

</div>

    <?php
    }
} else {
    echo '
    <div class="empty">
        <p style="margin-bottom: 1.5rem;">no video added yet!</p>
        <a href="add_content.php" class="btn" style="margin-top: 1.5rem;">add videos</a>
    </div>';
}
?>

    </section>
    <section class="comments">

    <h1 class="heading">user comments</h1>

    <div class="show-comments">

        <?php
        $select_comments = $conn->prepare("SELECT * FROM comments WHERE content_id = ?");
        $select_comments->execute([$get_id]);

        if ($select_comments->rowCount() > 0) {
            while ($fetch_comment = $select_comments->fetch(PDO::FETCH_ASSOC)) {
                $select_commentor = $conn->prepare("SELECT * FROM users WHERE id = ?");
                $select_commentor->execute([$fetch_comment['user_id']]);
                $fetch_commentor = $select_commentor->fetch(PDO::FETCH_ASSOC);
        ?>

<div class="box">

<div class="user">
    <img src="../uploaded_files/<?= $fetch_commentor['image']; ?>">
    <div>
        <h3><?= $fetch_commentor['name']; ?></h3>
        <span><?= $fetch_comment['date']; ?></span>
    </div>
</div>

<p class="text"><?= $fetch_comment['comments']; ?></p>

<form action="" method="post" class="flex-btn">
    <input type="hidden" name="comment_id" value="<?= $fetch_comment['id']; ?>">
    <button type="submit" name="delete_comment" class="btn" onclick="return confirm('delete this comment');">
        delete comment
    </button>
</form>

</div>


        <?php
            }
        } else {
            echo '<p class="empty">no comments added yet!</p>';
        }
        ?>

    </div>

</section>


<?php include '../components/footer.php'; ?>
   <script src="../js/admin_script.js"></script>
</body>
</html>
