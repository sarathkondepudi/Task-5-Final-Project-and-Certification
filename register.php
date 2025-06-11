<?php
require '../config/database.php';
require '../utils/session.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Server-side validation
    if (empty($username)) {
        $errors[] = "Username is required.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]{3,32}$/', $username)) {
        $errors[] = "Username must be 3-32 characters and contain only letters, numbers, or underscores.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 8 || strlen($password) > 64) {
        $errors[] = "Password must be between 8 and 64 characters.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // Check if username exists
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $errors[] = "Username already taken.";
        }
    }

    // Register user
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        if ($stmt->execute([$username, $hashedPassword])) {
            $success = "Registration successful! <a href='login.php'>Login here</a>.";
        } else {
            $errors[] = "Registration failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register | MyApp</title>
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
    <h3 class="text-center mb-4" style="color:#5a189a;">Create Account</h3>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php elseif ($success): ?>
        <div class="alert alert-success text-center">
            <?= $success ?>
        </div>
    <?php endif; ?>
    <form method="POST" id="registerForm" novalidate autocomplete="off">
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
                pattern="^[a-zA-Z0-9_]+$"
                title="Username must be 3-32 characters and contain only letters, numbers, or underscores"
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
                maxlength="64"
                title="Password must be at least 8 characters"
            >
        </div>
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm Password</label>
            <input
                type="password"
                name="confirm_password"
                id="confirm_password"
                class="form-control"
                placeholder="Confirm Password"
                required
                minlength="8"
                maxlength="64"
                title="Please confirm your password"
            >
        </div>
        <button type="submit" class="btn btn-primary w-100">Register</button>
    </form>
    <div class="mt-3 text-center">
        <span>Already have an account?</span>
        <a href="login.php" style="color:#5a189a; text-decoration:underline;">Login</a>
    </div>
</div>
<!-- Bootstrap JS (optional, for validation styles) -->
<script>
document.getElementById('registerForm').addEventListener('submit', function(e) {
    if (!this.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.add('was-validated');
    }
});
</script>
</body>
</html>