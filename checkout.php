<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $address = $_POST['address'] ?? '';
    $userId = $_SESSION['user_id'];

    $cart = $_SESSION['cart'] ?? [];
    $total = array_sum(array_column($cart, 'price'));

    try {
        $conn->beginTransaction();

        $stmt = $conn->prepare("INSERT INTO orders (user_id, total, status) VALUES (:user_id, :total, 'Pending')");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':total', $total);
        $stmt->execute();
        $orderId = $conn->lastInsertId();

        foreach ($cart as $item) {
            $stmt = $conn->prepare("INSERT INTO order_items (order_id, book_id, quantity, price) VALUES (:order_id, :book_id, 1, :price)");
            $stmt->bindParam(':order_id', $orderId);
            $stmt->bindParam(':book_id', $item['id']);
            $stmt->bindParam(':price', $item['price']);
            $stmt->execute();
            

        }

        $conn->commit();
        $_SESSION['cart'] = [];
        $message = "Order placed successfully! Thank you, $name.";
        header("Location: orders.php");
        exit();

    } catch (PDOException $e) {
        $conn->rollBack();
        $message = "Order failed: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-5">Checkout</h1>
        <p><?= $message ?></p>
        <?php if (empty($message)): ?>
            <form action="checkout.php" method="POST">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea class="form-control" id="address" name="address" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Submit Order</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
