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

// Fetch total contents
$select_contents = $conn->prepare("SELECT * FROM `content` WHERE tutor_id = ?");
$select_contents->execute([$tutor_id]);
$total_contents = $select_contents->rowCount() ?: 0; // Initialize to 0 if no results

$select_playlists = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id = ?");
$select_playlists->execute([$tutor_id]);
$total_playlists = $select_playlists->rowCount();

$select_likes = $conn->prepare("SELECT * FROM `likes` WHERE tutor_id = ?");
$select_likes->execute([$tutor_id]);
$total_likes = $select_likes->rowCount();

$select_comments = $conn->prepare("SELECT * FROM `comments` WHERE tutor_id = ?");
$select_comments->execute([$tutor_id]);
$total_comments = $select_comments->rowCount();

// Ensure the uploaded_files directory exists
$upload_dir = '../uploaded_files';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true); // Create the directory with write permissions
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_image'])) {
    $image_name = $_FILES['profile_image']['name'];
    $image_tmp_name = $_FILES['profile_image']['tmp_name'];
    $image_folder = $upload_dir . '/' . $image_name;

    if (move_uploaded_file($image_tmp_name, $image_folder)) {
        $update_image = $conn->prepare("UPDATE tutors SET image = ? WHERE id = ?");
        $update_image->execute([$image_name, $tutor_id]);
        echo "<p>Profile image updated successfully.</p>";
    } else {
        echo "<p>Failed to upload image. Please try again.</p>";
    }
}

// Debugging: Check if the uploaded file is stored in the database
$check_image = $conn->prepare("SELECT image FROM tutors WHERE id = ?");
$check_image->execute([$tutor_id]);
$image_data = $check_image->fetch(PDO::FETCH_ASSOC);

if ($image_data) {
    echo "<p>Image stored in database: " . htmlspecialchars($image_data['image']) . "</p>";
} else {
    echo "<p>No image found in database for this tutor.</p>";
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
   <title>Dashboard</title>
   <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

   <?php include '../components/admin_header.php'; ?>
   <section class="tutor-profile" style="min-height: calc(100vh-19rem);">
    <h1 class="heading">profile details </h1>
    <div class="details">

  <div class="tutor">
    <!-- Display profile image and details -->
    <?php if ($fetch_profile): ?>
        <img src="../uploaded_files/<?= $fetch_profile['image'] ?: 'default.png'; ?>" alt="Profile Image">
        <h3><?= $fetch_profile['name']; ?></h3>
        <span><?= $fetch_profile['profession']; ?></span>
    <?php else: ?>
        <p>Profile not found. Please update your profile.</p>
        <img src="../uploaded_files/default.png" alt="Default Profile Image">
    <?php endif; ?>
    <a href="update.php" class="btn">update profile</a>
  </div>

  <form action="" method="post" enctype="multipart/form-data">
    <label for="profile_image">Upload Profile Image:</label>
    <input type="file" name="profile_image" id="profile_image" required>
    <button type="submit">Upload</button>
  </form>

  <div class="flex">

    <div class="box">
      <span><?= $total_playlists; ?></span>
      <p>total playlists</p>
      <a href="playlists.php" class="btn">view playlists</a>
    </div>

    <div class="box">
      <span><?= $total_contents; ?></span>
      <p>total videos</p>
      <a href="contents.php" class="btn">view contents</a>
    </div>

    <div class="box">
      <span><?= $total_likes; ?></span>
      <p>total likes</p>
      <a href="contents.php" class="btn">view contents</a>
    </div>

    <div class="box">
      <span><?= $total_comments; ?></span>
      <p>total comments</p>
      <a href="comments.php" class="btn">view comments</a>
    </div>

  </div>

</div>

</section>
<?php include '../components/footer.php'; ?>
   <script src="../js/admin_script.js"></script>
</body>
</html>
