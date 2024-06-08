<?php
session_start();
require 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch orders for the logged-in user
try {
    $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC");
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching orders: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 2px solid #6c757d;
        }
        .card-body {
            background-color: #e9ecef;
        }
        .card {
            border: 1px solid #6c757d;
        }
        .order-status {
            font-weight: bold;
            color: #17a2b8;
        }
        .order-total {
            font-weight: bold;
            color: #28a745;
        }
        .order-items li {
            list-style: none;
            background-color: #fff;
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #6c757d;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="index.php">Bookstore</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="orders.php">My Orders</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    
    <div class="container mt-5">
        <h1 class="text-center mb-4">My Orders</h1>
        <?php if (empty($orders)): ?>
            <p class="text-center">You have no orders yet.</p>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <span class="order-id">Order #<?= htmlspecialchars($order['id']) ?></span> - 
                        <span class="order-date"><?= htmlspecialchars($order['created_at']) ?></span>
                    </div>
                    <div class="card-body">
                        <p class="order-status">Status: <?= htmlspecialchars($order['status']) ?></p>
                        <p class="order-total">Total: Rs<?= htmlspecialchars($order['total']) ?></p>
                        <h5>Items:</h5>
                        <?php
                        // Fetch order items
                        $stmt = $conn->prepare("SELECT order_items.*, books.title FROM order_items JOIN books ON order_items.book_id = books.id WHERE order_id = :order_id");
                        $stmt->bindParam(':order_id', $order['id']);
                        $stmt->execute();
                        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <ul class="order-items">
                            <?php foreach ($items as $item): ?>
                                <li>
                                    <strong><?= htmlspecialchars($item['title']) ?></strong> - Rs<?= htmlspecialchars($item['price']) ?> (Quantity: <?= htmlspecialchars($item['quantity']) ?>)
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
