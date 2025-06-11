<?php
require '../config/database.php';
require '../utils/session.php';

// Define requireRole if not already defined
if (!function_exists('requireRole')) {
    function requireRole(array $roles) {
        if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $roles)) {
            header('HTTP/1.1 403 Forbidden');
            echo "Access denied.";
            exit;
        }
    }
}

requireRole(['admin', 'editor']);
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');

    if (empty($title)) {
        $errors[] = "Title is required.";
    } elseif (mb_strlen($title) > 255) {
        $errors[] = "Title must be less than 255 characters.";
    }
    if (empty($content)) {
        $errors[] = "Content is required.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO posts (title, content) VALUES (?, ?)");
        $stmt->execute([$title, $content]);
        header('Location: index.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Post | MyApp</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(-45deg, #ff9a9e, #fad0c4, #a18cd1, #fbc2eb, #fcb69f, #ffdde1, #cfd9df, #e2ebf0);
            background-size: 400% 400%;
            animation: gradientBG 18s ease infinite;
            font-family: 'Segoe UI', 'Roboto', Arial, sans-serif;
        }
        @keyframes gradientBG {
            0% {background-position: 0% 50%;}
            50% {background-position: 100% 50%;}
            100% {background-position: 0% 50%;}
        }
        .card {
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.2);
            border-radius: 1.5rem;
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(8px);
            transition: transform 0.2s, box-shadow 0.2s;
            border: none;
        }
        .card:hover {
            transform: scale(1.02);
            box-shadow: 0 16px 48px 0 rgba(31, 38, 135, 0.25);
        }
        .card-header {
            border-radius: 1.5rem 1.5rem 0 0;
            background: linear-gradient(90deg, #6a11cb 0%, #2575fc 100%);
            letter-spacing: 1px;
        }
        .form-label {
            font-weight: 600;
            color: #2575fc;
        }
        .form-control:focus {
            border-color: #6a11cb;
            box-shadow: 0 0 0 0.2rem rgba(106,17,203,.15);
        }
        .btn-success {
            background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%);
            border: none;
            font-weight: 600;
            letter-spacing: 1px;
        }
        .btn-success:hover {
            background: linear-gradient(90deg, #38f9d7 0%, #43e97b 100%);
        }
        .btn-secondary {
            font-weight: 600;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>
<div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="card mx-auto w-100" style="max-width: 600px;">
        <div class="card-header bg-primary text-white text-center">
            <h4 class="mb-0">Create New Post</h4>
        </div>
        <div class="card-body">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <form method="post" action="" id="createPostForm" novalidate>
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        class="form-control"
                        required
                        maxlength="255"
                        value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"
                    >
                </div>
                <div class="mb-3">
                    <label for="content" class="form-label">Content</label>
                    <textarea
                        id="content"
                        name="content"
                        class="form-control"
                        required
                        minlength="10"
                        rows="6"
                    ><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
                </div>
                <button type="submit" class="btn btn-success">Create Post</button>
                <a href="index.php" class="btn btn-secondary ms-2">Back to Posts</a>
            </form>
            <script>
            document.getElementById('createPostForm').addEventListener('submit', function(e) {
                if (!this.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                    this.classList.add('was-validated');
                    alert('Please fill out the form correctly.');
                }
            });
            </script>
        </div>
    </div>
</div>
</body>
</html>