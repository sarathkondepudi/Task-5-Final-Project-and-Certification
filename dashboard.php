<?php
session_start();
require_once '../src/config/database.php';
require_once '../src/controllers/PostController.php';

$postController = new PostController();
$posts = $postController->getAllPosts();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="path/to/your/styles.css">
</head>
<body>
    <header>
        <h1>Welcome to Your Dashboard</h1>
        <a href="create-post.php">Create New Post</a>
        <a href="logout.php">Logout</a>
    </header>
    <main>
        <h2>Your Posts</h2>
        <ul>
            <?php foreach ($posts as $post): ?>
                <li>
                    <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                    <p><?php echo htmlspecialchars($post['content']); ?></p>
                    <a href="edit-post.php?id=<?php echo $post['id']; ?>">Edit</a>
                    <a href="delete-post.php?id=<?php echo $post['id']; ?>">Delete</a>
                </li>
            <?php endforeach; ?>
        </ul>
    </main>
</body>
</html>