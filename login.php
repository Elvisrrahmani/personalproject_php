<?php
require 'db.php';
require 'auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = (bool)$user['is_admin'];
        header('Location: index.php');
        exit;
    } else {
        $error = 'Invalid username or password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login — Kanban</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-body login-page">
    <div class="auth-card login-card">
        <h1>Sign in</h1>
        <p class="auth-sub">Access your kanban board</p>

        <?php if ($error): ?>
            <div class="auth-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" autocomplete="username" required autofocus>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" autocomplete="current-password" required>

            <button type="submit" class="btn-primary">Sign in</button>
        </form>

        <p class="auth-foot">No account? <a href="register.php">Register</a></p>
    </div>
</body>
</html>
