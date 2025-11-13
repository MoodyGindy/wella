<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

ensure_session();

if (!empty($_SESSION['admin_user_id'])) {
    header('Location: ' . site_url('admin/index.php'));
    exit;
}

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $errors[] = 'Please fill in both fields.';
    } else {
        if (authenticate_admin($email, $password)) {
            header('Location: ' . site_url('admin/index.php'));
            exit;
        }
        $errors[] = 'Incorrect email or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login · WellaResin</title>
    <link rel="stylesheet" href="<?= asset_url('css/styles.css') ?>">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: var(--light);
        }
        .login-card {
            width: min(420px, 100%);
            background: #fff;
            padding: 2rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }
        .login-card h1 {
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
<div class="login-card">
    <h1>Admin Login</h1>
    <?php foreach ($errors as $error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endforeach; ?>
    <form method="post">
        <div>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required value="<?= htmlspecialchars($email) ?>">
        </div>
        <div>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button class="btn-primary" type="submit">Log in</button>
    </form>
</div>
</body>
</html>

