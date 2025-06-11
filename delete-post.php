<?php
require_once '../src/config/database.php';

if (isset($_GET['id'])) {
    $postId = $_GET['id'];

    $query = "DELETE FROM posts WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $postId);

    if ($stmt->execute()) {
        header("Location: dashboard.php?message=Post deleted successfully");
        exit();
    } else {
        echo "Error deleting post.";
    }
} else {
    echo "Invalid request.";
}
?>