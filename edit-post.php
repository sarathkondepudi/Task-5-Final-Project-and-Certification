<?php
session_start();
require_once '../src/config/database.php';
require_once '../src/controllers/PostController.php';

$postController = new PostController($db);

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['id'])) {
    $postId = $_GET['id'];
    $post = $postController->getPost($postId);
} else {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];

    if ($postController->updatePost($postId, $title, $content)) {
        header('Location: dashboard.php');
        exit();
    } else {
        $error = "Failed to update post.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>
</head>
<body>
    <h1>Edit Post</h1>
    <?php if (isset($error)) : ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form action="edit-post.php?id=<?php echo $postId; ?>" method="POST">
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
        <br>
        <label for="content">Content:</label>
        <textarea name="content" id="content" required><?php echo htmlspecialchars($post['content']); ?></textarea>
        <br>
        <button type="submit">Update Post</button>
    </form>
    <a href="dashboard.php">Cancel</a>
</body>
</html>