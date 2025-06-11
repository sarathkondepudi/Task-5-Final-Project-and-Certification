<?php
require '../config/database.php';
session_start();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Server-side validation
    if (empty($username)) {
        $errors[] = "Username is required.";
    } elseif (mb_strlen($username) < 3 || mb_strlen($username) > 32) {
        $errors[] = "Username must be between 3 and 32 characters.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (mb_strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'] ?? 'user';
            header('Location: ../posts/index.php');
            exit;
        } else {
            $errors[] = "Invalid credentials.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | MyApp</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(120deg, #e0c3fc 0%, #8ec5fc 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .form-container {
            background: #fff;
            padding: 2.5rem 2rem 2rem 2rem;
            border-radius: 1.5rem;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.18);
            max-width: 400px;
            width: 100%;
        }
        .form-label {
            color: #5a189a;
            font-weight: 600;
        }
        .btn-primary {
            background: linear-gradient(90deg, #5a189a 0%, #43cea2 100%);
            border: none;
            font-weight: 600;
            letter-spacing: 1px;
        }
        .btn-primary:hover {
            background: linear-gradient(90deg, #43cea2 0%, #5a189a 100%);
        }
        .logo {
            font-size: 2rem;
            font-weight: bold;
            color: #5a189a;
            letter-spacing: 2px;
            margin-bottom: 1rem;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="form-container mx-auto">
    <div class="logo">MyApp</div>
    <h3 class="text-center mb-4" style="color:#5a189a;">Login</h3>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form method="POST" id="loginForm" novalidate autocomplete="off">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input
                type="text"
                name="username"
                id="username"
                class="form-control"
                placeholder="Username"
                required
                minlength="3"
                maxlength="32"
                value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
            >
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input
                type="password"
                name="password"
                id="password"
                class="form-control"
                placeholder="Password"
                required
                minlength="8"
            >
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
    <div class="mt-3 text-center">
        <span>Don't have an account?</span>
        <a href="register.php" style="color:#5a189a; text-decoration:underline;">Register</a>
    </div>
</div>
<script>
document.getElementById('loginForm').addEventListener('submit', function(e) {
    if (!this.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.add('was-validated');
    }
});
</script>
</body>
</html>