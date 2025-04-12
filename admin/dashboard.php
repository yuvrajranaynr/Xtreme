<?php
include '../components/connect.php';

if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   $tutor_id = ''; // Fixed the space to an empty string
   header('location: login.php');
}

// Fetch profile data
$select_profile = $conn->prepare("SELECT * FROM tutors WHERE id = ?");
$select_profile->execute([$tutor_id]);

if ($select_profile->rowCount() > 0) {
    $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
} else {
    $fetch_profile = null; // Handle case where no data is found
}

$select_contents = $conn->prepare("SELECT * FROM `content` WHERE tutor_id = ?");
$select_contents->execute([$tutor_id]);
$total_content = $select_contents->rowCount();

$select_playlists = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id = ?");
$select_playlists->execute([$tutor_id]);
$total_playlists = $select_playlists->rowCount();

$select_likes = $conn->prepare("SELECT * FROM `likes` WHERE tutor_id = ?");
$select_likes->execute([$tutor_id]);
$total_likes = $select_likes->rowCount();

$select_comments = $conn->prepare("SELECT * FROM `comments` WHERE tutor_id = ?");
$select_comments->execute([$tutor_id]);
$total_comments = $select_comments->rowCount();

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
   <title>Dashboard</title>

   <!-- Boxicons link -->
   <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

   <!-- Custom CSS link -->
   <link rel="stylesheet" href="../css/admin_style.css">


</head>
<body>

   <?php include '../components/admin_header.php'; ?>
   <section class="dashboard">

<h1 class="heading">dashboard</h1>

<div class="box-container">

  <div class="box">
    <?php if ($fetch_profile): ?>
        <h3>Welcome, <?= $fetch_profile['name']; ?>!</h3>
    <?php else: ?>
        <h3>Welcome, Guest!</h3>
    <?php endif; ?>
    <a href="profile.php" class="btn">view profile</a>
  </div>

  <div class="box">
    <h3><?= $total_content; ?></h3>
    <p>total contents</p>
    <a href="add_content.php" class="btn">add new content</a>
  </div>

  <div class="box">
    <h3><?= $total_playlists; ?></h3>
    <p>total playlists</p>
    <a href="add_playlist.php" class="btn">add new playlist</a>
  </div>

  <div class="box">
    <h3><?= $total_likes; ?></h3>
    <p>total likes</p>
    <a href="contents.php" class="btn">view contents</a>
  </div>

  <div class="box">
    <h3><?= $total_comments; ?></h3>
    <p>total comments</p>
    <a href="comments.php" class="btn">view comments</a>
  </div>

  <div class="box">
    <h3>quick start</h3>
    <div class="flex-btn">
      <a href="login.php" class="btn" style="width:200px;">login now</a>
      <a href="register.php" class="btn" style="width:200px;">register now</a>
    </div>
  </div>

</div>

</section>
<?php include '../components/footer.php'; ?>
   <script src="../js/admin_script.js"></script>
</body>
</html>
