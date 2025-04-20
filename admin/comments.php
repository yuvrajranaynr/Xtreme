<?php
include '../components/connect.php';
$message = [];

if (!isset($_COOKIE['tutor_id'])) {
    header('location: login.php');
    exit;
}
$tutor_id = $_COOKIE['tutor_id'];

if (isset($_POST['delete_comment'])) {
    $delete_id = $_POST['comment_id'];
    $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

    $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE id = ?");
    $verify_comment->execute([$delete_id]);

    if ($verify_comment->rowCount() > 0) {
        $delete_comment = $conn->prepare("DELETE FROM `comments` WHERE id = ?");
        $delete_comment->execute([$delete_id]);
        $message[] = 'Comment deleted successfully!';
    } else {
        $message[] = 'Comment already deleted!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Comments</title>

    <!-- Boxicons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<?php
// show messages if any
if (!empty($message)) {
    foreach ($message as $msg) {
        echo "<p class='message'>" . htmlspecialchars($msg) . "</p>";
    }
}
?>

<section class="comments">
    <h1 class="heading">User Comments</h1>
    <div class="show-comments">
        <?php
        $select_comments = $conn->prepare("SELECT * FROM `comments` WHERE tutor_id = ?");
        $select_comments->execute([$tutor_id]);

        if ($select_comments->rowCount() > 0) {
            while ($fetch_comment = $select_comments->fetch(PDO::FETCH_ASSOC)) {
                $content_id = $fetch_comment['content_id'];
                $select_content = $conn->prepare("SELECT title FROM `content` WHERE id = ? LIMIT 1");
                $select_content->execute([$content_id]);
                $fetch_content = $select_content->fetch(PDO::FETCH_ASSOC);
        ?>
        <div class="box">
            <div class="content">
                <span><?= htmlspecialchars($fetch_comment['date']); ?></span>
                <p>- <?= htmlspecialchars($fetch_content['title']); ?></p>
                <a href="view_content.php?get_id=<?= htmlspecialchars($content_id); ?>">View Content</a>
            </div>
            <p class="text"><?= htmlspecialchars($fetch_comment['comment']); ?></p>
            <form action="" method="post">
                <input type="hidden" name="comment_id" value="<?= htmlspecialchars($fetch_comment['id']); ?>">
                <button type="submit" name="delete_comment" class="btn" onclick="return confirm('Delete this comment?');">Delete Comment</button>
            </form>
        </div>
        <?php
            }
        } else {
            echo '<p class="empty">No comments yet!</p>';
        }
        ?>
    </div>
</section>

<?php include '../components/footer.php'; ?>
<script src="../js/admin_script.js"></script>
</body>
</html>
