<?php
session_start();
require 'config.php';

$message = '';
$referrer = $_SERVER['HTTP_REFERER'] ?? 'index.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $redirect_url = $_POST['redirect_url'] ?? 'index.php';

    if ($username && $password) {
        try {
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                header("Location: $redirect_url");
                exit;
            } else {
                $message = 'Invalid username or password.';
            }
        } catch (PDOException $e) {
            $message = 'Login failed: ' . $e->getMessage();
        }
    } else {
        $message = 'Please fill in all fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #e0ffe0;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin-top: 50px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .btn-custom {
            background-color: #ff4500;
            border-color: #ff4500;
            color: #fff;
        }
        .btn-custom:hover {
            background-color: #e03e00;
            border-color: #c83600;
        }
        .form-control {
            border-radius: 4px;
            padding: 10px;
            border: 1px solid #ccc;
            transition: border-color 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        }
        .form-control:focus {
            border-color: #ff4500;
            box-shadow: 0 0 5px rgba(255, 69, 0, 0.5);
        }
        .form-group label {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="bg-blue-100 flex items-center justify-center h-screen">
        <div class="container bg-white p-8 rounded-lg shadow-md w-full max-w-md">
            <h1 class="text-2xl font-bold mb-6 text-center">Login</h1>
            <?php if ($message): ?>
                <div class="alert alert-danger" role="alert">
                    <?= $message ?>
                </div>
            <?php endif; ?>
            <form action="login.php" method="POST">
                <input type="hidden" name="redirect_url" value="<?= htmlspecialchars($referrer) ?>">
                <div class="form-group">
                    <label for="username" class="font-weight-bold">Username</label>
                    <input type="text" id="username" name="username" required class="form-control">
                </div>
                <div class="form-group">
                    <label for="password" class="font-weight-bold">Password</label>
                    <input type="password" id="password" name="password" required class="form-control">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-custom btn-block">Login</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
