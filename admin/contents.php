<?php
include '../components/connect.php';

if (isset($_COOKIE['tutor_id'])) {
    $tutor_id = $_COOKIE['tutor_id'];
} else {
    $tutor_id = '';
    header('location: login.php');
    exit;
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
}
else{
    $message[] = 'Content already deleted!';
        }
    }

?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Playlists</title>

    <!-- Boxicons link -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <!-- Corrected the path to the CSS file -->
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

    <?php include '../components/admin_header.php'; ?>

    <section class="contents">
        <h1 class="heading">Your Contents</h1>

        <div class="box-container">
            <div class="add">
                <a href="add_content.php"><i class="bx bx-plus"></i></a>
            </div>
            <?php
        $select_videos = $conn->prepare("SELECT * FROM `content` WHERE tutor_id = ? ORDER BY date DESC");
        $select_videos->execute([$tutor_id]);
        if ($select_videos->rowCount() > 0) {
            while ($fetch_videos = $select_videos->fetch(PDO::FETCH_ASSOC)) {
                $video_id = $fetch_videos['id'];

             ?>
             <div class="box">
                <div class="flex">
                    <div><i class="bx bx-dots-vertical-rounded" style="<?php if(
                        $fetch_videos['status'] == 'active'
                    ) { echo 'color: limegreen;'; } else { echo 'color: red;'; } ?>"></i>
                    <span style="<?php if(
                        $fetch_videos['status'] == 'active'
                    ) { echo 'color: limegreen;'; } else { echo 'color: red;'; } ?>"><?= htmlspecialchars($fetch_videos['status']); ?></span>
                </div>
                </div>
                <div>
                    <i class="bx bxs-calendar-alt"></i>
                    <span><?= $fetch_videos['date'];?></span>
                </div>
                <img src="../uploaded_files/<?= htmlspecialchars($fetch_videos['thumb']); ?>?v=<?= filemtime('../uploaded_files/' . $fetch_videos['thumb']); ?>" class="thumb" alt="image">
                <h3 class="title"><?= $fetch_videos['title']; ?></h3>
  
     <form action="" method="post" class="flex-btn">
            <input type="hidden" name="video_id" value="<?= $video_id; ?>">
        <a href="update_content.php?get_id=<?= $video_id; ?>" class="btn">update</a>
        <input type="submit" name="delete_video" value="delete" class="btn" onclick="return confirm('delete this video');"> 
        <a href="view_content.php?get_id=<?= $video_id; ?>" class="btn">view content</a>
    </form>
                
             </div>
        <?php
            }
        } else{
            echo '<p class="empty">No contents added yet!</p>';
        }
        ?>
     </div>
    </section>

    <?php include '../components/footer.php'; ?>

    <script src="../js/admin_script.js"></script>
</body>
</html>