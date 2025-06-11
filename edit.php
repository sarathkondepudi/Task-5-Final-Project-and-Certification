<?php
require '../config/database.php';
require '../utils/session.php';
requireRole(['admin', 'editor']);

// Securely get the post ID
$id = isset($_GET['id']) ? $_GET['id'] : null;
if (!$id) {
    echo "<div class='alert alert-danger text-center mt-5'>No post ID provided.</div>";
    exit;
}

$stmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    echo "<div class='alert alert-danger text-center mt-5'>Post not found.</div>";
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');

    // Server-side validation
    if (empty($title)) {
        $errors[] = "Title is required.";
    } elseif (mb_strlen($title) > 255) {
        $errors[] = "Title must be less than 255 characters.";
    }
    if (empty($content)) {
        $errors[] = "Content is required.";
    } elseif (mb_strlen($content) < 10) {
        $errors[] = "Content must be at least 10 characters.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
        $stmt->execute([$title, $content, $id]);
        header('Location: index.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Post | MyApp</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(-45deg, #ff9a9e, #fad0c4, #a18cd1, #fbc2eb, #fcb69f, #ffdde1, #cfd9df, #e2ebf0);
            background-size: 400% 400%;
            animation: gradientBG 18s ease infinite;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        @keyframes gradientBG {
            0% {background-position: 0% 50%;}
            50% {background-position: 100% 50%;}
            100% {background-position: 0% 50%;}
        }
        .form-container {
            background: rgba(255,255,255,0.95);
            padding: 2.5rem 2rem 2rem 2rem;
            border-radius: 1.5rem;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.18);
            max-width: 500px;
            width: 100%;
        }
        .form-label {
            color: #23a6d5;
            font-weight: 600;
        }
        .btn-primary {
            background: linear-gradient(90deg, #e73c7e 0%, #23a6d5 100%);
            border: none;
            font-weight: 600;
            letter-spacing: 1px;
        }
        .btn-primary:hover {
            background: linear-gradient(90deg, #23a6d5 0%, #e73c7e 100%);
        }
        .btn-secondary {
            font-weight: 600;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>
<div class="form-container mx-auto">
    <h3 class="text-center mb-4" style="color:#23a6d5;">Edit Post</h3>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form method="POST" id="editPostForm" novalidate>
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input
                type="text"
                name="title"
                id="title"
                class="form-control"
                value="<?= htmlspecialchars($_POST['title'] ?? $post['title']) ?>"
                required
                minlength="3"
                maxlength="255"
                pattern=".{3,255}"
                title="Title must be between 3 and 255 characters"
            >
        </div>
        <div class="mb-3">
            <label for="content" class="form-label">Content</label>
            <textarea
                name="content"
                id="content"
                class="form-control"
                rows="5"
                required
                minlength="10"
                maxlength="5000"
                title="Content must be at least 10 characters"
            ><?= htmlspecialchars($_POST['content'] ?? $post['content']) ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary w-100">Update Post</button>
        <a href="index.php" class="btn btn-secondary w-100 mt-2">Back</a>
    </form>
</div>
<script>
document.getElementById('editPostForm').addEventListener('submit', function(e) {
  if (!this.checkValidity()) {
    e.preventDefault();
    e.stopPropagation();
    this.classList.add('was-validated');
    alert('Please fill out the form correctly.');
  }
});
</script>
</body>
</html>