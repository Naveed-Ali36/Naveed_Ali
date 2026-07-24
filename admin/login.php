<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';

    if (attemptLogin($user, $pass)) {
        redirect('index.php');
    }
    $error = 'Invalid username or password.';
}

if (isLoggedIn()) {
    redirect('index.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — Portfolio</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body class="login-body">
    <div class="login-card">
        <div class="login-brand">
            <i class='bx bx-layer'></i>
            <h1>Portfolio Admin</h1>
            <p>Sign in to manage your website</p>
        </div>
        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php endif; ?>
        <form method="POST" class="login-form">
            <label>
                <span>Username</span>
                <input type="text" name="username" required autocomplete="username" value="admin">
            </label>
            <label>
                <span>Password</span>
                <input type="password" name="password" required autocomplete="current-password" placeholder="Admin@123">
            </label>
            <button type="submit" class="btn btn-primary">Login <i class='bx bx-log-in'></i></button>
        </form>
        <p class="login-hint">Default: <code>admin</code> / <code>Admin@123</code> — change in Settings</p>
    </div>
</body>
</html>
