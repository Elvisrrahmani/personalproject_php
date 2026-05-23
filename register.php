<?php
require 'db.php';
require 'auth.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (strlen($username) < 3) {
        $error = 'Username must be at least 3 characters';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } else {
        $check = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $check->execute([$username]);

        if ($check->fetch()) {
            $error = 'Username already taken';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
            $stmt->execute([$username, $hash]);
            $success = 'Account created. You can now sign in.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register — Kanban</title>
   <link rel="stylesheet" href="style.css">
</head>
<body class="auth-body register-page">
    <div class="auth-card register-card">
        <h1>Register</h1>
        <p class="auth-sub">Create a new account</p>

        <?php if ($error): ?><div class="auth-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <?php if ($success): ?><div class="auth-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

        <form method="POST">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" autocomplete="username" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" autocomplete="new-password" required>

            <button type="submit" class="btn-primary">Create account</button>
        </form>

        <p class="auth-foot">Already have an account? <a href="login.php">Sign in</a></p>
    </div>
</body>
</html>
