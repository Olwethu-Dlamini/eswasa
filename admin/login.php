<?php
// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit();
}

require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT id, username, password, role FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['username'] = $user['username'];

            header('Location: index.php?page=dashboard.php');
            exit();
        } else {
            $error = 'Invalid password.';
        }
    } else {
        $error = 'User not found.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — ESWASA Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
            background: #fff;
        }
        .login-card .card-body {
            padding: 2.5rem;
        }
        .logo-container {
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .logo {
            max-width: 200px;
            max-height: 120px;
            width: auto;
            height: auto;
            object-fit: contain;
            display: inline-block;
        }
        .form-label {
            font-weight: 500;
            font-size: 0.9rem;
            color: #333;
            margin-bottom: 0.5rem;
        }
        .form-control {
            border: 1px solid #ced4da;
            border-radius: 8px;
            padding: 0.65rem 1rem;
            font-size: 1rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
        }
        .btn-primary {
            background-color: #0d6efd;
            border: none;
            border-radius: 8px;
            padding: 0.7rem;
            font-weight: 500;
            font-size: 1rem;
            transition: background-color 0.2s;
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
        }
        .alert-danger {
            border-radius: 8px;
            font-size: 0.95rem;
            padding: 0.75rem 1rem;
        }
        .card-header {
            background: #fff;
            border-bottom: none;
            padding: 2rem 2.5rem 0;
        }
        .card-header h3 {
            font-weight: 600;
            font-size: 1.4rem;
            color: #1a1a1a;
            margin: 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="card-header">
            <div class="logo-container">
               
                <img src="../assets/img/logo/ESWASALOGO.png" alt="ESWASA Logo" class="logo">
            </div>
            <h3>Admin Login</h3>
        </div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" novalidate>
                <div class="mb-4">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required autofocus autocomplete="username">
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required autocomplete="current-password">
                </div>
                <button type="submit" class="btn btn-primary w-100">Sign in</button>
            </form>
        </div>
    </div>
</body>
</html>